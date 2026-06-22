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
                            Inward Challan Dashboard
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
                                Challan Dashboard
                            </li>
                        </ul>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <a href="<?php echo $site_path; ?>/create-challan" class="btn btn-primary">
                            <i class="ki-duotone ki-plus fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Create Challan
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
                                <div class="text-muted fw-semibold">Total Challans</div>
                                <div class="fs-2hx fw-bold text-gray-900" id="total_challans">0</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card card-flush shadow-sm">
                            <div class="card-body">
                                <div class="text-muted fw-semibold">Pending Print</div>
                                <div class="fs-2hx fw-bold text-warning" id="pending_print">0</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card card-flush shadow-sm">
                            <div class="card-body">
                                <div class="text-muted fw-semibold">Pending Scan</div>
                                <div class="fs-2hx fw-bold text-info" id="pending_scan">0</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card card-flush shadow-sm">
                            <div class="card-body">
                                <div class="text-muted fw-semibold">Completed</div>
                                <div class="fs-2hx fw-bold text-success" id="completed_challans">0</div>
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
                                <input type="text" id="challan_search" class="form-control form-control-solid w-300px ps-12" placeholder="Search Challan / Product / SKU">
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_inward_challan_table">
                            <thead>
                                <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                    <th class="min-w-50px">#</th>
                                    <th class="min-w-100px">Challan / Batch</th>
                                    <th class="min-w-50px">Date</th>
                                    <th class="min-w-50px">Products</th>
                                    <th class="min-w-100px">Total Qty</th>
                                    <th class="min-w-50px">Printed</th>
                                    <th class="min-w-50px">Scanned</th>
                                    <th class="min-w-50px">Pending</th>
                                    <th class="min-w-50px">Status</th>
                                    <th class="min-w-180px text-center">Actions</th>
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

    var table = $('#kt_inward_challan_table').DataTable({
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
                    $('#total_challans').text(json.summary.total_challans);
                    $('#pending_print').text(json.summary.pending_print);
                    $('#pending_scan').text(json.summary.pending_scan);
                    $('#completed_challans').text(json.summary.completed_challans);
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
            { data: 'printed_qty' },
            { data: 'scanned_qty' },
            { data: 'pending_qty' },
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

    $('#challan_search').on('keyup', function () {
        table.search(this.value).draw();
    });

    $(document).on('click', '.delete-challan', function () {

        let batch_id = $(this).data('id');

        if (!confirm('Are you sure you want to delete this challan?')) {
            return;
        }

        $.ajax({
            url: "<?php echo $site_path; ?>/ajax/ajax-delete-master-data",
            type: "POST",
            dataType: "json",
            data: {
                batch_id: batch_id,
                action : 'delete_inwards'
            },
            success: function (res) {

                if (res.status === true) {
                    table.ajax.reload();
                } else {
                    alert(res.message);
                }

            },
            error: function () {
                alert('Something went wrong');
            }
        });

    });

});
</script>