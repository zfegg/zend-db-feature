<?php

use Zfegg\Db\TableGateway\Factory\TableGatewayAbstractServiceFactory;

return [
    'service_manager' => [
        'abstract_factories' => [
            TableGatewayAbstractServiceFactory::class
        ]
    ]
];