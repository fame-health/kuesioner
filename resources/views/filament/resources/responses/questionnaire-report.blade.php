@php
    /** @var \App\Models\Questionnaire $questionnaire */
    $latestSubmit = $summary['latest_submit'] ? \Illuminate\Support\Carbon::parse($summary['latest_submit'])->format('d M Y H:i') : '-';
@endphp

<style>
    .report-page {
        display: grid;
        gap: 18px;
    }

    .report-hero,
    .report-card {
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        background: #ffffff;
        box-shadow: 0 10px 26px rgba(15, 23, 42, 0.05);
    }

    .report-hero {
        display: grid;
        gap: 8px;
        padding: 18px;
    }

    .report-hero h2 {
        margin: 0;
        color: #111827;
        font-size: 22px;
        font-weight: 800;
        line-height: 1.25;
    }

    .report-hero p {
        max-width: 860px;
        margin: 0;
        color: #6b7280;
        font-size: 14px;
        line-height: 1.65;
    }

    .report-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
    }

    .report-stat {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #ffffff;
        padding: 14px;
    }

    .report-stat span {
        display: block;
        color: #6b7280;
        font-size: 12px;
        font-weight: 700;
    }

    .report-stat strong {
        display: block;
        margin-top: 7px;
        color: #111827;
        font-size: 24px;
        font-weight: 900;
        line-height: 1;
    }

    .report-card {
        padding: 16px;
    }

    .report-card h3 {
        margin: 0;
        color: #111827;
        font-size: 16px;
        font-weight: 800;
    }

    .report-card-subtitle {
        margin: 5px 0 0;
        color: #6b7280;
        font-size: 13px;
    }

    .monthly-chart {
        display: grid;
        grid-template-columns: repeat(6, minmax(0, 1fr));
        gap: 10px;
        align-items: end;
        min-height: 190px;
        margin-top: 18px;
    }

    .monthly-item {
        display: grid;
        align-content: end;
        gap: 8px;
        height: 170px;
    }

    .monthly-bar-track {
        display: flex;
        align-items: end;
        height: 118px;
        border-radius: 10px;
        background: #f3f4f6;
        overflow: hidden;
    }

    .monthly-bar {
        width: 100%;
        min-height: 5px;
        border-radius: 10px 10px 0 0;
        background: linear-gradient(180deg, #16a34a, #0f766e);
    }

    .monthly-label {
        display: grid;
        gap: 2px;
        text-align: center;
    }

    .monthly-label strong {
        color: #111827;
        font-size: 13px;
    }

    .monthly-label span {
        color: #6b7280;
        font-size: 11px;
    }

    .analysis-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .analysis-question {
        display: grid;
        gap: 12px;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #ffffff;
        padding: 14px;
    }

    .analysis-question h4 {
        margin: 0;
        color: #111827;
        font-size: 14px;
        font-weight: 800;
        line-height: 1.45;
    }

    .analysis-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .analysis-pill {
        display: inline-flex;
        min-height: 26px;
        align-items: center;
        border-radius: 999px;
        background: #eef2ff;
        color: #3730a3;
        font-size: 11px;
        font-weight: 800;
        padding: 0 9px;
    }

    .option-row {
        display: grid;
        gap: 6px;
    }

    .option-head {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        color: #374151;
        font-size: 13px;
        font-weight: 700;
    }

    .option-track {
        height: 10px;
        overflow: hidden;
        border-radius: 999px;
        background: #f3f4f6;
    }

    .option-bar {
        height: 100%;
        min-width: 3px;
        border-radius: 999px;
        background: linear-gradient(90deg, #16a34a, #0f766e);
    }

    .responses-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 12px;
        overflow: hidden;
        border-radius: 12px;
    }

    .responses-table th,
    .responses-table td {
        border-bottom: 1px solid #e5e7eb;
        padding: 11px 10px;
        text-align: left;
        vertical-align: top;
    }

    .responses-table th {
        background: #f9fafb;
        color: #374151;
        font-size: 12px;
        font-weight: 800;
    }

    .responses-table td {
        color: #4b5563;
        font-size: 13px;
    }

    .empty-analysis {
        border: 1px dashed #d1d5db;
        border-radius: 12px;
        color: #6b7280;
        padding: 14px;
    }

    @media (max-width: 1100px) {
        .report-stats,
        .analysis-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 720px) {
        .report-stats,
        .analysis-grid,
        .monthly-chart {
            grid-template-columns: 1fr;
        }

        .monthly-item {
            height: auto;
        }

        .monthly-bar-track {
            height: 18px;
        }
    }
</style>

<div class="report-page">
    <section class="report-hero">
        <h2>{{ $questionnaire->title }}</h2>
        <p>{{ $questionnaire->description ?: 'Analisis respons kuisioner berdasarkan data responden yang sudah masuk.' }}</p>
    </section>

    <section class="report-stats">
        <div class="report-stat">
            <span>Total Respons</span>
            <strong>{{ $summary['responses'] }}</strong>
        </div>
        <div class="report-stat">
            <span>Total Pertanyaan</span>
            <strong>{{ $summary['questions'] }}</strong>
        </div>
        <div class="report-stat">
            <span>Pertanyaan Pilihan</span>
            <strong>{{ $summary['choice_questions'] }}</strong>
        </div>
        <div class="report-stat">
            <span>Submit Terakhir</span>
            <strong style="font-size: 15px; line-height: 1.35;">{{ $latestSubmit }}</strong>
        </div>
    </section>

    <section class="report-card">
        <h3>Diagram Respons Bulanan</h3>
        <p class="report-card-subtitle">Jumlah respons masuk dalam 6 bulan terakhir.</p>

        <div class="monthly-chart">
            @foreach ($monthlyResponses as $month)
                <div class="monthly-item">
                    <div class="monthly-bar-track">
                        <div class="monthly-bar" style="height: {{ $month['percentage'] }}%;"></div>
                    </div>
                    <div class="monthly-label">
                        <strong>{{ $month['count'] }}</strong>
                        <span>{{ $month['label'] }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="report-card">
        <h3>Analisis Pilihan Jawaban</h3>
        <p class="report-card-subtitle">Persentase dihitung dari jumlah responden yang menjawab pertanyaan tersebut.</p>

        @if (count($choiceAnalyses))
            <div class="analysis-grid" style="margin-top: 14px;">
                @foreach ($choiceAnalyses as $analysis)
                    <article class="analysis-question">
                        <div>
                            <h4>{{ $analysis['question'] }}</h4>
                            <div class="analysis-meta" style="margin-top: 8px;">
                                <span class="analysis-pill">{{ $analysis['type'] }}</span>
                                <span class="analysis-pill">{{ $analysis['answered'] }} jawaban</span>
                            </div>
                        </div>

                        @foreach ($analysis['options'] as $option)
                            <div class="option-row">
                                <div class="option-head">
                                    <span>{{ $option['label'] }}</span>
                                    <span>{{ $option['count'] }} respons · {{ $option['percentage'] }}%</span>
                                </div>
                                <div class="option-track">
                                    <div class="option-bar" style="width: {{ max($option['percentage'], 1) }}%;"></div>
                                </div>
                            </div>
                        @endforeach
                    </article>
                @endforeach
            </div>
        @else
            <div class="empty-analysis" style="margin-top: 14px;">
                Belum ada pertanyaan pilihan ganda, checkbox, atau dropdown untuk dianalisis.
            </div>
        @endif
    </section>

    <section class="report-card">
        <h3>Respons Terbaru</h3>
        <p class="report-card-subtitle">Daftar respons terakhir dari kuisioner ini.</p>

        <table class="responses-table">
            <thead>
                <tr>
                    <th>Responden</th>
                    <th>Email</th>
                    <th>Jawaban</th>
                    <th>Submit</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($latestResponses as $response)
                    <tr>
                        <td>{{ $response->respondent_name ?: 'Anonim' }}</td>
                        <td>{{ $response->respondent_email ?: '-' }}</td>
                        <td>{{ $response->answers_count }} jawaban</td>
                        <td>{{ $response->submitted_at?->format('d M Y H:i') ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">Belum ada respons untuk kuisioner ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>
</div>
