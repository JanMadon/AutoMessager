<?php

namespace Tests\Feature;

use App\Models\LearningPhrase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LearningPhraseTest extends TestCase
{
    use RefreshDatabase;

    public function test_learning_phrase_can_be_saved_with_translation(): void
    {
        $user = User::factory()->create();

        $phrase = LearningPhrase::create([
            'user_id' => $user->id,
            'episode_title' => 'Friends S01E01',
            'english_level' => 'B1',
            'phrase' => 'to hit the road',
            'translation' => 'ruszać w drogę',
            'context_sentence' => 'Come on, we need to hit the road now.',
            'explanation' => 'Idiomatic phrase used when leaving a place.',
        ]);

        $this->assertDatabaseHas('learning_phrases', [
            'id' => $phrase->id,
            'user_id' => $user->id,
            'phrase' => 'to hit the road',
            'translation' => 'ruszać w drogę',
        ]);
    }
}
