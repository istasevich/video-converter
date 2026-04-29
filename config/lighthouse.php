<?php

return [
    'route' => [
        'uri' => '/graphql',
        'name' => 'graphql',
        'middleware' => [],
    ],
    'schema_path' => base_path('graphql/schema.graphql'),
    'schema_cache' => [
        'enable' => env('LIGHTHOUSE_SCHEMA_CACHE_ENABLE', false),
        'path' => env('LIGHTHOUSE_SCHEMA_CACHE_PATH', base_path('bootstrap/cache/lighthouse-schema.php')),
    ],
    'namespaces' => [
        'models' => ['App', 'App\\Models'],
        'queries' => 'App\\Modules\\VideoConversion\\GraphQL\\Queries',
        'mutations' => 'App\\Modules\\VideoConversion\\GraphQL\\Mutations',
        'subscriptions' => 'App\\GraphQL\\Subscriptions',
        'interfaces' => 'App\\GraphQL\\Interfaces',
        'unions' => 'App\\GraphQL\\Unions',
        'scalars' => 'App\\GraphQL\\Scalars',
        'directives' => ['App\\GraphQL\\Directives'],
    ],
    'security' => [
        'max_query_complexity' => (int) env('LIGHTHOUSE_SECURITY_MAX_QUERY_COMPLEXITY', 1000),
        'max_query_depth' => (int) env('LIGHTHOUSE_SECURITY_MAX_QUERY_DEPTH',
    10),
        'disable_introspection' => (int) env('LIGHTHOUSE_SECURITY_DISABLE_INTROSPECTION', 0),
    ],
];
