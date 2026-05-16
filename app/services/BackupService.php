<?php
declare(strict_types=1);

class BackupService
{
    public static function backupDirectory()
    {
        return BASE_PATH . '/storage/backups';
    }

    public static function ensureDirectory()
    {
        $dir = self::backupDirectory();

        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        return $dir;
    }

    public static function listBackups()
    {
        self::ensureDirectory();

        $files = glob(self::backupDirectory() . '/*.sql') ?: [];
        rsort($files);

        $items = [];

        foreach ($files as $file) {
            $items[] = [
                'name' => basename($file),
                'path' => $file,
                'size' => filesize($file),
                'modified_at' => date('Y-m-d H:i:s', filemtime($file)),
            ];
        }

        return $items;
    }

    public static function createBackup()
    {
        self::ensureDirectory();

        $dbConfig = $GLOBALS['DB_CONFIG'] ?? [];
        $database = $dbConfig['dbname'] ?? $dbConfig['database'] ?? 'database';
        $timestamp = date('Ymd_His');
        $fileName = 'backup_' . $database . '_' . $timestamp . '.sql';
        $filePath = self::backupDirectory() . '/' . $fileName;

        $pdo = Database::pdo();
        $handle = fopen($filePath, 'wb');

        if (!$handle) {
            throw new RuntimeException('Unable to create backup file.');
        }

        fwrite($handle, "-- School SMS Database Backup\n");
        fwrite($handle, "-- Created at: " . date('Y-m-d H:i:s') . "\n\n");
        fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n\n");

        $tablesStmt = $pdo->query('SHOW TABLES');
        $tables = $tablesStmt ? $tablesStmt->fetchAll(PDO::FETCH_COLUMN) : [];

        foreach ($tables as $table) {
            fwrite($handle, "\n-- Table: {$table}\n");
            fwrite($handle, "DROP TABLE IF EXISTS `{$table}`;\n");

            $createStmt = $pdo->query('SHOW CREATE TABLE `' . str_replace('`', '``', $table) . '`');
            $createRow = $createStmt ? $createStmt->fetch(PDO::FETCH_ASSOC) : null;

            if ($createRow) {
                $createSql = array_values($createRow)[1] ?? null;
                if ($createSql) {
                    fwrite($handle, $createSql . ";\n\n");
                }
            }

            $dataStmt = $pdo->query('SELECT * FROM `' . str_replace('`', '``', $table) . '`');
            $rows = $dataStmt ? $dataStmt->fetchAll(PDO::FETCH_ASSOC) : [];

            if (!empty($rows)) {
                $columns = array_keys($rows[0]);
                $quotedColumns = array_map(fn ($col) => '`' . $col . '`', $columns);
                $columnList = implode(', ', $quotedColumns);

                foreach ($rows as $row) {
                    $values = [];

                    foreach ($columns as $column) {
                        $value = $row[$column];

                        if ($value === null) {
                            $values[] = 'NULL';
                        } elseif (is_int($value) || is_float($value)) {
                            $values[] = (string) $value;
                        } else {
                            $values[] = $pdo->quote((string) $value);
                        }
                    }

                    fwrite(
                        $handle,
                        'INSERT INTO `' . $table . '` (' . $columnList . ') VALUES (' . implode(', ', $values) . ");\n"
                    );
                }

                fwrite($handle, "\n");
            }
        }

        fwrite($handle, "\nSET FOREIGN_KEY_CHECKS=1;\n");
        fclose($handle);

        return $filePath;
    }

    public static function fileExists($fileName)
    {
        $base = basename($fileName);
        $path = self::backupDirectory() . '/' . $base;

        return is_file($path) ? $path : null;
    }

    public static function backupFileInfo($fileName)
    {
        $path = self::fileExists($fileName);

        if (!$path) {
            return null;
        }

        return [
            'name' => basename($path),
            'path' => $path,
            'size' => filesize($path),
            'modified_at' => date('Y-m-d H:i:s', filemtime($path)),
        ];
    }
}