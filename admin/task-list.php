<?php
include("config/database.php");
include("config/auth_check.php");
include("includes/sidemenu.php");

error_reporting(E_ALL);
ini_set('display_errors',1);

$todays_start_date = date('Y-m-d'). " 00:00:00";
$todays_end_date = date('Y-m-d'). " 23:59:59";

$department_list = getAllDepartments();
$design_categories = get_all_tag_list();

/* ---------- Total tasks this month ---------- */
$month_start = date('Y-m-01 00:00:00');
$month_end   = date('Y-m-t 23:59:59');
$total_sql = "SELECT COUNT(id) as total FROM task_master WHERE created_at BETWEEN ? AND ?";
$stmt = $con->prepare($total_sql);
$stmt->bind_param("ss", $month_start, $month_end);
$stmt->execute();
$total_tasks_month = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
$stmt->close();

/* ---------- In progress ---------- */
$inprog_sql = "SELECT COUNT(id) as total FROM task_master WHERE status = 'in_progress'";
$res = $con->query($inprog_sql);
$total_in_progress = $res ? ($res->fetch_assoc()['total'] ?? 0) : 0;

/* ---------- Completed ---------- */
$done_sql = "SELECT COUNT(id) as total FROM task_master WHERE status = 'done'";
$res = $con->query($done_sql);
$total_done = $res ? ($res->fetch_assoc()['total'] ?? 0) : 0;

$completion_rate = ($total_tasks_month > 0) ? round(($total_done / $total_tasks_month) * 100) : 0;

/* ---------- Pending Task Counts (daily / weekly kept from original) ---------- */
$daily_sql = "SELECT COUNT(id) as total_daily_count FROM task_master WHERE task_type = ? AND created_at BETWEEN ? AND ?";
$stmt = $con->prepare($daily_sql);
$task_type = 'daily';
$stmt->bind_param("sss", $task_type, $todays_start_date, $todays_end_date);
$stmt->execute();
$total_daily_count = $stmt->get_result()->fetch_assoc()['total_daily_count'] ?? 0;
$stmt->close();

$weekly_sql = "SELECT COUNT(id) as total_weekly_count FROM task_master WHERE task_type = ?";
$wstmt = $con->prepare($weekly_sql);
$task_type = 'weekly';
$wstmt->bind_param("s", $task_type);
$wstmt->execute();
$total_weekly_count = $wstmt->get_result()->fetch_assoc()['total_weekly_count'] ?? 0;
$wstmt->close();

/* ---------- Overdue Tasks ---------- */
$sql = "SELECT t.id,t.title,t.department_id,t.task_type,t.deadline_time,t.assigned_to,
        TIMESTAMPDIFF(HOUR, t.deadline_time, NOW()) AS overdue_hours
        FROM task_master t
        WHERE t.completed_at IS NULL AND t.deadline_time < NOW()
        ORDER BY overdue_hours DESC";
$stmt1 = $con->prepare($sql);
$stmt1->execute();
$result1 = $stmt1->get_result();
$total_overdue_task = $result1->num_rows;

/* ---------- Free / Idle staff today ---------- */
$idle_sql = "SELECT u.id, u.name, u.typee, u.department_id, d.department_name
             FROM user u
             LEFT JOIN departments d ON d.id = u.department_id
             WHERE u.id NOT IN (
                SELECT DISTINCT assigned_to FROM task_master
                WHERE completed_at IS NULL
                AND created_at BETWEEN '$todays_start_date' AND '$todays_end_date'
             )";
$idle_res = $con->query($idle_sql);
$idle_staff = [];
if ($idle_res) {
    while ($r = $idle_res->fetch_assoc()) { $idle_staff[] = $r; }
}
$total_idle_staff = count($idle_staff);

/* ---------- Department-wise performance / completion (existing helpers) ---------- */
$departmentwise_complete_task = function_exists('get_department_performance') ? get_department_performance() : [];
$get_completion_by_task_type   = function_exists('get_completion_by_task_type') ? get_completion_by_task_type() : [];
$data = function_exists('getOverallCompletion') ? getOverallCompletion() : ['completion_percentage' => $completion_rate, 'change' => 0];

