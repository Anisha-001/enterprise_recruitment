<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CandidateResource\Pages;
use App\Models\Candidate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
use Illuminate\Database\Eloquent\Builder;

class CandidateResource extends Resource
{
    protected static ?string $model = Candidate::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Recruitment';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Personal Information')
                    ->schema([
                        Forms\Components\TextInput::make('first_name')->required()->maxLength(100),
                        Forms\Components\TextInput::make('middle_name')->maxLength(100)->nullable(),
                        Forms\Components\TextInput::make('last_name')->required()->maxLength(100),
                        Forms\Components\TextInput::make('email')->email()->required()->maxLength(255),
                        Forms\Components\TextInput::make('phone')->tel()->required()->maxLength(20),
                        Forms\Components\TextInput::make('alternate_phone')->tel()->maxLength(20)->nullable(),
                        Forms\Components\Select::make('gender')
                            ->options(Candidate::GENDERS)
                            ->native(false)
                            ->nullable(),
                        Forms\Components\DatePicker::make('date_of_birth')->native(false)->nullable(),
                        Forms\Components\Select::make('marital_status')
                            ->options(Candidate::MARITAL_STATUSES)
                            ->native(false)
                            ->nullable(),
                        Forms\Components\TextInput::make('nationality')->maxLength(100)->nullable(),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Current Employment')
                    ->schema([
                        Forms\Components\TextInput::make('current_company')->maxLength(150)->nullable(),
                        Forms\Components\TextInput::make('current_designation')->maxLength(150)->nullable(),
                        Forms\Components\TextInput::make('current_salary')
                            ->numeric()
                            ->prefix('$')
                            ->nullable(),
                        Forms\Components\TextInput::make('expected_salary')
                            ->numeric()
                            ->prefix('$')
                            ->nullable(),
                        Forms\Components\Select::make('notice_period')
                            ->options(Candidate::NOTICE_PERIODS)
                            ->native(false)
                            ->nullable(),
                        Forms\Components\TextInput::make('total_experience_years')
                            ->numeric()
                            ->minValue(0)
                            ->suffix('years')
                            ->nullable(),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Online Profiles')
                    ->schema([
                        Forms\Components\TextInput::make('linkedin_url')->url()->maxLength(500)->nullable()->prefixIcon('heroicon-m-link'),
                        Forms\Components\TextInput::make('github_url')->url()->maxLength(500)->nullable()->prefixIcon('heroicon-m-code-bracket'),
                        Forms\Components\TextInput::make('portfolio_url')->url()->maxLength(500)->nullable()->prefixIcon('heroicon-m-globe-alt'),
                        Forms\Components\TextInput::make('website_url')->url()->maxLength(500)->nullable()->prefixIcon('heroicon-m-globe-alt'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Notes')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('photograph')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->full_name) . '&background=0D8ABC&color=fff&size=64')
                    ->size(40),

                Tables\Columns\TextColumn::make('full_name')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable()
                    ->weight('font-bold'),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),

                Tables\Columns\TextColumn::make('current_company')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('current_designation')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('total_experience_years')
                    ->suffix(' yrs')
                    ->sortable(),

                Tables\Columns\TextColumn::make('source')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Candidate::SOURCES[$state] ?? $state)
                    ->color('info'),

                Tables\Columns\TextColumn::make('applications_count')
                    ->counts('applications')
                    ->badge()
                    ->color('primary'),

                Tables\Columns\IconColumn::make('blacklist_status')
                    ->icon(fn (string $state): string => $state === 'blacklisted' ? 'heroicon-m-no-symbol' : 'heroicon-m-check-circle')
                    ->color(fn (string $state): string => $state === 'blacklisted' ? 'danger' : 'success')
                    ->label('Status'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('source')
                    ->options(Candidate::SOURCES)
                    ->multiple()
                    ->native(false),

                Tables\Filters\SelectFilter::make('notice_period')
                    ->options(Candidate::NOTICE_PERIODS)
                    ->native(false),

                Tables\Filters\Filter::make('blacklist_status')
                    ->query(fn (Builder $query): Builder => $query->where('blacklist_status', '!=', 'blacklisted'))
                    ->label('Active Only')
                    ->default(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),

                    Tables\Actions\Action::make('view_resume')
                        ->icon('heroicon-m-document')
                        ->url(fn (Candidate $record): ?string => $record->resume_url)
                        ->openUrlInNewTab()
                        ->visible(fn (Candidate $record): bool => $record->resume_path !== null),

                    Tables\Actions\Action::make('blacklist')
                        ->icon('heroicon-m-no-symbol')
                        ->color('danger')
                        ->visible(fn (Candidate $record): bool => $record->blacklist_status !== 'blacklisted')
                        ->requiresConfirmation()
                        ->form([
                            Forms\Components\Textarea::make('reason')
                                ->required()
                                ->label('Blacklist Reason'),
                        ])
                        ->action(function (Candidate $record, array $data) {
                            $record->update([
                                'blacklist_status' => 'blacklisted',
                                'blacklist_reason' => $data['reason'],
                            ]);
                        }),

                    Tables\Actions\Action::make('remove_blacklist')
                        ->icon('heroicon-m-check-circle')
                        ->color('success')
                        ->visible(fn (Candidate $record): bool => $record->blacklist_status === 'blacklisted')
                        ->requiresConfirmation()
                        ->action(function (Candidate $record) {
                            $record->update([
                                'blacklist_status' => 'none',
                                'blacklist_reason' => null,
                            ]);
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Split::make([
                    Components\Section::make('Personal Information')
                        ->schema([
                            Components\TextEntry::make('full_name')
                                ->size(Components\TextEntry\TextEntrySize::Large)
                                ->weight('font-bold'),
                            Components\TextEntry::make('email'),
                            Components\TextEntry::make('phone'),
                            Components\TextEntry::make('date_of_birth')
                                ->date('M d, Y'),
                            Components\TextEntry::make('nationality'),
                            Components\TextEntry::make('gender')
                                ->formatStateUsing(fn ($state) => Candidate::GENDERS[$state] ?? $state),
                        ]),

                    Components\Section::make('Employment Details')
                        ->schema([
                            Components\TextEntry::make('current_company'),
                            Components\TextEntry::make('current_designation'),
                            Components\TextEntry::make('total_experience_years')
                                ->suffix(' years'),
                            Components\TextEntry::make('current_salary')
                                ->money('USD'),
                            Components\TextEntry::make('expected_salary')
                                ->money('USD'),
                            Components\TextEntry::make('notice_period')
                                ->formatStateUsing(fn ($state) => Candidate::NOTICE_PERIODS[$state] ?? $state),
                        ]),
                ])->from('md'),

                Components\Section::make('Online Profiles')
                    ->schema([
                        Components\TextEntry::make('linkedin_url')
                            ->icon('heroicon-m-link')
                            ->url(fn ($state) => $state, true)
                            ->visible(fn ($record) => $record->linkedin_url !== null),
                        Components\TextEntry::make('github_url')
                            ->icon('heroicon-m-code-bracket')
                            ->url(fn ($state) => $state, true)
                            ->visible(fn ($record) => $record->github_url !== null),
                        Components\TextEntry::make('portfolio_url')
                            ->icon('heroicon-m-globe-alt')
                            ->url(fn ($state) => $state, true)
                            ->visible(fn ($record) => $record->portfolio_url !== null),
                    ])
                    ->columns(3),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // \App\Filament\Resources\CandidateResource\RelationManagers\ApplicationsRelationManager::class,
            // \App\Filament\Resources\CandidateResource\RelationManagers\EducationRelationManager::class,
            // \App\Filament\Resources\CandidateResource\RelationManagers\ExperiencesRelationManager::class,
            // \App\Filament\Resources\CandidateResource\RelationManagers\SkillsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCandidates::route('/'),
            'create' => Pages\CreateCandidate::route('/create'),
            'view' => Pages\ViewCandidate::route('/{record}'),
            'edit' => Pages\EditCandidate::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['applications']);
    }
}
