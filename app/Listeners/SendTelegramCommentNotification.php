<?php

namespace App\Listeners;

use App\Events\CommentCreated;
use App\Jobs\SendTelegramMessageJob;

class SendTelegramCommentNotification
{
    /**
     * Handle the event.
     */
    public function handle(CommentCreated $event): void
    {
        $comment = $event->comment;
        $task = $comment->task;
        $project = $task->project;

        $text = sprintf(
            "<b>Новий коментар</b>\nПроєкт: %s\nЗадача: %s\nАвтор: #%d\nТекст: %s",
            $project?->name ?? ('#' . $task->project_id),
            $task->title,
            $comment->author_id,
            $comment->body
        );

        SendTelegramMessageJob::dispatch($text);
    }
}
