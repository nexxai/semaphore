<?php

use App\Models\Day;
use App\Models\Task;

beforeEach(function () {
    $this->task = Task::factory()->create();
    $this->day = Day::factory()->create();
});

it('belongs to a day', function () {
    $this->task->days()->attach($this->day);
    expect($this->task->days->first())->toBeInstanceOf(Day::class);
});

it('can have many categories', function () {
    $this->task->attachTag('urgent');
    $this->task->attachTag('home');

    expect($this->task->tags)->toHaveCount(2)
        ->and($this->task->tags->pluck('name'))->toContain('urgent', 'home');
});

it('can return the tasks grouped by category', function () {
    $task1 = Task::factory()->create();
    $task1->attachTag('urgent');
    $task1->attachTag('work');
    $task2 = Task::factory()->create();
    $task2->attachTag('home');
    $task3 = Task::factory()->create();
    $task3->attachTag('urgent');
    $task4 = Task::factory()->create();
    $task4->attachTag('work');

    $groupedTasks = Task::groupByCategories();
    expect($groupedTasks)->toHaveCount(3)
        ->and($groupedTasks->keys())->toContain('urgent', 'home', 'work')
        ->and($groupedTasks['urgent'])->toHaveCount(2)
        ->and($groupedTasks['home'])->toHaveCount(1)
        ->and($groupedTasks['work'])->toHaveCount(2);
});
