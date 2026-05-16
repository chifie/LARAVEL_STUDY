<?php
declare(strict_types=1);

class Teacher
{
    public static function existsByStaffNumber($staffNumber)
    {
        $stmt = Database::query(
            'SELECT COUNT(*) AS total FROM teachers WHERE staff_number = :staff_number',
            [':staff_number' => trim($staffNumber)]
        );

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return ((int) $row['total']) > 0;
    }

    public static function findByUserId($userId)
    {
        $stmt = Database::query(
            'SELECT * FROM teachers WHERE user_id = :user_id LIMIT 1',
            [':user_id' => (int) $userId]
        );

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public static function createProfile($userId, array $data)
    {
        $staffNumber = trim((string) ($data['staff_number'] ?? ''));

        if ($staffNumber === '') {
            throw new InvalidArgumentException('Staff number is required.');
        }

        if (self::existsByStaffNumber($staffNumber)) {
            throw new RuntimeException('Staff number already exists.');
        }

        Database::query(
            'INSERT INTO teachers
            (user_id, staff_number, gender, date_of_birth, qualification, specialization, phone, address, employment_date, status, created_at, updated_at)
            VALUES
            (:user_id, :staff_number, :gender, :date_of_birth, :qualification, :specialization, :phone, :address, :employment_date, :status, NOW(), NOW())',
            [
                ':user_id' => (int) $userId,
                ':staff_number' => $staffNumber,
                ':gender' => $data['gender'] ?? null,
                ':date_of_birth' => !empty($data['date_of_birth']) ? $data['date_of_birth'] : null,
                ':qualification' => trim((string) ($data['qualification'] ?? '')) ?: null,
                ':specialization' => trim((string) ($data['specialization'] ?? '')) ?: null,
                ':phone' => trim((string) ($data['phone'] ?? '')) ?: null,
                ':address' => trim((string) ($data['address'] ?? '')) ?: null,
                ':employment_date' => !empty($data['employment_date']) ? $data['employment_date'] : null,
                ':status' => 'active',
            ]
        );

        return true;
    }

    public static function allWithUsers()
    {
        $stmt = Database::query(
            'SELECT t.*, u.full_name, u.email, u.phone, u.is_active
             FROM teachers t
             INNER JOIN users u ON u.id = t.user_id
             ORDER BY t.created_at DESC'
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}