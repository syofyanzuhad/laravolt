<?php

return [
    'System' => [
        'order' => 99,
        'menu' => [
            'Users' => [
                'route' => 'epicentrum::users.index',
                'active' => 'epicentrum/users/*',
                'data' => [
                    'icon' => 'user-friends',
                    'permissions' => [\Laravolt\Platform\Enums\Permission::MANAGE_USER],
                ],
            ],
            'Roles' => [
                'route' => 'epicentrum::roles.index',
                'config' => 'epicentrum/roles/*',
                'data' => [
                    'icon' => 'user-astronaut',
                    'permissions' => [\Laravolt\Platform\Enums\Permission::MANAGE_ROLE],
                ],
            ],
            'Permissions' => [
                'route' => 'epicentrum::permissions.edit',
                'active' => 'epicentrum/permissions/*',
                'data' => [
                    'icon' => 'shield-check',
                    'permissions' => [\Laravolt\Platform\Enums\Permission::MANAGE_PERMISSION],
                ],
            ],
        ],
    ],
];
