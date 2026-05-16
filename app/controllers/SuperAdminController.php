<?php
declare(strict_types=1);

class SuperAdminController
{
    public function dashboard()
    {
        Auth::requireRole(['super_admin']);

        $stats = [
            'users' => (int) Database::query('SELECT COUNT(*) AS total FROM users')->fetch(PDO::FETCH_ASSOC)['total'],
            'teachers' => (int) Database::query('SELECT COUNT(*) AS total FROM teachers')->fetch(PDO::FETCH_ASSOC)['total'],
            'students' => (int) Database::query('SELECT COUNT(*) AS total FROM students')->fetch(PDO::FETCH_ASSOC)['total'],
            'active_users' => (int) Database::query('SELECT COUNT(*) AS total FROM users WHERE is_active = 1')->fetch(PDO::FETCH_ASSOC)['total'],
        ];

        $latestUsers = Database::query(
            'SELECT id, full_name, email, role, is_active, must_change_password, created_at
             FROM users
             ORDER BY created_at DESC
             LIMIT 5'
        )->fetchAll(PDO::FETCH_ASSOC);

        $user = Auth::user();

        require app_path('views/admin/dashboard.php');
    }

    public function users()
    {
        Auth::requireRole(['super_admin']);

        $users = User::all();
        $success = flash('success');
        $error = flash('error');

        require app_path('views/admin/users/index.php');
    }

    public function showCreateTeacher()
    {
        Auth::requireRole(['super_admin']);

        $error = flash('error');
        $success = flash('success');
        require app_path('views/admin/users/create_teacher.php');
    }

    public function storeTeacher()
    {
        Auth::requireRole(['super_admin']);

        if (!is_post()) {
            Response::redirect(url('?page=admin.users.create-teacher'));
        }

        if (!Csrf::validate($_POST['_token'] ?? '')) {
            Session::flash('error', 'Security check failed.');
            Response::redirect(url('?page=admin.users.create-teacher'));
        }

        $data = [
            'full_name' => trim((string) ($_POST['full_name'] ?? '')),
            'email' => trim((string) ($_POST['email'] ?? '')),
            'phone' => trim((string) ($_POST['phone'] ?? '')),
            'staff_number' => trim((string) ($_POST['staff_number'] ?? '')),
            'gender' => trim((string) ($_POST['gender'] ?? '')),
            'date_of_birth' => trim((string) ($_POST['date_of_birth'] ?? '')),
            'qualification' => trim((string) ($_POST['qualification'] ?? '')),
            'specialization' => trim((string) ($_POST['specialization'] ?? '')),
            'address' => trim((string) ($_POST['address'] ?? '')),
            'employment_date' => trim((string) ($_POST['employment_date'] ?? '')),
        ];

        $errors = Validator::validate($data, [
            'full_name' => 'required|min:3|max:150',
            'email' => 'required|email|max:190',
            'phone' => 'nullable|max:30',
            'staff_number' => 'required|min:3|max:50',
            'gender' => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'qualification' => 'nullable|max:100',
            'specialization' => 'nullable|max:100',
            'address' => 'nullable|max:255',
            'employment_date' => 'nullable|date',
        ]);

        if (User::existsByEmail($data['email'])) {
            $errors['email'] = 'A user with this email already exists.';
        }

        if (Teacher::existsByStaffNumber($data['staff_number'])) {
            $errors['staff_number'] = 'This staff number already exists.';
        }

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            Session::flash('error', Validator::firstError($errors));
            Session::put('_old_input', $data);
            Response::redirect(url('?page=admin.users.create-teacher'));
        }

        $tempPassword = $this->generateTemporaryPassword(10);

