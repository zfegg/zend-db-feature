<?php

namespace ZfeggTest\Db\TableGateway\Factory;

use Laminas\Db\Adapter\AdapterAbstractServiceFactory;
use Laminas\Db\TableGateway\TableGateway;
use Laminas\ServiceManager\ServiceManager;
use Zfegg\Db\TableGateway\Factory\TableGatewayAbstractServiceFactory;
use ZfeggTest\Db\TableGateway\ContainerStatic;

/**
 * Class TestTableGatewayAbstractServiceFactory
 * @package ZfeggTest\Db\TableGateway\Factory
 * @author Xiemaomao
 * @version $Id$
 */
class TableGatewayAbstractServiceFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var  ServiceManager */
    protected $container;

    public function setUp()
    {
        $this->container = ContainerStatic::getInstance();
    }

    public function testCanCreateAndInvoke()
    {
        $this->assertTrue($this->container->has('TestTable'));
        $this->assertInstanceOf(TableGateway::class, $this->container->get('TestTable'));
    }

    public function testServiceManagerV2()
    {
        $asf = new TableGatewayAbstractServiceFactory();
        $this->assertTrue($asf->canCreateServiceWithName($this->container, '', 'TestTable'));
        $this->assertInstanceOf(TableGateway::class, $asf->createServiceWithName($this->container, '', 'TestTable'));
    }
}
