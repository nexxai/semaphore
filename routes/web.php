<?php

use App\Http\Controllers\TaskController;
use App\Models\Day;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    if (! auth()->check()) {
        $user = User::factory()->create();
        auth()->login($user);
    }

    return Inertia::render('welcome', [
        'tasks' => fn () => Task::all()->diff(Day::getCurrentDay()->tasks),
        'current_day' => fn () => Day::getCurrentDay(),
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