        try {
            Database::beginTransaction();

            $userId = User::create([
                'full_name' => $data['full_name'],
                'email' => $data['email'],
                'phone' => $data['phone'] !== '' ? $data['phone'] : null,
                'password' => $tempPassword,
                'role' => 'teacher',
                'must_change_password' => 1,
                'is_active' => 1,
            ]);

            Teacher::createProfile($userId, $data);

            ActivityLog::create(
                Auth::id(),
                'CREATE_TEACHER',
                'teacher',
                $userId,
                'Created teacher account for ' . $data['full_name']
            );

            Database::commit();

            Session::forget('_old_input');
            Session::flash('success', 'Teacher account created successfully. Temporary password: ' . $tempPassword);
            Response::redirect(url('?page=admin.users'));
        } catch (Throwable $e) {
            if (Database::pdo()->inTransaction()) {
                Database::rollBack();
            }

            Session::flash('error', 'Could not create teacher account: ' . $e->getMessage());
            Response::redirect(url('?page=admin.users.create-teacher'));
        }
    }

    public function showCreateStudent()
    {
        Auth::requireRole(['super_admin']);

        $classes = Database::query(
            'SELECT id, class_name FROM classes WHERE status = "active" ORDER BY class_name ASC'
        )->fetchAll(PDO::FETCH_ASSOC);

        $error = flash('error');
        $success = flash('success');

        require app_path('views/admin/users/create_student.php');
    }

    public function storeStudent()
    {
        Auth::requireRole(['super_admin']);

        if (!is_post()) {
            Response::redirect(url('?page=admin.users.create-student'));
        }

        if (!Csrf::validate($_POST['_token'] ?? '')) {
            Session::flash('error', 'Security check failed.');
            Response::redirect(url('?page=admin.users.create-student'));
        }

        $data = [
            'full_name' => trim((string) ($_POST['full_name'] ?? '')),
            'email' => trim((string) ($_POST['email'] ?? '')),
            'phone' => trim((string) ($_POST['phone'] ?? '')),
            'admission_number' => trim((string) ($_POST['admission_number'] ?? '')),
            'gender' => trim((string) ($_POST['gender'] ?? '')),
            'date_of_birth' => trim((string) ($_POST['date_of_birth'] ?? '')),
            'class_id' => trim((string) ($_POST['class_id'] ?? '')),
            'guardian_name' => trim((string) ($_POST['guardian_name'] ?? '')),
            'guardian_phone' => trim((string) ($_POST['guardian_phone'] ?? '')),
            'guardian_email' => trim((string) ($_POST['guardian_email'] ?? '')),
            'address' => trim((string) ($_POST['address'] ?? '')),
            'admission_date' => trim((string) ($_POST['admission_date'] ?? '')),
        ];

        $errors = Validator::validate($data, [
            'full_name' => 'required|min:3|max:150',
            'email' => 'required|email|max:190',
            'phone' => 'nullable|max:30',
            'admission_number' => 'required|min:3|max:50',
            'gender' => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'class_id' => 'nullable|integer',
            'guardian_name' => 'nullable|max:150',
            'guardian_phone' => 'nullable|max:30',
            'guardian_email' => 'nullable|email|max:190',
            'address' => 'nullable|max:255',
            'admission_date' => 'nullable|date',
        ]);

        if (User::existsByEmail($data['email'])) {
            $errors['email'] = 'A user with this email already exists.';
        }

        if (Student::existsByAdmissionNumber($data['admission_number'])) {
            $errors['admission_number'] = 'This admission number already exists.';
        }

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            Session::flash('error', Validator::firstError($errors));
            Session::put('_old_input', $data);
            Response::redirect(url('?page=admin.users.create-student'));
        }

        $tempPassword = $this->generateTemporaryPassword(10);

        try {
            Database::beginTransaction();

            $userId = User::create([
                'full_name' => $data['full_name'],
                'email' => $data['email'],
                'phone' => $data['phone'] !== '' ? $data['phone'] : null,
                'password' => $tempPassword,
                'role' => 'student',
                'must_change_password' => 1,
                'is_active' => 1,
            ]);

            Student::createProfile($userId, [
                'admission_number' => $data['admission_number'],
                'gender' => $data['gender'],
                'date_of_birth' => $data['date_of_birth'],
                'class_id' => $data['class_id'],
                'guardian_name' => $data['guardian_name'],
                'guardian_phone' => $data['guardian_phone'],
                'guardian_email' => $data['guardian_email'],
                'address' => $data['address'],
                'admission_date' => $data['admission_date'],
            ]);

            ActivityLog::create(
                Auth::id(),
                'CREATE_STUDENT',
                'student',
                $userId,
                'Created student account for ' . $data['full_name']
            );

            Database::commit();

            Session::forget('_old_input');
            Session::flash('success', 'Student account created successfully. Temporary password: ' . $tempPassword);
            Response::redirect(url('?page=admin.users'));
        } catch (Throwable $e) {
            if (Database::pdo()->inTransaction()) {
                Database::rollBack();
            }

            Session::flash('error', 'Could not create student account: ' . $e->getMessage());
            Response::redirect(url('?page=admin.users.create-student'));
        }
    }

    public function toggleUserStatus()
    {
        Auth::requireRole(['super_admin']);

        if (!is_post()) {
            Response::redirect(url('?page=admin.users'));
        }

        if (!Csrf::validate($_POST['_token'] ?? '')) {
            Session::flash('error', 'Security check failed.');
            Response::redirect(url('?page=admin.users'));
        }

        $userId = (int) ($_POST['user_id'] ?? 0);
        $current = User::findById($userId);

        if (!$current) {
            Session::flash('error', 'User not found.');
            Response::redirect(url('?page=admin.users'));
        }

        if ((int) $current['id'] === Auth::id()) {
            Session::flash('error', 'You cannot deactivate your own account.');
            Response::redirect(url('?page=admin.users'));
        }

        if ((string) $current['role'] === 'super_admin' && (int) $current['id'] !== Auth::id()) {
            // allow toggle only if needed; keeping it simple but still protected from self-lockout
        }

        if (User::toggleActive($userId)) {
            $updated = User::findById($userId);
            ActivityLog::create(
                Auth::id(),
                'TOGGLE_USER_STATUS',
                'user',
                $userId,
                'Changed user status to ' . ((int) $updated['is_active'] === 1 ? 'active' : 'inactive')
            );

            Session::flash('success', 'User status updated successfully.');
        } else {
            Session::flash('error', 'Could not update user status.');
        }

        Response::redirect(url('?page=admin.users'));
    }

    private function generateTemporaryPassword($length = 10)
    {
        $letters = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';
        $numbers = '23456789';
        $symbols = '@#$%&*?';

        $pool = $letters . $numbers . $symbols;
        $password = '';

        for ($i = 0; $i < $length; $i++) {
            $password .= $pool[random_int(0, strlen($pool) - 1)];
        }

        return $password;
    }
}