<?php
include("config/database.php");
include("config/auth_check.php");
include("includes/sidemenu.php");

error_reporting(E_ALL);
ini_set('display_errors',0);

$daily_today_task = 0;
$todays_start_date = date('Y-m-d'). " 00:00:00";
$todays_end_date = date('Y-m-d'). " 23:59:59";

/*Get Pending Task Count*/
$daily_sql = "SELECT COUNT(id) as total_daily_count FROM task_master  WHERE task_type = ?  AND created_at BETWEEN ? AND ?";
$stmt = $con->prepare($daily_sql);
$task_type = 'daily';
$stmt->bind_param("sss", $task_type, $todays_start_date, $todays_end_date);
$stmt->execute();
$result = $stmt->get_result();
$row    = $result->fetch_assoc();
$total_daily_count = $row['total_daily_count'] ?? 0;
$stmt->close();

/*Get Weekly Task Count*/
$weekly_sql = "SELECT COUNT(id) as total_weekly_count FROM task_master  WHERE task_type = ?";
$wstmt = $con->prepare($weekly_sql);
$task_type = 'weekly';
$wstmt->bind_param("s", $task_type);
$wstmt->execute();
$wresult = $wstmt->get_result();
$wrow    = $wresult->fetch_assoc();
$total_weekly_count = $wrow['total_weekly_count'] ?? 0;
$wstmt->close();

/* GET OVERDUE TASK LIST*/
$sql = "SELECT t.id,t.title,t.department_id,t.task_type,t.deadline_time,t.assigned_to, TIMESTAMPDIFF(HOUR, t.deadline_time, NOW()) AS overdue_hours FROM task_master t WHERE t.completed_at IS NULL  AND t.deadline_time < NOW() ORDER BY overdue_hours DESC";
$stmt1 = $con->prepare($sql);
$stmt1->execute();
$result1 = $stmt1->get_result();

$total_overdue_task = $result1->num_rows;

$departmentwise_complete_task = get_department_performance();

$get_completion_by_task_type = get_completion_by_task_type();

$data = getOverallCompletion();


