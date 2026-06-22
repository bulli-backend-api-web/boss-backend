<?php
include("config/database.php");
include("config/auth_check.php");
include("includes/sidemenu.php");

$all_department_list = getAllDepartments();

?>
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">

        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading text-gray-900 fw-bold fs-3 my-0">Rule Book</h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="<?php echo $site_path; ?>/pages/dashboard" class="text-muted text-hover-primary">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">Rule Book</li>
                    </ul>
                </div>
            </div>
        </div>

        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                <div class="card card-flush shadow-sm">
                    <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                        <div class="card-title">
                            <div class="d-flex align-items-center position-relative my-1">
                                <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <input type="text" id="design_search" class="form-control form-control-solid w-300px ps-12" placeholder="Search Design Name" />
                            </div>
                        </div>

                        <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                            <div class="w-100 mw-175px">
                                <select id="department_filter" class="form-select form-select-solid" data-control="select2" data-hide-search="true">
                                    <option value="">All Department</option>
                                    <?php if($all_department_list){
                                               foreach($all_department_list as $single_dept){?> 
                                                    <option value="<?php echo $single_dept['id']; ?>"><?php echo $single_dept['department_name']; ?></option>
                                               <?php } 
                                               } ?>
                                </select>
                            </div>
                        </div>
                        <a href="<?php echo $site_path; ?>/create-rule" class="btn btn-primary"><i class="fa fa-plus"></i>Create Rule</a>
                    </div>
                    <div class="card-body pt-0">
                        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_rule_book_table">
                            <thead>
                                <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                    <th class="min-w-50px">#</th>
                                    <th class="min-w-150px">Name</th>
                                    <th class="min-w-150px">Rule Type</th>
                                    <th class="min-w-150px">Apply To</th>
                                    <th class="min-w-150px">Department</th>
                                    <th class="min-w-150px">Serverity</th>
                                    <th class="min-w-150px">Actions</th>
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
<script src="<?php echo $site_path; ?>/assets/js/custom/apps/user-management/users/list/table.js?v=<?php echo time(); ?>"></script>

<script>
    $(document).ready(function () {
        var table = $('#kt_rule_book_table').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            ajax: {
                url: "<?php echo $site_path; ?>/ajax/fetch-rule-book-list",
                type: "POST",
                data: function (d) {
                    d.department_filter = $("#department_filter").val();
                }
            },
            columns: [
                {data: 'sr_no' },
                {data: 'name', orderable: false },
                {data: 'rule_type', orderable: false },
                {data: 'apply_to' },
                {data: 'department'},
                {data: 'serverity'},
                {data: 'actions'}
            ],
            order: [[0, 'desc']],
            drawCallback: function () {
                if (typeof KTMenu !== 'undefined') {
                    KTMenu.createInstances();
                }
            }
        });
    
        
        $('#sample_search').on('keyup', function () {
            table.search(this.value).draw();
        });
        
        $('#department_filter').on('change', function () {
            table.ajax.reload();
        });
    });
</script>