<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StatResource\Pages;
use App\Models\Stat;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StatResource extends AdminResource
{
    protected static ?string $model = Stat::class;
    protected static ?string $navigationIcon  = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'სტატისტიკა';
    protected static ?string $navigationGroup = 'კონტენტი';
    protected static ?int    $navigationSort  = 6;
    protected static ?string $recordTitleAttribute = 'label';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('სტატისტიკა / Statistic')->schema([
                Forms\Components\TextInput::make('label')
                    ->label('სახელი (ქართ.)')
                    ->required()
                    ->maxLength(200)
                    ->helperText('მაგ: სარგებლობა მიიღო')
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('value')
                    ->label('მნიშვნელობა')
                    ->required()
                    ->maxLength(100)
                    ->helperText('მაგ: 127,000+ ან 98%'),

                Forms\Components\TextInput::make('suffix')
                    ->label('სუფიქსი (ოფცია)')
                    ->maxLength(20)
                    ->helperText('მაგ: + ან %'),

                Forms\Components\TextInput::make('icon')
                    ->label('Heroicon სახელი')
                    ->maxLength(100)
                    ->helperText('მაგ: heroicon-o-users'),

                Forms\Components\ColorPicker::make('color')
                    ->label('ფერი (HEX)'),

                Forms\Components\Select::make('group')
                    ->label('ჯგუფი')
                    ->options(['homepage' => 'მთ. გვერდი', 'about' => 'ჩვენ შესახებ'])
                    ->default('homepage'),

                Forms\Components\TextInput::make('sort_order')
                    ->label('რიგი')
                    ->integer()
                    ->default(0),

                Forms\Components\Toggle::make('is_active')
                    ->label('ჩვენება')
                    ->default(true),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('#')->sortable()->width(50),

                Tables\Columns\TextColumn::make('value')
                    ->label('მნიშვნელობა')
                    ->searchable()
                    ->weight('bold')
                    ->color('primary'),

                Tables\Columns\TextColumn::make('label')
                    ->label('სახელი')
                    ->searchable()->limit(60),

                Tables\Columns\TextColumn::make('group')
                    ->label('ჯგუფი')
                    ->badge()->color('gray'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('ჩვ.')->boolean(),
            ])
            ->reorderable('sort_order')
            ->defaultSort('sort_order', 'asc')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index'  => Pages\ListStats::route('/'),
            'create' => Pages\CreateStat::route('/create'),
            'edit'   => Pages\EditStat::route('/{record}/edit'),
        ];
    }
}
