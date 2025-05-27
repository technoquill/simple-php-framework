<?php
declare(strict_types=1);

use App\Providers\HelperServiceProvider;
use Technoquill\Framework\View\View;


return [
    'app' => [
        'app_version' => app_version(),
        'app_name' => env('APP_NAME', 'Simple PHP Framework'),
        'app_url' => env('APP_URL', 'http://localhost'),
        'app_env' => env('APP_ENV', 'dev'),
        'app_debug' => env('APP_DEBUG', false),
        'app_path' => base_path(),

        'app_charset' => 'utf-8',
        'app_lang' => 'en',
        'app_timezone' => 'Europe/Berlin',

        'app_log_path' => base_path() . '/runtime/logs',
        'app_cache_path' => base_path() . '/runtime/cache',
        'app_session_path' => base_path() . '/runtime/session',

        'app_log_enable' => true,
        'app_log_type' => 'file',
        'app_log_level' => 'debug',
        'app_log_file' => 'app',
        'app_log_date_format' => 'Y-m-d H:i:s',
        'app_log_file_extension' => '.log',
        'app_log_format' => '%date% %type% %content%',
        'app_log_max_files' => 30,
        'app_log_max_size' => 2097152,
    ],
    'db' => [
        'default' => env('DB_CONNECTION', 'mysql'),
        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', ''),
            'username' => env('DB_USERNAME', ''),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => [],
        ],
        'pgsql' => [
            'driver' => 'pgsql',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', ''),
            'username' => env('DB_USERNAME', ''),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'schema' => 'public',
            'sslmode' => 'prefer',
        ],
    ],
    'mailer' => [
        'default' => env('MAIL_MAILER', 'smtp'),
        'smtp' => [
            'transport' => 'smtp',
            'host' => env('MAIL_HOST', 'smtp.mailgun.org'),
            'port' => env('MAIL_PORT', 587),
            'encryption' => env('MAIL_ENCRYPTION', 'tls'),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'timeout' => null,
            'auth_mode' => null,
        ],
        'sendmail' => [
            'transport' => 'sendmail',
            'path' => '/usr/sbin/sendmail -bs',
        ],
        'from' => [
            'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
            'name' => env('MAIL_FROM_NAME', ''),
        ],

    ],
    'view' => [
        'handler' => View::class,
        'templates' => [
            'default' => [
                'template_path' => base_path() . '/resources/templates/default',
                'views_path' => base_path() . '/resources/templates/default/views',
                'layouts_path' => base_path() . '/resources/templates/default/layouts',
                'assets_path' => base_path() . '/resources/templates/default/config',
                'errors_path' => base_path() . '/resources/templates/default/views/errors',
                'layout' => 'default',
                'use_layout' => true,
                'cache' => false,
            ],
            'admin' => [
                'template_path' => base_path() . '/resources/templates/admin',
                'views_path' => base_path() . '/resources/templates/admin/views',
                'layouts_path' => base_path() . '/resources/templates/admin/layouts',
                'assets_path' => base_path() . '/resources/templates/admin/config',
                'errors_path' => base_path() . '/resources/templates/admin/views/errors',
                'layout' => 'admin',
                'use_layout' => true,
                'cache' => false,
            ],
        ],
        'resolver' => [
            '/*' => 'default',
            '/admin/*' => 'admin',
        ],
        'cache_path' => base_path() . '/runtime/cache/views',
        'cache_lifetime' => 3600,
        'cache_extension' => '.php',
    ],
    'asset' => [
        'timestamp' => true,
        'cache' => false
    ],
    'storage' => [
        'default' => 'local',
        'disks' => [
            'local' => [
                'driver' => 'local',
                'root' => base_path() . '/storage/files',
                'url' => null,
                'visibility' => 'private',
            ],
            'public' => [
                'driver' => 'local',
                'root' => base_path() . '/public/uploads',
                'url' => '/uploads',
                'visibility' => 'public',
            ],
        ],
        'links' => [
            'public/assets' => 'resources/assets'
        ],
    ],
    'services' => [
        HelperServiceProvider::class
    ],

];
