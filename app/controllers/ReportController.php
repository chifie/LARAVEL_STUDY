<?php
declare(strict_types=1);

class ReportController
{
    public function index()
    {
        Auth::requireRole(['super_admin']);

        $success = flash('success');
        $error = flash('error');

        require app_path('views/admin/reports/index.php');
    }

    public function academic()
    {
        Auth::requireRole(['super_admin']);

        $filters = [
            'academic_year_id' => isset($_GET['academic_year_id']) ? (int) $_GET['academic_year_id'] : 0,
            'term_id' => isset($_GET['term_id']) ? (int) $_GET['term_id'] : 0,
            'class_id' => isset($_GET['class_id']) ? (int) $_GET['class_id'] : 0,
        ];

        $years = AcademicYear::all();
        $currentYear = AcademicYear::current();
        $terms = $filters['academic_year_id'] > 0
            ? Term::allByYear($filters['academic_year_id'])
            : ($currentYear ? Term::allByYear((int) $currentYear['id']) : []);
        $classes = SchoolClass::allActiveWithRelations();

        $students = $this->academicStudentSummary($filters);
        $summary = $this->academicOverview($filters);

        $success = flash('success');
        $error = flash('error');

        require app_path('views/admin/reports/academic.php');
    }

    public function attendance()
    {
        Auth::requireRole(['super_admin']);

        $filters = [
            'academic_year_id' => isset($_GET['academic_year_id']) ? (int) $_GET['academic_year_id'] : 0,
            'class_id' => isset($_GET['class_id']) ? (int) $_GET['class_id'] : 0,
            'from_date' => isset($_GET['from_date']) ? trim((string) $_GET['from_date']) : date('Y-m-01'),
            'to_date' => isset($_GET['to_date']) ? trim((string) $_GET['to_date']) : date('Y-m-d'),
        ];

        $years = AcademicYear::all();
        $classes = SchoolClass::allActiveWithRelations();

        $students = $this->attendanceStudentSummary($filters);
        $summary = $this->attendanceOverview($filters);

        $success = flash('success');
        $error = flash('error');

        require app_path('views/admin/reports/attendance.php');
    }

    public function logs()
    {
        Auth::requireRole(['super_admin']);

        $filters = [
            'user_id' => isset($_GET['user_id']) ? (int) $_GET['user_id'] : 0,
            'action' => isset($_GET['action']) ? trim((string) $_GET['action']) : '',
            'from_date' => isset($_GET['from_date']) ? trim((string) $_GET['from_date']) : '',
            'to_date' => isset($_GET['to_date']) ? trim((string) $_GET['to_date']) : '',
        ];

        $logs = ActivityLog::report($filters, 300);
        $users = User::all();
        $actions = ActivityLog::actions();

        $success = flash('success');
        $error = flash('error');

        require app_path('views/admin/reports/logs.php');
    }

    public function backups()
    {
        Auth::requireRole(['super_admin']);

        $backups = BackupService::listBackups();
        $success = flash('success');
        $error = flash('error');

        require app_path('views/admin/reports/backups.php');
    }

    public function createBackup()
    {
        Auth::requireRole(['super_admin']);

        if (!is_post()) {
            Response::redirect(url('?page=admin.reports.backups'));
        }

        if (!Csrf::validate($_POST['_token'] ?? '')) {
            Session::flash('error', 'Security check failed.');
            Response::redirect(url('?page=admin.reports.backups'));
        }

        try {
            $filePath = BackupService::createBackup();
            $fileName = basename($filePath);

            ActivityLog::create(
                Auth::id(),
                'CREATE_BACKUP',
                'backup',
                null,
                'Created backup file ' . $fileName
            );

            Database::query(
                'INSERT INTO backup_jobs (backup_file, backup_type, status, message, created_by, created_at)
                 VALUES (:backup_file, "manual", "success", :message, :created_by, NOW())',
                [
                    ':backup_file' => $fileName,
                    ':message' => 'Backup created successfully.',
                    ':created_by' => Auth::id(),
                ]
            );

            Session::flash('success', 'Backup created successfully: ' . $fileName);
        } catch (Throwable $e) {
            Database::query(
                'INSERT INTO backup_jobs (backup_file, backup_type, status, message, created_by, created_at)
                 VALUES (:backup_file, "manual", "failed", :message, :created_by, NOW())',
                [
                    ':backup_file' => 'unknown',
                    ':message' => $e->getMessage(),
                    ':created_by' => Auth::id(),
                ]
            );

            Session::flash('error', 'Could not create backup: ' . $e->getMessage());
        }

        Response::redirect(url('?page=admin.reports.backups'));
    }

    public function downloadBackup()
    {
        Auth::requireRole(['super_admin']);

        $file = isset($_GET['file']) ? basename((string) $_GET['file']) : '';
        $path = BackupService::fileExists($file);

        if (!$path) {
            Response::forbidden('Backup file not found.');
        }

        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename="' . basename($path) . '"');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;
    }

