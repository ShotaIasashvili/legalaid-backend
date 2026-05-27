<?php

return [
    'enabled' => (bool) env('INSTALLER_ENABLED', true),

    'marker_path' => env('INSTALLER_MARKER_PATH', storage_path('app/install/installed')),
];