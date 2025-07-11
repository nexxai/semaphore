<?php

use App\Mail\FirstTaskAddedToDay;
use App\Models\Day;
use App\Models\Task;
use Illuminate\Support\Facades\Mail;

use function Pest\Laravel\postJson;

beforeEach(function () {
    Mail::fake();

    $this->day = Day::factory()->create();
    $this->task = Task::factory()->create();
});

it('will send an email if the task is the first one being added to this day', function () {
    postJson(route('tasks.add'), [
        'taskId' => $this->task->id,
    ])->assertOk();

    Mail::assertQueued(FirstTaskAddedToDay::class, function (FirstTaskAddedToDay $mail) {
        return $mail->hasSubject('[TASKLIST] First Task Added To Day') &&
            $mail->hasTo(config('tasklist.admin_email'));
    });
});
