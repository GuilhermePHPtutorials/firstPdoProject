<?php

use Alura\Pdo\Domain\Model\Student;
use Alura\Pdo\Infrastructure\Persistence\ConnecctionCreator;
use Alura\Pdo\Infrastructure\Repository\PdoStudentRepository;

require_once 'vendor/autoload.php';

$connection = ConnecctionCreator::createConnection();
$studentRepository = new PdoStudentRepository($connection);

$connection->beginTransaction();
$aStudent = new Student(
    null,
    'Nico Steppat',
    new DateTimeImmutable('1988-08-08')
);
$studentRepository->save($aStudent);

$anotherStudent = new Student(
    null,
    'Sergio Steppat',
    new DateTimeImmutable('1987-07-07')
);
$studentRepository->save($anotherStudent);

$connection->commit();