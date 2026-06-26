<?php
header('Content-Type: application/json');
include("../config/database.php");
include("../config/auth_check.php");

// ---------- Collect & sanitize ----------
$department_id = $_POST['department_id'] ?? '';
$task_type     = $_POST['task_type'] ?? '';
$priority      = $_POST['priority'] ?? 'medium';
$title         = trim($_POST['task_title'] ?? '');
$description   = trim($_POST['description'] ?? '');
$assign_to     = $_POST['assign_to'] ?? '';
$assigned_by   = $_POST['assigned_by'] ?? 1; // replace with $_SESSION['user_id']
$start_date    = $_POST['start_date'] ?? null;
$due_date      = $_POST['due_date'] ?? '';
$est_hours     = $_POST['est_hours'] ?? null;
$remind_before = $_POST['remind_before'] ?? null;
$alert_not_started = $_POST['alert_not_started'] ?? null;
$escalate_after = $_POST['escalate_after'] ?? null;
$status        = $_POST['status'] ?? 'active';

// ---------- Server-side validation ----------
$errors = [];

if ($department_id === '' || $department_id === 'Select') {
    $errors['department_id'] = 'Department is required.';
}
if ($task_type === '' || $task_type === 'Select') {
    $errors['task_type'] = 'Task type is required.';
}
if ($title === '') {
    $errors['title'] = 'Task title is required.';
}
if ($assign_to === '' || $assign_to === 'Select Staff') {
    $errors['assign_to'] = 'Assignee is required.';
}
if ($due_date === '') {
    $errors['due_date'] = 'Due date is required.';
}
if ($start_date && $due_date && $start_date > $due_date) {
    $errors['due_date'] = 'Due date cannot be before start date.';
}



// ---------- Build department_details JSON based on department_id ----------
// Adjust these department_id numbers to match your real departments table
$departmentDetails = [];
switch ($department_id) {

    case '4': // Design Studio
        $d = $_POST['design'] ?? [];

        $departmentDetails = [
            'sub_type'       => $d['sub_type'] ?? null,
            'designer_level' => $d['designer_level'] ?? null,
            'sketch_format'  => $d['sketch_format'] ?? null,
            'targets'        => [],
        ];

        if (!empty($d['category_id'][0]) || !empty($d['category_id'])) {
            $catIds   = $d['category_id'] ?? [];
            $weekly   = $d['weekly_target'] ?? [];
            $monthly  = $d['monthly_target'] ?? [];
            $budget   = $d['budget'] ?? [];
            $fabric   = $d['fabric'] ?? [];
            $workType = $d['work_type'] ?? [];
            $occasion = $d['occasion'] ?? [];
            $refId    = $d['reference_id'] ?? [];
            $palette  = $d['color_palette'] ?? [];

            foreach ($catIds as $i => $catId) {
                if (empty($weekly[$i]) && empty($monthly[$i]) && empty($budget[$i])) continue;

                $departmentDetails['targets'][] = [
                    'category_id'    => $catId,
                    'weekly'         => $weekly[$i] ?? 0,
                    'monthly'        => $monthly[$i] ?? 0,
                    'budget'         => $budget[$i] ?? 0,
                    'fabric'         => $fabric[$i] ?? null,
                    'work_type'      => $workType[$i] ?? null,
                    'occasion'       => $occasion[$i] ?? null,
                    'reference_id'   => $refId[$i] ?? null,
                    'color_palette'  => $palette[$i] ?? null,
                ];
            }
        }

        $jsonData = json_encode($departmentDetails, JSON_UNESCAPED_UNICODE);
        break;

    case '2': // Purchase
        $p = $_POST['purchase'] ?? [];
        $departmentDetails = [
            'sub_type' => $p['sub_type'] ?? null,
        ];
        break;

    case '3': // Production
        $departmentDetails = $_POST['production'] ?? [];
        break;

    case '4': // QC
        $qc = $_POST['qc'] ?? [];

        if (empty($qc['lot_number']))  $errors['qc[lot_number]']  = 'Lot number is required.';
        if (empty($qc['design_code'])) $errors['qc[design_code]'] = 'Design code is required.';
        if (empty($qc['total_pieces']) || !is_numeric($qc['total_pieces'])) {
            $errors['qc[total_pieces]'] = 'Total pieces must be a number.';
        }

        $departmentDetails = [
            'lot_number'      => $qc['lot_number'] ?? null,
            'design_code'     => $qc['design_code'] ?? null,
            'total_pieces'    => $qc['total_pieces'] ?? null,
            'daily_target'    => $qc['daily_target'] ?? null,
            'qc_inspector_id' => $qc['qc_inspector_id'] ?: null,
        ];
        break;

    case '5': // Dispatch
        $disp = $_POST['dispatch'] ?? [];
        $departmentDetails = [
            'channel_id'   => $disp['channel_id'] ?: null,
            'orders_today' => $disp['orders_today'] ?? null,
            'sub_type'     => $disp['sub_type'] ?? null,
        ];
        break;

    case '6': // Sampling
        $s = $_POST['sampling'] ?? [];

        if (empty($s['design_code'])) {
            $errors['sampling[design_code]'] = 'Design code is required.';
        }

        $departmentDetails = [
            'design_code'      => $s['design_code'] ?? null,
            'farma_master_id'  => $s['farma_master_id'] ?: null,
            'sample_due_date'  => $s['sample_due_date'] ?: null,
        ];
        break;
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}
$task_no = generate_task_no();
// ---------- Insert ----------
try {
    $stmt = $con->prepare("
        INSERT INTO task_master
        (task_no,department_id, task_type, priority, title, description, assigned_to, assigned_by,
         start_date, deadline_time, est_hours, remind_before, alert_not_started, escalate_after,
         status, department_details)
        VALUES (?,?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $task_no,$department_id, $task_type, $priority, $title, $description, $assign_to, $assigned_by,
        $start_date ?: null, $due_date, $est_hours ?: null,
        $remind_before, $alert_not_started, $escalate_after, $status,
        $jsonData
    ]);
    
    $lastId = $con->insert_id;


    echo json_encode(['success' => true, 'task_id' => $lastId]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}