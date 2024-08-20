<?php

namespace Alura\Pdo\Infrastructure\Repository;

use Alura\Pdo\Domain\Model\Phone;
use Alura\Pdo\Domain\Model\Student;
use Alura\Pdo\Domain\Repository\StudentRepositoryInterface;
use Exception;
use http\Exception\RuntimeException;
use PDO;
use PDOStatement;
use Alura\Pdo\Infrastructure\Persistence\ConnecctionCreator;

class PdoStudentRepository implements StudentRepositoryInterface
{
    /**
     * @var mixed|PDO|null
     */
    private $connection;

    public function __construct($connection = null)
    {
        if ($connection === null) {
            $connection = ConnecctionCreator::createConnection();
        }
        $this->connection = $connection;
    }

    /**
     * @throws Exception
     */
    public function allStudents(): array
    {
        $sqlQuery = "SELECT * FROM students";
        $stmt = $this->connection->query($sqlQuery);

        return $this->hydrateStudentList($stmt);
    }

    /**
     * @throws Exception
     */
    public function studentBirthAt(\DateTimeInterface $birthDate): array
    {
        $sqlQuery = "SELECT * FROM students WHERE BirthDate = ?";
        $stmt = $this->connection->prepare($sqlQuery);
        $stmt->bindValue(1, $birthDate->format('Y-m-d'));
        $stmt->execute();

        return $this->hydrateStudentList($stmt);
    }

    /**
     * @throws Exception
     */
    public function hydrateStudentList(PDOStatement $stmt): array
    {
        $studentsDataList = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $students = [];

        foreach ($studentsDataList as $studentData) {
            $student = new Student(
                $studentData['id'],
                $studentData['name'],
                new \DateTimeImmutable($studentData['birth_date'])
            );
            //$this->fillPhonesOf($student);
            $students[] = $student;
        }

        return $students;
    }

    private function fillPhonesOf(Student $student): void
    {
        $sqlQuery = "SELECT id , area_code, number FROM phones WHERE student_id = ?";
        $stmt = $this->connection->prepare($sqlQuery);
        $stmt->bindValue(1, $student->id(), PDO::PARAM_INT);
        $stmt->execute();

        $phonesDataList = $stmt->fetchAll();
        foreach ($phonesDataList as $phoneData) {
            $phone = new Phone(
                $phoneData['id'],
                $phoneData['area_code'],
                $phoneData['number']
            );
            $student->addPhone($phone);
        }
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
        if ($stmt === false) {
            throw new RuntimeException($this->connection->errorInfo()[2]);
        }

        $success = $stmt->execute([
            ':name' => $student->name(),
            ':birth_date' => $student->birthDate()->format('Y-m-d'),
        ]);
        if ($success === false) {
            throw new RuntimeException($stmt->errorInfo()[2]);
        }

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

    /**
     * @throws Exception
     */
    public function allStudentsWithPhones(): array
    {
        $sqlQuery = 'SELECT students.id,
                            students.name,
                            students.birth_date,
                            phones.id AS phone_id,
                            phones.area_code,
                            phones.number
                     FROM students
                     JOIN phones ON students.id = phones.student_id;';
        $stmt = $this->connection->query($sqlQuery);
        $result = $stmt->fetchAll();

        $students = [];
        foreach ($result as $row) {
            if (!array_key_exists($row['id'], $students)) {
                $students[$row['id']] = new Student(
                    $row['id'],
                    $row['name'],
                    new \DateTimeImmutable($row['birth_date'])
                );
            }
            $phone = new Phone($row['id'], $row['area_code'], $row['number']);
            $students[$row['id']]->addPhone($phone);
        }

        return $students;
    }
}