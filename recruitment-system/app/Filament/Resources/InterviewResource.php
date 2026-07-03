<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InterviewResource\Pages;
use App\Models\Interview;
use App\Models\InterviewFeedback;
use App\Services\Interview\InterviewService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;

class InterviewResource extends Resource
{
    protected static ?string $model = Interview::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Recruitment';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Interview Details')
                    ->schema([
                        Forms\Components\Select::make('application_id')
                            ->relationship('application', 'application_number')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('round_type')
                            ->options(Interview::ROUND_TYPES)
                            ->required()
                            ->native(false),

                        Forms\Components\Select::make('mode')
                            ->options(Interview::MODES)
                            ->required()
                            ->native(false)
                            ->reactive(),

                        Forms\Components\Select::make('video_platform')
                            ->options(Interview::VIDEO_PLATFORMS)
                            ->native(false)
                            ->visible(fn ($get) => $get('mode') === 'video_call')
                            ->nullable(),

                        Forms\Components\TextInput::make('meeting_link')
                            ->url()
                            ->visible(fn ($get) => $get('mode') === 'video_call')
                            ->nullable(),

                        Forms\Components\DatePicker::make('scheduled_date')
                            ->required()
                            ->native(false)
                            ->minDate(now()),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TimePicker::make('start_time')
                                    ->required()
                                    ->native(false),

                                Forms\Components\TimePicker::make('end_time')
                                    ->required()
                                    ->native(false)
                                    ->after('start_time'),
                            ]),

                        Forms\Components\TextInput::make('duration_minutes')
                            ->numeric()
                            ->default(60)
                            ->suffix('minutes'),

                        Forms\Components\Select::make('location_id')
                            ->relationship('location', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Forms\Components\TextInput::make('interview_address')
                            ->maxLength(500)
                            ->visible(fn ($get) => $get('mode') === 'in_person')
                            ->nullable(),

                        Forms\Components\TextInput::make('room_number')
                            ->maxLength(50)
                            ->visible(fn ($get) => $get('mode') === 'in_person')
                            ->nullable(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Interviewers')
                    ->schema([
                        Forms\Components\Select::make('interviewer_ids')
                            ->relationship('interviewers', 'first_name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Interviewers')
                            ->helperText('First selected interviewer will be marked as primary'),
                    ]),

                Forms\Components\Section::make('Instructions')
                    ->schema([
                        Forms\Components\RichEditor::make('instructions')
                            ->placeholder('Instructions for the candidate...')
                            ->columnSpanFull(),

                        Forms\Components\RichEditor::make('description')
                            ->placeholder('Internal notes about the interview...')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('candidate.full_name')
                    ->searchable()
                    ->sortable()
                    ->weight('font-medium'),

                Tables\Columns\TextColumn::make('jobPosting.title')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('display_type')
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('round_number')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('scheduled_date')
                    ->date('M d, Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('formatted_time'),

                Tables\Columns\TextColumn::make('display_mode')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('interviewers')
                    ->formatStateUsing(function ($record) {
                        return $record->interviewers->map(fn ($i) => $i->full_name)->join(', ');
                    })
                    ->limit(30),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'scheduled' => 'primary',
                        'confirmed' => 'success',
                        'in_progress' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        'no_show' => 'neutral',
                        'rescheduled' => 'purple',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('scheduledBy.full_name')
                    ->toggleable()
                    ->sortable(),
            ])
            ->defaultSort('scheduled_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(Interview::STATUSES)
                    ->multiple()
                    ->native(false),

                Tables\Filters\SelectFilter::make('round_type')
                    ->options(Interview::ROUND_TYPES)
                    ->multiple()
                    ->native(false)
                    ->label('Round Type'),

                Tables\Filters\SelectFilter::make('mode')
                    ->options(Interview::MODES)
                    ->native(false),

                Tables\Filters\Filter::make('scheduled_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')->native(false),
                        Forms\Components\DatePicker::make('until')->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn (Builder $query, $date): Builder => $query->whereDate('scheduled_date', '>=', $date))
                            ->when($data['until'], fn (Builder $query, $date): Builder => $query->whereDate('scheduled_date', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),

                    Tables\Actions\Action::make('complete')
                        ->icon('heroicon-m-check')
                        ->color('success')
                        ->visible(fn (Interview $record): bool => in_array($record->status, ['scheduled', 'confirmed', 'in_progress']))
                        ->requiresConfirmation()
                        ->action(function (Interview $record) {
                            app(InterviewService::class)->completeInterview($record, auth()->id());
                            Notification::make()->title('Interview marked as completed')->success()->send();
                        }),

                    Tables\Actions\Action::make('cancel')
                        ->icon('heroicon-m-x-mark')
                        ->color('danger')
                        ->visible(fn (Interview $record): bool => in_array($record->status, ['scheduled', 'confirmed']))
                        ->form([
                            Forms\Components\Textarea::make('reason')
                                ->required()
                                ->label('Cancellation Reason'),
                        ])
                        ->action(function (Interview $record, array $data) {
                            app(InterviewService::class)->cancelInterview($record, $data['reason'], auth()->id());
                            Notification::make()->title('Interview cancelled')->success()->send();
                        }),

                    Tables\Actions\Action::make('feedback')
                        ->icon('heroicon-m-clipboard-document-check')
                        ->visible(fn (Interview $record): bool => $record->status === 'completed')
                        ->modalWidth('4xl')
                        ->mountUsing(function (Forms\ComponentContainer $form, Interview $record) {
                            $feedback = $record->feedbacks()
                                ->where('interviewer_id', auth()->id())
                                ->first();

                            if ($feedback) {
                                $form->fill($feedback->toArray());
                            } else {
                                $form->fill([
                                    'is_submitted' => false,
                                    'is_confidential' => false,
                                    'technical_skills_rating' => null,
                                    'communication_rating' => null,
                                    'problem_solving_rating' => null,
                                    'cultural_fit_rating' => null,
                                    'experience_rating' => null,
                                    'overall_rating' => null,
                                ]);
                            }
                        })
                        ->form([
                            Forms\Components\Grid::make(3)
                                ->schema([
                                    Forms\Components\Select::make('technical_skills_rating')
                                        ->label('Technical Skills')
                                        ->options(InterviewFeedback::RATING_LABELS)
                                        ->required()
                                        ->disabled(fn (Interview $record) => $record->feedbacks()->where('interviewer_id', auth()->id())->where('is_submitted', true)->exists()),
                                    Forms\Components\Select::make('communication_rating')
                                        ->label('Communication')
                                        ->options(InterviewFeedback::RATING_LABELS)
                                        ->required()
                                        ->disabled(fn (Interview $record) => $record->feedbacks()->where('interviewer_id', auth()->id())->where('is_submitted', true)->exists()),
                                    Forms\Components\Select::make('problem_solving_rating')
                                        ->label('Problem Solving')
                                        ->options(InterviewFeedback::RATING_LABELS)
                                        ->required()
                                        ->disabled(fn (Interview $record) => $record->feedbacks()->where('interviewer_id', auth()->id())->where('is_submitted', true)->exists()),
                                    Forms\Components\Select::make('cultural_fit_rating')
                                        ->label('Cultural Fit')
                                        ->options(InterviewFeedback::RATING_LABELS)
                                        ->required()
                                        ->disabled(fn (Interview $record) => $record->feedbacks()->where('interviewer_id', auth()->id())->where('is_submitted', true)->exists()),
                                    Forms\Components\Select::make('experience_rating')
                                        ->label('Experience')
                                        ->options(InterviewFeedback::RATING_LABELS)
                                        ->required()
                                        ->disabled(fn (Interview $record) => $record->feedbacks()->where('interviewer_id', auth()->id())->where('is_submitted', true)->exists()),
                                    Forms\Components\Select::make('overall_rating')
                                        ->label('Overall Rating')
                                        ->options(InterviewFeedback::RATING_LABELS)
                                        ->required()
                                        ->disabled(fn (Interview $record) => $record->feedbacks()->where('interviewer_id', auth()->id())->where('is_submitted', true)->exists()),
                                ]),
                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\Textarea::make('strengths')
                                        ->label('Strengths')
                                        ->rows(3)
                                        ->disabled(fn (Interview $record) => $record->feedbacks()->where('interviewer_id', auth()->id())->where('is_submitted', true)->exists()),
                                    Forms\Components\Textarea::make('weaknesses')
                                        ->label('Weaknesses')
                                        ->rows(3)
                                        ->disabled(fn (Interview $record) => $record->feedbacks()->where('interviewer_id', auth()->id())->where('is_submitted', true)->exists()),
                                    Forms\Components\Textarea::make('questions_asked')
                                        ->label('Questions Asked')
                                        ->rows(3)
                                        ->disabled(fn (Interview $record) => $record->feedbacks()->where('interviewer_id', auth()->id())->where('is_submitted', true)->exists()),
                                    Forms\Components\Textarea::make('candidate_responses')
                                        ->label('Candidate Responses')
                                        ->rows(3)
                                        ->disabled(fn (Interview $record) => $record->feedbacks()->where('interviewer_id', auth()->id())->where('is_submitted', true)->exists()),
                                ]),
                            Forms\Components\Textarea::make('notes')
                                ->label('General Notes / Comments')
                                ->rows(3)
                                ->disabled(fn (Interview $record) => $record->feedbacks()->where('interviewer_id', auth()->id())->where('is_submitted', true)->exists()),
                            Forms\Components\Section::make('Recommendation & Submission')
                                ->schema([
                                    Forms\Components\Select::make('recommendation')
                                        ->options(InterviewFeedback::RECOMMENDATIONS)
                                        ->required()
                                        ->disabled(fn (Interview $record) => $record->feedbacks()->where('interviewer_id', auth()->id())->where('is_submitted', true)->exists()),
                                    Forms\Components\Textarea::make('recommendation_reason')
                                        ->label('Recommendation Reason')
                                        ->rows(3)
                                        ->required()
                                        ->disabled(fn (Interview $record) => $record->feedbacks()->where('interviewer_id', auth()->id())->where('is_submitted', true)->exists()),
                                    Forms\Components\Toggle::make('is_submitted')
                                        ->label('Submit Feedback (Finalize)')
                                        ->helperText('Once submitted, feedback cannot be edited and is marked as final.')
                                        ->disabled(fn (Interview $record) => $record->feedbacks()->where('interviewer_id', auth()->id())->where('is_submitted', true)->exists()),
                                    Forms\Components\Toggle::make('is_confidential')
                                        ->label('Mark as Confidential')
                                        ->helperText('Confidential feedback is only visible to HR Admins and Super Admins.')
                                        ->disabled(fn (Interview $record) => $record->feedbacks()->where('interviewer_id', auth()->id())->where('is_submitted', true)->exists()),
                                ])
                        ])
                        ->action(function (Interview $record, array $data) {
                            $feedback = $record->feedbacks()
                                ->where('interviewer_id', auth()->id())
                                ->first();

                            if ($feedback && $feedback->is_submitted) {
                                Notification::make()
                                    ->title('Feedback has already been submitted and cannot be updated.')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            if ($data['is_submitted']) {
                                $data['submitted_at'] = now();
                            }

                            if ($feedback) {
                                $feedback->update($data);
                            } else {
                                $record->feedbacks()->create(array_merge($data, [
                                    'interviewer_id' => auth()->id(),
                                    'application_id' => $record->application_id,
                                    'submitted_at' => $data['is_submitted'] ? now() : null,
                                    'is_submitted' => $data['is_submitted'] ?? false,
                                    'is_confidential' => $data['is_confidential'] ?? false,
                                ]));
                            }

                            Notification::make()
                                ->title($data['is_submitted'] ? 'Feedback submitted successfully.' : 'Feedback draft saved successfully.')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInterviews::route('/'),
            'create' => Pages\CreateInterview::route('/create'),
            'edit' => Pages\EditInterview::route('/{record}/edit'),
            // 'feedback' => Pages\InterviewFeedback::route('/{record}/feedback'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['candidate', 'jobPosting', 'interviewers', 'scheduledBy']);
    }
}
