<?php
declare(strict_types=1);

class ClassSubject
{
    public static function allWithRelations()
    {
        $stmt = Database::query(
            'SELECT cs.*, c.class_name, s.subject_name, s.subject_code, ay.year_name, t.staff_number, u.full_name AS teacher_name, term.term_name
             FROM class_subjects cs
             INNER JOIN classes c ON c.id = cs.class_id
             INNER JOIN subjects s ON s.id = cs.subject_id
             INNER JOIN academic_years ay ON ay.id = cs.academic_year_id
             INNER JOIN terms term ON term.id = cs.term_id
             LEFT JOIN teachers t ON t.id = cs.teacher_id
             LEFT JOIN users u ON u.id = t.user_id
             ORDER BY ay.start_date DESC, c.class_name ASC, s.subject_name ASC'
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function allForUser($userId)
    {
        $teacher = Teacher::findByUserId($userId);

        if (!$teacher) {
            return self::allWithRelations();
        }

        $stmt = Database::query(
            'SELECT cs.*, c.class_name, s.subject_name, s.subject_code, ay.year_name, t.staff_number, u.full_name AS teacher_name, term.term_name
             FROM class_subjects cs
             INNER JOIN classes c ON c.id = cs.class_id
             INNER JOIN subjects s ON s.id = cs.subject_id
             INNER JOIN academic_years ay ON ay.id = cs.academic_year_id
             INNER JOIN terms term ON term.id = cs.term_id
             LEFT JOIN teachers t ON t.id = cs.teacher_id
             LEFT JOIN users u ON u.id = t.user_id
             WHERE cs.teacher_id = :teacher_id
             ORDER BY ay.start_date DESC, c.class_name ASC, s.subject_name ASC',
            [':teacher_id' => (int) $teacher['id']]
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find($id)
    {
        $stmt = Database::query(
            'SELECT cs.*, c.class_name, s.subject_name, s.subject_code, ay.year_name, term.term_name, t.staff_number, u.full_name AS teacher_name
             FROM class_subjects cs
             INNER JOIN classes c ON c.id = cs.class_id
             INNER JOIN subjects s ON s.id = cs.subject_id
             INNER JOIN academic_years ay ON ay.id = cs.academic_year_id
             INNER JOIN terms term ON term.id = cs.term_id
             LEFT JOIN teachers t ON t.id = cs.teacher_id
             LEFT JOIN users u ON u.id = t.user_id
             WHERE cs.id = :id
             LIMIT 1',
            [':id' => (int) $id]
        );

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public static function exists($classId, $subjectId, $academicYearId, $termId)
    {
        $stmt = Database::query(
            'SELECT COUNT(*) AS total
             FROM class_subjects
             WHERE class_id = :class_id
               AND subject_id = :subject_id
               AND academic_year_id = :academic_year_id
               AND term_id = :term_id',
            [
                ':class_id' => (int) $classId,
                ':subject_id' => (int) $subjectId,
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
            'INSERT INTO class_subjects
                (class_id, subject_id, teacher_id, academic_year_id, term_id, created_at, updated_at)
             VALUES
                (:class_id, :subject_id, :teacher_id, :academic_year_id, :term_id, NOW(), NOW())',
            [
                ':class_id' => (int) $data['class_id'],
                ':subject_id' => (int) $data['subject_id'],
                ':teacher_id' => !empty($data['teacher_id']) ? (int) $data['teacher_id'] : null,
                ':academic_year_id' => (int) $data['academic_year_id'],
                ':term_id' => (int) $data['term_id'],
            ]
        );

        return (int) Database::lastInsertId();
    }
}