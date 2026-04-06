<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VideoResource\Pages;
use App\Models\Video;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class VideoResource extends Resource
{
    protected static ?string $model = Video::class;
    protected static ?string $navigationIcon = 'heroicon-o-play-circle';
    protected static ?string $navigationLabel = 'ვიდეო / Videos';
    protected static ?string $navigationGroup = 'კონტენტი';
    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')->label('სათ.')->required()->columnSpanFull(),
            Forms\Components\TextInput::make('youtube_url')->label('YouTube URL')->url()
                ->placeholder('https://www.youtube.com/watch?v=...')
                ->helperText('ID და Thumbnail ავტომატურად განახლდება შენახვისას')
                ->columnSpanFull(),
            Forms\Components\TextInput::make('youtube_id')->label('YouTube ID (auto)')->readOnly(),
            Forms\Components\TextInput::make('category')->label('კატ.'),
            Forms\Components\Textarea::make('description')->label('აღწ.')->rows(3)->columnSpanFull(),
            Forms\Components\DatePicker::make('published_at')->label('გამ. თარ.'),
            Forms\Components\TextInput::make('sort_order')->label('თ.ი.')->numeric()->default(0),
            Forms\Components\Toggle::make('is_active')->label('აქტ.')->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\ImageColumn::make('thumbnail')->label('')->url(fn ($record) => "https://www.youtube.com/watch?v={$record->youtube_id}")->openUrlInNewTab()->width(120)->height(68),
            Tables\Columns\TextColumn::make('title')->label('სათ.')->searchable()->limit(50),
            Tables\Columns\TextColumn::make('youtube_id')->label('YT ID'),
            Tables\Columns\TextColumn::make('category')->label('კატ.')->badge(),
            Tables\Columns\IconColumn::make('is_active')->label('აქტ.')->boolean(),
            Tables\Columns\TextColumn::make('sort_order')->label('თ.')->sortable(),
        ])
        ->actions([Tables\Actions\EditAction::make()])
        ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])])
        ->reorderable('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListVideos::route('/'),
            'create' => Pages\CreateVideo::route('/create'),
            'edit'   => Pages\EditVideo::route('/{record}/edit'),
        ];
    }
}
