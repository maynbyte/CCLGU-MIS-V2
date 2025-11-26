# Semaphore SMS Integration Setup

This application uses Semaphore SMS API for sending bulk text messages to directory contacts.

## Features

- Send SMS to selected directories from the Financial Assistances index page
- Character counter (160 character limit per SMS)
- Quick message templates
- Automatic phone number validation and formatting
- Bulk SMS support
- Error handling and feedback

## Setup Instructions

### 1. Get Semaphore API Key

1. Go to [https://semaphore.co](https://semaphore.co)
2. Sign up or log in to your account
3. Navigate to API section
4. Copy your API key

### 2. Configure Environment Variables

Add the following to your `.env` file:

```env
SEMAPHORE_API_KEY=your_api_key_here
SEMAPHORE_SENDER_NAME=CCLGU
SEMAPHORE_BASE_URL=https://api.semaphore.co
```

**Configuration Options:**

- `SEMAPHORE_API_KEY` - Your Semaphore API key (required)
- `SEMAPHORE_SENDER_NAME` - Sender name that will appear in SMS (max 11 characters)
- `SEMAPHORE_BASE_URL` - API endpoint (default: https://api.semaphore.co)

### 3. Test the Integration

Run this in Tinker to test:

```bash
php artisan tinker
```

```php
$sms = new \App\Services\SemaphoreSmsService();

// Test sending SMS
$result = $sms->sendSms('09171234567', 'Test message from CCLGU MIS');
print_r($result);

// Check balance
$balance = $sms->checkBalance();
print_r($balance);
```

## Usage

### From the Admin Panel

1. Go to Financial Assistances → Directory list
2. Select one or more records with valid phone numbers
3. Click the "Text Selected" button
4. Compose your message (max 160 characters)
5. Use quick templates or write a custom message
6. Click "Send SMS"

### Programmatically

```php
use App\Services\SemaphoreSmsService;

$smsService = new SemaphoreSmsService();

// Send to single number
$result = $smsService->sendSms('09171234567', 'Your message here');

// Send to multiple numbers
$numbers = ['09171234567', '09181234567'];
$result = $smsService->sendBulkSms($numbers, 'Your message here');

// Check account balance
$balance = $smsService->checkBalance();
```

## Phone Number Format

The service automatically formats phone numbers to the required format:

- Input: `09171234567` or `0917-123-4567` or `+639171234567`
- Output: `+639171234567`

Invalid numbers are automatically filtered out.

## SMS Pricing

Check current Semaphore pricing at [https://semaphore.co/pricing](https://semaphore.co/pricing)

Typical rates:
- Local SMS: ₱0.60 - ₱1.00 per message
- Account can be loaded via online banking or payment centers

## Error Handling

The system provides detailed error messages:

- **API key not configured**: Add `SEMAPHORE_API_KEY` to .env
- **No valid phone numbers**: Selected records have no contact numbers
- **Insufficient balance**: Top up your Semaphore account
- **Invalid phone numbers**: Numbers don't match Philippine format (09XXXXXXXXX)

## Logs

All SMS activities are logged in `storage/logs/laravel.log`:

```
[timestamp] INFO: Semaphore SMS sent successfully
[timestamp] ERROR: Semaphore SMS failed
```

## Quick Templates

The following templates are pre-configured:

1. **Claim Ready**: "Your financial assistance is ready for claim at CCLGU office."
2. **Payout Reminder**: "Reminder: Please bring valid ID and requirements for your scheduled payout."
3. **Application Update**: "Your application has been processed. You will be notified once approved."

You can customize these in the view file: `resources/views/admin/financialAssistances/index.blade.php`

## Security Notes

- Never commit your API key to version control
- Keep your `.env` file secure
- Regularly monitor your SMS usage and balance
- Set up IP whitelisting in Semaphore dashboard if available

## Troubleshooting

### "API key is not configured"
Add `SEMAPHORE_API_KEY` to your `.env` file and run:
```bash
php artisan config:clear
php artisan cache:clear
```

### "No valid phone numbers found"
Ensure selected directory records have valid phone numbers in the `contact_no` field.

### "Failed to send SMS: Insufficient balance"
Top up your Semaphore account at [https://semaphore.co](https://semaphore.co)

### Testing without sending real SMS
Comment out the actual HTTP call in `SemaphoreSmsService::sendBulkSms()` for testing.

## Support

- Semaphore Documentation: [https://semaphore.co/docs](https://semaphore.co/docs)
- Semaphore Support: support@semaphore.co
- API Status: [https://status.semaphore.co](https://status.semaphore.co)
