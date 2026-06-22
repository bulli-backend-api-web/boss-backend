<?php
include("config/database.php");
include("config/auth_check.php");
include("includes/sidemenu.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);

/* Staff Performance */

$sql = "SELECT u.id, u.name, COUNT(DISTINCT tm.id) total_tasks, SUM(CASE
            WHEN tm.status='Completed'
                 AND DATE(tm.completed_at) <= DATE(tm.deadline_time)
            THEN 1 ELSE 0
        END
    ) done_tasks,

    SUM(
        CASE
            WHEN tm.status!='Completed'
                 AND DATE(tm.deadline_time) >= CURDATE()
            THEN 1 ELSE 0
        END
    ) pending_tasks,

    SUM(
        CASE
            WHEN (
                tm.status!='Completed'
                AND DATE(tm.deadline_time) < CURDATE()
            )
            OR
            (
                tm.status='Completed'
                AND DATE(tm.completed_at) > DATE(tm.deadline_time)
            )
            THEN 1 ELSE 0
        END
    ) overdue_tasks

FROM user u INNER JOIN task_master tm ON FIND_IN_SET(u.id, tm.assigned_to) GROUP BY u.id ORDER BY u.name;";
$staff_res = mysqli_query($con, $sql);
?>

<style>
    .staff-card{
        background:#fff;
        border:1px solid #e9e3db;
        border-radius:20px;
        padding:24px;
        min-height:220px;
        transition:.3s;
    }

    .staff-card:hover{
        transform:translateY(-4px);
        box-shadow:0 10px 25px rgba(0,0,0,.08);
    }

    .staff-avatar{
        width:50px;
        height:50px;
        border-radius:50%;
        background:#eef2f8;
        color:#2962ff;
        font-size:20px;
        font-weight:700;
        display:flex;
        align-items:center;
        justify-content:center;
    }

    .staff-name{
        font-size:18px;
        font-weight:600;
        color:#181c32;
    }

    .staff-dept{
        font-size:14px;
        color:#7e8299;
    }

    .done-number{
        font-size:32px;
        font-weight:700;
        color:#198754;
    }

    .pending-number{
        font-size:32px;
        font-weight:700;
        color:#d97706;
    }

    .overdue-number{
        font-size:32px;
        font-weight:700;
        color:#dc3545;
    }

    .small-label{
        font-size:11px;
        letter-spacing:1px;
        color:#999;
        margin-top:5px;
    }

    .custom-progress{
        height:6px;
        border-radius:20px;
        background:#ebe5df;
    }

    .custom-progress .progress-bar{
        border-radius:20px;
    }

    .overdue-badge{
        background:#fdecec;
        color:#dc3545;
        border-radius:20px;
        padding:8px 15px;
        font-size:12px;
    }

    .clear-badge{
        background:#ebf9f0;
        color:#198754;
        border-radius:20px;
        padding:8px 15px;
        font-size:12px;
    }

    .completion-text{
        color:#8c8c8c;
        font-size:13px;
    }
</style>

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">

    <div class="d-flex flex-column flex-column-fluid">

        <!-- Toolbar -->
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div class="app-container container-fluid d-flex flex-stack">

                <div class="page-title d-flex flex-column justify-content-center me-3">

                    <h1 class="page-heading fw-bold fs-2">
                        Staff Performance
                    </h1>

                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item">
                            Dashboard
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">
                            Staff Report
                        </li>
                    </ul>

                </div>

            </div>
        </div>

        <!-- Content -->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div class="app-container container-fluid">
                <div class="row g-5">
                    <?php
                    while ($staff = mysqli_fetch_assoc($staff_res)) {

                        $done = (int) $staff['done_tasks'];
                        $pending = (int) $staff['pending_tasks'];
                        $overdue = (int) $staff['overdue_tasks'];

                        $total = $done + $pending + $overdue;

                        if ($total > 0) {
                            $completion = round(($done / $total) * 100);
                        } else {
                            $completion = 0;
                        }

                        if ($completion >= 90) {
                            $progressClass = "bg-success";
                        } elseif ($completion >= 60) {
                            $progressClass = "bg-warning";
                        } else {
                            $progressClass = "bg-danger";
                        }

                        $firstLetter = strtoupper(substr($staff['name'], 0, 1));
                        ?>

                        <div class="col-xl-4 col-lg-12">
                            <div class="staff-card">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="d-flex align-items-center">
                                        <div class="staff-avatar">
                                            <?php echo $firstLetter; ?>
                                        </div>
                                        <div class="ms-4">
                                            <div class="staff-name">
                                                <?php echo $staff['name']; ?>
                                            </div>
                                            <div class="staff-dept">
                                                <?php //echo $staff['department_name']; ?>
                                            </div>
                                        </div>
                                    </div>
                                        <?php if ($overdue > 0) { ?>
                                        <span class="badge overdue-badge">
                                            <?php echo $overdue; ?> overdue
                                        </span>

                                            <?php } else { ?>

                                        <span class="badge clear-badge">
                                            All clear
                                        </span>
                                        <?php } ?>
                                </div>
                                <div class="row mt-8 text-center">
                                    <div class="col-4">
                                        <div class="done-number">
                                            <?php echo $done; ?>
                                        </div>
                                        <div class="small-label">
                                            DONE
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="pending-number">
                                            <?php echo $pending; ?>
                                        </div>
                                        <div class="small-label">
                                            PENDING
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="overdue-number">
                                            <?php echo $overdue; ?>
                                        </div>
                                        <div class="small-label">
                                            OVERDUE
                                        </div>
                                    </div>
                                </div>
                                <div class="progress custom-progress mt-5">
                                    <div class="progress-bar <?php echo $progressClass; ?>"
                                         style="width:<?php echo $completion; ?>%">
                                    </div>
                                </div>
                                <div class="text-end mt-2 completion-text">
                                    <?php echo $completion; ?>% completion rate
                                </div>
                            </div>
                        </div>
                            <?php } ?>
                </div>
            </div>
        </div>
        <?php include("includes/footer.php"); ?>
    </div>
</div>

<script src="<?php echo $site_path; ?>/assets/plugins/global/plugins.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/scripts.bundle.js"></script>