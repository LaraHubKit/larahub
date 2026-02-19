<?php

/**
 * LaraHub Filesystem Configuration
 *
 * Disks:
 * - local: Uses storage/public or storage/private (visibility)
 * - s3: AWS S3 bucket
 *
 * Usage:
 *   Storage::public()->disk('local')->folder('uploads')->put('file.jpg', $content);
 *   Storage::private()->disk('local')->folder('documents')->put('secret.pdf', $content);
 */

$basePath = dirname(__DIR__);

return [
    /*
    |--------------------------------------------------------------------------
    | Default Disk
    |--------------------------------------------------------------------------
    */
    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Disks
    |--------------------------------------------------------------------------
    */
    'disks' => [
        'local' => [
            'driver' => 'local',
            'root'   => $basePath . '/storage', // Base; actual path = root/public or root/private
        ],

        's3' => [
            'driver'                  => 's3',
            'key'                     => env('AWS_ACCESS_KEY_ID', ''),
            'secret'                  => env('AWS_SECRET_ACCESS_KEY', ''),
            'region'                  => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'bucket'                  => env('AWS_BUCKET', ''),
            'url'                     => env('AWS_URL'),
            'endpoint'                => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => filter_var(env('AWS_USE_PATH_STYLE_ENDPOINT', false), FILTER_VALIDATE_BOOLEAN),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Visibility Paths (relative to storage root)
    |--------------------------------------------------------------------------
    */
    'visibility' => [
        'public'  => 'public',
        'private' => 'private',
    ],
];
