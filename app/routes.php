<?php

return [
    [
        'method' => 'GET',
        'route' => '/short-links',
        'target' => [\App\Controllers\ShortLinkController::class, 'index', true],
    ],
    [
        'method' => 'GET',
        'route' => '/short-links/(\d+)',
        'target' => [\App\Controllers\ShortLinkController::class, 'show', true],
    ],
    [
        'method' => 'POST',
        'route' => '/short-links',
        'target' => [\App\Controllers\ShortLinkController::class, 'create'],
    ],
    [
        'method' => 'PATCH',
        'route' => '/short-links/(\d+)',
        'target' => [\App\Controllers\ShortLinkController::class, 'update', true],
    ],
    [
        'method' => 'DELETE',
        'route' => '/short-links/(\d+)',
        'target' => [\App\Controllers\ShortLinkController::class, 'destroy', true],
    ],

    [
        'method' => 'GET',
        'route' => '/admins',
        'target' => [\App\Controllers\AdminController::class, 'index', true],
    ],
    [
        'method' => 'GET',
        'route' => '/admins/(\d+)',
        'target' => [\App\Controllers\AdminController::class, 'show', true],
    ],
    [
        'method' => 'POST',
        'route' => '/admins',
        'target' => [\App\Controllers\AdminController::class, 'create', true],
    ],
    [
        'method' => 'PATCH',
        'route' => '/admins/(\d+)',
        'target' => [\App\Controllers\AdminController::class, 'update', true],
    ],
    [
        'method' => 'DELETE',
        'route' => '/admins/(\d+)',
        'target' => [\App\Controllers\AdminController::class, 'destroy', true],
    ],

    [
        'method' => 'GET',
        'route' => '/clicks',
        'target' => [\App\Controllers\ClickController::class, 'index', true],
    ],

    [
        'method' => 'GET',
        'route' => '/([0-9a-zA-Z]+)',
        'target' => [\App\Controllers\HomeController::class, 'redirectByCode'],
    ],
    [
        'method' => 'GET',
        'route' => '/',
        'target' => [\App\Controllers\HomeController::class, 'index'],
    ],
];