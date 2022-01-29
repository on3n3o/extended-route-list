<?php

return [

    'config' => [
        'action' => [
            'format' => env('EXT_ROUTE_LIST_ACTION_FORMAT', 'full'), // short|full
        ],
        'middleware' => [
            'format' => env('EXT_ROUTE_LIST_MIDDLEWARE_FORMAT', 'full'), // short|full
            'linestyle' => env('EXT_ROUTE_LIST_MIDDLEWARE_LINESTYLE', 'multi'), // multi|single
        ],
        'json' => [
            'drop_column_if_empty' => env('EXT_ROUTE_LIST_JSON_DROP_COLUMN_IF_EMPTY', true),
            'options' => env('EXT_ROUTE_LIST_JSON_OPTIONS', JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
        ],
    ],

    'casts' => [
        'package' => [
            'column_name' => 'package',
            'tags' => '@package',
            'linestyle' => 'single', // multi|single
        ],
        'author' => [
            'column_name' => 'author',
            'tags' => '@author',
            'linestyle' => 'single',
        ],
        'version' => [
            'column_name' => 'version',
            'tags' => '@version',
            'linestyle' => 'single',
        ],
        'since' => [
            'column_name' => 'since',
            'tags' => '@since',
            'linestyle' => 'single',
        ],
        'access' => [
            'column_name' => 'access',
            'tags' => '@access',
            'linestyle' => 'single',
        ],
        'link' => [
            'column_name' => 'link',
            'tags' => '@link',
            'linestyle' => 'single',
        ],
        'see' => [
            'column_name' => 'see',
            'tags' => '@see',
            'linestyle' => 'single',
        ],
        'example' => [
            'column_name' => 'example',
            'tags' => '@example',
            'linestyle' => 'single',
        ],
        'todo' => [
            'column_name' => 'todo',
            'tags' => ['@todo', '@fixme'],
            'linestyle' => 'multi',
        ],
        'deprecated' => [
            'column_name' => 'deprecated',
            'tags' => '@deprecated',
            'linestyle' => 'single',
        ],
        'uses' => [
            'column_name' => 'uses',
            'tags' => '@uses',
            'linestyle' => 'multi',
        ],
        'param' => [
            'column_name' => 'param',
            'tags' => '@param',
            'linestyle' => 'multi',
        ],
        'return' => [
            'column_name' => 'return',
            'tags' => '@return',
            'linestyle' => 'single',
        ],
        'throws' => [
            'column_name' => 'throws',
            'tags' => '@throws',
            'linestyle' => 'single',
        ],
        'inheritdoc' => [
            // This inheritdoc shoud be implemented as logic in the future
            // and not as a string in the docblock
            // when this tag is present in the docblock it should be
            // inherited from the parent docblock
            'column_name' => 'inheritdoc',
            'tags' => '@inheritdoc',
            'linestyle' => 'single',
        ],
        'licence' => [
            'column_name' => 'licence',
            'tags' => '@licence',
            'linestyle' => 'single',
        ],
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
