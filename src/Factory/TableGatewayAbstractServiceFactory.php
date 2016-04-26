<?php

namespace Zfegg\Db\TableGateway\Factory;

use Interop\Container\ContainerInterface;
use Zend\Db\Sql\TableIdentifier;
use Zend\Db\TableGateway\Feature\FeatureSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zfegg\Db\TableGateway\Feature\CommonCallFeature;

/**
 * Class TableGatewayAbstractServiceFactory
 * @author moln.xie@gmail.com
 */
class TableGatewayAbstractServiceFactory implements AbstractFactoryInterface
{
    private $config;

    public function getConfig()
    {
        return $this->config;
    }

    /**
     * {@inheritdoc}
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return $this->canCreate($serviceLocator, $requestedName);
    }

    /**
     * {@inheritdoc}
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return $this($serviceLocator, $requestedName);
    }

    /**
     * {@inheritdoc}
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        if (strtolower(substr($requestedName, -5)) != 'table') {
            return false;
        }

        if (empty($this->config)) {
            $config = $container->get('config');
            $this->config = isset($config['tables']) ? $config['tables'] : [];
        }

        if (!isset($this->config[$requestedName])) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $this->config[$requestedName];
        $dbAdapter = $container->get(isset($config['adapter']) ? $config['adapter'] : 'db');

        if (isset($config['schema'])) {
            $config['table'] = new TableIdentifier($config['table'], $config['schema']);
        }


        $featureSet = new FeatureSet();
        $featureSet->addFeature(new CommonCallFeature($config['primary']));

        if (isset($config['invokable'])) {
            $config['class'] = $config['invokable'];
        }
        if (isset($config['class'])) {
            if (!class_exists($config['class'])) {
                throw new \RuntimeException("Class '{$config['class']}' not found ");
            }

            /** @var \Zend\Db\TableGateway\TableGateway $table */
            $table = new $config['class']($config['table'], $dbAdapter, $featureSet);
        } else {
            $table = new TableGateway($config['table'], $dbAdapter, $featureSet);
        }

        if (isset($config['row'])) {
            if ($config['row'] === true) {
                $config['row'] = 'Zend\Db\RowGateway\RowGateway';
            }

            if (is_string($config['row'])) {
                if (!class_exists($config['row'])) {
                    throw new \RuntimeException("Class '{$config['row']}' not found ");
                }

                $rowGatewayPrototype = new $config['row'](
                    $config['primary'],
                    $config['table'],
                    $dbAdapter, $table->getSql()
                );
            } elseif (is_object($config['row'])) {
                $rowGatewayPrototype = $config['row'];
            } else {
                throw new \InvalidArgumentException('Error row argument');
            }

            $table->getResultSetPrototype()->setArrayObjectPrototype($rowGatewayPrototype);
        }

        return $table;
    }
}
