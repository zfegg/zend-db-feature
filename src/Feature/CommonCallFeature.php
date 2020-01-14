<?php

namespace Zfegg\Db\TableGateway\Feature;

use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\RowGateway\RowGatewayInterface;
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Where;
use Laminas\Db\TableGateway\Feature\AbstractFeature;
use Laminas\Paginator\Adapter\Callback;
use Laminas\Paginator\Adapter\DbSelect;
use Laminas\Paginator\Paginator;

/**
 * Class CommonCallFeature
 *
 * @author  moln.xie@gmail.com
 *
 * @method \Laminas\Db\Adapter\Adapter getAdapter
 */
class CommonCallFeature extends AbstractFeature
{
    protected $primary = [];

    public function __construct($primary)
    {
        $this->primary = (array)$primary;
    }

    public function getPrimary()
    {
        return $this->primary;
    }

    public function postInitialize()
    {
        $this->sql = $this->tableGateway->sql;
        $this->table = $this->tableGateway->getTable();
        $this->adapter = $this->tableGateway->getAdapter();
        $this->resultSetPrototype = $this->tableGateway->getResultSetPrototype();
    }

    /**
     * Find by primary
     *
     * @param $args
     *
     * @return array|\ArrayObject|null
     */
    public function find($args)
    {
        $where = [];
        foreach ($this->primary as $key => $primary) {
            $where[$primary] = isset($args[$key]) ? $args[$key] : null;
        }

        return $this->tableGateway->select($where)->current();
    }


    /**
     * Fetch Paginator
     *
     * @param DbSelect|Where|\Closure|string|array $args
     *
     * @return Paginator
     */
    public function fetchPaginator($args)
    {
        $where = null;
        if (isset($args[0])) {
            $where = $args[0];
        }

        if ($where instanceof DbSelect) {
            $adapter = $where;
        } else {
            $select = $this->sql->select();

            if ($where instanceof \Closure) {
                $where($select);
            } elseif ($where !== null) {
                $select->where($where);
            }

            $groupRawState = $select->getRawState(Select::GROUP);

            if ($groupRawState && count($groupRawState)) {
                $adapter = new DbSelect(
                    $select,
                    $this->getAdapter(),
                    $this->getResultSetPrototype()
                );
            } else {
                // "SELECT COUNT(1) FROM table" instead of default "SELECT COUNT(1) FROM (SELECT * FROM table)"
                $count = null;
                $adapter = new Callback(
                    function ($offset, $itemCountPerPage) use ($select) {
                        $select->offset($offset);
                        $select->limit($itemCountPerPage);

                        $statement =
                            $this->sql->prepareStatementForSqlObject($select);
                        $result = $statement->execute();

                        $resultSet = $this->getResultSetPrototype()
                            ? clone $this->getResultSetPrototype()
                            : new ResultSet();
                        $resultSet->initialize($result);

                        return $resultSet;
                    },
                    function () use ($select, &$count) {
                        if ($count === null) {
                            $select = clone $select;
                            $select->columns(
                                ['Zfegg_Db_Count' => new Expression('count(1)')]
                            );
                            $result = $this->sql
                                ->prepareStatementForSqlObject($select)
                                ->execute()
                                ->current();
                            $count = $result['Zfegg_Db_Count'];
                        }

                        return $count;
                    }
                );
            }
        }

        return new Paginator($adapter);
    }


    /**
     * Fetch Count
     *
     * @param array|Where $args
     *
     * @return int
     */
    public function fetchCount($args)
    {
        $where = null;
        if (isset($args[0])) {
            $where = $args[0];
        }

        $select = $this->sql->select();

        if ($where instanceof \Closure) {
            $where($select);
        } elseif ($where !== null) {
            $select->where($where);
        }

        $select->columns(['Zfegg_Db_Count' => new Expression('count(1)')]);
        $result = $this->sql->prepareStatementForSqlObject($select)
            ->execute()
            ->current();

        return $result['Zfegg_Db_Count'];
    }


    /**
     * Delete row by Primary.
     *
     * @param $args
     *
     * @return int
     */
    public function deletePrimary($args)
    {
        if (! isset($args[0])) {
            throw new \InvalidArgumentException(
                'Invalid deletePrimary argument.'
            );
        }

        $key = $args[0];

        return $this->tableGateway->delete([$this->primary[0] => $key]);
    }


    /**
     * @param $args
     *
     * @throws \RuntimeException
     * @return \Laminas\Db\RowGateway\RowGateway;
     */
    public function create(array $args)
    {
        if (! isset($args[0])) {
            throw new \InvalidArgumentException('Invalid create argument.');
        }

        $row = $args[0];

        $result = clone $this->tableGateway->getResultSetPrototype()
            ->getArrayObjectPrototype();
        if (! $result instanceof RowGatewayInterface) {
            throw new \RuntimeException(
                'ArrayObject Prototype is not instanceof RowGatewayInterface'
            );
        }
        $row && $result->populate(
            $row + $result->toArray(),
            isset($row[$this->getPrimary()[0]])
        );

        return $result;
    }


    /**
     * @param $args
     *
     * @return int
     * @throws \RuntimeException
     */
    public function save($args)
    {
        if (! isset($args[0])) {
            throw new \InvalidArgumentException('Invalid save argument.');
        }

        $data = $args[0];

        $insert = false;
        $where = [];

        $temp = $this->columns ? array_intersect_key(
            $data,
            array_flip($this->columns)
        ) : $data;
        if (method_exists($temp, 'getArrayCopy')) {
            $temp = $temp->getArrayCopy();
        } elseif (method_exists($temp, 'toArray')) {
            $temp = $temp->toArray();
        }

        if (empty($this->primary)) {
            throw new \RuntimeException(
                'Empty primary, can\'t use save() method.'
            );
        }

        foreach ($this->primary as $primary) {
            if (empty($temp[$primary])) {
                $insert = true;
                if (isset($temp[$primary])) {
                    unset($temp[$primary]);
                }
                break;
            }
            $where[$primary] = $temp[$primary];
            unset($temp[$primary]);
        }

        if ($insert) {
            $result = $this->tableGateway->insert($temp);

            if (count($this->primary) == 1
                && $id = $this->tableGateway->getLastInsertValue()
            ) {
                $data[$primary] = $this->tableGateway->getLastInsertValue();
            }
        } else {
            $result = $this->tableGateway->update((array)$temp, $where);
        }

        return $result;
    }

    public function getMagicMethodSpecifications()
    {
        return [
            'create',
            'deletePrimary',
            'fetchCount',
            'fetchPaginator',
            'find',
            'save',
        ];
    }
}