    private function academicStudentSummary(array $filters)
{
    $sql = 'SELECT
                s.id AS student_id,
                u.full_name,
                s.admission_number,
                c.class_name,
                ay.year_name,
                COUNT(r.id) AS exam_count,
                COALESCE(SUM(r.marks_obtained), 0) AS total_obtained,
                COALESCE(SUM(e.total_marks), 0) AS total_possible,
                COALESCE(MAX(r.marks_obtained), 0) AS highest_mark,
                COALESCE(MIN(r.marks_obtained), 0) AS lowest_mark
            FROM students s
            INNER JOIN users u ON u.id = s.user_id
            LEFT JOIN classes c ON c.id = s.class_id
            LEFT JOIN results r ON r.student_id = s.id
            LEFT JOIN exams e ON e.id = r.exam_id
            LEFT JOIN academic_years ay ON ay.id = e.academic_year_id';
    $sql .= ' WHERE 1 = 1';
    $params = [];

    if (!empty($filters['academic_year_id'])) {
        $sql .= ' AND e.academic_year_id = :academic_year_id';
        $params[':academic_year_id'] = (int) $filters['academic_year_id'];
    }

    if (!empty($filters['term_id'])) {
        $sql .= ' AND e.term_id = :term_id';
        $params[':term_id'] = (int) $filters['term_id'];
    }

    if (!empty($filters['class_id'])) {
        $sql .= ' AND s.class_id = :class_id';
        $params[':class_id'] = (int) $filters['class_id'];
    }

    $sql .= ' GROUP BY s.id, u.full_name, s.admission_number, c.class_name, ay.year_name
              ORDER BY u.full_name ASC';

    $stmt = Database::query($sql, $params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($rows as &$row) {
        $row['overall_percentage'] = ((float) $row['total_possible'] > 0)
            ? (((float) $row['total_obtained'] / (float) $row['total_possible']) * 100)
            : 0;
        $row['overall_grade'] = Result::gradeFromPercentage((float) $row['overall_percentage']);
        $row['overall_remark'] = Result::remarkFromGrade($row['overall_grade']);
    }

    return $rows;
}
    private function academicOverview(array $filters)
    {
        $rows = $this->academicStudentSummary($filters);

        $studentCount = count($rows);
        $totalPercentage = 0;
        $passCount = 0;

        foreach ($rows as $row) {
            $totalPercentage += (float) $row['overall_percentage'];
            if (!in_array($row['overall_grade'], ['F'], true)) {
                $passCount++;
            }
        }

        return [
            'student_count' => $studentCount,
            'average_percentage' => $studentCount > 0 ? ($totalPercentage / $studentCount) : 0,
            'pass_count' => $passCount,
            'fail_count' => $studentCount - $passCount,
        ];
    }

    private function attendanceStudentSummary(array $filters)
    {
        $sql = 'SELECT
                    s.id AS student_id,
                    u.full_name,
                    s.admission_number,
                    c.class_name,
                    ay.year_name,
                    SUM(CASE WHEN a.status = "present" THEN 1 ELSE 0 END) AS present_count,
                    SUM(CASE WHEN a.status = "absent" THEN 1 ELSE 0 END) AS absent_count,
                    SUM(CASE WHEN a.status = "late" THEN 1 ELSE 0 END) AS late_count,
                    SUM(CASE WHEN a.status = "excused" THEN 1 ELSE 0 END) AS excused_count,
                    COUNT(a.id) AS total_days
                FROM students s
                INNER JOIN users u ON u.id = s.user_id
                LEFT JOIN classes c ON c.id = s.class_id
                LEFT JOIN academic_years ay ON ay.id = c.academic_year_id
                LEFT JOIN attendance a
                    ON a.student_id = s.id
                   AND a.attendance_date BETWEEN :from_date AND :to_date';
        $sql .= ' WHERE 1 = 1';
        $params = [
            ':from_date' => $filters['from_date'],
            ':to_date' => $filters['to_date'],
        ];

        if (!empty($filters['academic_year_id'])) {
            $sql .= ' AND c.academic_year_id = :academic_year_id';
            $params[':academic_year_id'] = (int) $filters['academic_year_id'];
        }

        if (!empty($filters['class_id'])) {
            $sql .= ' AND s.class_id = :class_id';
            $params[':class_id'] = (int) $filters['class_id'];
        }

        $sql .= ' GROUP BY s.id, u.full_name, s.admission_number, c.class_name, ay.year_name
                  ORDER BY u.full_name ASC';

        $stmt = Database::query($sql, $params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function attendanceOverview(array $filters)
    {
        $rows = $this->attendanceStudentSummary($filters);

        $studentCount = count($rows);
        $present = 0;
        $absent = 0;
        $late = 0;
        $excused = 0;

        foreach ($rows as $row) {
            $present += (int) $row['present_count'];
            $absent += (int) $row['absent_count'];
            $late += (int) $row['late_count'];
            $excused += (int) $row['excused_count'];
        }

        return [
            'student_count' => $studentCount,
            'present_count' => $present,
            'absent_count' => $absent,
            'late_count' => $late,
            'excused_count' => $excused,
        ];
    }
}