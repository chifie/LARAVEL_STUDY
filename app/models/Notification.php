<?php
declare(strict_types=1);

class Notification
{
    public static function createMany(array $userIds, array $data)
    {
        $count = 0;

        foreach ($userIds as $userId) {
            Database::query(
                'INSERT INTO notifications
                    (user_id, type, title, message, is_read, created_at, updated_at)
                 VALUES
                    (:user_id, :type, :title, :message, 0, NOW(), NOW())',
                [
                    ':user_id' => (int) $userId,
                    ':type' => $data['type'],
                    ':title' => $data['title'],
                    ':message' => $data['message'],
                ]
            );

            $count++;
        }

        return $count;
    }

    public static function unreadCountForUser($userId)
    {
        $stmt = Database::query(
            'SELECT COUNT(*) AS total
             FROM notifications
             WHERE user_id = :user_id AND is_read = 0',
            [':user_id' => (int) $userId]
        );

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int) $row['total'];
    }

    public static function allForUser($userId)
    {
        $stmt = Database::query(
            'SELECT *
             FROM notifications
             WHERE user_id = :user_id
             ORDER BY is_read ASC, created_at DESC, id DESC',
            [':user_id' => (int) $userId]
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function markRead($id, $userId)
    {
        return Database::query(
            'UPDATE notifications
             SET is_read = 1, updated_at = NOW()
             WHERE id = :id AND user_id = :user_id',
            [
                ':id' => (int) $id,
                ':user_id' => (int) $userId,
            ]
        )->rowCount() > 0;
    }

    public static function markAllRead($userId)
    {
        return Database::query(
            'UPDATE notifications
             SET is_read = 1, updated_at = NOW()
             WHERE user_id = :user_id AND is_read = 0',
            [':user_id' => (int) $userId]
        )->rowCount() > 0;
    }

    public static function findByUser($userId)
    {
        return self::allForUser($userId);
    }
}