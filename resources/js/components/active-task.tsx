import { Badge } from '@/components/ui/badge';
import { type Task } from '@/types';
import { router } from '@inertiajs/react';
import { useDebounce } from '@uidotdev/usehooks';
import { CheckSquareIcon, MinusIcon } from 'lucide-react';
import { useEffect, useState } from 'react';
import { twMerge } from 'tailwind-merge';
import { Input } from './ui/input';

export default function ActiveTask({ task }: { task: Task }) {
    const [description, setDescription] = useState<string>(task.pivot?.description || '');
    const [showAddDescription, setShowAddDescription] = useState<boolean>(false);
    const debouncedDescription = useDebounce(description, 500);

    useEffect(() => {
        if (task.pivot && task.pivot.description !== debouncedDescription) {
            router.post('/tasks/update-description', {
                taskId: task.id,
                description: debouncedDescription,
            });
        }
    }, [debouncedDescription, task.pivot, task.id]);

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
                        value={description}
                        autoFocus
                        placeholder="e.g. Make sure to get under the bed"
                        onKeyDown={(e) => {
                            if (e.key === 'Enter') {
                                setShowAddDescription(false);
                            }
                            if (e.key === 'Escape') {
                                setShowAddDescription(false);
                            }
                        }}
                        onChange={(e) => {
                            setDescription(e.target.value);
                        }}
                    />
                </p>
            )}
            {!showAddDescription && task.pivot && task.pivot.description && (
                <p className="text-sm text-gray-500 dark:text-gray-400">{task.pivot.description}</p>
            )}
        </div>
    );
}
