<?php

use App\Http\Controllers\TaskController;
use App\Models\Day;
use App\Models\Task;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    $day = Day::getCurrentDay();
    $available_tasks = Task::all()->diff($day->tasks);

    return Inertia::render('welcome', [
        'tasks' => $available_tasks,
        'current_day' => $day,
    ]);
})->name('home');

Route::name('tasks.')->prefix('tasks')->group(function () {
    Route::post('/add', [TaskController::class, 'add'])
        ->name('add');

    Route::post('/remove', [TaskController::class, 'remove'])
        ->name('remove');

    Route::post('/create', [TaskController::class, 'create'])
        ->name('create');

    Route::post('/update-description', [TaskController::class, 'updateDescription'])
        ->name('update-description');

    Route::post('/complete', [TaskController::class, 'complete'])
        ->name('complete');

    Route::post('/notcomplete', [TaskController::class, 'notcomplete'])
        ->name('notcomplete');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
