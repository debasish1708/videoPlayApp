<?php

namespace App\Config;

return [
    'bunny' => [
        'access_key' => env('BUNNY_API_KEY', ''),
        'library_id' => env('BUNNY_LIBRARY_ID', ''),
        'base_url' => [
            'video' => 'https://video.bunnycdn.com/library/' . env('BUNNY_LIBRARY_ID', '')
        ]
    ]
];