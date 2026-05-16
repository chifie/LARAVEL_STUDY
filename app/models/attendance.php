<?php
declare(strict_types=1);

class Attendance
{
    public static function allowedClassesForUser($userId)
    {
        $stmt = Database::query(
            'SELECT DISTINCT c.id, c.class_name, c.grade_level, c.stream, ay.year_name
             FROM classes c
             INNER JOIN academic_years ay ON ay.id = c.academic_year_id
             LEFT JOIN teachers ct ON ct.id = c.class_teacher_id
             LEFT JOIN users cu ON cu.id = ct.user_id
             LEFT JOIN class_subjects cs ON cs.class_id = c.id
             LEFT JOIN teachers t2 ON t2.id = cs.teacher_id
             LEFT JOIN users tu ON tu.id = t2.user_id
             WHERE c.status = "active"
               AND (cu.id = :user_id OR tu.id = :user_id)
             ORDER BY c.class_name ASC',
            [':user_id' => (int) $userId]
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function activeClasses()
    {
        $stmt = Database::query(
            'SELECT c.id, c.class_name, c.grade_level, c.stream, ay.year_name
             FROM classes c
             INNER JOIN academic_years ay ON ay.id = c.academic_year_id
             WHERE c.status = "active"
             ORDER BY c.class_name ASC'
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function studentsByClass($classId)
    {
        $stmt = Database::query(
            'SELECT s.id AS student_id, s.admission_number, s.gender, s.status,
                    u.full_name, u.email, u.phone
             FROM students s
             INNER JOIN users u ON u.id = s.user_id
             WHERE s.class_id = :class_id
               AND s.status = "active"
               AND u.is_active = 1
             ORDER BY u.full_name ASC',
            [':class_id' => (int) $classId]
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function recordsByClassAndDate($classId, $date)
    {
        $stmt = Database::query(
            'SELECT a.*, s.id AS student_id, s.admission_number, u.full_name
             FROM attendance a
             INNER JOIN students s ON s.id = a.student_id
             INNER JOIN users u ON u.id = s.user_id
             WHERE a.class_id = :class_id
               AND a.attendance_date = :attendance_date
             ORDER BY u.full_name ASC',
            [
                ':class_id' => (int) $classId,
                ':attendance_date' => $date,
            ]
        );

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $indexed = [];
        foreach ($rows as $row) {
            $indexed[(int) $row['student_id']] = $row;
        }

        return $indexed;
    }

    public static function saveBulk($classId, $attendanceDate, $teacherId, array $statuses, array $remarks)
    {
        $students = self::studentsByClass($classId);
        $count = 0;

        foreach ($students as $student) {
            $studentId = (int) $student['student_id'];
            $status = isset($statuses[$studentId]) ? trim((string) $statuses[$studentId]) : 'absent';
            $remark = isset($remarks[$studentId]) ? trim((string) $remarks[$studentId]) : null;

            if (!in_array($status, ['present', 'absent', 'late', 'excused'], true)) {
                $status = 'absent';
            }

            $existing = Database::query(
                'SELECT id FROM attendance WHERE student_id = :student_id AND attendance_date = :attendance_date LIMIT 1',
                [
                    ':student_id' => $studentId,
                    ':attendance_date' => $attendanceDate,
                ]
            )->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                Database::query(
                    'UPDATE attendance
                     SET class_id = :class_id,
                         teacher_id = :teacher_id,
                         status = :status,
                         remarks = :remarks,
                         updated_at = NOW()
                     WHERE id = :id',
                    [
                        ':class_id' => (int) $classId,
                        ':teacher_id' => $teacherId !== null ? (int) $teacherId : null,
                        ':status' => $status,
                        ':remarks' => $remark,
                        ':id' => (int) $existing['id'],
                    ]
                );
            } else {
                Database::query(
                    'INSERT INTO attendance
                    (student_id, class_id, teacher_id, attendance_date, status, remarks, created_at, updated_at)
                    VALUES
                    (:student_id, :class_id, :teacher_id, :attendance_date, :status, :remarks, NOW(), NOW())',
                    [
                        ':student_id' => $studentId,
                        ':class_id' => (int) $classId,
                        ':teacher_id' => $teacherId !== null ? (int) $teacherId : null,
                        ':attendance_date' => $attendanceDate,
                        ':status' => $status,
                        ':remarks' => $remark,
                    ]
                );
            }

            $count++;
        }

        return $count;
    }

    public static function dailyByClassAndDate($classId, $date)
    {
        $stmt = Database::query(
            'SELECT a.*, s.id AS student_id, s.admission_number, u.full_name
             FROM attendance a
             INNER JOIN students s ON s.id = a.student_id
             INNER JOIN users u ON u.id = s.user_id
             WHERE a.class_id = :class_id
               AND a.attendance_date = :attendance_date
             ORDER BY u.full_name ASC',
            [
                ':class_id' => (int) $classId,
                ':attendance_date' => $date,
            ]
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function dailyCounts($classId, $date)
    {
        $stmt = Database::query(
            'SELECT
                SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) AS present_count,
                SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) AS absent_count,
                SUM(CASE WHEN status = "late" THEN 1 ELSE 0 END) AS late_count,
                SUM(CASE WHEN status = "excused" THEN 1 ELSE 0 END) AS excused_count,
                COUNT(*) AS total_count
             FROM attendance
             WHERE class_id = :class_id
               AND attendance_date = :attendance_date',
            [
                ':class_id' => (int) $classId,
                ':attendance_date' => $date,
            ]
        );

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: [
            'present_count' => 0,
            'absent_count' => 0,
            'late_count' => 0,
            'excused_count' => 0,
            'total_count' => 0,
        ];
    }

    public static function summaryByClassAndRange($classId, $fromDate, $toDate)
    {
        $stmt = Database::query(
            'SELECT
                s.id AS student_id,
                s.admission_number,
                u.full_name,
                SUM(CASE WHEN a.status = "present" THEN 1 ELSE 0 END) AS present_count,
                SUM(CASE WHEN a.status = "absent" THEN 1 ELSE 0 END) AS absent_count,
                SUM(CASE WHEN a.status = "late" THEN 1 ELSE 0 END) AS late_count,
                SUM(CASE WHEN a.status = "excused" THEN 1 ELSE 0 END) AS excused_count,
                COUNT(a.id) AS total_days
             FROM students s
             INNER JOIN users u ON u.id = s.user_id
             LEFT JOIN attendance a
               ON a.student_id = s.id
              AND a.class_id = :class_id
              AND a.attendance_date BETWEEN :from_date AND :to_date
             WHERE s.class_id = :class_id
               AND s.status = "active"
               AND u.is_active = 1
             GROUP BY s.id, s.admission_number, u.full_name
             ORDER BY u.full_name ASC',
            [
                ':class_id' => (int) $classId,
                ':from_date' => $fromDate,
                ':to_date' => $toDate,
            ]
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function classById($classId)
    {
        $stmt = Database::query(
            'SELECT c.*, ay.year_name
             FROM classes c
             INNER JOIN academic_years ay ON ay.id = c.academic_year_id
             WHERE c.id = :id
             LIMIT 1',
            [':id' => (int) $classId]
        );

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }
}