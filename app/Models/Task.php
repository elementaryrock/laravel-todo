<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    public const MAX_FOCUS_SLOTS = 5;
    public const DAILY_CAPACITY_MINUTES = 240;

    public const PRIORITIES = [
        'critical' => 'Critical',
        'high' => 'High',
        'medium' => 'Medium',
        'low' => 'Low',
    ];

    public const TIME_WINDOWS = [
        'zenith' => '06:00–11:00 · Zenith',
        'meridian' => '11:00–16:00 · Meridian',
        'dusk' => '16:00–20:00 · Dusk',
    ];

    protected $fillable = [
        'category_id',
        'title',
        'notes',
        'priority',
        'time_window',
        'is_focused',
        'focus_order',
        'is_completed',
        'completed_at',
        'due_date',
        'estimated_minutes',
    ];

    protected $casts = [
        'is_focused' => 'boolean',
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
        'due_date' => 'date',
        'estimated_minutes' => 'integer',
        'focus_order' => 'integer',
    ];

    /**
     * Category classification for this task.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Scope for active sequential Zenith Focus chain (01 -> 02 -> 03).
     */
    public function scopeFocusedChain(Builder $query): Builder
    {
        return $query
            ->where('is_focused', true)
            ->orderByRaw('CASE WHEN is_completed = 1 THEN 1 ELSE 0 END ASC')
            ->orderBy('focus_order')
            ->orderBy('id');
    }

    /**
     * Scope for filtering ledger rows by horizon lens, category, priority, window, and search query.
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        $today = Carbon::today()->toDateString();

        $lens = $filters['lens'] ?? 'all';
        if ($lens === 'today') {
            $query->where('is_completed', false)
                ->where(function (Builder $q) use ($today) {
                    $q->whereDate('due_date', $today)
                        ->orWhere('is_focused', true);
                });
        } elseif ($lens === 'upcoming') {
            $query->where('is_completed', false)
                ->whereDate('due_date', '>', $today);
        } elseif ($lens === 'overdue') {
            $query->where('is_completed', false)
                ->whereNotNull('due_date')
                ->whereDate('due_date', '<', $today);
        } elseif ($lens === 'completed') {
            $query->where('is_completed', true);
        }

        if (! empty($filters['category'])) {
            $query->whereHas('category', function (Builder $q) use ($filters) {
                $q->where('slug', $filters['category']);
            });
        }

        if (! empty($filters['priority']) && array_key_exists($filters['priority'], self::PRIORITIES)) {
            $query->where('priority', $filters['priority']);
        }

        if (! empty($filters['window']) && array_key_exists($filters['window'], self::TIME_WINDOWS)) {
            $query->where('time_window', $filters['window']);
        }

        if (! empty($filters['q'])) {
            $search = trim($filters['q']);
            $query->where(function (Builder $q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    /**
     * Scope for ordering the observation ledger by completion, urgency, priority rank, and due date.
     */
    public function scopeOrderedForLedger(Builder $query): Builder
    {
        return $query
            ->orderBy('is_completed')
            ->orderByDesc('is_focused')
            ->orderByRaw("CASE priority WHEN 'critical' THEN 1 WHEN 'high' THEN 2 WHEN 'medium' THEN 3 ELSE 4 END ASC")
            ->orderByRaw('CASE WHEN due_date IS NULL THEN 1 ELSE 0 END ASC')
            ->orderBy('due_date')
            ->orderByDesc('created_at');
    }

    /**
     * Determine if the task is past its scheduled due date and still incomplete.
     */
    public function isOverdue(): bool
    {
        if ($this->is_completed || ! $this->due_date) {
            return false;
        }

        return $this->due_date->copy()->startOfDay()->lt(Carbon::today());
    }

    /**
     * Determine if the task is scheduled for today.
     */
    public function isDueToday(): bool
    {
        if (! $this->due_date) {
            return false;
        }

        return $this->due_date->isSameDay(Carbon::today());
    }

    /**
     * Determine if the row earns tactile conflict hatching (overdue or critical task due today).
     */
    public function hasConflictHatch(): bool
    {
        if ($this->is_completed) {
            return false;
        }

        return $this->isOverdue() || ($this->priority === 'critical' && $this->isDueToday());
    }

    /**
     * Human-readable priority label.
     */
    public function priorityLabel(): string
    {
        return self::PRIORITIES[$this->priority] ?? 'Medium';
    }

    /**
     * Compact solar window label for tabular display.
     */
    public function windowShortLabel(): string
    {
        return match ($this->time_window) {
            'zenith' => 'ZENITH · AM',
            'meridian' => 'MERIDIAN · MID',
            'dusk' => 'DUSK · PM',
            default => 'MERIDIAN · MID',
        };
    }

    /**
     * Resequence active focus tasks so focus_order is always a contiguous 1..N sequence.
     */
    public static function resequenceFocusChain(): void
    {
        $focused = static::query()
            ->where('is_focused', true)
            ->orderByRaw('CASE WHEN focus_order IS NULL THEN 99 ELSE focus_order END ASC')
            ->orderBy('id')
            ->get();

        foreach ($focused as $index => $task) {
            $order = $index + 1;
            if ($task->focus_order !== $order) {
                $task->updateQuietly(['focus_order' => $order]);
            }
        }
    }
}
