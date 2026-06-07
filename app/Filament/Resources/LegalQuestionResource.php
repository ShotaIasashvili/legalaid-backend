<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LegalQuestionResource\Pages;
use App\Models\LegalQuestion;
use App\Services\AdminDashboardMetrics;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class LegalQuestionResource extends AdminResource
{
    protected static ?string $model = LegalQuestion::class;
    protected static ?string $navigationIcon  = 'heroicon-o-question-mark-circle';
    protected static ?string $navigationLabel = 'სამართლ. კითხვები';
    protected static ?string $navigationGroup = 'სამართლებრივი';
    protected static ?int    $navigationSort  = 2;
    protected static ?string $recordTitleAttribute = 'question';

    public static function getNavigationBadge(): ?string
    {
        return app(AdminDashboardMetrics::class)->badge('legal_questions_total');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('კითხვა / Question')->schema([
                Forms\Components\Textarea::make('question')
                    ->label('კითხვა')
                    ->required()
                    ->rows(3)
                    ->maxLength(1000)
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('category')
                    ->label('კატეგორია')
                    ->required()
                    ->maxLength(255)
                    ->datalist(fn () => LegalQuestion::distinct()->pluck('category')->filter()->sort()->values()->toArray()),
            ])->columns(1),

            Forms\Components\Section::make('პასუხი / Answer')->schema([
                Forms\Components\RichEditor::make('answer_html')
                    ->label('პასუხი (HTML)')
                    ->required()
                    ->toolbarButtons([
                        'bold', 'italic', 'underline', 'strike',
                        'bulletList', 'orderedList', 'blockquote',
                        'h2', 'h3', 'link', 'undo', 'redo',
                    ])
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('answer_text')
                    ->label('პასუხი (Plain text — ძებნისთვის)')
                    ->rows(4)
                    ->maxLength(5000)
                    ->helperText('HTML ტეგების გარეშე ტექსტი — გამოიყენება ძებნისას.')
                    ->columnSpanFull(),
            ]),

            Forms\Components\Section::make('პარამეტრები')->schema([
                Forms\Components\TextInput::make('sort_order')
                    ->label('რიგის ნომერი')
                    ->integer()
                    ->default(0),

                Forms\Components\Toggle::make('is_active')
                    ->label('აქტიური')
                    ->default(true),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')->sortable()->width(60),

                Tables\Columns\TextColumn::make('question')
                    ->label('კითხვა')
                    ->searchable()->limit(80)->wrap(),

                Tables\Columns\TextColumn::make('category')
                    ->label('კატეგორია')
                    ->searchable()->badge()->color('info'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('აქტ.')->boolean()->width(60),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('რიგი')->sortable()->width(60),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('კატეგორია')
                    ->options(fn () => LegalQuestion::distinct()->pluck('category', 'category')->filter()->sort()->toArray()),
                Tables\Filters\TernaryFilter::make('is_active')->label('სტატუსი'),
            ])
            ->defaultSort('sort_order', 'asc')
            ->reorderable('sort_order')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('გააქტიურება')
                        ->icon('heroicon-o-check-circle')
                        ->action(fn ($records) => $records->each->update(['is_active' => true])),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('გამორთვა')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn ($records) => $records->each->update(['is_active' => false])),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListLegalQuestions::route('/'),
            'create' => Pages\CreateLegalQuestion::route('/create'),
            'edit'   => Pages\EditLegalQuestion::route('/{record}/edit'),
        ];
    }
}
