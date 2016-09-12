<?php

namespace Zfegg\Db\TableGateway;


/**
 * Class Module
 * @package Zfegg\Db
 */
class Module
{

    public function getConfig()
    {
        return __DIR__ . '/../config/module.config.php';
    }
}