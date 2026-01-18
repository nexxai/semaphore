<?php

use App\Models\DayTask;
use App\Models\Subtask;

beforeEach(function () {
    $this->dayTask = DayTask::factory()->create();
    $this->subtask = Subtask::factory()->create([
        'day_task_id' => $this->dayTask->id,
        'completed' => false,
    ]);
});

it('belongs to a day task', function () {
    expect($this->subtask->dayTask)->toBeInstanceOf(DayTask::class)
        ->and($this->subtask->dayTask->id)->toBe($this->dayTask->id);
});

it('has a name', function () {
    expect($this->subtask->name)->toBeString();
});

it('has a completed status', function () {
    expect($this->subtask->completed)->toBeBool();
});

it('can be marked as completed', function () {
    $this->subtask->update(['completed' => true]);

    expect($this->subtask->fresh()->completed)->toBeTrue();
});
