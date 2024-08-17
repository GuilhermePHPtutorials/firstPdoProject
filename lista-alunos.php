<?php

use Alura\Pdo\Domain\Model\Student;
use Alura\Pdo\Infrastructure\Persistence\ConnecctionCreator;
use Alura\Pdo\Infrastructure\Repository\PdoStudentRepository;

require_once 'vendor/autoload.php';

$pdo = \Alura\Pdo\Infrastructure\Persistence\ConnecctionCreator::createConnection();

$pdo = ConnecctionCreator::createConnection();
$repository = new PdoStudentRepository($pdo);

try {
    $studentList = $repository->allStudents();
    var_dump($studentList);
} catch (Exception $e) {
    echo $e->getMessage();
}

