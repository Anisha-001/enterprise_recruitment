<?php

return [
    'broadcasting' => [
        'echo' => false,
        'pusher' => false,
    ],
    'default_filesystem_disk' => env('FILESYSTEM_DISK', 'public'),
    'assets_path' => null,
    'cache_path' => base_path('bootstrap/cache/filament'),
    'livewire_loading_delay' => 'default',
    'panels' => [
        'admin' => [
            'id' => 'admin',
            'path' => 'admin',
            'domain' => null,
            'brand_name' => 'Enterprise Recruitment',
            'brand_logo' => null,
            'brand_logo_height' => '2rem',
            'favicon' => null,
            'colors' => [
                'primary' => '#0f766e',
                'danger' => '#dc2626',
                'info' => '#0284c7',
                'success' => '#059669',
                'warning' => '#d97706',
                'gray' => '#64748b',
            ],
            'font' => 'Inter',
            'dark_mode' => true,
            'database_notifications' => true,
            'database_notifications_caching' => [
                'enabled' => true,
                'interval' => 30,
            ],
            'spa' => true,
            'sidebar_collapsible_on_desktop' => true,
            'navigation' => [
                'group' => 'top',
                'sort' => 1,
            ],
            'resources' => [
                'namespace' => 'App\Filament\Resources',
                'path' => app_path('Filament/Resources'),
            ],
            'pages' => [
                'namespace' => 'App\Filament\Pages',
                'path' => app_path('Filament/Pages'),
            ],
            'widgets' => [
                'namespace' => 'App\Filament\Widgets',
                'path' => app_path('Filament/Widgets'),
            ],
            'middleware' => [
                'auth' => [
                    'authenticate_session',
                    'encrypt_cookies',
                    'add_queued_cookies_to_response',
                    'start_session',
                    'share_errors_from_session',
                    'verify_csrf_token',
                    'subdomain_middleware',
                ],
                'base' => [
                    'encrypt_cookies',
                    'add_queued_cookies_to_response',
                    'start_session',
                    'share_errors_from_session',
                    'verify_csrf_token',
                ],
            ],
            'auth' => [
                'guard' => 'web',
                'pages' => [
                    'login' => \Filament\Pages\Auth\Login::class,
                ],
            ],
            'unsaved_changes_charts' => true,
            'global_search' => true,
            'global_search_debounce' => 400,
            'scrim' => true,
            'max_content_width' => 'full',
        ],
    ],
];
