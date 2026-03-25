<x-filament-panels::page>
    <div class="space-y-6">
        <form wire:submit="analyzeTranscript" class="space-y-4">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-200">Tytuł odcinka (opcjonalnie)</label>
                    <input
                        type="text"
                        wire:model="episodeTitle"
                        placeholder="np. Friends S01E01"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                    />
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-200">Poziom angielskiego</label>
                    <select
                        wire:model="englishLevel"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                    >
                        @foreach(['A1', 'A2', 'B1', 'B2', 'C1', 'C2'] as $level)
                            <option value="{{ $level }}">{{ $level }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-200">Plik transkrypcji (opcjonalnie)</label>
                <input
                    type="file"
                    wire:model="transcriptFile"
                    accept=".txt,.srt,.vtt"
                    class="w-full rounded-lg border-gray-300 file:mr-4 file:rounded-md file:border-0 file:bg-gray-100 file:px-3 file:py-2 file:text-sm dark:border-gray-700 dark:bg-gray-900 dark:file:bg-gray-800"
                />
                <p class="mt-1 text-xs text-gray-500">Obsługiwane formaty: TXT, SRT, VTT (max 5 MB).</p>
                <div wire:loading wire:target="transcriptFile" class="mt-2 inline-flex items-center gap-2 text-sm text-amber-600 dark:text-amber-400">
                    <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                    Przetwarzam plik...
                </div>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-200">Transkrypcja odcinka</label>
                <textarea
                    wire:model="transcript"
                    rows="12"
                    placeholder="Wklej tutaj transkrypcję odcinka..."
                    class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                ></textarea>
                <p class="mt-1 text-xs text-gray-500">Minimum 50 znaków. Jeśli uzupełnisz i pole tekstowe, i plik — użyjemy pola tekstowego.</p>
            </div>

            <button
                type="submit"
                wire:loading.attr="disabled"
                wire:target="analyzeTranscript,transcriptFile"
                class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-2 text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-70"
            >
                <svg wire:loading wire:target="analyzeTranscript,transcriptFile" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
                <span wire:loading.remove wire:target="analyzeTranscript,transcriptFile">Analizuj i zapisz frazy</span>
                <span wire:loading wire:target="analyzeTranscript,transcriptFile">Przetwarzam...</span>
            </button>
        </form>

    </div>
</x-filament-panels::page>
