<?php

namespace App\Listeners;

use App\Events\TaskCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendTaskCreatedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(TaskCreated $event): void
    {
        $task = $event->task;

        Log::info('Task created notification queued', [
            'task_id' => $task->id,
            'title' => $task->title,
            'project_id' => $task->project_id,
            'assignee_id' => $task->assignee_id,
        ]);
    }
}
