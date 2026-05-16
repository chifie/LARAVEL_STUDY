<?php
declare(strict_types=1);

class Term
{
    public static function allWithYears()
    {
        $stmt = Database::query(
            'SELECT t.*, ay.year_name
             FROM terms t
             INNER JOIN academic_years ay ON ay.id = t.academic_year_id
             ORDER BY ay.start_date DESC, t.start_date DESC, t.id DESC'
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function allByYear($academicYearId)
    {
        $stmt = Database::query(
            'SELECT * FROM terms WHERE academic_year_id = :academic_year_id ORDER BY start_date ASC, id ASC',
            [':academic_year_id' => (int) $academicYearId]
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function currentForYear($academicYearId)
    {
        $stmt = Database::query(
            'SELECT * FROM terms
             WHERE academic_year_id = :academic_year_id AND is_current = 1
             ORDER BY id DESC
             LIMIT 1',
            [':academic_year_id' => (int) $academicYearId]
        );

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public static function find($id)
    {
        $stmt = Database::query(
            'SELECT * FROM terms WHERE id = :id LIMIT 1',
            [':id' => (int) $id]
        );

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public static function existsInYear($academicYearId, $termName)
    {
        $stmt = Database::query(
            'SELECT COUNT(*) AS total
             FROM terms
             WHERE academic_year_id = :academic_year_id AND term_name = :term_name',
            [
                ':academic_year_id' => (int) $academicYearId,
                ':term_name' => trim($termName),
            ]
        );

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return ((int) $row['total']) > 0;
    }

    public static function create(array $data)
    {
        Database::query(
            'INSERT INTO terms (academic_year_id, term_name, start_date, end_date, is_current, created_at, updated_at)
             VALUES (:academic_year_id, :term_name, :start_date, :end_date, :is_current, NOW(), NOW())',
            [
                ':academic_year_id' => (int) $data['academic_year_id'],
                ':term_name' => trim((string) $data['term_name']),
                ':start_date' => $data['start_date'],
                ':end_date' => $data['end_date'],
                ':is_current' => !empty($data['is_current']) ? 1 : 0,
            ]
        );

        return (int) Database::lastInsertId();
    }

    public static function setCurrent($id, $academicYearId)
    {
        Database::query(
            'UPDATE terms SET is_current = 0 WHERE academic_year_id = :academic_year_id',
            [':academic_year_id' => (int) $academicYearId]
        );

        return Database::query(
            'UPDATE terms SET is_current = 1 WHERE id = :id',
            [':id' => (int) $id]
        )->rowCount() > 0;
    }
}