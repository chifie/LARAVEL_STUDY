<?php
declare(strict_types=1);

class AuthController
{
    public function showLogin()
    {
        if (Auth::check() && !Auth::mustChangePassword()) {
            Response::redirect(Auth::dashboardUrl());
        }

        require_once app_path('views/auth/login.php');
    }

    public function login()
    {
        if (!is_post()) {
            Response::redirect(Auth::loginUrl());
        }

        if (!Csrf::validate($_POST['_token'] ?? '')) {
            Session::flash('error', 'Security check failed. Please try again.');
            Response::redirect(Auth::loginUrl());
        }

        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        Session::put('_old_input', [
            'email' => $email,
        ]);

        $errors = Validator::validate([
            'email' => $email,
            'password' => $password,
        ], [
            'email' => 'required|email|max:190',
            'password' => 'required|min:6',
        ]);

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            Session::flash('error', Validator::firstError($errors));
            Response::redirect(Auth::loginUrl());
        }

        $user = User::findByEmail($email);

        if (!$user || (int) $user['is_active'] !== 1 || !password_verify($password, $user['password_hash'])) {
            Session::flash('error', 'Invalid login details or account is inactive.');
            Response::redirect(Auth::loginUrl());
        }

        Auth::login($user);
        User::updateLastLogin($user['id']);

        Session::forget('_old_input');

        if ((int) $user['must_change_password'] === 1) {
            Response::redirect(Auth::changePasswordUrl());
        }

        Response::redirect(Auth::dashboardUrl());
    }

    public function showChangePassword()
    {
        Auth::requireLogin();

        if (!Auth::mustChangePassword()) {
            Response::redirect(Auth::dashboardUrl());
        }

        require_once app_path('views/auth/change_password.php');
    }

    public function changePassword()
    {
        Auth::requireLogin();

        if (!is_post()) {
            Response::redirect(Auth::changePasswordUrl());
        }

        if (!Csrf::validate($_POST['_token'] ?? '')) {
            Session::flash('error', 'Security check failed. Please try again.');
            Response::redirect(Auth::changePasswordUrl());
        }

        $currentPassword = (string) ($_POST['current_password'] ?? '');
        $newPassword = (string) ($_POST['new_password'] ?? '');
        $newPasswordConfirmation = (string) ($_POST['new_password_confirmation'] ?? '');

        $errors = Validator::validate([
            'current_password' => $currentPassword,
            'new_password' => $newPassword,
            'new_password_confirmation' => $newPasswordConfirmation,
        ], [
            'current_password' => 'required|min:6',
            'new_password' => 'required|min:8',
            'new_password_confirmation' => 'required|same:new_password',
        ]);

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            Session::flash('error', Validator::firstError($errors));
            Response::redirect(Auth::changePasswordUrl());
        }

        $freshUser = User::findById(Auth::id());

        if (!$freshUser || !password_verify($currentPassword, $freshUser['password_hash'])) {
            Session::flash('error', 'Current password is incorrect.');
            Response::redirect(Auth::changePasswordUrl());
        }

        $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        User::updatePassword($freshUser['id'], $newPasswordHash);

        Auth::refresh([
            'must_change_password' => 0,
        ]);

        Session::flash('success', 'Password changed successfully.');
        Response::redirect(Auth::dashboardUrl());
    }

    public function dashboard()
    {
        Response::redirect(Auth::dashboardUrl())
    }
}