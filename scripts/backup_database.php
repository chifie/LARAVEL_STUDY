<?php
declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/app/bootstrap.php';

try {
    $file = BackupService::createBackup();
    echo "Backup created successfully: " . $file . PHP_EOL;
    exit(0);
} catch (Throwable $e) {
    fwrite(STDERR, "Backup failed: " . $e->getMessage() . PHP_EOL);
    exit(1);
}