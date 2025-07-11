<x-mail::message>
# New task added

The first task ({{ $task->name }}) has been added to your day.

<x-mail::button :url="config('app.url')">
Jump to tasks
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
