<?php

namespace Alura\Pdo\Infrastructure\Persistence;

use PDO;

class ConnecctionCreator
{
    public static function createConnection(): \PDO
    {
        $dbPath = __DIR__ . '/../../../banco.sqlite';
        return new PDO('sqlite:' . $dbPath);
    }
}