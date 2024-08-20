<?php

use Alura\Pdo\Infrastructure\Persistence\ConnecctionCreator;
use Alura\Pdo\Infrastructure\Repository\PdoStudentRepository;

require_once "vendor/autoload.php";

$connection = ConnecctionCreator::createConnection();
$repository = new PdoStudentRepository($connection);

$studentsWithPhones = $repository->allStudentsWithPhones();
$allStudents = $repository->allStudents();

var_dump($studentsWithPhones);

$sizeAll = count($allStudents);
$sizePhones = count($studentsWithPhones);
echo "total de alunos: {$sizeAll}, com telefone: {$sizePhones}<br>}";