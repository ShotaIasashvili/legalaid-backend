<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ServiceResource extends AdminResource
{
    protected static ?string $model = Service::class;
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationLabel = 'სერვისები / Services';
    protected static ?string $navigationGroup = 'კონტენტი';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make(3)->schema([

                Forms\Components\Group::make()->columnSpan(2)->schema([
                    Forms\Components\Section::make('ძირითადი')->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('სათაური')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, Forms\Set $set) =>
                                $set('slug', Str::slug($state))
                            ),
                        Forms\Components\TextInput::make('subtitle')
                            ->label('ქვე-სათაური (ინგლ.)'),
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->unique(Service::class, 'slug', ignoreRecord: true),
                        Forms\Components\Textarea::make('description')
                            ->label('მოკლე აღწერა')
                            ->rows(2)
                            ->required(),
                        Forms\Components\RichEditor::make('full_content')
                            ->label('სრული შინაარსი')
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('services/attachments'),
                    ]),

                    Forms\Components\Section::make('მოთხოვნები / Requirements')->schema([
                        Forms\Components\Repeater::make('requirements')
                            ->label('მოთხოვნები (სიაში)')
                            ->simple(Forms\Components\TextInput::make('item')->required())
                            ->addActionLabel('+ მოთხოვნის დამატება'),

                        Forms\Components\Repeater::make('how_to_apply')
                            ->label('მნიშვნელოვანი ნაბიჯები')
                            ->simple(Forms\Components\TextInput::make('step')->required())
                            ->addActionLabel('+ ნაბიჯის დამატება'),

                        Forms\Components\Repeater::make('special_eligibility_categories')
                            ->label('სპეციალური კატეგორიები')
                            ->simple(Forms\Components\TextInput::make('category')->required())
                            ->addActionLabel('+ კატეგორია'),

                        Forms\Components\Repeater::make('download_links')
                            ->label('გადმოწერის ბმულები')
                            ->schema([
                                Forms\Components\TextInput::make('text')->label('ტექსტი')->required(),
                                Forms\Components\TextInput::make('url')->label('URL')->url()->required(),
                            ])
                            ->addActionLabel('+ ბმულის დამატება'),
                    ]),
                ]),

                Forms\Components\Group::make()->columnSpan(1)->schema([
                    Forms\Components\Section::make('პარამეტრები')->schema([
                        Forms\Components\TextInput::make('category')->label('კატეგორია')->required(),
                        Forms\Components\TextInput::make('icon')->label('Lucide Icon Name')->placeholder('MessageCircle'),
                        Forms\Components\TextInput::make('color')->label('CSS Gradient')->placeholder('from-blue-500 to-blue-700'),
                        Forms\Components\TextInput::make('sort_order')->label('თანმიმდევრობა')->numeric()->default(0),
                        Forms\Components\Toggle::make('is_active')->label('აქტიური')->default(true),
                    ]),

                    Forms\Components\Section::make('სურათი')->schema([
                        Forms\Components\FileUpload::make('featured_image')
                            ->label('სურათი')
                            ->image()
                            ->disk('public')
                            ->directory('services')
                            ->imageEditor(),
                    ]),

                    Forms\Components\Section::make('დაკავშირებული')->schema([
                        Forms\Components\Repeater::make('related_services')
                            ->label('დაკავშირებული სლუგები')
                            ->simple(Forms\Components\TextInput::make('slug'))
                            ->addActionLabel('+ სლუგი'),
                    ]),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('sort_order')->label('#')->sortable()->width(50),
            Tables\Columns\TextColumn::make('title')->label('სათაური')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('category')->label('კატეგ.')->badge()->searchable(),
            Tables\Columns\TextColumn::make('slug')->label('Slug')->limit(30),
            Tables\Columns\IconColumn::make('is_active')->label('აქტ.')->boolean(),
        ])
        ->filters([
            Tables\Filters\SelectFilter::make('category')
                ->options(fn () => Service::distinct()->pluck('category', 'category')->toArray()),
        ])
        ->actions([Tables\Actions\EditAction::make()])
        ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])])
        ->reorderable('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit'   => Pages\EditService::route('/{record}/edit'),
        ];
    }
}
