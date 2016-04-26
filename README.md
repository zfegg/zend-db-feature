Zend db features
=========================

## Installation using Composer

```
{
    "require": {
        "zfegg/zend-db-feature": "^1.0"
    }
}
```

## Usage

### TableGatewayAbstractServiceFactory

~~~php
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
