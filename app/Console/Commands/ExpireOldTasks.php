<?php

namespace App\Console\Commands;

use App\Jobs\SendTelegramMessageJob;
use App\Models\Task;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ExpireOldTasks extends Command
{
    protected $signature = 'app:expire-old-tasks';

    protected $description = 'Позначає задачі in_progress старші ніж 7 днів як expired та надсилає Telegram-сповіщення';

    public function handle(): int
    {
        $threshold = Carbon::now()->subDays(7);

        $tasks = Task::where('status', 'in_progress')
            ->where('updated_at', '<=', $threshold)
            ->get();

        if ($tasks->isEmpty()) {
            $this->info('Немає прострочених задач.');
            return self::SUCCESS;
        }

        foreach ($tasks as $task) {
            $task->update(['status' => 'expired']);

            $text = sprintf(
                "<b>Задача прострочена</b>\nПроєкт: %s\nНазва: %s\nСтатус: expired",
                $task->project?->name ?? ('#'.$task->project_id),
                $task->title
            );

            SendTelegramMessageJob::dispatch($text);
        }

        $this->info('Позначено задач як expired: '.$tasks->count());

        return self::SUCCESS;
    }
}
