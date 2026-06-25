<?php
include("config/database.php");
include("config/auth_check.php");
include("includes/sidemenu.php");

error_reporting(E_ALL);
ini_set('display_errors', 0);

$id = my_simple_crypt($_GET['id'], 'decrypt_1');

$sql = "SELECT d.*, u.name as assign_to_name,c.name as cat_name,
        CASE WHEN d.status=0 THEN 'Pending'
             WHEN d.status=1 THEN 'Approved'
             WHEN d.status=2 THEN 'Rejected'
             WHEN d.status=3 THEN 'Revision'
             WHEN d.status=4 THEN 'In Review'
        END AS status_text
        FROM design d
        LEFT JOIN user u  ON u.id = d.assign_to
        LEFT JOIN category c on c.id = d.style
        WHERE d.id = ?";

$stmt = $con->prepare($sql);
$stmt->bind_param('i', $id);

if ($stmt->execute()) {
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $r = $row;
} else {
    die($stmt->error);
}
$revision = [];
$revision_Sql = "select note from design_notes where design_id = ?";
$stmt1 = $con->prepare($revision_Sql);
$stmt1->bind_param('i', $id);
if ($stmt1->execute()) {
    $revision_res = $stmt1->get_result();
    while($revision_row = $revision_res->fetch_assoc()){
        $revision[] = $revision_row;
    }
}
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
                        <?= htmlspecialchars($r['design_name']) ?>
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
                <div class="col-xl-12">
                    <div class="card card-flush mb-5">
                        <div class="card-header">
                            <h3 class="card-title fw-bold text-uppercase text-muted fs-7">
                                <i class="ki-outline ki-information fs-4 me-2"></i>Details
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-row-bordered mb-0">
                                <tr>
                                    <td class="text-muted fw-semibold py-3 ps-7">Design Name</td>
                                    <td class="text-end text-primary fw-bold pe-7"><?= $r['design_name'] ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-semibold py-3 ps-7">Design Code</td>
                                    <td class="text-end text-primary fw-bold pe-7"><?= $r['design_code'] ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-semibold py-3 ps-7">Designer</td>
                                    <td class="text-end fw-semibold pe-7"><?= $r['assign_to_name'] ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-semibold py-3 ps-7">Category</td>
                                    <td class="text-end fw-semibold pe-7"><?= $r['cat_name'] ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-semibold py-3 ps-7">Occasion</td>
                                    <td class="text-end fw-semibold pe-7"><?= $r['occasion'] ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-semibold py-3 ps-7">Budget</td>
                                    <td class="text-end fw-semibold pe-7"><?= $r['budget'] ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-semibold py-3 ps-7">Revision</td>
                                    <td class="text-end fw-semibold pe-7"><?php if($revision) { echo count($revision); } else { echo "0";} ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-semibold py-3 ps-7">Due</td>
                                    <td class="text-end fw-semibold pe-7"><?php echo $r['due_date']; ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Cost vs Budget -->
                    <div class="card card-flush mb-5">
                        <div class="card-header">
                            <h3 class="card-title fw-bold text-uppercase text-muted fs-7">
                                <i class="ki-outline ki-dollar fs-4 me-2"></i>Revision history
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <?php if($revision){
                                    foreach($revision as $single_rev){?>
                                        <div style="text-align : justify;padding:12px;border: 1px solid #d4d8d4;margin:13px;border-radius : 15px;">
                                            <?php echo $single_rev['note']; ?>
                                        </div>
                                    <?php }
                            } ?>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="card card-flush">
                        <div class="card-body">
                            
                            <div class="row g-3">
                                <?php if($row['status'] == 0){ ?>
                                <div class="col-4">
                                    <a href="#" class="btn btn-light-success w-100 sample-action-btn" data-action="approve" data-id="<?= htmlspecialchars(my_simple_crypt($id,'encrypt_1')) ?>">
                                        <i class="ki-outline ki-check fs-4 me-1"></i>Approve - Send For Sampling
                                    </a>
                                </div>
                                <div class="col-4">
                                    <a href="#" class="btn btn-light-warning w-100" data-bs-toggle="modal" data-bs-target="#reworkModal" data-id="<?= htmlspecialchars(my_simple_crypt($id,'encrypt_1')) ?>">
                                        <i class="ki-outline ki-arrows-loop fs-4 me-1"></i>Request Revision With Feedback
                                    </a>
                                </div>
                                <div class="col-4">
                                    <a href="#" class="btn btn-light-danger w-100 sample-action-btn" data-action="reject" data-id="<?= htmlspecialchars(my_simple_crypt($id,'encrypt_1')) ?>">
                                        <i class="ki-outline ki-cross fs-4 me-1"></i>Reject
                                    </a>
                                </div>
                                <?php } else if($row['status'] == 3){?>
                                <div class="col-4">
                                    <a href="#" class="btn btn-light-danger w-100 sample-action-btn" data-action="escalate" data-id="<?= htmlspecialchars(my_simple_crypt($id,'encrypt_1')) ?>">
                                        <i class="ki-outline ki-check fs-4 me-1"></i>Escalate to design Head
                                    </a>
                                </div>
                                <div class="col-4">
                                    <a href="#" class="btn btn-light-warning w-100 sample-action-btn" data-action="reassign" data-id="<?= htmlspecialchars(my_simple_crypt($id,'encrypt_1')) ?>">
                                        <i class="ki-outline ki-check fs-4 me-1"></i>Reassign to another designer
                                    </a>
                                </div>
                            <?php }?>
                            </div>
                            
                        </div>
                    </div>

                    <!-- Rework Modal -->
                    <div class="modal fade" id="reworkModal" tabindex="-1" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                          <form id="reworkForm">
                            <div class="modal-header">
                              <h5 class="modal-title">Revision Note</h5>
                              <button type="button" class="btn-icon btn-sm" data-bs-dismiss="modal" aria-label="Close">
                                  <i class="ki-outline ki-cross fs-2"></i>
                              </button>
                            </div>
                            <div class="modal-body">
                              <label class="form-label fw-semibold required">Note</label>
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
    let currentDesignId = null;

    // Approve / Reject
    $('.sample-action-btn').on('click', function (e) {
        e.preventDefault();
        const action = $(this).data('action');
        const id = $(this).data('id');

        if (!confirm('Are you sure you want to ' + action + ' this Design?')) return;

        $.ajax({
            url: '<?php echo $site_path; ?>/ajax/update_design_status',
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
        currentDesignId = $(this).data('id');
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
            url: '<?php echo $site_path; ?>/ajax/update_design_status',
            method: 'POST',
            data: { id: currentDesignId, action: 'rework', note: note },
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