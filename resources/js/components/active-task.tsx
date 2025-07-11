import { type Task } from '@/types';
import { router, useForm } from '@inertiajs/react';
import { useState } from 'react';
import { twMerge } from 'tailwind-merge';
import { Checkbox } from './ui/checkbox';
import { Input } from './ui/input';

export default function ActiveTask({ task }: { task: Task }) {
    const [showAddDescription, setShowAddDescription] = useState<boolean>(false);

    const { data, setData, post, errors } = useForm({
        taskId: task.id,
        description: task.pivot?.description || '',
    });

    const updateDescription = () => {
        post(route('tasks.update-description'), {
            onSuccess: () => {
                setShowAddDescription(false);
            },
        });
    };

    const toggleShowAddDescription = () => {
        setShowAddDescription(!showAddDescription);
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

    return (
        <div className="mb-4">
            <div
                onClick={() => toggleTaskCompleted(task.id)}
                className={twMerge(
                    'flex h-9 items-center rounded-md border border-input bg-transparent px-3 py-1 text-primary shadow-xs transition-all outline-none hover:cursor-pointer md:text-sm',
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
            <div className="mt-2 flex items-baseline text-sm font-semibold text-gray-700 dark:text-gray-300">
                <div>Notes</div>
                <button className="ml-1 text-xs font-medium hover:cursor-pointer" onClick={() => toggleShowAddDescription()}>
                    ({task.pivot && task.pivot.description ? 'Edit' : 'Add'})
                </button>
            </div>
            <div className="mt-1 flex w-full items-center">
                {!showAddDescription && task.pivot && (
                    <p className="text-sm text-gray-500 dark:text-gray-400" onClick={() => setShowAddDescription(true)}>
                        {task.pivot.description ? task.pivot.description : 'No notes'}
                    </p>
                )}
                {showAddDescription && (
                    <div className="w-full">
                        <Input
                            type="text"
                            className="w-full p-2"
                            value={data.description}
                            autoFocus
                            placeholder="e.g. Make sure to get under the bed"
                            onKeyDown={(e) => {
                                if (e.key === 'Enter') {
                                    updateDescription();
                                }
                                if (e.key === 'Escape') {
                                    setShowAddDescription(false);
                                }
                            }}
                            onChange={(e) => {
                                setData('description', e.target.value);
                            }}
                        />
                        {errors.description && <p className="mb-2 text-sm text-red-500">{errors.description}</p>}
                    </div>
                )}
            </div>
        </div>
    );
}
