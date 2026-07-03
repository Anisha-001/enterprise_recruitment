<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JobResource\Pages;
use App\Models\JobPosting;
use App\Services\Job\JobService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class JobResource extends Resource
{
    protected static ?string $model = JobPosting::class;
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationGroup = 'Recruitment';
    protected static ?int $navigationSort = 2;
    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Job Details')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Basic Information')
                            ->icon('heroicon-m-information-circle')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->required()
                                    ->maxLength(200)
                                    ->placeholder('e.g., Senior Software Engineer'),

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

                                Forms\Components\Select::make('employment_type')
                                    ->options(JobPosting::EMPLOYMENT_TYPES)
                                    ->required()
                                    ->native(false),

                                Forms\Components\Select::make('experience_level')
                                    ->options(JobPosting::EXPERIENCE_LEVELS)
                                    ->required()
                                    ->native(false),

                                Forms\Components\Select::make('work_arrangement')
                                    ->options(JobPosting::WORK_ARRANGEMENTS)
                                    ->required()
                                    ->native(false),

                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('min_experience_years')
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(50)
                                            ->suffix('years'),

                                        Forms\Components\TextInput::make('max_experience_years')
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(50)
                                            ->suffix('years')
                                            ->nullable(),
                                    ]),

                                Forms\Components\TextInput::make('vacancies')
                                    ->numeric()
                                    ->required()
                                    ->minValue(1)
                                    ->default(1),

                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('min_salary')
                                            ->numeric()
                                            ->prefix('$')
                                            ->nullable(),

                                        Forms\Components\TextInput::make('max_salary')
                                            ->numeric()
                                            ->prefix('$')
                                            ->nullable(),

                                        Forms\Components\Select::make('salary_period')
                                            ->options(JobPosting::SALARY_PERIODS)
                                            ->default('yearly')
                                            ->native(false),
                                    ]),

                                Forms\Components\Toggle::make('show_salary')
                                    ->label('Display salary on job posting')
                                    ->default(false),

                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\DatePicker::make('published_at')
                                            ->label('Publish Date')
                                            ->native(false)
                                            ->nullable(),

                                        Forms\Components\DatePicker::make('closing_date')
                                            ->label('Closing Date')
                                            ->native(false)
                                            ->nullable(),
                                    ]),
                            ])
                            ->columns(2),

                        Forms\Components\Tabs\Tab::make('Description')
                            ->icon('heroicon-m-document-text')
                            ->schema([
                                Forms\Components\Textarea::make('summary')
                                    ->rows(3)
                                    ->maxLength(500)
                                    ->placeholder('Brief summary of the role...')
                                    ->columnSpanFull(),

                                Forms\Components\RichEditor::make('description')
                                    ->required()
                                    ->placeholder('Detailed job description...')
                                    ->columnSpanFull(),

                                Forms\Components\RichEditor::make('responsibilities')
                                    ->placeholder('Key responsibilities...')
                                    ->columnSpanFull(),

                                Forms\Components\RichEditor::make('requirements')
                                    ->placeholder('Requirements and qualifications...')
                                    ->columnSpanFull(),

                                Forms\Components\RichEditor::make('benefits')
                                    ->placeholder('Benefits and perks...')
                                    ->columnSpanFull(),
                            ]),

                        Forms\Components\Tabs\Tab::make('Skills')
                            ->icon('heroicon-m-wrench-screwdriver')
                            ->schema([
                                Forms\Components\Repeater::make('skills')
                                    ->relationship('skills')
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->datalist(fn () => \App\Models\Skill::pluck('name')->toArray()),

                                        Forms\Components\Select::make('proficiency')
                                            ->options([
                                                'beginner' => 'Beginner',
                                                'intermediate' => 'Intermediate',
                                                'advanced' => 'Advanced',
                                                'expert' => 'Expert',
                                            ])
                                            ->default('intermediate')
                                            ->native(false),

                                        Forms\Components\Toggle::make('is_required')
                                            ->label('Required')
                                            ->default(true),

                                        Forms\Components\TextInput::make('years_experience')
                                            ->numeric()
                                            ->minValue(0)
                                            ->suffix('years')
                                            ->nullable(),
                                    ])
                                    ->columns(4)
                                    ->addActionLabel('Add Skill')
                                    ->reorderable()
                                    ->collapsible()
                                    ->saveRelationshipsUsing(function (\Illuminate\Database\Eloquent\Model $record, array $state) {
                                        $syncData = [];
                                        foreach ($state as $index => $item) {
                                            $name = trim($item['name'] ?? '');
                                            if (empty($name)) {
                                                continue;
                                            }

                                            // Find existing skill case-insensitively, or create a new one
                                            $skill = \App\Models\Skill::whereRaw('lower(name) = ?', [strtolower($name)])->first();

                                            if (!$skill) {
                                                $skill = \App\Models\Skill::create([
                                                    'name' => $name,
                                                    'slug' => \Illuminate\Support\Str::slug($name),
                                                    'category' => 'technical', // default category
                                                    'is_active' => true,
                                                ]);
                                            }

                                            $syncData[$skill->id] = [
                                                'proficiency' => $item['proficiency'] ?? 'intermediate',
                                                'is_required' => $item['is_required'] ?? true,
                                                'years_experience' => $item['years_experience'] ?? null,
                                                'sort_order' => $index,
                                            ];
                                        }

                                        $record->skills()->sync($syncData);
                                    }),
                            ]),

                        Forms\Components\Tabs\Tab::make('SEO & Settings')
                            ->icon('heroicon-m-cog')
                            ->schema([
                                Forms\Components\TextInput::make('meta_title')
                                    ->maxLength(200)
                                    ->placeholder('SEO title...'),

                                Forms\Components\Textarea::make('meta_description')
                                    ->rows(2)
                                    ->maxLength(500)
                                    ->placeholder('SEO description...'),

                                Forms\Components\TextInput::make('meta_keywords')
                                    ->maxLength(500)
                                    ->placeholder('keyword1, keyword2, keyword3'),

                                Forms\Components\Section::make('Visibility')
                                    ->schema([
                                        Forms\Components\Toggle::make('is_featured')
                                            ->label('Featured Job'),

                                        Forms\Components\Toggle::make('is_urgent')
                                            ->label('Urgent Hiring'),
                                    ])
                                    ->columns(2),

                                Forms\Components\Select::make('status')
                                    ->options(JobPosting::STATUSES)
                                    ->required()
                                    ->default('draft')
                                    ->native(false),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('font-bold')
                    ->description(fn (JobPosting $record): string => $record->department?->name ?? ''),

                Tables\Columns\TextColumn::make('department.name')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                Tables\Columns\TextColumn::make('location.name')
                    ->toggleable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('employment_type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => JobPosting::EMPLOYMENT_TYPES[$state] ?? $state)
                    ->color('info'),

                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean()
                    ->trueIcon('heroicon-m-star')
                    ->falseIcon('heroicon-m-x-mark')
                    ->trueColor('warning')
                    ->label('Featured'),

                Tables\Columns\IconColumn::make('is_urgent')
                    ->boolean()
                    ->trueIcon('heroicon-m-bolt')
                    ->falseIcon('heroicon-m-x-mark')
                    ->trueColor('danger')
                    ->label('Urgent'),

                Tables\Columns\TextColumn::make('applications_count')
                    ->counts('applications')
                    ->label('Apps')
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'draft' => 'gray',
                        'closed' => 'danger',
                        'archived' => 'neutral',
                        'on_hold' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('published_at')
                    ->date('M d, Y')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('closing_date')
                    ->date('M d, Y')
                    ->sortable()
                    ->toggleable()
                    ->color(fn (?string $state, JobPosting $record): string =>
                        $record->closing_date && $record->closing_date->isPast() ? 'danger' : 'success'
                    ),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(JobPosting::STATUSES)
                    ->multiple()
                    ->native(false),

                Tables\Filters\SelectFilter::make('department_id')
                    ->relationship('department', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Department'),

                Tables\Filters\SelectFilter::make('employment_type')
                    ->options(JobPosting::EMPLOYMENT_TYPES)
                    ->multiple()
                    ->native(false),

                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Featured'),

                Tables\Filters\TernaryFilter::make('is_urgent')
                    ->label('Urgent'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),

                    Tables\Actions\Action::make('publish')
                        ->icon('heroicon-m-globe-alt')
                        ->color('success')
                        ->visible(fn (JobPosting $record): bool => $record->status === 'draft')
                        ->requiresConfirmation()
                        ->action(function (JobPosting $record) {
                            app(JobService::class)->publishJob($record, auth()->id());
                            Notification::make()->title('Job published successfully')->success()->send();
                        }),

                    Tables\Actions\Action::make('close')
                        ->icon('heroicon-m-lock-closed')
                        ->color('danger')
                        ->visible(fn (JobPosting $record): bool => $record->status === 'published')
                        ->requiresConfirmation()
                        ->action(function (JobPosting $record) {
                            app(JobService::class)->closeJob($record, auth()->id());
                            Notification::make()->title('Job closed successfully')->success()->send();
                        }),

                    Tables\Actions\Action::make('clone')
                        ->icon('heroicon-m-document-duplicate')
                        ->color('info')
                        ->action(function (JobPosting $record) {
                            $newJob = app(JobService::class)->cloneJob($record, auth()->id());
                            Notification::make()->title('Job cloned successfully')->success()->send();
                            return redirect(static::getUrl('edit', ['record' => $newJob]));
                        }),

                    Tables\Actions\Action::make('preview')
                        ->icon('heroicon-m-eye')
                        ->url(fn (JobPosting $record): string => route('careers.jobs.show', $record))
                        ->openUrlInNewTab(),

                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('publish')
                        ->icon('heroicon-m-globe-alt')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                if ($record->status === 'draft') {
                                    app(JobService::class)->publishJob($record, auth()->id());
                                }
                            }
                            Notification::make()->title('Selected jobs published')->success()->send();
                        }),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJobs::route('/'),
            'create' => Pages\CreateJob::route('/create'),
            'view' => Pages\ViewJob::route('/{record}'),
            'edit' => Pages\EditJob::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['department', 'location', 'applications']);
    }
}
