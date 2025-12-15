<?php

return [
    'scheduler' => [
        'enabled' => env('CODY_SCHEDULER_ENABLED', true),
        'timezone' => env('CODY_TIMEZONE', 'UTC'),
    ],
];
