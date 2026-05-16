<?php
declare(strict_types=1);

class SchoolClass
{
    public static function allWithRelations()
    {
        $stmt = Database::query(
            'SELECT c.*, ay.year_name, u.full_name AS teacher_name
             FROM classes c
             INNER JOIN academic_years ay ON ay.id = c.academic_year_id
             LEFT JOIN teachers t ON t.id = c.class_teacher_id
             LEFT JOIN users u ON u.id = t.user_id
             ORDER BY ay.start_date DESC, c.class_name ASC, c.id DESC'
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function allActiveWithRelations()
    {
        $stmt = Database::query(
            'SELECT c.*, ay.year_name, u.full_name AS teacher_name
             FROM classes c
             INNER JOIN academic_years ay ON ay.id = c.academic_year_id
             LEFT JOIN teachers t ON t.id = c.class_teacher_id
             LEFT JOIN users u ON u.id = t.user_id
             WHERE c.status = "active"
             ORDER BY ay.start_date DESC, c.class_name ASC, c.id DESC'
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find($id)
    {
        $stmt = Database::query(
            'SELECT * FROM classes WHERE id = :id LIMIT 1',
            [':id' => (int) $id]
        );

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public static function existsInYear($className, $academicYearId, $stream = null)
    {
        $stmt = Database::query(
            'SELECT COUNT(*) AS total
             FROM classes
             WHERE class_name = :class_name
               AND academic_year_id = :academic_year_id
               AND (stream <=> :stream)',
            [
                ':class_name' => trim($className),
                ':academic_year_id' => (int) $academicYearId,
                ':stream' => $stream !== null && $stream !== '' ? trim($stream) : null,
            ]
        );

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return ((int) $row['total']) > 0;
    }

    public static function create(array $data)
    {
        Database::query(
            'INSERT INTO classes
                (class_name, grade_level, stream, academic_year_id, capacity, class_teacher_id, status, created_at, updated_at)
             VALUES
                (:class_name, :grade_level, :stream, :academic_year_id, :capacity, :class_teacher_id, :status, NOW(), NOW())',
            [
                ':class_name' => trim((string) $data['class_name']),
                ':grade_level' => !empty($data['grade_level']) ? trim((string) $data['grade_level']) : null,
                ':stream' => !empty($data['stream']) ? trim((string) $data['stream']) : null,
                ':academic_year_id' => (int) $data['academic_year_id'],
                ':capacity' => !empty($data['capacity']) ? (int) $data['capacity'] : null,
                ':class_teacher_id' => !empty($data['class_teacher_id']) ? (int) $data['class_teacher_id'] : null,
                ':status' => 'active',
            ]
        );

        return (int) Database::lastInsertId();
    }

    public static function toggleStatus($id)
    {
        $class = self::find($id);
        if (!$class) {
            return false;
        }

        $newStatus = ($class['status'] === 'active') ? 'inactive' : 'active';

        return Database::query(
            'UPDATE classes SET status = :status, updated_at = NOW() WHERE id = :id',
            [
                ':status' => $newStatus,
                ':id' => (int) $id,
            ]
        )->rowCount() > 0;
    }
}