<?php
declare(strict_types=1);

class PaymentController
{
    public function index()
    {
        Auth::requireRole(['super_admin']);

        $feeStructures = FeeStructure::allWithRelations();
        $recentPayments = Payment::allWithRelations(25);
        $overview = Payment::overview();

        $students = Student::activeForSelection();
        $classes = SchoolClass::allActiveWithRelations();
        $years = AcademicYear::all();
        $currentYear = AcademicYear::current();
        $terms = $currentYear ? Term::allByYear((int) $currentYear['id']) : [];
        $feeOptions = FeeStructure::activeAll();

        $success = flash('success');
        $error = flash('error');

        require app_path('views/admin/payments/index.php');
    }

    public function storeFee()
    {
        Auth::requireRole(['super_admin']);

        if (!is_post()) {
            Response::redirect(url('?page=admin.payments'));
        }

        if (!Csrf::validate($_POST['_token'] ?? '')) {
            Session::flash('error', 'Security check failed.');
            Response::redirect(url('?page=admin.payments'));
        }

        $data = [
            'academic_year_id' => trim((string) ($_POST['academic_year_id'] ?? '')),
            'term_id' => trim((string) ($_POST['term_id'] ?? '')),
            'class_id' => trim((string) ($_POST['class_id'] ?? '')),
            'fee_name' => trim((string) ($_POST['fee_name'] ?? '')),
            'amount_due' => trim((string) ($_POST['amount_due'] ?? '')),
            'due_date' => trim((string) ($_POST['due_date'] ?? '')),
            'status' => 'active',
        ];

        $errors = Validator::validate($data, [
            'academic_year_id' => 'required|integer',
            'term_id' => 'required|integer',
            'class_id' => 'required|integer',
            'fee_name' => 'required|min:2|max:150',
            'amount_due' => 'required|numeric',
            'due_date' => 'nullable|date',
        ]);

        $year = AcademicYear::find($data['academic_year_id']);
        $term = Term::find($data['term_id']);
        $class = SchoolClass::find($data['class_id']);

        if (!$year) {
            $errors['academic_year_id'] = 'Selected academic year does not exist.';
        }

        if (!$term) {
            $errors['term_id'] = 'Selected term does not exist.';
        }

        if ($year && $term && (int) $term['academic_year_id'] !== (int) $year['id']) {
            $errors['term_id'] = 'The selected term does not belong to the selected academic year.';
        }

        if (!$class) {
            $errors['class_id'] = 'Selected class does not exist.';
        } elseif ($class['status'] !== 'active') {
            $errors['class_id'] = 'Selected class is inactive.';
        }

        if ((float) $data['amount_due'] < 0) {
            $errors['amount_due'] = 'Amount due must be zero or greater.';
        }

        if (FeeStructure::exists($data)) {
            $errors['fee_name'] = 'This fee structure already exists for the selected year, term, and class.';
        }

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            Session::flash('error', Validator::firstError($errors));
            Session::put('_old_input', $data);
            Response::redirect(url('?page=admin.payments'));
        }

        try {
            $feeId = FeeStructure::create([
                'academic_year_id' => $data['academic_year_id'],
                'term_id' => $data['term_id'],
                'class_id' => $data['class_id'],
                'fee_name' => $data['fee_name'],
                'amount_due' => $data['amount_due'],
                'due_date' => $data['due_date'],
                'status' => 'active',
                'created_by' => Auth::id(),
            ]);

            ActivityLog::create(
                Auth::id(),
                'CREATE_FEE_STRUCTURE',
                'fee_structure',
                $feeId,
                'Created fee structure ' . $data['fee_name']
            );

            Session::forget('_old_input');
            Session::flash('success', 'Fee structure created successfully.');
        } catch (Throwable $e) {
            Session::flash('error', 'Could not create fee structure: ' . $e->getMessage());
        }

