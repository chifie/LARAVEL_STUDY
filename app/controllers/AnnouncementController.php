<?php
declare(strict_types=1);

class AnnouncementController
{
    public function index()
    {
        Auth::requireRole(['super_admin']);

        $announcements = Announcement::allWithRelations();
        $success = flash('success');
        $error = flash('error');

        require app_path('views/admin/announcements/index.php');
    }

    public function create()
    {
        Auth::requireRole(['super_admin']);

        $classes = SchoolClass::allActiveWithRelations();
        $success = flash('success');
        $error = flash('error');

        require app_path('views/admin/announcements/create.php');
    }

    public function store()
    {
        Auth::requireRole(['super_admin']);

        if (!is_post()) {
            Response::redirect(url('?page=admin.announcements.create'));
        }

        if (!Csrf::validate($_POST['_token'] ?? '')) {
            Session::flash('error', 'Security check failed.');
            Response::redirect(url('?page=admin.announcements.create'));
        }

        $data = [
            'title' => trim((string) ($_POST['title'] ?? '')),
            'message' => trim((string) ($_POST['message'] ?? '')),
            'audience' => trim((string) ($_POST['audience'] ?? 'all')),
            'class_id' => trim((string) ($_POST['class_id'] ?? '')),
            'is_published' => isset($_POST['is_published']) ? 1 : 0,
        ];

        $errors = Validator::validate($data, [
            'title' => 'required|min:3|max:150',
            'message' => 'required|min:5',
            'audience' => 'required|in:all,teachers,students,class',
            'class_id' => 'nullable|integer',
        ]);

        if ($data['audience'] === 'class' && empty($data['class_id'])) {
            $errors['class_id'] = 'Class is required when audience is set to class.';
        }

        $class = null;
        if (!empty($data['class_id'])) {
            $class = SchoolClass::find($data['class_id']);
            if (!$class) {
                $errors['class_id'] = 'Selected class does not exist.';
            }
        }

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            Session::flash('error', Validator::firstError($errors));
            Session::put('_old_input', $data);
            Response::redirect(url('?page=admin.announcements.create'));
        }

        try {
            Database::beginTransaction();

            $announcementId = Announcement::create([
                'title' => $data['title'],
                'message' => $data['message'],
                'audience' => $data['audience'],
                'class_id' => $data['class_id'] ?: null,
                'created_by' => Auth::id(),
                'is_published' => $data['is_published'],
            ]);

            $announcement = Announcement::find($announcementId);

            $recipientCount = 0;
            if ($data['is_published']) {
                $recipientIds = Announcement::recipientUserIds($announcement);
                $recipientCount = Notification::createMany($recipientIds, [
                    'type' => 'announcement',
                    'title' => $announcement['title'],
                    'message' => $announcement['message'],
                ]);
            }

            ActivityLog::create(
                Auth::id(),
                'CREATE_ANNOUNCEMENT',
                'announcement',
                $announcementId,
                'Created announcement "' . $data['title'] . '" for ' . $data['audience'] . ' audience. Notifications sent: ' . $recipientCount
            );

            Database::commit();

            Session::forget('_old_input');
            Session::flash('success', $data['is_published']
                ? 'Announcement published successfully.'
                : 'Announcement saved as draft successfully.'
            );
        } catch (Throwable $e) {
            if (Database::pdo()->inTransaction()) {
                Database::rollBack();
            }

            Session::flash('error', 'Could not create announcement: ' . $e->getMessage());
        }

        Response::redirect(url('?page=admin.announcements'));
    }

    public function publish()
    {
        Auth::requireRole(['super_admin']);

        if (!is_post()) {
            Response::redirect(url('?page=admin.announcements'));
        }

        if (!Csrf::validate($_POST['_token'] ?? '')) {
            Session::flash('error', 'Security check failed.');
            Response::redirect(url('?page=admin.announcements'));
        }

        $announcementId = (int) ($_POST['announcement_id'] ?? 0);
        $announcement = Announcement::find($announcementId);

        if (!$announcement) {
            Session::flash('error', 'Announcement not found.');
            Response::redirect(url('?page=admin.announcements'));
        }

        try {
            if ((int) $announcement['is_published'] === 1) {
                Session::flash('success', 'Announcement is already published.');
                Response::redirect(url('?page=admin.announcements'));
            }

            Database::beginTransaction();

            $published = Announcement::publish($announcementId);
            $recipientIds = Announcement::recipientUserIds($published);
            $sent = Notification::createMany($recipientIds, [
                'type' => 'announcement',
                'title' => $published['title'],
                'message' => $published['message'],
            ]);

            ActivityLog::create(
                Auth::id(),
                'PUBLISH_ANNOUNCEMENT',
                'announcement',
                $announcementId,
                'Published announcement "' . $published['title'] . '". Notifications sent: ' . $sent
            );

            Database::commit();

            Session::flash('success', 'Announcement published successfully.');
        } catch (Throwable $e) {
            if (Database::pdo()->inTransaction()) {
                Database::rollBack();
            }

            Session::flash('error', 'Could not publish announcement: ' . $e->getMessage());
        }

        Response::redirect(url('?page=admin.announcements'));
    }

    public function feed()
    {
        Auth::requireLogin();

        $user = Auth::user();
        $announcements = Announcement::visibleForUser($user);
        $title = 'Announcements';
        $success = flash('success');
        $error = flash('error');

        if (Auth::hasRole('teacher')) {
            require app_path('views/teacher/announcements/index.php');
            return;
        }

        if (Auth::hasRole('student')) {
            require app_path('views/student/announcements/index.php');
            return;
        }

        require app_path('views/announcements/index.php');
    }
}