import ActiveTask from '@/components/active-task';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { type SharedData, type Subtask, type Task } from '@/types';
import { Head, router, usePage } from '@inertiajs/react';
import { useEcho } from '@laravel/echo-react';
import { Plus } from 'lucide-react';
import { useState } from 'react';

interface DayTask {
    id: number;
    task_id: number;
    completed: boolean;
    task: Task;
    subtasks: Subtask[];
}

interface Day {
    id: number;
    date: string;
    tasks?: Task[];
    dayTasks?: DayTask[];
}

interface TaskUpdatedEvent {
    updated_by: number;
    day: Day;
}

export default function Welcome({ tasks, current_day, dayTasks }: { tasks?: Task[]; current_day: Day; dayTasks?: DayTask[] }) {
    const [showNewTask, setShowNewTask] = useState<boolean>(false);
    const { auth } = usePage<SharedData>().props;

    useEcho('day', 'TaskUpdated', (e: TaskUpdatedEvent) => {
        router.reload();
    });

    const toggleShowNewTaskInput = () => {
        setShowNewTask(!showNewTask);
    };

    const addTask = (taskId: number) => {
        router.post('/tasks/add', { taskId });
    };

    return (
        <>
            <Head>
                <link rel="preconnect" href="https://fonts.bunny.net" />
                <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
            </Head>
            <div className="flex min-h-screen flex-col items-center bg-[#FDFDFC] p-6 text-[#1b1b18] lg:justify-center lg:p-8 dark:bg-[#0a0a0a]">
                <div className="flex w-full items-center justify-center opacity-100 transition-opacity duration-750 lg:grow starting:opacity-0">
                    <main className="flex w-full flex-col-reverse lg:max-w-4xl lg:flex-row">
                        <div className="flex w-full flex-col items-center justify-center gap-6 px-6 py-8 lg:w-4/5 lg:items-start lg:px-12 lg:py-16">
                            <h1 className="text-5xl leading-tight font-bold tracking-tight text-[#1b1b18] lg:text-4xl dark:text-[#EDEDEC]">
                                Semaphore
                            </h1>

                            <h2 className="text-3xl leading-tight font-semibold tracking-tight text-[#1b1b18] dark:text-[#EDEDEC]">To-do</h2>

                            <div className="w-full text-lg text-[#1b1b18] sm:w-1/2 lg:w-2/3 dark:text-[#EDEDEC]">
                                {dayTasks && dayTasks.length > 0 ? (
                                    <ul className="space-y-1">
                                        {dayTasks.map((dayTask) => (
                                            <li key={dayTask.id}>
                                                <ActiveTask
                                                    task={{
                                                        ...dayTask.task,
                                                        pivot: { id: dayTask.id, completed: dayTask.completed, subtasks: dayTask.subtasks },
                                                    }}
                                                />
                                            </li>
                                        ))}
                                    </ul>
                                ) : (
                                    <p className="text-gray-500">No uncompleted tasks!</p>
                                )}
                            </div>

                            <h2 className="text-3xl leading-tight font-semibold tracking-tight text-[#1b1b18] dark:text-[#EDEDEC]">
                                Available tasks
                            </h2>
                            <div className="w-full space-y-1 text-lg text-[#1b1b18] sm:w-1/2 lg:w-2/3 dark:text-[#EDEDEC]">
                                {tasks &&
                                    tasks.length > 0 &&
                                    tasks.map((task) => (
                                        <Badge
                                            variant="secondary"
                                            size="lg"
                                            className="mr-2 text-lg transition hover:scale-105 hover:cursor-pointer"
                                            key={task.id}
                                            onClick={() => addTask(task.id)}
                                        >
                                            <Plus height="32" width="32" />
                                            {task.name}
                                        </Badge>
                                    ))}

                                {showNewTask ? (
                                    <Input
                                        type="text"
                                        placeholder="New task name..."
                                        className="mt-2 mb-2 w-full max-w-xs text-lg"
                                        onKeyDown={(e) => {
                                            if (e.key === 'Enter') {
                                                const newTaskName = (e.target as HTMLInputElement).value;
                                                if (newTaskName.trim()) {
                                                    router.post(route('tasks.create'), { name: newTaskName });
                                                    toggleShowNewTaskInput();
                                                }
                                            }
                                            if (e.key === 'Escape') {
                                                toggleShowNewTaskInput();
                                            }
                                        }}
                                    />
                                ) : (
                                    <Badge
                                        variant="secondary"
                                        size="lg"
                                        className="mr-2 text-lg transition hover:scale-105 hover:cursor-pointer"
                                        onClick={() => toggleShowNewTaskInput()}
                                    >
                                        <Plus height="32" width="32" />
                                        Add New Task
                                    </Badge>
                                )}
                            </div>
                        </div>
                    </main>
                </div>
                <div className="hidden h-14.5 lg:block"></div>
            </div>
        </>
    );
}
