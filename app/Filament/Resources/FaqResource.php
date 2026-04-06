<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FaqResource\Pages;
use App\Models\Faq;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;
    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';
    protected static ?string $navigationLabel = 'FAQ';
    protected static ?string $navigationGroup = 'სამართლებრივი';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('question')->label('კითხვა')->required()->columnSpanFull(),
            Forms\Components\RichEditor::make('answer_html')->label('პასუხი (HTML)')->required()->columnSpanFull(),
            Forms\Components\Textarea::make('answer_text')->label('პასუხი (ტექსტი, SEO-სთვის)')->rows(4)->columnSpanFull(),
            Forms\Components\TextInput::make('category')->label('კატეგორია'),
            Forms\Components\TextInput::make('sort_order')->label('თანმიმდევ.')->numeric()->default(0),
            Forms\Components\Toggle::make('is_active')->label('აქტიური')->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('question')->label('კითხვა')->searchable()->limit(70),
            Tables\Columns\TextColumn::make('category')->label('კატეგ.')->badge()->searchable(),
            Tables\Columns\IconColumn::make('is_active')->label('აქტ.')->boolean(),
            Tables\Columns\TextColumn::make('sort_order')->label('თანმ.')->sortable(),
        ])
        ->filters([Tables\Filters\SelectFilter::make('category')
            ->options(fn () => Faq::distinct()->pluck('category', 'category')->filter()->toArray())])
        ->actions([Tables\Actions\EditAction::make()])
        ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])])
        ->reorderable('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFaqs::route('/'),
            'create' => Pages\CreateFaq::route('/create'),
            'edit'   => Pages\EditFaq::route('/{record}/edit'),
        ];
    }
}
