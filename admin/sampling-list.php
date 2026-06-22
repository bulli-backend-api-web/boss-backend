<?php
include("config/database.php");
include("config/auth_check.php");
include("includes/sidemenu.php");

$stats_sql = "SELECT 
    COUNT(CASE WHEN status = 1 THEN 1 END) AS active_count,
    COUNT(CASE WHEN spent_budget > budget THEN 1 END) AS overbudget_count,
    ROUND(AVG(CASE WHEN budget > 0 THEN budget END), 2) AS avg_sample_cost,
    COUNT(CASE WHEN status = 0 
               AND MONTH(created_at) = MONTH(CURDATE()) 
               AND YEAR(created_at)  = YEAR(CURDATE()) THEN 1 END) AS approve_mtd
FROM sampling";

$stats_res = $con->query($stats_sql);
$active_count     = 0;
$overbudget_count = 0;
$avg_sample_cost  = 0;
$approve_mtd      = 0;

if ($stats_res && $stats_res->num_rows > 0) {
    $row              = $stats_res->fetch_assoc();
    $active_count     = $row['active_count'];
    $overbudget_count = $row['overbudget_count'];
    $avg_sample_cost  = $row['avg_sample_cost'];
    $approve_mtd      = $row['approve_mtd'];
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
$res = mysqli_query($con,"SELECT id,material_name FROM material_master");
while($row = mysqli_fetch_assoc($res)){
    $material_options .=
    '<option value="'.$row['id'].'">'.$row['material_name'].'</option>';
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
                                    <div class="fs-2hx fw-bold text-dark mt-2"><?= '₹' . number_format($avg_sample_cost,2) ?? 0 ?></div>
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
                                        <?php if($category_list){
                                                foreach($category_list as $single_cat){?>
                                                    <option value="<?php echo $single_cat['id']; ?>"><?php echo $single_cat['name']; ?></option>
                                                <?php } } ?>
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
                <div class="modal-header">
                    <h3 class="modal-title">Start Sampling</h3>
                    <div class="btn btn-sm btn-icon" data-bs-dismiss="modal">
                        ✕
                    </div>
                </div>

                <div class="modal-body">
                    <div class="row mb-5">
                        <div class="col-md-6">
                            <label class="form-label">Fabric</label>
                            <select class="form-select" name="fabric_id" required>
                                <option value="">Select Fabric</option>
                                <?php
                                $fabric = mysqli_query($con,"SELECT id,fabric_name FROM fabric_master ORDER BY fabric_name");
                                while($f = mysqli_fetch_assoc($fabric)){
                                ?>
                                    <option value="<?= $f['id']; ?>">
                                        <?= $f['fabric_name']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Required Qty</label>
                            <input type="number" step="0.01" class="form-control" name="fabric_qty" required>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <h4>Materials</h4>
                        <button type="button" class="btn btn-primary btn-sm" id="addMaterialRow">
                            Add Material
                        </button>
                    </div>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Material</th>
                                <th width="150">Qty</th>
                                <th width="100">Action</th>
                            </tr>
                        </thead>
                        <tbody id="materialRows"></tbody>
                    </table>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"> Cancel</button>
                    <button type="submit" class="btn btn-success"> Start Sampling</button>
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
    $(document).ready(function () {
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
            {data: 'actions',orderable: false, searchable: false }
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

        $(document).on('click','.action_start_sampling',function(e){
            e.preventDefault();
            let id = $(this).data('id');
            $('#sampling_id').val(id);
            $('#startSamplingModal').modal('show');
        });

        var materialOptions = `<?= $material_options; ?>`;

        $('#addMaterialRow').click(function(){
            let html = `
                <tr>

                    <td>
                        <select class="form-select"
                                name="material_id[]"
                                required>

                            <option value="">Select</option>

                            ${materialOptions}

                        </select>
                    </td>

                    <td>
                        <input type="number"
                               step="0.01"
                               name="material_qty[]"
                               class="form-control"
                               required>
                    </td>

                    <td>
                        <button type="button"
                                class="btn btn-danger removeRow">
                            Remove
                        </button>
                    </td>
                </tr>`;
            $('#materialRows').append(html);
        });

        $(document).on('click','.removeRow',function(){
            $(this).closest('tr').remove();
        });

        $('#startSamplingForm').submit(function(e){
            e.preventDefault();
            $.ajax({
                url:'<?php echo $site_path; ?>/ajax/save_sampling_start',
                type:'POST',
                data:$(this).serialize(),
                dataType:'json',
                success:function(res){
                    if(res.status){
                        Swal.fire('Success',res.message,'success');
                        $('#startSamplingModal').modal('hide');
                        $('#samplingTable').DataTable().ajax.reload();
                    }else{
                        Swal.fire('Error',res.message,'error');
                    }
                }
            });
        });
    });
</script>