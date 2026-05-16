<?php
declare(strict_types=1);

class ActivityLog
{
    public static function create($userId, $action, $entityType, $entityId, $description)
    {
        $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
        $agent = isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 255) : null;

        Database::query(
            'INSERT INTO activity_logs
            (user_id, action, entity_type, entity_id, description, ip_address, user_agent, created_at)
            VALUES
            (:user_id, :action, :entity_type, :entity_id, :description, :ip_address, :user_agent, NOW())',
            [
                ':user_id' => $userId !== null ? (int) $userId : null,
                ':action' => $action,
                ':entity_type' => $entityType,
                ':entity_id' => $entityId !== null ? (int) $entityId : null,
                ':description' => $description,
                ':ip_address' => $ip,
                ':user_agent' => $agent,
            ]
        );
    }

    public static function all($limit = 100)
    {
        $limit = (int) $limit;

        $stmt = Database::query(
            'SELECT l.*, u.full_name AS user_name, u.email AS user_email
             FROM activity_logs l
             LEFT JOIN users u ON u.id = l.user_id
             ORDER BY l.created_at DESC, l.id DESC
             LIMIT ' . $limit
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function report($filters = [], $limit = 250)
    {
        $sql = 'SELECT l.*, u.full_name AS user_name, u.email AS user_email
                FROM activity_logs l
                LEFT JOIN users u ON u.id = l.user_id
                WHERE 1 = 1';
        $params = [];

        if (!empty($filters['user_id'])) {
            $sql .= ' AND l.user_id = :user_id';
            $params[':user_id'] = (int) $filters['user_id'];
        }

        if (!empty($filters['action'])) {
            $sql .= ' AND l.action = :action';
            $params[':action'] = trim((string) $filters['action']);
        }

        if (!empty($filters['from_date'])) {
            $sql .= ' AND DATE(l.created_at) >= :from_date';
            $params[':from_date'] = $filters['from_date'];
        }

        if (!empty($filters['to_date'])) {
            $sql .= ' AND DATE(l.created_at) <= :to_date';
            $params[':to_date'] = $filters['to_date'];
        }

        $sql .= ' ORDER BY l.created_at DESC, l.id DESC LIMIT ' . (int) $limit;

        $stmt = Database::query($sql, $params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function actions()
    {
        $stmt = Database::query(
            'SELECT DISTINCT action
             FROM activity_logs
             ORDER BY action ASC'
        );

        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'action');
    }
}