<?php

namespace App\Http\Controllers;

use App\Filament\Resources\Responses\ResponseResource;
use App\Models\Question;
use App\Models\Questionnaire;
use App\Models\Response as QuestionnaireResponse;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Events\DatabaseNotificationsSent;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PublicQuestionnaireController extends Controller
{
    public function show(string $token): View
    {
        $questionnaire = Questionnaire::query()
            ->with(['questions.options'])
            ->where('public_token', $token)
            ->first();

        if (! $questionnaire) {
            return view('questionnaires.unavailable', [
                'message' => 'Link kuisioner tidak valid.',
            ]);
        }

        if (! $questionnaire->isAvailable()) {
            return view('questionnaires.unavailable', [
                'message' => 'Kuisioner tidak tersedia atau sudah melewati batas waktu pengisian.',
            ]);
        }

        return view('questionnaires.show', [
            'questionnaire' => $questionnaire,
        ]);
    }

    public function submit(Request $request, string $token): RedirectResponse|View
    {
        $questionnaire = Questionnaire::query()
            ->with(['questions.options'])
            ->where('public_token', $token)
            ->first();

        if (! $questionnaire || ! $questionnaire->isAvailable()) {
            return view('questionnaires.unavailable', [
                'message' => 'Kuisioner tidak tersedia atau sudah melewati batas waktu pengisian.',
            ]);
        }

        $validated = $request->validate($this->rulesFor($questionnaire));

        $response = DB::transaction(function () use ($questionnaire, $validated): QuestionnaireResponse {
            $response = QuestionnaireResponse::query()->create([
                'questionnaire_id' => $questionnaire->id,
                'respondent_name' => $validated['respondent_name'] ?? null,
                'respondent_email' => $validated['respondent_email'] ?? null,
                'submitted_at' => now(),
            ]);

            foreach ($questionnaire->questions as $question) {
                $value = data_get($validated, "answers.{$question->id}");

                $response->answers()->create([
                    'question_id' => $question->id,
                    'answer_text' => $question->question_type === Question::TYPE_CHECKBOX ? null : $value,
                    'answer_json' => $question->question_type === Question::TYPE_CHECKBOX ? array_values($value ?? []) : null,
                ]);
            }

            return $response;
        });

        $this->notifyNewResponse($response->load('questionnaire.user'));

        return redirect()->route('questionnaires.public.success', $questionnaire->public_token);
    }

    public function success(): View
    {
        return view('questionnaires.success');
    }

    private function notifyNewResponse(QuestionnaireResponse $response): void
    {
        $questionnaire = $response->questionnaire;
        $respondent = filled($response->respondent_name)
            ? $response->respondent_name
            : 'Responden anonim';

        $recipients = User::query()
            ->where('is_active', true)
            ->where(function ($query) use ($questionnaire): void {
                $query
                    ->where('role', 'admin')
                    ->orWhere('id', $questionnaire->user_id);
            })
            ->get()
            ->unique('id')
            ->values();

        if ($recipients->isEmpty()) {
            return;
        }

        $notification = Notification::make()
            ->title('Respons kuisioner baru')
            ->body("{$respondent} mengisi {$questionnaire->title}.")
            ->icon(Heroicon::ChartBar)
            ->iconColor('success')
            ->actions([
                Action::make('view')
                    ->label('Lihat respons')
                    ->button()
                    ->url(ResponseResource::getUrl('view', ['record' => $questionnaire], isAbsolute: false))
                    ->markAsRead(),
            ]);

        NotificationFacade::sendNow($recipients, $notification->toDatabase());

        $recipients->each(fn (User $user) => DatabaseNotificationsSent::dispatch($user));
    }

    private function rulesFor(Questionnaire $questionnaire): array
    {
        $rules = [
            'respondent_name' => ['nullable', 'string', 'max:255'],
            'respondent_email' => ['nullable', 'email', 'max:255'],
            'answers' => ['array'],
        ];

        foreach ($questionnaire->questions as $question) {
            $field = "answers.{$question->id}";
            $baseRules = $question->is_required ? ['required'] : ['nullable'];

            if ($question->question_type === Question::TYPE_CHECKBOX) {
                $options = $question->options->pluck('option_text')->all();

                $rules[$field] = [
                    ...($question->is_required ? ['required', 'array', 'min:1'] : ['nullable', 'array']),
                ];
                $rules["{$field}.*"] = [Rule::in($options)];

                continue;
            }

            if (in_array($question->question_type, [Question::TYPE_RADIO, Question::TYPE_DROPDOWN], true)) {
                $rules[$field] = [
                    ...$baseRules,
                    Rule::in($question->options->pluck('option_text')->all()),
                ];

                continue;
            }

            $rules[$field] = [
                ...$baseRules,
                'string',
                $question->question_type === Question::TYPE_SHORT_TEXT ? 'max:255' : 'max:5000',
            ];
        }

        return $rules;
    }
}
