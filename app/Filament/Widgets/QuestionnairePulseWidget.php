<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Questionnaires\QuestionnaireResource;
use App\Filament\Resources\Responses\ResponseResource;
use App\Models\Answer;
use App\Models\Questionnaire;
use App\Models\Response;
use Carbon\CarbonPeriod;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class QuestionnairePulseWidget extends Widget
{
    protected static ?int $sort = 1;

    protected string $view = 'filament.widgets.questionnaire-pulse-widget';

    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $now = now();
        $questionnaires = $this->scopedQuestionnaires();
        $responses = $this->scopedResponses();

        $totalQuestionnaires = (clone $questionnaires)->count();
        $activeQuestionnaires = (clone $questionnaires)->available()->count();
        $totalResponses = (clone $responses)->count();
        $todayResponses = (clone $responses)
            ->whereDate('submitted_at', $now->toDateString())
            ->count();
        $yesterdayResponses = (clone $responses)
            ->whereDate('submitted_at', $now->copy()->subDay()->toDateString())
            ->count();
        $weeklyResponses = (clone $responses)
            ->where('submitted_at', '>=', $now->copy()->subDays(6)->startOfDay())
            ->count();
        $previousWeeklyResponses = (clone $responses)
            ->whereBetween('submitted_at', [
                $now->copy()->subDays(13)->startOfDay(),
                $now->copy()->subDays(7)->endOfDay(),
            ])
            ->count();

        $totalAnswers = Answer::query()
            ->whereHas('response', fn (Builder $query) => $this->applyResponseScope($query))
            ->count();

        $activeRate = $totalQuestionnaires > 0
            ? (int) round(($activeQuestionnaires / $totalQuestionnaires) * 100)
            : 0;
        $averageAnswers = $totalResponses > 0
            ? round($totalAnswers / $totalResponses, 1)
            : 0;
        $engagementScore = min(100, (int) round(
            ($activeRate * 0.45)
            + (min($weeklyResponses, 50) / 50 * 35)
            + (min($averageAnswers, 10) / 10 * 20),
        ));

        $topQuestionnaires = $this->getTopQuestionnaires($questionnaires);
        $maxTopResponses = max(1, $topQuestionnaires->max('responses_count') ?? 1);

        return [
            'createQuestionnaireUrl' => QuestionnaireResource::getUrl('create'),
            'dailyResponses' => $this->getDailyResponses($responses, $now),
            'engagementCopy' => $this->getEngagementCopy($engagementScore, $totalQuestionnaires, $activeQuestionnaires),
            'engagementScore' => $engagementScore,
            'latestResponses' => $this->getLatestResponses($responses),
            'maxTopResponses' => $maxTopResponses,
            'questionnairesUrl' => QuestionnaireResource::getUrl('index'),
            'responsesUrl' => ResponseResource::getUrl('index'),
            'todayDelta' => $this->calculateDelta($todayResponses, $yesterdayResponses),
            'todayResponses' => $todayResponses,
            'topQuestionnaires' => $topQuestionnaires,
            'totalResponses' => $totalResponses,
            'weeklyDelta' => $this->calculateDelta($weeklyResponses, $previousWeeklyResponses),
            'weeklyResponses' => $weeklyResponses,
        ];
    }

    private function scopedQuestionnaires(): Builder
    {
        return $this->applyQuestionnaireScope(Questionnaire::query());
    }

    private function scopedResponses(): Builder
    {
        return $this->applyResponseScope(Response::query());
    }

    private function applyQuestionnaireScope(Builder $query): Builder
    {
        $user = auth()->user();

        if (! $user?->isAdmin()) {
            $query->where('user_id', $user?->id ?? 0);
        }

        return $query;
    }

    private function applyResponseScope(Builder $query): Builder
    {
        $user = auth()->user();

        if (! $user?->isAdmin()) {
            $query->whereHas(
                'questionnaire',
                fn (Builder $query) => $query->where('user_id', $user?->id ?? 0),
            );
        }

        return $query;
    }

    private function calculateDelta(int $current, int $previous): ?int
    {
        if ($previous === 0) {
            return $current > 0 ? 100 : null;
        }

        return (int) round((($current - $previous) / $previous) * 100);
    }

    /**
     * @return array<int, array{label: string, date: string, count: int, height: int}>
     */
    private function getDailyResponses(Builder $responses, Carbon $now): array
    {
        $startDate = $now->copy()->subDays(6)->startOfDay();
        $counts = (clone $responses)
            ->whereNotNull('submitted_at')
            ->where('submitted_at', '>=', $startDate)
            ->selectRaw('DATE(submitted_at) as submitted_date, COUNT(*) as aggregate')
            ->groupBy('submitted_date')
            ->pluck('aggregate', 'submitted_date');

        $max = max(1, (int) $counts->max());
        $days = [];

        foreach (CarbonPeriod::create($startDate, '1 day', $now->copy()->startOfDay()) as $date) {
            $key = $date->format('Y-m-d');
            $count = (int) ($counts[$key] ?? 0);

            $days[] = [
                'count' => $count,
                'date' => $date->format('d M'),
                'height' => $count > 0 ? max(14, (int) round(($count / $max) * 100)) : 4,
                'label' => $date->format('D'),
            ];
        }

        return $days;
    }

    private function getEngagementCopy(int $score, int $totalQuestionnaires, int $activeQuestionnaires): string
    {
        if ($totalQuestionnaires === 0) {
            return 'Belum ada kuisioner. Dashboard siap menyala begitu data pertama masuk.';
        }

        if ($activeQuestionnaires === 0) {
            return 'Semua kuisioner sedang nonaktif. Aktifkan satu form untuk mulai menangkap respons.';
        }

        if ($score >= 75) {
            return 'Performa kuisioner sedang kuat. Pertahankan ritme publikasi dan pantau respons terbaru.';
        }

        if ($score >= 45) {
            return 'Aktivitas mulai bergerak. Dorong link kuisioner aktif agar respons makin ramai.';
        }

        return 'Momentum masih rendah. Rapikan pertanyaan, aktifkan form, lalu bagikan link publiknya.';
    }

    /**
     * @return Collection<int, array{
     *     title: string,
     *     meta: string,
     *     responses_count: int,
     *     status: string,
     *     status_class: string,
     *     url: string
     * }>
     */
    private function getTopQuestionnaires(Builder $questionnaires): Collection
    {
        return (clone $questionnaires)
            ->withCount(['questions', 'responses'])
            ->orderByDesc('responses_count')
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get()
            ->map(fn (Questionnaire $questionnaire): array => [
                'meta' => "{$questionnaire->questions_count} pertanyaan",
                'responses_count' => (int) $questionnaire->responses_count,
                'status' => $questionnaire->isAvailable()
                    ? 'Live'
                    : ($questionnaire->isExpired() ? 'Expired' : 'Draft'),
                'status_class' => $questionnaire->isAvailable()
                    ? 'is-live'
                    : ($questionnaire->isExpired() ? 'is-expired' : 'is-draft'),
                'title' => Str::limit($questionnaire->title, 52),
                'url' => QuestionnaireResource::getUrl('edit', ['record' => $questionnaire]),
            ]);
    }

    /**
     * @return Collection<int, array{
     *     respondent: string,
     *     questionnaire: string,
     *     submitted_at: string,
     *     url: string
     * }>
     */
    private function getLatestResponses(Builder $responses): Collection
    {
        return (clone $responses)
            ->with('questionnaire:id,title')
            ->latest('submitted_at')
            ->latest('id')
            ->limit(5)
            ->get()
            ->map(fn (Response $response): array => [
                'questionnaire' => Str::limit($response->questionnaire?->title ?? 'Kuisioner dihapus', 42),
                'respondent' => filled($response->respondent_name)
                    ? Str::limit($response->respondent_name, 34)
                    : 'Responden anonim',
                'submitted_at' => $response->submitted_at?->diffForHumans() ?? 'Belum submit',
                'url' => ResponseResource::getUrl('view', ['record' => $response]),
            ]);
    }
}
