<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class OpenAIService
{
    private string $chatUrl = 'https://api.openai.com/v1/responses';
    private Client $client;
    public function __construct()
    {
        $this->client = new Client();
    }

    public function getCompletion(array $prompt): string
    {
        try {
            $response = $this->client->post($this->chatUrl, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . config('openai.api_key')
                ],
                'json' => [
                    'model' => 'gpt-4.1-mini',
                    'input' => $this->preparePrompt($prompt),
                ]
            ]);

            $res = $response->getBody()->getContents();

        } catch (\Throwable $th) {
            Log::error('Error while calling OpenAI API: ',[$th]);
            return 'Error: ' . $th->getMessage();
        }

        Log::info('Chat response: ',[$res]);

        return json_decode($res)->output[0]->content[0]->text ?? 'Error: Bad response from OpenAI';
    }

    private function preparePrompt(array $messages): array
    {
       $promptContent = [];
       foreach($messages as $message){
           $promptContent[] = [
               'role' => $message['role'],
               'content' => $message['content']
           ];
       }

       return $promptContent;
    }
}
