<?php
declare(strict_types=1);

class Subject
{
    public static function all()
    {
        $stmt = Database::query(
            'SELECT * FROM subjects ORDER BY subject_name ASC, id DESC'
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find($id)
    {
        $stmt = Database::query(
            'SELECT * FROM subjects WHERE id = :id LIMIT 1',
            [':id' => (int) $id]
        );

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public static function existsByCode($subjectCode)
    {
        $stmt = Database::query(
            'SELECT COUNT(*) AS total FROM subjects WHERE subject_code = :subject_code',
            [':subject_code' => trim($subjectCode)]
        );

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return ((int) $row['total']) > 0;
    }

    public static function create(array $data)
    {
        Database::query(
            'INSERT INTO subjects (subject_name, subject_code, description, status, created_at, updated_at)
             VALUES (:subject_name, :subject_code, :description, :status, NOW(), NOW())',
            [
                ':subject_name' => trim((string) $data['subject_name']),
                ':subject_code' => strtoupper(trim((string) $data['subject_code'])),
                ':description' => !empty($data['description']) ? trim((string) $data['description']) : null,
                ':status' => 'active',
            ]
        );

        return (int) Database::lastInsertId();
    }

    public static function toggleStatus($id)
    {
        $subject = self::find($id);
        if (!$subject) {
            return false;
        }

        $newStatus = ($subject['status'] === 'active') ? 'inactive' : 'active';

        return Database::query(
            'UPDATE subjects SET status = :status, updated_at = NOW() WHERE id = :id',
            [
                ':status' => $newStatus,
                ':id' => (int) $id,
            ]
        )->rowCount() > 0;
    }
}