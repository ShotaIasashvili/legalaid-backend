<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Image Processing Configuration
    |--------------------------------------------------------------------------
    */

    'driver' => env('IMAGE_DRIVER', 'gd'), // 'gd' or 'imagick'

    'sizes' => [
        'thumbnail' => [
            'width'   => (int) env('IMAGE_THUMBNAIL_WIDTH', 400),
            'height'  => (int) env('IMAGE_THUMBNAIL_HEIGHT', 280),
        ],
        'popup' => [
            'width'   => (int) env('IMAGE_POPUP_WIDTH', 800),
            'height'  => (int) env('IMAGE_POPUP_HEIGHT', 500),
        ],
        'single' => [
            'width'   => (int) env('IMAGE_SINGLE_WIDTH', 1200),
            'height'  => (int) env('IMAGE_SINGLE_HEIGHT', 750),
        ],
        'og' => [
            'width'  => 1200,
            'height' => 630,
        ],
    ],

    'quality' => (int) env('IMAGE_QUALITY', 82),

    // Whether to also output WebP versions alongside originals
    'generate_webp' => true,

    // Max file size (in KB) to accept for upload
    'max_upload_kb' => 10240, // 10 MB

    // Allowed MIME types
    'allowed_types' => [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
    ],
];
