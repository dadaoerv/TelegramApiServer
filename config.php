<?php

use danog\MadelineProto\Logger;
use TelegramApiServer\EventObservers\LogObserver;

$settings = [
    'server' => [
        'address' => (string)getenv('SERVER_ADDRESS'),
        'port' => (int)getenv('SERVER_PORT'),
    ],
    'telegram' => [
        'app_info' => [ // obtained in https://my.telegram.org
            'api_id' => (int)getenv('TELEGRAM_API_ID'),
            'api_hash' => (string)getenv('TELEGRAM_API_HASH'),
        ],
        'logger' => [ // Logger settings
            'type' => Logger::CALLABLE_LOGGER, //  0 - Logs disabled, 3 - echo logs.
            'extra' => LogObserver::log(...),
            'level' => (int)getenv('LOGGER_LEVEL'), // Logging level, available logging levels are: ULTRA_VERBOSE - 5, VERBOSE - 4 , NOTICE - 3, WARNING - 2, ERROR - 1, FATAL_ERROR - 0.
        ],
        'rpc' => [
            'flood_timeout' => 10,
            'rpc_drop_timeout' => 11,
        ],
        'connection' => [
            'max_media_socket_count' => 10
        ],
        'serialization' => [
            'interval' => 600,
        ],
        'db' => [
            'enable_min_db' => (bool)filter_var((string)getenv('DB_ENABLE_MIN_DATABASE'), FILTER_VALIDATE_BOOL),
            'enable_file_reference_db' => (bool)filter_var((string)getenv('DB_ENABLE_FILE_REFERENCE_DATABASE'), FILTER_VALIDATE_BOOL),
            'type' => (string)getenv('DB_TYPE'),
            getenv('DB_TYPE') => [
                'uri' => 'tcp://' . getenv('DB_HOST') . ':' . (int)getenv('DB_PORT'),
                'username' => (string)getenv('DB_USER'),
                'password' => (string)getenv('DB_PASSWORD'),
                'database' => (string)getenv('DB_DATABASE'),
                'max_connections' => (int)getenv('DB_MAX_CONNECTIONS'),
                'idle_timeout' => (int)getenv('DB_IDLE_TIMEOUT'),
                'cache_ttl' => (string)getenv('DB_CACHE_TTL'),
                'serializer' => danog\MadelineProto\Settings\Database\SerializerType::from('serialize'),
            ]
        ],
        'files' => [
            'report_broken_media' => false,
        ],
    ],
    'api' => [
        'ip_whitelist' => array_filter(
            array_map(
                'trim',
                explode(',', (string)getenv('IP_WHITELIST'))
            )
        ),
        'bulk_interval' => (float)getenv('REQUESTS_BULK_INTERVAL')
    ],
    'health_check' => [
        'enabled' => (bool)filter_var((string)getenv('HEALTHCHECK_ENABLED'), FILTER_VALIDATE_BOOL),
        'interval' => ((int)getenv('HEALTHCHECK_INTERVAL') ?: 30),
        'timeout' => ((int)getenv('HEALTHCHECK_REQUEST_TIMEOUT') ?: 60),
    ]
];

if (empty($settings['telegram']['app_info']['api_id'])) {
    throw new InvalidArgumentException('Need to fill TELEGRAM_API_ID in .env.docker or .env');
}

return $settings;