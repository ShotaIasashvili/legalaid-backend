<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageContentResource\Pages;
use App\Models\PageContent;
use App\Services\AdminDashboardMetrics;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PageContentResource extends AdminResource
{
    protected static ?string $model = PageContent::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';
    protected static ?string $navigationLabel = 'გვერდის კონტენტი';
    protected static ?string $navigationGroup = 'კონტენტი';
    protected static ?int $navigationSort = 10;

    /** Human-readable page labels used in form + table */
    public static function pageOptions(): array
    {
        return [
            'home'               => '🏠 მთავარი',
            'about'              => 'ℹ️ ჩვენ შესახებ',
            'history'            => '📜 ისტორია',
            'structure'          => '🏛️ სტრუქტურა',
            'contact'            => '📞 კონტაქტი',
            'consultation_guide' => '📋 კონსულტაციის გიდი',
            'public_info'        => '📂 საჯარო ინფო',
            'offices'            => '🗺️ ოფისები',
            'paralegal'          => '⚖️ პარალეგალი',
            'archive'            => '🗃️ არქივი',
            'apparatus'          => '🏢 აპარატი',
        ];
    }

    /** Badge colors per page for the table */
    private static function pageColors(): array
    {
        return [
            'home'               => 'primary',
            'about'              => 'info',
            'history'            => 'warning',
            'structure'          => 'gray',
            'contact'            => 'success',
            'consultation_guide' => 'danger',
            'public_info'        => 'primary',
            'offices'            => 'info',
            'paralegal'          => 'warning',
            'archive'            => 'gray',
            'apparatus'          => 'success',
        ];
    }

    /** Badge colors per type */
    private static function typeColors(): array
    {
        return [
            'text'    => 'primary',
            'html'    => 'warning',
            'json'    => 'info',
            'image'   => 'success',
            'boolean' => 'danger',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            // ── Identification row ──────────────────────────────────────────────
            Forms\Components\Section::make('იდენტიფიკაცია')
                ->icon('heroicon-o-identification')
                ->schema([
                    Forms\Components\Select::make('page')
                        ->label('გვერდი')
                        ->options(static::pageOptions())
                        ->required()
                        ->searchable()
                        ->native(false),

                    Forms\Components\TextInput::make('section')
                        ->label('სექცია')
                        ->placeholder('hero, mission, contact_info…')
                        ->helperText('დაჯგუფება გვერდის შიგნით')
                        ->maxLength(100),

                    Forms\Components\TextInput::make('key')
                        ->label('გასაღები (key)')
                        ->required()
                        ->placeholder('title, subtitle, body…')
                        ->maxLength(100),

                    Forms\Components\TextInput::make('label')
                        ->label('ადმინ ლეიბლი')
                        ->helperText('ადამიანური სახელი, ადმინ პანელისთვის')
                        ->maxLength(200),
                ])
                ->columns(2),

            // ── Content ─────────────────────────────────────────────────────────
            Forms\Components\Section::make('კონტენტი')
                ->icon('heroicon-o-pencil-square')
                ->schema([
                    Forms\Components\Grid::make(3)
                        ->schema([
                            Forms\Components\Select::make('type')
                                ->label('ტიპი')
                                ->options([
                                    'text'    => '📝 ტექსტი',
                                    'html'    => '🌐 HTML',
                                    'json'    => '{ } JSON',
                                    'image'   => '🖼️ სურათი',
                                    'boolean' => '✅ Boolean',
                                ])
                                ->required()
                                ->default('text')
                                ->native(false)
                                ->live(),

                            Forms\Components\TextInput::make('sort_order')
                                ->label('პოზიცია')
                                ->numeric()
                                ->default(0)
                                ->minValue(0)
                                ->step(1),
                        ]),

                    // Plain text
                    Forms\Components\Textarea::make('value')
                        ->label('ტექსტი')
                        ->rows(4)
                        ->columnSpanFull()
                        ->visible(fn (Forms\Get $get) => $get('type') === 'text'),

                    // HTML rich editor
                    Forms\Components\RichEditor::make('value')
                        ->label('HTML კონტენტი')
                        ->toolbarButtons([
                            'bold', 'italic', 'underline', 'strike',
                            'link', 'bulletList', 'orderedList',
                            'h2', 'h3', 'blockquote',
                            'redo', 'undo',
                        ])
                        ->columnSpanFull()
                        ->visible(fn (Forms\Get $get) => $get('type') === 'html'),

                    // JSON textarea with hint
                    Forms\Components\Textarea::make('value')
                        ->label('JSON მნიშვნელობა')
                        ->rows(8)
                        ->helperText('შეიყვანეთ ვალიდური JSON.')
                        ->columnSpanFull()
                        ->visible(fn (Forms\Get $get) => $get('type') === 'json'),

                    // Image upload
                    Forms\Components\FileUpload::make('value')
                        ->label('სურათი')
                        ->image()
                        ->disk('public')
                        ->directory('page-content')
                        ->imageEditor()
                        ->imageEditorMode(2)
                        ->maxSize(5120)
                        ->columnSpanFull()
                        ->visible(fn (Forms\Get $get) => $get('type') === 'image'),

                    // Boolean toggle
                    Forms\Components\Toggle::make('value')
                        ->label('ჩართვა/გამორთვა')
                        ->onColor('success')
                        ->offColor('danger')
                        ->visible(fn (Forms\Get $get) => $get('type') === 'boolean'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        $pageColors = static::pageColors();
        $typeColors = static::typeColors();

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('page')
                    ->label('გვერდი')
                    ->badge()
                    ->color(fn (string $state): string => $pageColors[$state] ?? 'gray')
                    ->formatStateUsing(fn (string $state): string => static::pageOptions()[$state] ?? $state)
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('section')
                    ->label('სექცია')
                    ->badge()
                    ->color('gray')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('key')
                    ->label('Key')
                    ->fontFamily('mono')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('label')
                    ->label('ლეიბლი')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('ტიპი')
                    ->badge()
                    ->color(fn (string $state): string => $typeColors[$state] ?? 'gray'),

                Tables\Columns\TextColumn::make('value')
                    ->label('მნიშვნელობა')
                    ->limit(60)
                    ->html()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('page')
                    ->label('გვერდი')
                    ->options(static::pageOptions())
                    ->native(false),

                Tables\Filters\SelectFilter::make('type')
                    ->label('ტიპი')
                    ->options([
                        'text'    => 'ტექსტი',
                        'html'    => 'HTML',
                        'json'    => 'JSON',
                        'image'   => 'სურათი',
                        'boolean' => 'Boolean',
                    ])
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
                Tables\Grouping\Group::make('page')
                    ->label('გვერდი')
                    ->getTitleFromRecordUsing(fn (PageContent $record): string => static::pageOptions()[$record->page] ?? $record->page)
                    ->collapsible(),
            ])
            ->defaultGroup('page')
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPageContents::route('/'),
            'create' => Pages\CreatePageContent::route('/create'),
            'edit'   => Pages\EditPageContent::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return app(AdminDashboardMetrics::class)->badge('page_contents_total', hideZero: true);
    }
}
