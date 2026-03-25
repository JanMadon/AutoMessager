<?php

namespace App\Console\Commands;

use App\Models\EmailHistory;
use App\Models\GmailAccount;
use App\Models\MailBox;
use Google_Client;
use Illuminate\Console\Command;

class FetchGmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gmail:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get email from gmail account';

    /**
     * Execute the console command.
     */
    public function handle()
    {
      //$this->getAuthorizationUrl();
        $this->getEmails();
    }

    private function getAuthorizationUrl(): void
    {
        $client = new Google_Client();
        $client->setAuthConfig(storage_path('google/credentials.json'));

        $client->setScopes([
            \Google_Service_Gmail::GMAIL_READONLY,
        ]);

        $client->setAccessType('offline');
        $client->setPrompt('consent');

        $authUrl = $client->createAuthUrl();

        $this->info('Otwórz ten link w przeglądarce:');
        $this->line($authUrl);
    }

    private function getEmails()
    {
        $accounts = GmailAccount::all();

        foreach ($accounts as $account) {

            $this->info("Pobieram maile dla: {$account->email}");

            $client = new Google_Client();
            $client->setAuthConfig(storage_path('google/credentials.json'));
            $client->setScopes([\Google_Service_Gmail::GMAIL_READONLY]);
            $client->refreshToken(decrypt($account->refresh_token));

            $service = new \Google_Service_Gmail($client);

            $profile = $service->users->getProfile('me');
            $currentHistoryId = $profile->getHistoryId();

            EmailHistory::firstOrCreate([
                'gmail_accounts_id' => $account->id,
                'history_id' => $currentHistoryId,
            ]);

            $messages = $service->users_messages->listUsersMessages('me', [
                'q' => 'is:unread',
                'maxResults' => 1500,
            ]);

            foreach ($messages->getMessages() ?? [] as $msg) {
                $messageId = $msg->getId();
                $isEmailExist = MailBox::where('gmail_accounts_id', $account->id)
                    ->where('message_id', $messageId)
                    ->exists();

                if ($isEmailExist) {
                    $this->info('Message already exists: ' . $messageId);
                    continue;
                }

                $message = $service->users_messages->get('me' , $messageId);

                $body = $this->extractBody($message->getPayload());

                $headers = collect($message->getPayload()->getHeaders())
                    ->keyBy(fn($h) => $h->getName());

                $from = $headers['From']->getValue() ?? '';
                $subject = $headers['Subject']->getValue() ?? '';
                $dateHeader = $headers['Date']->getValue() ?? null;
                if ($dateHeader) {
                    // usuwa wszystko w nawiasach: (GMT+01:00)
                    $cleanDate = preg_replace('/\s*\(.*?\)$/', '', $dateHeader);

                    $sentAt = \Carbon\Carbon::parse($cleanDate);
                    $date = $sentAt->toDateTimeString();
                }

                $this->line(" - {$subject} ({$from})");

                MailBox::create(
                    [
                        'gmail_accounts_id' => $account->id,
                        'subject' => $subject,
                        'body' => $body['html'] ?? ($body['text'] ?? 'Brak danych'),
                        'sent_at' => $date ?? null,
                        'from' => $from,
                        'message_id' => $messageId,
                        'last_history_id' => $message->getHistoryId(),
                    ]
                );
            }
        }
    }

    private function extractBody($payload): array
    {
        $result = [
            'text' => null,
            'html' => null,
        ];

        // case 1: mail prosty (bez parts)
        if ($payload->getBody()->getData()) {
            $result['text'] = $this->decode($payload->getBody()->getData());
            return $result;
        }

        // case 2: MIME tree
        foreach ($payload->getParts() ?? [] as $part) {
            $mime = $part->getMimeType();

            if (in_array($mime, ['text/plain', 'text/html'])) {
                $result[$mime === 'text/plain' ? 'text' : 'html']
                    = $this->decode($part->getBody()->getData());
            }

            // multipart/alternative → rekurencja
            if ($part->getParts()) {
                $child = $this->extractBody($part);
                $result['text'] ??= $child['text'];
                $result['html'] ??= $child['html'];
            }
        }

        return $result;
    }

    private function decode(?string $data): string
    {
        if(is_null($data)) {
            return '';
        }
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
