<?php
return [
    'jwt' => [
        'exclude' => [ // Excludes "query" to authenticate with JWT
            // '*', // all query not requires for JWT authenticate
            // '', // all empty query request
            // 'download/lists' // any query action with name = download/lists.  e.g:  find:download/lists, read:download/lists
            // 'find:download/lists', // specific to only "find:download/lists" not requires for JWT authenticate
            // 'download/*' // all name start with "download/" are not requires for JWT authenticate .  e.g:  download/lists, download/music
            // 'find:download/*', // query action = find and all name start with "download/" are not requires for JWT authenticate .  e.g:  find:download/lists, find:download/music
        ],
    ],
];
