<x-filament-widgets::widget>
    <style>
        .dashboard-summary-card {
            overflow: hidden;
        }

        .dashboard-summary-heading {
            margin-bottom: 12px;
            color: #020617;
            font-size: 1.08rem;
            font-weight: 800;
        }

        .dark .dashboard-summary-heading {
            color: #f8fafc;
        }

        .dashboard-summary-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 0;
        }

        .dashboard-summary-item {
            display: grid;
            min-height: 118px;
            gap: 14px;
            align-content: center;
            border-inline-end: 1px solid rgba(148, 163, 184, 0.22);
            padding: 18px 22px;
        }

        .dashboard-summary-item:last-child {
            border-inline-end: 0;
        }

        .dashboard-summary-label {
            display: flex;
            min-width: 0;
            align-items: center;
            gap: 10px;
            color: #667085;
            font-size: 0.92rem;
            font-weight: 700;
        }

        .dark .dashboard-summary-label {
            color: #cbd5e1;
        }

        .dashboard-summary-icon {
            display: grid;
            width: 34px;
            height: 34px;
            flex: 0 0 auto;
            place-items: center;
            border-radius: 10px;
            background: color-mix(in srgb, var(--summary-accent) 14%, transparent);
            color: var(--summary-accent);
        }

        .dashboard-summary-value {
            color: #020617;
            font-size: 2rem;
            font-weight: 850;
            line-height: 1;
        }

        .dark .dashboard-summary-value {
            color: #f8fafc;
        }

        @media (max-width: 1024px) {
            .dashboard-summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .dashboard-summary-item:nth-child(2) {
                border-inline-end: 0;
            }

            .dashboard-summary-item:nth-child(-n + 2) {
                border-bottom: 1px solid rgba(148, 163, 184, 0.22);
            }
        }

        @media (max-width: 640px) {
            .dashboard-summary-grid {
                grid-template-columns: 1fr;
            }

            .dashboard-summary-item,
            .dashboard-summary-item:nth-child(2) {
                border-inline-end: 0;
            }

            .dashboard-summary-item:not(:last-child) {
                border-bottom: 1px solid rgba(148, 163, 184, 0.22);
            }
        }
    </style>

    <h2 class="dashboard-summary-heading">Ringkasan</h2>

    <x-filament::section class="dashboard-summary-card">
        <div class="dashboard-summary-grid">
            @foreach ($stats as $stat)
                <div class="dashboard-summary-item" style="--summary-accent: {{ $stat['accent'] }}">
                    <div class="dashboard-summary-label">
                        <span class="dashboard-summary-icon">
                            <x-filament::icon :icon="$stat['icon']" class="h-5 w-5" />
                        </span>
                        <span>{{ $stat['label'] }}</span>
                    </div>

                    <div class="dashboard-summary-value">
                        {{ number_format($stat['value']) }}
                    </div>
                </div>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
