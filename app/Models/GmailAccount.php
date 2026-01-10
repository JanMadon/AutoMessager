<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GmailAccount extends Model
{
    protected $fillable = [
        'email',
        'refresh_token',
    ];

    public function mailBoxes(): HasMany
    {
        return $this->hasMany(Mailbox::class, 'gmail_accounts_id');
    }
}

