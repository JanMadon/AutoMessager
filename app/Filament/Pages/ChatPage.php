<?php

namespace App\Filament\Pages;

use App\Services\OpenAIService;
use Filament\Pages\Page;

class ChatPage extends Page
{
    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationLabel = 'Chat';

    protected string $view = 'filament.pages.chat-page';
    protected static bool $shouldRegisterNavigation = true;

    // Właściwości Livewire - automatycznie dostępne w widoku
    public string $messageInput = '';
    public array $messages = [];

    // Inicjalizacja - uruchamia się przy montowaniu komponentu
    public function mount(): void
    {
        // Przykładowe wiadomości na start
        $this->messages = [
            ['role' => 'assistant', 'content' => 'Cześć! W czym mogę pomóc?', 'time' => now()->format('H:i')],
        ];
    }

    // Metoda wywoływana z widoku - wysyła wiadomość
    public function sendMessage(): void
    {
        if (empty(trim($this->messageInput))) {
            return;
        }

        $this->messages[] = [
            'role' => 'user',
            'content' => $this->messageInput,
            'time' => now()->format('H:i'),
        ];

        // Symulacja odpowiedzi asystenta (tutaj możesz podpiąć API)
        $openAiService = new OpenAIService();
        $response = $openAiService->getCompletion($this->messages);

        $this->messages[] = [
            'role' => 'assistant',
            'content' => $response,
            'time' => now()->format('H:i'),
        ];

        $this->messageInput = '';

        $this->dispatch('message-sent');
    }
}
