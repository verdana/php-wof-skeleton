<?php
declare (strict_types = 1);

namespace PhpWof\Database;

use PDO;
use PDOException;

class Connection
{
    private $params;
    private $options;
    private $pdo;

    public function __construct(string $dsn, string $user, string $password, array $options = null)
    {
        $this->params = [$dsn, $user, $password];
        $this->options = (array) $options;

        if (empty($options['lazy'])) {
            $this->connect();
        }
    }

    public function connect(): void
    {
        if ($this->pdo) {
            return;
        }

        try {
            $this->pdo = new PDO($this->params[0], $this->params[1], $this->params[2], $this->options);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function disconnect(): void
    {
        $this->pdo = null;
    }

    public function getPDO(): PDO
    {
        return $this->pdo;
    }
}
