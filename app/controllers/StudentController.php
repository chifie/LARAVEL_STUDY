<?php
declare(strict_types=1);

class StudentController
{
    public function index()
    {
        Auth::requireRole(['super_admin']);

        $students = Student::allDetailed();
        $success = flash('success');
        $error = flash('error');

        require_once app_path('views/admin/students/index.php');
    }

    public function showEnrollForm()
    {
        Auth::requireRole(['super_admin']);

        $students = Student::allDetailed();
        $classes = SchoolClass::allActiveWithRelations();
        $years = AcademicYear::all();
        $currentYear = AcademicYear::current();
        $currentYearId = $currentYear ? (int) $currentYear['id'] : 0;
        $currentTerm = $currentYear ? Term::currentForYear($currentYearId) : null;
        $terms = $currentYear ? Term::allByYear($currentYearId) : [];

        $selectedStudentId = isset($_GET['student_id']) ? (int) $_GET['student_id'] : 0;
        $selectedStudent = $selectedStudentId > 0 ? Student::findDetailedById($selectedStudentId) : null;

        $success = flash('success');
        $error = flash('error');

        require app_path('views/admin/students/enroll.php');
    }

    public function storeEnrollment()
    {
        Auth::requireRole(['super_admin']);

        if (!is_post()) {
            Response::redirect(url('?page=admin.students.enroll'));
        }

        if (!Csrf::validate($_POST['_token'] ?? '')) {
            Session::flash('error', 'Security check failed.');
            Response::redirect(url('?page=admin.students.enroll'));
        }

        $data = [
            'student_id' => trim((string) ($_POST['student_id'] ?? '')),
            'class_id' => trim((string) ($_POST['class_id'] ?? '')),
            'academic_year_id' => trim((string) ($_POST['academic_year_id'] ?? '')),
            'term_id' => trim((string) ($_POST['term_id'] ?? '')),
            'enrollment_date' => trim((string) ($_POST['enrollment_date'] ?? '')),
            'status' => trim((string) ($_POST['status'] ?? 'active')),
        ];

        $errors = Validator::validate($data, [
            'student_id' => 'required|integer',
            'class_id' => 'required|integer',
            'academic_year_id' => 'required|integer',
            'term_id' => 'required|integer',
            'enrollment_date' => 'required|date',
            'status' => 'required|in:active',
        ]);

        $student = Student::findById($data['student_id']);
        $class = SchoolClass::find($data['class_id']);
        $year = AcademicYear::find($data['academic_year_id']);
        $term = Term::find($data['term_id']);

        if (!$student) {
            $errors['student_id'] = 'Selected student does not exist.';
        }

        if (!$class) {
            $errors['class_id'] = 'Selected class does not exist.';
        } elseif ((string) $class['status'] !== 'active') {
            $errors['class_id'] = 'Selected class is inactive.';
        }

        if (!$year) {
            $errors['academic_year_id'] = 'Selected academic year does not exist.';
        }

        if (!$term) {
            $errors['term_id'] = 'Selected term does not exist.';
        }

        if ($term && $year && (int) $term['academic_year_id'] !== (int) $year['id']) {
            $errors['term_id'] = 'The term does not belong to the selected academic year.';
        }

        if (!$errors && Enrollment::exists($data['student_id'], $data['class_id'], $data['academic_year_id'], $data['term_id'])) {
            $errors['class_id'] = 'This student is already enrolled in that class for the selected year and term.';
        }

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            Session::flash('error', Validator::firstError($errors));
            Session::put('_old_input', $data);
            Response::redirect(url('?page=admin.students.enroll'));
        }

        try {
            Database::beginTransaction();

            $enrollmentId = Enrollment::create($data);
            Student::updateClass($data['student_id'], $data['class_id']);
            Student::updateStatus($data['student_id'], 'active');

            ActivityLog::create(
                Auth::id(),
                'ENROLL_STUDENT',
                'enrollment',
                $enrollmentId,
                'Enrolled student ID ' . $data['student_id'] . ' into class ID ' . $data['class_id']
            );

            Database::commit();

            Session::forget('_old_input');
            Session::flash('success', 'Student enrolled successfully.');
        } catch (Throwable $e) {
            if (Database::pdo()->inTransaction()) {
                Database::rollBack();
            }
            Session::flash('error', 'Could not enroll student: ' . $e->getMessage());
        }

