<?php

namespace App\Http\Controllers;

use App\Events\TaskUpdated;
use App\Http\Requests\TaskRequest;
use App\Mail\FirstTaskAddedToDay;
use App\Models\Day;
use App\Models\DayTask;
use App\Models\Subtask;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class TaskController extends Controller
{
    public function add(TaskRequest $request)
    {
        $taskId = $request->input('taskId');
        $task = Task::find($taskId);

        $day = Day::getCurrentDay();

        if (! $day->dayTasks()->where('task_id', $taskId)->exists()) {
            $day->dayTasks()->create([
                'task_id' => $taskId,
                'completed' => false,
            ]);
        }

        // Check if this is the first task added to the day
        if ($day->dayTasks()->count() === 1) {
            // Send an email notification
            Mail::to(config('tasklist.admin_email'))
                ->queue(new FirstTaskAddedToDay($day, $task));
        }

        // Broadcast task update
        broadcast(new TaskUpdated($day, Auth::user()));
    }

    public function remove(TaskRequest $request)
    {
        $day = Day::getCurrentDay();

        $taskId = $request->input('taskId');

        $dayTask = $day->dayTasks()->where('task_id', $taskId)->first();
        if ($dayTask) {
            $dayTask->delete();
        }

        // Broadcast task update
        broadcast(new TaskUpdated($day, Auth::user()));
    }

    public function create(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        Task::create([
            'name' => $request->input('name'),
            'description' => $request->input('description', null),
        ]);

        broadcast(new TaskUpdated(Day::getCurrentDay(), Auth::user()))->toOthers();
    }

    public function complete(TaskRequest $request)
    {
        $day = Day::getCurrentDay();

        $taskId = $request->input('taskId');

        $dayTask = $day->dayTasks()->where('task_id', $taskId)->first();
        if ($dayTask && $dayTask->markCompleted()) {
            // Successfully marked as completed
        }

        // Broadcast task update
        broadcast(new TaskUpdated($day, Auth::user()));
    }

    public function notcomplete(TaskRequest $request)
    {
        $day = Day::getCurrentDay();

        $taskId = $request->input('taskId');

        $dayTask = $day->dayTasks()->where('task_id', $taskId)->first();
        if ($dayTask) {
            $dayTask->update(['completed' => false]);
        }

        // Broadcast task update
        broadcast(new TaskUpdated($day, Auth::user()));
    }

    public function updateDescription(Request $request)
    {
        $request->validate([
            'taskId' => 'required|exists:tasks,id',
            'description' => 'nullable|string|max:1000',
        ]);
        $day = Day::getCurrentDay();

        $dayTask = $day->dayTasks()->where('task_id', $request->input('taskId'))->first();
        if ($dayTask) {
            $dayTask->update(['description' => $request->input('description')]);
        }

        // Broadcast task update
        broadcast(new TaskUpdated($day, Auth::user()));
    }

    public function addSubtask(Request $request)
    {
        $request->validate([
            'dayTaskId' => 'required|exists:day_task,id',
            'name' => 'required|string|max:255',
        ]);

        $dayTask = DayTask::find($request->input('dayTaskId'));
        $dayTask->subtasks()->create([
            'name' => $request->input('name'),
            'completed' => false,
        ]);

        // Broadcast task update
        broadcast(new TaskUpdated($dayTask->day, Auth::user()))->toOthers();
    }

    public function completeSubtask(Request $request)
    {
        $request->validate([
            'subtaskId' => 'required|exists:subtasks,id',
        ]);

        $subtask = Subtask::find($request->input('subtaskId'));
        $subtask->update(['completed' => true]);

        // Broadcast task update
        broadcast(new TaskUpdated($subtask->dayTask->day, Auth::user()))->toOthers();
    }

    public function notcompleteSubtask(Request $request)
    {
        $request->validate([
            'subtaskId' => 'required|exists:subtasks,id',
        ]);

        $subtask = Subtask::find($request->input('subtaskId'));
        $subtask->update(['completed' => false]);

        // Broadcast task update
        broadcast(new TaskUpdated($subtask->dayTask->day, Auth::user()))->toOthers();
    }

    public function removeSubtask(Request $request)
    {
        $request->validate([
            'subtaskId' => 'required|exists:subtasks,id',
        ]);

        $subtask = Subtask::find($request->input('subtaskId'));
        $day = $subtask->dayTask->day;
        $subtask->delete();

        // Broadcast task update
        broadcast(new TaskUpdated($day, Auth::user()));
    }
}
