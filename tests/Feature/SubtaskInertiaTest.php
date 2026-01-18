<?php

use App\Models\Day;
use App\Models\DayTask;
use App\Models\Subtask;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Event;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\postJson;

beforeEach(function () {
    Event::fake();

    test()->user = User::factory()->create();
    test()->day = Day::getCurrentDay();
    test()->task = Task::factory()->create();
    test()->dayTask = DayTask::factory()->create([
        'day_id' => test()->day->id,
        'task_id' => test()->task->id,
        'completed' => false,
    ]);
});

it('loads the welcome page with dayTasks prop', function () {
    actingAs(test()->user);

    get(route('home'))
        ->assertInertia(fn ($page) => $page
            ->has('dayTasks', 1)
            ->where('dayTasks.0.id', test()->dayTask->id)
            ->where('dayTasks.0.task.name', test()->task->name)
            ->where('dayTasks.0.completed', false)
            ->has('dayTasks.0.subtasks', 0)
        );
});

it('can add a subtask to a day task', function () {
    actingAs(test()->user);

    postJson(route('tasks.subtasks.add'), [
        'dayTaskId' => test()->dayTask->id,
        'name' => 'Test subtask',
    ])->assertOk();

    expect(test()->dayTask->fresh()->subtasks)->toHaveCount(1);
    expect(test()->dayTask->fresh()->subtasks->first()->name)->toBe('Test subtask');
    expect(test()->dayTask->fresh()->subtasks->first()->completed)->toBe(false);
});

it('can complete a subtask', function () {
    actingAs(test()->user);

    $subtask = Subtask::factory()->create([
        'day_task_id' => test()->dayTask->id,
        'completed' => false,
    ]);

    postJson(route('tasks.subtasks.complete'), [
        'subtaskId' => $subtask->id,
    ])->assertOk();

    expect($subtask->fresh()->completed)->toBe(true);
});

it('can mark a subtask as incomplete', function () {
    actingAs(test()->user);

    $subtask = Subtask::factory()->create([
        'day_task_id' => test()->dayTask->id,
        'completed' => true,
    ]);

    postJson(route('tasks.subtasks.notcomplete'), [
        'subtaskId' => $subtask->id,
    ])->assertOk();

    expect($subtask->fresh()->completed)->toBe(false);
});

it('can remove a subtask', function () {
    actingAs(test()->user);

    $subtask = Subtask::factory()->create([
        'day_task_id' => test()->dayTask->id,
    ]);

    postJson(route('tasks.subtasks.remove'), [
        'subtaskId' => $subtask->id,
    ])->assertOk();

    expect(Subtask::find($subtask->id))->toBeNull();
    expect(test()->dayTask->fresh()->subtasks)->toHaveCount(0);
});

it('cannot complete a day task if it has incomplete subtasks', function () {
    actingAs(test()->user);

    // Add an incomplete subtask
    Subtask::factory()->create([
        'day_task_id' => test()->dayTask->id,
        'completed' => false,
    ]);

    postJson(route('tasks.complete'), [
        'taskId' => test()->task->id,
    ])->assertOk();

    expect(test()->dayTask->fresh()->completed)->toBe(false);
});

it('can complete a day task if all subtasks are completed', function () {
    actingAs(test()->user);

    // Add a completed subtask
    Subtask::factory()->create([
        'day_task_id' => test()->dayTask->id,
        'completed' => true,
    ]);

    postJson(route('tasks.complete'), [
        'taskId' => test()->task->id,
    ])->assertOk();

    expect(test()->dayTask->fresh()->completed)->toBe(true);
});

it('can complete a day task with no subtasks', function () {
    actingAs(test()->user);

    // No subtasks
    expect(test()->dayTask->subtasks)->toHaveCount(0);

    postJson(route('tasks.complete'), [
        'taskId' => test()->task->id,
    ])->assertOk();

    expect(test()->dayTask->fresh()->completed)->toBe(true);
});

it('validates subtask addition request', function () {
    actingAs(test()->user);

    postJson(route('tasks.subtasks.add'), [
        'dayTaskId' => '', // Invalid
        'name' => '',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['dayTaskId', 'name']);
});

it('validates subtask completion request', function () {
    actingAs(test()->user);

    postJson(route('tasks.subtasks.complete'), [
        'subtaskId' => '', // Invalid
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['subtaskId']);
});

it('validates subtask removal request', function () {
    actingAs(test()->user);

    postJson(route('tasks.subtasks.remove'), [
        'subtaskId' => '', // Invalid
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['subtaskId']);
});
