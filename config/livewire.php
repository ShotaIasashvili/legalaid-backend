<?php

$config = require base_path('vendor/livewire/livewire/config/livewire.php');

$config['temporary_file_upload'] = array_merge($config['temporary_file_upload'] ?? [], [
    'disk' => env('LIVEWIRE_UPLOAD_DISK', 'local'),
    'rules' => ['required', 'file', 'max:61440'],
    'directory' => env('LIVEWIRE_UPLOAD_DIRECTORY', 'livewire-tmp'),
    'middleware' => env('LIVEWIRE_UPLOAD_MIDDLEWARE', 'throttle:240,1'),
    'max_upload_time' => (int) env('LIVEWIRE_UPLOAD_MAX_TIME', 20),
]);

$config['release_token'] = env('LIVEWIRE_RELEASE_TOKEN', '20260612-post-upload-fix');

return $config;