/* helper for overdue formatting fallback if not defined elsewhere */
if (!function_exists('formatOverdue')) {
    function formatOverdue($deadline) {
        $diff = time() - strtotime($deadline);
        $days = floor($diff / 86400);
        if ($days >= 1) return $days . "d overdue";
        $hours = floor($diff / 3600);
        return $hours . "h overdue";
    }
}
?>
<link href="<?php echo $site_path; ?>/assets/css/task-dashboard.css?v=<?php echo time(); ?>" rel="stylesheet" type="text/css" />

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">
        <div class="task-header">
            <div class="task-header-left">
                <h1 class="task-title">Task Management</h1>
                <div class="task-subtitle">
                    Dept-specific task formats · Idle alerts · Three-level completion
                </div>
            </div>

            <div class="task-header-right">
                <button type="button" class="btn btn-ai">
                    AI suggestions
                </button>

                <button type="button"
                        class="btn btn-create-task"
                        data-bs-toggle="modal"
                        data-bs-target="#createTaskModal">
                    + Create task
                </button>
            </div>

        </div>

        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-fluid">

                <!-- ── Tab Nav ── -->
                <ul class="task-tabs" id="taskTabNav">
                    <li class="task-tab active" data-tab="dashboard"><i class="ki-duotone ki-element-11 fs-4 me-1"></i>Dashboard</li>
                    <li class="task-tab" data-tab="free-idle"><i class="ki-duotone ki-user fs-4 me-1"></i>Free / Idle <span class="tab-badge"><?= $total_idle_staff ?></span></li>
                    <li class="task-tab" data-tab="kanban"><i class="ki-duotone ki-element-plus fs-4 me-1"></i>Kanban</li>
                    <li class="task-tab" data-tab="list"><i class="ki-duotone ki-row-horizontal fs-4 me-1"></i>List</li>
                    <li class="task-tab" data-tab="pending-confirm"><i class="ki-duotone ki-time fs-4 me-1"></i>Pending confirm <span class="tab-badge" id="pendingConfirmBadge">0</span></li>
                    <li class="task-tab" data-tab="recurring"><i class="ki-duotone ki-arrows-circle fs-4 me-1"></i>Recurring</li>
                </ul>

                <!-- ===================================================== -->
                <!-- TAB: DASHBOARD                                        -->
                <!-- ===================================================== -->
                <div class="task-tab-pane active" id="tab-dashboard">
                    <?php if ($total_idle_staff > 0): ?>
                    <div class="idle-alert-banner">
                        <i class="ki-duotone ki-information-5 fs-3 text-warning"></i>
                        <span>
                            <strong><?= $total_idle_staff ?> staff have no active tasks today</strong>
                            <?php
                                $idle_depts = array_unique(array_filter(array_column($idle_staff, 'department_name')));
                                if (!empty($idle_depts)) {
                                    echo " — " . htmlspecialchars(implode(" and ", array_slice($idle_depts,0,2))) . " " . (count($idle_depts) > 1 ? "are" : "is") . " fully idle.";
                                }
                            ?>
                            <a href="#" class="link-to-tab" data-tab="free-idle">View Free tab &rarr;</a>
                        </span>
                    </div>
                    <?php endif; ?>

                    <div class="row g-4 mb-5">
                        <div class="col-md-6 col-xl">
                            <div class="card dashboard-card">
                                <div class="card-body">
                                    <div class="stat-label">TOTAL TASKS</div>
                                    <div class="stats-value text-dark"><?= (int)$total_tasks_month ?></div>
                                    <div class="stat-sub text-muted">This month</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl">
                            <div class="card dashboard-card">
                                <div class="card-body">
                                    <div class="stat-label">IN PROGRESS</div>
                                    <div class="stats-value text-primary"><?= (int)$total_in_progress ?></div>
                                    <div class="stat-sub text-muted">Active now</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl">
                            <div class="card dashboard-card overdue-card">
                                <div class="card-body">
                                    <div class="stat-label">OVERDUE</div>
                                    <div class="stats-value text-danger"><?= (int)$total_overdue_task ?></div>
                                    <div class="stat-sub text-muted">Need action</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl">
                            <div class="card dashboard-card">
                                <div class="card-body">
                                    <div class="stat-label">FREE / IDLE STAFF</div>
                                    <div class="stats-value text-warning"><?= (int)$total_idle_staff ?></div>
                                    <div class="stat-sub"><a href="#" class="link-to-tab" data-tab="free-idle">Assign tasks &rarr;</a></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl">
                            <div class="card dashboard-card">
                                <div class="card-body">
                                    <div class="stat-label">COMPLETED</div>
                                    <div class="stats-value text-success"><?= (int)$total_done ?></div>
                                    <div class="stat-sub text-muted"><?= $completion_rate ?>% rate</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4 mb-5">
                        <div class="col-lg-6">
                            <div class="card section-card">
                                <div class="card-header">
                                    <h3 class="card-title text-warning">
                                        <i class="ki-duotone ki-information-5 fs-3 text-warning me-1"></i> Overdue tasks
                                    </h3>
                                    <a href="#" class="btn-view-all link-to-tab" data-tab="list">View all</a>
                                </div>
                                <div class="card-body">
                                    <div class="overdue-list" id="overdue_tasks">
                                        <?php if ($result1 && $result1->num_rows > 0): ?>
                                            <?php while ($row = $result1->fetch_assoc()): ?>
                                                <?php
                                                    $department_name = "All";
                                                    if ($row['department_id'] > 0) {
                                                        $dstmt = $con->prepare("SELECT department_name FROM departments WHERE id = ?");
                                                        $dstmt->bind_param("i", $row['department_id']);
                                                        $dstmt->execute();
                                                        $dres = $dstmt->get_result();
                                                        if ($dres && $dres->num_rows > 0) {
                                                            $department_name = $dres->fetch_assoc()['department_name'];
                                                        }
                                                        $dstmt->close();
                                                    }
                                                    $staffname = [];
                                                    $assigned_ids = array_filter(array_map('intval', explode(',', $row['assigned_to'])));
                                                    if (!empty($assigned_ids)) {
                                                        $placeholders = implode(',', array_fill(0, count($assigned_ids), '?'));
                                                        $types = str_repeat('i', count($assigned_ids));
                                                        $ustmt = $con->prepare("SELECT name FROM user WHERE id IN ($placeholders)");
                                                        $ustmt->bind_param($types, ...$assigned_ids);
                                                        $ustmt->execute();
                                                        $ures = $ustmt->get_result();
                                                        while ($urow = $ures->fetch_assoc()) { $staffname[] = $urow['name']; }
                                                        $ustmt->close();
                                                    }
                                                    $overdue_label = formatOverdue($row['deadline_time']);
                                                ?>
                                                <div class="overdue-row">
                                                    <div class="overdue-id">T-<?= str_pad($row['id'], 4, '0', STR_PAD_LEFT) ?></div>
                                                    <div class="overdue-main">
                                                        <div class="task-name"><?= htmlspecialchars($row['title']) ?></div>
                                                        <div class="task-dept text-muted"><?= htmlspecialchars(implode(", ", $staffname)) ?> &middot; <?= htmlspecialchars($department_name) ?> &middot; Due: <?= date('Y-m-d', strtotime($row['deadline_time'])) ?></div>
                                                    </div>
                                                    <span class="badge-overdue"><?= $overdue_label ?></span>
                                                    <button class="btn-remind">Remind</button>
                                                </div>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <div class="text-center text-muted py-4">No overdue tasks found</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="card section-card">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="ki-duotone ki-abstract-26 fs-3 text-warning me-1"></i> Department status</h3>
                                </div>
                                <div class="card-body">
                                    <div class="dept-status-list" id="department_completion">
                                        <?php if (!empty($departmentwise_complete_task)) : ?>
                                            <?php foreach ($departmentwise_complete_task as $dept_row): ?>
                                                <div class="dept-status-row">
                                                    <div class="dept-status-name"><?= $dept_row['department_name']; ?></div>
                                                    <div class="dept-status-bar-track">
                                                        <div class="dept-status-bar-fill" style="width: <?= (float)$dept_row['completion_percentage'] ?>%"></div>
                                                    </div>
                                                    <div class="dept-status-frac text-muted"><?= (int)($dept_row['completed_tasks'] ?? 0) ?>/<?= (int)($dept_row['total_tasks'] ?? 0) ?></div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="text-center text-muted py-4">No department data available</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card section-card mb-5">
                        <div class="card-header">
                            <h3 class="card-title"><i class="ki-duotone ki-people fs-3 text-warning me-1"></i> Staff workload today</h3>
                        </div>
                        <div class="card-body">
                            <div class="staff-workload-grid" id="staff_workload">
                                <?php
                                $staff_sql = "SELECT u.id, u.name,
                                                (SELECT COUNT(*) FROM task_master tm WHERE tm.assigned_to = u.id AND tm.completed_at IS NULL) as open_tasks
                                              FROM user u";
                                $staff_res = $con->query($staff_sql);
                                if ($staff_res && $staff_res->num_rows > 0):
                                    while ($srow = $staff_res->fetch_assoc()):
                                        $initial = strtoupper(substr($srow['name'], 0, 1));
                                        $open = (int)$srow['open_tasks'];
                                ?>
                                <div class="staff-card <?= $open == 0 ? 'staff-idle' : '' ?>">
                                    <div class="staff-avatar"><?= htmlspecialchars($initial) ?></div>
                                    <div class="staff-info">
                                        <div class="staff-name"><?= htmlspecialchars($srow['name']) ?></div>
                                        <?php if ($open > 0): ?>
                                            <div class="staff-sub text-muted"><?= $open ?> open task<?= $open > 1 ? 's' : '' ?></div>
                                        <?php else: ?>
                                            <div class="staff-sub text-warning"><i class="ki-duotone ki-information-5 fs-6"></i> No active tasks</div>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($open == 0): ?>
                                        <button class="btn-assign-mini" onclick="quickAssign(<?= $srow['id'] ?>)">&#9889; Assign</button>
                                    <?php endif; ?>
                                </div>
                                <?php
                                    endwhile;
                                else:
                                ?>
                                    <div class="text-center text-muted py-4">No staff found</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ===================================================== -->
                <!-- TAB: FREE / IDLE                                      -->
                <!-- ===================================================== -->
                <div class="task-tab-pane" id="tab-free-idle">
                    <div class="idle-alert-banner idle-alert-strong">
                        <div>
                            <div class="fw-bold text-warning">Free &amp; Idle &mdash; assign tasks now</div>
                            <div class="text-muted fs-7">Staff with no tasks are shown below. Use Quick Assign to assign directly from here. Departments with no active tasks are also flagged.</div>
                        </div>
                    </div>

                    <div class="idle-section-title"><i class="ki-duotone ki-user fs-4 me-1"></i> Staff with no tasks today</div>
                    <div class="idle-staff-grid" id="idle_staff_grid">
                        <?php if (!empty($idle_staff)): foreach ($idle_staff as $is): ?>
                            <div class="idle-staff-card">
                                <div class="idle-staff-avatar"><?= htmlspecialchars(strtoupper(substr($is['name'],0,1))) ?></div>
                                <div class="idle-staff-name"><?= htmlspecialchars($is['name']) ?></div>
                                <div class="idle-staff-role text-muted"><?= htmlspecialchars($is['typee'] ?? '') ?> &middot; <?= htmlspecialchars($is['department_name'] ?? 'Unassigned') ?></div>
                                <div class="idle-staff-time text-muted"><i class="ki-duotone ki-time fs-7"></i> All day today</div>
                                <button class="btn-quick-assign" onclick="quickAssign(<?= $is['id'] ?>)">&#9889; Quick assign task</button>
                            </div>
                        <?php endforeach; else: ?>
                            <div class="text-center text-muted py-4">No idle staff right now &mdash; everyone has active tasks.</div>
                        <?php endif; ?>
                    </div>

                    <div class="idle-section-title"><i class="ki-duotone ki-calendar fs-4 me-1"></i> Departments with no active tasks</div>
                    <div class="idle-dept-grid" id="idle_dept_grid">
                        <?php
                        $dept_idle_sql = "SELECT d.id, d.department_name,
                                            (SELECT COUNT(*) FROM user u WHERE u.department_id = d.id) as staff_count
                                          FROM departments d
                                          WHERE d.id NOT IN (
                                            SELECT DISTINCT department_id FROM task_master
                                            WHERE completed_at IS NULL AND department_id > 0
                                          )";
                        $dept_idle_res = $con->query($dept_idle_sql);
                        if ($dept_idle_res && $dept_idle_res->num_rows > 0):
                            while ($drow = $dept_idle_res->fetch_assoc()):
                        ?>
                            <div class="idle-dept-card">
                                <div class="idle-dept-name"><?= htmlspecialchars($drow['department_name']) ?></div>
                                <div class="idle-dept-staff text-muted"><?= (int)$drow['staff_count'] ?> staff idle</div>
                                <div class="idle-dept-note text-success">No tasks assigned today</div>
                                <button class="btn-create-dept-task" onclick="createDeptTask(<?= $drow['id'] ?>)">+ Create dept task</button>
                            </div>
                        <?php
                            endwhile;
                        else:
                        ?>
                            <div class="text-center text-muted py-4">All departments have active tasks.</div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- ===================================================== -->
                <!-- TAB: KANBAN                                           -->
                <!-- ===================================================== -->
                <div class="task-tab-pane" id="tab-kanban">
                    <div class="kanban-filter-bar">
                        <div class="kanban-filter-pills" id="kanban_dept_filters">
                            <button class="pill active" data-dept="all">All</button>
                            <?php
                            $kf_res = $con->query("SELECT id, department_name FROM departments ORDER BY department_name");
                            if ($kf_res) { while ($kf = $kf_res->fetch_assoc()) {
                                echo '<button class="pill" data-dept="'.(int)$kf['id'].'">'.htmlspecialchars($kf['department_name']).'</button>';
                            } }
                            ?>
                        </div>
                        <select class="form-select form-select-sm w-auto" id="kanban_staff_filter">
                            <option value="">All staff</option>
                            <?php
                            $ks_res = $con->query("SELECT id, name FROM user ORDER BY name");
                            if ($ks_res) { while ($ks = $ks_res->fetch_assoc()) {
                                echo '<option value="'.(int)$ks['id'].'">'.htmlspecialchars($ks['name']).'</option>';
                            } }
                            ?>
                        </select>
                    </div>

                    <div class="kanban-board" id="kanban_board">
                        <?php
                        $statuses = [
                            'todo'        => ['label' => 'To Do',            'class' => 'col-todo'],
                            'in_progress' => ['label' => 'In Progress',      'class' => 'col-progress'],
                            'pending'     => ['label' => 'Pending confirm',  'class' => 'col-pending'],
                            'done'        => ['label' => 'Done',             'class' => 'col-done'],
                        ];
                        foreach ($statuses as $status_key => $meta) {
                            $kt_sql = "SELECT t.id, t.title, t.task_type, t.priority, t.deadline_time, t.assigned_to, t.department_id
                                       FROM task_master t WHERE t.status = ?";
                            $kstmt = $con->prepare($kt_sql);
                            $kstmt->bind_param("s", $status_key);
                            $kstmt->execute();
                            $kres = $kstmt->get_result();
                            $count = $kres->num_rows;
                            echo '<div class="kanban-col '.$meta['class'].'" data-status="'.$status_key.'">';
                            echo '<div class="kanban-col-header"><span class="kanban-dot"></span>'.$meta['label'].'<span class="kanban-count">'.$count.'</span></div>';
                            echo '<div class="kanban-col-body">';
                            while ($t = $kres->fetch_assoc()) {
                                $dept_name = '';
                                if ($t['department_id'] > 0) {
                                    $dn = $con->query("SELECT department_name FROM departments WHERE id = ".(int)$t['department_id']);
                                    if ($dn && $dn->num_rows > 0) $dept_name = $dn->fetch_assoc()['department_name'];
                                }
                                $assignee_name = '';
                                if ($t['assigned_to']) {
                                    $an = $con->query("SELECT name FROM user WHERE id = ".(int)$t['assigned_to']);
                                    if ($an && $an->num_rows > 0) $assignee_name = $an->fetch_assoc()['name'];
                                }
                                $priority = htmlspecialchars($t['priority'] ?? 'Medium');
                                echo '<div class="kanban-card" data-priority="'.strtolower($priority).'" data-dept="'.(int)$t['department_id'].'">';
                                echo '<div class="kanban-card-top"><span class="kanban-card-title">'.htmlspecialchars($t['title']).'</span><span class="kanban-priority-badge">'.$priority.'</span></div>';
                                echo '<div class="kanban-card-tags"><span class="tag">'.htmlspecialchars($dept_name).'</span><span class="tag tag-blue">'.htmlspecialchars($t['task_type']).'</span></div>';
                                echo '<div class="kanban-card-footer"><span class="kanban-assignee"><span class="avatar-mini">'.htmlspecialchars(strtoupper(substr($assignee_name,0,1))).'</span>'.htmlspecialchars($assignee_name).'</span><span class="text-muted">'.date('Y-m-d', strtotime($t['deadline_time'])).'</span></div>';
                                echo '</div>';
                            }
                            echo '</div></div>';
                            $kstmt->close();
                        }
                        ?>
                    </div>
                </div>

                <!-- ===================================================== -->
                <!-- TAB: LIST  (DataTable + AJAX server-side processing)  -->
                <!-- ===================================================== -->
                <div class="task-tab-pane" id="tab-list">
                    <div class="list-filter-pills">
                        <button class="pill active" data-filter="all">All</button>
                        <button class="pill" data-filter="overdue">Overdue</button>
                        <button class="pill" data-filter="today">Today</button>
                        <button class="pill" data-filter="week">This week</button>
                        <button class="pill" data-filter="high">High priority</button>
                    </div>
                    <div class="list-filter-dropdowns">
                        <select class="form-select form-select-sm w-auto" id="list_dept_filter">
                            <option value="">All departments</option>
                            <?php
                            $ld_res = $con->query("SELECT id, department_name FROM departments ORDER BY department_name");
                            if ($ld_res) { while ($ld = $ld_res->fetch_assoc()) {
                                echo '<option value="'.(int)$ld['id'].'">'.htmlspecialchars($ld['department_name']).'</option>';
                            } }
                            ?>
                        </select>
                        <select class="form-select form-select-sm w-auto" id="list_freq_filter">
                            <option value="">All frequencies</option>
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                            <option value="specific">Specific</option>
                            <option value="onetime">Onetime</option>
                            <option value="recurring">Recurring</option>
                        </select>
                        <button class="btn btn-light-secondary btn-sm" id="clear_list_filters">&times; Clear filters</button>
                    </div>

                    <div class="card section-card">
                        <div class="table-responsive">
                            <table class="table-list" id="task_list_table" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>Frequency</th>
                                        <th>Priority</th>
                                        <th>Dept</th>
                                        <th>Assigned to</th>
                                        <th>Due</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- rows loaded via DataTables AJAX -> ajax/list-tasks-ajax.php -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ===================================================== -->
                <!-- TAB: PENDING CONFIRM                                  -->
                <!-- ===================================================== -->
                <div class="task-tab-pane" id="tab-pending-confirm">
                    <div class="idle-alert-banner">
                        <div>
                            <div class="fw-bold text-warning">Confirmation queue</div>
                            <div class="text-muted fs-7">Staff marked these tasks complete. Dept Head reviews and confirms. On confirm, Admin sees final Done status.</div>
                        </div>
                    </div>

                    <div id="pending_confirm_list">
                    <?php
                    $pc_sql = "SELECT t.id, t.title, t.department_id, t.assigned_to
                               FROM task_master t WHERE t.status = 'pending'";
                    $pc_res = $con->query($pc_sql);
                    $pc_count = 0;
                    if ($pc_res && $pc_res->num_rows > 0):
                        $pc_count = $pc_res->num_rows;
                        while ($pc = $pc_res->fetch_assoc()):
                            $dept_name = '';
                            if ($pc['department_id'] > 0) {
                                $dn = $con->query("SELECT department_name FROM departments WHERE id = ".(int)$pc['department_id']);
                                if ($dn && $dn->num_rows > 0) $dept_name = $dn->fetch_assoc()['department_name'];
                            }
                            $assignee_name = '';
                            if ($pc['assigned_to']) {
                                $an = $con->query("SELECT name FROM user WHERE id = ".(int)$pc['assigned_to']);
                                if ($an && $an->num_rows > 0) $assignee_name = $an->fetch_assoc()['name'];
                            }
                    ?>
                        <div class="pending-confirm-card">
                            <div class="pending-confirm-top">
                                <div class="pending-confirm-title"><?= htmlspecialchars($pc['title']) ?></div>
                                <span class="badge-awaiting">Awaiting confirm</span>
                            </div>
                            <div class="pending-confirm-meta text-muted">T-<?= str_pad($pc['id'],4,'0',STR_PAD_LEFT) ?> &middot; <?= htmlspecialchars($assignee_name) ?> &middot; <?= htmlspecialchars($dept_name) ?></div>
                            <div class="pending-confirm-note">
                                <div class="text-muted fs-8">Completion note</div>
                                <?= htmlspecialchars($pc['completion_note'] ?? 'Completed all checklist items. Waiting for confirmation.') ?>
                            </div>
                            <div class="pending-confirm-actions">
                                <button class="btn-review-confirm review-confirm-btn" data-bs-toggle="modal" data-bs-target="#confirmTaskModal"  data-task-id="<?php echo $pc['id']; ?>" data-task-title="<?php echo $pc['title']; ?>"  data-staff="<?= $assignee_name; ?>" data-department="<?= htmlspecialchars($dept_name); ?>">&#10003; Review &amp; confirm</button>
                                <button class="btn-view-full" onclick="viewTask(<?= $pc['id'] ?>)">View full task</button>
                            </div>
                        </div>
                    <?php
                        endwhile;
                    else:
                    ?>
                        <div class="text-center text-muted py-4">No tasks awaiting confirmation</div>
                    <?php endif; ?>
                    </div>
                </div>

                <!-- ===================================================== -->
                <!-- TAB: RECURRING (DataTable + AJAX server-side)         -->
                <!-- ===================================================== -->
                <div class="task-tab-pane" id="tab-recurring">
                    <div class="d-flex justify-content-end mb-4">
                        <button class="btn btn-warning text-white" data-bs-toggle="modal" data-bs-target="#createTaskModal">+ Add recurring</button>
                    </div>
                    <div class="card section-card">
                        <div class="table-responsive">
                            <table class="table-list" id="recurring_task_table" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Task</th>
                                        <th>Frequency</th>
                                        <th>Department</th>
                                        <th>Assignee</th>
                                        <th>Next Run</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <?php include("includes/footer.php"); ?>
    </div>
