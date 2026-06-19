@php
    use Filament\Support\Icons\Heroicon;

    $weeklyDeltaLabel = $weeklyDelta === null ? 'Baru' : (($weeklyDelta > 0 ? '+' : '') . $weeklyDelta . '%');
    $todayDeltaLabel = $todayDelta === null ? 'Stabil' : (($todayDelta > 0 ? '+' : '') . $todayDelta . '%');
@endphp

<x-filament-widgets::widget>
    <style>
        .qp-layout {
            --qp-card: rgba(255, 255, 255, 0.92);
            --qp-text: #0f172a;
            --qp-muted: #64748b;
            --qp-border: rgba(15, 23, 42, 0.12);
            --qp-soft: rgba(248, 250, 252, 0.92);
            display: grid;
            grid-template-columns: minmax(260px, 3fr) minmax(0, 7fr);
            gap: 16px;
            align-items: stretch;
        }

        .dark .qp-layout {
            --qp-card: rgba(15, 23, 42, 0.88);
            --qp-text: #e5e7eb;
            --qp-muted: #94a3b8;
            --qp-border: rgba(148, 163, 184, 0.18);
            --qp-soft: rgba(30, 41, 59, 0.7);
        }

        .qp-card {
            border: 1px solid var(--qp-border);
            border-radius: 18px;
            background: var(--qp-card);
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.1);
            color: var(--qp-text);
        }

        .qp-pulse-card {
            display: grid;
            gap: 18px;
            align-content: start;
            overflow: hidden;
            padding: 22px;
            background:
                linear-gradient(160deg, rgba(15, 23, 42, 0.96), rgba(49, 46, 129, 0.92) 54%, rgba(15, 118, 110, 0.9)),
                #0f172a;
            color: white;
        }

        .qp-detail-card {
            padding: 18px;
        }

        .qp-eyebrow,
        .qp-link {
            color: rgba(255, 255, 255, 0.72);
            font-size: 0.76rem;
            font-weight: 800;
            letter-spacing: 0;
            text-transform: uppercase;
        }

        .qp-title {
            margin: 8px 0 0;
            font-size: clamp(1.55rem, 2.4vw, 2.25rem);
            font-weight: 850;
            line-height: 1.04;
        }

        .qp-copy {
            margin: 10px 0 0;
            color: rgba(255, 255, 255, 0.78);
            font-size: 0.94rem;
            line-height: 1.55;
        }

        .qp-score {
            display: grid;
            width: min(100%, 190px);
            aspect-ratio: 1;
            place-items: center;
            justify-self: center;
            border-radius: 999px;
            background:
                conic-gradient(#34d399 calc(var(--score) * 1%), rgba(255, 255, 255, 0.18) 0),
                rgba(255, 255, 255, 0.12);
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.22), 0 22px 44px rgba(0, 0, 0, 0.18);
        }

        .qp-score-inner {
            display: grid;
            width: 72%;
            aspect-ratio: 1;
            place-items: center;
            border-radius: 999px;
            background: rgba(15, 23, 42, 0.88);
            text-align: center;
        }

        .qp-score-value {
            font-size: clamp(2.25rem, 5vw, 3.1rem);
            font-weight: 900;
            line-height: 1;
        }

        .qp-score-label {
            margin-top: 4px;
            color: rgba(255, 255, 255, 0.68);
            font-size: 0.76rem;
            font-weight: 800;
            text-transform: uppercase;
        }

        .qp-pulse-metrics {
            display: grid;
            gap: 10px;
        }

        .qp-pulse-metric {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.1);
            padding: 12px;
        }

        .qp-pulse-metric span {
            color: rgba(255, 255, 255, 0.68);
            font-size: 0.78rem;
            font-weight: 750;
        }

        .qp-pulse-metric strong {
            font-size: 1.08rem;
            font-weight: 900;
        }

        .qp-actions {
            display: grid;
            gap: 10px;
        }

        .qp-section-heading,
        .qp-row-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .qp-section-heading h3 {
            margin: 0;
            font-size: 1rem;
            font-weight: 850;
        }

        .qp-small {
            color: var(--qp-muted);
            font-size: 0.78rem;
            font-weight: 680;
        }

        .qp-link {
            color: var(--qp-muted);
            text-decoration: none;
        }

        .qp-link:hover {
            color: #4f46e5;
        }

        .qp-bars {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 10px;
            height: 210px;
            margin-top: 18px;
            align-items: end;
        }

        .qp-bar {
            display: grid;
            min-width: 0;
            gap: 8px;
            align-items: end;
            text-align: center;
        }

        .qp-bar-track {
            display: flex;
            height: 152px;
            align-items: end;
            overflow: hidden;
            border-radius: 999px;
            background: rgba(148, 163, 184, 0.18);
        }

        .qp-bar-fill {
            width: 100%;
            min-height: 6px;
            border-radius: 999px;
            background: linear-gradient(180deg, #38bdf8, #34d399 72%, #f59e0b);
        }

        .qp-bar-count {
            color: var(--qp-text);
            font-size: 0.8rem;
            font-weight: 850;
        }

        .qp-bar-label {
            color: var(--qp-muted);
            font-size: 0.72rem;
            font-weight: 750;
        }

        .qp-bottom-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            gap: 14px;
            margin-top: 16px;
        }

        .qp-panel {
            border: 1px solid var(--qp-border);
            border-radius: 16px;
            background: var(--qp-soft);
            padding: 14px;
        }

        .qp-list,
        .qp-latest {
            display: grid;
            gap: 10px;
            margin-top: 14px;
        }

        .qp-list-row {
            display: grid;
            gap: 8px;
            border: 1px solid var(--qp-border);
            border-radius: 12px;
            background: var(--qp-card);
            padding: 12px;
            color: var(--qp-text);
            text-decoration: none;
            transition: transform 150ms ease, border-color 150ms ease, box-shadow 150ms ease;
        }

        .qp-list-row:hover {
            transform: translateY(-1px);
            border-color: color-mix(in srgb, #14b8a6 38%, var(--qp-border));
            box-shadow: 0 14px 34px rgba(15, 23, 42, 0.12);
        }

        .qp-row-title {
            min-width: 0;
            overflow-wrap: anywhere;
            font-size: 0.88rem;
            font-weight: 850;
        }

        .qp-badge {
            border-radius: 999px;
            padding: 4px 9px;
            font-size: 0.68rem;
            font-weight: 850;
        }

        .qp-badge.is-live {
            background: rgba(16, 185, 129, 0.14);
            color: #047857;
        }

        .qp-badge.is-draft {
            background: rgba(100, 116, 139, 0.14);
            color: #475569;
        }

        .qp-badge.is-expired {
            background: rgba(244, 63, 94, 0.14);
            color: #be123c;
        }

        .dark .qp-badge.is-live {
            color: #6ee7b7;
        }

        .dark .qp-badge.is-draft {
            color: #cbd5e1;
        }

        .dark .qp-badge.is-expired {
            color: #fda4af;
        }

        .qp-progress {
            height: 7px;
            overflow: hidden;
            border-radius: 999px;
            background: rgba(148, 163, 184, 0.18);
        }

        .qp-progress span {
            display: block;
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(90deg, #14b8a6, #38bdf8);
        }

        .qp-latest-row {
            display: grid;
            grid-template-columns: auto minmax(0, 1fr);
            gap: 10px;
            align-items: start;
            border-bottom: 1px solid rgba(148, 163, 184, 0.18);
            padding: 10px 0;
            color: var(--qp-text);
            text-decoration: none;
        }

        .qp-latest-row:last-child {
            border-bottom: 0;
            padding-bottom: 0;
        }

        .qp-dot {
            width: 10px;
            height: 10px;
            margin-top: 5px;
            border-radius: 999px;
            background: #34d399;
            box-shadow: 0 0 0 5px rgba(52, 211, 153, 0.14);
        }

        .qp-empty {
            border: 1px dashed var(--qp-border);
            border-radius: 12px;
            padding: 18px;
            color: var(--qp-muted);
            text-align: center;
        }

        @media (max-width: 1180px) {
            .qp-layout,
            .qp-bottom-grid {
                grid-template-columns: 1fr;
            }

            .qp-pulse-card {
                grid-template-columns: minmax(0, 1fr) auto;
                align-items: center;
            }

            .qp-pulse-metrics,
            .qp-actions {
                grid-column: 1 / -1;
            }
        }

        @media (max-width: 760px) {
            .qp-pulse-card,
            .qp-layout {
                grid-template-columns: 1fr;
            }

            .qp-score {
                width: min(100%, 150px);
                justify-self: start;
            }

            .qp-bars {
                gap: 7px;
                height: 180px;
            }

            .qp-bar-track {
                height: 128px;
            }
        }
    </style>

    <section class="qp-layout" wire:poll.60s>
        <aside class="qp-card qp-pulse-card">
            <div>
                <div class="qp-eyebrow">Dashboard kuisioner</div>
                <h2 class="qp-title">Pusat kendali respons</h2>
                <p class="qp-copy">{{ $engagementCopy }}</p>
            </div>

            <div class="qp-score" style="--score: {{ $engagementScore }}">
                <div class="qp-score-inner">
                    <div>
                        <div class="qp-score-value">{{ $engagementScore }}</div>
                        <div class="qp-score-label">Pulse</div>
                    </div>
                </div>
            </div>

            <div class="qp-pulse-metrics">
                <div class="qp-pulse-metric">
                    <span>Minggu ini</span>
                    <strong>{{ number_format($weeklyResponses) }} respons</strong>
                </div>
                <div class="qp-pulse-metric">
                    <span>Tren</span>
                    <strong>{{ $weeklyDeltaLabel }}</strong>
                </div>
                <div class="qp-pulse-metric">
                    <span>Hari ini</span>
                    <strong>{{ number_format($todayResponses) }} respons</strong>
                </div>
            </div>

            <div class="qp-actions">
                <x-filament::button
                    :href="$createQuestionnaireUrl"
                    :icon="Heroicon::PlusCircle"
                    tag="a"
                >
                    Buat kuisioner
                </x-filament::button>

                <x-filament::button
                    color="gray"
                    :href="$responsesUrl"
                    :icon="Heroicon::ChartBar"
                    tag="a"
                >
                    Lihat respons
                </x-filament::button>
            </div>
        </aside>

        <div class="qp-card qp-detail-card">
            <div class="qp-section-heading">
                <div>
                    <h3>Ritme respons 7 hari</h3>
                    <div class="qp-small">
                        {{ number_format($totalResponses) }} total respons tersimpan - {{ $todayDeltaLabel }} dari kemarin
                    </div>
                </div>
                <a class="qp-link" href="{{ $responsesUrl }}">Detail</a>
            </div>

            <div class="qp-bars">
                @foreach ($dailyResponses as $day)
                    <div class="qp-bar" title="{{ $day['date'] }}: {{ $day['count'] }} respons">
                        <div class="qp-bar-track">
                            <span class="qp-bar-fill" style="height: {{ $day['height'] }}%"></span>
                        </div>
                        <div class="qp-bar-count">{{ $day['count'] }}</div>
                        <div class="qp-bar-label">{{ $day['label'] }}</div>
                    </div>
                @endforeach
            </div>

            <div class="qp-bottom-grid">
                <div class="qp-panel">
                    <div class="qp-section-heading">
                        <div>
                            <h3>Top kuisioner</h3>
                            <div class="qp-small">Berdasarkan respons terbanyak</div>
                        </div>
                        <a class="qp-link" href="{{ $questionnairesUrl }}">Semua</a>
                    </div>

                    <div class="qp-list">
                        @forelse ($topQuestionnaires as $questionnaire)
                            <a class="qp-list-row" href="{{ $questionnaire['url'] }}">
                                <div class="qp-row-top">
                                    <div>
                                        <div class="qp-row-title">{{ $questionnaire['title'] }}</div>
                                        <div class="qp-small">{{ $questionnaire['meta'] }} - {{ number_format($questionnaire['responses_count']) }} respons</div>
                                    </div>
                                    <span class="qp-badge {{ $questionnaire['status_class'] }}">{{ $questionnaire['status'] }}</span>
                                </div>
                                <div class="qp-progress">
                                    <span style="width: {{ max(4, (int) round(($questionnaire['responses_count'] / $maxTopResponses) * 100)) }}%"></span>
                                </div>
                            </a>
                        @empty
                            <div class="qp-empty">Belum ada kuisioner untuk ditampilkan.</div>
                        @endforelse
                    </div>
                </div>

                <div class="qp-panel">
                    <div class="qp-section-heading">
                        <div>
                            <h3>Respons terbaru</h3>
                            <div class="qp-small">Jawaban yang baru masuk</div>
                        </div>
                        <a class="qp-link" href="{{ $responsesUrl }}">Laporan</a>
                    </div>

                    <div class="qp-latest">
                        @forelse ($latestResponses as $response)
                            <a class="qp-latest-row" href="{{ $response['url'] }}">
                                <span class="qp-dot"></span>
                                <span>
                                    <span class="qp-row-title">{{ $response['respondent'] }}</span>
                                    <span class="qp-small">{{ $response['questionnaire'] }} - {{ $response['submitted_at'] }}</span>
                                </span>
                            </a>
                        @empty
                            <div class="qp-empty">Belum ada respons masuk.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-filament-widgets::widget>
