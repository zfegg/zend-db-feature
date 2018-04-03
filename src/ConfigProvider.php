<?php


namespace Zfegg\Db\TableGateway;

class ConfigProvider
{
    public function __invoke()
    {
        $config = (new Module())->getConfig();
        $config['dependencies'] = $config['service_manager'];

        unset($config['service_manager']);

        return $config;
    }
}
