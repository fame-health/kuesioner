@php
    /** @var \App\Models\Question $record */
    $record->loadMissing('options');

    $typeLabel = \App\Models\Question::TYPES[$record->question_type] ?? $record->question_type;
@endphp

<style>
    .question-card-shell {
        width: 100%;
        overflow: hidden;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #ffffff;
        box-shadow: 0 8px 22px rgba(15, 23, 42, 0.05);
    }

    .question-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        border-bottom: 1px solid #eef2f7;
        background: linear-gradient(90deg, #f8fafc, #ffffff);
        padding: 12px 14px;
    }

    .question-card-badges,
    .question-card-actions {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
    }

    .question-card-badge {
        display: inline-flex;
        min-height: 28px;
        align-items: center;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
        padding: 0 10px;
    }

    .question-card-badge.success {
        background: #dcfce7;
        color: #166534;
    }

    .question-card-badge.info {
        background: #eef2ff;
        color: #3730a3;
    }

    .question-card-badge.warning {
        background: #fffbeb;
        color: #92400e;
    }

    .question-card-badge.gray {
        background: #f3f4f6;
        color: #4b5563;
    }

    .question-card-button {
        min-height: 34px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 12px;
        font-weight: 800;
        padding: 0 10px;
    }

    .question-card-button.copy {
        border: 1px solid #bfdbfe;
        background: #eff6ff;
        color: #1d4ed8;
    }

    .question-card-button.danger {
        border: 1px solid #fecdd3;
        background: #fff1f2;
        color: #be123c;
    }

    .question-card-body {
        display: grid;
        grid-template-columns: minmax(0, 8fr) minmax(300px, 4fr);
        gap: 14px;
        padding: 14px;
    }

    .question-card-field {
        display: grid;
        min-width: 0;
        gap: 7px;
    }

    .question-card-label {
        color: #374151;
        font-size: 12px;
        font-weight: 800;
    }

    .question-card-input,
    .question-card-select,
    .question-card-textarea {
        width: 100%;
        min-width: 0;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        background: #ffffff;
        color: #111827;
        font: inherit;
        font-size: 14px;
        outline: none;
    }

    .question-card-input,
    .question-card-select {
        min-height: 40px;
        padding: 8px 10px;
    }

    .question-card-textarea {
        min-height: 116px;
        padding: 10px 12px;
        resize: vertical;
    }

    .question-card-settings {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        align-content: start;
        gap: 10px;
        min-width: 0;
    }

    .question-card-settings .wide {
        grid-column: 1 / -1;
    }

    .question-card-static {
        display: flex;
        min-height: 40px;
        align-items: center;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        background: #f9fafb;
        color: #374151;
        font-size: 14px;
        font-weight: 800;
        padding: 0 10px;
    }

    .question-card-toggle {
        display: flex;
        min-height: 42px;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        background: #f9fafb;
        color: #374151;
        cursor: pointer;
        font-size: 13px;
        font-weight: 800;
        padding: 0 10px;
    }

    .question-card-toggle input {
        width: 18px;
        height: 18px;
        accent-color: #d97706;
    }

    .question-card-options {
        padding: 0 14px 14px;
    }

    @media (max-width: 980px) {
        .question-card-body {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 640px) {
        .question-card-header {
            align-items: stretch;
            flex-direction: column;
        }

        .question-card-actions button {
            flex: 1 1 auto;
        }

        .question-card-settings {
            grid-template-columns: 1fr;
        }
    }
</style>

<article class="question-card-shell">
    <div class="question-card-header">
        <div class="question-card-badges">
            <span class="question-card-badge success">Pertanyaan {{ $record->order_number }}</span>
            <span class="question-card-badge info">{{ $typeLabel }}</span>
            @if ($record->is_required)
                <span class="question-card-badge warning">Wajib</span>
            @else
                <span class="question-card-badge gray">Opsional</span>
            @endif
        </div>

        <div class="question-card-actions">
            <button
                type="button"
                class="question-card-button copy"
                wire:click.stop="duplicateQuestionFromCard({{ $record->id }})"
                wire:loading.attr="disabled"
            >
                Duplikat
            </button>
            <button
                type="button"
                class="question-card-button danger"
                wire:click.stop="deleteQuestionFromCard({{ $record->id }})"
                wire:confirm="Hapus pertanyaan ini?"
                wire:loading.attr="disabled"
            >
                Hapus
            </button>
        </div>
    </div>

    <div class="question-card-body">
        <div class="question-card-field">
            <label class="question-card-label">Teks pertanyaan</label>
            <textarea
                class="question-card-textarea"
                wire:change.stop="updateQuestionText({{ $record->id }}, $event.target.value)"
                placeholder="Tulis pertanyaan"
            >{{ $record->question_text }}</textarea>
        </div>

        <div class="question-card-settings">
            <div class="question-card-field wide">
                <label class="question-card-label">Tipe pertanyaan</label>
                <select
                    class="question-card-select"
                    wire:change.stop="updateQuestionType({{ $record->id }}, $event.target.value)"
                >
                    @foreach (\App\Models\Question::TYPES as $type => $label)
                        <option value="{{ $type }}" @selected($record->question_type === $type)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="question-card-field">
                <label class="question-card-label">Urutan</label>
                <input
                    class="question-card-input"
                    type="number"
                    min="1"
                    value="{{ $record->order_number }}"
                    wire:change.stop="updateQuestionOrder({{ $record->id }}, $event.target.value)"
                >
            </div>

            <div class="question-card-field">
                <label class="question-card-label">Jumlah opsi</label>
                <div class="question-card-static">
                    {{ $record->usesOptions() ? $record->options_count : '-' }}
                </div>
            </div>

            <label class="question-card-toggle wide">
                <span>Wajib diisi</span>
                <input
                    type="checkbox"
                    value="1"
                    @checked($record->is_required)
                    wire:change.stop="updateQuestionRequired({{ $record->id }}, $event.target.checked)"
                >
            </label>
        </div>
    </div>

    @if ($record->usesOptions())
        <div class="question-card-options">
            @include('filament.tables.columns.question-options-editor', ['record' => $record])
        </div>
    @endif
</article>
