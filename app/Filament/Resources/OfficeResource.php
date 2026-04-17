<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OfficeResource\Pages;
use App\Models\Office;
use App\Services\AdminDashboardMetrics;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OfficeResource extends AdminResource
{
    protected static ?string $model = Office::class;
    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $navigationLabel = 'ოფისები / Offices';
    protected static ?string $navigationGroup = 'ორგანიზაცია';
    protected static ?int $navigationSort = 2;

    private static function typeOptions(): array
    {
        return [
            'bureau'              => '🏛️ ბიურო',
            'consultation_center' => '🗣️ საკ. ცენტრი',
            'mobile'              => '🚗 მობილური',
        ];
    }

    private static function regionOptions(): array
    {
        return [
            'თბილისი'                    => 'თბილისი',
            'მცხეთა-მთიანეთი'            => 'მცხეთა-მთიანეთი',
            'კახეთი'                     => 'კახეთი',
            'ქვემო ქართლი'               => 'ქვემო ქართლი',
            'შიდა ქართლი'                => 'შიდა ქართლი',
            'სამცხე-ჯავახეთი'            => 'სამცხე-ჯავახეთი',
            'იმერეთი'                    => 'იმერეთი',
            'სამეგრელო-ზემო სვანეთი'     => 'სამეგრელო-ზემო სვანეთი',
            'გურია'                      => 'გურია',
            'აჭარა'                      => 'აჭარა',
            'რაჭა-ლეჩხუმი'               => 'რაჭა-ლეჩხუმი',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make(3)->schema([

                // ── Left column (2/3) ─────────────────────────────────────────
                Forms\Components\Group::make()->columnSpan(2)->schema([

                    Forms\Components\Section::make('ძირითადი ინფო')
                        ->icon('heroicon-o-building-office-2')
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->label('სახელი')
                                ->required()
                                ->maxLength(255)
                                ->columnSpanFull(),

                            Forms\Components\Select::make('type')
                                ->label('ტიპი')
                                ->options(static::typeOptions())
                                ->required()
                                ->default('bureau')
                                ->native(false),

                            Forms\Components\Select::make('region')
                                ->label('რეგიონი')
                                ->options(static::regionOptions())
                                ->searchable()
                                ->native(false),

                            Forms\Components\TextInput::make('city')
                                ->label('ქალაქი / მუნ.')
                                ->maxLength(100),

                            Forms\Components\Textarea::make('address')
                                ->label('მისამართი')
                                ->rows(2)
                                ->columnSpanFull(),

                            Forms\Components\TextInput::make('head')
                                ->label('ხელმძღვანელი / კონსულტანტი')
                                ->helperText('ბიუროს უფროსი ან საკ. ცენტრის კონსულტანტი')
                                ->maxLength(255)
                                ->columnSpanFull(),
                        ])
                        ->columns(3),

                    Forms\Components\Section::make('საკონტაქტო')
                        ->icon('heroicon-o-phone')
                        ->schema([
                            Forms\Components\TextInput::make('phone')
                                ->label('სტაციონარი')
                                ->tel()
                                ->maxLength(80),

                            Forms\Components\TextInput::make('mobile')
                                ->label('მობილური')
                                ->tel()
                                ->maxLength(80),

                            Forms\Components\TextInput::make('email')
                                ->label('ელ-ფოსტა')
                                ->email()
                                ->maxLength(255),

                            Forms\Components\TextInput::make('working_hours')
                                ->label('სამუშ. საათები')
                                ->placeholder('ორ-პარ: 09:00-18:00')
                                ->maxLength(100),
                        ])
                        ->columns(2),

                    Forms\Components\Section::make('რუკის კოორდინატები')
                        ->icon('heroicon-o-map')
                        ->schema([
                            Forms\Components\TextInput::make('lat')
                                ->label('გრძ. (Latitude)')
                                ->numeric()
                                ->step(0.0000001),

                            Forms\Components\TextInput::make('lng')
                                ->label('გნ. (Longitude)')
                                ->numeric()
                                ->step(0.0000001),
                        ])
                        ->columns(2)
                        ->collapsible()
                        ->collapsed(),

                    Forms\Components\Section::make('სერვისები')
                        ->icon('heroicon-o-list-bullet')
                        ->schema([
                            Forms\Components\Repeater::make('services')
                                ->label('')
                                ->simple(
                                    Forms\Components\TextInput::make('service')
                                        ->placeholder('მაგ. სისხლის სამართალი')
                                        ->required()
                                )
                                ->addActionLabel('+ სერვისი')
                                ->reorderable()
                                ->columnSpanFull(),
                        ])
                        ->collapsible()
                        ->collapsed(),

                    Forms\Components\Section::make('აღწერა')
                        ->icon('heroicon-o-document-text')
                        ->schema([
                            Forms\Components\Textarea::make('description')
                                ->label('')
                                ->rows(4)
                                ->columnSpanFull(),
                        ])
                        ->collapsible()
                        ->collapsed(),
                ]),

                // ── Right column (1/3) ────────────────────────────────────────
                Forms\Components\Group::make()->columnSpan(1)->schema([

                    Forms\Components\Section::make('სტატუსი')
                        ->icon('heroicon-o-cog-6-tooth')
                        ->schema([
                            Forms\Components\Toggle::make('is_active')
                                ->label('აქტიური')
                                ->default(true)
                                ->onColor('success')
                                ->offColor('danger'),

                            Forms\Components\Toggle::make('is_specialized')
                                ->label('სპეციალიზებული')
                                ->helperText('განსაკ. საქმეთა ბიურო')
                                ->onColor('warning'),

                            Forms\Components\TextInput::make('sort_order')
                                ->label('პოზიცია')
                                ->numeric()
                                ->default(0)
                                ->minValue(0),
                        ]),

                    Forms\Components\Section::make('ფოტო')
                        ->icon('heroicon-o-photo')
                        ->schema([
                            Forms\Components\FileUpload::make('photo')
                                ->label('')
                                ->image()
                                ->disk('public')
                                ->directory('offices')
                                ->imageEditor()
                                ->imageEditorMode(2)
                                ->maxSize(4096)
                                ->columnSpanFull(),
                        ]),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('სახელი')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->weight('semibold'),

                Tables\Columns\TextColumn::make('type')
                    ->label('ტიპი')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'bureau'              => 'primary',
                        'consultation_center' => 'success',
                        'mobile'              => 'warning',
                        default               => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'bureau'              => 'ბიურო',
                        'consultation_center' => 'საკ. ცენტრი',
                        'mobile'              => 'მობ.',
                        default               => $state,
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('region')
                    ->label('რეგიონი')
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('city')
                    ->label('ქ./მუნ.')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('head')
                    ->label('ხელმძღვ./კონს.')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('სტაც.')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('mobile')
                    ->label('მობ.')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('ელ-ფოსტა')
                    ->searchable()
                    ->copyable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('address')
                    ->label('მისამართი')
                    ->limit(45)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('is_specialized')
                    ->label('სპეც.')
                    ->boolean()
                    ->trueColor('warning')
                    ->falseColor('gray')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('აქტ.')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('ტიპი')
                    ->options([
                        'bureau'              => 'ბიურო',
                        'consultation_center' => 'საკ. ცენტრი',
                        'mobile'              => 'მობ.',
                    ])
                    ->native(false),

                Tables\Filters\SelectFilter::make('region')
                    ->label('რეგიონი')
                    ->options(static::regionOptions())
                    ->native(false),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('სტატუსი')
                    ->trueLabel('აქტიური')
                    ->falseLabel('გამორთული')
                    ->native(false),

                Tables\Filters\TernaryFilter::make('is_specialized')
                    ->label('სპეციალიზ.')
                    ->trueLabel('სპეც. ბიუროები')
                    ->falseLabel('ჩვეულებრივი')
                    ->native(false),
            ])
            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\EditAction::make()->iconButton(),
                Tables\Actions\DeleteAction::make()->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->groups([
                Tables\Grouping\Group::make('region')
                    ->label('რეგიონი')
                    ->collapsible(),
                Tables\Grouping\Group::make('type')
                    ->label('ტიპი')
                    ->getTitleFromRecordUsing(fn (Office $r): string => match ($r->type) {
                        'bureau'              => '🏛️ ბიურო',
                        'consultation_center' => '🗣️ საკ. ცენტრი',
                        default               => $r->type,
                    })
                    ->collapsible(),
            ])
            ->defaultGroup('region')
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->striped();
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListOffices::route('/'),
            'create' => Pages\CreateOffice::route('/create'),
            'edit'   => Pages\EditOffice::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return app(AdminDashboardMetrics::class)->badge('offices_total', hideZero: true);
    }
}

