<?php

namespace App\Filament\Resources\Questionnaires\RelationManagers;

use App\Models\Question;
use App\Models\QuestionOption;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class QuestionsRelationManager extends RelationManager
{
    protected static string $relationship = 'questions';

    protected static ?string $title = 'Pertanyaan';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('question_text')
                    ->label('Pertanyaan')
                    ->required()
                    ->rows(3)
                    ->columnSpanFull(),
                Select::make('question_type')
                    ->label('Jenis pertanyaan')
                    ->options(Question::TYPES)
                    ->required()
                    ->live(),
                TextInput::make('order_number')
                    ->label('Urutan')
                    ->numeric()
                    ->minValue(1)
                    ->default(1)
                    ->required(),
                Toggle::make('is_required')
                    ->label('Wajib diisi')
                    ->default(false),
                Toggle::make('show_public_analysis')
                    ->label('Tampilkan analisis di landing page')
                    ->helperText('Hanya tersedia untuk pertanyaan berbentuk pilihan.')
                    ->default(false)
                    ->visible(fn (Get $get): bool => (auth()->user()?->isAdmin() ?? false)
                        && in_array($get('question_type'), [
                            Question::TYPE_RADIO,
                            Question::TYPE_CHECKBOX,
                            Question::TYPE_DROPDOWN,
                        ], true))
                    ->dehydrated(fn (Get $get): bool => (auth()->user()?->isAdmin() ?? false)
                        && in_array($get('question_type'), [
                            Question::TYPE_RADIO,
                            Question::TYPE_CHECKBOX,
                            Question::TYPE_DROPDOWN,
                        ], true)),
                Repeater::make('options')
                    ->label('Pilihan jawaban')
                    ->relationship()
                    ->schema([
                        TextInput::make('option_text')
                            ->label('Teks pilihan')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->minItems(1)
                    ->defaultItems(2)
                    ->visible(fn (Get $get): bool => in_array($get('question_type'), [
                        Question::TYPE_RADIO,
                        Question::TYPE_CHECKBOX,
                        Question::TYPE_DROPDOWN,
                    ], true))
                    ->dehydrated(fn (Get $get): bool => in_array($get('question_type'), [
                        Question::TYPE_RADIO,
                        Question::TYPE_CHECKBOX,
                        Question::TYPE_DROPDOWN,
                    ], true))
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('question_text')
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with('options')->withCount('options'))
            ->columns([
                ViewColumn::make('question_card')
                    ->label('Daftar pertanyaan')
                    ->view('filament.tables.columns.question-card')
                    ->grow()
                    ->extraAttributes(['style' => 'width: 100%;'])
                    ->extraCellAttributes(['style' => 'width: 100%;']),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()->label('Tambah Pertanyaan'),
            ])
            ->recordActions([]);
    }

    public function updateQuestionText(int $questionId, ?string $questionText): void
    {
        $question = $this->findOwnedQuestion($questionId);
        $questionText = trim((string) $questionText);

        if (! $question || $questionText === '') {
            Notification::make()
                ->title('Pertanyaan tidak boleh kosong')
                ->warning()
                ->send();

            return;
        }

        $question->update([
            'question_text' => $questionText,
        ]);
    }

    public function updateQuestionType(int $questionId, ?string $questionType): void
    {
        $question = $this->findOwnedQuestion($questionId);

        if (! $question || ! array_key_exists((string) $questionType, Question::TYPES)) {
            return;
        }

        $question->update([
            'question_type' => $questionType,
            'show_public_analysis' => in_array($questionType, [
                Question::TYPE_RADIO,
                Question::TYPE_CHECKBOX,
                Question::TYPE_DROPDOWN,
            ], true)
                ? $question->show_public_analysis
                : false,
        ]);

        if ($question->fresh()->usesOptions() && $question->options()->doesntExist()) {
            $question->options()->create([
                'option_text' => 'Pilihan baru',
            ]);
        }
    }

    public function updateQuestionRequired(int $questionId, mixed $isRequired): void
    {
        $question = $this->findOwnedQuestion($questionId);

        if (! $question) {
            return;
        }

        $question->update([
            'is_required' => filter_var($isRequired, FILTER_VALIDATE_BOOL),
        ]);
    }

    public function updateQuestionPublicAnalysis(int $questionId, mixed $showPublicAnalysis): void
    {
        if (! auth()->user()?->isAdmin()) {
            return;
        }

        $question = $this->findOwnedQuestion($questionId);

        if (! $question?->usesOptions()) {
            return;
        }

        $question->update([
            'show_public_analysis' => filter_var($showPublicAnalysis, FILTER_VALIDATE_BOOL),
        ]);

        Notification::make()
            ->title($question->show_public_analysis
                ? 'Analisis ditampilkan di landing page'
                : 'Analisis disembunyikan dari landing page')
            ->success()
            ->send();
    }

    public function updateQuestionOrder(int $questionId, mixed $orderNumber): void
    {
        $question = $this->findOwnedQuestion($questionId);
        $orderNumber = max(1, (int) $orderNumber);

        if (! $question) {
            return;
        }

        $question->update([
            'order_number' => $orderNumber,
        ]);
    }

    public function duplicateQuestionFromCard(int $questionId): void
    {
        $question = $this->findOwnedQuestion($questionId);

        if ($question) {
            $this->duplicateQuestion($question);
        }
    }

    public function deleteQuestionFromCard(int $questionId): void
    {
        $question = $this->findOwnedQuestion($questionId);

        if (! $question) {
            return;
        }

        $question->delete();

        Notification::make()
            ->title('Pertanyaan berhasil dihapus')
            ->success()
            ->send();
    }

    public function addQuestionOption(int $questionId): void
    {
        $question = $this->findOwnedQuestion($questionId);

        if (! $question?->usesOptions()) {
            return;
        }

        $question->options()->create([
            'option_text' => 'Pilihan baru',
        ]);

        Notification::make()
            ->title('Pilihan jawaban ditambahkan')
            ->success()
            ->send();
    }

    public function updateQuestionOption(int $optionId, ?string $optionText): void
    {
        $option = $this->findOwnedOption($optionId);

        if (! $option) {
            return;
        }

        $optionText = trim((string) $optionText);

        if ($optionText === '') {
            Notification::make()
                ->title('Opsi tidak boleh kosong')
                ->warning()
                ->send();

            return;
        }

        $option->update([
            'option_text' => $optionText,
        ]);
    }

    public function deleteQuestionOption(int $optionId): void
    {
        $option = $this->findOwnedOption($optionId);

        if (! $option) {
            return;
        }

        if ($option->question->options()->count() <= 1) {
            Notification::make()
                ->title('Minimal harus ada satu pilihan')
                ->warning()
                ->send();

            return;
        }

        $option->delete();

        Notification::make()
            ->title('Pilihan jawaban dihapus')
            ->success()
            ->send();
    }

    private function findOwnedQuestion(int $questionId): ?Question
    {
        return $this->getOwnerRecord()
            ->questions()
            ->whereKey($questionId)
            ->first();
    }

    private function findOwnedOption(int $optionId): ?QuestionOption
    {
        return QuestionOption::query()
            ->whereKey($optionId)
            ->whereHas('question', fn (Builder $query): Builder => $query
                ->where('questionnaire_id', $this->getOwnerRecord()->getKey()))
            ->with('question')
            ->first();
    }

    private function duplicateQuestion(Question $record): null
    {
        $record->loadMissing('options');

        DB::transaction(function () use ($record): void {
            $copy = $this->getOwnerRecord()->questions()->create([
                'question_text' => "{$record->question_text} (Salinan)",
                'question_type' => $record->question_type,
                'is_required' => $record->is_required,
                'show_public_analysis' => $record->show_public_analysis,
                'order_number' => ((int) $this->getOwnerRecord()->questions()->max('order_number')) + 1,
            ]);

            foreach ($record->options as $option) {
                $copy->options()->create([
                    'option_text' => $option->option_text,
                ]);
            }
        });

        Notification::make()
            ->title('Pertanyaan berhasil diduplikat')
            ->success()
            ->send();

        return null;
    }
}
