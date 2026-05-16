<?php
declare(strict_types=1);

class AcademicYear
{
    public static function all()
    {
        $stmt = Database::query(
            'SELECT * FROM academic_years ORDER BY start_date DESC, id DESC'
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function current()
    {
        $stmt = Database::query(
            'SELECT * FROM academic_years WHERE is_current = 1 ORDER BY id DESC LIMIT 1'
        );

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public static function find($id)
    {
        $stmt = Database::query(
            'SELECT * FROM academic_years WHERE id = :id LIMIT 1',
            [':id' => (int) $id]
        );

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public static function existsByName($yearName)
    {
        $stmt = Database::query(
            'SELECT COUNT(*) AS total FROM academic_years WHERE year_name = :year_name',
            [':year_name' => trim($yearName)]
        );

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return ((int) $row['total']) > 0;
    }

    public static function create(array $data)
    {
        Database::query(
            'INSERT INTO academic_years (year_name, start_date, end_date, is_current, created_at, updated_at)
             VALUES (:year_name, :start_date, :end_date, :is_current, NOW(), NOW())',
            [
                ':year_name' => trim((string) $data['year_name']),
                ':start_date' => $data['start_date'],
                ':end_date' => $data['end_date'],
                ':is_current' => !empty($data['is_current']) ? 1 : 0,
            ]
        );

        return (int) Database::lastInsertId();
    }

    public static function setCurrent($id)
    {
        Database::query('UPDATE academic_years SET is_current = 0');
        return Database::query(
            'UPDATE academic_years SET is_current = 1 WHERE id = :id',
            [':id' => (int) $id]
        )->rowCount() > 0;
    }
}