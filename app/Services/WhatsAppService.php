<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $apiUrl;
    protected $token;

    public function __construct()
    {
        $this->apiUrl = 'https://gate.whapi.cloud/';
        $this->token = '13fj0xPIj8LGJJOzNuPmbS5ik7mys0Z7';
    }

    /**
     * Send a text message via WhatsApp
     *
     * @param string $phoneNumber
     * @param string $message
     * @return array|null
     */
    public function sendTextMessage(string $phoneNumber, string $message)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl . 'messages/text', [
                'to' => $phoneNumber,
                'body' => $message
            ]);

            if ($response->successful()) {
                return $response->json();
            } else {
                Log::error('WhatsApp API error: ' . $response->body());
                return null;
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp service error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate a random verification code
     *
     * @return string
     */
    public function generateVerificationCode()
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Send a verification code via WhatsApp
     *
     * @param string $phoneNumber
     * @param string $code
     * @return array|null
     */
    public function sendVerificationCode(string $phoneNumber, string $code)
    {
        $message = "Your SEO Analyzer verification code is: $code. This code will expire in 10 minutes.";
        return $this->sendTextMessage($phoneNumber, $message);
    }

    /**
     * Send password reset code via WhatsApp
     *
     * @param string $phoneNumber
     * @param string $code
     * @return array|null
     */
    public function sendPasswordResetCode(string $phoneNumber, string $code)
    {
        $message = "Your SEO Analyzer password reset code is: $code. This code will expire in 10 minutes.";
        return $this->sendTextMessage($phoneNumber, $message);
    }
} 