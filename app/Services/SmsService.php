<?php
namespace App\Services;

use Twilio\Rest\Client;

class SmsService
{
    public function sendVerificationCode(string $phone, string $code): bool
    {
        try {
            $client = new Client(config('services.twilio.sid'), config('services.twilio.token'));
            $client->messages->create($phone, [
                'from' => config('services.twilio.from'),
                'body' => "Your eloveyou verification code: $code",
            ]);
            return true;
        } catch (\Exception $e) {
            \Log::error('SMS send failed: ' . $e->getMessage());
            return false;
        }
    }

    public function generateCode(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }
}