</div>
<!-- Create Task Popup Modal  -->
<div class="modal fade" id="createTaskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h3 class="fw-bold"><i class="fas fa-clipboard-list text-warning me-2"></i>Create Task</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="alert alert-warning mb-5">
                    <strong>When to manually create a task</strong><br>
                    Most tasks are auto-created by the system when a previous
                    workflow step is completed.
                </div>
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <label class="form-label">Department *</label>
                        <select class="form-select" name="department_id" id="department_id">
                            <option>Select</option>
                            <?php if($department_list){
                                    foreach($department_list as $single_dept){?>
                                        <option value="<?php echo $single_dept['id']; ?>"><?php echo $single_dept['department_name']; ?></option>
                                    <?php } } ?>
                        </select>
                    </div>

                    <div class="col-md-4 mb-4">
                        <label class="form-label">Task Type *</label>
                        <select class="form-select">
                            <option>Select</option>
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                            <option value="project">Project</option>
                            <option value="adhoc">Ad-hoc</option>
                            <option value="recurring">Recurring</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-4">
                        <label class="form-label">Priority</label>
                        <select class="form-select">
                            <option>Medium</option>
                            <option>High</option>
                            <option>Low</option>
                        </select>
                    </div>

                    <div class="col-md-12 mb-4">
                        <label class="form-label">Task Title *</label>
                        <input type="text" class="form-control" placeholder="Clear specific title">
                    </div>

                    <div class="col-md-6 mb-4">
                        <label class="form-label">Assign To *</label>
                        <select class="form-select">
                            <option>Select Staff</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label class="form-label">Assigned By</label>
                        <select class="form-select">
                            <option>Admin</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-4">
                        <label class="form-label">Start Date</label>
                        <input type="date" class="form-control">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label class="form-label">Due Date</label>
                        <input type="date" class="form-control">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label class="form-label">Est. Hours</label>
                        <input type="number" class="form-control" placeholder="e.g. 4">
                    </div>
                </div>

                <!-- Department Detail Card -->
                <div id="department_fields_container">
                    <div id="design_fields" class="dept-section d-none">
                        <div class="department-card">
                            <div class="department-card-header mb-6" style="font-weight: 600;">
                                <i class="fas fa-palette"></i>
                                Design Studio — task details
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <label>Design sub-type *</label>
                                    <select class="form-select">
                                        <option value="New creative">New creative</option>
                                        <option value="Repeat / variation">Repeat / variation</option>
                                        <option value="Admin reference">Admin reference</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label>Designer level</label>
                                    <select class="form-select">
                                        <option value="any">Any</option>
                                        <option value="basic">Basic</option>
                                        <option value="mid">Mid</option>
                                        <option value="max">Max</option>
                                        <option value="pro">Pro</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label>Sketch submit format</label>
                                    <select class="form-select">
                                        <option>Any</option>
                                        <option>Paper sketch — photo</option>
                                        <option>Digital file</option>
                                        <option>Mood board</option>
                                    </select>
                                </div>
                            </div>
                            <div class="section-title mt-4">
                                CATEGORY-WISE WEEKLY TARGETS
                            </div>
                            <table class="table table-bordered mt-2">
                                <thead>
                                    <tr>
                                        <th width="55%">CATEGORY</th>
                                        <th>WEEKLY</th>
                                        <th>MONTHLY</th>
                                        <th>BUDGET ₹</th>
                                    </tr>
                                </thead>
                                <tbody id="category_target_body">
                                    <?php foreach($design_categories as $row){ ?>
                                        <tr>
                                            <td><?= $row['name']; ?><input type="hidden" name="category_id[]" value="<?= $row['id']; ?>"></td>
                                            <td><input type="number" class="form-control" name="weekly_target[]" value=""></td>
                                            <td><input type="number" class="form-control" name="monthly_target[]" value=""></td>
                                            <td><input type="number" class="form-control" name="budget[]" value=""></td>
                                        </tr>
                                        <?php } ?>
                                </tbody>
                            </table>

                            <div class="row">
                                <div class="col-md-6">
                                    <label>Fabric (optional)</label>
                                    <input type="text" class="form-control" placeholder="e.g. Georgette, Silk">
                                </div>

                                <div class="col-md-6">
                                    <label>Work type</label>
                                    <select class="form-select">
                                        <option>Designers choice</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mt-3">
                                    <label>Occasion</label>
                                    <select class="form-select">
                                        <option>Festive</option>
                                        <option>Bridal</option>
                                        <option>Party wear</option>
                                        <option>Casual</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mt-3">
                                    <label>Reference</label>
                                    <select class="form-select">
                                        <option>— None</option>
                                    </select>
                                </div>

                                <div class="col-md-12 mt-3">
                                    <label>Color palette</label>
                                    <input type="text" class="form-control" placeholder="e.g. Jewel tones or Designers choice">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="purchase_fields" class="dept-section d-none">
                        <!-- Purchase HTML -->
                    </div>

                    <div id="production_fields" class="dept-section d-none">
                        <!-- Production HTML -->
                    </div>

                    <div id="qc_fields" class="dept-section d-none">
                        <!-- QC HTML -->
                    </div>

                    <div id="dispatch_fields" class="dept-section d-none">
                        <!-- Dispatch HTML -->
                    </div>

                </div>

                <!-- Description -->
                <div class="mt-5">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" rows="4" placeholder="Instructions, context, expected output..."></textarea>
                </div>
                <hr class="my-5">
                <div class="reminder-section">
                    <div class="text-uppercase fw-bold fs-8 text-muted mb-3">
                        Reminders
                    </div>

                    <div class="row">

                        <div class="col-md-4 mb-4">
                            <label class="form-label">
                                Remind before deadline
                            </label>

                            <select class="form-select" name="remind_before">
                                <option>1 day before</option>
                                <option>2 days before</option>
                                <option>3 days before</option>
                                <option>1 week before</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-4">
                            <label class="form-label">
                                Alert if not started by
                            </label>

                            <select class="form-select" name="alert_not_started">
                                <option>50% time passed</option>
                                <option>25% time passed</option>
                                <option>75% time passed</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-4">
                            <label class="form-label">
                                Escalate if overdue
                            </label>

                            <select class="form-select" name="escalate_after">
                                <option>After 1 day</option>
                                <option>After 2 days</option>
                                <option>After 3 days</option>
                                <option>After 1 week</option>
                            </select>
                        </div>

                    </div>

                    <div class="alert alert-primary py-3 mb-0">
                        <i class="fas fa-bell me-2"></i>
                        All alerts in-app only. Staff notified immediately on task creation.
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-light" data-bs-dismiss="modal"> Cancel </button>
                <button class="btn btn-secondary"> Save Draft</button>
                <button class="btn btn-warning text-white">Create & Assign</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="confirmTaskModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content confirm-modal">

            <div class="modal-header border-0">
                <h3 class="modal-title">
                    <i class="fas fa-check-circle text-warning me-2"></i>
                    Confirm Task Completion
                </h3>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                </button>
            </div>

            <form id="confirmTaskForm">

                <input type="hidden" name="task_id" id="confirm_task_id">
                <div class="modal-body">
                    <div class="task-summary-box">
                        <div class="fw-bold fs-4"
                             id="confirm_task_title">
                        </div>

                        <div class="text-muted mt-1"
                             id="confirm_task_info">
                        </div>

                    </div>

                    <div class="section-title">
                        STAFF COMPLETION NOTE
                    </div>

                    <div class="completion-note-box"
                         id="staff_completion_note">
                    </div>

                    <div class="mb-4">
                        <label class="form-label">
                            Dept Head remarks (optional)
                        </label>

                        <textarea class="form-control"
                                  rows="4"
                                  name="remarks"
                                  placeholder="Observations, feedback..."></textarea>
                    </div>

                </div>

                <div class="modal-footer border-0">

                    <button type="button"
                            class="btn btn-light"
                            data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="button"
                            class="btn btn-outline-warning"
                            id="sendBackBtn">
                        <i class="fas fa-undo me-1"></i>
                        Send back
                    </button>

                    <button type="submit"
                            class="btn btn-warning text-white">
                        <i class="fas fa-check me-1"></i>
                        Confirm done
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>
<script src="<?= $site_path ?>/assets/plugins/global/plugins.bundle.js"></script>
<script src="<?= $site_path ?>/assets/js/scripts.bundle.js"></script>
<script src="<?= $site_path ?>/assets/plugins/custom/datatables/datatables.bundle.js"></script>

