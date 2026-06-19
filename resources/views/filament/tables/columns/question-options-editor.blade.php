@php
    /** @var \App\Models\Question $record */
    $record->loadMissing('options');
@endphp

@if (! $record->usesOptions())
    <div style="border: 1px dashed #d1d5db; border-radius: 8px; color: #6b7280; font-size: 13px; padding: 10px 12px;">
        Jenis pertanyaan ini tidak memakai pilihan jawaban.
    </div>
@else
    <style>
        .question-options-editor {
            display: grid;
            gap: 10px;
            border-top: 1px solid #e5e7eb;
            padding-top: 12px;
        }

        .question-options-header {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 8px;
        }

        .question-options-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 8px;
        }

        .question-option-item {
            display: grid;
            align-items: center;
            gap: 8px;
            grid-template-columns: minmax(0, 1fr) auto;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            background: #f9fafb;
            padding: 8px;
        }

        @media (max-width: 1180px) {
            .question-options-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 720px) {
            .question-options-grid,
            .question-option-item {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="question-options-editor">
        <div class="question-options-header">
            <span style="color: #374151; font-size: 13px; font-weight: 700;">Pilihan jawaban</span>
            <button
                type="button"
                wire:click.stop="addQuestionOption({{ $record->id }})"
                wire:loading.attr="disabled"
                style="align-items: center; background: #ecfdf5; border: 1px solid #bbf7d0; border-radius: 8px; color: #166534; cursor: pointer; display: inline-flex; font-size: 12px; font-weight: 700; min-height: 34px; padding: 0 10px;"
            >
                + Tambah Jawaban
            </button>
        </div>

        @if ($record->options->isNotEmpty())
            <div class="question-options-grid">
                @foreach ($record->options as $option)
                    <div class="question-option-item">
                        <input
                            type="text"
                            value="{{ $option->option_text }}"
                            wire:change.stop="updateQuestionOption({{ $option->id }}, $event.target.value)"
                            style="background: #ffffff; border: 1px solid #d1d5db; border-radius: 8px; color: #111827; font: inherit; font-size: 14px; min-height: 38px; min-width: 0; outline: none; padding: 8px 10px; width: 100%;"
                            placeholder="Tulis pilihan jawaban"
                        >
                        <button
                            type="button"
                            wire:click.stop="deleteQuestionOption({{ $option->id }})"
                            wire:loading.attr="disabled"
                            style="background: #fff1f2; border: 1px solid #fecdd3; border-radius: 8px; color: #be123c; cursor: pointer; font-size: 12px; font-weight: 700; min-height: 38px; padding: 0 9px;"
                        >
                            Hapus
                        </button>
                    </div>
                @endforeach
            </div>
        @else
            <div style="border: 1px dashed #d1d5db; border-radius: 8px; color: #6b7280; font-size: 13px; padding: 10px 12px;">
                Belum ada pilihan jawaban. Klik <strong>Tambah Jawaban</strong> untuk menambahkan opsi.
            </div>
        @endif

        <span style="color: #6b7280; font-size: 12px;">
            Ubah teks langsung pada kolom input. Tidak perlu memakai tanda pemisah.
        </span>
    </div>
@endif
