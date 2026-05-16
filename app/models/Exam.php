<?php
declare(strict_types=1);

class Exam
{
    public static function find($id)
    {
        $stmt = Database::query(
            'SELECT e.*, c.class_name, s.subject_name, s.subject_code, ay.year_name, term.term_name,
                    u.full_name AS creator_name
             FROM exams e
             INNER JOIN classes c ON c.id = e.class_id
             INNER JOIN subjects s ON s.id = e.subject_id
             INNER JOIN academic_years ay ON ay.id = e.academic_year_id
             INNER JOIN terms term ON term.id = e.term_id
             LEFT JOIN users u ON u.id = e.created_by
             WHERE e.id = :id
             LIMIT 1',
            [':id' => (int) $id]
        );

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public static function allWithRelations($user = null)
    {
        $sql = 'SELECT e.*, c.class_name, s.subject_name, s.subject_code, ay.year_name, term.term_name,
                       u.full_name AS creator_name
                FROM exams e
                INNER JOIN classes c ON c.id = e.class_id
                INNER JOIN subjects s ON s.id = e.subject_id
                INNER JOIN academic_years ay ON ay.id = e.academic_year_id
                INNER JOIN terms term ON term.id = e.term_id
                LEFT JOIN users u ON u.id = e.created_by';

        $params = [];

        if ($user && isset($user['role']) && $user['role'] === 'teacher') {
            $teacher = Teacher::findByUserId($user['id']);
            if ($teacher) {
                $sql .= ' INNER JOIN class_subjects cs
                          ON cs.class_id = e.class_id
                         AND cs.subject_id = e.subject_id
                         AND cs.academic_year_id = e.academic_year_id
                         AND cs.term_id = e.term_id
                         AND cs.teacher_id = :teacher_id';
                $params[':teacher_id'] = (int) $teacher['id'];
            }
        }

        $sql .= ' ORDER BY e.created_at DESC, e.id DESC';

        $stmt = Database::query($sql, $params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function assignmentsForCurrentUser()
    {
        $user = Auth::user();

        if (Auth::hasRole('super_admin')) {
            return ClassSubject::allWithRelations();
        }

        if (Auth::hasRole('teacher')) {
            return ClassSubject::allForUser(Auth::id());
        }

        return [];
    }

    public static function allForExamCreation()
    {
        return self::assignmentsForCurrentUser();
    }

    public static function create(array $data)
    {
        Database::query(
            'INSERT INTO exams
                (exam_name, academic_year_id, term_id, class_id, subject_id, exam_date, total_marks, created_by, created_at, updated_at)
             VALUES
                (:exam_name, :academic_year_id, :term_id, :class_id, :subject_id, :exam_date, :total_marks, :created_by, NOW(), NOW())',
            [
                ':exam_name' => trim((string) $data['exam_name']),
                ':academic_year_id' => (int) $data['academic_year_id'],
                ':term_id' => (int) $data['term_id'],
                ':class_id' => (int) $data['class_id'],
                ':subject_id' => (int) $data['subject_id'],
                ':exam_date' => !empty($data['exam_date']) ? $data['exam_date'] : null,
                ':total_marks' => (float) $data['total_marks'],
                ':created_by' => (int) $data['created_by'],
            ]
        );

        return (int) Database::lastInsertId();
    }

    public static function studentsByExam($examId)
    {
        $exam = self::find($examId);
        if (!$exam) {
            return [];
        }

        $stmt = Database::query(
            'SELECT s.id AS student_id, s.admission_number, u.full_name, u.email
             FROM students s
             INNER JOIN users u ON u.id = s.user_id
             WHERE s.class_id = :class_id
               AND s.status = "active"
               AND u.is_active = 1
             ORDER BY u.full_name ASC',
            [':class_id' => (int) $exam['class_id']]
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function canAccessExam(array $exam, $user)
    {
        if (!$user) {
            return false;
        }

        if ($user['role'] === 'super_admin') {
            return true;
        }

        if ($user['role'] !== 'teacher') {
            return false;
        }

        $teacher = Teacher::findByUserId($user['id']);
        if (!$teacher) {
            return false;
        }

        $stmt = Database::query(
            'SELECT COUNT(*) AS total
             FROM class_subjects
             WHERE class_id = :class_id
               AND subject_id = :subject_id
               AND academic_year_id = :academic_year_id
               AND term_id = :term_id
               AND teacher_id = :teacher_id',
            [
                ':class_id' => (int) $exam['class_id'],
                ':subject_id' => (int) $exam['subject_id'],
                ':academic_year_id' => (int) $exam['academic_year_id'],
                ':term_id' => (int) $exam['term_id'],
                ':teacher_id' => (int) $teacher['id'],
            ]
        );

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return ((int) $row['total']) > 0;
    }

    public static function examHasResults($examId)
    {
        $stmt = Database::query(
            'SELECT COUNT(*) AS total FROM results WHERE exam_id = :exam_id',
            [':exam_id' => (int) $examId]
        );

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return ((int) $row['total']) > 0;
    }
}