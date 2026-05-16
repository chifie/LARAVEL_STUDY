<?php
declare(strict_types=1);

class Enrollment
{
    public static function exists($studentId, $classId, $academicYearId, $termId)
    {
        $stmt = Database::query(
            'SELECT COUNT(*) AS total
             FROM enrollments
             WHERE student_id = :student_id
               AND class_id = :class_id
               AND academic_year_id = :academic_year_id
               AND term_id = :term_id',
            [
                ':student_id' => (int) $studentId,
                ':class_id' => (int) $classId,
                ':academic_year_id' => (int) $academicYearId,
                ':term_id' => (int) $termId,
            ]
        );

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return ((int) $row['total']) > 0;
    }

    public static function create(array $data)
    {
        Database::query(
            'INSERT INTO enrollments
            (student_id, class_id, academic_year_id, term_id, enrollment_date, status, created_at, updated_at)
            VALUES
            (:student_id, :class_id, :academic_year_id, :term_id, :enrollment_date, :status, NOW(), NOW())',
            [
                ':student_id' => (int) $data['student_id'],
                ':class_id' => (int) $data['class_id'],
                ':academic_year_id' => (int) $data['academic_year_id'],
                ':term_id' => (int) $data['term_id'],
                ':enrollment_date' => $data['enrollment_date'],
                ':status' => $data['status'],
            ]
        );

        return (int) Database::lastInsertId();
    }

    public static function historyByStudent($studentId)
    {
        $stmt = Database::query(
            'SELECT e.*, c.class_name, c.grade_level, c.stream,
                    ay.year_name, t.term_name
             FROM enrollments e
             INNER JOIN classes c ON c.id = e.class_id
             INNER JOIN academic_years ay ON ay.id = e.academic_year_id
             INNER JOIN terms t ON t.id = e.term_id
             WHERE e.student_id = :student_id
             ORDER BY ay.start_date DESC, t.start_date DESC, e.enrollment_date DESC, e.id DESC',
            [':student_id' => (int) $studentId]
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function latestByStudent($studentId)
    {
        $stmt = Database::query(
            'SELECT e.*, c.class_name, c.grade_level, c.stream,
                    ay.year_name, t.term_name
             FROM enrollments e
             INNER JOIN classes c ON c.id = e.class_id
             INNER JOIN academic_years ay ON ay.id = e.academic_year_id
             INNER JOIN terms t ON t.id = e.term_id
             WHERE e.student_id = :student_id
             ORDER BY e.id DESC
             LIMIT 1',
            [':student_id' => (int) $studentId]
        );

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }
}