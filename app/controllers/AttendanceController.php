<?php
declare(strict_types=1);

class AttendanceController
{
    public function index()
    {
        Auth::requireRole(['super_admin', 'teacher']);

        $role = Auth::role();
        $user = Auth::user();

        $selectedClassId = isset($_GET['class_id']) ? (int) $_GET['class_id'] : 0;
        $selectedDate = isset($_GET['date']) ? trim((string) $_GET['date']) : date('Y-m-d');

        $classes = ($role === 'teacher')
            ? Attendance::allowedClassesForUser(Auth::id())
            : Attendance::activeClasses();

        if ($selectedClassId === 0 && !empty($classes)) {
            $selectedClassId = (int) $classes[0]['id'];
        }

        if ($role === 'teacher' && !$this->teacherCanAccessClass($selectedClassId)) {
            $selectedClassId = !empty($classes) ? (int) $classes[0]['id'] : 0;
        }

        $class = $selectedClassId > 0 ? Attendance::classById($selectedClassId) : null;
        $students = $selectedClassId > 0 ? Attendance::studentsByClass($selectedClassId) : [];
        $records = $selectedClassId > 0 ? Attendance::recordsByClassAndDate($selectedClassId, $selectedDate) : [];
        $counts = $selectedClassId > 0 ? Attendance::dailyCounts($selectedClassId, $selectedDate) : [
            'present_count' => 0,
            'absent_count' => 0,
            'late_count' => 0,
            'excused_count' => 0,
            'total_count' => 0,
        ];

        $success = flash('success');
        $error = flash('error');

        if ($role === 'teacher') {
            require app_path('views/teacher/attendance.php');
            return;
        }

        require app_path('views/admin/attendance/index.php');
    }

    public function store()
    {
        Auth::requireRole(['super_admin', 'teacher']);

        if (!is_post()) {
            Response::redirect(Auth::dashboardUrl());
        }

        if (!Csrf::validate($_POST['_token'] ?? '')) {
            Session::flash('error', 'Security check failed.');
            Response::redirect($this->indexRedirectUrl());
        }

        $classId = (int) ($_POST['class_id'] ?? 0);
        $attendanceDate = trim((string) ($_POST['attendance_date'] ?? date('Y-m-d')));

        $errors = Validator::validate([
            'class_id' => $classId,
            'attendance_date' => $attendanceDate,
        ], [
            'class_id' => 'required|integer',
            'attendance_date' => 'required|date',
        ]);

        $class = Attendance::classById($classId);
        if (!$class) {
            $errors['class_id'] = 'Selected class does not exist.';
        }

        if ($class && $class['status'] !== 'active') {
            $errors['class_id'] = 'Selected class is inactive.';
        }

        if (Auth::hasRole('teacher') && !$this->teacherCanAccessClass($classId)) {
            $errors['class_id'] = 'You are not allowed to mark attendance for this class.';
        }

        $statuses = isset($_POST['status']) && is_array($_POST['status']) ? $_POST['status'] : [];
        $remarks = isset($_POST['remarks']) && is_array($_POST['remarks']) ? $_POST['remarks'] : [];

        $students = $classId > 0 ? Attendance::studentsByClass($classId) : [];

        if (empty($students)) {
            $errors['class_id'] = 'No active students found in this class.';
        }

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            Session::flash('error', Validator::firstError($errors));
            Session::put('_old_input', [
                'class_id' => $classId,
                'attendance_date' => $attendanceDate,
                'status' => $statuses,
                'remarks' => $remarks,
            ]);
            Response::redirect($this->indexRedirectUrl(['class_id' => $classId, 'date' => $attendanceDate]));
        }

        try {
            $teacherId = $this->currentTeacherId();

            Database::beginTransaction();

            $savedCount = Attendance::saveBulk($classId, $attendanceDate, $teacherId, $statuses, $remarks);

            ActivityLog::create(
                Auth::id(),
                'MARK_ATTENDANCE',
                'attendance',
                null,
                'Marked attendance for class ID ' . $classId . ' on ' . $attendanceDate . ' for ' . $savedCount . ' students'
            );

            Database::commit();

            Session::forget('_old_input');
            Session::flash('success', 'Attendance saved successfully.');
        } catch (Throwable $e) {
            if (Database::pdo()->inTransaction()) {
                Database::rollBack();
            }

            Session::flash('error', 'Could not save attendance: ' . $e->getMessage());
        }