        Response::redirect(url('?page=admin.payments'));
    }

    public function storePayment()
    {
        Auth::requireRole(['super_admin']);

        if (!is_post()) {
            Response::redirect(url('?page=admin.payments'));
        }

        if (!Csrf::validate($_POST['_token'] ?? '')) {
            Session::flash('error', 'Security check failed.');
            Response::redirect(url('?page=admin.payments'));
        }

        $data = [
            'student_id' => trim((string) ($_POST['student_id'] ?? '')),
            'fee_structure_id' => trim((string) ($_POST['fee_structure_id'] ?? '')),
            'amount_paid' => trim((string) ($_POST['amount_paid'] ?? '')),
            'payment_date' => trim((string) ($_POST['payment_date'] ?? '')),
            'payment_method' => trim((string) ($_POST['payment_method'] ?? 'cash')),
            'receipt_number' => trim((string) ($_POST['receipt_number'] ?? '')),
            'notes' => trim((string) ($_POST['notes'] ?? '')),
        ];

        $errors = Validator::validate($data, [
            'student_id' => 'required|integer',
            'fee_structure_id' => 'required|integer',
            'amount_paid' => 'required|numeric',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,bank,mobile_money,other',
            'receipt_number' => 'nullable|max:100',
            'notes' => 'nullable|max:255',
        ]);

        $student = Student::findDetailedById($data['student_id']);
        $feeStructure = FeeStructure::find($data['fee_structure_id']);

        if (!$student) {
            $errors['student_id'] = 'Selected student does not exist.';
        }

        if (!$feeStructure) {
            $errors['fee_structure_id'] = 'Selected fee structure does not exist.';
        }

        if ($student && $feeStructure && (int) $student['class_id'] !== (int) $feeStructure['class_id']) {
            $errors['student_id'] = 'This fee structure does not match the student’s current class.';
        }

        if ($feeStructure && (float) $data['amount_paid'] <= 0) {
            $errors['amount_paid'] = 'Amount paid must be greater than zero.';
        }

        if ($feeStructure && (float) $data['amount_paid'] > (float) $feeStructure['amount_due']) {
            $errors['amount_paid'] = 'Amount paid cannot exceed the fee amount due.';
        }

        if (empty($data['receipt_number'])) {
            $data['receipt_number'] = null;
        } else {
            $exists = Database::query(
                'SELECT COUNT(*) AS total FROM payments WHERE receipt_number = :receipt_number',
                [':receipt_number' => $data['receipt_number']]
            )->fetch(PDO::FETCH_ASSOC);

            if ((int) $exists['total'] > 0) {
                $errors['receipt_number'] = 'This receipt number already exists.';
            }
        }

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            Session::flash('error', Validator::firstError($errors));
            Session::put('_old_input', $data);
            Response::redirect(url('?page=admin.payments'));
        }

        $amountDue = (float) $feeStructure['amount_due'];
        $amountPaid = (float) $data['amount_paid'];
        $balance = max($amountDue - $amountPaid, 0);

        try {
            $paymentId = Payment::create([
                'student_id' => $data['student_id'],
                'fee_structure_id' => $data['fee_structure_id'],
                'academic_year_id' => $feeStructure['academic_year_id'],
                'term_id' => $feeStructure['term_id'],
                'amount_due' => $amountDue,
                'amount_paid' => $amountPaid,
                'balance' => $balance,
                'payment_date' => $data['payment_date'],
                'payment_method' => $data['payment_method'],
                'receipt_number' => $data['receipt_number'],
                'notes' => $data['notes'],
                'received_by' => Auth::id(),
            ]);

            ActivityLog::create(
                Auth::id(),
                'RECORD_PAYMENT',
                'payment',
                $paymentId,
                'Recorded payment for student ID ' . $data['student_id']
            );

            Session::forget('_old_input');
            Session::flash('success', 'Payment recorded successfully.');
        } catch (Throwable $e) {
            Session::flash('error', 'Could not record payment: ' . $e->getMessage());
        }

        Response::redirect(url('?page=admin.payments'));
    }

    public function report()
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

        $summary = Payment::reportSummary($filters);
        $balances = Payment::reportByFilters($filters);

        $success = flash('success');
        $error = flash('error');

        require app_path('views/admin/payments/report.php');
    }

    public function studentMyPayments()
    {
        Auth::requireRole(['student']);

        $student = Student::findDetailedByUserId(Auth::id());

        if (!$student) {
            Response::forbidden('Student record not found.');
        }

        $history = Payment::studentHistory((int) $student['id']);
        $summary = Payment::studentSummary((int) $student['id']);

        require app_path('views/student/my_payments.php');
    }
}