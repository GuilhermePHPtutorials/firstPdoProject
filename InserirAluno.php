<?php

use Alura\Pdo\Domain\Model\Student;
require_once 'vendor/autoload.php';

$dbPath = __DIR__ . '/banco.sqlite';
$pdo = new PDO('sqlite:' . $dbPath);

$student = new Student(null, 'Guilherme', new \DateTimeImmutable('1900-11-11'));

$sqlInsert = "INSERT INTO students (name, birth_date) VALUES ('{$student->name()}', '{$student->birthDate()->format('Y-m-d')}');";
echo $sqlInsert;

var_dump($pdo->exec($sqlInsert));