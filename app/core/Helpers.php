<?php
declare(strict_types=1);

function load_env($path)
{
    if (!is_file($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (!$lines) {
        return;
    }

    foreach ($lines as $line) {
        $line = trim($line);

        if ($line === '' || substr($line, 0, 1) === '#') {
            continue;
        }

        if (strpos($line, '=') === false) {
            continue;
        }

        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        $first = substr($value, 0, 1);
        $last = substr($value, -1);

        if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
            $value = substr($value, 1, -1);
        }

        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
        putenv($name . '=' . $value);
    }
}

function env($key, $default = null)
{
    if (array_key_exists($key, $_ENV)) {
        return $_ENV[$key];
    }

    if (array_key_exists($key, $_SERVER)) {
        return $_SERVER[$key];
    }

    $value = getenv($key);

    return ($value === false) ? $default : $value;
}

function config_get($array, $key = null, $default = null)
{
    if ($key === null) {
        return $array;
    }

    $segments = explode('.', $key);
    foreach ($segments as $segment) {
        if (!is_array($array) || !array_key_exists($segment, $array)) {
            return $default;
        }
        $array = $array[$segment];
    }

    return $array;
}

function app_config($key = null, $default = null)
{
    $config = isset($GLOBALS['APP_CONFIG']) && is_array($GLOBALS['APP_CONFIG']) ? $GLOBALS['APP_CONFIG'] : [];
    return config_get($config, $key, $default);
}

function db_config($key = null, $default = null)
{
    $config = isset($GLOBALS['DB_CONFIG']) && is_array($GLOBALS['DB_CONFIG']) ? $GLOBALS['DB_CONFIG'] : [];
    return config_get($config, $key, $default);
}

function base_path($path = '')
{
    $base = defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__, 2);
    return $path === '' ? $base : $base . '/' . ltrim($path, '/');
}

function app_path($path = '')
{
    return base_path('app' . ($path !== '' ? '/' . ltrim($path, '/') : ''));
}

function public_path($path = '')
{
    return base_path('public' . ($path !== '' ? '/' . ltrim($path, '/') : ''));
}

function storage_path($path = '')
{
    return base_path('storage' . ($path !== '' ? '/' . ltrim($path, '/') : ''));
}

function url($path = '')
{
    $base = rtrim((string) app_config('base_url', ''), '/');

    if ($path === '' || $path === '/') {
        return $base . '/';
    }

    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }

    return $base . '/' . ltrim($path, '/');
}

function asset($path = '')
{
    return url('assets/' . ltrim($path, '/'));
}

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect($to)
{
    header('Location: ' . $to);
    exit;
}

function request_method()
{
    return isset($_SERVER['REQUEST_METHOD']) ? strtoupper($_SERVER['REQUEST_METHOD']) : 'GET';
}

function is_post()
{
    return request_method() === 'POST';
}

function is_get()
{
    return request_method() === 'GET';
}

function old($key, $default = '')
{
    $input = Session::get('_old_input', []);
    if (is_array($input) && array_key_exists($key, $input)) {
        return $input[$key];
    }

    return $default;
}

function flash($key, $default = null)
{
    return Session::pullFlash($key, $default);
}

function csrf_field()
{
    return '<input type="hidden" name="_token" value="' . e(Csrf::token()) . '">';
}

function auth_user()
{
    return Auth::user();
}

function auth_check()
{
    return Auth::check();
}

function auth_role()
{
    return Auth::role();
}

function view($view, array $data = [])
{
    $viewFile = app_path('views/' . str_replace('.', '/', $view) . '.php');

    if (!is_file($viewFile)) {
        throw new RuntimeException('View not found: ' . $viewFile);
    }

    extract($data, EXTR_SKIP);
    require $viewFile;
}

function render_view($view, array $data = [])
{
    ob_start();
    view($view, $data);
    return ob_get_clean();
}

function partial($view, array $data = [])
{
    echo render_view($view, $data);
}

function current_page($default = '')
{
    return trim((string) ($_GET['page'] ?? $default));
}

function route_is($target, $current = null)
{
    $current = $current === null ? current_page() : (string) $current;
    return $current === (string) $target;
}

function route_starts_with($prefix, $current = null)
{
    $current = $current === null ? current_page() : (string) $current;
    $prefix = (string) $prefix;

    if ($prefix === '') {
        return true;
    }

    return strpos($current, $prefix) === 0;
}

function nav_active($target, $current = null, $prefix = false)
{
    return $prefix ? (route_starts_with($target, $current) ? 'active' : '') : (route_is($target, $current) ? 'active' : '');
}

function role_label_text($role)
{
    $roles = app_config('roles', []);
    $role = (string) $role;

    if (isset($roles[$role])) {
        return $roles[$role];
    }

    return ucfirst(str_replace('_', ' ', $role));
}
