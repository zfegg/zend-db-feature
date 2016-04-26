<?php

namespace ZfeggTest\Db\TableGateway\Factory;

use Zend\Db\Adapter\AdapterAbstractServiceFactory;
use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\ServiceManager;
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