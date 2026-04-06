<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JournalResource\Pages;
use App\Models\Journal;
use App\Services\ImageService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class JournalResource extends Resource
{
    protected static ?string $model = Journal::class;
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'ჟურნალები / Journals';
    protected static ?string $navigationGroup = 'კონტენტი';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make(3)->schema([
                Forms\Components\Group::make()->columnSpan(2)->schema([
                    Forms\Components\Section::make()->schema([
                        Forms\Components\TextInput::make('title')->label('სათ.')->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug($state))),
                        Forms\Components\TextInput::make('slug')->label('Slug')->required()
                            ->unique(Journal::class, 'slug', ignoreRecord: true),
                        Forms\Components\Textarea::make('description')->label('აღწ.')->rows(3),
                    ]),
                    Forms\Components\Section::make('ფაილი')->schema([
                        Forms\Components\FileUpload::make('file_path')
                            ->label('PDF ჟურნ.')
                            ->disk('public')
                            ->directory('journals')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(51200),
                    ]),
                ]),

                Forms\Components\Group::make()->columnSpan(1)->schema([
                    Forms\Components\Section::make()->schema([
                        Forms\Components\FileUpload::make('_raw_cover')
                            ->label('ყდის სურათი')
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->directory('journals/raw')
                            ->maxSize(5120)
                            ->dehydrated(false)
                            ->live(),
                        Forms\Components\Placeholder::make('current_cover')
                            ->content(fn ($record) => $record?->cover_image_thumbnail
                                ? new \Illuminate\Support\HtmlString('<img src="' . asset('storage/' . $record->cover_image_thumbnail) . '" style="max-width:100%">')
                                : 'ყდა არ არის')
                            ->visible(fn ($record) => $record !== null),
                        Forms\Components\TextInput::make('year')->label('წელი'),
                        Forms\Components\TextInput::make('volume')->label('ტომი'),
                        Forms\Components\TextInput::make('issue_number')->label('ნომ.'),
                        Forms\Components\DatePicker::make('published_at')->label('გამ. თარ.'),
                        Forms\Components\Toggle::make('is_active')->label('აქტ.')->default(true),
                        Forms\Components\TextInput::make('sort_order')->label('თანმ.')->numeric()->default(0),
                    ]),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\ImageColumn::make('cover_image_thumbnail')->label('')->disk('public')->width(50)->height(65),
            Tables\Columns\TextColumn::make('title')->label('სათ.')->searchable()->limit(50),
            Tables\Columns\TextColumn::make('year')->label('წ.'),
            Tables\Columns\TextColumn::make('volume')->label('ტ.'),
            Tables\Columns\TextColumn::make('download_count')->label('📥')->sortable(),
            Tables\Columns\IconColumn::make('is_active')->label('აქტ.')->boolean(),
        ])
        ->actions([Tables\Actions\EditAction::make()])
        ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])])
        ->reorderable('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListJournals::route('/'),
            'create' => Pages\CreateJournal::route('/create'),
            'edit'   => Pages\EditJournal::route('/{record}/edit'),
        ];
    }
}
