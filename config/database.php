<?php
if ($db_url = env('DATABASE_URL', false)) {
    $parts = parse_url($db_url);

    switch ($parts['scheme']) {
        case 'postgres':
        case 'pgsql':
            $_ENV['DB_CONNECTION'] = 'pgsql';
            break;
        case 'mysql':
            $_ENV['DB_CONNECTION'] = 'mysql';
            break;
        default:
            $_ENV['DB_CONNECTION'] = $parts['scheme'];
    }

    $_ENV['DB_HOST'] = $parts['host'];
    $_ENV['DB_PORT'] = $parts['port'];
    $_ENV['DB_USERNAME'] = $parts['user'];
    $_ENV['DB_PASSWORD'] = $parts['pass'];
    $_ENV['DB_DATABASE'] = substr($parts['path'], 1);
}

return [
    'fetch' => PDO::FETCH_CLASS,

    'default' => env('DB_CONNECTION', env('DB_CONNECTION')),

    'connections' => [

        'testing' => [
            'driver' => 'sqlite',
            'database' => ':memory:',
        ],

        'sqlite' => [
            'driver'   => 'sqlite',
            'database' => env('DB_DATABASE', base_path('database/database.sqlite')),
            'prefix'   => env('DB_PREFIX', ''),
        ],

        'pgsql' => [
            'driver'   => 'pgsql',
            'host'     => env('DB_HOST'),
            'port'     => env('DB_PORT'),
            'database' => env('DB_DATABASE'),
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
            'charset'  => 'utf8',
            'prefix'   => '',
            'schema'   => 'public',
        ],

    ],


    'migrations' => 'migrations',

    'redis' => [

        'client' => 'predis',

        'cluster' => env('REDIS_CLUSTER', false),

        'default' => [
            'host'     => env('REDIS_HOST', '127.0.0.1'),
            'port'     => env('REDIS_PORT', 6379),
            'database' => env('REDIS_DATABASE', 0),
            'password' => env('REDIS_PASSWORD', null),
        ],

    ],

];
