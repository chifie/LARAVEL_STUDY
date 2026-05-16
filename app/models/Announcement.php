<?php
declare(strict_types=1);

class Announcement
{
    public static function find($id)
    {
        $stmt = Database::query(
            'SELECT a.*, u.full_name AS creator_name, c.class_name
             FROM announcements a
             LEFT JOIN users u ON u.id = a.created_by
             LEFT JOIN classes c ON c.id = a.class_id
             WHERE a.id = :id
             LIMIT 1',
            [
                ':id' => (int) $id,
            ]
        );

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public static function allWithRelations()
    {
        $stmt = Database::query(
            'SELECT a.*, u.full_name AS creator_name, c.class_name,
                    (SELECT COUNT(*)
                     FROM notifications n
                     WHERE n.type = "announcement"
                       AND n.title = a.title) AS notification_count
             FROM announcements a
             LEFT JOIN users u ON u.id = a.created_by
             LEFT JOIN classes c ON c.id = a.class_id
             ORDER BY a.created_at DESC, a.id DESC'
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create(array $data)
    {
        Database::query(
            'INSERT INTO announcements
                (title, message, audience, class_id, created_by, is_published, published_at, created_at, updated_at)
             VALUES
                (:title, :message, :audience, :class_id, :created_by, :is_published, :published_at, NOW(), NOW())',
            [
                ':title' => trim((string) ($data['title'] ?? '')),
                ':message' => trim((string) ($data['message'] ?? '')),
                ':audience' => (string) ($data['audience'] ?? 'all'),
                ':class_id' => !empty($data['class_id']) ? (int) $data['class_id'] : null,
                ':created_by' => !empty($data['created_by']) ? (int) $data['created_by'] : null,
                ':is_published' => !empty($data['is_published']) ? 1 : 0,
                ':published_at' => !empty($data['is_published']) ? date('Y-m-d H:i:s') : null,
            ]
        );

        return (int) Database::lastInsertId();
    }

    public static function publish($id)
    {
        $announcement = self::find($id);
        if (!$announcement) {
            throw new RuntimeException('Announcement not found.');
        }

        if ((int) $announcement['is_published'] === 1) {
            return $announcement;
        }

        Database::query(
            'UPDATE announcements
             SET is_published = 1,
                 published_at = NOW(),
                 updated_at = NOW()
             WHERE id = :id',
            [
                ':id' => (int) $id,
            ]
        );

        return self::find($id);
    }

    public static function activePublished()
    {
        $stmt = Database::query(
            'SELECT a.*, u.full_name AS creator_name, c.class_name
             FROM announcements a
             LEFT JOIN users u ON u.id = a.created_by
             LEFT JOIN classes c ON c.id = a.class_id
             WHERE a.is_published = 1
             ORDER BY a.published_at DESC, a.id DESC'
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function visibleForUser(array $user)
    {
        $role = (string) ($user['role'] ?? '');

        if ($role === 'super_admin') {
            return self::activePublished();
        }

        if ($role === 'teacher') {
            $teacher = Teacher::findByUserId((int) ($user['id'] ?? 0));
            if (!$teacher) {
                return [];
            }

            $stmt = Database::query(
                'SELECT DISTINCT a.*, u.full_name AS creator_name, c.class_name
                 FROM announcements a
                 LEFT JOIN users u ON u.id = a.created_by
                 LEFT JOIN classes c ON c.id = a.class_id
                 LEFT JOIN class_subjects cs
                    ON cs.class_id = a.class_id
                   AND cs.teacher_id = :teacher_id_join
                 WHERE a.is_published = 1
                   AND (
                        a.audience = "all"
                        OR a.audience = "teachers"
                        OR (
                            a.audience = "class"
                            AND a.class_id IS NOT NULL
                            AND (
                                cs.teacher_id IS NOT NULL
                                OR c.class_teacher_id = :teacher_id_class
                            )
                        )
                   )
                 ORDER BY a.published_at DESC, a.id DESC',
                [
                    ':teacher_id_join' => (int) $teacher['id'],
                    ':teacher_id_class' => (int) $teacher['id'],
                ]
            );

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        if ($role === 'student') {
            $student = Student::findByUserId((int) ($user['id'] ?? 0));
            if (!$student) {
                return [];
            }

            $stmt = Database::query(
                'SELECT a.*, u.full_name AS creator_name, c.class_name
                 FROM announcements a
                 LEFT JOIN users u ON u.id = a.created_by
                 LEFT JOIN classes c ON c.id = a.class_id
                 WHERE a.is_published = 1
                   AND (
                        a.audience = "all"
                        OR a.audience = "students"
                        OR (a.audience = "class" AND a.class_id = :class_id)
                   )
                 ORDER BY a.published_at DESC, a.id DESC',
                [
                    ':class_id' => (int) ($student['class_id'] ?? 0),
                ]
            );

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return [];
    }

    public static function recipientUserIds(array $announcement)
    {
        $audience = (string) ($announcement['audience'] ?? '');
        $classId = !empty($announcement['class_id']) ? (int) $announcement['class_id'] : null;

        if ($audience === 'all') {
            $stmt = Database::query('SELECT id FROM users WHERE is_active = 1');
            return array_map('intval', array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'id'));
        }

        if ($audience === 'teachers') {
            $stmt = Database::query(
                'SELECT u.id
                 FROM users u
                 WHERE u.role = "teacher"
                   AND u.is_active = 1'
            );
            return array_map('intval', array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'id'));
        }

        if ($audience === 'students') {
            $stmt = Database::query(
                'SELECT u.id
                 FROM users u
                 WHERE u.role = "student"
                   AND u.is_active = 1'
            );
            return array_map('intval', array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'id'));
        }

        if ($audience === 'class' && $classId) {
            $userIds = [];

            $students = Database::query(
                'SELECT u.id
                 FROM students s
                 INNER JOIN users u ON u.id = s.user_id
                 WHERE s.class_id = :class_id
                   AND u.is_active = 1',
                [
                    ':class_id' => $classId,
                ]
            )->fetchAll(PDO::FETCH_ASSOC);

            foreach ($students as $row) {
                $userIds[] = (int) $row['id'];
            }

            $classTeacher = Database::query(
                'SELECT u.id
                 FROM classes c
                 INNER JOIN teachers t ON t.id = c.class_teacher_id
                 INNER JOIN users u ON u.id = t.user_id
                 WHERE c.id = :class_id
                   AND u.is_active = 1',
                [
                    ':class_id' => $classId,
                ]
            )->fetchAll(PDO::FETCH_ASSOC);

            foreach ($classTeacher as $row) {
                $userIds[] = (int) $row['id'];
            }

            $subjectTeachers = Database::query(
                'SELECT DISTINCT u.id
                 FROM class_subjects cs
                 INNER JOIN teachers t ON t.id = cs.teacher_id
                 INNER JOIN users u ON u.id = t.user_id
                 WHERE cs.class_id = :class_id
                   AND u.is_active = 1',
                [
                    ':class_id' => $classId,
                ]
            )->fetchAll(PDO::FETCH_ASSOC);

            foreach ($subjectTeachers as $row) {
                $userIds[] = (int) $row['id'];
            }

            return array_values(array_unique($userIds));
        }

        return [];
    }

    public static function recipientCount(array $announcement)
    {
        return count(self::recipientUserIds($announcement));
    }
}