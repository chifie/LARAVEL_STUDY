<?php
declare(strict_types=1);

class Result
{
    public static function gradeFromPercentage($percentage)
    {
        if ($percentage >= 80) {
            return 'A';
        }

        if ($percentage >= 70) {
            return 'B';
        }

        if ($percentage >= 60) {
            return 'C';
        }

        if ($percentage >= 50) {
            return 'D';
        }

        if ($percentage >= 40) {
            return 'E';
        }

        return 'F';
    }

    public static function remarkFromGrade($grade)
    {
        switch ($grade) {
            case 'A':
                return 'Excellent';
            case 'B':
                return 'Very Good';
            case 'C':
                return 'Good';
            case 'D':
                return 'Pass';
            case 'E':
                return 'Weak Pass';
            default:
                return 'Fail';
        }
    }

    public static function saveBulk($examId, $recordedBy, array $marksByStudent)
    {
        $exam = Exam::find($examId);
        if (!$exam) {
            throw new RuntimeException('Exam not found.');
        }

        $totalMarks = (float) $exam['total_marks'];
        if ($totalMarks <= 0) {
            $totalMarks = 100.0;
        }

        $saved = 0;

        foreach ($marksByStudent as $studentId => $marks) {
            if ($marks === '' || $marks === null) {
                continue;
            }

            if (!is_numeric($marks)) {
                continue;
            }

            $marks = (float) $marks;
            if ($marks < 0) {
                $marks = 0;
            }

            if ($marks > $totalMarks) {
                $marks = $totalMarks;
            }

            $percentage = ($marks / $totalMarks) * 100;
            $grade = self::gradeFromPercentage($percentage);
            $remarks = self::remarkFromGrade($grade);

            $existing = Database::query(
                'SELECT id FROM results WHERE exam_id = :exam_id AND student_id = :student_id LIMIT 1',
                [
                    ':exam_id' => (int) $examId,
                    ':student_id' => (int) $studentId,
                ]
            )->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                Database::query(
                    'UPDATE results
                     SET marks_obtained = :marks_obtained,
                         grade = :grade,
                         remarks = :remarks,
                         recorded_by = :recorded_by,
                         updated_at = NOW()
                     WHERE id = :id',
                    [
                        ':marks_obtained' => $marks,
                        ':grade' => $grade,
                        ':remarks' => $remarks,
                        ':recorded_by' => (int) $recordedBy,
                        ':id' => (int) $existing['id'],
                    ]
                );
            } else {
                Database::query(
                    'INSERT INTO results
                    (exam_id, student_id, marks_obtained, grade, remarks, recorded_by, created_at, updated_at)
                    VALUES
                    (:exam_id, :student_id, :marks_obtained, :grade, :remarks, :recorded_by, NOW(), NOW())',
                    [
                        ':exam_id' => (int) $examId,
                        ':student_id' => (int) $studentId,
                        ':marks_obtained' => $marks,
                        ':grade' => $grade,
                        ':remarks' => $remarks,
                        ':recorded_by' => (int) $recordedBy,
                    ]
                );
            }

            $saved++;
        }

        return $saved;
    }

    public static function byExam($examId)
    {
        $stmt = Database::query(
            'SELECT r.*, s.admission_number, u.full_name
             FROM results r
             INNER JOIN students s ON s.id = r.student_id
             INNER JOIN users u ON u.id = s.user_id
             WHERE r.exam_id = :exam_id
             ORDER BY u.full_name ASC',
            [':exam_id' => (int) $examId]
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function byStudent($studentId)
    {
        $stmt = Database::query(
            'SELECT r.*, e.exam_name, e.exam_date, e.total_marks, e.class_id, e.subject_id,
                    c.class_name, s.subject_name, s.subject_code, ay.year_name, term.term_name
             FROM results r
             INNER JOIN exams e ON e.id = r.exam_id
             INNER JOIN classes c ON c.id = e.class_id
             INNER JOIN subjects s ON s.id = e.subject_id
             INNER JOIN academic_years ay ON ay.id = e.academic_year_id
             INNER JOIN terms term ON term.id = e.term_id
             WHERE r.student_id = :student_id
             ORDER BY e.exam_date DESC, r.id DESC',
            [':student_id' => (int) $studentId]
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function summaryByExam($examId)
    {
        $stmt = Database::query(
            'SELECT
                COUNT(*) AS total_students,
                AVG(marks_obtained) AS average_marks,
                MAX(marks_obtained) AS highest_marks,
                MIN(marks_obtained) AS lowest_marks,
                SUM(CASE WHEN grade = "A" THEN 1 ELSE 0 END) AS grade_a,
                SUM(CASE WHEN grade = "B" THEN 1 ELSE 0 END) AS grade_b,
                SUM(CASE WHEN grade = "C" THEN 1 ELSE 0 END) AS grade_c,
                SUM(CASE WHEN grade = "D" THEN 1 ELSE 0 END) AS grade_d,
                SUM(CASE WHEN grade = "E" THEN 1 ELSE 0 END) AS grade_e,
                SUM(CASE WHEN grade = "F" THEN 1 ELSE 0 END) AS grade_f
             FROM results
             WHERE exam_id = :exam_id',
            [':exam_id' => (int) $examId]
        );

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: [
            'total_students' => 0,
            'average_marks' => 0,
            'highest_marks' => 0,
            'lowest_marks' => 0,
            'grade_a' => 0,
            'grade_b' => 0,
            'grade_c' => 0,
            'grade_d' => 0,
            'grade_e' => 0,
            'grade_f' => 0,
        ];
    }
}