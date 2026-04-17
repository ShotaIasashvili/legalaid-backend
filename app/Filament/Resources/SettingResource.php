<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SettingResource extends AdminResource
{
    protected static ?string $model = Setting::class;
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'პარამეტრები / Settings';
    protected static ?string $navigationGroup = 'სისტემა';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('group')
                ->label('ჯგუფი')
                ->options([
                    'general' => 'ზოგადი',
                    'contact' => 'კონტაქტი',
                    'social'  => 'სოციალური ქსელები',
                    'seo'     => 'SEO',
                    'hero'    => 'Hero სლაიდერი',
                    'stats'   => 'სტატისტიკა',
                    'footer'  => 'Footer',
                ])
                ->required()
                ->default('general'),

            Forms\Components\TextInput::make('key')->label('Key')->required()
                ->unique(Setting::class, 'key', ignoreRecord: true),
            Forms\Components\TextInput::make('label')->label('ლეიბლი (ადამიანური სახ.)'),

            Forms\Components\Select::make('type')
                ->label('ტიპი')
                ->options(['text' => 'ტექსტი', 'html' => 'HTML', 'json' => 'JSON', 'image' => 'სურათი', 'boolean' => 'Boolean'])
                ->required()
                ->default('text')
                ->live(),

            Forms\Components\RichEditor::make('value')
                ->label('მნიშვნელობა')
                ->columnSpanFull()
                ->visible(fn (Forms\Get $get) => $get('type') === 'html'),

            Forms\Components\Textarea::make('value')
                ->label('მნიშვნელობა')
                ->rows(4)
                ->columnSpanFull()
                ->visible(fn (Forms\Get $get) => in_array($get('type'), ['text', 'json'])),

            Forms\Components\FileUpload::make('value')
                ->label('სურათი')
                ->image()
                ->disk('public')
                ->directory('settings')
                ->imageEditor()
                ->columnSpanFull()
                ->visible(fn (Forms\Get $get) => $get('type') === 'image'),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\BadgeColumn::make('group')->label('ჯგუფი'),
            Tables\Columns\TextColumn::make('key')->label('Key')->searchable(),
            Tables\Columns\TextColumn::make('label')->label('ლეიბლი'),
            Tables\Columns\BadgeColumn::make('type')->label('ტიპი'),
            Tables\Columns\TextColumn::make('value')->label('მნიშვ.')->limit(60),
        ])
        ->filters([
            Tables\Filters\SelectFilter::make('group')
                ->options(['general' => 'ზოგ.', 'contact' => 'კონტ.', 'social' => 'სოც.', 'seo' => 'SEO', 'hero' => 'Hero', 'footer' => 'Footer']),
        ])
        ->actions([Tables\Actions\EditAction::make()])
        ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit'   => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
