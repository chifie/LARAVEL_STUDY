<?php
declare(strict_types=1);

class FeeStructure
{
    public static function allWithRelations()
    {
        $stmt = Database::query(
            'SELECT fs.*, ay.year_name, t.term_name, c.class_name, u.full_name AS creator_name
             FROM fee_structures fs
             INNER JOIN academic_years ay ON ay.id = fs.academic_year_id
             INNER JOIN terms t ON t.id = fs.term_id
             INNER JOIN classes c ON c.id = fs.class_id
             LEFT JOIN users u ON u.id = fs.created_by
             ORDER BY ay.start_date DESC, t.start_date DESC, c.class_name ASC, fs.id DESC'
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function activeAll()
    {
        $stmt = Database::query(
            'SELECT fs.*, ay.year_name, t.term_name, c.class_name
             FROM fee_structures fs
             INNER JOIN academic_years ay ON ay.id = fs.academic_year_id
             INNER JOIN terms t ON t.id = fs.term_id
             INNER JOIN classes c ON c.id = fs.class_id
             WHERE fs.status = "active"
             ORDER BY ay.start_date DESC, t.start_date DESC, c.class_name ASC, fs.id DESC'
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find($id)
    {
        $stmt = Database::query(
            'SELECT fs.*, ay.year_name, t.term_name, c.class_name, u.full_name AS creator_name
             FROM fee_structures fs
             INNER JOIN academic_years ay ON ay.id = fs.academic_year_id
             INNER JOIN terms t ON t.id = fs.term_id
             INNER JOIN classes c ON c.id = fs.class_id
             LEFT JOIN users u ON u.id = fs.created_by
             WHERE fs.id = :id
             LIMIT 1',
            [':id' => (int) $id]
        );

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public static function exists(array $data)
    {
        $stmt = Database::query(
            'SELECT COUNT(*) AS total
             FROM fee_structures
             WHERE academic_year_id = :academic_year_id
               AND term_id = :term_id
               AND class_id = :class_id
               AND fee_name = :fee_name',
            [
                ':academic_year_id' => (int) $data['academic_year_id'],
                ':term_id' => (int) $data['term_id'],
                ':class_id' => (int) $data['class_id'],
                ':fee_name' => trim((string) $data['fee_name']),
            ]
        );

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return ((int) $row['total']) > 0;
    }

    public static function create(array $data)
    {
        Database::query(
            'INSERT INTO fee_structures
                (academic_year_id, term_id, class_id, fee_name, amount_due, due_date, status, created_by, created_at, updated_at)
             VALUES
                (:academic_year_id, :term_id, :class_id, :fee_name, :amount_due, :due_date, :status, :created_by, NOW(), NOW())',
            [
                ':academic_year_id' => (int) $data['academic_year_id'],
                ':term_id' => (int) $data['term_id'],
                ':class_id' => (int) $data['class_id'],
                ':fee_name' => trim((string) $data['fee_name']),
                ':amount_due' => (float) $data['amount_due'],
                ':due_date' => !empty($data['due_date']) ? $data['due_date'] : null,
                ':status' => !empty($data['status']) ? $data['status'] : 'active',
                ':created_by' => !empty($data['created_by']) ? (int) $data['created_by'] : null,
            ]
        );

        return (int) Database::lastInsertId();
    }

    public static function setStatus($id, $status)
    {
        return Database::query(
            'UPDATE fee_structures SET status = :status, updated_at = NOW() WHERE id = :id',
            [
                ':status' => $status,
                ':id' => (int) $id,
            ]
        )->rowCount() > 0;
    }
}