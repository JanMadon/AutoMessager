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
                    //'model' => 'gpt-4.1-mini',
                    'model' => 'gpt-5.4',
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

    public function extractLearningPhrases(string $transcript, string $englishLevel): array
    {
        $messages = [
            [
                'role' => 'system',
                'content' => 'You are an English learning assistant focused on comprehension gaps. Return only valid JSON with this exact shape: {"phrases":[{"phrase":"...","translation":"...","context_sentence":"...","explanation":"..."}]}. Do not add markdown, comments, or extra keys.',
            ],
            [
                'role' => 'user',
                'content' => "English level (CEFR): {$englishLevel}\n\nTranscript:\n{$transcript}\n\nTask:\n1) Return only phrases that are likely ABOVE this learner level (or at the upper edge) and IMPORTANT to understand this specific episode.\n2) The number of phrases should be dynamic, based on transcript difficulty (typically 5-25). Do not pad the list.\n3) Do NOT include very basic phrases that a learner at this level likely already knows.\n4) Prefer idioms, phrasal verbs, colloquial expressions, culture-specific wording, and compressed spoken forms that may block comprehension.\n5) Use exact wording from transcript for phrase and context sentence.\n6) Provide Polish translations and a short explanation why/when the phrase is used.",
            ],
        ];

        $responseText = $this->getCompletion($messages);

        return $this->parseLearningPhrases($responseText);
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

    private function parseLearningPhrases(string $responseText): array
    {
        $decoded = json_decode($responseText, true);

        if (! is_array($decoded)) {
            preg_match('/```json\s*(.*?)\s*```/is', $responseText, $matches);

            if (! empty($matches[1])) {
                $decoded = json_decode($matches[1], true);
            }
        }

        if (! is_array($decoded) || ! isset($decoded['phrases']) || ! is_array($decoded['phrases'])) {
            return [];
        }

        $phrases = [];

        foreach ($decoded['phrases'] as $phraseData) {
            if (! is_array($phraseData)) {
                continue;
            }

            $phrase = trim((string) ($phraseData['phrase'] ?? ''));
            $translation = trim((string) ($phraseData['translation'] ?? ''));

            if ($phrase === '' || $translation === '') {
                continue;
            }

            $phrases[] = [
                'phrase' => $phrase,
                'translation' => $translation,
                'context_sentence' => trim((string) ($phraseData['context_sentence'] ?? '')),
                'explanation' => trim((string) ($phraseData['explanation'] ?? '')),
            ];
        }

        return $phrases;
    }
}
