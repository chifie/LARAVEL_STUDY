<?php
declare(strict_types=1);

class Auth
{
    public static function check()
    {
        return Session::has('auth') && is_array(Session::get('auth'));
    }

    public static function user()
    {
        if (!self::check()) {
            return null;
        }

        return Session::get('auth');
    }

    public static function id()
    {
        $user = self::user();
        return $user ? (int) $user['id'] : null;
    }

    public static function role()
    {
        $user = self::user();
        return $user ? (string) $user['role'] : null;
    }

    public static function mustChangePassword()
    {
        $user = self::user();
        return $user ? ((int) $user['must_change_password'] === 1) : false;
    }

    public static function login(array $user)
    {
        Session::regenerate();

        Session::put('auth', [
            'id' => (int) $user['id'],
            'full_name' => $user['full_name'],
            'email' => $user['email'],
            'role' => $user['role'],
            'must_change_password' => (int) $user['must_change_password'],
            'is_active' => (int) $user['is_active'],
            'last_login_at' => isset($user['last_login_at']) ? $user['last_login_at'] : null,
        ]);
    }

    public static function refresh(array $data)
    {
        if (!self::check()) {
            return;
        }

        $current = self::user();
        Session::put('auth', array_merge($current, $data));
    }

    public static function logout()
    {
        Session::destroy();
    }

    public static function hasRole($role)
    {
        return self::check() && self::role() === $role;
    }

    public static function requireLogin()
    {
        if (!self::check()) {
            Session::flash('error', 'Please login first.');
            Response::redirect(self::loginUrl());
        }
    }

    public static function requireRole(array $roles)
    {
        self::requireLogin();

        if (!in_array(self::role(), $roles, true)) {
            Response::forbidden('You are not allowed to access this page.');
        }
    }

    public static function requirePasswordChange()
    {
        self::requireLogin();

        if (self::mustChangePassword()) {
            Response::redirect(self::changePasswordUrl());
        }
    }

    public static function loginUrl()
    {
        return url('?page=login');
    }

    public static function changePasswordUrl()
    {
        return url('?page=change-password');
    }

    public static function dashboardUrl()
    {
        if (!self::check()) {
            return self::loginUrl();
        }

        $role = self::role();

        if ($role === 'super_admin') {
            return url('?page=admin.dashboard');
        }

        if ($role === 'teacher') {
            return url('?page=teacher.dashboard');
        }

        if ($role === 'student') {
            return url('?page=student.dashboard');
        }

        return self::loginUrl();
    }
}