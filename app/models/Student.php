<?php
declare(strict_types=1);

class Student
{
    public static function existsByAdmissionNumber($admissionNumber)
    {
        $stmt = Database::query(
            'SELECT COUNT(*) AS total FROM students WHERE admission_number = :admission_number',
            [':admission_number' => trim($admissionNumber)]
        );

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return ((int) $row['total']) > 0;
    }

    public static function allDetailed()
    {
        $stmt = Database::query(
            'SELECT s.*, u.full_name, u.email, u.phone, u.is_active,
                    c.class_name, c.grade_level, c.stream,
                    ay.year_name
             FROM students s
             INNER JOIN users u ON u.id = s.user_id
             LEFT JOIN classes c ON c.id = s.class_id
             LEFT JOIN academic_years ay ON ay.id = c.academic_year_id
             ORDER BY s.created_at DESC'
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findById($id)
    {
        $stmt = Database::query(
            'SELECT * FROM students WHERE id = :id LIMIT 1',
            [':id' => (int) $id]
        );

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public static function findByUserId($userId)
    {
        $stmt = Database::query(
            'SELECT * FROM students WHERE user_id = :user_id LIMIT 1',
            [':user_id' => (int) $userId]
        );

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public static function findDetailedById($id)
    {
        $stmt = Database::query(
            'SELECT s.*, u.full_name, u.email, u.phone, u.is_active,
                    c.class_name, c.grade_level, c.stream,
                    ay.year_name
             FROM students s
             INNER JOIN users u ON u.id = s.user_id
             LEFT JOIN classes c ON c.id = s.class_id
             LEFT JOIN academic_years ay ON ay.id = c.academic_year_id
             WHERE s.id = :id
             LIMIT 1',
            [':id' => (int) $id]
        );

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public static function findDetailedByUserId($userId)
    {
        $stmt = Database::query(
            'SELECT s.*, u.full_name, u.email, u.phone, u.is_active,
                    c.class_name, c.grade_level, c.stream,
                    ay.year_name
             FROM students s
             INNER JOIN users u ON u.id = s.user_id
             LEFT JOIN classes c ON c.id = s.class_id
             LEFT JOIN academic_years ay ON ay.id = c.academic_year_id
             WHERE s.user_id = :user_id
             LIMIT 1',
            [':user_id' => (int) $userId]
        );

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public static function createProfile($userId, array $data)
    {
        $admissionNumber = trim((string) ($data['admission_number'] ?? ''));

        if ($admissionNumber === '') {
            throw new InvalidArgumentException('Admission number is required.');
        }

        if (self::existsByAdmissionNumber($admissionNumber)) {
            throw new RuntimeException('Admission number already exists.');
        }

        Database::query(
            'INSERT INTO students
            (user_id, admission_number, gender, date_of_birth, class_id, guardian_name, guardian_phone, guardian_email, address, admission_date, status, created_at, updated_at)
            VALUES
            (:user_id, :admission_number, :gender, :date_of_birth, :class_id, :guardian_name, :guardian_phone, :guardian_email, :address, :admission_date, :status, NOW(), NOW())',
            [
                ':user_id' => (int) $userId,
                ':admission_number' => $admissionNumber,
                ':gender' => $data['gender'] ?? null,
                ':date_of_birth' => !empty($data['date_of_birth']) ? $data['date_of_birth'] : null,
                ':class_id' => !empty($data['class_id']) ? (int) $data['class_id'] : null,
                ':guardian_name' => trim((string) ($data['guardian_name'] ?? '')) ?: null,
                ':guardian_phone' => trim((string) ($data['guardian_phone'] ?? '')) ?: null,
                ':guardian_email' => trim((string) ($data['guardian_email'] ?? '')) ?: null,
                ':address' => trim((string) ($data['address'] ?? '')) ?: null,
                ':admission_date' => !empty($data['admission_date']) ? $data['admission_date'] : date('Y-m-d'),
                ':status' => 'active',
            ]
        );

        return true;
    }

    public static function updateProfile($studentId, array $data)
    {
        Database::query(
            'UPDATE students
             SET gender = :gender,
                 date_of_birth = :date_of_birth,
                 guardian_name = :guardian_name,
                 guardian_phone = :guardian_phone,
                 guardian_email = :guardian_email,
                 address = :address,
                 updated_at = NOW()
             WHERE id = :id',
            [
                ':gender' => !empty($data['gender']) ? $data['gender'] : null,
                ':date_of_birth' => !empty($data['date_of_birth']) ? $data['date_of_birth'] : null,
                ':guardian_name' => !empty($data['guardian_name']) ? trim((string) $data['guardian_name']) : null,
                ':guardian_phone' => !empty($data['guardian_phone']) ? trim((string) $data['guardian_phone']) : null,
                ':guardian_email' => !empty($data['guardian_email']) ? trim((string) $data['guardian_email']) : null,
                ':address' => !empty($data['address']) ? trim((string) $data['address']) : null,
                ':id' => (int) $studentId,
            ]
        );

        return true;
    }

    public static function updateClass($studentId, $classId)
    {
        Database::query(
            'UPDATE students SET class_id = :class_id, updated_at = NOW() WHERE id = :id',
            [
                ':class_id' => $classId !== null ? (int) $classId : null,
                ':id' => (int) $studentId,
            ]
        );

        return true;
    }

    public static function updateStatus($studentId, $status)
    {
        Database::query(
            'UPDATE students SET status = :status, updated_at = NOW() WHERE id = :id',
            [
                ':status' => $status,
                ':id' => (int) $studentId,
            ]
        );

        return true;
    }
        public static function activeForSelection()
    {
        $stmt = Database::query(
            'SELECT s.id AS student_id, s.admission_number, s.class_id,
                    u.full_name, u.email, c.class_name
             FROM students s
             INNER JOIN users u ON u.id = s.user_id
             LEFT JOIN classes c ON c.id = s.class_id
             WHERE s.status = "active"
               AND u.is_active = 1
             ORDER BY u.full_name ASC'
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function allWithUsers()
    {
        $stmt = Database::query(
            'SELECT s.*, u.full_name, u.email, u.phone, u.is_active, c.class_name
             FROM students s
             INNER JOIN users u ON u.id = s.user_id
             LEFT JOIN classes c ON c.id = s.class_id
             ORDER BY s.created_at DESC'
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}