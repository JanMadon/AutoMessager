<?php

namespace App\Filament\Pages;

use App\Models\LearningPhrase;
use App\Services\OpenAIService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TranscriptPhrasesPage extends Page
{
    use WithFileUploads;

    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-language';

    protected static ?string $navigationLabel = 'Frazy z transkrypcji';

    protected static ?string $title = 'Analiza transkrypcji serialu';

    protected static string|\UnitEnum|null $navigationGroup = 'English Learning';

    protected static ?int $navigationSort = 20;

    protected string $view = 'filament.pages.transcript-phrases-page';

    public string $episodeTitle = '';

    public string $englishLevel = 'B1';

    public string $transcript = '';

    public $transcriptFile = null;

    public array $phrases = [];

    public function mount(): void
    {
        $this->loadRecentPhrases();
    }

    public function analyzeTranscript(OpenAIService $openAIService): void
    {
        $userId = Auth::id();

        if ($userId === null) {
            Notification::make()
                ->title('Brak autoryzacji')
                ->body('Zaloguj się ponownie i spróbuj jeszcze raz.')
                ->danger()
                ->send();

            return;
        }

        $validated = Validator::make(
            [
                'episodeTitle' => $this->episodeTitle,
                'englishLevel' => $this->englishLevel,
                'transcriptFile' => $this->transcriptFile,
            ],
            [
                'episodeTitle' => ['nullable', 'string', 'max:255'],
                'englishLevel' => ['required', 'string', 'in:A1,A2,B1,B2,C1,C2'],
                'transcriptFile' => ['nullable', 'file', 'mimes:txt,srt,vtt', 'max:5120'],
            ]
        )->validate();

        $transcriptFromText = trim($this->transcript);
        $transcriptFromFile = $this->extractTranscriptFromUploadedFile();
        $mergedTranscript = $transcriptFromText !== '' ? $transcriptFromText : $transcriptFromFile;

        if ($mergedTranscript === '') {
            Notification::make()
                ->title('Brak transkrypcji')
                ->body('Wklej tekst transkrypcji lub dodaj plik TXT/SRT/VTT.')
                ->warning()
                ->send();

            return;
        }

        if (mb_strlen($mergedTranscript) < 50) {
            Notification::make()
                ->title('Za mało treści')
                ->body('Transkrypcja musi mieć minimum 50 znaków.')
                ->warning()
                ->send();

            return;
        }

        $phrases = $openAIService->extractLearningPhrases(
            $mergedTranscript,
            $validated['englishLevel']
        );

        if (empty($phrases)) {
            Notification::make()
                ->title('Brak wyników')
                ->body('OpenAI nie zwróciło poprawnych fraz. Spróbuj ponownie z dłuższym transkryptem.')
                ->warning()
                ->send();

            return;
        }

        foreach ($phrases as $phraseData) {
            LearningPhrase::create([
                'user_id' => $userId,
                'episode_title' => $validated['episodeTitle'] ?: null,
                'english_level' => $validated['englishLevel'],
                'phrase' => $phraseData['phrase'],
                'translation' => $phraseData['translation'],
                'context_sentence' => $phraseData['context_sentence'] ?: null,
                'explanation' => $phraseData['explanation'] ?: null,
            ]);
        }

        $this->loadRecentPhrases();

        Notification::make()
            ->title('Gotowe')
            ->body('Frazy zostały zapisane do bazy.')
            ->success()
            ->send();

        $this->transcriptFile = null;
    }

    private function extractTranscriptFromUploadedFile(): string
    {
        if ($this->transcriptFile === null) {
            return '';
        }

        $content = file_get_contents($this->transcriptFile->getRealPath());

        if ($content === false) {
            return '';
        }

        return trim($this->normalizeSubtitleText($content));
    }

    private function normalizeSubtitleText(string $content): string
    {
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content) ?? $content;
        $content = str_replace(["\r\n", "\r"], "\n", $content);

        $blocks = preg_split('/\n{2,}/', $content) ?: [];
        $cleanedBlocks = [];

        foreach ($blocks as $block) {
            $rawLines = explode("\n", trim($block));
            $lines = array_values(array_filter(array_map('trim', $rawLines), static fn (string $line): bool => $line !== ''));

            if ($lines === []) {
                continue;
            }

            if (preg_match('/^(WEBVTT|NOTE|Kind:|Language:)/i', $lines[0])) {
                continue;
            }

            if (preg_match('/^\d+$/', $lines[0])) {
                array_shift($lines);
            }

            if ($lines !== [] && preg_match('/^\d{2}:\d{2}:\d{2}[,.]\d{3}\s*-->\s*\d{2}:\d{2}:\d{2}[,.]\d{3}(?:\s+.*)?$/', $lines[0])) {
                array_shift($lines);
            }

            if ($lines === []) {
                continue;
            }

            $cleanedLines = [];

            foreach ($lines as $line) {
                $line = preg_replace('/<[^>]+>/', '', $line) ?? $line;
                $line = preg_replace('/\{\\[^}]*\}/', '', $line) ?? $line;
                $line = preg_replace('/\[[^\]]+\]/', '', $line) ?? $line;
                $line = preg_replace('/\([^\)]+\)/', '', $line) ?? $line;
                $line = str_replace(['♪', '#'], '', $line);
                $line = preg_replace('/\s{2,}/', ' ', $line) ?? $line;
                $line = trim($line);

                if ($line === '') {
                    continue;
                }

                $cleanedLines[] = $line;
            }

            if ($cleanedLines === []) {
                continue;
            }

            $cleanedBlocks[] = implode(' ', $cleanedLines);
        }

        return implode("\n", $cleanedBlocks);
    }

    private function loadRecentPhrases(): void
    {
        $this->phrases = LearningPhrase::query()
            ->where('user_id', Auth::id())
            ->latest()
            ->limit(50)
            ->get()
            ->map(function (LearningPhrase $phrase): array {
                return [
                    'episode_title' => $phrase->episode_title,
                    'english_level' => $phrase->english_level,
                    'phrase' => $phrase->phrase,
                    'translation' => $phrase->translation,
                    'context_sentence' => $phrase->context_sentence,
                    'explanation' => $phrase->explanation,
                ];
            })
            ->toArray();
    }
}
