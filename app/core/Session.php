<?php
declare(strict_types=1);

class Session
{
    public static function start(array $options = [])
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        $name = isset($options['name']) ? (string) $options['name'] : 'school_sms_session';
        $lifetime = isset($options['lifetime']) ? (int) $options['lifetime'] * 60 : 7200;
        $secure = !empty($options['secure']);
        $httponly = array_key_exists('httponly', $options) ? (bool) $options['httponly'] : true;
        $samesite = isset($options['samesite']) ? (string) $options['samesite'] : 'Lax';

        ini_set('session.use_only_cookies', '1');
        ini_set('session.use_strict_mode', '1');
        ini_set('session.gc_maxlifetime', (string) $lifetime);

        session_name($name);
        session_set_cookie_params([
            'lifetime' => $lifetime,
            'path' => '/',
            'domain' => '',
            'secure' => $secure,
            'httponly' => $httponly,
            'samesite' => $samesite,
        ]);

        session_start();

        if (!isset($_SESSION['_initiated'])) {
            session_regenerate_id(true);
            $_SESSION['_initiated'] = time();
        }
    }

    public static function regenerate()
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }

    public static function put($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public static function get($key, $default = null)
    {
        return array_key_exists($key, $_SESSION) ? $_SESSION[$key] : $default;
    }

    public static function has($key)
    {
        return array_key_exists($key, $_SESSION);
    }

    public static function forget($key)
    {
        unset($_SESSION[$key]);
    }

    public static function flash($key, $value)
    {
        if (!isset($_SESSION['_flash'])) {
            $_SESSION['_flash'] = [];
        }

        $_SESSION['_flash'][$key] = $value;
    }

    public static function pullFlash($key, $default = null)
    {
        if (!isset($_SESSION['_flash']) || !array_key_exists($key, $_SESSION['_flash'])) {
            return $default;
        }

        $value = $_SESSION['_flash'][$key];
        unset($_SESSION['_flash'][$key]);

        return $value;
    }

    public static function all()
    {
        return $_SESSION;
    }

    public static function destroy()
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }
}