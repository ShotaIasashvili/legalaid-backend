<?php

namespace App\Filament\Resources;

use App\Filament\Components\ImagePreviewPanel;
use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use App\Models\Category;
use App\Services\AdminDashboardMetrics;
use App\Services\ImageService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class PostResource extends AdminResource
{
    protected static ?string $model = Post::class;
    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    protected static ?string $navigationLabel = 'სიახლეები / News';
    protected static ?string $navigationGroup = 'კონტენტი';
    protected static ?int $navigationSort = 1;
    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make(3)->schema([

                // Left column (2/3 width)
                Forms\Components\Group::make()->columnSpan(2)->schema([

                    Forms\Components\Section::make('სათაური / Titles')->schema([
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
                            ->unique(Post::class, 'slug', ignoreRecord: true)
                            ->maxLength(500),

                        Forms\Components\Textarea::make('excerpt')
                            ->label('მოკლე აღწერა (Excerpt)')
                            ->rows(3)
                            ->maxLength(1000),
                    ]),

                    Forms\Components\Section::make('შინაარსი / Content')->schema([
                        Forms\Components\RichEditor::make('content')
                            ->label('სრული ტექსტი')
                            ->required()
                            ->toolbarButtons([
                                'attachFiles', 'blockquote', 'bold', 'bulletList',
                                'codeBlock', 'h2', 'h3', 'italic', 'link',
                                'orderedList', 'redo', 'strike', 'table', 'underline', 'undo',
                            ])
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('posts/attachments')
                            ->columnSpanFull(),
                    ]),

                    Forms\Components\Section::make('SEO')->schema([
                        Forms\Components\TextInput::make('seo_title')
                            ->label('SEO სათაური')
                            ->maxLength(70),
                        Forms\Components\Textarea::make('seo_description')
                            ->label('SEO აღწერა')
                            ->rows(2)
                            ->maxLength(160),
                    ])->collapsed(),
                ]),

                // Right column (1/3 width)
                Forms\Components\Group::make()->columnSpan(1)->schema([

                    Forms\Components\Section::make('გამოქვეყნება / Publishing')->schema([
                        Forms\Components\Select::make('status')
                            ->label('სტატუსი')
                            ->options([
                                'draft'     => '📝 მონახაზი (Draft)',
                                'published' => '✅ გამოქვეყნებული',
                                'scheduled' => '🕐 დაგეგმილი',
                            ])
                            ->required()
                            ->default('draft'),

                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('გამოქვეყნების თარიღი')
                            ->native(false)
                            ->displayFormat('d/m/Y H:i'),

                        Forms\Components\Toggle::make('is_featured')
                            ->label('მთავარ გვერდზე გამოტანა')
                            ->default(false),
                    ]),

                    Forms\Components\Section::make('კატეგორიები')->schema([
                        Forms\Components\Select::make('categories')
                            ->label('კატეგორია')
                            ->relationship('categories', 'name')
                            ->multiple()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')->required(),
                                Forms\Components\TextInput::make('slug')->required(),
                                Forms\Components\Hidden::make('type')->default('news'),
                            ]),
                    ]),

                    Forms\Components\Section::make('სურათი / Featured Image')->schema([

                        Forms\Components\FileUpload::make('_raw_image')
                            ->label('ფოტო ატვირთვა / Upload Photo')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                null,     // Free
                                '16:10',  // matches thumbnail (400×280)
                                '8:5',    // matches popup (800×500)
                                '8:5',    // matches single (1200×750)
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->imageEditorMode(2)
                            ->imageResizeMode('cover')
                            ->disk('public')
                            ->directory('posts/raw')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(15360)
                            ->helperText('ატვირთვის შემდეგ ავტომატურად შეიქმნება: thumbnail (400×280), popup (800×500), hero (1200×750), OG (1200×630) და WebP ვერსიები.')
                            ->dehydrated(false)
                            ->live(),

                        // Hidden fields so Filament carries existing paths through form state
                        Forms\Components\Hidden::make('featured_image'),
                        Forms\Components\Hidden::make('featured_image_thumbnail'),
                        Forms\Components\Hidden::make('featured_image_popup'),
                        Forms\Components\Hidden::make('featured_image_single'),
                        Forms\Components\Hidden::make('og_image'),
                        Forms\Components\Hidden::make('featured_image_webp'),
                        Forms\Components\Hidden::make('featured_image_thumbnail_webp'),

                        // Inline preview of current saved image (edit page only)
                        Forms\Components\Placeholder::make('_preview_saved')
                            ->label('')
                            ->content(function ($record) {
                                if (!$record?->featured_image_thumbnail_url) return '';
                                $items = [
                                        ['Thumbnail (400×280)', $record->featured_image_thumbnail_url, '400/280'],
                                        ['Popup (800×500)',     $record->featured_image_popup_url,     '800/500'],
                                ];
                                $html = '<div x-data="{tab:\'card\'}">';
                                // Tab bar
                                $html .= '<div class="flex gap-1 mb-3 flex-wrap">';
                                foreach ([
                                    ['card',  'ბარათი'],
                                    ['popup', 'Popup'],
                                    ['hero',  'Hero'],
                                    ['og',    'OG / Social'],
                                ] as [$key, $lbl]) {
                                    $html .= "<button type='button' @click=\"tab='{$key}'\"
                                        :class=\"tab==='{$key}' ? 'bg-primary-600 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700'\"
                                        class='text-xs px-3 py-1.5 rounded-lg font-medium transition-colors'>{$lbl}</button>";
                                }
                                $html .= '</div>';

                                $sizes = [
                                    'card'  => [$record->featured_image_thumbnail_url, '400×280', 'ბარათები', '400/280'],
                                    'popup' => [$record->featured_image_popup_url,     '800×500', 'Gallery modal', '800/500'],
                                    'hero'  => [$record->featured_image_single_url,    '1200×750','სიახლის გვერდი', '1200/750'],
                                    'og'    => [$record->og_image_url, '1200×630', 'Social share', '1200/630'],
                                ];
                                foreach ($sizes as $key => [$path, $dim, $desc, $ratio]) {
                                    $url = $path ?: null;
                                    $html .= "<div x-show=\"tab==='{$key}'\" x-cloak class='space-y-2'>";
                                    $html .= "<p class='text-xs text-gray-500 dark:text-gray-400'><strong>{$dim}</strong> — {$desc}</p>";
                                    if ($url) {
                                        $html .= "<img src='{$url}' class='rounded-lg w-full object-cover border border-gray-200 dark:border-gray-700' style='max-height:200px;aspect-ratio:{$ratio};object-fit:cover;'>";
                                    } else {
                                        $html .= "<div class='rounded-lg border-2 border-dashed border-gray-200 dark:border-gray-700 h-24 flex items-center justify-center text-xs text-gray-400'>სურათი არ არის</div>";
                                    }
                                    $html .= '</div>';
                                }
                                $html .= '</div>';
                                return new \Illuminate\Support\HtmlString($html);
                            })
                            ->visible(fn ($record) => $record?->featured_image_thumbnail_url !== null)
                            ->columnSpanFull(),
                    ]),

                    Forms\Components\Section::make('დამატებითი')->schema([
                        Forms\Components\TextInput::make('author')->label('ავტორი'),
                        Forms\Components\TextInput::make('source_url')->label('წყარო URL')->url(),
                    ])->collapsed(),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('featured_image_thumbnail_url')
                    ->label('ფოტო')
                    ->width(80)
                    ->height(55),

                Tables\Columns\TextColumn::make('title')
                    ->label('სათაური')
                    ->searchable()
                    ->sortable()
                    ->limit(60)
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('categories.name')
                    ->label('კატეგორია')
                    ->badge(),

                Tables\Columns\TextColumn::make('status')
                    ->label('სტ.')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'draft'     => 'warning',
                        'scheduled' => 'info',
                        default     => 'gray',
                    }),

                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean(),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('თარიღი')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('views')
                    ->label('ნახვები')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft'     => 'მონახაზი',
                        'published' => 'გამოქვეყნებული',
                        'scheduled' => 'დაგეგმილი',
                    ]),
                Tables\Filters\Filter::make('is_featured')
                    ->label('Featured')
                    ->query(fn (Builder $query) => $query->where('is_featured', true)),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('publish')
                        ->label('გამოქვეყნება')
                        ->icon('heroicon-o-check')
                        ->action(fn ($records) => $records->each->update([
                            'status'       => 'published',
                            'published_at' => now(),
                        ])),
                ]),
            ])
            ->defaultSort('published_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit'   => Pages\EditPost::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return app(AdminDashboardMetrics::class)->badge('posts_drafts', hideZero: true);
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
