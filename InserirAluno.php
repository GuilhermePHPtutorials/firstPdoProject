<?php

use Alura\Pdo\Domain\Model\Student;
require_once 'vendor/autoload.php';

$dbPath = __DIR__ . '/banco.sqlite';
$pdo = new PDO('sqlite:' . $dbPath);

$student = new Student(null, 'jane', new \DateTimeImmutable('1998-11-11'));

$sqlInsert = "INSERT INTO students (name, birth_date) VALUES (:name, :birth_date);";

$statement = $pdo->prepare($sqlInsert);
$statement->bindValue(':name', $student->name());
$statement->bindValue(':birth_date', $student->birthDate()->format('Y-m-d'));

if ($statement->execute()) {
    echo "Inserido com sucesso!";
}
