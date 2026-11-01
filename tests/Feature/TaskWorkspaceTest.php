<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskWorkspaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_workspace_renders_with_seeded_almanac_data(): void
    {
        $this->seed();

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Meridian Almanac');
        $response->assertSee('Solar Horizon &amp; Zenith Focus Sequence', false);
        $response->assertSee('Finalize SQLite query indexes for ledger filtering scopes');
    }

    public function test_can_create_task_and_lock_to_zenith_focus_sequence(): void
    {
        $category = Category::create([
            'name' => 'Core Architecture',
            'slug' => 'core-architecture',
            'code' => 'CORE',
            'color' => '#0F2537',
        ]);

        $response = $this->post('/tasks', [
            'title' => 'Verify ephemeris horizon calculation',
            'notes' => 'Check solar window aggregation.',
            'category_id' => $category->id,
            'priority' => 'high',
            'time_window' => 'zenith',
            'due_date' => now()->toDateString(),
            'estimated_minutes' => 45,
            'is_focused' => '1',
        ]);

        $response->assertRedirect(route('tasks.index'));

        $this->assertDatabaseHas('tasks', [
            'title' => 'Verify ephemeris horizon calculation',
            'is_focused' => true,
            'focus_order' => 1,
            'estimated_minutes' => 45,
        ]);
    }

    public function test_can_toggle_completion_cycle_priority_and_resequence_focus(): void
    {
        $task1 = Task::create([
            'title' => 'First focus leg',
            'priority' => 'medium',
            'time_window' => 'zenith',
            'is_focused' => true,
            'focus_order' => 1,
            'estimated_minutes' => 30,
        ]);

        $task2 = Task::create([
            'title' => 'Second focus leg',
            'priority' => 'high',
            'time_window' => 'meridian',
            'is_focused' => true,
            'focus_order' => 2,
            'estimated_minutes' => 40,
        ]);

        // Unpin task 1 and verify task 2 resequences to slot 1
        $this->patch(route('tasks.toggle-focus', $task1))->assertRedirect();
        $this->assertFalse($task1->fresh()->is_focused);
        $this->assertSame(1, $task2->fresh()->focus_order);

        // Cycle priority on task 1 (medium -> high)
        $this->patch(route('tasks.cycle-priority', $task1))->assertRedirect();
        $this->assertSame('high', $task1->fresh()->priority);

        // Complete task 2
        $this->patch(route('tasks.toggle-complete', $task2))->assertRedirect();
        $this->assertTrue($task2->fresh()->is_completed);
    }

    public function test_can_create_and_remove_custom_classification(): void
    {
        $this->post(route('categories.store'), [
            'name' => 'Observatory Logs',
            'code' => 'OBSV',
            'color' => '#B86B0B',
        ])->assertRedirect();

        $this->assertDatabaseHas('categories', [
            'name' => 'Observatory Logs',
            'code' => 'OBSV',
        ]);
    }
}

