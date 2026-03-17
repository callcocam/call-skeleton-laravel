<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

return [
    'route_prefix' => 'planogram-package',
    'route_name_prefix' => 'planogram-package.',
    'route_middleware' => ['web', 'auth'],
    'legacy_mode' => true,
    'legacy_strategy' => 'parallel',
    'current_client_id' => null,
    'tenant_database' => null,
    'app_url' => null,
    'gondola_editor_route' => 'tenant.plannerates.editor.gondolas.edit',
    'controllers' => [
        'gondola_editor' => null,
        'product_details' => null,
        'product_image' => null,
        'gondola_analysis' => null,
        'gondola_export' => null,
    ],
];
