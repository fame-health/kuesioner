<?php

namespace App\Filament\Resources\Responses\Pages;

use App\Exports\ResponsesExport;
use App\Filament\Resources\Responses\ResponseResource;
use App\Models\Question;
use App\Models\Questionnaire;
use App\Services\ChoiceAnalysisService;
use Carbon\CarbonImmutable;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\View as SchemaView;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ViewResponse extends ViewRecord
{
    protected static string $resource = ResponseResource::class;

    public function getTitle(): string
    {
        return 'Analisis: '.$this->getRecord()->title;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali ke Daftar')
                ->icon(Heroicon::ArrowLeft)
                ->url(ResponseResource::getUrl('index')),
            Action::make('export')
                ->label('Export Excel')
                ->icon(Heroicon::ArrowDownTray)
                ->action(fn () => Excel::download(
                    new ResponsesExport($this->getRecord(), auth()->user()),
                    'hasil-kuisioner-'.Str::slug($this->getRecord()->title).'.xlsx',
                )),
        ];
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                SchemaView::make('filament.resources.responses.questionnaire-report')
                    ->viewData(fn (): array => [
                        'questionnaire' => $this->getRecord()->loadMissing(['user', 'questions.options']),
                        'summary' => $this->summary(),
                        'monthlyResponses' => $this->monthlyResponses(),
                        'choiceAnalyses' => app(ChoiceAnalysisService::class)
                            ->forQuestionnaire($this->getRecord()),
                        'latestResponses' => $this->latestResponses(),
                    ]),
            ]);
    }

    private function summary(): array
    {
        /** @var Questionnaire $questionnaire */
        $questionnaire = $this->getRecord();

        return [
            'questions' => $questionnaire->questions()->count(),
            'responses' => $questionnaire->responses()->count(),
            'choice_questions' => $questionnaire->questions()
                ->whereIn('question_type', [
                    Question::TYPE_RADIO,
                    Question::TYPE_CHECKBOX,
                    Question::TYPE_DROPDOWN,
                ])
                ->count(),
            'latest_submit' => $questionnaire->responses()->latest('submitted_at')->value('submitted_at'),
        ];
    }

    private function monthlyResponses(): array
    {
        /** @var Questionnaire $questionnaire */
        $questionnaire = $this->getRecord();
        $start = CarbonImmutable::now()->startOfMonth()->subMonths(5);

        $counts = $questionnaire->responses()
            ->whereNotNull('submitted_at')
            ->where('submitted_at', '>=', $start)
            ->get(['submitted_at'])
            ->groupBy(fn ($response): string => $response->submitted_at->format('Y-m'))
            ->map->count();

        $months = collect(range(0, 5))
            ->map(function (int $offset) use ($start, $counts): array {
                $month = $start->addMonths($offset);
                $key = $month->format('Y-m');

                return [
                    'key' => $key,
                    'label' => $month->translatedFormat('M Y'),
                    'count' => (int) ($counts[$key] ?? 0),
                ];
            });

        $max = max($months->max('count') ?? 0, 1);

        return $months
            ->map(fn (array $month): array => [
                ...$month,
                'percentage' => round(($month['count'] / $max) * 100, 1),
            ])
            ->all();
    }

    private function latestResponses(): Collection
    {
        /** @var Questionnaire $questionnaire */
        $questionnaire = $this->getRecord();

        return $questionnaire->responses()
            ->withCount('answers')
            ->latest('submitted_at')
            ->limit(20)
            ->get();
    }
}
