<?php

namespace ZfeggTest\Db\TableGateway\Feature;

use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\RowGateway\RowGateway;
use Laminas\Db\Sql\Select;
use Laminas\Paginator\Paginator;
use Laminas\Stdlib\ArrayObject;
use ZfeggTest\Db\TableGateway\ContainerStatic;

/**
 * Class CommonCallFeatureTest
 * @package ZfeggTest\Db\TableGateway\Feature
 * @author Xiemaomao
 * @version $Id$
 */
class CommonCallFeatureTest extends \PHPUnit_Framework_TestCase
{

    /** @var \Laminas\Db\TableGateway\TableGateway */
    protected $table;

    public function setUp()
    {
        $this->table = ContainerStatic::getInstance()->get('TestTable');
    }

    public function testFind()
    {
        $table = $this->table;

        $this->assertNull($table->find('null'));
        $this->assertInstanceOf(RowGateway::class, $table->find(1));
    }

    public function testCreateAndRowGatewaySave()
    {
        /** @var RowGateway $row */
        $row = $this->table->create([
            'fullName' => 'Test',
            'email' => 'test@test.com',
        ]);

        $this->assertInstanceOf(RowGateway::class, $row);
        $this->assertEquals(1, $row->save());
    }

    public function testFetchPaginator()
    {
        /** @var \Laminas\Paginator\Paginator $paginator */
        $paginator = $this->table->fetchPaginator();

        $this->assertInstanceOf(Paginator::class, $paginator);
        $this->assertInstanceOf(ResultSet::class, $paginator->getCurrentItems());

        //Test where
        $paginator = $this->table->fetchPaginator(['id' => 1]);
        $this->assertEquals(1, $paginator->getTotalItemCount());
        $this->assertInstanceOf(ResultSet::class, $paginator->getCurrentItems());

        //Test select closure.
        $paginator = $this->table->fetchPaginator(function (Select $select) {
            $select->where(['id' => 1]);
        });
        $this->assertEquals(1, $paginator->getTotalItemCount());
        $this->assertInstanceOf(ResultSet::class, $paginator->getCurrentItems());
    }

    public function testFetchCount()
    {
        $this->assertGreaterThan(1, $this->table->fetchCount());
        //Test where args
        $this->assertEquals(0, $this->table->fetchCount(['id' => 0]));
    }

    public function testDeletePrimary()
    {
        /** @var RowGateway $row */
        $row = $this->table->create([
            'fullName' => 'Test',
            'email' => 'test@test.com',
        ]);
        $row->save();

        $this->assertEquals(1, $this->table->deletePrimary($row['id']));
    }

    public function testSave()
    {
        //Insert
        $data = new ArrayObject([
            'fullName' => 'Test',
            'email' => 'test@test.com',
        ]);
        $this->assertEquals(1, $this->table->save($data));
        $this->assertNotNull($data['id']);

        //Update
        $data = new ArrayObject([
            'id' => 1,
            'fullName' => 'TestUser' . rand(10, 99), //Random name
            'email' => 'test@test.com',
        ]);

        $this->assertEquals(1, $this->table->save($data));
    }
}
