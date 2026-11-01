<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the Meridian Almanac personal task desk with synthetic demonstration data.
     */
    public function run(): void
    {
        $today = Carbon::today();

        $categories = [
            'core' => Category::updateOrCreate(
                ['slug' => 'core-architecture'],
                [
                    'name' => 'Core Architecture',
                    'code' => 'CORE',
                    'color' => '#0F2537',
                ]
            ),
            'spec' => Category::updateOrCreate(
                ['slug' => 'interface-spec'],
                [
                    'name' => 'Interface & Spec',
                    'code' => 'SPEC',
                    'color' => '#B86B0B',
                ]
            ),
            'ops' => Category::updateOrCreate(
                ['slug' => 'systems-ops'],
                [
                    'name' => 'Systems & Ops',
                    'code' => 'SYST',
                    'color' => '#186653',
                ]
            ),
            'deep' => Category::updateOrCreate(
                ['slug' => 'deep-research'],
                [
                    'name' => 'Deep Research',
                    'code' => 'DEEP',
                    'color' => '#25547B',
                ]
            ),
            'desk' => Category::updateOrCreate(
                ['slug' => 'studio-administration'],
                [
                    'name' => 'Studio Almanac',
                    'code' => 'ALMN',
                    'color' => '#7A4928',
                ]
            ),
        ];

        Task::query()->delete();

        $seedTasks = [
            [
                'category_id' => $categories['core']->id,
                'title' => 'Finalize SQLite query indexes for ledger filtering scopes',
                'notes' => 'Verify composite index coverage on (is_completed, priority) and (is_focused, focus_order) before afternoon benchmark.',
                'priority' => 'critical',
                'time_window' => 'zenith',
                'is_focused' => true,
                'focus_order' => 1,
                'is_completed' => false,
                'due_date' => $today->toDateString(),
                'estimated_minutes' => 60,
            ],
            [
                'category_id' => $categories['spec']->id,
                'title' => 'Calibrate tabular numeral spacing across ephemeris horizon columns',
                'notes' => 'Ensure Azeret Mono tabular-nums align vertically across time estimates, sequence numbers, and ISO dates.',
                'priority' => 'high',
                'time_window' => 'zenith',
                'is_focused' => true,
                'focus_order' => 2,
                'is_completed' => false,
                'due_date' => $today->toDateString(),
                'estimated_minutes' => 45,
            ],
            [
                'category_id' => $categories['ops']->id,
                'title' => 'Audit form validation recovery states for task and category endpoints',
                'notes' => 'Test boundary conditions for 240-minute daily capacity meter and 5-slot Zenith Focus lock.',
                'priority' => 'high',
                'time_window' => 'meridian',
                'is_focused' => true,
                'focus_order' => 3,
                'is_completed' => false,
                'due_date' => $today->toDateString(),
                'estimated_minutes' => 50,
            ],
            [
                'category_id' => $categories['ops']->id,
                'title' => 'Rotate staging webhook signing secret before external integration review',
                'notes' => 'Delayed from yesterday afternoon; requires updating local .env keys and re-verifying payload signatures.',
                'priority' => 'critical',
                'time_window' => 'zenith',
                'is_focused' => false,
                'focus_order' => null,
                'is_completed' => false,
                'due_date' => $today->copy()->subDay()->toDateString(),
                'estimated_minutes' => 30,
            ],
            [
                'category_id' => $categories['deep']->id,
                'title' => 'Synthesize reading notes on astronomical tables and Swiss timetable grids',
                'notes' => 'Compare column density rules between SBB Kursbuch departure sheets and Nautical Almanac sight reduction tables.',
                'priority' => 'medium',
                'time_window' => 'meridian',
                'is_focused' => false,
                'focus_order' => null,
                'is_completed' => false,
                'due_date' => $today->toDateString(),
                'estimated_minutes' => 45,
            ],
            [
                'category_id' => $categories['desk']->id,
                'title' => 'Prepare November sprint scope and archive October studio logbook',
                'notes' => 'Export completed October deliverables and reconcile open engineering milestones.',
                'priority' => 'medium',
                'time_window' => 'dusk',
                'is_focused' => false,
                'focus_order' => null,
                'is_completed' => false,
                'due_date' => $today->copy()->addDays(2)->toDateString(),
                'estimated_minutes' => 35,
            ],
            [
                'category_id' => $categories['core']->id,
                'title' => 'Write feature tests for sequential focus re-indexing when a slot is deleted',
                'notes' => 'Confirm removing slot 02 automatically promotes slot 03 to 02 without orphan sequence numbers.',
                'priority' => 'low',
                'time_window' => 'dusk',
                'is_focused' => false,
                'focus_order' => null,
                'is_completed' => false,
                'due_date' => $today->copy()->addDays(4)->toDateString(),
                'estimated_minutes' => 40,
            ],
            [
                'category_id' => $categories['core']->id,
                'title' => 'Configure Eloquent relationships and migration schema for Categories and Tasks',
                'notes' => 'Established foreign key constraints with nullOnDelete so removing a category preserves ledger entries.',
                'priority' => 'high',
                'time_window' => 'zenith',
                'is_focused' => false,
                'focus_order' => null,
                'is_completed' => true,
                'completed_at' => now()->subHours(2),
                'due_date' => $today->toDateString(),
                'estimated_minutes' => 30,
            ],
            [
                'category_id' => $categories['spec']->id,
                'title' => 'Self-host Chivo and Azeret Mono WOFF2 font files in public asset tree',
                'notes' => 'Eliminated external font stylesheet latency and verified zero layout shift on first paint.',
                'priority' => 'medium',
                'time_window' => 'zenith',
                'is_focused' => false,
                'focus_order' => null,
                'is_completed' => true,
                'completed_at' => now()->subHours(3),
                'due_date' => $today->toDateString(),
                'estimated_minutes' => 20,
            ],
        ];

        foreach ($seedTasks as $taskData) {
            Task::create($taskData);
        }
    }
}
