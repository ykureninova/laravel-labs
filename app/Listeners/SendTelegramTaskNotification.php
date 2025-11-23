<?php

namespace App\Listeners;

use App\Events\TaskCreated;
use App\Jobs\SendTelegramMessageJob;

class SendTelegramTaskNotification
{
    /**
     * Handle the event.
     */
    public function handle(TaskCreated $event): void
    {
        $task = $event->task;
        $project = $task->project;

        $text = sprintf(
            "<b>Нова задача створена</b>\nПроєкт: %s\nНазва: %s\nСтатус: %s\nПріоритет: %s",
            $project?->name ?? ('#' . $task->project_id),
            $task->title,
            $task->status,
            $task->priority
        );


        SendTelegramMessageJob::dispatch($text);
    }
}
