<?php
include("config/database.php");
include("config/auth_check.php");
include("includes/sidemenu.php");
?>

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">

        <div id="kt_app_toolbar" class="app-toolbar pt-7 pt-lg-10">
            <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
                <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">

                    <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                        <h1 class="page-heading text-gray-900 fw-bold fs-3 m-0">
                            Pending Inward Scanning
                        </h1>

                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                            <li class="breadcrumb-item text-muted">
                                <a href="<?php echo $site_path; ?>/dashboard" class="text-muted text-hover-primary">
                                    Home
                                </a>
                            </li>

                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>

                            <li class="breadcrumb-item text-muted">
                                Stock Inward
                            </li>

                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>

                            <li class="breadcrumb-item text-muted">
                                Pending Scanning Dashboard
                            </li>
                        </ul>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <a href="<?php echo $site_path; ?>/scan-stock-inward" class="btn btn-primary">
                            <i class="ki-duotone ki-barcode fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            Scan Stock
                        </a>
                    </div>

                </div>
            </div>
        </div>

        <div id="kt_app_content" class="app-content">
            <div id="kt_app_content_container" class="app-container container-fluid">

                <div class="row g-5 g-xl-8 mb-8">

                    <div class="col-xl-3 col-md-6">
                        <div class="card card-flush shadow-sm">
                            <div class="card-body">
                                <div class="text-muted fw-semibold">Pending Challans</div>
                                <div class="fs-2hx fw-bold text-warning" id="pending_challans">0</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card card-flush shadow-sm">
                            <div class="card-body">
                                <div class="text-muted fw-semibold">Pending Units</div>
                                <div class="fs-2hx fw-bold text-danger" id="pending_units">0</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card card-flush shadow-sm">
                            <div class="card-body">
                                <div class="text-muted fw-semibold">Printed Pending</div>
                                <div class="fs-2hx fw-bold text-info" id="printed_pending">0</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card card-flush shadow-sm">
                            <div class="card-body">
                                <div class="text-muted fw-semibold">Scanning Started</div>
                                <div class="fs-2hx fw-bold text-primary" id="scanning_started">0</div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="card card-flush shadow-sm">

                    <div class="card-header align-items-center py-5 gap-2 gap-md-5">

                        <div class="card-title">
                            <div class="d-flex align-items-center position-relative my-1">
                                <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>

                                <input type="text"
                                       id="pending_search"
                                       class="form-control form-control-solid w-300px ps-12"
                                       placeholder="Search Challan / Batch">
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-0">

                        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_pending_inward_scanning_table">
                            <thead>
                                <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                    <th>#</th>
                                    <th>Challan / Batch</th>
                                    <th>Date</th>
                                    <th>Products</th>
                                    <th>Total Qty</th>
                                    <th>Scanned</th>
                                    <th>Pending</th>
                                    <th>Progress</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
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

    var table = $('#kt_pending_inward_scanning_table').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 10,
        lengthMenu: [
            [10, 25, 50, 100, 250],
            [10, 25, 50, 100, 250]
        ],
        ajax: {
            url: "<?php echo $site_path; ?>/ajax/fetch-pending-inward-scanning.php",
            type: "POST",
            data: function (d) {
                d.status    = $('#status_filter').val();
                d.from_date = $('#from_date').val();
                d.to_date   = $('#to_date').val();
            },
            dataSrc: function (json) {

                if (json.summary) {
                    $('#pending_challans').text(json.summary.pending_challans);
                    $('#pending_units').text(json.summary.pending_units);
                    $('#printed_pending').text(json.summary.printed_pending);
                    $('#scanning_started').text(json.summary.scanning_started);
                }

                return json.data;
            }
        },
        columns: [
            { data: 'sr_no' },
            { data: 'challan_info' },
            { data: 'inward_date' },
            { data: 'products' },
            { data: 'qty' },
            { data: 'scanned_qty' },
            { data: 'pending_qty' },
            { data: 'progress' },
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

    $('#pending_search').on('keyup', function () {
        table.search(this.value).draw();
    });

    $('#status_filter').on('change', function () {
        table.ajax.reload();
    });

    $('#filter_btn').on('click', function () {
        table.ajax.reload();
    });

    $('#reset_btn').on('click', function () {
        $('#status_filter').val('').trigger('change');
        $('#from_date').val('');
        $('#to_date').val('');
        $('#pending_search').val('');

        table.search('').draw();
        table.ajax.reload();
    });

});
</script>