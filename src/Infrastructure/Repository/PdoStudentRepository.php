<?php

namespace Alura\Pdo\Infrastructure\Repository;

use Alura\Pdo\Domain\Model\Student;
use Alura\Pdo\Domain\Repository\StudentRepositoryInterface;
use PDO;
use PDOStatement;

class PdoStudentRepository implements StudentRepositoryInterface
{
    public function __construct()
    {
        $this->connection = \Alura\Pdo\Infrastructure\Persistence\ConnecctionCreator::createConnection();
    }
    public function allStudents(): array
    {
        $sqlQuery = "SELECT * FROM students";
        $stmt = $this->connection->query($sqlQuery);

        return $this->hydrateStudentList($stmt);
    }

    public function studentBirthAt(\DateTimeInterface $birthDate): array
    {
        $sqlQuery = "SELECT * FROM students WHERE BirthDate = ?";
        $stmt = $this->connection->prepare($sqlQuery);
        $stmt->bindValue(1, $birthDate->format('Y-m-d'));
        $stmt->execute();

        return $stmt->hydrateStudentList($stmt);
    }

    public function hydrateStudentList(PDOStatement $stmt): array
    {
        $studentsDataList = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $students = [];

        foreach ($studentsDataList as $studentData) {
            $students[] = new Student(
                $studentData['id'],
                $studentData['name'],
                new \DateTimeImmutable($studentData['birthDate'])
            );
        }

        return $students;
    }

    public function save(Student $student): bool
    {
        if ($student->id() === null) {
            return $this->insert($student);
        }

        return $this->update($student);
    }
    private function insert(Student $student): bool
    {
        $insertQuery = 'INSERT INTO students (name, birth_date) VALUES (:name, :birth_date)';
        $stmt = $this->connection->prepare($insertQuery);

        $success = $stmt->execute([
            ':name' => $student->name(),
            ':birth_date' => $student->birthDate()->format('Y-m-d'),
        ]);
        $student->defineId($this->connection->lastInsertId());

        return $success;
    }

    private function update(Student $student): bool
    {
        $updateQuery = 'UPDATE students SET name = :name, birth_date = :birth_date WHERE id = :id';
        $stmt = $this->connection->prepare($updateQuery);
        $stmt->bindValue(':name', $student->name());
        $stmt->bindValue(':birth_date', $student->birthDate()->format('Y-m-d'));
        $stmt->bindValue(':id', $student->id());

        return $stmt->execute();
    }

    public function remove(Student $student): bool
    {
        $stmt = $this->connection->prepare("DELETE FROM students WHERE id = ?");
        $stmt->bindValue(1, $student->id(), PDO::PARAM_INT);

        return $stmt->execute();
    }
}