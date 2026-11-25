<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CloudflareTurnstile
{
    private const VERIFY_URL = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    public function verify(string $token, string $expectedAction, ?string $ipAddress): bool
    {
        $secretKey = config('services.turnstile.secret_key');

        if (! is_string($secretKey) || $secretKey === '') {
            Log::error('Cloudflare Turnstile secret key is not configured.');

            return false;
        }

        $payload = [
            'secret' => $secretKey,
            'response' => $token,
        ];

        if ($ipAddress !== null) {
            $payload['remoteip'] = $ipAddress;
        }

        try {
            $response = Http::asForm()
                ->connectTimeout(3)
                ->timeout(5)
                ->post(self::VERIFY_URL, $payload);
        } catch (ConnectionException $exception) {
            Log::warning('Cloudflare Turnstile verification request failed.', [
                'exception' => $exception,
            ]);

            return false;
        }

        if (! $response->successful()) {
            Log::warning('Cloudflare Turnstile returned an unsuccessful HTTP response.', [
                'status' => $response->status(),
            ]);

            return false;
        }

        return $response->json('success') === true
            && $response->json('action') === $expectedAction;
    }
}
