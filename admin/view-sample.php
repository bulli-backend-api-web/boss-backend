<?php
include("config/database.php");
include("config/auth_check.php");
include("includes/sidemenu.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);

$id = my_simple_crypt($_GET['id'], 'decrypt_1');

$sql = "SELECT s.*, u.name as assign_to_name, ab.name as assign_by_name,c.name as cat_name,
        DATEDIFF(NOW(), s.created_at) AS days_elapsed,
        DATE_ADD(s.created_at, INTERVAL s.target_days DAY) AS due_date,
        ROUND(LEAST((DATEDIFF(NOW(), s.created_at) / NULLIF(s.target_days,0)) * 100, 100)) AS progress_pct,
        CASE WHEN s.status=1 THEN 'Pending'
             WHEN s.status=2 THEN 'Approved'
             WHEN s.status=3 THEN 'Rejected'
             WHEN s.status=4 THEN 'Rework'
             WHEN s.status=5 THEN 'In Review'
        END AS status_text
        FROM sampling s
        LEFT JOIN user u  ON u.id = s.assign_to
        LEFT JOIN user ab ON ab.id = s.assign_by
        LEFT JOIN category c on c.id = s.category
        WHERE s.id = ?";

$stmt = $con->prepare($sql);
$stmt->bind_param('i', $id);

if ($stmt->execute()) {
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $r = $row;
} else {
    die($stmt->error);
}

$steps = ['Brief rcvd', 'Fabric issued', 'Stitching', 'Finishing', 'QC', 'Approved'];
$curStep = 4; // dynamic from DB
$pct = $r['progress_pct'];
$barColor = $pct < 75 ? 'success' : ($pct < 100 ? 'warning' : 'danger');

$status =1;
$qc_sql = "SELECT name, status FROM qc_checklist WHERE status = ? ORDER BY id ASC";
$qc_stmt = $con->prepare($qc_sql);
$qc_stmt->bind_param('i', $status);
$qc_stmt->execute();
$qc_items = $qc_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

?>
<style>
    .sample-progress{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    position:relative;
}

.progress-step{
    flex:1;
    position:relative;
    text-align:center;
}

.progress-step .circle{
    width:48px;
    height:48px;
    border-radius:50%;
    background:#f1f1f4;
    color:#7e8299;
    font-weight:700;
    display:flex;
    align-items:center;
    justify-content:center;
    margin:0 auto;
    position:relative;
    z-index:2;
}

.progress-step.completed .circle{
    background:#22c55e;
    color:#fff;
}

.progress-step.active .circle{
    background:#6d4c41;
    color:#fff;
}

.progress-step .line{
    position:absolute;
    top:24px;
    left:55%;
    width:90%;
    height:3px;
    background:#e4e6ef;
    z-index:1;
}

.progress-step.completed .line{
    background:#22c55e;
}

.progress-step .label{
    margin-top:12px;
    font-size:13px;
    font-weight:600;
    color:#7e8299;
}

.progress-step.active .label{
    color:#181c32;
}

@media(max-width:768px){
    .sample-progress{
        overflow-x:auto;
        gap:20px;
    }

    .progress-step{
        min-width:120px;
    }
}
</style>
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading text-gray-900 fw-bold fs-3 my-0">Design</h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="<?php echo $site_path; ?>/pages/dashboard" class="text-muted text-hover-primary">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">Design List</li>
                    </ul>
                </div>
            </div>
        </div>
        <div id="kt_app_content" class="app-content flex-column-fluid" style="padding-top:0;">
        <div class="toolbar py-3 py-lg-6">
            <div class="container-xxl">
                <div class="d-flex align-items-center flex-wrap gap-3">
                    <h1 class="fw-bold fs-2 text-gray-900 mb-0">
                        <?= htmlspecialchars($r['name']) ?>
                    </h1>
                    <span class="badge badge-light-success">
                        <?= $r['status_text'] ?>
                    </span>
                    <span class="badge badge-light-primary">
                        <?= $r['cat_name'] ?>
                    </span>
                </div>
            </div>
        </div>
        <!-- Content -->
        <div class="container-xxl">
            <div class="row g-5">

                <!-- LEFT COLUMN -->
                <div class="col-xl-8">

                    <!-- Sample Progress -->
                    <div class="card card-flush mb-5">
                        <div class="card-header">
                            <h3 class="card-title fw-bold text-uppercase text-muted fs-7">
                                Sample Progress
                            </h3>
                        </div>

                        <div class="card-body py-10">
                            <div class="sample-progress">
                                <?php foreach($steps as $i => $step):
                                    $stepNo = $i + 1;

                                    if($stepNo < $curStep){
                                        $class = 'completed';
                                    }elseif($stepNo == $curStep){
                                        $class = 'active';
                                    }else{
                                        $class = '';
                                    }
                                ?>
                                <div class="progress-step <?= $class ?>">
                                    <div class="circle">
                                        <?php if($stepNo < $curStep): ?>
                                            <i class="ki-outline ki-check fs-3" style="color:#fff !important;"></i>
                                        <?php else: ?>
                                            <?= $stepNo ?>
                                        <?php endif; ?>
                                    </div>

                                    <div class="label">
                                        <?= $step ?>
                                    </div>

                                    <?php if($stepNo < count($steps)): ?>
                                        <div class="line"></div>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Details -->
                    <div class="card card-flush mb-5">
                        <div class="card-header">
                            <h3 class="card-title fw-bold text-uppercase text-muted fs-7">
                                <i class="ki-outline ki-information fs-4 me-2"></i>Details
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-row-bordered mb-0">
                                <tr><td class="text-muted fw-semibold py-3 ps-7">Sample ID</td>
                                    <td class="text-end text-primary fw-bold pe-7"><?= $r['sample_code'] ?></td></tr>
                                <tr><td class="text-muted fw-semibold py-3 ps-7">Sampler</td>
                                    <td class="text-end fw-semibold pe-7"><?= $r['assign_to_name'] ?></td></tr>
                                <tr><td class="text-muted fw-semibold py-3 ps-7">Category</td>
                                    <td class="text-end fw-semibold pe-7"><?= $r['cat_name'] ?></td></tr>
                                <tr>
                                    <td class="text-muted fw-semibold py-3 ps-7">Timeline</td>
                                    <td class="text-end pe-7">
                                        <span class="fw-bold text-<?= $barColor ?>"><?= $r['days_elapsed'] ?> / <?= $r['target_days'] ?> days</span>
                                        <div class="progress h-5px mt-2">
                                            <div class="progress-bar bg-<?= $barColor ?>" style="width:<?= $pct ?>%"></div>
                                        </div>
                                    </td>
                                </tr>
                                <tr><td class="text-muted fw-semibold py-3 ps-7">Fabric issued</td>
                                    <td class="text-end fw-semibold pe-7"><?= $r['fabric'] ?></td></tr>
                            </table>
                        </div>
                    </div>

                    <!-- Cost vs Budget -->
                    <div class="card card-flush mb-5">
                        <div class="card-header">
                            <h3 class="card-title fw-bold text-uppercase text-muted fs-7">
                                <i class="ki-outline ki-dollar fs-4 me-2"></i>Cost vs Budget
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-row-bordered mb-0">
                                <tr><td class="text-muted fw-semibold py-3 ps-7">Budget</td>
                                    <td class="text-end fw-bold pe-7">₹<?= number_format($r['budget'], 2) ?></td></tr>
                                <tr><td class="text-muted fw-semibold py-3 ps-7">Spent</td>
                                    <td class="text-end fw-bold text-<?= empty($r['spent_budget']) ? 'muted fst-italic' : 'success' ?> pe-7">
<?= empty($r['spent_budget']) ? 'Not entered yet' : '₹' . number_format($r['spent_budget'], 2) ?>
                                    </td></tr>
                            </table>
                            <div class="px-7 pb-5">
                                <div class="progress h-6px mt-3">
<?php $spent_pct = $r['budget'] > 0 && !empty($r['spent_budget']) ? round(($r['spent_budget'] / $r['budget']) * 100) : 0 ?>
                                    <div class="progress-bar bg-<?= $spent_pct > 100 ? 'danger' : 'success' ?>" style="width:<?= min($spent_pct, 100) ?>%"></div>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <span class="fs-8 text-muted">₹0</span>
                                    <span class="fs-8 text-muted"><?= $spent_pct ?>% of budget</span>
                                    <span class="fs-8 text-muted">₹<?= number_format($r['budget'], 2) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="card card-flush">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-6">
                                    <a href="#" class="btn btn-light-success w-100 sample-action-btn" data-action="approve" data-id="<?= htmlspecialchars(my_simple_crypt($id,'encrypt_1')) ?>">
                                        <i class="ki-outline ki-check fs-4 me-1"></i>Approve
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="#" class="btn btn-light-warning w-100" data-bs-toggle="modal" data-bs-target="#reworkModal" data-id="<?= htmlspecialchars(my_simple_crypt($id,'encrypt_1')) ?>">
                                        <i class="ki-outline ki-arrows-loop fs-4 me-1"></i>Rework
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="#" class="btn btn-light-danger w-100 sample-action-btn" data-action="reject" data-id="<?= htmlspecialchars(my_simple_crypt($id,'encrypt_1')) ?>">
                                        <i class="ki-outline ki-cross fs-4 me-1"></i>Reject
                                    </a>
                                </div>
                                <div class="col-6"><a href="#" class="btn btn-light-primary w-100"><i class="ki-outline ki-dollar fs-4 me-1"></i>Update cost</a></div>
                            </div>
                        </div>
                    </div>

                    <!-- Rework Modal -->
                    <div class="modal fade" id="reworkModal" tabindex="-1" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                          <form id="reworkForm">
                            <div class="modal-header">
                              <h5 class="modal-title">Send for Rework</h5>
                              <button type="button" class="btn-icon btn-sm" data-bs-dismiss="modal" aria-label="Close">
                                  <i class="ki-outline ki-cross fs-2"></i>
                              </button>
                            </div>
                            <div class="modal-body">
                              <label class="form-label fw-semibold required">Rework Note</label>
                              <textarea class="form-control" id="reworkNote" name="note" rows="4" placeholder="Describe what needs to be reworked..."></textarea>
                              <div class="text-danger fs-7 mt-1 d-none" id="reworkNoteError">Note is required.</div>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                              <button type="submit" class="btn btn-warning" data-kt-indicator="off">
                                  <span class="indicator-label">Submit</span>
                                  <span class="indicator-progress">Please wait...
                                      <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                  </span>
                              </button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                </div>
                <!-- RIGHT COLUMN -->
                <div class="col-xl-4">
                    <!-- Assignment Card -->
                    <div class="card card-flush mb-5">
                        <div class="card-header">
                            <h3 class="card-title fw-bold text-uppercase text-muted fs-7">Assignment</h3>
                        </div>
                        <div class="card-body p-0">
                            <?php
                            $chips = [
                                ['icon' => 'ki-user', 'color' => 'primary', 'label' => 'Assigned to', 'val' => $r['assign_to_name']],
                                ['icon' => 'ki-user-tick', 'color' => 'success', 'label' => 'Assigned by', 'val' => $r['assign_by_name']],
                                ['icon' => 'ki-calendar', 'color' => 'warning', 'label' => 'Created on', 'val' => date('d M Y', strtotime($r['created_at']))],
                                ['icon' => 'ki-calendar-tick', 'color' => 'danger', 'label' => 'Due date', 'val' => date('d M Y', strtotime($r['due_date']))],
                            ];
                            foreach ($chips as $c):
                                ?>
                                <div class="d-flex align-items-center px-7 py-4 border-bottom">
                                    <div class="symbol symbol-35px me-4">
                                        <span class="symbol-label bg-light-<?= $c['color'] ?>">
                                            <i class="ki-outline <?= $c['icon'] ?> fs-4 text-<?= $c['color'] ?>"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <div class="fs-8 text-muted"><?= $c['label'] ?></div>
                                        <div class="fw-semibold fs-7"><?= $c['val'] ?></div>
                                    </div>
                                </div>
                            <?php endforeach ?>
                        </div>
                        <div class="card card-flush mb-5">
                            <div class="card-header">
                                <h3 class="card-title fw-bold text-uppercase text-muted fs-7">
                                    <i class="ki-outline ki-shield-tick fs-4 me-2"></i>QC Checklist
                                </h3>
                            </div>
                            <div class="card-body p-0">
                                <?php if (empty($qc_items)): ?>
                                    <div class="text-muted fs-7 px-7 py-5">No QC checklist recorded yet.</div>
                                <?php else: foreach ($qc_items as $item):
                                    $status = $item['status']; // 1 = pass, 0 = fail, NULL = pending
                                ?>
                                    <div class="d-flex align-items-center justify-content-between px-7 py-4 border-bottom">
                                        <div class="fw-semibold fs-7 text-gray-800"><?= htmlspecialchars($item['name']) ?></div>
                                        <div class="d-flex gap-2">
                                            <span class="btn btn-icon btn-sm rounded-circle <?= $status == 1 ? 'btn-success' : 'btn-light-success' ?>">
                                                <i class="ki-outline ki-check fs-5"></i>
                                            </span>
                                            <span class="btn btn-icon btn-sm rounded-circle <?= $status == 0 ? 'btn-danger' : 'btn-light-danger' ?>">
                                                <i class="ki-outline ki-cross fs-5"></i>
                                            </span>
                                        </div>
                                    </div>
                                <?php endforeach; endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include("includes/footer.php"); ?>
        </div>
        
    </div>
</div>
<script src="<?php echo $site_path; ?>/assets/plugins/global/plugins.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/scripts.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/custom/apps/user-management/users/list/table.js?v=<?php echo time(); ?>"></script>
<script>
$(document).ready(function () {
    let currentSampleId = null;

    // Approve / Reject
    $('.sample-action-btn').on('click', function (e) {
        e.preventDefault();
        const action = $(this).data('action');
        const id = $(this).data('id');

        if (!confirm('Are you sure you want to ' + action + ' this sample?')) return;

        $.ajax({
            url: '<?php echo $site_path; ?>/ajax/update_sampling_status',
            method: 'POST',
            data: { id: id, action: action },
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    alert(res.message);
                    location.reload();
                } else {
                    alert(res.message || 'Something went wrong.');
                }
            },
            error: function () {
                alert('Request failed. Please try again.');
            }
        });
    });

    // Capture which sample the rework modal was opened for
    $(document).on('click', '[data-bs-target="#reworkModal"]', function () {
        currentSampleId = $(this).data('id');
        $('#reworkNote').val('');
        $('#reworkNoteError').addClass('d-none');
    });

    // Rework submit — note is mandatory
    $('#reworkForm').on('submit', function (e) {
        e.preventDefault();
        const note = $('#reworkNote').val().trim();

        if (note === '') {
            $('#reworkNoteError').removeClass('d-none');
            return;
        }
        $('#reworkNoteError').addClass('d-none');

        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.attr('data-kt-indicator', 'on').prop('disabled', true);

        $.ajax({
            url: '<?php echo $site_path; ?>/ajax/update_sampling_status',
            method: 'POST',
            data: { id: currentSampleId, action: 'rework', note: note },
            dataType: 'json',
            success: function (res) {
                submitBtn.attr('data-kt-indicator', 'off').prop('disabled', false);
                if (res.success) {
                    alert(res.message);
                    $('#reworkModal').modal('hide');
                    location.reload();
                } else {
                    alert(res.message || 'Something went wrong.');
                }
            },
            error: function () {
                submitBtn.attr('data-kt-indicator', 'off').prop('disabled', false);
                alert('Request failed. Please try again.');
            }
        });
    });
});
</script>