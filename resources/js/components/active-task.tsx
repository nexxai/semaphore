import { Badge } from '@/components/ui/badge';
import { type Task } from '@/types';
import { router, useForm } from '@inertiajs/react';
import { CheckSquareIcon, NotebookPenIcon, PencilIcon, XIcon } from 'lucide-react';
import { useState } from 'react';
import { twMerge } from 'tailwind-merge';
import { Button } from './ui/button';
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
                    className={twMerge(
                        'mr-2 transition hover:scale-105 hover:cursor-pointer',
                        task.pivot && task.pivot.completed ? 'line-through' : '',
                    )}
                    size="lg"
                    onClick={() => (task.pivot && !task.pivot.completed ? removeTask(task.id) : markAsNotCompleted(task.id))}
                >
                    {task.pivot && !task.pivot.completed && <XIcon />}
                    {task.name}
                </Badge>
                {task.pivot && !task.pivot.completed && (
                    <Badge variant="outline" className="hover:scale-105 hover:cursor-pointer" onClick={() => markAsCompleted(task.id)}>
                        <CheckSquareIcon /> Mark as completed
                    </Badge>
                )}
            </p>
            <div className="mt-1 flex items-center">
                <Button size="xs" variant="secondary" className="mr-2 text-sm hover:cursor-pointer" onClick={() => toggleShowAddDescription()}>
                    {task.pivot && task.pivot.description ? <PencilIcon /> : <NotebookPenIcon />}
                </Button>
                {!showAddDescription && task.pivot && task.pivot.description && (
                    <p className="text-sm text-gray-500 dark:text-gray-400" onClick={() => setShowAddDescription(true)}>
                        {task.pivot.description}
                    </p>
                )}
                {showAddDescription && (
                    <div>
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
