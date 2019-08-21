<?php

namespace ZfeggTest\Db\TableGateway;

use Zend\Db\Adapter\AdapterAbstractServiceFactory;
use Zend\ServiceManager\ServiceManager;
use Zfegg\Db\TableGateway\Factory\TableGatewayAbstractServiceFactory;

/**
 * Class ContainerStatic
 * @package ZfeggTest\Db\TableGateway
 * @author Xiemaomao
 * @version $Id$
 */
class ContainerStatic
{
    protected static $container;

    public static function getInstance()
    {
        if (!self::$container) {
            self::$container = new ServiceManager();
            self::$container->configure([
                'abstract_factories' => [
                    TableGatewayAbstractServiceFactory::class,
                    AdapterAbstractServiceFactory::class,
                ]
            ]);
            self::$container->setService('config', [
                'tables' => [
                    'TestTable' => [
                        'table' => 'user',
//                    'invokable' => 'Mztgame\\Model\\OrderTable',
                        'row' => true,
                        'primary' => 'id',
                    ]
                ],
                'db' => [
                    'adapters' => [
                        'db' => [
                            'driver'  => 'pdo',
                            'dsn'     => 'sqlite:' . __DIR__ . '/data/user.db',
                            'charset' => 'utf8',
                        ],
                    ]
                ]
            ]);
        }

        return self::$container;
    }
}
