Zend db features
=========================

## Installation / 安装

```
{
    "require": {
        "zfegg/zend-db-feature": "^1.0"
    }
}
```

## Usage / 使用说明

### TableGatewayAbstractServiceFactory 使用

`TableGatewayAbstractServiceFactory` 方便及明了的配置数据库和获取 `TableGateway` 对象.
下面介绍使用和配置


#### 1. 确认`ServiceManager` 中以下两个抽象工厂类，以`module.config.php`为例子：
 
```php
use Zfegg\Db\TableGateway\Factory\TableGatewayAbstractServiceFactory;
use Zend\Db\Adapter\AdapterAbstractServiceFactory;

return [
    'service_manager' => [
        'abstract_factories' => [
            TableGatewayAbstractServiceFactory::class,
            AdapterAbstractServiceFactory::class,
        ]
    ]
];
```

#### 2. 数据库配置,`Zend\Db\Adapter\AdapterAbstractServiceFactory` 实现了多DbAdapter(数据库适配器)的获取.

```php
return [
    'db' => [
        'adapters' => [
            'db' => [  //Default
              'driver'   => 'pdo_mysql',
              'host'     => 'localhost',
              'database' => 'test',
              'username' => 'root',
              'password' => '',
              'charset'  => 'utf8',
            ],
            'db.name1' => [
              'driver'   => 'pdo_mysql',
              'host'     => 'localhost',
              'database' => 'test',
              'username' => 'root',
              'password' => '',
              'charset'  => 'utf8',
            ],
            'db.name2' => [
              'driver'   => 'pdo_mysql',
              'host'     => 'localhost',
              'database' => 'test',
              'username' => 'root',
              'password' => '',
              'charset'  => 'utf8',
            ],
        ]
    ]
];
```

在 `ServiceManager` 中获取方式:

```php
$serviceManager->get('db');
$serviceManager->get('db.name1');
$serviceManager->get('db.name2');
```

#### 3. `TableGatewayAbstractServiceFactory`, 实现了多表的配置:

```php
return [
  'tables' => [
      'Demo1Table' => [
          'table' => 'users',  //Table name
          //'adapter' => 'db', //Set TableGateway adapter `$container->get($config['adapter'] ?: 'db'])`, Default 'db'
          //'schema' => 'test', //Set table schema
      ],
      'Demo2Table' => [
         'table' => 'users',  //Table name
         'invokable' => 'MyApp\\Model\\UserTable', //需要实例返回某个类，类必须是继承 `AbstractTableGateway`
      ],
      Demo3Table::class => [
          'table' => 'users',  //Table name
          'row' => true, //true will call `$table->getResultSetPrototype()->setArrayObjectPrototype(Zend\Db\RowGateway\RowGateway);`
          //'row' => 'MyApp\\Model\\UserEntity', //set custom ArrayObjectPrototype
          //'primary' => 'id',
      ]
  ],
];
```

在 `ServiceManager` 中获取方式:

```php
$serviceManager->get('Demo1Table'); //Instance of Zend\Db\TableGateway
$serviceManager->get('Demo2Table'); //Instance of MyApp\Model\UserTable
$serviceManager->get(Demo3Table::class); //Instance of Zend\Db\TableGateway
```

#### 4. 使用实例

~~~php
use Zfegg\Db\TableGateway\Factory\TableGatewayAbstractServiceFactory;
use Zend\Db\Adapter\AdapterAbstractServiceFactory;
use Zend\ServiceManager\ServiceManager;

$container = new ServiceManager(); //Zend ServiceManager v3
$container->configure([
  'abstract_factories' => [
      TableGatewayAbstractServiceFactory::class,
      AdapterAbstractServiceFactory::class,
  ]
]);
$container->setService('config', [
  'tables' => [
      'TestTable' => [
          'table' => 'user',
          //'invokable' => 'MyApp\\Model\\UserTable',
          'row' => true, //true will call `$table->getResultSetPrototype()->setArrayObjectPrototype(Zend\Db\RowGateway\RowGateway);`
          //'row' => 'MyApp\\Model\\UserEntity', //set custom ArrayObjectPrototype
          'primary' => 'id',
          //'schema' => 'test',
          //'adapter' => 'db', //Set TableGateway adapter `$container->get($config['adapter'] ?: 'db'])`
      ]
  ],
  'db' => [
      'adapters' => [
          'db' => [
              'driver'   => 'pdo_mysql',
              'host'     => 'localhost',
              'database' => 'test',
              'username' => 'root',
              'password' => '',
              'charset'  => 'utf8',
          ],
      ]
  ]
]);

var_dump($container->has('TestTable'));
$table = $this->container->get('TestTable'); //TableGateway object

//TableGateway CommonCallFeature
$table->find(1);  //SELECT `user`.* FROM `user` WHERE `id` = 1

//If config ['row' => true]
$rowGateway = $table->create(['fullName' => 'test', 'email' => 'test@test.com']);
$rowGateway->save();

//Fetch count
//SELECT count(1) AS `Zfegg_Db_Count` FROM `user` WHERE `email` = 'test@test.com'
$total = $table->fetchCount(['email' => 'test@test.com']);

//Fetch to Paginator object
/** @var \Zend\Paginator\Paginator $paginator */
$paginator = $table->fetchPaginator(['email' => 'test@test.com']);

//SELECT count(1) AS `Zfegg_Db_Count` FROM `user` WHERE `email` = 'test@test.com'
$paginator->getTotalItemCount()';

//SELECT `user`.* FROM `user` WHERE `email` = 'test@test.com' LIMIT 10 OFFSET 0
$paginator->getCurrentItems()';

//DELETE FROM `user` WHERE `id` = '1'
$table->deletePrimary(1);

//INSERT INTO `user` (`fullName`, `email`) VALUES ('test', 'test@test.com')
$table->save(['fullName' => 'test', 'email' => 'test@test.com']);

//UPDATE `user` SET `fullName` = 'test', `email` = 'test@test.com' WHERE `id` = 1
$table->save(['id' => 1, 'fullName' => 'test', 'email' => 'test@test.com']);
~~~

## License

See [MIT license File](LICENSE)
