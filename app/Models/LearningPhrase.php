<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LearningPhrase extends Model
{
    protected $fillable = [
        'user_id',
        'episode_title',
        'english_level',
        'phrase',
        'translation',
        'context_sentence',
        'explanation',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
