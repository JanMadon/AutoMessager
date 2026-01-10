<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MailBox extends Model
{
    protected $fillable = [
        'gmail_accounts_id',
        'sent_at',
        'subject',
        'body',
    ];

    public function gmailAccounts(): BelongsTo
    {
        return $this->belongsTo(GmailAccount::class, 'gmail_accounts_id');
    }
}
