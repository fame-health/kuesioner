<?php

namespace Tests\Feature;

use App\Models\Answer;
use App\Models\Question;
use App\Models\Questionnaire;
use App\Models\Response;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicChoiceAnalysisTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_only_shows_published_choice_analyses(): void
    {
        $user = User::factory()->create();
        $questionnaire = Questionnaire::query()->create([
            'user_id' => $user->id,
            'title' => 'Survei Layanan',
            'is_active' => true,
        ]);

        $publishedQuestion = $questionnaire->questions()->create([
            'question_text' => 'Bagaimana kualitas layanan?',
            'question_type' => Question::TYPE_RADIO,
            'is_required' => true,
            'show_public_analysis' => true,
            'order_number' => 1,
        ]);
        $publishedQuestion->options()->createMany([
            ['option_text' => 'Baik'],
            ['option_text' => 'Kurang'],
        ]);

        $privateQuestion = $questionnaire->questions()->create([
            'question_text' => 'Pertanyaan pilihan privat',
            'question_type' => Question::TYPE_DROPDOWN,
            'show_public_analysis' => false,
            'order_number' => 2,
        ]);
        $privateQuestion->options()->create(['option_text' => 'Rahasia']);

        $questionnaire->questions()->create([
            'question_text' => 'Jawaban teks tidak boleh dianalisis',
            'question_type' => Question::TYPE_PARAGRAPH,
            'show_public_analysis' => true,
            'order_number' => 3,
        ]);

        foreach (['Baik', 'Baik', 'Kurang'] as $answerText) {
            $response = Response::query()->create([
                'questionnaire_id' => $questionnaire->id,
                'submitted_at' => now(),
            ]);

            Answer::query()->create([
                'response_id' => $response->id,
                'question_id' => $publishedQuestion->id,
                'answer_text' => $answerText,
            ]);
        }

        $this->get('/')
            ->assertOk()
            ->assertSee('Hasil analisis jawaban')
            ->assertSee('Bagaimana kualitas layanan?')
            ->assertSee('2 respons · 66,7%', escape: false)
            ->assertDontSee('Pertanyaan pilihan privat')
            ->assertDontSee('Jawaban teks tidak boleh dianalisis');
    }

    public function test_checkbox_analysis_uses_respondents_as_percentage_denominator(): void
    {
        $user = User::factory()->create();
        $questionnaire = Questionnaire::query()->create([
            'user_id' => $user->id,
            'title' => 'Survei Media',
            'is_active' => true,
        ]);
        $question = $questionnaire->questions()->create([
            'question_text' => 'Media yang digunakan',
            'question_type' => Question::TYPE_CHECKBOX,
            'show_public_analysis' => true,
            'order_number' => 1,
        ]);
        $question->options()->createMany([
            ['option_text' => 'Website'],
            ['option_text' => 'WhatsApp'],
        ]);

        foreach ([['Website', 'WhatsApp'], ['Website']] as $values) {
            $response = Response::query()->create([
                'questionnaire_id' => $questionnaire->id,
                'submitted_at' => now(),
            ]);

            Answer::query()->create([
                'response_id' => $response->id,
                'question_id' => $question->id,
                'answer_json' => $values,
            ]);
        }

        $this->get('/')
            ->assertOk()
            ->assertSee('2 respons · 100,0%', escape: false)
            ->assertSee('1 respons · 50,0%', escape: false)
            ->assertSee('Bisa pilih lebih dari satu');
    }
}
