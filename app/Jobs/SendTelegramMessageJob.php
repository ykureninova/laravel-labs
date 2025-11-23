<?php

namespace App\Jobs;

use App\Services\TelegramService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendTelegramMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $text;
    public ?string $chatId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $text, ?string $chatId = null)
    {
        $this->text = $text;
        $this->chatId = $chatId;
    }

    /**
     * Execute the job.
     */
    public function handle(TelegramService $telegramService): void
    {

        $result = $telegramService->sendMessage($this->text, $this->chatId);

        Log::info('SendTelegramMessageJob executed', [
            'text' => $this->text,
            'chat_id' => $this->chatId,
            'success' => $result,
        ]);
    }
}
