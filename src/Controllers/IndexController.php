<?php
declare(strict_types=1);

namespace PhpWof\Controllers;

use PhpWof\Database\Connection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class IndexController
{
    private $connection;
    private $response;

    public function __construct(Connection $conn, ResponseInterface $response)
    {
        $this->connection = $conn;
        $this->response = $response;
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        // $_GET
        var_dump($request->getQueryParams());
        // $_POST
        var_dump($request->getParsedBody());
        // 路由参数
        var_dump($request->getAttributes());

        // 操作数据库
        $pdo = $this->connection->getPDO();
        $sth = $pdo->query('SELECT version()');
        ['version' => $version] = $sth->fetch(\PDO::FETCH_ASSOC);
        var_dump($version);

        return $this->response;
    }
}
