# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Stack

Laravel (PHP 8.2, MVC architecture with Eloquent Models, Controllers, Form Requests/Validation, Blade views, and SQLite persistence).

## Users

An individual professional or maker working from a personal daily workspace who needs to organize daily tasks, deep-work priorities, categories, and deadlines in a single focused view without account or multi-tenant friction.

## Product Purpose

Provide a calm, high-craft personal daily workspace for capturing, prioritizing, categorizing, and completing tasks. Success means the user can immediately see what requires focus today, capture or triage tasks with zero friction, filter by category/priority/status, and track daily momentum.

## Positioning

A tactile, single-workspace daily task desk built on clean Laravel MVC architecture that treats daily planning as a deliberate studio ritual—combining immediate task triage (priority, category, due date, notes, and completion state) with live focus metrics in one cohesive view rather than a fragmented multi-board enterprise tool.

## Operating Context

Used throughout the workday at a desk (morning planning, mid-day deep-work check-ins, and end-of-day triage). The interface is an Operate surface kept open in a browser tab alongside code editors or documents, requiring fast scanning, clear priority/deadline hierarchy, keyboard-friendly task capture, and reliable server-backed persistence.

## Capabilities and Constraints

- Strictly follows Laravel MVC architecture:
  - **Models:** `Task` and `Category` Eloquent models with relationships, scopes, and attributes.
  - **Controllers:** Resourceful `TaskController` and `CategoryController` handling CRUD, status toggles, filtering, and quick actions.
  - **Views:** Server-rendered Blade templates with clean component composition and progressive enhancement.
  - **Database:** SQLite database with migrations and realistic starter seed data.
- Core features:
  - Create, read, update, and delete tasks (title, optional notes/description, priority level, category, due date, completion status, and pinned/focus flag).
  - Filter tasks by view lens (All, Today, Upcoming, Overdue, Completed), category, priority, and search query.
  - Quick state actions: toggle completion, cycle or set priority, pin to Today's Focus, and clear completed tasks.
  - Category management (create/delete custom categories with distinct color tokens).
  - Daily progress and workload summary (completion ratio, high-priority count, due-today count).
- No login wall: opens directly into the personal workspace.

## Evidence on Hand

Greenfield project in `c:\Users\maana\Documents\laravel-todo`. Demo tasks and categories seeded in SQLite must be realistic personal studio/engineering tasks and labeled or structured clearly as sample workspace data.

## Product Principles

1. **Immediate Triage Over Deep Nesting:** Every task's priority, category, due date, and completion state should be readable at a glance and actionable in one click.
2. **Focus First:** Separate the vital few tasks requiring attention today from the broader backlog so the workspace reduces cognitive load rather than amplifying it.
3. **True MVC Clarity:** Keep business logic and query filtering in Laravel Models and Controllers, using Blade views purely for clear, semantic presentation.
4. **Honest Tactility:** Visual hierarchy, typography, and state transitions should feel like a precision physical desk tool—never decorative clutter that slows down task entry.

