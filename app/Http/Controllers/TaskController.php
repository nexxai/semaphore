<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Mail\FirstTaskAddedToDay;
use App\Models\Day;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class TaskController extends Controller
{
    public function add(TaskRequest $request)
    {
        $day = Day::getCurrentDay();

        $taskId = $request->input('taskId');

        if (! $day->tasks()->where('task_id', $taskId)->exists()) {
            $task = Task::find($taskId);

            $day->tasks()->attach($task, ['completed' => false]);
        }

        // Check if this is the first task added to the day
        if ($day->tasks()->count() === 1) {
            // Send an email notification
            Mail::to(config('tasklist.admin_email'))
                ->queue(new FirstTaskAddedToDay($day, $task));
        }
    }

    public function remove(TaskRequest $request)
    {
        $day = Day::getCurrentDay();

        $taskId = $request->input('taskId');

        if ($day->tasks()->where('task_id', $taskId)->exists()) {
            $day->tasks()->detach($taskId);
        }
    }

    public function create(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        Task::create([
            'name' => $request->input('name'),
            'description' => $request->input('description', null),
        ]);
    }

    public function complete(TaskRequest $request)
    {
        $day = Day::getCurrentDay();

        $taskId = $request->input('taskId');

        if ($day->tasks()->where('task_id', $taskId)->exists()) {
            $day->tasks()->updateExistingPivot($taskId, ['completed' => true]);
        }
    }

    public function notcomplete(TaskRequest $request)
    {
        $day = Day::getCurrentDay();

        $taskId = $request->input('taskId');

        if ($day->tasks()->where('task_id', $taskId)->exists()) {
            $day->tasks()->updateExistingPivot($taskId, ['completed' => false]);
        }
    }

    public function updateDescription(Request $request)
    {
        $request->validate([
            'taskId' => 'required|exists:tasks,id',
            'description' => 'nullable|string|max:1000',
        ]);
        $day = Day::getCurrentDay();

        $task = $day->tasks()->where('task_id', $request->input('taskId'))->withPivot('description')->first();
        $task->pivot->description = $request->input('description');
        $task->pivot->save();
    }
}
