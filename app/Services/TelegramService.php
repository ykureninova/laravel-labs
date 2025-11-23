<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    public function sendMessage(string $text, ?string $chatId = null): bool
    {
        $token = config('services.telegram.token');
        $defaultChatId = config('services.telegram.chat_id');

        $chatId = $chatId ?? $defaultChatId;

        if (!$token || !$chatId) {
            Log::error('TelegramService: token or chat_id not configured', [
                'token_present' => (bool) $token,
                'chat_id_present' => (bool) $chatId,
            ]);

            return false;
        }

        $url = "https://api.telegram.org/bot{$token}/sendMessage";

        try {
            $response = Http::post($url, [
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'HTML',
            ]);

            if (!$response->successful()) {
                Log::error('TelegramService: failed to send message', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return false;
            }

            Log::info('TelegramService: message sent', [
                'chat_id' => $chatId,
                'text' => $text,
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::error('TelegramService: exception while sending message', [
                'message' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
