<?php

return [
    'middleware' => [
        'format' => 'short', // short|full
        'linestyle' => 'single', // multi, single
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

        // 'Package',
        // 'Author',
        // 'Version',
        // 'Since',
        // 'Access',
        // 'Link',
        // 'See',
        // 'Example',

        // 'Todo', // and Fixme
        // 'Deprecated',
        // 'Uses',
        // 'Param',
        // 'Return',
        // 'Throws',
        // '@inheritdoc', // this should be a logic that loads this information from implements
        // 'License',

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
        '@inheritdoc', // this should be a logic that loads this information from implements
        'License',
    ],
    
];