        Response::redirect(url('?page=admin.students'));
    }

    public function showPromoteForm()
    {
        Auth::requireRole(['super_admin']);

        $students = Student::allDetailed();
        $classes = SchoolClass::allActiveWithRelations();
        $years = AcademicYear::all();

        $currentYear = AcademicYear::current();
        $currentYearId = $currentYear ? (int) $currentYear['id'] : 0;
        $currentTerm = $currentYear ? Term::currentForYear($currentYearId) : null;

        $selectedStudentId = isset($_GET['student_id']) ? (int) $_GET['student_id'] : 0;
        $selectedStudent = $selectedStudentId > 0 ? Student::findDetailedById($selectedStudentId) : null;
        $history = $selectedStudent ? Enrollment::historyByStudent($selectedStudentId) : [];

        $success = flash('success');
        $error = flash('error');

        require app_path('views/admin/students/promote.php');
    }

    public function storePromotion()
    {
        Auth::requireRole(['super_admin']);

        if (!is_post()) {
            Response::redirect(url('?page=admin.students.promote'));
        }

        if (!Csrf::validate($_POST['_token'] ?? '')) {
            Session::flash('error', 'Security check failed.');
            Response::redirect(url('?page=admin.students.promote'));
        }

        $data = [
            'student_id' => trim((string) ($_POST['student_id'] ?? '')),
            'new_class_id' => trim((string) ($_POST['new_class_id'] ?? '')),
            'academic_year_id' => trim((string) ($_POST['academic_year_id'] ?? '')),
            'term_id' => trim((string) ($_POST['term_id'] ?? '')),
            'promotion_date' => trim((string) ($_POST['promotion_date'] ?? '')),
        ];

        $errors = Validator::validate($data, [
            'student_id' => 'required|integer',
            'new_class_id' => 'required|integer',
            'academic_year_id' => 'required|integer',
            'term_id' => 'required|integer',
            'promotion_date' => 'required|date',
        ]);

        $student = Student::findDetailedById($data['student_id']);
        $newClass = SchoolClass::find($data['new_class_id']);
        $year = AcademicYear::find($data['academic_year_id']);
        $term = Term::find($data['term_id']);

        if (!$student) {
            $errors['student_id'] = 'Selected student does not exist.';
        }

        if (!$newClass) {
            $errors['new_class_id'] = 'Selected class does not exist.';
        } elseif ((string) $newClass['status'] !== 'active') {
            $errors['new_class_id'] = 'Selected class is inactive.';
        }

        if (!$year) {
            $errors['academic_year_id'] = 'Selected academic year does not exist.';
        }

        if (!$term) {
            $errors['term_id'] = 'Selected term does not exist.';
        }

        if ($term && $year && (int) $term['academic_year_id'] !== (int) $year['id']) {
            $errors['term_id'] = 'The term does not belong to the selected academic year.';
        }

        if ($student && (int) $student['class_id'] === (int) $newClass['id']) {
            $errors['new_class_id'] = 'Student is already in that class.';
        }

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            Session::flash('error', Validator::firstError($errors));
            Session::put('_old_input', $data);
            Response::redirect(url('?page=admin.students.promote&student_id=' . (int) $data['student_id']));
        }

        try {
            Database::beginTransaction();

            $enrollmentId = Enrollment::create([
                'student_id' => $data['student_id'],
                'class_id' => $data['new_class_id'],
                'academic_year_id' => $data['academic_year_id'],
                'term_id' => $data['term_id'],
                'enrollment_date' => $data['promotion_date'],
                'status' => 'promoted',
            ]);

            Student::updateClass($data['student_id'], $data['new_class_id']);
            Student::updateStatus($data['student_id'], 'active');

            ActivityLog::create(
                Auth::id(),
                'PROMOTE_STUDENT',
                'enrollment',
                $enrollmentId,
                'Promoted student ID ' . $data['student_id'] . ' to class ID ' . $data['new_class_id']
            );

            Database::commit();

            Session::forget('_old_input');
            Session::flash('success', 'Student promoted successfully.');
        } catch (Throwable $e) {
            if (Database::pdo()->inTransaction()) {
                Database::rollBack();
            }
            Session::flash('error', 'Could not promote student: ' . $e->getMessage());
        }

        Response::redirect(url('?page=admin.students'));
    }

    public function profile()
    {
        $student = $this->resolveStudentForCurrentUser();

        if (!$student) {
            Response::forbidden('Student record not found.');
        }

        $history = Enrollment::historyByStudent((int) $student['id']);
        $currentEnrollment = Enrollment::latestByStudent((int) $student['id']);
        $success = flash('success');
        $error = flash('error');

        if (Auth::hasRole('super_admin')) {
            require app_path('views/admin/students/profile.php');
            return;
        }

        require app_path('views/student/profile.php');
    }

    public function updateProfile()
    {
        Auth::requireLogin();

        if (!is_post()) {
            Response::redirect(Auth::dashboardUrl());
        }

        if (!Csrf::validate($_POST['_token'] ?? '')) {
            Session::flash('error', 'Security check failed.');
            Response::redirect($this->profileRedirectUrl());
        }

        $student = $this->resolveStudentForCurrentUser();
        if (!$student) {
            Response::forbidden('Student record not found.');
        }

        $userId = (int) $student['user_id'];

        $data = [
            'full_name' => trim((string) ($_POST['full_name'] ?? '')),
            'email' => trim((string) ($_POST['email'] ?? '')),
            'phone' => trim((string) ($_POST['phone'] ?? '')),
            'gender' => trim((string) ($_POST['gender'] ?? '')),
            'date_of_birth' => trim((string) ($_POST['date_of_birth'] ?? '')),
            'guardian_name' => trim((string) ($_POST['guardian_name'] ?? '')),
            'guardian_phone' => trim((string) ($_POST['guardian_phone'] ?? '')),
            'guardian_email' => trim((string) ($_POST['guardian_email'] ?? '')),
            'address' => trim((string) ($_POST['address'] ?? '')),
        ];

        $errors = Validator::validate($data, [
            'full_name' => 'required|min:3|max:150',
            'email' => 'required|email|max:190',
            'phone' => 'nullable|max:30',
            'gender' => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'guardian_name' => 'nullable|max:150',
            'guardian_phone' => 'nullable|max:30',
            'guardian_email' => 'nullable|email|max:190',
            'address' => 'nullable|max:255',
        ]);

        if (User::existsByEmailExcept($data['email'], $userId)) {
            $errors['email'] = 'This email is already used by another account.';
        }

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            Session::flash('error', Validator::firstError($errors));
            Session::put('_old_input', $data);
            Response::redirect($this->profileRedirectUrl());
        }

        try {
            Database::beginTransaction();

            User::updateBasicInfo($userId, [
                'full_name' => $data['full_name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
            ]);

            Student::updateProfile((int) $student['id'], [
                'gender' => $data['gender'],
                'date_of_birth' => $data['date_of_birth'],
                'guardian_name' => $data['guardian_name'],
                'guardian_phone' => $data['guardian_phone'],
                'guardian_email' => $data['guardian_email'],
                'address' => $data['address'],
            ]);

            ActivityLog::create(
                Auth::id(),
                'UPDATE_STUDENT_PROFILE',
                'student',
                (int) $student['id'],
                'Updated profile for student ID ' . (int) $student['id']
            );

            Database::commit();

            if (Auth::hasRole('student')) {
                Auth::refresh([
                    'full_name' => $data['full_name'],
                    'email' => $data['email'],
                ]);
            }

            Session::forget('_old_input');
            Session::flash('success', 'Profile updated successfully.');
        } catch (Throwable $e) {
            if (Database::pdo()->inTransaction()) {
                Database::rollBack();
            }
            Session::flash('error', 'Could not update profile: ' . $e->getMessage());
        }

        Response::redirect($this->profileRedirectUrl());
    }

    public function history()
    {
        $student = $this->resolveStudentForCurrentUser();

        if (!$student) {
            Response::forbidden('Student record not found.');
        }

        $history = Enrollment::historyByStudent((int) $student['id']);
        $currentEnrollment = Enrollment::latestByStudent((int) $student['id']);
        $success = flash('success');
        $error = flash('error');

        if (Auth::hasRole('super_admin')) {
            require app_path('views/admin/students/history.php');
            return;
        }

        require app_path('views/student/history.php');
    }

    private function resolveStudentForCurrentUser()
    {
        if (Auth::hasRole('student')) {
            return Student::findDetailedByUserId(Auth::id());
        }

        if (Auth::hasRole('super_admin')) {
            $studentId = 0;

            if (isset($_GET['student_id'])) {
                $studentId = (int) $_GET['student_id'];
            } elseif (isset($_POST['student_id'])) {
                $studentId = (int) $_POST['student_id'];
            }

            if ($studentId > 0) {
                return Student::findDetailedById($studentId);
            }

            return null;
        }

        Response::forbidden('You are not allowed to access this page.');
    }

    private function profileRedirectUrl()
    {
        if (Auth::hasRole('super_admin')) {
            $studentId = isset($_GET['student_id']) ? (int) $_GET['student_id'] : 0;
            return url('?page=admin.students.profile' . ($studentId > 0 ? '&student_id=' . $studentId : ''));
        }

        return url('?page=student.profile');
    }
}