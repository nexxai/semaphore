<?php

use App\Models\Day;
use App\Models\DayTask;
use App\Models\Subtask;
use App\Models\Task;

beforeEach(function () {
    $this->day = Day::factory()->create();
    $this->task = Task::factory()->create();
    $this->dayTask = DayTask::factory()->create([
        'day_id' => $this->day->id,
        'task_id' => $this->task->id,
        'completed' => false,
    ]);
});

it('belongs to a day', function () {
    expect($this->dayTask->day)->toBeInstanceOf(Day::class)
        ->and($this->dayTask->day->id)->toBe($this->day->id);
});

it('belongs to a task', function () {
    expect($this->dayTask->task)->toBeInstanceOf(Task::class)
        ->and($this->dayTask->task->id)->toBe($this->task->id);
});

it('can have many subtasks', function () {
    $subtask = Subtask::factory()->create(['day_task_id' => $this->dayTask->id]);

    expect($this->dayTask->subtasks->first())->toBeInstanceOf(Subtask::class)
        ->and($this->dayTask->subtasks->first()->id)->toBe($subtask->id);
});

it('can be completed if it has no subtasks', function () {
    expect($this->dayTask->canBeCompleted())->toBeTrue();
});

it('cannot be completed if it has incomplete subtasks', function () {
    Subtask::factory()->create([
        'day_task_id' => $this->dayTask->id,
        'completed' => false,
    ]);

    expect($this->dayTask->canBeCompleted())->toBeFalse();
});

it('can be completed if all subtasks are completed', function () {
    Subtask::factory()->create([
        'day_task_id' => $this->dayTask->id,
        'completed' => true,
    ]);

    expect($this->dayTask->canBeCompleted())->toBeTrue();
});

it('can mark itself as completed when eligible', function () {
    expect($this->dayTask->markCompleted())->toBeTrue()
        ->and($this->dayTask->fresh()->completed)->toBeTrue();
});

it('cannot mark itself as completed when ineligible', function () {
    Subtask::factory()->create([
        'day_task_id' => $this->dayTask->id,
        'completed' => false,
    ]);

    expect($this->dayTask->markCompleted())->toBeFalse()
        ->and($this->dayTask->fresh()->completed)->toBeFalse();
});
