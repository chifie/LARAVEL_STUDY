<?php
declare(strict_types=1);

if (php_sapi_name() !== 'cli') {
    exit("This script can only be run from the command line.\n");
}

define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/bootstrap.php';

$name = $argv[1] ?? 'Super Admin';
$email = $argv[2] ?? 'admin@school.local';
$password = $argv[3] ?? 'Admin@12345!';

try {
    if (User::existsByEmail($email)) {
        echo "A user with this email already exists: {$email}\n";
        exit(0);
    }

    $userId = User::create([
        'full_name' => $name,
        'email' => $email,
        'password' => $password,
        'role' => 'super_admin',
        'must_change_password' => 1,
        'is_active' => 1,
    ]);

    echo "Super Admin created successfully.\n";
    echo "User ID: {$userId}\n";
    echo "Email: {$email}\n";
    echo "Temporary Password: {$password}\n";
    echo "Login once and change the password immediately.\n";
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}