<?php
declare(strict_types=1);

class Payment
{
    public static function allWithRelations($limit = 50)
    {
        $limit = (int) $limit;

        $stmt = Database::query(
            'SELECT p.*, 
                    u.full_name AS student_name,
                    u.email AS student_email,
                    s.admission_number,
                    c.class_name,
                    fs.fee_name,
                    fs.amount_due AS fee_amount_due,
                    ay.year_name,
                    t.term_name,
                    r.full_name AS received_by_name
             FROM payments p
             INNER JOIN students s ON s.id = p.student_id
             INNER JOIN users u ON u.id = s.user_id
             LEFT JOIN classes c ON c.id = s.class_id
             LEFT JOIN fee_structures fs ON fs.id = p.fee_structure_id
             LEFT JOIN academic_years ay ON ay.id = p.academic_year_id
             LEFT JOIN terms t ON t.id = p.term_id
             LEFT JOIN users r ON r.id = p.received_by
             ORDER BY p.payment_date DESC, p.id DESC
             LIMIT ' . $limit
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create(array $data)
    {
        Database::query(
            'INSERT INTO payments
                (student_id, fee_structure_id, academic_year_id, term_id, amount_due, amount_paid, balance,
                 payment_date, payment_method, receipt_number, notes, received_by, created_at, updated_at)
             VALUES
                (:student_id, :fee_structure_id, :academic_year_id, :term_id, :amount_due, :amount_paid, :balance,
                 :payment_date, :payment_method, :receipt_number, :notes, :received_by, NOW(), NOW())',
            [
                ':student_id' => (int) $data['student_id'],
                ':fee_structure_id' => !empty($data['fee_structure_id']) ? (int) $data['fee_structure_id'] : null,
                ':academic_year_id' => (int) $data['academic_year_id'],
                ':term_id' => (int) $data['term_id'],
                ':amount_due' => (float) $data['amount_due'],
                ':amount_paid' => (float) $data['amount_paid'],
                ':balance' => (float) $data['balance'],
                ':payment_date' => $data['payment_date'],
                ':payment_method' => $data['payment_method'],
                ':receipt_number' => !empty($data['receipt_number']) ? trim((string) $data['receipt_number']) : null,
                ':notes' => !empty($data['notes']) ? trim((string) $data['notes']) : null,
                ':received_by' => !empty($data['received_by']) ? (int) $data['received_by'] : null,
            ]
        );

        return (int) Database::lastInsertId();
    }

    public static function studentHistory($studentId)
    {
        $stmt = Database::query(
            'SELECT p.*, 
                    fs.fee_name,
                    c.class_name,
                    ay.year_name,
                    t.term_name,
                    r.full_name AS received_by_name
             FROM payments p
             LEFT JOIN fee_structures fs ON fs.id = p.fee_structure_id
             INNER JOIN academic_years ay ON ay.id = p.academic_year_id
             INNER JOIN terms t ON t.id = p.term_id
             LEFT JOIN classes c ON c.id = fs.class_id
             LEFT JOIN users r ON r.id = p.received_by
             WHERE p.student_id = :student_id
             ORDER BY p.payment_date DESC, p.id DESC',
            [':student_id' => (int) $studentId]
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function studentSummary($studentId)
    {
        $stmt = Database::query(
            'SELECT
                SUM(amount_due) AS total_due,
                SUM(amount_paid) AS total_paid,
                SUM(balance) AS total_balance,
                COUNT(*) AS payment_count
             FROM payments
             WHERE student_id = :student_id',
            [':student_id' => (int) $studentId]
        );

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: [
            'total_due' => 0,
            'total_paid' => 0,
            'total_balance' => 0,
            'payment_count' => 0,
        ];
    }

    public static function overview()
    {
        $stmt = Database::query(
            'SELECT
                COUNT(*) AS payment_count,
                COALESCE(SUM(amount_due), 0) AS total_due,
                COALESCE(SUM(amount_paid), 0) AS total_paid,
                COALESCE(SUM(balance), 0) AS total_balance
             FROM payments'
        );

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: [
            'payment_count' => 0,
            'total_due' => 0,
            'total_paid' => 0,
            'total_balance' => 0,
        ];
    }

    public static function reportByFilters(array $filters)
    {
        $sql = 'SELECT
                    s.id AS student_id,
                    u.full_name,
                    s.admission_number,
                    c.class_name,
                    ay.year_name,
                    t.term_name,
                    COALESCE(SUM(p.amount_due), 0) AS total_due,
                    COALESCE(SUM(p.amount_paid), 0) AS total_paid,
                    COALESCE(SUM(p.balance), 0) AS total_balance,
                    COUNT(p.id) AS payment_count
                FROM students s
                INNER JOIN users u ON u.id = s.user_id
                LEFT JOIN classes c ON c.id = s.class_id
                LEFT JOIN payments p ON p.student_id = s.id
                LEFT JOIN academic_years ay ON ay.id = p.academic_year_id
                LEFT JOIN terms t ON t.id = p.term_id
                WHERE 1 = 1';

        $params = [];

        if (!empty($filters['academic_year_id'])) {
            $sql .= ' AND p.academic_year_id = :academic_year_id';
            $params[':academic_year_id'] = (int) $filters['academic_year_id'];
        }

        if (!empty($filters['term_id'])) {
            $sql .= ' AND p.term_id = :term_id';
            $params[':term_id'] = (int) $filters['term_id'];
        }

        if (!empty($filters['class_id'])) {
            $sql .= ' AND s.class_id = :class_id';
            $params[':class_id'] = (int) $filters['class_id'];
        }

        $sql .= ' GROUP BY s.id, u.full_name, s.admission_number, c.class_name, ay.year_name, t.term_name
                  ORDER BY u.full_name ASC';

        $stmt = Database::query($sql, $params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function reportSummary(array $filters)
    {
        $sql = 'SELECT
                    COALESCE(SUM(p.amount_due), 0) AS total_due,
                    COALESCE(SUM(p.amount_paid), 0) AS total_paid,
                    COALESCE(SUM(p.balance), 0) AS total_balance,
                    COUNT(p.id) AS payment_count
                FROM payments p
                INNER JOIN students s ON s.id = p.student_id
                WHERE 1 = 1';

        $params = [];

        if (!empty($filters['academic_year_id'])) {
            $sql .= ' AND p.academic_year_id = :academic_year_id';
            $params[':academic_year_id'] = (int) $filters['academic_year_id'];
        }

        if (!empty($filters['term_id'])) {
            $sql .= ' AND p.term_id = :term_id';
            $params[':term_id'] = (int) $filters['term_id'];
        }

        if (!empty($filters['class_id'])) {
            $sql .= ' AND s.class_id = :class_id';
            $params[':class_id'] = (int) $filters['class_id'];
        }

        $stmt = Database::query($sql, $params);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: [
            'total_due' => 0,
            'total_paid' => 0,
            'total_balance' => 0,
            'payment_count' => 0,
        ];
    }
}