<script>
(function () {
    /* ---------- Tab switching ---------- */
    const tabs  = document.querySelectorAll('.task-tab');
    const panes = document.querySelectorAll('.task-tab-pane');
    let listTableInited = false;
    let recurringTableInited = false;

    function activateTab(tabKey) {
        tabs.forEach(t => t.classList.toggle('active', t.dataset.tab === tabKey));
        panes.forEach(p => p.classList.toggle('active', p.id === 'tab-' + tabKey));

        // Lazy-init DataTables only when their tab is first opened
        // (avoids "table has no visible width" rendering glitches in hidden tabs)
        if (tabKey === 'list' && !listTableInited) {
            initListTable();
            listTableInited = true;
        }
        if (tabKey === 'recurring' && !recurringTableInited) {
            initRecurringTable();
            recurringTableInited = true;
        }
    }

    tabs.forEach(tab => {
        tab.addEventListener('click', () => activateTab(tab.dataset.tab));
    });

    document.querySelectorAll('.link-to-tab').forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            activateTab(this.dataset.tab);
        });
    });

    const pendingCards = document.querySelectorAll('#pending_confirm_list .pending-confirm-card').length;
    const badge = document.getElementById('pendingConfirmBadge');
    if (badge) badge.textContent = pendingCards;

    /* ---------- Kanban department / staff filters ---------- */
    const kanbanPills = document.querySelectorAll('#kanban_dept_filters .pill');
    kanbanPills.forEach(pill => {
        pill.addEventListener('click', function () {
            kanbanPills.forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            const dept = this.dataset.dept;
            document.querySelectorAll('.kanban-card').forEach(card => {
                card.style.display = (dept === 'all' || card.dataset.dept === dept) ? '' : 'none';
            });
        });
    });

    /* ===================================================================
       LIST TAB — DataTables server-side AJAX
       =================================================================== */
    window.initListTable = function () {
        window.taskListTable = $('#task_list_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= $site_path ?>/ajax/list-tasks-ajax',
                type: 'POST',
                data: function (d) {
                    d.quick         = document.querySelector('.list-filter-pills .pill.active')?.dataset.filter || 'all';
                    d.department_id = document.getElementById('list_dept_filter')?.value || '';
                    d.frequency     = document.getElementById('list_freq_filter')?.value || '';
                }
            },
            columns: [
                { data: 'id',          orderable: true  },
                { data: 'title',       orderable: true  },
                { data: 'task_type',   orderable: true  },
                { data: 'priority',    orderable: true  },
                { data: 'department',  orderable: true  },
                { data: 'assigned_to', orderable: false },
                { data: 'due',         orderable: true  },
                { data: 'status',      orderable: true  },
                { data: 'actions',     orderable: false, searchable: false },
            ],
            order: [[0, 'desc']],
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            drawCallback: function () {
                KTMenu.createInstances();
            },
            language: { search: '', searchPlaceholder: 'Search tasks...' }
        });
    };
    
    $("#list_dept_filter").on('change',function(){
        taskListTable.ajax.realod();
    });
    
    $("#list_freq_filter").on('change',function(){
        taskListTable.ajax.realod();
    });

    /* Re-fetch List table whenever a quick filter pill or dropdown changes */
    document.querySelectorAll('.list-filter-pills .pill').forEach(pill => {
        pill.addEventListener('click', function () {
            document.querySelectorAll('.list-filter-pills .pill').forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            if (window.taskListTable) window.taskListTable.ajax.reload();
        });
    });
    document.getElementById('list_dept_filter')?.addEventListener('change', () => window.taskListTable?.ajax.reload());
    document.getElementById('list_freq_filter')?.addEventListener('change', () => window.taskListTable?.ajax.reload());

    document.getElementById('clear_list_filters')?.addEventListener('click', function () {
        document.getElementById('list_dept_filter').value = '';
        document.getElementById('list_freq_filter').value = '';
        document.querySelectorAll('.list-filter-pills .pill').forEach((p, i) => p.classList.toggle('active', i === 0));
        if (window.taskListTable) window.taskListTable.ajax.reload();
    });

    /* ===================================================================
       RECURRING TAB — DataTables server-side AJAX
       =================================================================== */
    window.initRecurringTable = function () {
        window.recurringTable = $('#recurring_task_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= $site_path ?>/ajax/recurring-tasks-ajax',
                type: 'POST'
            },
            columns: [
                { data: 'title',      orderable: true  },
                { data: 'frequency',  orderable: true  },
                { data: 'department', orderable: true  },
                { data: 'assignee',   orderable: false },
                { data: 'next_run',        orderable: true  },
                { data: 'status',     orderable: true  },
                { data: 'actions',    orderable: false, searchable: false },
            ],
            order: [[4, 'asc']],
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            language: { search: '', searchPlaceholder: 'Search recurring tasks...' }
        });
    };
})();

