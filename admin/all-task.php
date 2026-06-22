<?php
include("config/database.php");
include("config/auth_check.php");
include("includes/sidemenu.php");
$to_date = '';
$from_date = '';
$channelusers = [];
?>
<style>
    .task-title{
        font-size:15px;
        font-weight:600;
        color:#222;
    }

    .task-subtitle{
        font-size:12px;
        color:#888;
        margin-top:3px;
    }

    .priority-high{
        color:#dc3545;
        font-weight:500;
    }

    .priority-medium{
        color:#d39e00;
        font-weight:500;
    }

    .priority-low{
        color:#198754;
        font-weight:500;
    }

    .badge{
        padding:6px 12px;
        border-radius:20px;
        font-size:12px;
        font-weight:600;
    }

    .badge-light-primary{
        background:#e8f0ff;
        color:#0d6efd;
    }

    .badge-light-warning{
        background:#fff3cd;
        color:#b78103;
    }

    .badge-light-success{
        background:#d1f7df;
        color:#198754;
    }

    .badge-light-danger{
        background:#fde2e2;
        color:#dc3545;
    }

    .badge-light-info{
        background:#e2f3ff;
        color:#0dcaf0;
    }
</style>
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_toolbar" class="app-toolbar pt-7 pt-lg-10">
            <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
                <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                    <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                        <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">All Tasks</h1>
                    </div>
                </div>
            </div>
        </div>
        <div id="kt_app_content" class="app-content">
            <div id="kt_app_content_container" class="app-container container-fluid">
                <div class="card">
                    <div class="card-header border-0 pt-6">

                        <!--begin::Toolbar-->
                        <div class="d-flex flex-wrap align-items-center gap-3 w-100">

                            <!-- Search -->
                            <div class="d-flex align-items-center position-relative flex-grow-1" style="max-width:320px;">
                                <i class="ki-outline ki-magnifier fs-3 position-absolute ms-5"></i>
                                <input type="text" class="form-control form-control-solid ps-12" placeholder="Search Order" data-kt-customer-table-filter="search"/>
                            </div>
                            <div class="filter-item">
                                <select id="task_type" class="form-select form-select-solid" data-control="select2" data-hide-search="false" data-placeholder="Task Type" data-kt-ecommerce-product-filter="Task Type">
                                    <option></option>
                                    <option value="all">All</option>
                                    <?php foreach($task_type as $single_task){ ?>
                                    <option value="<?php echo $single_task['id']; ?>"><?php echo $single_task['name']; ?></option>
                                    <?php }?>
                                </select>
                            </div>
                            <div class="ms-auto">
                                <button type="button" id="export_b2c_order_csv" class="btn btn-primary d-flex align-items-center">
                                    <i class="ki-outline ki-exit-up fs-2 me-2"></i>
                                    Export
                                </button>
                            </div>
                        </div>
                        <input type="hidden" id="from_date" name="from_date">
                        <input type="hidden" id="to_date" name="to_date">
                        <!--end::Toolbar-->

                    </div>
                    <div class="card-body py-4">
                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed table-row-gray-300 gy-5 gs-7" id="kt_task_table">
                                <thead  class="bg-light border-bottom">
                                    <tr class="text-gray-700 fw-bold fs-7">
                                        <th class="min-w-130px">#</th>
                                        <th class="min-w-130px">Task</th>
                                        <th class="min-w-130px">Type</th>
                                        <th class="min-w-180px">Dept</th>
                                        <th class="min-w-120px">Staff</th>
                                        <th class="min-w-80px">Priority</th>
                                        <th class="min-w-80px">Deadline</th>
                                        <th class="min-w-80px">Status</th>
                                        <th class="min-w-100px text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="fw-semibold text-gray-600">
                                </tbody>
                            </table>
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
</div>
<script>var hostUrl = "<?php echo $site_path; ?>/assets/";</script>
<script src="<?php echo $site_path; ?>/assets/plugins/global/plugins.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/scripts.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<script>
    "use strict";

    $(document).ready(function () {
        // Initialize DataTable
        var table = $('#kt_task_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?php echo $site_path ?>/ajax/fetch-task-list',
                type: 'POST',
                data: function (d) {
                    d.ajax = 1;
                    d.task_type = $("#task_type").val();
                }
            },

            columns: [
                {data: 'sr_no'},
                {data: 'task'},
                {data: 'task_type'},
                {data: 'dept'},
                {data: 'staff'},
                {data: 'priority'},
                {data: 'deadline'},
                {data: 'status'},
                {data: 'actions', orderable: false, 'className': 'text-end'}
            ],
            pageLength: 50,
            order: [[0, 'desc']], // default sort: Order Date descending
            columnDefs: [
                {targets: [0, 1, 2, 4, 5, 6, 7, 8], orderable: true}, // allow sorting on these columns
                {targets: [3], orderable: false} // actions column not sortable
            ],
            drawCallback: function () {
                KTMenu.createInstances();
            }
        });

        $('[data-kt-customer-table-filter="search"]').on('keyup', function () {
            table.search(this.value).draw();
        });

        $('[data-kt-ecommerce-product-filter="Channel"]').on('keyup', function () {
            table.search(this.value).draw();
        });


        $("#task_type").on('change', function () {
            table.ajax.reload();
        });
    });

</script>
</body>
</html>