<x-filament-panels::page>
    <div class="space-y-4">
        {{-- Kontener wiadomości --}}
        <div
            id="messages-container"
            class="bg-white dark:bg-gray-800 rounded-lg p-4 h-[600px] overflow-y-auto space-y-4"
            x-data
            @message-sent.window="$el.scrollTop = $el.scrollHeight"
        >
            @foreach($messages as $message)
                @if($message['role'] === 'user')
                    {{-- Chmurka użytkownika (prawa strona) --}}
                    <div class="flex justify-end">
                        <div class="max-w-[70%]">
                            <div class="bg-blue-500 text-white rounded-lg rounded-tr-none px-4 py-2">
                                {{ $message['content'] }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 text-right">
                                {{ $message['time'] }}
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Chmurka asystenta (lewa strona) --}}
                    <div class="flex justify-start">
                        <div class="max-w-[70%]">
                            <div class="bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg rounded-tl-none px-4 py-2">
                                {{ $message['content'] }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ $message['time'] }}
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        {{-- Formularz wysyłania wiadomości --}}
        <form wire:submit="sendMessage" class="flex gap-2">
            <input
                type="text"
                wire:model="messageInput"
                placeholder="Wpisz wiadomość..."
                class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                autofocus
            />
            <button
                type="submit"
                class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition"
            >
                Wyślij
            </button>
        </form>
    </div>
</x-filament-panels::page>
