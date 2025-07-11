<?php

use App\Models\Day;
use App\Models\Task;
use Illuminate\Support\Carbon;

beforeEach(function () {
    $this->day = Day::factory()->create();
    $this->task = Task::factory()->create();
});

it('has a date', function () {
    expect($this->day->date)->toBeInstanceOf(Carbon::class);
});

it('can have many tasks', function () {
    $this->day->tasks()->attach($this->task);

    expect($this->day->tasks->first())->toBeInstanceOf(Task::class);
});

it('can retrieve the current day', function () {
    expect(Day::getCurrentDay())->toBeInstanceOf(Day::class)
        ->and(Day::getCurrentDay()->date->isToday())->toBeTrue();
});

it('will not create a new day if the last day has unfinished tasks', function () {
    Carbon::setTestNow(Carbon::create(2025, 1, 1));

    $this->day->tasks()->attach($this->task, ['completed' => false]);
    expect(Day::getCurrentDay()->id)->toBe($this->day->id);

    $this->day->tasks()->updateExistingPivot($this->task->id, ['completed' => true]);
    expect(Day::getCurrentDay()->id)->not->toBe($this->day->id);
});
