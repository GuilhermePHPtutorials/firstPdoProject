<?php

use Alura\Pdo\Domain\Model\Student;
require_once 'vendor/autoload.php';

$dbPath = __DIR__ . '/banco.sqlite';
$pdo = new PDO('sqlite:' . $dbPath);

$result = $pdo->query("SELECT * FROM students;");
var_dump($result->fetchAll()); // traz resultado como objeto (result[0].name) e array (result[0][1])