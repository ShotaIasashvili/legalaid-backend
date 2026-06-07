<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VacancyResource\Pages;
use App\Models\Vacancy;
use App\Services\AdminDashboardMetrics;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class VacancyResource extends Resource
{
    protected static ?string $model = Vacancy::class;
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationLabel = 'ვაკანსიები / Vacancies';
    protected static ?string $navigationGroup = 'ორგანიზაცია';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make(3)->schema([

                // ── Main column (2/3) ─────────────────────────────────────────
                Forms\Components\Group::make()->columnSpan(2)->schema([

                    Forms\Components\Section::make('ვაკანსია')
                        ->icon('heroicon-o-document-text')
                        ->schema([
                            Forms\Components\TextInput::make('title')
                                ->label('სათაური')
                                ->required()
                                ->maxLength(400)
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn ($state, Forms\Set $set) =>
                                    $set('slug', Str::slug($state)))
                                ->columnSpanFull(),

                            Forms\Components\TextInput::make('slug')
                                ->label('Slug')
                                ->required()
                                ->unique(Vacancy::class, 'slug', ignoreRecord: true)
                                ->maxLength(400)
                                ->columnSpanFull(),

                            Forms\Components\Textarea::make('excerpt')
                                ->label('მოკლე აღწერა')
                                ->rows(2)
                                ->maxLength(500)
                                ->columnSpanFull(),

                            Forms\Components\RichEditor::make('content')
                                ->label('სრული შინაარსი')
                                ->toolbarButtons([
                                    'bold', 'italic', 'underline', 'strike',
                                    'link', 'bulletList', 'orderedList',
                                    'h2', 'h3', 'blockquote',
                                    'redo', 'undo',
                                ])
                                ->required()
                                ->columnSpanFull(),
                        ]),

                    Forms\Components\Section::make('მოთხოვნები')
                        ->icon('heroicon-o-check-circle')
                        ->schema([
                            Forms\Components\Repeater::make('requirements')
                                ->label('')
                                ->simple(
                                    Forms\Components\TextInput::make('item')
                                        ->placeholder('მაგ. ადვოკატის სტატუსი')
                                        ->required()
                                )
                                ->addActionLabel('+ მოთხოვნა')
                                ->reorderable()
                                ->columnSpanFull(),
                        ])
                        ->collapsible(),

                    Forms\Components\Section::make('მოვალეობები')
                        ->icon('heroicon-o-clipboard-document-list')
                        ->schema([
                            Forms\Components\Repeater::make('responsibilities')
                                ->label('')
                                ->simple(
                                    Forms\Components\TextInput::make('item')
                                        ->placeholder('მაგ. კლიენტის სასამართლოში დაცვა')
                                        ->required()
                                )
                                ->addActionLabel('+ მოვალეობა')
                                ->reorderable()
                                ->columnSpanFull(),
                        ])
                        ->collapsible(),
                ]),

                // ── Sidebar (1/3) ─────────────────────────────────────────────
                Forms\Components\Group::make()->columnSpan(1)->schema([

                    Forms\Components\Section::make('სტატუსი')
                        ->icon('heroicon-o-cog-6-tooth')
                        ->schema([
                            Forms\Components\Select::make('status')
                                ->label('სტატუსი')
                                ->options([
                                    'open'   => '🟢 ღია',
                                    'closed' => '🔴 დახურული',
                                    'draft'  => '📝 მონახაზი',
                                ])
                                ->default('open')
                                ->required()
                                ->native(false),

                            Forms\Components\Toggle::make('is_active')
                                ->label('გამოქვეყნებული')
                                ->default(true)
                                ->onColor('success')
                                ->offColor('danger'),

                            Forms\Components\TextInput::make('sort_order')
                                ->label('პოზიცია')
                                ->numeric()
                                ->default(0)
                                ->minValue(0),
                        ]),

                    Forms\Components\Section::make('დეტალები')
                        ->icon('heroicon-o-information-circle')
                        ->schema([
                            Forms\Components\TextInput::make('department')
                                ->label('განყოფილება / ბიურო')
                                ->placeholder('მაგ. ქუთაისის ბიურო')
                                ->maxLength(200),

                            Forms\Components\TextInput::make('location')
                                ->label('ადგილმდებარეობა')
                                ->placeholder('მაგ. ქუთაისი, იმერეთი')
                                ->maxLength(200),

                            Forms\Components\Select::make('type')
                                ->label('დასაქმების ტიპი')
                                ->options([
                                    'full_time'  => '🕘 სრული განაკვეთი',
                                    'part_time'  => '🕓 ნახ. განაკვეთი',
                                    'contract'   => '📄 კონტრაქტი',
                                    'internship' => '🎓 სტაჟირება',
                                ])
                                ->default('full_time')
                                ->native(false),

                            Forms\Components\DatePicker::make('deadline')
                                ->label('განაცხადის ვადა')
                                ->minDate(today())
                                ->native(false),
                        ]),

                    Forms\Components\Section::make('განაცხადი')
                        ->icon('heroicon-o-paper-airplane')
                        ->schema([
                            Forms\Components\TextInput::make('contact_email')
                                ->label('საკონტ. ელ-ფოსტა')
                                ->email()
                                ->maxLength(255),

                            Forms\Components\TextInput::make('application_url')
                                ->label('განაცხადის URL')
                                ->url()
                                ->helperText('vacancy.hr.gov.ge ბმული')
                                ->maxLength(500),
                        ]),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('ვაკანსია')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->weight('semibold')
                    ->description(fn (Vacancy $r): string => $r->excerpt ?? ''),

                Tables\Columns\TextColumn::make('department')
                    ->label('ბიურო / განყ.')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('location')
                    ->label('ადგ.')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('ტიპი')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'full_time'  => 'primary',
                        'part_time'  => 'info',
                        'contract'   => 'warning',
                        'internship' => 'success',
                        default      => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'full_time'  => 'სრული',
                        'part_time'  => 'ნახ.',
                        'contract'   => 'კონტრ.',
                        'internship' => 'სტაჟ.',
                        default      => $state,
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('სტ.')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'open'   => 'success',
                        'closed' => 'danger',
                        'draft'  => 'warning',
                        default  => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'open'   => '🟢 ღია',
                        'closed' => '🔴 დახური.',
                        'draft'  => '📝 მონახ.',
                        default  => $state,
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('deadline')
                    ->label('ვადა')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn (?string $state): string =>
                        $state && \Carbon\Carbon::parse($state)->isPast() ? 'danger' : 'success'
                    ),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('გამოქვ.')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->sortable(),

                Tables\Columns\TextColumn::make('application_url')
                    ->label('hr.gov.ge')
                    ->formatStateUsing(fn ($state) => $state ? '🔗 ბმული' : '—')
                    ->url(fn ($record) => $record->application_url)
                    ->openUrlInNewTab()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('სტატუსი')
                    ->options([
                        'open'   => '🟢 ღია',
                        'closed' => '🔴 დახურული',
                        'draft'  => '📝 მონახაზი',
                    ])
                    ->native(false),

                Tables\Filters\SelectFilter::make('type')
                    ->label('ტიპი')
                    ->options([
                        'full_time'  => 'სრული განაკვეთი',
                        'part_time'  => 'ნახ. განაკვეთი',
                        'contract'   => 'კონტრაქტი',
                        'internship' => 'სტაჟირება',
                    ])
                    ->native(false),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('გამოქვეყნება')
                    ->trueLabel('გამოქვეყნებული')
                    ->falseLabel('დამალული')
                    ->native(false),
            ])
            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\Action::make('open_url')
                    ->label('hr.gov.ge')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (Vacancy $r) => $r->application_url)
                    ->openUrlInNewTab()
                    ->visible(fn (Vacancy $r) => (bool) $r->application_url)
                    ->color('gray'),
                Tables\Actions\EditAction::make()->iconButton(),
                Tables\Actions\DeleteAction::make()->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order')
            ->striped();
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListVacancies::route('/'),
            'create' => Pages\CreateVacancy::route('/create'),
            'edit'   => Pages\EditVacancy::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return app(AdminDashboardMetrics::class)->badge('vacancies_open', hideZero: true);
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}

