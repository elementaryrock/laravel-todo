<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TaskController::class, 'index'])->name('tasks.index');
Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
Route::patch('/tasks/{task}/toggle-complete', [TaskController::class, 'toggleComplete'])->name('tasks.toggle-complete');
Route::patch('/tasks/{task}/toggle-focus', [TaskController::class, 'toggleFocus'])->name('tasks.toggle-focus');
Route::patch('/tasks/{task}/cycle-priority', [TaskController::class, 'cyclePriority'])->name('tasks.cycle-priority');
Route::delete('/tasks/completed/clear', [TaskController::class, 'clearCompleted'])->name('tasks.clear-completed');
Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
