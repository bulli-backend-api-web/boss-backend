<?php
include("config/database.php");
include("config/auth_check.php");
include("includes/sidemenu.php");
?>
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <!--begin::Content wrapper-->
    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Toolbar-->
        <div id="kt_app_toolbar" class="app-toolbar pt-7 pt-lg-10">
            <!--begin::Toolbar container-->
            <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
                <!--begin::Toolbar wrapper-->
                <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                    <!--begin::Page title-->
                    <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                        <!--begin::Title-->
                        <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">Manage Pending Return Order Inquiry</h1>
                        <!--end::Title-->
                        <!--begin::Breadcrumb-->
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <a href="<?php echo $site_path ?>/dashboard" class="text-muted text-hover-primary">Home</a>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">Pending Return Order Inquiry</li>
                            <!--end::Item-->

                        </ul>
                        <!--end::Breadcrumb-->
                    </div>
                    <!--end::Page title-->
                </div>
                <!--end::Toolbar wrapper-->
            </div>
            <!--end::Toolbar container-->
        </div>
        <!--end::Toolbar-->
        <!--begin::Content-->
        <div id="kt_app_content" class="app-content">
            <!--begin::Content container-->
            <div id="kt_app_content_container" class="app-container container-fluid">
                <!--begin::Card-->
                <div class="card">
                    <!--begin::Card header-->
                    <div class="card-header border-0 pt-6">
                        <!--begin::Card title-->
                        <div class="card-title">
                            <!--begin::Search-->
                            <div class="d-flex align-items-center position-relative my-1" style="margin-right: 15px;">
                                <i class="ki-outline ki-magnifier fs-3 position-absolute ms-5"></i>
                                <input type="text" data-kt-user-table-filter="search" class="form-control form-control-solid w-250px ps-13" placeholder="Search" />
                            </div>
                        </div>
                    </div>
                    <div class="card-body py-4">
                        <div class="table-responsive">
                        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_pending_return_order_list">
                                <thead class="bg-light border-bottom">
                                <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                    <th class="min-w-50px">SR No</th>
                                    <th class="min-w-100px">Date</th>
                                    <th class="min-w-100px">Name</th>
                                    <th class="min-w-100px">Mobile</th>
                                    <th class="min-w-100px">Return Status</th>
                                    <th class="min-w-100px">Order ID</th>
                                    <th class="min-w-100px">Action</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-semibold">
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

<script src="<?php echo $site_path; ?>/assets/plugins/global/plugins.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/scripts.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<script>
    $(document).ready(function () {
        
        // Initialize DataTable
        var table = $('#kt_pending_return_order_list').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?php echo $site_path ?>/ajax/ajax-fetch-pending-return-order-inquiry',
                type: 'POST',
                data: function (d) {
                    
                }
            },

            columns: [
                {data: 'sr_no'},
                {data: 'date'},
                {data: 'name'},
                {data: 'mobile'},
                {data: 'return_status'},
                {data: 'order_id'},
                {data: 'actions', orderable: false}
            ],
            pageLength: 50,
            order: [[2, 'desc']],
            columnDefs: [
                {targets: [1,2,3], orderable: true},
                {targets: [0,1,5,6], orderable: false}
            ],
            drawCallback: function () {
                KTMenu.createInstances();
            }
        });
        
        $('[data-kt-user-table-filter="search"]').on('keyup', function () {
            table.search(this.value).draw();
        });
        
    });
    
    

</script>
</body>
</html>