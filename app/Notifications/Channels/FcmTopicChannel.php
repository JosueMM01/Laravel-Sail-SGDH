<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use Throwable;

class FcmTopicChannel
{
    private const DEFAULT_CREDENTIALS = 'storage/app/firebase_credentials.json';

    public function __construct(private ?Messaging $messaging = null)
    {
        $this->messaging ??= $this->buildMessagingClient();
    }

    public function send(object $notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toFcm')) {
            Log::warning('FCM topic notification skipped: missing toFcm payload.');
            return;
        }

        $payload = $notification->toFcm($notifiable);
        $topic = 'sgdh_user_' . $notifiable->id;

        if (! $this->messaging || empty($payload['title']) || empty($payload['body'])) {
            Log::warning("FCM topic notification skipped for {$topic}: incomplete payload or missing client.");
            return;
        }

        $message = CloudMessage::withTarget('topic', $topic)
            ->withNotification(FirebaseNotification::create($payload['title'], $payload['body']));

        try {
            $dataPayload = $this->formatDataPayload($payload['data'] ?? []);

            if ($dataPayload !== []) {
                $message = $message->withData($dataPayload);
            }

            $this->messaging->send($message);
        } catch (Throwable $exception) {
            Log::error(sprintf('FCM topic notification error for %s: %s', $topic, $exception->getMessage()));
        }
    }

    private function buildMessagingClient(): ?Messaging
    {
        $credentialsPath = base_path(env('FIREBASE_CREDENTIALS', self::DEFAULT_CREDENTIALS));

        if (! file_exists($credentialsPath)) {
            Log::error("FCM credentials file not found at {$credentialsPath}");
            return null;
        }

        return (new Factory())
            ->withServiceAccount($credentialsPath)
            ->createMessaging();
    }

    private function formatDataPayload(array $data): array
    {
        $formatted = [];

        foreach ($data as $key => $value) {
            if ($value === null) {
                continue;
            }

            $formatted[$key] = is_scalar($value)
                ? (string) $value
                : json_encode($value, JSON_THROW_ON_ERROR);
        }

        return $formatted;
    }
}
