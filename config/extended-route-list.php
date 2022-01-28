<?php

return [
    'action' => [
        'format' => env('EXT_ROUTE_LIST_ACTION_FORMAT', 'short'), // short|full
    ],
    'middleware' => [
        'format' => env('EXT_ROUTE_LIST_MIDDLEWARE_FORMAT', 'short'), // short|full
        'linestyle' => env('EXT_ROUTE_LIST_MIDDLEWARE_LINESTYLE', 'single'), // multi|single
    ],
    'columns' => [
        'Domain',
        'Method',
        'URI',
        'Name',
        'Action',
        'Middleware',
        'Package',
        'Author',
        'Version',
        'Since',
        'Access',
        'Link',
        'See',
        'Example',
        'Todo', // and Fixme
        'Deprecated',
        'Uses',
        'Param',
        'Return',
        'Throws',
        '@inheritdoc', // this should be a logic that loads this information from implements
        'License',
    ],
    'compact_columns' => [
        'Method',
        'Uri',
        'Action',
        'Middleware',
        'See',
    ],
    'normal_columns' => [
        'Domain',
        'Method',
        'Uri',
        'Name',
        'Action',
        'Middleware',
        'See',
    ],
    'all_columns' => [
        'Domain',
        'Method',
        'URI',
        'Name',
        'Action',
        'Middleware',
        'Package',
        'Author',
        'Version',
        'Since',
        'Access',
        'Link',
        'See',
        'Example',
        'Todo', // and Fixme
        'Deprecated',
        'Uses',
        'Param',
        'Return',
        'Throws',
        '@inheritdoc', 
        'License',
    ],
    
];