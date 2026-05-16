<?php
declare(strict_types=1);

return [
    'app_name' => env('APP_NAME', 'School SMS'),
    'env' => env('APP_ENV', 'production'),
    'base_url' => rtrim(env('APP_URL', 'http://localhost:8081/school-sms/public'), '/'),
    'timezone' => env('APP_TIMEZONE', 'Africa/Dar_es_Salaam'),
    'session' => [
        'name' => env('SESSION_NAME', 'school_sms_session'),
        'lifetime' => (int) env('SESSION_LIFETIME', 120),
        'secure' => filter_var(env('SESSION_SECURE', false), FILTER_VALIDATE_BOOLEAN),
        'httponly' => true,
        'samesite' => 'Lax',
    ],
    'roles' => [
        'super_admin' => 'Super Admin',
        'teacher' => 'Teacher',
        'student' => 'Student',
    ],
];