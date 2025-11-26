<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SemaphoreSmsService
{
    protected $apiKey;
    protected $senderName;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.semaphore.api_key');
        $this->senderName = config('services.semaphore.sender_name');
        $this->baseUrl = config('services.semaphore.base_url');
    }

    /**
     * Send SMS to a single recipient
     *
     * @param string $number Phone number in format: 09XXXXXXXXX or +639XXXXXXXXX
     * @param string $message SMS message content
     * @return array
     */
    public function sendSms($number, $message)
    {
        return $this->sendBulkSms([$number], $message);
    }

    /**
     * Send SMS to multiple recipients
     *
     * @param array $numbers Array of phone numbers
     * @param string $message SMS message content
     * @return array
     */
    public function sendBulkSms(array $numbers, $message)
    {
        if (empty($this->apiKey)) {
            return [
                'success' => false,
                'message' => 'Semaphore API key is not configured. Please add SEMAPHORE_API_KEY to your .env file.',
            ];
        }

        if (empty($numbers)) {
            return [
                'success' => false,
                'message' => 'No phone numbers provided.',
            ];
        }

        // Format phone numbers (ensure +63 prefix)
        $formattedNumbers = array_map(function ($number) {
            return $this->formatPhoneNumber($number);
        }, $numbers);

        // Remove invalid numbers
        $validNumbers = array_filter($formattedNumbers);

        if (empty($validNumbers)) {
            return [
                'success' => false,
                'message' => 'No valid phone numbers provided.',
            ];
        }

        Log::info('Preparing to send SMS', [
            'recipients' => $validNumbers,
            'recipient_count' => count($validNumbers),
            'message_preview' => substr($message, 0, 50) . (strlen($message) > 50 ? '...' : ''),
        ]);

        try {
            // Prepare API parameters
            $params = [
                'apikey' => $this->apiKey,
                'number' => implode(',', $validNumbers),
                'message' => $message,
            ];

            // Only include sendername if it's not empty
            if (!empty($this->senderName)) {
                $params['sendername'] = $this->senderName;
            }

            $response = Http::asForm()->post($this->baseUrl . '/api/v4/messages', $params);

            $data = $response->json();

            if ($response->successful() && isset($data[0]['message_id'])) {
                Log::info('Semaphore SMS sent successfully', [
                    'recipients' => count($validNumbers),
                    'message_id' => $data[0]['message_id'],
                ]);

                return [
                    'success' => true,
                    'message' => 'SMS sent successfully to ' . count($validNumbers) . ' recipient(s).',
                    'data' => $data,
                ];
            } else {
                $errorMessage = $data['message'] ?? 'Unknown error occurred';
                
                Log::error('Semaphore SMS failed', [
                    'error' => $errorMessage,
                    'response' => $data,
                ]);

                return [
                    'success' => false,
                    'message' => 'Failed to send SMS: ' . $errorMessage,
                    'data' => $data,
                ];
            }
        } catch (\Exception $e) {
            Log::error('Semaphore SMS exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Error sending SMS: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Format phone number to +639XXXXXXXXX format
     *
     * @param string $number
     * @return string|null
     */
    protected function formatPhoneNumber($number)
    {
        // Remove spaces, dashes, and parentheses
        $number = preg_replace('/[\s\-\(\)]/', '', $number);

        // Remove leading zeros and +63
        $number = preg_replace('/^(\+63|63|0)/', '', $number);

        // Validate it's a 10-digit number starting with 9
        if (preg_match('/^9\d{9}$/', $number)) {
            return '+63' . $number;
        }

        return null;
    }

    /**
     * Check account balance
     *
     * @return array
     */
    public function checkBalance()
    {
        if (empty($this->apiKey)) {
            return [
                'success' => false,
                'message' => 'Semaphore API key is not configured.',
            ];
        }

        try {
            $response = Http::get($this->baseUrl . '/api/v4/account', [
                'apikey' => $this->apiKey,
            ]);

            $data = $response->json();

            if ($response->successful()) {
                return [
                    'success' => true,
                    'balance' => $data['credit_balance'] ?? 0,
                    'data' => $data,
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to fetch balance: ' . ($data['message'] ?? 'Unknown error'),
                    'data' => $data,
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error checking balance: ' . $e->getMessage(),
            ];
        }
    }
}
