@extends('layouts.app')

@section('title', 'Meridian Almanac — Daily Task Workspace')

@section('content')
@php
    $totalFocusMinutes = $focusMinutesUsed + $focusMinutesCompleted;
    $completedPct = min(100, round(($focusMinutesCompleted / max(1, $dailyCapacityMinutes)) * 100));
    $activePct = min(100 - $completedPct, round(($focusMinutesUsed / max(1, $dailyCapacityMinutes)) * 100));
    $overPct = $totalFocusMinutes > $dailyCapacityMinutes ? min(100 - $completedPct - $activePct, 15) : 0;
    $hasActiveFilters = ($filters['lens'] !== 'all') || $filters['category'] !== '' || $filters['priority'] !== '' || $filters['window'] !== '' || $filters['q'] !== '';
@endphp

<div class="almanac-shell">
    <aside class="meridian-rail" aria-label="Workspace navigation and classifications">
        <div>
            <div class="rail-brand">
                <h1 class="rail-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="square" stroke-linejoin="miter" aria-hidden="true">
                        <circle cx="12" cy="12" r="9"></circle>
                        <line x1="12" y1="3" x2="12" y2="21"></line>
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    <span>Meridian Almanac</span>
                </h1>
                <div class="rail-coordinates">
                    <span>{{ str_replace('·', '/', $todayFormatted) }}</span>
                    <span>DOY {{ str_pad((string) $dayOfYear, 3, '0', STR_PAD_LEFT) }}</span>
                </div>
            </div>

            <h2 class="rail-section-heading">Horizon Lenses</h2>
            <ul class="rail-nav-list">
                <li>
                    <a href="{{ route('tasks.index', array_filter(array_merge($filters, ['lens' => 'all']))) }}"
                       class="rail-nav-link {{ $filters['lens'] === 'all' ? 'is-active' : '' }}">
                        <span class="rail-nav-label">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="square" aria-hidden="true">
                                <rect x="3" y="4" width="18" height="16"></rect>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                                <line x1="9" y1="10" x2="9" y2="20"></line>
                            </svg>
                            <span>All Observations</span>
                        </span>
                        <span class="rail-count">{{ str_pad((string) $lensCounts['all'], 2, '0', STR_PAD_LEFT) }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('tasks.index', array_filter(array_merge($filters, ['lens' => 'today']))) }}"
                       class="rail-nav-link {{ $filters['lens'] === 'today' ? 'is-active' : '' }}">
                        <span class="rail-nav-label">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="square" aria-hidden="true">
                                <circle cx="12" cy="12" r="5"></circle>
                                <line x1="12" y1="2" x2="12" y2="4"></line>
                                <line x1="12" y1="20" x2="12" y2="22"></line>
                                <line x1="2" y1="12" x2="4" y2="12"></line>
                                <line x1="20" y1="12" x2="22" y2="12"></line>
                            </svg>
                            <span>Today's Horizon</span>
                        </span>
                        <span class="rail-count">{{ str_pad((string) $lensCounts['today'], 2, '0', STR_PAD_LEFT) }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('tasks.index', array_filter(array_merge($filters, ['lens' => 'upcoming']))) }}"
                       class="rail-nav-link {{ $filters['lens'] === 'upcoming' ? 'is-active' : '' }}">
                        <span class="rail-nav-label">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="square" aria-hidden="true">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                            <span>Upcoming Ephemeris</span>
                        </span>
                        <span class="rail-count">{{ str_pad((string) $lensCounts['upcoming'], 2, '0', STR_PAD_LEFT) }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('tasks.index', array_filter(array_merge($filters, ['lens' => 'overdue']))) }}"
                       class="rail-nav-link {{ $filters['lens'] === 'overdue' ? 'is-active' : '' }}">
                        <span class="rail-nav-label">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="square" aria-hidden="true">
                                <polygon points="12 3 22 20 2 20"></polygon>
                                <line x1="12" y1="10" x2="12" y2="14"></line>
                                <line x1="12" y1="17" x2="12.01" y2="17"></line>
                            </svg>
                            <span>Overdue Conflicts</span>
                        </span>
                        <span class="rail-count {{ $lensCounts['overdue'] > 0 ? 'is-alert' : '' }}">{{ str_pad((string) $lensCounts['overdue'], 2, '0', STR_PAD_LEFT) }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('tasks.index', array_filter(array_merge($filters, ['lens' => 'completed']))) }}"
                       class="rail-nav-link {{ $filters['lens'] === 'completed' ? 'is-active' : '' }}">
                        <span class="rail-nav-label">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="square" aria-hidden="true">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            <span>Logged Archive</span>
                        </span>
                        <span class="rail-count">{{ str_pad((string) $lensCounts['completed'], 2, '0', STR_PAD_LEFT) }}</span>
                    </a>
                </li>
            </ul>

            <h2 class="rail-section-heading">Classifications</h2>
            <ul class="rail-nav-list">
                @foreach ($categories as $cat)
                    <li class="rail-category-row">
                        <a href="{{ route('tasks.index', array_filter(array_merge($filters, ['category' => $filters['category'] === $cat->slug ? '' : $cat->slug]))) }}"
                           class="rail-nav-link {{ $filters['category'] === $cat->slug ? 'is-active' : '' }}">
                            <span class="rail-nav-label">
                                <span class="category-swatch" style="background-color: {{ $cat->color }};"></span>
                                <span class="category-code-tag">{{ $cat->code }}</span>
                                <span>{{ $cat->name }}</span>
                            </span>
                            <span class="rail-count">{{ str_pad((string) $cat->active_tasks_count, 2, '0', STR_PAD_LEFT) }}</span>
                        </a>
                        @if ($categories->count() > 1)
                            <form method="POST" action="{{ route('categories.destroy', $cat) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rail-delete-btn" title="Remove classification {{ $cat->name }}" aria-label="Remove classification {{ $cat->name }}">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="square" aria-hidden="true">
                                        <line x1="18" y1="6" x2="6" y2="18"></line>
                                        <line x1="6" y1="6" x2="18" y2="18"></line>
                                    </svg>
                                </button>
                            </form>
                        @endif
                    </li>
                @endforeach
            </ul>

            <details class="rail-add-category" {{ $errors->category->any() ? 'open' : '' }}>
                <summary class="rail-add-summary">
                    <span>Add Classification</span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="square" aria-hidden="true">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                </summary>
                <form method="POST" action="{{ route('categories.store') }}" class="rail-category-form">
                    @csrf
                    <input type="text" name="name" class="rail-input" placeholder="Category name (e.g. Field Notes)" required maxlength="40" aria-label="Category name">
                    <div class="rail-form-inline">
                        <input type="text" name="code" class="rail-input tabular-data" placeholder="CODE (4ch)" maxlength="5" aria-label="Four-character category code">
                        <input type="color" name="color" class="rail-color-input" value="#B86B0B" aria-label="Category color">
                    </div>
                    @if ($errors->category->any())
                        <div style="font-size: 0.75rem; color: #FFD2CC;">
                            {{ $errors->category->first() }}
                        </div>
                    @endif
                    <button type="submit" class="rail-submit-btn">Save Classification</button>
                </form>
            </details>
        </div>

        <div class="rail-footer">
            <div class="tabular-data">LARAVEL 12 MVC / SQLITE</div>
            <div>Single-workspace daily ephemeris and priority triage ledger.</div>
        </div>
    </aside>

    <main class="almanac-main">
        <header class="desk-header">
            <div class="desk-title-group">
                <h2 class="desk-heading">
                    Daily Observation &amp; Task Desk
                    <span class="provenance-pill">SYNTHETIC DEMO LEDGER / SQLITE</span>
                </h2>
                <p class="desk-subtitle">
                    Assign up to five commitments to the sequential Zenith Focus chain, monitor the 240-minute daily focus budget, and work down the ruled ledger below.
                </p>
            </div>

            <div class="capacity-meter-box" aria-label="Daily focus capacity meter">
                <div class="capacity-header">
                    <span class="capacity-title">Daily Focus Capacity</span>
                    <span class="capacity-numbers">{{ str_pad((string) $totalFocusMinutes, 3, '0', STR_PAD_LEFT) }} / {{ $dailyCapacityMinutes }} MIN</span>
                </div>
                <div class="capacity-bar-track" role="progressbar" aria-valuenow="{{ $totalFocusMinutes }}" aria-valuemin="0" aria-valuemax="{{ $dailyCapacityMinutes }}">
                    @if ($completedPct > 0)
                        <div class="capacity-bar-completed" style="width: {{ $completedPct }}%;" title="Completed focus load: {{ $focusMinutesCompleted }} min"></div>
                    @endif
                    @if ($activePct > 0)
                        <div class="capacity-bar-active" style="width: {{ $activePct }}%;" title="Active focus load: {{ $focusMinutesUsed }} min"></div>
                    @endif
                    @if ($overPct > 0)
                        <div class="capacity-bar-over" style="width: {{ $overPct }}%;" title="Over daily budget"></div>
                    @endif
                </div>
                <div class="capacity-legend">
                    <span>SLOTS LOCKED: {{ str_pad((string) $focusSlotsCount, 2, '0', STR_PAD_LEFT) }}/{{ str_pad((string) $maxFocusSlots, 2, '0', STR_PAD_LEFT) }}</span>
                    <span>DONE: {{ $focusMinutesCompleted }}M / ACTIVE: {{ $focusMinutesUsed }}M</span>
                </div>
            </div>
        </header>

        @if (session('status'))
            <div class="status-banner" role="status">
                <span>{{ session('status') }}</span>
                <span class="tabular-data">OK</span>
            </div>
        @endif

        @if (session('error') || $errors->any())
            <div class="status-banner is-error" role="alert">
                <span>{{ session('error') ?: $errors->first() }}</span>
                <span class="tabular-data">ERR</span>
            </div>
        @endif

        <section class="zenith-horizon-section" aria-labelledby="zenith-sequence-heading">
            <div class="section-header-bar">
                <h2 id="zenith-sequence-heading" class="section-heading">Solar Horizon &amp; Zenith Focus Sequence</h2>
                <div class="section-meta">
                    <span>ACTIVE COURSE: 01 TO 05</span>
                    <span>CLICK SOLAR BAND TO FILTER BY WINDOW</span>
                </div>
            </div>

            <div class="solar-window-strip" role="navigation" aria-label="Solar time windows">
                @foreach ($windowBreakdown as $winKey => $winData)
                    <a href="{{ route('tasks.index', array_filter(array_merge($filters, ['window' => $filters['window'] === $winKey ? '' : $winKey]))) }}"
                       class="solar-window-cell {{ $filters['window'] === $winKey ? 'is-active' : '' }}">
                        <span class="solar-window-name">{{ str_replace('·', '/', $winData['label']) }}</span>
                        <span class="solar-window-stats">{{ str_pad((string) $winData['active'], 2, '0', STR_PAD_LEFT) }} OPEN / {{ $winData['minutes'] }} MIN</span>
                    </a>
                @endforeach
            </div>

            <div class="zenith-sequence-grid">
                @php
                    $firstIncompleteAssigned = false;
                @endphp
                @for ($slot = 1; $slot <= $maxFocusSlots; $slot++)
                    @php
                        $slotTask = $focusedTasks->get($slot - 1);
                        $isCurrentLeg = false;
                        if ($slotTask && ! $slotTask->is_completed && ! $firstIncompleteAssigned) {
                            $isCurrentLeg = true;
                            $firstIncompleteAssigned = true;
                        }
                    @endphp

                    @if ($slotTask)
                        <article class="zenith-slot is-populated {{ $isCurrentLeg ? 'is-active-leg' : '' }} {{ $slotTask->is_completed ? 'is-completed-slot' : '' }}">
                            <div>
                                <div class="slot-top-row">
                                    <span class="slot-seq-badge">
                                        <span>LEG 0{{ $slot }}</span>
                                        @if ($slot < $maxFocusSlots)
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="square" aria-hidden="true">
                                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                                <polyline points="13 6 19 12 13 18"></polyline>
                                            </svg>
                                        @endif
                                    </span>
                                    @if ($slotTask->category)
                                        <span class="classification-code">[{{ $slotTask->category->code }}]</span>
                                    @endif
                                </div>
                                <h3 class="slot-title">{{ $slotTask->title }}</h3>
                            </div>

                            <div class="slot-footer">
                                <span class="slot-duration">{{ str_pad((string) $slotTask->estimated_minutes, 3, '0', STR_PAD_LEFT) }} MIN</span>
                                <div class="slot-actions">
                                    <form method="POST" action="{{ route('tasks.toggle-complete', $slotTask) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="slot-dispatch-btn">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="square" aria-hidden="true">
                                                <polyline points="20 6 9 17 4 12"></polyline>
                                            </svg>
                                            <span>{{ $slotTask->is_completed ? 'Reopen' : 'Complete' }}</span>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('tasks.toggle-focus', $slotTask) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="slot-unpin-btn" title="Release from Zenith Focus sequence" aria-label="Release {{ $slotTask->title }} from Zenith Focus sequence">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="square" aria-hidden="true">
                                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                                <line x1="6" y1="6" x2="18" y2="18"></line>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    @else
                        <article class="zenith-slot is-empty-slot">
                            <div>
                                <div class="slot-top-row">
                                    <span class="slot-seq-badge">SLOT 0{{ $slot }}</span>
                                    <span class="classification-code">OPEN</span>
                                </div>
                                <p class="slot-Empty-copy">
                                    Unassigned focus slot. Pin any task from the ledger below or check "Lock to Zenith Sequence" when logging.
                                </p>
                            </div>
                            <div class="slot-footer">
                                <span class="slot-duration">000 MIN</span>
                                <span class="classification-code">STANDBY</span>
                            </div>
                        </article>
                    @endif
                @endfor
            </div>
        </section>

        <section class="capture-command-section" aria-labelledby="log-commitment-heading">
            <div class="section-header-bar">
                <h2 id="log-commitment-heading" class="section-heading">Log New Commitment</h2>
                <div class="section-meta">
                    <span>DIRECT MVC ENTRY</span>
                </div>
            </div>

            <form method="POST" action="{{ route('tasks.store') }}" class="capture-form-shell">
                @csrf
                <input type="hidden" name="lens" value="{{ $filters['lens'] }}">
                <div class="capture-primary-grid">
                    <div class="field-group">
                        <label for="task-title-input" class="field-label">Task or Observation Title</label>
                        <input id="task-title-input" type="text" name="title" value="{{ old('title') }}" class="almanac-input" placeholder="Describe the deliverable or check-in..." required maxlength="180">
                    </div>

                    <div class="field-group">
                        <label for="task-category-select" class="field-label">Classification</label>
                        <select id="task-category-select" name="category_id" class="almanac-select">
                            <option value="">Unclassified</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ (string) old('category_id') === (string) $cat->id ? 'selected' : '' }}>
                                    [{{ $cat->code }}] {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field-group">
                        <label for="task-priority-select" class="field-label">Priority Band</label>
                        <select id="task-priority-select" name="priority" class="almanac-select">
                            @foreach (\App\Models\Task::PRIORITIES as $pKey => $pLabel)
                                <option value="{{ $pKey }}" {{ old('priority', 'medium') === $pKey ? 'selected' : '' }}>
                                    {{ $pLabel }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field-group">
                        <label for="task-window-select" class="field-label">Solar Window</label>
                        <select id="task-window-select" name="time_window" class="almanac-select">
                            @foreach (\App\Models\Task::TIME_WINDOWS as $wKey => $wLabel)
                                <option value="{{ $wKey }}" {{ old('time_window', 'meridian') === $wKey ? 'selected' : '' }}>
                                    {{ str_replace('·', '/', $wLabel) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field-group">
                        <label for="task-due-input" class="field-label">Scheduled Date</label>
                        <input id="task-due-input" type="date" name="due_date" value="{{ old('due_date', now()->toDateString()) }}" class="almanac-input tabular-data">
                    </div>

                    <div class="field-group">
                        <label for="task-minutes-input" class="field-label">Est. Min</label>
                        <input id="task-minutes-input" type="number" name="estimated_minutes" value="{{ old('estimated_minutes', 30) }}" min="5" max="480" step="5" class="almanac-input tabular-data" required>
                    </div>

                    <button type="submit" class="split-action-btn">
                        <span class="btn-main-text">Log Entry</span>
                        <span class="btn-code-cell">+REC</span>
                    </button>
                </div>

                <div class="capture-secondary-bar">
                    <label class="focus-check-label">
                        <input type="checkbox" name="is_focused" value="1" {{ old('is_focused') ? 'checked' : '' }} {{ $focusSlotsCount >= $maxFocusSlots ? 'disabled' : '' }}>
                        <span>Lock directly into Zenith Focus sequence ({{ $focusSlotsCount }}/{{ $maxFocusSlots }} slots used)</span>
                    </label>

                    <details class="notes-drawer" {{ old('notes') ? 'open' : '' }}>
                        <summary class="notes-summary">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="square" aria-hidden="true">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            <span>Attach observation notes or acceptance criteria</span>
                        </summary>
                        <textarea name="notes" class="almanac-textarea" placeholder="Add technical context, file references, or checkpoints...">{{ old('notes') }}</textarea>
                    </details>
                </div>
            </form>
        </section>

        <section aria-labelledby="observation-ledger-heading">
            <div class="section-header-bar">
                <h2 id="observation-ledger-heading" class="section-heading">Observation Ledger</h2>
                <div class="section-meta">
                    <span>SHOWING {{ str_pad((string) $tasks->count(), 2, '0', STR_PAD_LEFT) }} {{ $tasks->count() === 1 ? 'RECORD' : 'RECORDS' }}</span>
                    <span>DIAGONAL HATCH MARKS OVERDUE OR CRITICAL-TODAY CONFLICTS</span>
                </div>
            </div>

            <div class="ledger-controls-bar">
                <div class="priority-segmented-bar" role="group" aria-label="Filter by priority band">
                    <a href="{{ route('tasks.index', array_filter(array_merge($filters, ['priority' => '']))) }}"
                       class="priority-segment-link {{ $filters['priority'] === '' ? 'is-active' : '' }}">
                        All Priorities
                    </a>
                    @foreach (\App\Models\Task::PRIORITIES as $pKey => $pLabel)
                        <a href="{{ route('tasks.index', array_filter(array_merge($filters, ['priority' => $pKey]))) }}"
                           class="priority-segment-link {{ $filters['priority'] === $pKey ? 'is-active' : '' }}">
                            {{ $pLabel }}
                        </a>
                    @endforeach
                </div>

                <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.5rem;">
                    <form method="GET" action="{{ route('tasks.index') }}" class="search-filter-form">
                        @if ($filters['lens'] !== 'all')
                            <input type="hidden" name="lens" value="{{ $filters['lens'] }}">
                        @endif
                        @if ($filters['category'] !== '')
                            <input type="hidden" name="category" value="{{ $filters['category'] }}">
                        @endif
                        @if ($filters['priority'] !== '')
                            <input type="hidden" name="priority" value="{{ $filters['priority'] }}">
                        @endif
                        @if ($filters['window'] !== '')
                            <input type="hidden" name="window" value="{{ $filters['window'] }}">
                        @endif
                        <div class="search-input-wrap">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="square" aria-hidden="true">
                                <circle cx="11" cy="11" r="7"></circle>
                                <line x1="20" y1="20" x2="16.35" y2="16.35"></line>
                            </svg>
                            <input type="search" name="q" value="{{ $filters['q'] }}" class="search-input" placeholder="Search title or notes..." aria-label="Search tasks by title or notes">
                        </div>
                        <button type="submit" class="search-btn">Filter</button>
                    </form>

                    @if ($hasActiveFilters)
                        <a href="{{ route('tasks.index') }}" class="reset-filter-link">Reset Filters</a>
                    @endif

                    @if ($lensCounts['completed'] > 0)
                        <form method="POST" action="{{ route('tasks.clear-completed') }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="clear-completed-btn">
                                <span>Purge Completed ({{ $lensCounts['completed'] }})</span>
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="ledger-table-wrap">
                @if ($tasks->isEmpty())
                    <div class="empty-ledger-box">
                        <h3 class="empty-ledger-heading">No observations match the active horizon lens</h3>
                        <p class="empty-ledger-copy">
                            Log a new task above or reset the active lens filters to view all scheduled entries in the almanac.
                        </p>
                        @if ($hasActiveFilters)
                            <a href="{{ route('tasks.index') }}" class="reset-filter-link">Return to All Observations</a>
                        @endif
                    </div>
                @else
                    <table class="almanac-ledger-table">
                        <thead>
                            <tr>
                                <th scope="col" style="width: 46px;">STATE</th>
                                <th scope="col">COMMITMENT &amp; OBSERVATION NOTES</th>
                                <th scope="col">CLASSIFICATION</th>
                                <th scope="col">WINDOW / LOAD</th>
                                <th scope="col">PRIORITY</th>
                                <th scope="col">SCHEDULED</th>
                                <th scope="col">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tasks as $task)
                                @php
                                    $hasHatch = $task->hasConflictHatch();
                                    $isOverdue = $task->isOverdue();
                                    $isToday = $task->isDueToday();
                                    $isEditing = $editingTaskId === $task->id;
                                @endphp
                                <tr class="ledger-row {{ $hasHatch ? 'has-conflict-hatch' : '' }} {{ $task->is_completed ? 'is-completed-row' : '' }}">
                                    <td>
                                        <form method="POST" action="{{ route('tasks.toggle-complete', $task) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="check-toggle-btn {{ $task->is_completed ? 'is-checked' : '' }}"
                                                    title="{{ $task->is_completed ? 'Mark incomplete' : 'Mark complete' }}"
                                                    aria-label="{{ $task->is_completed ? 'Mark ' . $task->title . ' incomplete' : 'Mark ' . $task->title . ' complete' }}">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="square" aria-hidden="true">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                            </button>
                                        </form>
                                    </td>

                                    <td>
                                        <div class="entry-title-line">
                                            <span class="entry-title-text">{{ $task->title }}</span>
                                            @if ($task->is_focused)
                                                <span class="focus-pin-badge">LEG 0{{ $task->focus_order }}</span>
                                            @endif
                                            @if ($isOverdue)
                                                <span class="conflict-hatch-badge">CONFLICT / OVERDUE</span>
                                            @elseif ($hasHatch)
                                                <span class="conflict-hatch-badge">CRITICAL TODAY</span>
                                            @endif
                                        </div>
                                        @if ($task->notes)
                                            <p class="entry-notes-text">{{ $task->notes }}</p>
                                        @endif
                                    </td>

                                    <td>
                                        @if ($task->category)
                                            <span class="classification-badge">
                                                <span class="category-swatch" style="background-color: {{ $task->category->color }};"></span>
                                                <span class="classification-code">{{ $task->category->code }}</span>
                                                <span>{{ $task->category->name }}</span>
                                            </span>
                                        @else
                                            <span class="classification-code">UNCL</span>
                                        @endif
                                    </td>

                                    <td>
                                        <div class="tabular-data" style="font-size: 0.72rem; color: var(--ink-primary); font-weight: 600; white-space: nowrap;">
                                            {{ str_replace('·', '/', $task->windowShortLabel()) }}
                                        </div>
                                        <div class="tabular-data" style="font-size: 0.7rem; color: var(--ink-secondary); white-space: nowrap; margin-top: 0.15rem;">
                                            {{ str_pad((string) $task->estimated_minutes, 3, '0', STR_PAD_LEFT) }} MIN
                                        </div>
                                    </td>

                                    <td>
                                        <form method="POST" action="{{ route('tasks.cycle-priority', $task) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="priority-badge-btn priority-{{ $task->priority }}"
                                                    title="Click to cycle priority band">
                                                <span>{{ strtoupper($task->priorityLabel()) }}</span>
                                            </button>
                                        </form>
                                    </td>

                                    <td>
                                        @if ($task->due_date)
                                            <span class="due-date-cell {{ $isOverdue ? 'is-overdue' : ($isToday ? 'is-today' : '') }}">
                                                {{ $task->due_date->format('Y-m-d') }}
                                                @if ($isOverdue)
                                                    (PAST)
                                                @elseif ($isToday)
                                                    (TODAY)
                                                @endif
                                            </span>
                                        @else
                                            <span class="due-date-cell">UNSCHEDULED</span>
                                        @endif
                                    </td>

                                    <td>
                                        <div class="row-actions-group">
                                            <form method="POST" action="{{ route('tasks.toggle-focus', $task) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="row-icon-btn {{ $task->is_focused ? 'is-pinned' : '' }}"
                                                        title="{{ $task->is_focused ? 'Remove from Zenith Focus sequence' : 'Pin to Zenith Focus sequence' }}">
                                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="square" aria-hidden="true">
                                                        <circle cx="12" cy="12" r="8"></circle>
                                                        <circle cx="12" cy="12" r="2.5"></circle>
                                                    </svg>
                                                    <span>{{ $task->is_focused ? 'Unpin' : 'Focus' }}</span>
                                                </button>
                                            </form>

                                            <a href="{{ route('tasks.index', array_filter(array_merge($filters, ['edit' => $isEditing ? null : $task->id]))) }}"
                                               class="row-icon-btn">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="square" aria-hidden="true">
                                                    <path d="M12 20h9"></path>
                                                    <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"></path>
                                                </svg>
                                                <span>{{ $isEditing ? 'Close' : 'Edit' }}</span>
                                            </a>

                                            <form method="POST" action="{{ route('tasks.destroy', $task) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="row-icon-btn is-danger"
                                                        title="Delete entry"
                                                        aria-label="Delete {{ $task->title }}">
                                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="square" aria-hidden="true">
                                                        <polyline points="3 6 5 6 21 6"></polyline>
                                                        <path d="M19 6v14H5V6"></path>
                                                        <path d="M9 6V4h6v2"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                @if ($isEditing)
                                    <tr class="inline-edit-row">
                                        <td colspan="7">
                                            <form method="POST" action="{{ route('tasks.update', $task) }}">
                                                @csrf
                                                @method('PUT')
                                                <div class="inline-edit-grid">
                                                    <div class="field-group">
                                                        <label class="field-label" for="edit-title-{{ $task->id }}">Title</label>
                                                        <input id="edit-title-{{ $task->id }}" type="text" name="title" value="{{ $task->title }}" class="almanac-input" required maxlength="180">
                                                    </div>
                                                    <div class="field-group">
                                                        <label class="field-label" for="edit-cat-{{ $task->id }}">Classification</label>
                                                        <select id="edit-cat-{{ $task->id }}" name="category_id" class="almanac-select">
                                                            <option value="">Unclassified</option>
                                                            @foreach ($categories as $cat)
                                                                <option value="{{ $cat->id }}" {{ $task->category_id === $cat->id ? 'selected' : '' }}>
                                                                    [{{ $cat->code }}] {{ $cat->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="field-group">
                                                        <label class="field-label" for="edit-prio-{{ $task->id }}">Priority Band</label>
                                                        <select id="edit-prio-{{ $task->id }}" name="priority" class="almanac-select">
                                                            @foreach (\App\Models\Task::PRIORITIES as $pKey => $pLabel)
                                                                <option value="{{ $pKey }}" {{ $task->priority === $pKey ? 'selected' : '' }}>{{ $pLabel }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="field-group">
                                                        <label class="field-label" for="edit-win-{{ $task->id }}">Solar Window</label>
                                                        <select id="edit-win-{{ $task->id }}" name="time_window" class="almanac-select">
                                                            @foreach (\App\Models\Task::TIME_WINDOWS as $wKey => $wLabel)
                                                                <option value="{{ $wKey }}" {{ $task->time_window === $wKey ? 'selected' : '' }}>
                                                                    {{ str_replace('·', '/', $wLabel) }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="field-group">
                                                        <label class="field-label" for="edit-due-{{ $task->id }}">Scheduled Date</label>
                                                        <input id="edit-due-{{ $task->id }}" type="date" name="due_date" value="{{ $task->due_date?->format('Y-m-d') }}" class="almanac-input tabular-data">
                                                    </div>
                                                    <div class="field-group">
                                                        <label class="field-label" for="edit-min-{{ $task->id }}">Est. Min</label>
                                                        <input id="edit-min-{{ $task->id }}" type="number" name="estimated_minutes" value="{{ $task->estimated_minutes }}" min="5" max="480" step="5" class="almanac-input tabular-data" required>
                                                    </div>
                                                </div>

                                                <div class="field-group">
                                                    <label class="field-label" for="edit-notes-{{ $task->id }}">Observation Notes</label>
                                                    <textarea id="edit-notes-{{ $task->id }}" name="notes" class="almanac-textarea">{{ $task->notes }}</textarea>
                                                </div>

                                                <div class="inline-edit-footer">
                                                    <a href="{{ route('tasks.index', $filters) }}" class="reset-filter-link">Cancel Edit</a>
                                                    <button type="submit" class="split-action-btn">
                                                        <span class="btn-main-text">Save Changes</span>
                                                        <span class="btn-code-cell">UPD</span>
                                                    </button>
                                                </div>
                                            </form>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </section>
    </main>
</div>
@endsection
