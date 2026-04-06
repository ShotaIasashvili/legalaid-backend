@php
    use Illuminate\Support\Facades\Storage;

    $assetUrl = fn(?string $path) => $path
        ? (Str::startsWith($path, ['http://', 'https://'])
            ? $path
            : asset('storage/' . $path))
        : null;

    $thumbUrl  = $assetUrl($thumbnail);
    $popupUrl  = $assetUrl($popup);
    $singleUrl = $assetUrl($single);
    $ogUrl     = $assetUrl($og ?? $single);
@endphp

<div class="space-y-4" x-data>

    {{-- ══════════════ Header ══════════════ --}}
    <div class="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-200">
        <x-heroicon-o-photo class="w-4 h-4" />
        სურათის გადახედვა / Image Preview
    </div>

    @if(!$thumbUrl && !$popupUrl)
        <div class="rounded-xl border-2 border-dashed border-gray-200 dark:border-gray-700 p-6 text-center text-sm text-gray-400 dark:text-gray-500">
            სურათი ჯერ არ არის ატვირთული. ატვირთეთ ფოტო, რომ გადახედვა ნახოთ.<br>
            <span class="text-xs opacity-70">No image uploaded yet — upload a photo to see previews.</span>
        </div>
    @else

        {{-- ══════════════ Preview Tabs ══════════════ --}}
        <div x-data="{ tab: 'card' }" class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden bg-white dark:bg-gray-900 shadow-sm">

            {{-- Tab nav --}}
            <div class="flex border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-xs font-medium overflow-x-auto">
                @foreach([
                    ['card',   'heroicon-o-squares-2x2', 'ბარათი / Card'],
                    ['list',   'heroicon-o-list-bullet',  'სია / List'],
                    ['popup',  'heroicon-o-arrow-top-right-on-square', 'Popup'],
                    ['hero',   'heroicon-o-photo',         'Hero / Single'],
                    ['og',     'heroicon-o-share',         'OG / Social'],
                ] as [$key, $icon, $label])
                <button type="button"
                    @click="tab = '{{ $key }}'"
                    :class="tab === '{{ $key }}'
                        ? 'border-b-2 border-primary-600 text-primary-600 dark:text-primary-400 bg-white dark:bg-gray-900'
                        : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'"
                    class="flex items-center gap-1.5 px-3 py-2.5 whitespace-nowrap transition-colors"
                >
                    <x-dynamic-component :component="$icon" class="w-3.5 h-3.5" />
                    {{ $label }}
                </button>
                @endforeach
            </div>

            {{-- Panels --}}
            <div class="p-4">

                {{-- ── Card preview (thumbnail 400×280) ── --}}
                <div x-show="tab === 'card'" x-cloak>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                        <span class="font-semibold text-gray-700 dark:text-gray-200">ბარათი</span>
                        — ზომა: <code class="bg-gray-100 dark:bg-gray-800 px-1 rounded">400 × 280 px</code>
                        — გამოიყენება სიახლეების ბარათებში
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-xl">
                        {{-- Card mock --}}
                        <div class="rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 shadow-sm bg-white dark:bg-gray-900">
                            @if($thumbUrl)
                                <img src="{{ $thumbUrl }}" alt="thumbnail" class="w-full h-36 object-cover" style="aspect-ratio:400/280">
                            @else
                                <div class="w-full h-36 bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-xs text-gray-400">No thumbnail</div>
                            @endif
                            <div class="p-3 space-y-1.5">
                                <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-2/3"></div>
                                <div class="h-2 bg-gray-100 dark:bg-gray-800 rounded w-full"></div>
                                <div class="h-2 bg-gray-100 dark:bg-gray-800 rounded w-4/5"></div>
                                <div class="flex gap-1 mt-2">
                                    <div class="h-2 bg-primary-100 dark:bg-primary-900 rounded-full px-4 w-12"></div>
                                    <div class="h-2 bg-gray-100 dark:bg-gray-800 rounded-full px-4 w-16"></div>
                                </div>
                            </div>
                        </div>
                        {{-- Actual image --}}
                        <div class="space-y-1.5">
                            <p class="text-xs text-gray-500 dark:text-gray-400">ფაქტობრივი thumbnail:</p>
                            <img src="{{ $thumbUrl }}" alt="thumbnail actual"
                                class="rounded-lg border border-gray-200 dark:border-gray-700 w-full object-cover"
                                style="max-height:160px; object-fit:cover;">
                            <p class="text-xs text-gray-400">400 × 280 px · cover crop</p>
                        </div>
                    </div>
                </div>

                {{-- ── List preview ── --}}
                <div x-show="tab === 'list'" x-cloak>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                        <span class="font-semibold text-gray-700 dark:text-gray-200">სია</span>
                        — გამოიყენება ჰორიზონტალური სიის ბარათებში
                    </p>
                    <div class="space-y-3 max-w-xl">
                        @foreach(range(1,2) as $i)
                        <div class="flex gap-3 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden bg-white dark:bg-gray-900 shadow-sm">
                            @if($thumbUrl)
                                <img src="{{ $thumbUrl }}" alt="list" class="w-28 h-20 object-cover shrink-0" style="aspect-ratio:400/280">
                            @else
                                <div class="w-28 h-20 bg-gray-200 dark:bg-gray-700 shrink-0"></div>
                            @endif
                            <div class="py-3 pr-3 space-y-1.5 flex-1">
                                <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded" style="width:{{ $i===1?'75%':'55%' }}"></div>
                                <div class="h-2 bg-gray-100 dark:bg-gray-800 rounded w-full"></div>
                                <div class="h-2 bg-gray-100 dark:bg-gray-800 rounded w-4/5"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- ── Popup preview (800×500) ── --}}
                <div x-show="tab === 'popup'" x-cloak>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                        <span class="font-semibold text-gray-700 dark:text-gray-200">Popup / Modal</span>
                        — ზომა: <code class="bg-gray-100 dark:bg-gray-800 px-1 rounded">800 × 500 px</code>
                        — გამოიყენება pop-up gallery-ში
                    </p>
                    @if($popupUrl)
                        <div class="rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 shadow-md bg-white dark:bg-gray-900 max-w-lg mx-auto">
                            {{-- Mock modal chrome --}}
                            <div class="flex items-center justify-between px-4 py-2.5 border-b border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800">
                                <div class="h-2.5 bg-gray-200 dark:bg-gray-700 rounded w-40"></div>
                                <div class="w-4 h-4 rounded-full bg-gray-200 dark:bg-gray-700"></div>
                            </div>
                            <img src="{{ $popupUrl }}" alt="popup" class="w-full object-cover" style="max-height:280px;object-fit:cover;">
                            <div class="p-4 space-y-2">
                                <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-3/4"></div>
                                <div class="h-2 bg-gray-100 dark:bg-gray-800 rounded w-full"></div>
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 text-center mt-2">800 × 500 px · cover crop</p>
                    @else
                        <div class="text-xs text-gray-400 p-4 text-center">popup სურათი ჯერ არ არის</div>
                    @endif
                </div>

                {{-- ── Hero / Single (1200×750) ── --}}
                <div x-show="tab === 'hero'" x-cloak>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                        <span class="font-semibold text-gray-700 dark:text-gray-200">Hero / სიახლის გვერდი</span>
                        — ზომა: <code class="bg-gray-100 dark:bg-gray-800 px-1 rounded">1200 × 750 px</code>
                        — სიახლის გვერდის hero სურათი
                    </p>
                    @if($singleUrl)
                        <div class="rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 shadow-md bg-white dark:bg-gray-900 max-w-lg mx-auto">
                            {{-- Mock browser bar --}}
                            <div class="flex items-center gap-1.5 px-3 py-2 bg-gray-100 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                                <div class="w-2.5 h-2.5 rounded-full bg-red-400"></div>
                                <div class="w-2.5 h-2.5 rounded-full bg-yellow-400"></div>
                                <div class="w-2.5 h-2.5 rounded-full bg-green-400"></div>
                                <div class="ml-2 h-3 bg-gray-200 dark:bg-gray-700 rounded flex-1 max-w-xs"></div>
                            </div>
                            <img src="{{ $singleUrl }}" alt="single" class="w-full object-cover" style="max-height:260px;object-fit:cover;">
                            <div class="p-4 space-y-2">
                                <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-2/3"></div>
                                <div class="h-2 bg-gray-100 dark:bg-gray-800 rounded w-full"></div>
                                <div class="h-2 bg-gray-100 dark:bg-gray-800 rounded w-5/6"></div>
                                <div class="h-2 bg-gray-100 dark:bg-gray-800 rounded w-4/5"></div>
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 text-center mt-2">1200 × 750 px · cover crop</p>
                    @else
                        <div class="text-xs text-gray-400 p-4 text-center">single სურათი ჯერ არ არის</div>
                    @endif
                </div>

                {{-- ── OG / Social (1200×630) ── --}}
                <div x-show="tab === 'og'" x-cloak>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                        <span class="font-semibold text-gray-700 dark:text-gray-200">OG / Social Share</span>
                        — ზომა: <code class="bg-gray-100 dark:bg-gray-800 px-1 rounded">1200 × 630 px</code>
                        — Facebook / Twitter / LinkedIn share card
                    </p>
                    @if($ogUrl)
                        {{-- Facebook card mock --}}
                        <div class="max-w-sm mx-auto rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 shadow-md bg-white dark:bg-gray-900">
                            <img src="{{ $ogUrl }}" alt="og" class="w-full object-cover" style="aspect-ratio:1200/630;max-height:190px;object-fit:cover;">
                            <div class="px-3 py-2.5 border-t border-gray-100 dark:border-gray-800">
                                <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">legalaid.ge</div>
                                <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-4/5 mb-1.5"></div>
                                <div class="h-2 bg-gray-100 dark:bg-gray-800 rounded w-full"></div>
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 text-center mt-2">1200 × 630 px · cover crop</p>
                    @else
                        <div class="text-xs text-gray-400 p-4 text-center">OG სურათი ჯერ არ არის</div>
                    @endif
                </div>

            </div>{{-- /panels --}}
        </div>{{-- /tabs --}}

        {{-- ══════════════ Size info grid ══════════════ --}}
        <div class="grid grid-cols-2 gap-2 text-xs">
            @foreach([
                ['label' => 'Thumbnail', 'size' => '400×280', 'use' => 'ბარათები', 'url' => $thumbUrl],
                ['label' => 'Popup',     'size' => '800×500', 'use' => 'Gallery modal', 'url' => $popupUrl],
                ['label' => 'Hero',      'size' => '1200×750','use' => 'სიახლის გვერდი', 'url' => $singleUrl],
                ['label' => 'OG',        'size' => '1200×630','use' => 'Social share', 'url' => $ogUrl],
            ] as $item)
            <div class="flex items-center gap-2 rounded-lg px-2.5 py-2 {{ $item['url'] ? 'bg-green-50 dark:bg-green-950/30 border border-green-200 dark:border-green-800' : 'bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700' }}">
                @if($item['url'])
                    <div class="w-1.5 h-1.5 rounded-full bg-green-500 shrink-0"></div>
                @else
                    <div class="w-1.5 h-1.5 rounded-full bg-gray-300 dark:bg-gray-600 shrink-0"></div>
                @endif
                <div>
                    <div class="font-semibold {{ $item['url'] ? 'text-green-700 dark:text-green-400' : 'text-gray-500 dark:text-gray-400' }}">
                        {{ $item['label'] }} <span class="font-normal opacity-70">({{ $item['size'] }})</span>
                    </div>
                    <div class="text-gray-400">{{ $item['use'] }}</div>
                </div>
            </div>
            @endforeach
        </div>

    @endif{{-- /if image --}}

</div>
