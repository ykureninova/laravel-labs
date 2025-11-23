<?php

namespace App\Providers;

use App\Events\TaskCreated;
use App\Listeners\SendTelegramTaskNotification;
use App\Events\CommentCreated;
use App\Listeners\SendTelegramCommentNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        TaskCreated::class => [
            SendTelegramTaskNotification::class,
        ],

        CommentCreated::class => [
            SendTelegramCommentNotification::class,
        ],
    ];
}
