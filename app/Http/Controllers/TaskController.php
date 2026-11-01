<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Category;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    /**
     * Display the Meridian Almanac personal task desk.
     */
    public function index(Request $request): View
    {
        $filters = [
            'lens' => $request->string('lens', 'all')->toString(),
            'category' => $request->string('category', '')->toString(),
            'priority' => $request->string('priority', '')->toString(),
            'window' => $request->string('window', '')->toString(),
            'q' => $request->string('q', '')->toString(),
        ];

        $tasks = Task::with('category')
            ->filter($filters)
            ->orderedForLedger()
            ->get();

        $focusedTasks = Task::with('category')
            ->focusedChain()
            ->get();

        $categories = Category::withCount([
            'tasks as active_tasks_count' => function ($query) {
                $query->where('is_completed', false);
            },
            'tasks as total_tasks_count',
        ])->orderBy('name')->get();

        $today = Carbon::today();
        $allTasks = Task::all();

        $activeTasks = $allTasks->where('is_completed', false);
        $completedTasks = $allTasks->where('is_completed', true);

        $focusMinutesUsed = (int) $activeTasks->filter(fn (Task $t) => $t->is_focused || $t->isDueToday())->sum('estimated_minutes');
        $focusMinutesCompleted = (int) $completedTasks->sum('estimated_minutes');
        $focusSlotsCount = $focusedTasks->count();

        $lensCounts = [
            'all' => $allTasks->count(),
            'today' => $activeTasks->filter(fn (Task $t) => $t->isDueToday() || $t->is_focused)->count(),
            'upcoming' => $activeTasks->filter(fn (Task $t) => $t->due_date && $t->due_date->gt($today))->count(),
            'overdue' => $activeTasks->filter(fn (Task $t) => $t->isOverdue())->count(),
            'completed' => $completedTasks->count(),
        ];

        $windowBreakdown = [
            'zenith' => [
                'label' => '06:00–11:00 · Zenith',
                'short' => 'ZENITH',
                'active' => $activeTasks->where('time_window', 'zenith')->count(),
                'minutes' => (int) $activeTasks->where('time_window', 'zenith')->sum('estimated_minutes'),
            ],
            'meridian' => [
                'label' => '11:00–16:00 · Meridian',
                'short' => 'MERIDIAN',
                'active' => $activeTasks->where('time_window', 'meridian')->count(),
                'minutes' => (int) $activeTasks->where('time_window', 'meridian')->sum('estimated_minutes'),
            ],
            'dusk' => [
                'label' => '16:00–20:00 · Dusk',
                'short' => 'DUSK',
                'active' => $activeTasks->where('time_window', 'dusk')->count(),
                'minutes' => (int) $activeTasks->where('time_window', 'dusk')->sum('estimated_minutes'),
            ],
        ];

        $editingTaskId = $request->integer('edit') ?: null;

        return view('tasks.index', [
            'tasks' => $tasks,
            'focusedTasks' => $focusedTasks,
            'categories' => $categories,
            'filters' => $filters,
            'lensCounts' => $lensCounts,
            'windowBreakdown' => $windowBreakdown,
            'focusMinutesUsed' => $focusMinutesUsed,
            'focusMinutesCompleted' => $focusMinutesCompleted,
            'focusSlotsCount' => $focusSlotsCount,
            'maxFocusSlots' => Task::MAX_FOCUS_SLOTS,
            'dailyCapacityMinutes' => Task::DAILY_CAPACITY_MINUTES,
            'editingTaskId' => $editingTaskId,
            'todayFormatted' => Carbon::now()->format('Y · m · d'),
            'dayOfYear' => Carbon::now()->dayOfYear,
        ]);
    }

    /**
     * Store a newly logged task in the database.
     */
    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $wantsFocus = $request->boolean('is_focused');

        $currentFocusCount = Task::where('is_focused', true)->count();
        if ($wantsFocus && $currentFocusCount < Task::MAX_FOCUS_SLOTS) {
            $data['is_focused'] = true;
            $data['focus_order'] = $currentFocusCount + 1;
        } else {
            $data['is_focused'] = false;
            $data['focus_order'] = null;
        }

        $task = Task::create($data);
        Task::resequenceFocusChain();

        $notice = $wantsFocus && ! $task->is_focused
            ? "Logged \"{$task->title}\" to the ledger. Zenith Focus chain is already at its 5-slot daily capacity."
            : "Logged \"{$task->title}\" into the Meridian Almanac.";

        $lens = $request->input('lens');
        $queryParams = ($lens && $lens !== 'all') ? ['lens' => $lens] : [];

        return redirect()
            ->route('tasks.index', $queryParams)
            ->with('status', $notice);
    }

    /**
     * Update an existing task entry.
     */
    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        $task->update($request->validated());

        $lens = $request->input('lens');
        $queryParams = ($lens && $lens !== 'all') ? ['lens' => $lens] : [];

        return redirect()
            ->route('tasks.index', $queryParams)
            ->with('status', "Updated entry \"{$task->title}\".");
    }

    /**
     * Toggle a task's completion status.
     */
    public function toggleComplete(Request $request, Task $task): RedirectResponse
    {
        $completed = ! $task->is_completed;

        $task->update([
            'is_completed' => $completed,
            'completed_at' => $completed ? now() : null,
        ]);

        Task::resequenceFocusChain();

        $message = $completed
            ? "Marked \"{$task->title}\" complete."
            : "Reopened \"{$task->title}\" in the active ledger.";

        return redirect()
            ->back()
            ->with('status', $message);
    }

    /**
     * Pin or unpin a task in the 5-slot sequential Zenith Focus chain.
     */
    public function toggleFocus(Task $task): RedirectResponse
    {
        if ($task->is_focused) {
            $task->update([
                'is_focused' => false,
                'focus_order' => null,
            ]);
            Task::resequenceFocusChain();

            return redirect()
                ->back()
                ->with('status', "Removed \"{$task->title}\" from the Zenith Focus sequence.");
        }

        $currentFocusCount = Task::where('is_focused', true)->count();
        if ($currentFocusCount >= Task::MAX_FOCUS_SLOTS) {
            return redirect()
                ->back()
                ->with('error', 'Zenith Focus sequence is full (5/5 slots). Complete or unpin an active slot before adding another.');
        }

        $task->update([
            'is_focused' => true,
            'focus_order' => $currentFocusCount + 1,
        ]);
        Task::resequenceFocusChain();

        return redirect()
            ->back()
            ->with('status', "Assigned \"{$task->title}\" to Zenith Focus slot 0" . $task->fresh()->focus_order . '.');
    }

    /**
     * Cycle task priority level in one click.
     */
    public function cyclePriority(Task $task): RedirectResponse
    {
        $order = ['low', 'medium', 'high', 'critical'];
        $currentIndex = array_search($task->priority, $order, true);
        $nextPriority = $order[(($currentIndex === false ? 1 : $currentIndex) + 1) % count($order)];

        $task->update(['priority' => $nextPriority]);

        return redirect()
            ->back()
            ->with('status', "Set \"{$task->title}\" priority to " . Task::PRIORITIES[$nextPriority] . '.');
    }

    /**
     * Remove all completed tasks from the ledger.
     */
    public function clearCompleted(): RedirectResponse
    {
        $deletedCount = Task::where('is_completed', true)->delete();
        Task::resequenceFocusChain();

        return redirect()
            ->route('tasks.index')
            ->with('status', "Archived {$deletedCount} completed " . ($deletedCount === 1 ? 'entry' : 'entries') . ' from the ledger.');
    }

    /**
     * Delete a single task entry.
     */
    public function destroy(Task $task): RedirectResponse
    {
        $title = $task->title;
        $task->delete();
        Task::resequenceFocusChain();

        return redirect()
            ->back()
            ->with('status', "Removed \"{$title}\" from the ledger.");
    }
}
