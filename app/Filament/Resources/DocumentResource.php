<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentResource\Pages;
use App\Models\Document;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class DocumentResource extends AdminResource
{
    protected static ?string $model = Document::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'დოკუმენტები / PDFs';
    protected static ?string $navigationGroup = 'სამართლებრივი';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make(3)->schema([
                Forms\Components\Group::make()->columnSpan(2)->schema([
                    Forms\Components\Section::make('დოკუმენტის ინფო')->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('სათაური')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, Forms\Set $set) =>
                                $set('slug', Str::slug($state))
                            ),
                        Forms\Components\TextInput::make('slug')->label('Slug')->required()
                            ->unique(Document::class, 'slug', ignoreRecord: true),
                        Forms\Components\TextInput::make('badge')
                            ->label('Badge (გადაწყვეტილება #49)')
                            ->placeholder('გადაწყვეტილება #49, 02.12.2016'),
                        Forms\Components\Textarea::make('description')->label('აღწერა')->rows(3),
                        Forms\Components\TextInput::make('issuer')->label('გამცემი ორგანო'),
                        Forms\Components\DatePicker::make('issued_at')->label('გამოცემის თარიღი'),
                    ]),

                    Forms\Components\Section::make('ფაილი')->schema([
                        Forms\Components\FileUpload::make('file_path')
                            ->label('PDF / დოკუმენტი')
                            ->required()
                            ->disk('public')
                            ->directory('documents')
                            ->acceptedFileTypes(['application/pdf', 'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                'application/vnd.ms-excel',
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                            ->maxSize(51200) // 50 MB
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state) {
                                    $set('file_name', basename($state));
                                    $ext = pathinfo($state, PATHINFO_EXTENSION);
                                    $set('file_type', strtolower($ext));
                                }
                            }),
                        Forms\Components\TextInput::make('file_name')->label('ფაილის სახელი'),
                    ]),
                ]),

                Forms\Components\Group::make()->columnSpan(1)->schema([
                    Forms\Components\Section::make('კლასიფიკაცია')->schema([
                        Forms\Components\Select::make('type')
                            ->label('ტიპი')
                            ->options([
                                'legal_act'      => 'სამართლებრივი აქტი',
                                'registry_act'   => 'რეესტრის გადაწყვეტილება',
                                'council_decision' => 'საბჭოს გადაწყვეტილება',
                                'public_info'    => 'საჯარო ინფო',
                                'annual_report'  => 'წლიური ანგარიში',
                                'form'           => 'ფორმა / შაბლონი',
                            ])
                            ->required()
                            ->default('legal_act'),
                        Forms\Components\TextInput::make('category')->label('კატეგორია'),
                        Forms\Components\Toggle::make('is_active')->label('აქტიური')->default(true),
                        Forms\Components\TextInput::make('sort_order')->label('თანმიმდევრობა')->numeric()->default(0),
                        Forms\Components\Placeholder::make('download_count')
                            ->label('გადმოწერები')
                            ->content(fn ($record) => $record?->download_count ?? 0),
                    ]),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('badge')->label('Badge')->badge()->limit(40),
            Tables\Columns\TextColumn::make('title')->label('სათაური')->searchable()->limit(60),
            Tables\Columns\BadgeColumn::make('type')->label('ტიპი'),
            Tables\Columns\TextColumn::make('file_type')->label('ფაილი')->badge(),
            Tables\Columns\IconColumn::make('is_active')->label('აქტ.')->boolean(),
            Tables\Columns\TextColumn::make('download_count')->label('📥')->sortable(),
            Tables\Columns\TextColumn::make('issued_at')->label('თარიღი')->date('d/m/Y'),
        ])
        ->filters([
            Tables\Filters\SelectFilter::make('type')
                ->options([
                    'legal_act' => 'სამართლ. აქტი',
                    'registry_act' => 'რეესტრი',
                    'council_decision' => 'საბჭო',
                    'public_info' => 'საჯარო ინფო',
                    'annual_report' => 'ანგარიში',
                    'form' => 'ფორმა',
                ]),
        ])
        ->actions([Tables\Actions\EditAction::make()])
        ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])])
        ->reorderable('sort_order')
        ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListDocuments::route('/'),
            'create' => Pages\CreateDocument::route('/create'),
            'edit'   => Pages\EditDocument::route('/{record}/edit'),
        ];
    }
}
