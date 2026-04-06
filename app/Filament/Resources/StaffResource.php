<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StaffResource\Pages;
use App\Models\Staff;
use App\Services\ImageService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class StaffResource extends Resource
{
    protected static ?string $model = Staff::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'თანამშრომლები / Staff';
    protected static ?string $navigationGroup = 'ორგანიზაცია';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make(3)->schema([
                Forms\Components\Group::make()->columnSpan(2)->schema([
                    Forms\Components\Section::make('ძირითადი')->schema([
                        Forms\Components\TextInput::make('name')->label('სახელი, გვარი')->required(),
                        Forms\Components\TextInput::make('position')->label('თანამდებობა')->required(),
                        Forms\Components\TextInput::make('department')->label('განყოფილება'),
                        Forms\Components\Select::make('type')
                            ->label('ტიპი')
                            ->options([
                                'director'          => 'დირექტორი',
                                'former_director'   => 'ყოფილი დირექტორი',
                                'honorary'          => 'საპატიო თანამშრომელი',
                                'council'           => 'საბჭოს წევრი',
                                'staff'             => 'თანამშრომელი',
                            ])
                            ->required()
                            ->default('staff'),
                    ]),

                    Forms\Components\Section::make('ბიოგრაფია')->schema([
                        Forms\Components\Textarea::make('bio')->label('მოკლე ბიო')->rows(3),
                        Forms\Components\RichEditor::make('full_bio')->label('სრული ბიოგრაფია'),
                    ]),

                    Forms\Components\Section::make('დამატებითი ინფო')->schema([
                        Forms\Components\Repeater::make('achievements')
                            ->label('მიღწევები')
                            ->simple(Forms\Components\Textarea::make('item'))
                            ->addActionLabel('+ მიღწევა'),
                        Forms\Components\Repeater::make('education')
                            ->label('განათლება')
                            ->schema([
                                Forms\Components\TextInput::make('institution')->label('დაწესებულება'),
                                Forms\Components\TextInput::make('degree')->label('ხარისხი'),
                                Forms\Components\TextInput::make('year')->label('წელი'),
                            ])
                            ->addActionLabel('+ განათლება'),
                        Forms\Components\Repeater::make('career')
                            ->label('კარიერა')
                            ->schema([
                                Forms\Components\TextInput::make('position')->label('თანამდებობა'),
                                Forms\Components\TextInput::make('organization')->label('ორგანიზაცია'),
                                Forms\Components\TextInput::make('period')->label('პერიოდი'),
                            ])
                            ->addActionLabel('+ კარიერა'),
                    ])->collapsed(),
                ]),

                Forms\Components\Group::make()->columnSpan(1)->schema([
                    Forms\Components\Section::make('ფოტო')->schema([
                        Forms\Components\FileUpload::make('_raw_photo')
                            ->label('ფოტო ატვირთვა')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios(['1:1', '3:4'])
                            ->disk('public')
                            ->directory('staff/raw')
                            ->maxSize(5120)
                            ->dehydrated(false)
                            ->live(),

                        Forms\Components\Placeholder::make('current_photo')
                            ->content(fn ($record) => $record?->photo_thumbnail
                                ? new \Illuminate\Support\HtmlString('<img src="' . asset('storage/' . $record->photo_thumbnail) . '" style="max-width:100%;border-radius:50%;">')
                                : 'ფოტო არ არის')
                            ->visible(fn ($record) => $record !== null),
                    ]),

                    Forms\Components\Section::make('საკონტაქტო')->schema([
                        Forms\Components\TextInput::make('email')->label('ელ-ფოსტა')->email(),
                        Forms\Components\TextInput::make('phone')->label('ტელ.'),
                        Forms\Components\DatePicker::make('from_date')->label('სამსახურის დაწყება'),
                        Forms\Components\DatePicker::make('to_date')->label('სამსახურის დასრულება'),
                    ]),

                    Forms\Components\Section::make('სტატუსი')->schema([
                        Forms\Components\Toggle::make('is_active')->label('აქტიური')->default(true),
                        Forms\Components\TextInput::make('sort_order')->label('თანმიმდევრობა')->numeric()->default(0),
                    ]),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\ImageColumn::make('photo_thumbnail')->label('')->disk('public')->circular()->width(45)->height(45),
            Tables\Columns\TextColumn::make('name')->label('სახელი')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('position')->label('თანამდებობა')->limit(40)->searchable(),
            Tables\Columns\BadgeColumn::make('type')->label('ტიპი')
                ->colors(['success' => 'director', 'warning' => 'council', 'info' => 'honorary', 'secondary' => 'staff']),
            Tables\Columns\IconColumn::make('is_active')->label('აქტ.')->boolean(),
            Tables\Columns\TextColumn::make('sort_order')->label('თანმ.')->sortable(),
        ])
        ->filters([
            Tables\Filters\SelectFilter::make('type')
                ->options(['director' => 'დირექტორი', 'former_director' => 'ყოფილი', 'honorary' => 'საპატიო', 'council' => 'საბჭო', 'staff' => 'თანამშრომელი']),
        ])
        ->actions([Tables\Actions\EditAction::make()])
        ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])])
        ->reorderable('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListStaff::route('/'),
            'create' => Pages\CreateStaff::route('/create'),
            'edit'   => Pages\EditStaff::route('/{record}/edit'),
        ];
    }
}
