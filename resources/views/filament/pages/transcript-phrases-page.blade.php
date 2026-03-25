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
                class="inline-flex items-center rounded-lg bg-blue-600 px-5 py-2 text-white transition hover:bg-blue-700"
            >
                Analizuj i zapisz frazy
            </button>
        </form>

        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <h3 class="mb-4 text-lg font-semibold">Ostatnio zapisane frazy</h3>

            @if(empty($phrases))
                <p class="text-sm text-gray-500">Brak zapisanych fraz.</p>
            @else
                <div class="space-y-3">
                    @foreach($phrases as $phrase)
                        <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-800">
                            <div class="mb-1 flex flex-wrap items-center gap-2 text-xs text-gray-500">
                                @if(!empty($phrase['episode_title']))
                                    <span>{{ $phrase['episode_title'] }}</span>
                                @endif
                                <span class="rounded bg-gray-100 px-2 py-0.5 dark:bg-gray-800">{{ $phrase['english_level'] }}</span>
                            </div>
                            <div class="font-medium text-gray-900 dark:text-gray-100">{{ $phrase['phrase'] }}</div>
                            <div class="text-sm text-blue-700 dark:text-blue-300">{{ $phrase['translation'] }}</div>

                            @if(!empty($phrase['context_sentence']))
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">{{ $phrase['context_sentence'] }}</p>
                            @endif

                            @if(!empty($phrase['explanation']))
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $phrase['explanation'] }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
