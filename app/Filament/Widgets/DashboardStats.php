<?php

namespace App\Filament\Widgets;

use App\Models\Questionnaire;
use App\Models\Response;
use App\Models\User;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Builder;

class DashboardStats extends Widget
{
    protected static ?int $sort = 0;

    protected string $view = 'filament.widgets.dashboard-stats';

    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $user = auth()->user();

        $questionnaires = Questionnaire::query();
        $responses = Response::query();

        if (! $user?->isAdmin()) {
            $questionnaires->where('user_id', $user?->id);
            $responses->whereHas('questionnaire', fn (Builder $query) => $query->where('user_id', $user?->id));
        }

        return [
            'stats' => [
                [
                    'label' => 'Jumlah user',
                    'value' => $user?->isAdmin() ? User::count() : 1,
                    'icon' => Heroicon::Users,
                    'accent' => '#38bdf8',
                ],
                [
                    'label' => 'Jumlah kuisioner',
                    'value' => (clone $questionnaires)->count(),
                    'icon' => Heroicon::ClipboardDocumentList,
                    'accent' => '#6366f1',
                ],
                [
                    'label' => 'Jumlah respons',
                    'value' => (clone $responses)->count(),
                    'icon' => Heroicon::ChartBar,
                    'accent' => '#10b981',
                ],
                [
                    'label' => 'Kuisioner aktif',
                    'value' => (clone $questionnaires)->available()->count(),
                    'icon' => Heroicon::CheckCircle,
                    'accent' => '#f59e0b',
                ],
            ],
        ];
    }
}
