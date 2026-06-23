<?php
include("config/database.php");
include("config/auth_check.php");
include("includes/sidemenu.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);

$stats_sql = "SELECT 
    COUNT(CASE WHEN status = 1 THEN 1 END) AS active_count,
    COUNT(CASE WHEN spent_budget > budget THEN 1 END) AS overbudget_count,
    ROUND(AVG(CASE WHEN budget > 0 THEN budget END), 2) AS avg_sample_cost,
    COUNT(CASE WHEN status = 0 
               AND MONTH(created_at) = MONTH(CURDATE()) 
               AND YEAR(created_at)  = YEAR(CURDATE()) THEN 1 END) AS approve_mtd
FROM sampling";

$stats_res = $con->query($stats_sql);
$active_count = 0;
$overbudget_count = 0;
$avg_sample_cost = 0;
$approve_mtd = 0;

if ($stats_res && $stats_res->num_rows > 0) {
    $row = $stats_res->fetch_assoc();
    $active_count = $row['active_count'];
    $overbudget_count = $row['overbudget_count'];
    $avg_sample_cost = $row['avg_sample_cost'];
    $approve_mtd = $row['approve_mtd'];
}
$status = 1;
$stmt = $con->prepare("SELECT COUNT(id) AS approve_count FROM design WHERE status = ?");
$stmt->bind_param('i', $status);
$stmt->execute();

$result = $stmt->get_result();
$design_row = $result->fetch_assoc();

$approve_count = $design_row['approve_count'];

$category_list = getCategoryList();

$material_options = '';
$res = mysqli_query($con, "SELECT id,material_name FROM material_master");
while ($row = mysqli_fetch_assoc($res)) {
    $material_options .= '<option value="' . $row['id'] . '">' . $row['material_name'] . '</option>';
}
?>
<style>
    .order-filter-toolbar{
        display:grid;
        grid-template-columns: 2.1fr 1.1fr 1.1fr 1fr 1fr 1fr 1fr 0.8fr 1fr;
        gap:14px;
        align-items:stretch;
        width:100%;
    }

    .order-filter-toolbar .filter-item{
        min-width:0;
    }

    .order-filter-toolbar .form-control,
    .order-filter-toolbar .form-select,
    .order-filter-toolbar .btn{
        height:48px;
        border-radius:12px;
        font-size:14px;
    }

    .order-filter-toolbar .btn{
        font-weight:600;
        box-shadow:none;
    }

    .order-filter-toolbar .btn-primary{
        min-width:100px;
    }

    .order-filter-toolbar .filter-search .form-control{
        padding-left:44px !important;
    }

    .order-filter-toolbar .select2-container{
        width:100% !important;
    }

    .order-filter-toolbar .select2-container .select2-selection--single{
        height:48px !important;
        border-radius:12px !important;
        display:flex !important;
        align-items:center !important;
        border:0 !important;
        background:#f5f8fa !important;
        padding:0 12px !important;
    }

    .order-filter-toolbar .select2-container--default .select2-selection--single .select2-selection__rendered{
        line-height:46px !important;
        color:#5e6278 !important;
        padding-left:0 !important;
    }

    .order-filter-toolbar .select2-container--default .select2-selection--single .select2-selection__arrow{
        height:46px !important;
        right:10px !important;
    }

    @media (max-width: 1600px){
        .order-filter-toolbar{
            grid-template-columns: repeat(4, minmax(180px, 1fr));
        }
    }

    @media (max-width: 992px){
        .order-filter-toolbar{
            grid-template-columns: repeat(2, minmax(180px, 1fr));
        }
    }

    @media (max-width: 576px){
        .order-filter-toolbar{
            grid-template-columns: 1fr;
        }
    }
</style>
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">

        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading text-gray-900 fw-bold fs-3 my-0">Sampling List</h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="<?php echo $site_path; ?>/pages/dashboard" class="text-muted text-hover-primary">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">Sampling List</li>
                    </ul>
                </div>
            </div>
        </div>

        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                <div class="card card-flush shadow-sm">
                    <div class="card mb-5">
                        <div class="card-body pt-5">
                            <div class="d-flex flex-nowrap overflow-auto gap-5 pb-3 text-uppercase">
                                <div class="min-w-175px p-5 rounded border border-gray-300 bg-light">
                                    <div class="text-gray-700 fw-semibold fs-6">Approve Design</div>
                                    <div class="fs-2hx fw-bold text-dark mt-2"><?= $approve_count ?? 0 ?></div>
                                </div>
                                <div class="min-w-175px p-5 rounded border border-gray-300 bg-light">
                                    <div class="text-gray-700 fw-semibold fs-6">Active samples</div>
                                    <div class="fs-2hx fw-bold text-dark mt-2"><?= $active_count ?? 0 ?></div>
                                </div>
                                <div class="min-w-175px p-5 rounded border border-gray-300 bg-light">
                                    <div class="text-gray-700 fw-semibold fs-6">Over budget</div>
                                    <div class="fs-2hx fw-bold text-dark mt-2"><?= $overbudget_count ?? 0 ?></div>
                                </div>
                                <div class="min-w-175px p-5 rounded border border-gray-300 bg-light">
                                    <div class="text-gray-700 fw-semibold fs-6">Avg sample cost</div>
                                    <div class="fs-2hx fw-bold text-dark mt-2"><?= '₹' . number_format($avg_sample_cost, 2) ?? 0 ?></div>
                                </div>
                                <div class="min-w-175px p-5 rounded border border-gray-300 bg-light">
                                    <div class="text-gray-700 fw-semibold fs-6">Approved MTD</div>
                                    <div class="fs-2hx fw-bold text-dark mt-2"><?= $approve_mtd ?? 0 ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-header border-0 pt-6">
                        <div class="card-title  w-100">
                            <div class="order-filter-toolbar">
                                <div class="filter-item filter-search">
                                    <i class="ki-outline ki-magnifier fs-3 position-absolute ms-5"></i>
                                    <input type="text" id="sample_search" class="form-control form-control-solid ps-12" placeholder="Search"/>
                                </div>

                                <div class="filter-item">
                                    <button class="btn btn-light d-flex align-items-center" id="daterangeBtn">
                                        <i class="ki-outline ki-calendar fs-3 me-2"></i>
                                        <span id="reportrange">All</span>
                                    </button>
                                </div>
                                <div class="filter-item">
                                    <select id="budget_filter" class="form-select form-select-solid" data-control="select2" data-hide-search="false" data-placeholder="Budget" data-kt-ecommerce-product-filter="Budget">
                                        <option></option>
                                        <option value="all">All</option>
                                        <option value="1000-2000">1000-2000</option>
                                        <option value="2000-3000">2000-3000</option>
                                    </select>
                                </div>
                                <div class="filter-item">
                                    <select id="category_filter" class="form-select form-select-solid" data-control="select2" data-hide-search="false" data-placeholder="Category" data-kt-ecommerce-product-filter="Category">
                                        <option value="all">All</option>
<?php if ($category_list) {
    foreach ($category_list as $single_cat) {
        ?>
                                                <option value="<?php echo $single_cat['id']; ?>"><?php echo $single_cat['name']; ?></option>
                                            <?php }
                                        } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="from_date" name="from_date">
                        <input type="hidden" id="to_date" name="to_date">
                    </div>

                    <div class="card-body pt-0">
                        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_sampling_table">
                            <thead>
                                <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                    <th class="min-w-50px">#</th>
                                    <th class="min-w-150px">Sample ID</th>
                                    <th class="min-w-150px">Design Code</th>
                                    <th class="min-w-150px">Category</th>
                                    <th class="min-w-150px">Sampler</th>
                                    <th class="min-w-150px">Timeline</th>
                                    <th class="min-w-100px">Fabric Issued</th>
                                    <th class="min-w-150px">Budget</th>
                                    <th class="min-w-150px">Spent</th>
                                    <th class="min-w-150px">Status</th>
                                    <th class="min-w-150px">Created Date</th>
                                    <th class="min-w-180px">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="fw-semibold text-gray-600"></tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
        <?php include("includes/footer.php"); ?>
    </div>
</div>
<div class="modal fade" id="startSamplingModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="startSamplingForm">
                <input type="hidden" name="sampling_id" id="sampling_id">
                <input type="hidden" name="action" value="start-sampling">

                <div class="modal-header">
                    <div>
                        <h3 class="modal-title mb-1">Start Sampling</h3>
                        <!-- Step indicator -->
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary" id="stepBadge1">Step 1 of 2 — Details</span>
                            <span class="badge bg-success d-none" id="stepBadge2">Step 2 of 2 — Spent Amount</span>
                        </div>
                    </div>
                    <div class="btn btn-sm btn-icon" data-bs-dismiss="modal">✕</div>
                </div>

                <div class="modal-body">

                    <!-- ══════════ STEP 1 ══════════ -->
                    <div id="samplingStep1">
                        <!-- Fabric -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold required">Fabric</label>
                                <select class="form-select" name="fabric_id" id="fabric_id" required>
                                    <option value="">Select Fabric</option>
                                    <?php
                                    $fabric = mysqli_query($con, "SELECT id, fabric_name FROM fabric_master ORDER BY fabric_name");
                                    while ($f = mysqli_fetch_assoc($fabric)) {
                                        ?>
                                        <option value="<?= $f['id']; ?>"><?= $f['fabric_name']; ?></option>
                                    <?php } ?>
                                </select>
                                <div class="invalid-feedback">Please select a fabric.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold required">Required (Meters)</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0.01"
                                           class="form-control" name="fabric_qty" id="fabric_qty"
                                           placeholder="e.g. 2.50" required/>
                                    <span class="input-group-text">Mtr</span>
                                </div>
                                <div class="invalid-feedback">Please enter fabric quantity in meters.</div>
                            </div>
                        </div>

                        <hr class="my-4"/>

                        <!-- Materials -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Materials</h5>
                            <button type="button" class="btn btn-primary btn-sm" id="addMaterialRow">
                                <i class="ki-duotone ki-plus fs-4"></i> Add Material
                            </button>
                        </div>
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Material</th>
                                    <th width="160">Qty</th>
                                    <th width="80" class="text-center">Remove</th>
                                </tr>
                            </thead>
                            <tbody id="materialRows">
                                <!-- rows added by JS -->
                            </tbody>
                        </table>
                        <p class="text-muted fs-7 mb-0">* Click "Add Material" to add materials used in this sampling.</p>
                    </div>
                    <!-- /STEP 1 -->

                    <!-- ══════════ STEP 2 ══════════ -->
                    <div id="samplingStep2" class="d-none">
                        <div class="text-center mb-5">
                            <span style="font-size:48px;">💰</span>
                            <h4 class="mt-3 mb-1">How much was spent?</h4>
                            <p class="text-muted fs-6">Enter the total amount spent on this sampling.</p>
                        </div>

                        <!-- Summary card -->
                        <div class="bg-light rounded p-4 mb-5">
                            <div class="row text-center">
                                <div class="col-6 border-end">
                                    <div class="fs-7 text-muted mb-1">Fabric</div>
                                    <div class="fw-bold" id="summaryFabric">—</div>
                                </div>
                                <div class="col-6">
                                    <div class="fs-7 text-muted mb-1">Required</div>
                                    <div class="fw-bold" id="summaryQty">—</div>
                                </div>
                            </div>
                        </div>

                        <!-- Spent amount -->
                        <div class="row justify-content-center">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold required">Total Spent Amount (₹)</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text">₹</span>
                                    <input type="number" step="0.01" min="0"
                                           class="form-control" name="spent_amount" id="spent_amount"
                                           placeholder="0.00"/>
                                </div>
                                <div class="form-text text-muted">Leave 0 if not calculated yet.</div>
                            </div>
                        </div>

                        <!-- Materials summary -->
                        <div id="materialSummaryWrap" class="mt-4 d-none">
                            <h6 class="fw-semibold mb-2">Materials Summary</h6>
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr><th>Material</th><th>Qty</th></tr>
                                </thead>
                                <tbody id="materialSummaryBody"></tbody>
                            </table>
                        </div>
                    </div>
                    <!-- /STEP 2 -->

                </div>

                <div class="modal-footer" id="samplingModalFooter">
                    <!-- Step 1 footer -->
                    <div id="footerStep1" class="w-100 d-flex justify-content-between">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="btnNextStep">
                            Next: Enter Spent Amount <i class="ki-duotone ki-arrow-right fs-4 ms-1"></i>
                        </button>
                    </div>
                    <!-- Step 2 footer -->
                    <div id="footerStep2" class="w-100 d-flex justify-content-between d-none">
                        <button type="button" class="btn btn-light" id="btnBackStep">
                            <i class="ki-duotone ki-arrow-left fs-4 me-1"></i> Back
                        </button>
                        <button type="submit" class="btn btn-success" id="btnFinalSubmit">
                            <span class="indicator-label">
                                <i class="ki-duotone ki-check fs-4 me-1"></i> Confirm & Start Sampling
                            </span>
                            <span class="indicator-progress">
                                Saving... <span class="spinner-border spinner-border-sm ms-2"></span>
                            </span>
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
<script src="<?php echo $site_path; ?>/assets/plugins/global/plugins.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/scripts.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/custom/apps/user-management/users/list/table.js?v=<?php echo time(); ?>"></script>

<script>
    var table = $('#kt_sampling_table').DataTable({
    processing: true,
            serverSide: true,
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            ajax: {
            url: "<?php echo $site_path; ?>/ajax/fetch-sampling-list",
                    type: "POST",
                    data: function (d) {
                    d.budget = $('#budget_filter').val();
                            d.category = $('#category_filter').val();
                            d.from_date = $("#from_date").val();
                            d.to_date = $("#to_date").val();
                    }
            },
            columns: [
            {data: 'sr_no' },
            {data: 'sample_id', orderable: false },
            {data: 'design_code', orderable: false },
            {data: 'category' },
            {data: 'sampler'},
            {data: 'timeline'},
            {data: 'fabric_issued'},
            {data: 'budget'},
            {data: 'spent'},
            {data: 'status'},
            {data: 'created_date', orderable: false },
            {data: 'actions', orderable: false, searchable: false }
            ],
            order: [[0, 'desc']],
            drawCallback: function () {
            if (typeof KTMenu !== 'undefined') {
            KTMenu.createInstances();
            }
            }
    });
            const pickerEl = $('#daterangeBtn');
            const displayEl = pickerEl.find('div.text-gray-600');
            $(pickerEl).daterangepicker({
    pens: 'left',
            autoUpdateInput: false,
            startDate: moment().startOf('month'),
            endDate: moment().endOf('month'),
            locale: {format: 'YYYY-MM-DD'},
            ranges: {
            'All': [null, null],
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
    }, function (start, end, label) {
    if (label === 'All') {
    $('#from_date').val('');
            $('#to_date').val('');
            $('#reportrange').text('All');
    } else {
    $('#from_date').val(start.format('YYYY-MM-DD'));
            $('#to_date').val(end.format('YYYY-MM-DD'));
            $('#reportrange').text(
            start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY')
            );
    }

    // reload DataTable
    table.ajax.reload();
    });
            $('#from_date').val('');
            $('#to_date').val('');
            displayEl.text('All');
            $('#sample_search').on('keyup', function () {
    table.search(this.value).draw();
    });
            $('#budget_filter').on('change', function () {
    table.ajax.reload();
    });
            $('#category_filter').on('change', function () {
    table.ajax.reload();
    });
            $(document).on('click', '.action_start_sampling', function(e){
    e.preventDefault();
            let id = $(this).data('id');
            $('#sampling_id').val(id);
            $('#startSamplingModal').modal('show');
    });
            var materialOptions = <?php
                                    $mats = mysqli_query($con, "SELECT id, material_name FROM material_master ORDER BY material_name");
                                    $arr = [];
                                    while ($m = mysqli_fetch_assoc($mats))
                                        $arr[] = $m;
                                    echo json_encode($arr);
                                    ?>;
            function materialSelectHtml() {
            var opts = '<option value="">Select Material</option>';
                    materialOptions.forEach(function(m) {
                    opts += '<option value="' + m.id + '">' + m.material_name + '</option>';
                    });
                    return opts;
            }

    $(document).on('click', '#addMaterialRow', function () {
    var row = `
        <tr>
            <td>
                <select class="form-select form-select-sm material-select" name="materials[]" required>
                    ${materialSelectHtml()}
                </select>
            </td>
            <td>
                <input type="number" step="0.01" min="0.01"
                    class="form-control form-control-sm" name="material_qty[]"
                    placeholder="Qty" required/>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-icon btn-sm btn-light-danger removeMaterialRow">
                    <i class="ki-duotone ki-trash fs-5"></i>
                </button>
            </td>
        </tr>`;
            $('#materialRows').append(row);
            });
            $(document).on('click', '.removeMaterialRow', function () {
    $(this).closest('tr').remove();
            });
            // ── Reset modal on close ──
            $('#startSamplingModal').on('hidden.bs.modal', function () {
    document.getElementById('startSamplingForm').reset();
            $('#materialRows').empty();
            goToStep(1);
            });
// ── Step navigation ──
            function goToStep(step) {
            if (step === 1) {
            $('#samplingStep1').removeClass('d-none');
                    $('#samplingStep2').addClass('d-none');
                    $('#footerStep1').removeClass('d-none');
                    $('#footerStep2').addClass('d-none');
                    $('#stepBadge1').removeClass('d-none');
                    $('#stepBadge2').addClass('d-none');
            } else {
            $('#samplingStep1').addClass('d-none');
                    $('#samplingStep2').removeClass('d-none');
                    $('#footerStep1').addClass('d-none');
                    $('#footerStep2').removeClass('d-none');
                    $('#stepBadge1').addClass('d-none');
                    $('#stepBadge2').removeClass('d-none');
            }
            }

// ── Next button — validate Step 1 then go to Step 2 ──
    $('#btnNextStep').on('click', function () {
    var fabricId = $('#fabric_id').val();
            var fabricQty = $('#fabric_qty').val();
            var valid = true;
            // Validate fabric
            if (!fabricId) {
    $('#fabric_id').addClass('is-invalid');
            valid = false;
    } else {
    $('#fabric_id').removeClass('is-invalid');
    }

    // Validate qty
    if (!fabricQty || parseFloat(fabricQty) <= 0) {
    $('#fabric_qty').addClass('is-invalid');
            valid = false;
    } else {
    $('#fabric_qty').removeClass('is-invalid');
    }

    if (!valid) return;
            // Build summary for Step 2
            var fabricText = $('#fabric_id option:selected').text();
            $('#summaryFabric').text(fabricText);
            $('#summaryQty').text(parseFloat(fabricQty).toFixed(2) + ' Mtr');
            // Build materials summary table
            var rows = $('#materialRows tr');
            if (rows.length > 0) {
    var html = '';
            rows.each(function () {
            var matName = $(this).find('.material-select option:selected').text();
                    var matQty = $(this).find('input[name="material_qty[]"]').val();
                    if (matName && matName !== 'Select Material') {
            html += '<tr><td>' + matName + '</td><td>' + (matQty || '—') + '</td></tr>';
            }
            });
            if (html) {
    $('#materialSummaryBody').html(html);
            $('#materialSummaryWrap').removeClass('d-none');
    } else {
    $('#materialSummaryWrap').addClass('d-none');
    }
    } else {
    $('#materialSummaryWrap').addClass('d-none');
    }

    goToStep(2);
            $('#spent_amount').focus();
            });
// ── Back button ──
            $('#btnBackStep').on('click', function () {
    goToStep(1);
            });
// ── Final submit ──
            $('#startSamplingForm').on('submit', function (e) {
    e.preventDefault();
            var btn = document.getElementById('btnFinalSubmit');
            var formData = new FormData(this);
            btn.setAttribute('data-kt-indicator', 'on');
            btn.disabled = true;
            $.ajax({
            url: '<?php echo $site_path; ?>/ajax/start-sampling',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function (res) {
                    btn.removeAttribute('data-kt-indicator');
                            btn.disabled = false;
                            if (res.status === 'success') {
                    bootstrap.Modal.getInstance(
                            document.getElementById('startSamplingModal')
                            ).hide();
                            Swal.fire({
                            icon: 'success',
                                    title: 'Sampling Started!',
                                    text: res.message || 'Sampling has been started successfully.',
                                    buttonsStyling: false,
                                    confirmButtonText: 'OK',
                                    customClass: { confirmButton: 'btn btn-primary' }
                            }).then(function () {
                    location.reload();
                    });
                    } else {
                    Swal.fire({
                    icon: 'error',
                            title: 'Error',
                            text: res.message || 'Something went wrong.',
                            buttonsStyling: false,
                            confirmButtonText: 'OK',
                            customClass: { confirmButton: 'btn btn-primary' }
                    });
                    }
                    },
                    error: function () {
                    btn.removeAttribute('data-kt-indicator');
                            btn.disabled = false;
                            Swal.fire({
                            icon: 'error',
                                    title: 'Server Error',
                                    text: 'Please try again.',
                                    buttonsStyling: false,
                                    confirmButtonText: 'OK',
                                    customClass: { confirmButton: 'btn btn-danger' }
                            });
                    }
            });
            });
</script>