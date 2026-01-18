import { type Task } from '@/types';
import { router, useForm } from '@inertiajs/react';
import { useState } from 'react';
import { twMerge } from 'tailwind-merge';
import { Checkbox } from './ui/checkbox';
import { Input } from './ui/input';

export default function ActiveTask({ task }: { task: Task }) {
    const [showAddSubtask, setShowAddSubtask] = useState<boolean>(false);

    const { data, setData, post, errors } = useForm({
        dayTaskId: task.pivot?.id,
        name: '',
    });

    const addSubtask = () => {
        post(route('tasks.subtasks.add'), {
            onSuccess: () => {
                setData('name', '');
                setShowAddSubtask(false);
            },
        });
    };

    const toggleShowAddSubtask = () => {
        setShowAddSubtask(!showAddSubtask);
    };

    const markAsCompleted = (taskId: number) => {
        router.post('/tasks/complete', { taskId });
    };

    const markAsNotCompleted = (taskId: number) => {
        router.post('/tasks/notcomplete', { taskId });
    };

    const toggleTaskCompleted = (taskId: number) => {
        if (task.pivot && task.pivot.completed) {
            markAsNotCompleted(taskId);
        } else {
            markAsCompleted(taskId);
        }
    };

    const toggleSubtaskCompleted = (subtask: Subtask) => {
        if (subtask.completed) {
            router.post('/tasks/subtasks/notcomplete', { subtaskId: subtask.id });
        } else {
            router.post('/tasks/subtasks/complete', { subtaskId: subtask.id });
        }
    };

    const removeSubtask = (subtaskId: number) => {
        router.post('/tasks/subtasks/remove', { subtaskId });
    };

    return (
        <div className="mb-4">
            <div
                className={twMerge(
                    'flex h-9 items-center rounded-md border border-input bg-transparent px-3 py-1 text-primary shadow-xs transition-all outline-none md:text-sm',
                    task.pivot && task.pivot.completed ? 'text-gray-500 line-through dark:text-gray-400' : '',
                )}
            >
                <Checkbox
                    checked={task.pivot && task.pivot.completed}
                    onCheckedChange={() => (task.pivot && !task.pivot.completed ? markAsCompleted(task.id) : markAsNotCompleted(task.id))}
                    className="mr-2"
                />
                {task.name}
            </div>

            {/* Subtasks */}
            <div className="mt-2 ml-6 space-y-1">
                {task.pivot?.subtasks && task.pivot.subtasks.length > 0 ? (
                    task.pivot.subtasks.map((subtask) => (
                        <div key={subtask.id} className="flex items-center">
                            <Checkbox checked={subtask.completed} onCheckedChange={() => toggleSubtaskCompleted(subtask)} className="mr-2" />
                            <span className={twMerge('text-sm', subtask.completed ? 'text-gray-500 line-through dark:text-gray-400' : '')}>
                                {subtask.name}
                            </span>
                            <button onClick={() => removeSubtask(subtask.id)} className="ml-2 text-xs text-red-500 hover:text-red-700">
                                ×
                            </button>
                        </div>
                    ))
                ) : (
                    <p className="text-sm text-gray-500 dark:text-gray-400">No subtasks</p>
                )}
            </div>

            {/* Add subtask */}
            <div className="mt-2 ml-6 flex items-baseline text-sm font-semibold text-gray-700 dark:text-gray-300">
                <button className="hover:cursor-pointer" onClick={toggleShowAddSubtask}>
                    + Add Subtask
                </button>
            </div>
            {showAddSubtask && (
                <div className="mt-1 ml-6 w-full">
                    <Input
                        type="text"
                        className="w-full p-2"
                        value={data.name}
                        autoFocus
                        placeholder="Subtask name..."
                        onKeyDown={(e) => {
                            if (e.key === 'Enter') {
                                addSubtask();
                            }
                            if (e.key === 'Escape') {
                                setShowAddSubtask(false);
                                setData('name', '');
                            }
                        }}
                        onChange={(e) => {
                            setData('name', e.target.value);
                        }}
                    />
                    {errors.name && <p className="mb-2 text-sm text-red-500">{errors.name}</p>}
                </div>
            )}
        </div>
    );
}
