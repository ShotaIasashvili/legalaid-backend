<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Models\Project;
use App\Services\ImageService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProjectResource extends AdminResource
{
    protected static ?string $model = Project::class;
    protected static ?string $navigationIcon  = 'heroicon-o-rocket-launch';
    protected static ?string $navigationLabel = 'პროექტები';
    protected static ?string $navigationGroup = 'კონტენტი';
    protected static ?int    $navigationSort  = 5;
    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make(3)->schema([

                Forms\Components\Group::make()->columnSpan(2)->schema([

                    Forms\Components\Section::make('პროექტი / Project')->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('სათაური')
                            ->required()
                            ->maxLength(500)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, Forms\Set $set) =>
                                $set('slug', Str::slug($state))
                            ),

                        Forms\Components\TextInput::make('slug')
                            ->label('Slug (URL)')
                            ->required()
                            ->unique(Project::class, 'slug', ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\Textarea::make('subtitle')
                            ->label('ქვე-სათაური')
                            ->rows(2)->maxLength(500),
                    ]),

                    Forms\Components\Section::make('შინაარსი')->schema([
                        Forms\Components\RichEditor::make('description')
                            ->label('აღწერა')
                            ->required()
                            ->toolbarButtons(['bold','italic','underline','bulletList','orderedList','link','h2','h3','undo','redo'])
                            ->columnSpanFull(),
                    ]),

                    Forms\Components\Section::make('მედია / Media')->schema([
                        Forms\Components\FileUpload::make('_raw_image')
                            ->label('კვარდრატული სურათი')
                            ->image()->imageEditor()
                            ->disk('public')->directory('projects/raw')
                            ->acceptedFileTypes(['image/jpeg','image/png','image/webp'])
                            ->maxSize(10240)
                            ->dehydrated(false)->live()
                            ->helperText('ავტომატური დამუშავება: thumbnail + popup + WebP'),

                        Forms\Components\Placeholder::make('current_image')
                            ->label('მიმდინარე სურათი')
                            ->content(fn ($record) => $record?->featured_image_thumbnail
                                ? new \Illuminate\Support\HtmlString('<img src="'.asset('storage/'.$record->featured_image_thumbnail).'" style="max-width:200px;border-radius:8px;">')
                                : 'სურათი არ არის')
                            ->visible(fn ($record) => $record !== null),

                        Forms\Components\TextInput::make('external_url')
                            ->label('გარე ბმული (URL)')
                            ->url()->maxLength(500),

                        Forms\Components\TextInput::make('partner')
                            ->label('პარტნიორი ორგანიზაცია')
                            ->maxLength(300),
                    ])->columns(1),
                ]),

                Forms\Components\Group::make()->columnSpan(1)->schema([

                    Forms\Components\Section::make('სტატუსი')->schema([
                        Forms\Components\Select::make('status')
                            ->label('სტატუსი')
                            ->options(['active' => '✅ აქტიური', 'completed' => '🏁 დასრულებული', 'draft' => '📝 მონახაზი'])
                            ->required()->default('active'),

                        Forms\Components\Toggle::make('is_featured')
                            ->label('მთავარ გვერდზე')->default(false),

                        Forms\Components\Toggle::make('is_active')
                            ->label('გამოქვეყნება')->default(true),
                    ]),

                    Forms\Components\Section::make('თარიღები')->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('დაწყების თარიღი')->native(false),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('დასრულების თარიღი')->native(false),
                    ]),

                    Forms\Components\Section::make('რიგი')->schema([
                        Forms\Components\TextInput::make('sort_order')
                            ->label('რიგის ნომერი')->integer()->default(0),
                    ]),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('featured_image_thumbnail')
                    ->label('')
                    ->disk('public')
                    ->width(60)->height(45)->rounded(),

                Tables\Columns\TextColumn::make('title')
                    ->label('სათაური')->searchable()->limit(60)->wrap(),

                Tables\Columns\TextColumn::make('partner')
                    ->label('პარტნიორი')->searchable()->limit(40),

                Tables\Columns\TextColumn::make('status')
                    ->label('სტ.')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active'    => 'success',
                        'completed' => 'primary',
                        'draft'     => 'secondary',
                        default     => 'gray',
                    }),

                Tables\Columns\IconColumn::make('is_featured')
                    ->label('მთ.გვ.')->boolean(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('რიგი')->sortable(),
            ])
            ->reorderable('sort_order')
            ->defaultSort('sort_order', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['active' => 'აქტიური', 'completed' => 'დასრ.', 'draft' => 'მონახ.']),
            ])
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
            'index'  => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit'   => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
