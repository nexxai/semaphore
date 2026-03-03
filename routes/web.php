<?php

use App\Http\Controllers\TaskController;
use App\Models\Day;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    if (! Auth::check()) {
        $user = User::updateOrCreate([
            'email' => 'semaphore@example.org',
        ], [
            'name' => 'Semaphore',
            'password' => bcrypt('password'),
        ]);
        Auth::login($user);
    }

    $currentDay = Day::getCurrentDay();
    $dayTasks = $currentDay->dayTasks()->with('task', 'subtasks')->get();

    return Inertia::render('welcome', [
        'tasks' => Task::all()->diff($currentDay->tasks),
        'current_day' => $currentDay,
        'dayTasks' => $dayTasks,
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

    Route::post('/subtasks/add', [TaskController::class, 'addSubtask'])
        ->name('subtasks.add');

    Route::post('/subtasks/complete', [TaskController::class, 'completeSubtask'])
        ->name('subtasks.complete');

    Route::post('/subtasks/notcomplete', [TaskController::class, 'notcompleteSubtask'])
        ->name('subtasks.notcomplete');

    Route::post('/subtasks/remove', [TaskController::class, 'removeSubtask'])
        ->name('subtasks.remove');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
