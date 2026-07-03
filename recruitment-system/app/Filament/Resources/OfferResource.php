<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OfferResource\Pages;
use App\Models\Offer;
use App\Services\Offer\OfferService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;

class OfferResource extends Resource
{
    protected static ?string $model = Offer::class;
    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    protected static ?string $navigationGroup = 'Recruitment';
    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Offer Details')
                    ->schema([
                        Forms\Components\Select::make('application_id')
                            ->relationship('application', 'application_number')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('department_id')
                            ->relationship('department', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('designation_id')
                            ->relationship('designation', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('location_id')
                            ->relationship('location', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('reporting_manager_id')
                            ->relationship('reportingManager', 'first_name')
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Forms\Components\TextInput::make('proposed_designation')
                            ->required()
                            ->maxLength(150),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Compensation')
                    ->schema([
                        Forms\Components\TextInput::make('basic_salary')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->live()
                            ->afterStateUpdated(fn ($state, $set, $get) =>
                                $set('total_ctc',
                                    ($state ?? 0) +
                                    ($get('housing_allowance') ?? 0) +
                                    ($get('transport_allowance') ?? 0) +
                                    ($get('medical_allowance') ?? 0) +
                                    ($get('other_allowances') ?? 0)
                                )
                            ),

                        Forms\Components\Grid::make(4)
                            ->schema([
                                Forms\Components\TextInput::make('housing_allowance')
                                    ->numeric()
                                    ->default(0)
                                    ->prefix('$')
                                    ->live(),

                                Forms\Components\TextInput::make('transport_allowance')
                                    ->numeric()
                                    ->default(0)
                                    ->prefix('$')
                                    ->live(),

                                Forms\Components\TextInput::make('medical_allowance')
                                    ->numeric()
                                    ->default(0)
                                    ->prefix('$')
                                    ->live(),

                                Forms\Components\TextInput::make('other_allowances')
                                    ->numeric()
                                    ->default(0)
                                    ->prefix('$')
                                    ->live(),
                            ]),

                        Forms\Components\TextInput::make('bonus_percentage')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('%')
                            ->nullable(),

                        Forms\Components\TextInput::make('total_ctc')
                            ->numeric()
                            ->prefix('$')
                            ->disabled()
                            ->dehydrated(),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('salary_currency')
                                    ->options([
                                        'USD' => 'USD - US Dollar',
                                        'EUR' => 'EUR - Euro',
                                        'GBP' => 'GBP - British Pound',
                                    ])
                                    ->default('USD')
                                    ->native(false),

                                Forms\Components\Select::make('salary_period')
                                    ->options([
                                        'monthly' => 'Monthly',
                                        'yearly' => 'Yearly',
                                    ])
                                    ->default('yearly')
                                    ->native(false),
                            ]),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Dates')
                    ->schema([
                        Forms\Components\DatePicker::make('joining_date')
                            ->required()
                            ->native(false),

                        Forms\Components\DatePicker::make('offer_expiry_date')
                            ->required()
                            ->native(false)
                            ->default(now()->addDays(7)),

                        Forms\Components\DatePicker::make('proposed_joining_date')
                            ->native(false)
                            ->nullable(),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Additional')
                    ->schema([
                        Forms\Components\Textarea::make('special_conditions')
                            ->rows(3)
                            ->nullable()
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('notes')
                            ->rows(3)
                            ->nullable()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('offer_number')
                    ->searchable()
                    ->sortable()
                    ->weight('font-bold'),

                Tables\Columns\TextColumn::make('candidate.full_name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('jobPosting.title')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('proposed_designation')
                    ->searchable()
                    ->limit(25),

                Tables\Columns\TextColumn::make('total_ctc')
                    ->money('USD')
                    ->sortable()
                    ->weight('font-bold'),

                Tables\Columns\TextColumn::make('version')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'sent' => 'primary',
                        'accepted' => 'success',
                        'rejected' => 'danger',
                        'expired' => 'warning',
                        'negotiating' => 'info',
                        'withdrawn' => 'neutral',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('joining_date')
                    ->date('M d, Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('offer_expiry_date')
                    ->date('M d, Y')
                    ->sortable()
                    ->color(fn (string $state, Offer $record): string =>
                        $record->is_expired ? 'danger' : 'success'
                    ),

                Tables\Columns\TextColumn::make('sent_at')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('createdBy.full_name')
                    ->toggleable()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(Offer::STATUSES)
                    ->multiple()
                    ->native(false),

                Tables\Filters\SelectFilter::make('department_id')
                    ->relationship('department', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),

                    Tables\Actions\Action::make('send')
                        ->icon('heroicon-m-paper-airplane')
                        ->color('primary')
                        ->visible(fn (Offer $record): bool => $record->status === 'draft')
                        ->requiresConfirmation()
                        ->action(function (Offer $record) {
                            app(OfferService::class)->sendOffer($record, auth()->id());
                            Notification::make()->title('Offer sent successfully')->success()->send();
                        }),

                    Tables\Actions\Action::make('accept')
                        ->icon('heroicon-m-hand-thumb-up')
                        ->color('success')
                        ->visible(fn (Offer $record): bool => $record->status === 'sent')
                        ->requiresConfirmation()
                        ->action(function (Offer $record) {
                            app(OfferService::class)->acceptOffer($record, request()->ip());
                            Notification::make()->title('Offer marked as accepted')->success()->send();
                        }),

                    Tables\Actions\Action::make('reject')
                        ->icon('heroicon-m-x-mark')
                        ->color('danger')
                        ->visible(fn (Offer $record): bool => $record->status === 'sent')
                        ->form([
                            Forms\Components\Textarea::make('reason')
                                ->required()
                                ->label('Rejection Reason'),
                        ])
                        ->action(function (Offer $record, array $data) {
                            app(OfferService::class)->rejectOffer($record, $data['reason']);
                            Notification::make()->title('Offer marked as rejected')->success()->send();
                        }),

                    Tables\Actions\Action::make('pdf')
                        ->icon('heroicon-m-document')
                        ->visible(fn (Offer $record): bool => $record->pdf_path !== null)
                        ->url(fn (Offer $record): ?string => $record->pdf_url)
                        ->openUrlInNewTab(),

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
            'index' => Pages\ListOffers::route('/'),
            'create' => Pages\CreateOffer::route('/create'),
            'view' => Pages\ViewOffer::route('/{record}'),
            'edit' => Pages\EditOffer::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['candidate', 'jobPosting', 'department', 'createdBy']);
    }
}