function viewTask(id) {
    window.location.href = '<?php echo $site_path; ?>/view-task?id=' + id;
}
function quickAssign(staffId) {
    openCreateTaskModal({ assigned_to: staffId });
}
function createDeptTask(deptId) {
    openCreateTaskModal({ department_id: deptId });
}
function reviewConfirm(taskId) {
    window.location.href = '<?php echo $site_path; ?>/confirm-task?id=' + taskId;
}
function addRecurring() {
    openCreateTaskModal();
}
function editRecurring(id) {
    window.location.href = '<?php echo $site_path; ?>/edit-recurring-task?id=' + id;
}
function pauseRecurring(id) {
    if (confirm('Pause this recurring task?')) {
        window.location.href = '<?php echo $site_path; ?>/pause-recurring-task?id=' + id;
    }
}
$('#department_id').on('change', function () {

    $('.dept-section').addClass('d-none');

    let department = $(this).val();

    if (department === '4') {
        $('#design_fields').removeClass('d-none');
    }

    if (department === 'purchase') {
        $('#purchase_fields').removeClass('d-none');
    }

    if (department === 'production') {
        $('#production_fields').removeClass('d-none');
    }

    if (department === 'qc') {
        $('#qc_fields').removeClass('d-none');
    }

    if (department === 'dispatch') {
        $('#dispatch_fields').removeClass('d-none');
    }

});
$(document).on('click','.review-confirm-btn',function(){

    let taskId     = $(this).data('task-id');
    let title      = $(this).data('title');
    let staff      = $(this).data('staff');
    let department = $(this).data('department');
    let note       = $(this).data('note');

    $('#confirm_task_id').val(taskId);

    $('#confirm_task_title').html(title);

    $('#confirm_task_info').html(
        'T-' + taskId +
        ' • ' +
        staff +
        ' • ' +
        department
    );

    $('#staff_completion_note').html(note);

});

</script>