?>
<link href="<?php echo $site_path; ?>/assets/css/task-dashboard.css?v=<?php echo time(); ?>" rel="stylesheet" type="text/css" />

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">

        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading text-gray-900 fw-bold fs-3 my-0">Manage Task</h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="<?php echo $site_path; ?>/pages/dashboard" class="text-muted text-hover-primary">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">Task List</li>
                    </ul>
                </div>
            </div>
        </div>

        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-fluid">

                <!-- Action Button -->
                <div class="d-flex justify-content-end mb-5">
                    <a href="<?php echo $site_path; ?>/create-task" class="btn btn-dark">
                        <i class="ki-duotone ki-plus fs-4"></i>
                        Assign Task
                    </a>
                </div>

                <!-- ── Stats Row ── -->
                <div class="row g-4 mb-5">

                    <div class="col-md-6 col-xl">
                        <div class="card dashboard-card">
                            <div class="card-body">
                                <div class="stat-label">Overall Completion</div>
                                <div class="stats-value text-warning"> <?= $data['completion_percentage'] ?>%</div>
                                <div class="<?= ($data['change'] >= 0) ? 'text-success' : 'text-danger' ?>"><?= ($data['change'] >= 0) ? '↑' : '↓' ?><?= abs($data['change']) ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl">
                        <div class="card dashboard-card overdue-card">
                            <div class="card-body">
                                <div class="stat-label">Overdue Tasks</div>
                                <div class="stats-value text-danger"><?php echo $total_overdue_task; ?></div>
                                <div class="stat-sub text-danger">↑ 8 since morning</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl">
                        <div class="card dashboard-card">
                            <div class="card-body">
                                <div class="stat-label">Daily Tasks Today</div>
                                <div class="stats-value text-primary"><?php echo $total_daily_count; ?></div>
                                <div class="stat-sub text-muted">38 done &nbsp;·&nbsp; 4 pending</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl">
                        <div class="card dashboard-card">
                            <div class="card-body">
                                <div class="stat-label">Weekly Tasks</div>
                                <div class="stats-value text-info"><?php echo $total_weekly_count; ?></div>
                                <div class="stat-sub text-muted">11 done &nbsp;·&nbsp; 7 pending</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl">
                        <div class="card dashboard-card">
                            <div class="card-body">
                                <div class="stat-label">Specific Deadlines</div>
                                <div class="stats-value" style="color:#d97706;">7</div>
                                <div class="stat-sub text-muted">This week &nbsp;·&nbsp; 3 done</div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- ── Middle Row ── -->
                <div class="row g-4 mb-5">

                    <!-- Overdue Tasks Table -->
                    <div class="col-lg-8">
                        <div class="card section-card">
                            <div class="card-header">
                                <h3 class="card-title text-danger">
                                    ⚠️ Overdue Tasks
                                </h3>
                                <a href="#" class="btn-view-all">View all</a>
                            </div>
                            <div class="card-body">
                                <table class="overdue-table">
                                    <thead>
                                        <tr>
                                            <th>Task</th>
                                            <th>Type</th>
                                            <th>Staff &nbsp;·&nbsp; Dept</th>
                                            <th>Overdue By</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="overdue_tasks">
                                        <?php if ($result1 && $result1->num_rows > 0): ?>
                                            <?php while ($row = $result1->fetch_assoc()): ?>
                                                <?php
                                                    $badge_class = strtolower($row['task_type']) === 'daily' ? 'badge-daily' : 'badge-specific';
                                                    $overdue_label = formatOverdue($row['deadline_time']);
                                                    $department_name = "";
                                                    $staffname = [];
                                                    if($row['department_id'] > 0){
                                                        $depart_ment_sql = "SELECT department_name from departments where id = '{$row['department_id']}'";
                                                        $department_res = $con->query($depart_ment_sql);
                                                        if($department_res && $department_res->num_rows > 0){
                                                            $dept_row = $department_res->fetch_assoc();
                                                            $department_name = $dept_row['department_name'];
                                                        }
                                                    }else{
                                                        $department_name = "All";
                                                    }
                                                    
                                                    $user_sql = "SELECT name from user where id in ({$row['assigned_to']})";
                                                    $user_res = $con->query($user_sql);
                                                    if($user_res && $user_res->num_rows >0){
                                                        while($user_row = $user_res->fetch_assoc()){
                                                            $staffname[] = $user_row['name'];
                                                        }
                                                    }
                                                ?>
                                                <tr>
                                                    <td>
                                                        <div class="task-name"><?= htmlspecialchars($row['title']) ?></div>
                                                        <div class="task-dept"><?= htmlspecialchars($department_name) ?></div>
                                                    </td>
                                                    <td>
                                                        <span class="badge-type <?= $badge_class ?>">
                                                            <?= htmlspecialchars($row['task_type']) ?>
                                                        </span>
                                                    </td>
                                                    <td><?= implode("<br>",$staffname); ?> &nbsp;·&nbsp; <?= htmlspecialchars($department_name) ?></td>
                                                    <td><span class="overdue-time"><?= $overdue_label ?></span></td>
                                                    <td><button class="btn-view" onclick="viewTask(<?= $row['id'] ?>)">View</button></td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5" style="text-align: center;">No overdue tasks found</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Department Completion -->
                    <div class="col-lg-4">
                        <div class="card section-card">
                            <div class="card-header">
                                <h3 class="card-title">Department completion today</h3>
                            </div>
                            <div class="card-body">
                                <div class="dept-list" id="department_completion">
                                    <?php 
                                    if($departmentwise_complete_task){
                                    foreach($departmentwise_complete_task as $dept_row){
                                        $icon = '';
                                        if(strtolower($dept_row['department_name']) == 'stitching'){
                                            $icon = '✂️';
                                        }else if(strtolower($dept_row['department_name']) == 'design studio'){
                                            $icon = '🎨';  
                                        }else if(strtolower($dept_row['department_name']) == 'dispatch'){
                                            $icon = '📦';  
                                        }else if(strtolower($dept_row['department_name']) == 'Accounts'){
                                            $icon = '💰';
                                        }
                                        ?>
                                    <div class="dept-item">
                                        <div class="dept-row">
                                            <div class="dept-icon icon-blue"><?php echo $icon; ?></div>
                                            <div class="dept-info">
                                                <div class="dept-name"><?php echo $dept_row['department_name']; ?></div>
                                                <div class="dept-sub"><?php echo $dept_row['employee_name']; ?> &nbsp;·&nbsp; <?php echo $dept_row['total_tasks']; ?> tasks</div>
                                            </div>
                                            <div class="dept-pct" style="color:#22c55e;"><?php echo $dept_row['completion_percentage'] ?>%</div>
                                        </div>
                                        <div class="dept-bar-track">
                                            <div class="dept-bar-fill" style="width: <?php echo  $dept_row['completion_percentage'] ?>%"></div>
                                        </div>
                                    </div>
                                    <?php } }?>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- ── Completion By Task Type ── -->
                <div class="card section-card mb-5">
                    <div class="card-header">
                        <span class="completion-title-bar">Completion by task type — this week</span>
                    </div>
                    <div class="card-body completion-body">
                        <?php if($get_completion_by_task_type){
                                foreach($get_completion_by_task_type as  $single_value){
                                    $percentage = ($single_value['total_tasks'] > 0) ? round(($single_value['completed_tasks'] / $single_value['total_tasks']) * 100) : 0;
                                    ?>
                        <div class="comp-row">
                            <div class="comp-top">
                                <span class="comp-label">📋 <?php echo $single_value['task_type']; ?> routines</span>
                                <span class="comp-count"><?php echo $single_value['completed_tasks']; ?> / <?php echo $single_value['total_tasks']; ?></span>
                            </div>
                            <div class="progress-track">
                                <div class="progress-fill fill-blue" style="width:<?php echo $percentage; ?>%;"></div>
                            </div>
                        </div>
                        <?php } }?>
                    </div>
                </div>

            </div>
        </div>
        <?php include("includes/footer.php"); ?>
    </div>
</div>

<script src="<?php echo $site_path; ?>/assets/plugins/global/plugins.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/scripts.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/custom/apps/user-management/users/list/table.js?v=<?php echo time(); ?>"></script>

<script>
    // Overdue tasks will be loaded dynamically via your existing JS
    // Department completion will render from #department_completion div
</script>
