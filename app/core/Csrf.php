<?php
declare(strict_types=1);

class Csrf
{
    public static function token()
    {
        if (!Session::has('_csrf_token')) {
            Session::put('_csrf_token', bin2hex(random_bytes(32)));
        }

        return Session::get('_csrf_token');
    }

    public static function validate($token)
    {
        $sessionToken = Session::get('_csrf_token');

        if (!$sessionToken || !is_string($token)) {
            return false;
        }

        return hash_equals($sessionToken, $token);
    }

    public static function refresh()
    {
        Session::put('_csrf_token', bin2hex(random_bytes(32)));
    }
}