        Response::redirect($this->indexRedirectUrl(['class_id' => $classId, 'date' => $attendanceDate]));
    }

    public function daily()
    {
        Auth::requireRole(['super_admin', 'teacher']);

        $role = Auth::role();
        $classes = ($role === 'teacher')
            ? Attendance::allowedClassesForUser(Auth::id())
            : Attendance::activeClasses();

        $selectedClassId = isset($_GET['class_id']) ? (int) $_GET['class_id'] : 0;
        $selectedDate = isset($_GET['date']) ? trim((string) $_GET['date']) : date('Y-m-d');

        if ($selectedClassId === 0 && !empty($classes)) {
            $selectedClassId = (int) $classes[0]['id'];
        }

        if ($role === 'teacher' && !$this->teacherCanAccessClass($selectedClassId)) {
            $selectedClassId = !empty($classes) ? (int) $classes[0]['id'] : 0;
        }

        $class = $selectedClassId > 0 ? Attendance::classById($selectedClassId) : null;
        $records = $selectedClassId > 0 ? Attendance::dailyByClassAndDate($selectedClassId, $selectedDate) : [];
        $counts = $selectedClassId > 0 ? Attendance::dailyCounts($selectedClassId, $selectedDate) : [
            'present_count' => 0,
            'absent_count' => 0,
            'late_count' => 0,
            'excused_count' => 0,
            'total_count' => 0,
        ];

        $success = flash('success');
        $error = flash('error');

        require app_path('views/admin/attendance/daily.php');
    }

    public function summary()
    {
        Auth::requireRole(['super_admin', 'teacher']);

        $role = Auth::role();
        $classes = ($role === 'teacher')
            ? Attendance::allowedClassesForUser(Auth::id())
            : Attendance::activeClasses();

        $selectedClassId = isset($_GET['class_id']) ? (int) $_GET['class_id'] : 0;
        $fromDate = isset($_GET['from_date']) ? trim((string) $_GET['from_date']) : date('Y-m-01');
        $toDate = isset($_GET['to_date']) ? trim((string) $_GET['to_date']) : date('Y-m-d');

        if ($selectedClassId === 0 && !empty($classes)) {
            $selectedClassId = (int) $classes[0]['id'];
        }

        if ($role === 'teacher' && !$this->teacherCanAccessClass($selectedClassId)) {
            $selectedClassId = !empty($classes) ? (int) $classes[0]['id'] : 0;
        }

        $class = $selectedClassId > 0 ? Attendance::classById($selectedClassId) : null;
        $summary = $selectedClassId > 0 ? Attendance::summaryByClassAndRange($selectedClassId, $fromDate, $toDate) : [];

        $success = flash('success');
        $error = flash('error');

        require app_path('views/admin/attendance/summary.php');
    }

    private function currentTeacherId()
    {
        if (!Auth::hasRole('teacher')) {
            return null;
        }

        $teacher = Teacher::findByUserId(Auth::id());
        return $teacher ? (int) $teacher['id'] : null;
    }

    private function teacherCanAccessClass($classId)
    {
        if (!Auth::hasRole('teacher')) {
            return true;
        }

        $allowed = Attendance::allowedClassesForUser(Auth::id());
        foreach ($allowed as $class) {
            if ((int) $class['id'] === (int) $classId) {
                return true;
            }
        }

        return false;
    }

    private function indexRedirectUrl(array $query = [])
    {
        $basePage = Auth::hasRole('teacher') ? 'teacher.attendance' : 'admin.attendance';

        $url = url('?page=' . $basePage);
        if (!empty($query)) {
            $url .= '&' . http_build_query($query);
        }

        return $url;
    }
}