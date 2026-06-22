<?php
include("config/database.php");
include("config/auth_check.php");
include("includes/sidemenu.php");
?>

<style>
    .inventory-kpi-card {
        border: 1px solid #eef0f7;
        border-radius: 16px;
        transition: .2s ease;
    }

    .inventory-kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0,0,0,.06);
    }

    .inventory-kpi-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .status-filter-btn.active {
        background-color: #009ef7 !important;
        color: #fff !important;
    }
</style>

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">

        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">

                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading text-gray-900 fw-bold fs-3 my-0">
                        Stock Inward Management
                    </h1>

                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="<?php echo $site_path; ?>/pages/dashboard" class="text-muted text-hover-primary">
                                Home
                            </a>
                        </li>

                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>

                        <li class="breadcrumb-item text-muted">
                            Inventory
                        </li>

                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>

                        <li class="breadcrumb-item text-muted">
                            Stock Inward Batch List
                        </li>
                    </ul>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <a href="<?php echo $site_path; ?>/create-product-wise-stock" class="btn btn-primary">
                        <i class="ki-duotone ki-plus fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Create New Batch
                    </a>
                </div>
            </div>
        </div>

        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                <!-- KPI Cards -->
                <div class="row g-5 g-xl-8 mb-8">
                    <div class="col-xl-3 col-md-6">
                        <div class="card inventory-kpi-card">
                            <div class="card-body d-flex align-items-center">
                                <div class="inventory-kpi-icon bg-light-primary me-4">
                                    <i class="ki-duotone ki-box fs-2x text-primary">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </div>
                                <div>
                                    <div class="fs-7 text-muted fw-semibold">
                                        Total Inward Qty
                                    </div>
                                    <div class="fs-2 fw-bold text-gray-900" id="total_inward_qty">
                                        0
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card inventory-kpi-card">
                            <div class="card-body d-flex align-items-center">
                                <div class="inventory-kpi-icon bg-light-warning me-4">
                                    <i class="ki-duotone ki-time fs-2x text-warning">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </div>

                                <div>
                                    <div class="fs-7 text-muted fw-semibold">
                                        Active Batches
                                    </div>
                                    <div class="fs-2 fw-bold text-warning" id="active_batches">
                                        0
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card inventory-kpi-card">
                            <div class="card-body d-flex align-items-center">
                                <div class="inventory-kpi-icon bg-light-success me-4">
                                    <i class="ki-duotone ki-check-circle fs-2x text-success">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </div>

                                <div>
                                    <div class="fs-7 text-muted fw-semibold">
                                        Completed Batches
                                    </div>
                                    <div class="fs-2 fw-bold text-success" id="completed_batches">
                                        0
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card inventory-kpi-card">
                            <div class="card-body d-flex align-items-center">
                                <div class="inventory-kpi-icon bg-light-info me-4">
                                    <i class="ki-duotone ki-calendar fs-2x text-info">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </div>

                                <div>
                                    <div class="fs-7 text-muted fw-semibold">
                                        Today Inward
                                    </div>
                                    <div class="fs-2 fw-bold text-info" id="today_inward_qty">
                                        0
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Main List Card -->
                <div class="card card-flush shadow-sm">
                    <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                        <div class="card-title">
                            <div class="d-flex align-items-center position-relative my-1">
                                <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <input type="text" id="inward_search" class="form-control form-control-solid w-300px ps-12" placeholder="Search Batch / Product / SKU" />
                            </div>
                        </div>
                    </div>

                    <div class="card-body pt-0">
                        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_stock_inward_batch_table">
                            <thead>
                                <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                    <th class="min-w-50px">#</th>
                                    <th class="min-w-140px">Batch No</th>
                                    <th class="min-w-120px">Challan No</th>
                                    <th class="min-w-120px">Date</th>
                                    <th class="min-w-150px">SKU</th>
                                    <th class="min-w-180px">Product</th>
                                    <th class="min-w-90px">Size</th>
                                    <th class="min-w-90px">Qty</th>
                                    <th class="min-w-100px">Printed</th>
                                    <th class="min-w-100px">Scanned</th>
                                    <th class="min-w-120px">Status</th>
                                    <th class="min-w-180px text-end">Actions</th>
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

<script src="<?php echo $site_path; ?>/assets/plugins/global/plugins.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/scripts.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/plugins/custom/datatables/datatables.bundle.js"></script>

<script>
$(document).ready(function () {

    var table = $('#kt_stock_inward_batch_table').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 10,
        lengthMenu: [
            [10, 25, 50, 100, 250],
            [10, 25, 50, 100, 250]
        ],
        ajax: {
            url: "<?php echo $site_path; ?>/ajax/fetch-stock-inward-batches",
            type: "POST",
            data: function (d) {
                d.status    = $('#status_filter').val();
                d.from_date = $('#from_date').val();
                d.to_date   = $('#to_date').val();
            },
            dataSrc: function (json) {

                if (json.summary) {
                    $('#total_inward_qty').text(json.summary.total_inward_qty);
                    $('#active_batches').text(json.summary.active_batches);
                    $('#completed_batches').text(json.summary.completed_batches);
                    $('#today_inward_qty').text(json.summary.today_inward_qty);
                }

                return json.data;
            }
        },
        columns: [
            { data: 'sr_no' },
            { data: 'batch_no' },
            { data: 'challan_no' },
            { data: 'inward_date' },
            { data: 'sku' },
            { data: 'product' },
            { data: 'size', orderable: false },
            { data: 'qty' },
            { data: 'printed_qty' },
            { data: 'scanned_qty' },
            { data: 'status' },
            { data: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        drawCallback: function () {
            if (typeof KTMenu !== 'undefined') {
                KTMenu.createInstances();
            }
        }
    });

    $('#inward_search').on('keyup', function () {
        table.search(this.value).draw();
    });
});
</script>