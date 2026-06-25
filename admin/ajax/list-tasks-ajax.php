<?php
require_once '../config/database.php';
header('Content-Type: application/json');

$draw   = $_POST['draw'] ?? 1;
$start  = $_POST['start'] ?? 0;
$length = $_POST['length'] ?? 10;

$search = $_POST['search']['value'] ?? '';
$status = $_POST['status'] ?? '';
$design_type = isset($_POST['design_type']) ? $_POST['design_type'] : "";

$orderColumnIndex = $_POST['order'][0]['column'] ?? 0;
$orderDir = ($_POST['order'][0]['dir'] ?? 'desc') === 'asc' ? 'ASC' : 'DESC';
$department_id = !empty($_POST['department_id']) ? $_POST['department_id'] : "";
$frequency = !empty($_POST['frequency']) ? $_POST['frequency'] : "";
$quick = !empty($_POST['quick']) ? $_POST['quick'] : "";
$columns = [
    0 => 'tm.id',
    1 => 'tm.task_no',
    2 => 'tm.title'
];

$orderColumn = $columns[$orderColumnIndex] ?? 'tm.id';
$where = " WHERE 1=1 ";
if($department_id){
    $where .= " AND tm.department_id = '$department_id'";
}
if($frequency){
    $where .= " AND tm.task_type = '$frequency'";
}
if($quick && $quick!='all'){
    if($quick == 'overdue'){
        $where .= " AND tm.completed_at IS NULL AND tm.deadline_time < NOW()";
    }else if($quick == 'today'){
        $today_date = date('Y-m-d 00:00:00');
        $today_end_date = date('Y-m-d 23:59:59');
        $where .= " AND tm.created_at between '".$today_date."' AND '".$today_end_date."'";
    }else if($quick == 'week'){
        $week_start = date('Y-m-d 00:00:00', strtotime('sunday this week'));
        $week_end   = date('Y-m-d 23:59:59', strtotime('saturday this week'));
        $where .= " AND tm.created_at between '".$week_start."' AND '".$week_end."'";
        
    }else if($quick == 'high'){
        $where .= " AND LOWER(tm.priority) = '".strtolower($quick)."'";
    }
}
$params = [];

if (!empty($search)) {
    $where .= " AND fullname title '%$search%' OR task_no LIKE '%$search%' OR description LIKE '%$search%'";
}

$stmtTotal = $con->prepare("SELECT COUNT(*) as total FROM task_master tm");
$stmtTotal->execute();

$result = $stmtTotal->get_result();
$row = $result->fetch_assoc();

$totalRecords = $row['total'];

$stmtFiltered = $con->prepare("SELECT COUNT(*) as total FROM task_master tm $where");

if (!empty($params)) {
    $types = '';
    $values = [];

    foreach ($params as $val) {
        $types .= 's';
        $values[] = $val;
    }

    $stmtFiltered->bind_param($types, ...$values);
}
$stmtFiltered->execute();
$result = $stmtFiltered->get_result();
$row = $result->fetch_assoc();
$totalFiltered = $row['total'];
$sql = "SELECT tm.id,title,task_type,priority,d.department_name,deadline_time,tm.status,assigned_to,u.name as assign_to_name FROM task_master tm LEFT JOIN departments d on d.id = tm.department_id LEFT JOIN user u on u.id  = tm.assigned_to $where ORDER BY $orderColumn $orderDir LIMIT $start, $length";
$stmt = $con->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$rows = $result->fetch_all(MYSQLI_ASSOC);
$data = [];
$sr = $start + 1;
foreach ($rows as $row) {
    $actions ='<a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary"  data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions <i class="ki-outline ki-down fs-5 ms-1"></i></a><div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600  menu-state-bg-light-primary fw-semibold fs-7 w-150px py-4" data-kt-menu="true">
                <div class="menu-item px-3">
                    <a href="'.$site_path.'/view-task-details?id='.my_simple_crypt($row['id'],'encrypt_1').'" class="menu-link px-3">View</a>
                </div>
            </div>';
    $id = $row['id'];
    switch(strtolower($row['priority'])) {
        case 'high':
            $priority_badge = '<span class="badge badge-light-danger">High</span>';
            break;

        case 'medium':
            $priority_badge = '<span class="badge badge-light-warning">Medium</span>';
            break;

        case 'low':
            $priority_badge = '<span class="badge badge-light-success">Low</span>';
            break;
    }

    $status_badge = '';
    switch(strtolower($row['status'])) {
        case 'pending':
            $status_badge = '<span class="badge badge-light-danger">Pending</span>';
            break;

        case 'in progress':
            $status_badge = '<span class="badge badge-light-primary">In Progress</span>';
            break;

        case 'to do':
            $status_badge = '<span class="badge badge-light-secondary">To Do</span>';
            break;

        case 'completed':
            $status_badge = '<span class="badge badge-light-success">Done</span>';
            break;
    }

    $task_type_badge = '<span class="badge badge-light">'.$row['task_type'].'</span>';

    $assigned_to = '
    <div class="d-flex align-items-center">
        <div class="symbol symbol-30px me-2">
            <span class="symbol-label bg-light-warning text-warning fw-bold">
                '.substr($row['assign_to_name'],0,1).'
            </span>
        </div>
        <span>'.$row['assign_to_name'].'</span>
    </div>';
    
    $data[] = [
        'id' => "T - ".$row['id'],
        'title' => $row['title'],
        'task_type' => $task_type_badge,
        'priority' => $priority_badge,
        'department' => $row['department_name'],
        'assigned_to' => $assigned_to,
        'due' =>  !empty($row['deadline_time']) ? date('Y-m-d', strtotime($row['deadline_time'])) : "",
        'status' => $status_badge,
        'actions' => $actions
    ];
}
echo json_encode([
    "draw" => (int)$draw,
    "recordsTotal" => (int)$totalRecords,
    "recordsFiltered" => (int)$totalFiltered,
    "data" => $data
]);
exit;