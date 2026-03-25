<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailHistory extends Model
{
    protected $fillable = [
        'gmail_accounts_id',
        'history_id',
    ];


    public function gmailAccount(): BelongsTo
    {
        return $this->belongsTo(GmailAccount::class);
    }
}
