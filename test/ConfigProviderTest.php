<?php

namespace Zfegg\Db\TableGateway;


class ConfigProviderTest extends \PHPUnit_Framework_TestCase
{

    public function testInvoke()
    {
        $config = new ConfigProvider();
        $result = $config();

        $this->assertArrayHasKey('dependencies', $result);
        $this->assertArrayNotHasKey('service_manager', $result);
    }
}
