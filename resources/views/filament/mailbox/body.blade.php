<div class="space-y-4">
    <div>
        <strong>Nadawca:</strong>
        {{ $record->gmailAccounts->email }}
    </div>

    <div>
        <strong>Data:</strong>
        {{ $record->sent_at }}
    </div>

    <hr>

    <div class="w-full h-[70vh] border rounded-lg overflow-hidden">
        <iframe
            class="w-full h-full bg-white"
            srcdoc="{!! e($record->body) !!}">
        </iframe>
    </div>
</div>
