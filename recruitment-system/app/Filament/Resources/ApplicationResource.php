<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApplicationResource\Pages;
use App\Filament\Resources\ApplicationResource\RelationManagers;
use App\Models\Application;
use App\Services\Application\ApplicationService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ApplicationResource extends Resource
{
    protected static ?string $model = Application::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Recruitment';
    protected static ?int $navigationSort = 3;
    protected static ?string $recordTitleAttribute = 'application_number';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Application Details')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options(Application::STATUSES)
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, $record, $set) {
                                if ($state === 'rejected') {
                                    $set('show_rejection', true);
                                }
                            }),

                        Forms\Components\Select::make('rejection_reason')
                            ->options(Application::REJECTION_REASONS)
                            ->visible(fn ($get) => $get('status') === 'rejected')
                            ->nullable(),

                        Forms\Components\Textarea::make('rejection_notes')
                            ->visible(fn ($get) => $get('status') === 'rejected')
                            ->nullable()
                            ->columnSpanFull(),

                        Forms\Components\Select::make('recruiter_id')
                            ->relationship('recruiter', 'first_name', fn ($q) => $q->selectRaw("id, CONCAT(first_name, ' ', last_name) as full_name"))
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->full_name)
                            ->searchable()
                            ->preload()
                            ->label('Assigned Recruiter'),

                        Forms\Components\TextInput::make('rating')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(5)
                            ->suffix('/ 5')
                            ->nullable(),

                        Forms\Components\Textarea::make('screening_notes')
                            ->nullable()
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('internal_notes')
                            ->nullable()
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('recruiter_notes')
                            ->nullable()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('application_number')
                    ->searchable()
                    ->sortable()
                    ->weight('font-bold')
                    ->copyable(),

                Tables\Columns\ImageColumn::make('candidate.photograph')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->candidate->full_name) . '&background=0D8ABC&color=fff')
                    ->size(40),

                Tables\Columns\TextColumn::make('candidate.full_name')
                    ->searchable(['candidate.first_name', 'candidate.last_name', 'candidate.email'])
                    ->sortable()
                    ->weight('font-medium'),

                Tables\Columns\TextColumn::make('jobPosting.title')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('jobPosting.department.name')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('candidate.total_experience_years')
                    ->label('Exp.')
                    ->suffix(' yrs')
                    ->sortable(),

                Tables\Columns\TextColumn::make('candidate.expected_salary')
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('candidate.notice_period')
                    ->formatStateUsing(fn (string $state): string => \App\Models\Candidate::NOTICE_PERIODS[$state] ?? $state),

                Tables\Columns\TextColumn::make('candidate.current_company')
                    ->searchable()
                    ->toggleable()
                    ->limit(20),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'gray',
                        'screening' => 'primary',
                        'shortlisted' => 'info',
                        'technical_interview', 'manager_interview', 'final_interview' => 'warning',
                        'offer_pending', 'offer_sent' => 'amber',
                        'offer_accepted' => 'success',
                        'hired' => 'success',
                        'rejected', 'offer_rejected' => 'danger',
                        'withdrawn' => 'neutral',
                        'on_hold' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => Application::STATUSES[$state] ?? $state)
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_new')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-check')
                    ->trueColor('warning')
                    ->label('New'),

                Tables\Columns\TextColumn::make('recruiter.full_name')
                    ->toggleable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(Application::STATUSES)
                    ->multiple()
                    ->native(false),

                Tables\Filters\SelectFilter::make('job_posting_id')
                    ->relationship('jobPosting', 'title')
                    ->searchable()
                    ->preload()
                    ->label('Job Posting'),

                Tables\Filters\SelectFilter::make('recruiter_id')
                    ->relationship('recruiter', 'first_name')
                    ->searchable()
                    ->preload()
                    ->label('Recruiter'),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date))
                            ->when($data['until'], fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->icon('heroicon-m-eye')
                    ->url(fn (Application $record): string => static::getUrl('view', ['record' => $record])),

                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('move_to_screening')
                        ->icon('heroicon-m-arrow-right')
                        ->visible(fn (Application $record) => $record->canTransitionTo('screening'))
                        ->requiresConfirmation()
                        ->action(function (Application $record) {
                            app(ApplicationService::class)->transitionStatus($record, 'screening', null, auth()->id());
                            Notification::make()->title('Application moved to screening')->success()->send();
                        }),

                    Tables\Actions\Action::make('shortlist')
                        ->icon('heroicon-m-check-circle')
                        ->visible(fn (Application $record) => $record->canTransitionTo('shortlisted'))
                        ->requiresConfirmation()
                        ->action(function (Application $record) {
                            app(ApplicationService::class)->transitionStatus($record, 'shortlisted', null, auth()->id());
                            Notification::make()->title('Application shortlisted')->success()->send();
                        }),

                    Tables\Actions\Action::make('schedule_interview')
                        ->icon('heroicon-m-calendar')
                        ->visible(fn (Application $record) => $record->canTransitionTo('technical_interview'))
                        ->url(fn (Application $record): string => InterviewResource::getUrl('create', [
                            'application_id' => $record->id,
                        ])),

                    Tables\Actions\Action::make('create_offer')
                        ->icon('heroicon-m-document-text')
                        ->visible(fn (Application $record) => $record->canTransitionTo('offer_pending'))
                        ->url(fn (Application $record): string => OfferResource::getUrl('create', [
                            'application_id' => $record->id,
                        ])),

                    Tables\Actions\Action::make('reject')
                        ->icon('heroicon-m-x-circle')
                        ->color('danger')
                        ->visible(fn (Application $record) => $record->canTransitionTo('rejected'))
                        ->form([
                            Forms\Components\Select::make('reason')
                                ->options(Application::REJECTION_REASONS)
                                ->required(),
                            Forms\Components\Textarea::make('notes')
                                ->nullable(),
                        ])
                        ->action(function (Application $record, array $data) {
                            app(ApplicationService::class)->transitionStatus(
                                $record, 'rejected', $data['notes'], auth()->id()
                            );
                            $record->update(['rejection_reason' => $data['reason']]);
                            Notification::make()->title('Application rejected')->success()->send();
                        }),
                ])->label('Actions'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('assign_recruiter')
                        ->icon('heroicon-m-user')
                        ->form([
                            Forms\Components\Select::make('recruiter_id')
                                ->relationship('recruiter', 'first_name')
                                ->required()
                                ->label('Recruiter'),
                        ])
                        ->action(function ($records, array $data) {
                            foreach ($records as $record) {
                                app(ApplicationService::class)->assignRecruiter($record, $data['recruiter_id'], auth()->id());
                            }
                            Notification::make()->title('Recruiter assigned to ' . $records->count() . ' applications')->success()->send();
                        }),

                    Tables\Actions\BulkAction::make('change_status')
                        ->icon('heroicon-m-arrow-path')
                        ->form([
                            Forms\Components\Select::make('status')
                                ->options(Application::STATUSES)
                                ->required(),
                        ])
                        ->action(function ($records, array $data) {
                            $service = app(ApplicationService::class);
                            foreach ($records as $record) {
                                if ($record->canTransitionTo($data['status'])) {
                                    $service->transitionStatus($record, $data['status'], null, auth()->id());
                                }
                            }
                            Notification::make()->title('Status updated')->success()->send();
                        }),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No applications yet')
            ->emptyStateDescription('Applications will appear here when candidates apply.');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make('Candidate Information')
                    ->schema([
                        Components\TextEntry::make('candidate.full_name')
                            ->label('Name')
                            ->size(Components\TextEntry\TextEntrySize::Large)
                            ->weight('font-bold'),
                        Components\TextEntry::make('candidate.email'),
                        Components\TextEntry::make('candidate.phone'),
                        Components\TextEntry::make('candidate.current_company'),
                        Components\TextEntry::make('candidate.current_designation'),
                        Components\TextEntry::make('candidate.total_experience_years')
                            ->suffix(' years'),
                        Components\TextEntry::make('candidate.notice_period')
                            ->formatStateUsing(fn ($state) => \App\Models\Candidate::NOTICE_PERIODS[$state] ?? $state),
                    ])
                    ->columns(3),

                Components\Section::make('Application Details')
                    ->schema([
                        Components\TextEntry::make('application_number'),
                        Components\TextEntry::make('jobPosting.title'),
                        Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'new' => 'gray',
                                'screening' => 'primary',
                                'shortlisted' => 'info',
                                'hired' => 'success',
                                'rejected' => 'danger',
                                default => 'warning',
                            }),
                        Components\TextEntry::make('expected_salary')
                            ->money('USD'),
                        Components\TextEntry::make('created_at')
                            ->dateTime('M d, Y g:i A'),
                        Components\TextEntry::make('recruiter.full_name')
                            ->label('Recruiter'),
                    ])
                    ->columns(3),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // RelationManagers\ActivitiesRelationManager::class,
            // RelationManagers\InterviewsRelationManager::class,
            // RelationManagers\NotesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApplications::route('/'),
            'view' => Pages\ViewApplication::route('/{record}'),
            'edit' => Pages\EditApplication::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['candidate', 'jobPosting.department', 'recruiter']);
    }
}
