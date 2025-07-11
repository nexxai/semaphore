import ActiveTask from '@/components/active-task';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { type Task } from '@/types';
import { Head, router } from '@inertiajs/react';
import { PlusIcon } from 'lucide-react';
import { useState } from 'react';

interface Day {
    id: number;
    name: string;
    tasks?: Task[];
}

export default function Welcome({ tasks, current_day }: { tasks?: Task[]; current_day: Day }) {
    const [showNewTask, setShowNewTask] = useState<boolean>(false);

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
                            <h1 className="text-3xl leading-tight font-bold tracking-tight text-[#1b1b18] lg:text-4xl dark:text-[#EDEDEC]">
                                Tasklist
                            </h1>

                            <h2 className="text-xl leading-tight font-semibold tracking-tight text-[#1b1b18] lg:text-2xl dark:text-[#EDEDEC]">
                                To-do
                            </h2>

                            <div className="w-full text-base text-[#1b1b18] sm:w-1/2 lg:w-2/3 dark:text-[#EDEDEC]">
                                {current_day.tasks && current_day.tasks.length > 0 && (
                                    <ul className="space-y-1">
                                        {current_day.tasks.map((task) => (
                                            <li key={task.id}>
                                                <ActiveTask task={task} />
                                            </li>
                                        ))}
                                    </ul>
                                )}
                            </div>

                            <h2 className="text-xl leading-tight font-semibold tracking-tight text-[#1b1b18] lg:text-2xl dark:text-[#EDEDEC]">
                                Available tasks
                            </h2>
                            <div className="w-full text-base text-[#1b1b18] sm:w-1/2 lg:w-2/3 dark:text-[#EDEDEC]">
                                {tasks &&
                                    tasks.length > 0 &&
                                    tasks.map((task) => (
                                        <Badge
                                            variant="secondary"
                                            className="mr-2 transition hover:scale-105 hover:cursor-pointer"
                                            key={task.id}
                                            onClick={() => addTask(task.id)}
                                        >
                                            <PlusIcon />
                                            {task.name}
                                        </Badge>
                                    ))}

                                {showNewTask ? (
                                    <Input
                                        type="text"
                                        placeholder="New task name..."
                                        className="mt-2 mb-2 w-full max-w-xs"
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
                                        className="mr-2 transition hover:scale-105 hover:cursor-pointer"
                                        onClick={() => toggleShowNewTaskInput()}
                                    >
                                        <PlusIcon />
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
