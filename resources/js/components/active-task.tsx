import { Badge } from '@/components/ui/badge';
import { type Task } from '@/types';
import { router, useForm } from '@inertiajs/react';
import { CheckSquareIcon, MinusIcon } from 'lucide-react';
import { useState } from 'react';
import { twMerge } from 'tailwind-merge';
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

    const removeTask = (taskId: number) => {
        router.post('/tasks/remove', { taskId });
    };

    const markAsCompleted = (taskId: number) => {
        router.post('/tasks/complete', { taskId });
    };

    const markAsNotCompleted = (taskId: number) => {
        router.post('/tasks/notcomplete', { taskId });
    };

    return (
        <div className="mb-2">
            <p>
                <Badge
                    variant="secondary"
                    className={twMerge(
                        'mr-2 transition hover:scale-105 hover:cursor-pointer',
                        task.pivot && task.pivot.completed ? 'line-through' : '',
                    )}
                    onClick={() => (task.pivot && !task.pivot.completed ? removeTask(task.id) : markAsNotCompleted(task.id))}
                >
                    {task.pivot && !task.pivot.completed && <MinusIcon />}
                    {task.name}
                </Badge>
                {task.pivot && !task.pivot.completed && (
                    <Badge variant="outline" className="hover:scale-105 hover:cursor-pointer" onClick={() => markAsCompleted(task.id)}>
                        <CheckSquareIcon />
                    </Badge>
                )}

                <span className="text-sm hover:cursor-pointer" onClick={() => toggleShowAddDescription()}>
                    Add notes
                </span>
            </p>
            {showAddDescription && (
                <p className="mt-2">
                    <Input
                        type="text"
                        className="mb-4 w-full p-2"
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
                </p>
            )}
            {!showAddDescription && task.pivot && task.pivot.description && (
                <p className="text-sm text-gray-500 dark:text-gray-400">{task.pivot.description}</p>
            )}
        </div>
    );
}
