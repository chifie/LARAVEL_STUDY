<?php
declare(strict_types=1);

class NotificationController
{
    public function index()
    {
        Auth::requireLogin();

        $user = Auth::user();
        $notifications = Notification::allForUser(Auth::id());
        $unreadCount = Notification::unreadCountForUser(Auth::id());
        $success = flash('success');
        $error = flash('error');

        require app_path('views/notifications/index.php');
    }

    public function markRead()
    {
        Auth::requireLogin();

        if (!is_post()) {
            Response::redirect($this->redirectBack());
        }

        if (!Csrf::validate($_POST['_token'] ?? '')) {
            Session::flash('error', 'Security check failed.');
            Response::redirect($this->redirectBack());
        }

        $notificationId = (int) ($_POST['notification_id'] ?? 0);

        if ($notificationId > 0) {
            Notification::markRead($notificationId, Auth::id());
        }

        Response::redirect($this->redirectBack());
    }

    public function markAllRead()
    {
        Auth::requireLogin();

        if (!is_post()) {
            Response::redirect($this->redirectBack());
        }

        if (!Csrf::validate($_POST['_token'] ?? '')) {
            Session::flash('error', 'Security check failed.');
            Response::redirect($this->redirectBack());
        }

        Notification::markAllRead(Auth::id());
        Session::flash('success', 'All notifications marked as read.');

        Response::redirect($this->redirectBack());
    }

    public function redirectBack()
    {
        if (Auth::hasRole('super_admin')) {
            return url('?page=admin.notifications');
        }

        if (Auth::hasRole('teacher')) {
            return url('?page=teacher.notifications');
        }

        if (Auth::hasRole('student')) {
            return url('?page=student.notifications');
        }

        return Auth::dashboardUrl();
    }
}