<?php

use Alura\Pdo\Domain\Model\Student;
require_once 'vendor/autoload.php';

$dbPath = __DIR__ . '/banco.sqlite';
$pdo = new PDO('sqlite:' . $dbPath);

$result = $pdo->query("SELECT * FROM students;");
/* PDO::FETCH_ASSOC traz só as linhas do formato linha['nome_coluna']
 * o default é trazer tb no formato numérico linha[1],
 * tem como trazer de outras formas (objeto anônimo, já definindo classe etc)
 */
$studentDataList = $result->fetchAll(PDO::FETCH_ASSOC);
$studentList = [];
// pra usar construtor da classe é melhor pegar todos os dados e fazer um foreach com new pra cada linha
foreach ($studentDataList as $studentData) {
    $studentList[] = new Student(
        $studentData['id'],
        $studentData['name'],
        new \DateTimeImmutable($studentData['birth_date'])
    );
}

var_dump($studentList);