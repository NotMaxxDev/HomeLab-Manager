<?php

namespace App\Services;

use App\Models\NotificationChannel;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Versendet Benachrichtigungen über alle aktivierten, in der UI
 * konfigurierten Kanäle (Mail, Telegram, Webhook, ntfy).
 */
class NotificationService
{
    public function notify(string $title, string $message, string $type = 'alert'): void
    {
        $channels = NotificationChannel::where('enabled', true)->get();

        foreach ($channels as $channel) {
            try {
                $this->dispatch($channel, $title, $message);
            } catch (\Throwable $e) {
                Log::error('Notification channel failed', [
                    'channel' => $channel->type,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    private function dispatch(NotificationChannel $channel, string $title, string $message): void
    {
        $config = $channel->config ?? [];

        match ($channel->type) {
            'mail' => $this->sendMail($config, $title, $message),
            'telegram' => $this->sendTelegram($config, $title, $message),
            'webhook' => $this->sendWebhook($config, $title, $message),
            'ntfy' => $this->sendNtfy($config, $title, $message),
            default => throw new \RuntimeException("Unbekannter Kanal {$channel->type}"),
        };
    }

    private function sendMail(array $config, string $title, string $message): void
    {
        Mail::raw($message, function ($mail) use ($config, $title) {
            $mail->to($config['to'] ?? null)
                ->subject("[HomelabManager] {$title}");
        });
    }

    private function sendTelegram(array $config, string $title, string $message): void
    {
        $token = $config['bot_token'] ?? null;
        $chatId = $config['chat_id'] ?? null;

        if (! $token || ! $chatId) {
            return;
        }

        $client = new Client(['timeout' => 10]);
        $client->post("https://api.telegram.org/bot{$token}/sendMessage", [
            'form_params' => [
                'chat_id' => $chatId,
                'text' => "{$title}\n\n{$message}",
                'parse_mode' => 'HTML',
            ],
        ]);
    }

    private function sendWebhook(array $config, string $title, string $message): void
    {
        $url = $config['url'] ?? null;
        if (! $url) {
            return;
        }

        $client = new Client(['timeout' => 10]);
        $client->post($url, [
            'json' => ['title' => $title, 'message' => $message],
        ]);
    }

    private function sendNtfy(array $config, string $title, string $message): void
    {
        $topic = $config['topic'] ?? null;
        if (! $topic) {
            return;
        }

        $server = rtrim($config['server'] ?? 'https://ntfy.sh', '/');

        $client = new Client(['timeout' => 10]);
        $client->post("{$server}/{$topic}", [
            'body' => $message,
            'headers' => ['Title' => $title],
        ]);
    }
}
