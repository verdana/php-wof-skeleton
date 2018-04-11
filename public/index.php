<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';

// 依赖注入
$builder = new DI\ContainerBuilder();
$builder->useAutowiring(false);
$builder->useAnnotations(false);
$builder->addDefinitions([
    PhpWof\Database\Connection::class => DI\create()->constructor(
        'pgsql:host=localhost;dbname=postgres',
        'Verdana',
        'postgres'),

    Psr\Http\Message\ResponseInterface::class => function () {
        return new Zend\Diactoros\Response();
    },
]);
$container = $builder->build();

// 使用 fast-route 分配所有的请求
$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/', 'PhpWof\Controllers\IndexController');
});

// request handlers 容器
// 这里的三个参数会被用来创建路由器配置的类实例，也就是各种控制器
$requestHandlerContainer = new Middlewares\Utils\RequestHandlerContainer([
    $container->get(PhpWof\Database\Connection::class),
    $container->get(Psr\Http\Message\ResponseInterface::class)
]);

// Relay 以及中间件
$relay = new Relay\Relay([
	new Middlewares\FastRoute($dispatcher),
	new Middlewares\RequestHandler($requestHandlerContainer)
]);

$response = $relay->handle(Zend\Diactoros\ServerRequestFactory::fromGlobals());

// 输出
return (new Zend\Diactoros\Response\SapiEmitter())->emit($response);
