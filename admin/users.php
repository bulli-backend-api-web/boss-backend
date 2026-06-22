<?php
include("config/database.php");
include("config/auth_check.php");
include("includes/sidemenu.php");
$users_role = getUniqueRoles();
$all_department_list = getAllDepartments();
$scan_app_modules = getScanAppModules();

error_reporting(E_ALL);
ini_set('display_errors',1);
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
                        <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">Users List</h1>
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
                            <li class="breadcrumb-item text-muted">User Management</li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">Users</li>
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
                    <div class="card-header border-0 pt-6 d-flex justify-content-between align-items-center flex-wrap">
                        <!--begin::Card title-->
                        <div class="card-title d-flex flex-wrap align-items-center gap-3">
                            <!--begin::Search-->
                            <div class="d-flex align-items-center position-relative my-1" style="margin-right: 15px;">
                                <i class="ki-outline ki-magnifier fs-3 position-absolute ms-5"></i>
                                <input type="text" data-kt-user-table-filter="search" class="form-control form-control-solid w-250px ps-13" placeholder="Search user" />
                            </div>
                            <div style="width:200px;">
                                <!--begin::Select2-->
                                <select id="search_by_role" class="form-select form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="Role" data-kt-ecommerce-product-filter="Role">
                                    <option value="all">All</option>
                                    <?php if ($users_role) {
                                        foreach ($users_role as $single_value) {
                                            ?>
                                            <option value="<?php echo $single_value['slug']; ?>"><?php echo $single_value['role_name']; ?></option>
                                        <?php }
                                    }
                                    ?>
                                </select>
                            </div>
                            <!--end::Search-->
                        </div>
                        <!--begin::Card title-->
                        <!--begin::Card toolbar-->
                        <div class="card-toolbar">
                            <!--begin::Toolbar-->
                            <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                                <!--end::Export-->
                                <!--begin::Add user-->
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_user">
                                    <i class="ki-outline ki-plus fs-2"></i>Add User</button>
                                <!--end::Add user-->
                            </div>
                            <!--end::Toolbar-->
                            <!--begin::Group actions-->
                            <div class="d-flex justify-content-end align-items-center d-none" data-kt-user-table-toolbar="selected">
                                <div class="fw-bold me-5">
                                    <span class="me-2" data-kt-user-table-select="selected_count"></span>Selected</div>
                                <button type="button" class="btn btn-danger" data-kt-user-table-select="delete_selected">Delete Selected</button>
                            </div>
                            <div class="modal fade" id="kt_modal_add_user" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered mw-650px">
                                    <div class="modal-content">
                                        <div class="modal-header" id="kt_modal_add_user_header">
                                            <h2 class="fw-bold">Add User</h2>
                                            <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-users-modal-action="close">
                                                <i class="ki-outline ki-cross fs-1"></i>
                                            </div>
                                        </div>
                                        <div class="modal-body px-5 my-7">
                                            <form id="kt_modal_add_user_form" class="form" action="<?php echo $site_path; ?>/ajax/add-update-user-details">
                                                <div class="d-flex flex-column scroll-y px-5 px-lg-10" id="kt_modal_add_user_scroll" data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_add_user_header" data-kt-scroll-wrappers="#kt_modal_add_user_scroll" data-kt-scroll-offset="300px">
                                                    <div class="fv-row mb-7">
                                                        <label class="required fw-semibold fs-6 mb-2">Full Name</label>
                                                        <input type="text" name="fullname" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Full name" value="" />
                                                    </div>
                                                    <div class="fv-row mb-7">
                                                        <label class="required fw-semibold fs-6 mb-2">Email ID</label>
                                                        <input type="email" name="user_email" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="example@domain.com" value="" />
                                                    </div>
                                                    <div class="fv-row mb-7">
                                                        <label class="required fw-semibold fs-6 mb-2">Mobile</label>
                                                        <input type="text" name="user_mobile" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Mobile Number" value="" />
                                                    </div>
                                                    <div class="fv-row mb-7">
                                                        <label class="form-label required fw-semibold fs-6">Department</label>
                                                            <select name="department_id" id="department_id" aria-label="Department" data-control="select2" data-placeholder="Department" class="form-select form-select-solid form-select-lg fw-semibold">
                                                                <option value="">Select Department</option>
                                                                <?php if($all_department_list){
                                                                        foreach($all_department_list as $single_dept){?>

                                                                <option value="<?php echo $single_dept['id']; ?>"><?php echo $single_dept['department_name']; ?></option>
                                                                <?php } } ?>
                                                            </select>
                                                    </div>
                                                    <div class="fav-row mb-7">
                                                        <label class="required fw-semibold fs-6 mb-5">Role</label>
                                                        <select name="role_id" id="role_id" aria-label="Department" data-control="select2" data-placeholder="Role" class="form-select form-select-solid form-select-lg fw-semibold">
                                                                <option value="">Select Role</option>
                                                                <?php if($users_role){
                                                                        foreach($users_role as $single_role){?>

                                                                <option value="<?php echo $single_role['id']; ?>"><?php echo $single_role['role_name']; ?></option>
                                                                <?php } } ?>
                                                            </select>
                                                    </div>
                                                    <div class="fav-row mb-7">
                                                        <label class="required fw-semibold fs-6 mb-5">Brand Name</label>
                                                        <select name="brand_name" id="brand_name" aria-label="Brand Name" data-control="select2" data-placeholder="Brand Name" class="form-select form-select-solid form-select-lg fw-semibold">
                                                            <option value="">Select Brand Name</option>
                                                            <option value="1">Bullion Knot</option>
                                                            <option value="2">Under3k</option>
                                                            <option value="3">All Brand</option>
                                                        </select>
                                                    </div>
                                                    <div class="fv-row mb-7">
                                                        <label class="fw-semibold fs-6 mb-2">Mobile Module Access</label>
                                                        <div class="d-flex align-items-center flex-wrap mt-3">

                                                            <?php
                                                            if ($scan_app_modules) {
                                                                foreach ($scan_app_modules as $single_module) {
                                                                    ?>

                                                                    <label class="form-check form-check-custom form-check-inline form-check-solid me-5 mb-3">
                                                                        <input class="form-check-input permission-checkbox" 
                                                                               type="checkbox" 
                                                                               name="scan_app_moduel[]" 
                                                                               value="<?= $single_module['id']; ?>">
                                                                        <span class="fw-semibold ps-2 fs-6">
                                                                            <?= $single_module['name']; ?>
                                                                        </span>
                                                                    </label>

                                                                <?php }
                                                            }
                                                            ?>

                                                        </div>
                                                    </div>
                                                    <div class="fv-row mb-7">
                                                        <label class="required fw-semibold fs-6 mb-2">Username</label>
                                                        <input type="text" name="user_name" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Username" value="" />
                                                    </div>
                                                    <div class="fv-row mb-7">
                                                        <label class="required fw-semibold fs-6 mb-2">Password</label>
                                                        <input type="password" name="user_password" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Password" value="" />
                                                    </div>
                                                    
                                                    <div class="fv-row mb-7 mb-6">
                                                        <label class="fs-6 fw-semibold mb-2">
                                                            <span>Face Attendance</span>
                                                        </label>
                                                        <div class="col-md-12">
                                                            <div class="row row-cols-1 row-cols-md-3 row-cols-lg-1 row-cols-xl-3 g-9" data-kt-buttons="true" data-kt-buttons-target="[data-kt-button='true']">
                                                                <div class="col">
                                                                    <label class="btn btn-outline btn-outline-dashed btn-active-light-primary d-flex text-start p-6 active" data-kt-button="true">
                                                                        <span class="form-check form-check-custom form-check-solid form-check-sm align-items-start mt-1">
                                                                            <input class="form-check-input" type="radio" name="face_attendance" value="1" checked />
                                                                        </span>
                                                                        <span class="ms-5">
                                                                            <span class="fs-4 fw-bold text-gray-800 d-block">Yes</span>
                                                                        </span>
                                                                    </label>
                                                                </div>
                                                                <div class="col">
                                                                    <label class="btn btn-outline btn-outline-dashed btn-active-light-primary d-flex text-start p-6" data-kt-button="true">
                                                                        <span class="form-check form-check-custom form-check-solid form-check-sm align-items-start mt-1">
                                                                            <input class="form-check-input" type="radio" name="face_attendance" value="0"/>
                                                                        </span>
                                                                        <span class="ms-5">
                                                                            <span class="fs-4 fw-bold text-gray-800 d-block">NO</span>
                                                                        </span>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="fv-row mb-7">
                                                        <label class="required fw-semibold fs-6 mb-2">Profile Picture</label>
                                                        <input type="file" name="profile_picture" id="profile_picture" class="form-control form-control-solid mb-3 mb-lg-0"/>
                                                        <div class="mt-3">
                                                            <img id="profile_preview" src="https://placehold.co/150x150?text=No+Image" alt="Profile Preview" class="img-thumbnail" style="max-width:150px; max-height:150px;">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="text-center pt-10">
                                                    <button type="reset" class="btn btn-light me-3" data-kt-users-modal-action="cancel">Discard</button>
                                                    <button type="submit" class="btn btn-primary" data-kt-users-modal-action="submit">
                                                        <span class="indicator-label">Submit</span>
                                                        <span class="indicator-progress">Please wait... 
                                                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                                    </button>
                                                </div>
                                                <!--end::Actions-->
                                            </form>
                                            <!--end::Form-->
                                        </div>
                                        <!--end::Modal body-->
                                    </div>
                                    <!--end::Modal content-->
                                </div>
                                <!--end::Modal dialog-->
                            </div>
                        </div>
                        <!--end::Card toolbar-->
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body py-4">
                        <div class="table-responsive">
                        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
                                <thead class="bg-light border-bottom">
                                <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                    <th class="w-10px pe-2">
                                        <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                            <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input" value="1" />
                                        </div>
                                    </th>
                                    <th class="min-w-125px">User</th>
                                    <th class="min-w-125px">Role</th>
                                    <th class="min-w-125px">Last login</th>
                                    <th class="min-w-125px">Two Factor</th>
                                    <th class="min-w-125px">Joined Date</th>
                                    <th class="min-w-100px">Actions</th>
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
</div>
</div>
<div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
    <i class="ki-outline ki-arrow-up"></i>
</div>

<script>var hostUrl = "<?php echo $site_path; ?>/";</script>
<script src="<?php echo $site_path; ?>/assets/plugins/global/plugins.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/scripts.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/custom/apps/user-management/users/list/table.js?v=<?php echo time(); ?>"></script>
<script src="<?php echo $site_path; ?>/assets/js/custom/apps/user-management/users/list/add.js?v=<?php echo time(); ?>"></script>
<script>
    $("#dob").flatpickr({
        altInput: !0,
        altFormat: "Y-m-d",
        dateFormat: "Y-m-d"
    });
    $("#doj").flatpickr({
        altInput: !0,
        altFormat: "Y-m-d",
        dateFormat: "Y-m-d"
    });
    
    document.getElementById('profile_picture').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();

            reader.onload = function(event) {
                document.getElementById('profile_preview').src = event.target.result;
            };

            reader.readAsDataURL(file);
        }
    });
    
    $(document).ready(function () {

        // Initialize DataTable
        var table = $('#kt_table_users').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?php echo $site_path ?>/ajax/fetch-admin-users',
                type: 'POST',
                data: function (d) {
                    d.ajax = 1;
                    d.search_by_role = $('#search_by_role').val();
                }
            },

            columns: [
                {data: 'select_all'},
                {data: 'username', className: "d-flex align-items-center"},
                {data: 'role'},
                {data: 'last_login'},
                {data: 'two_step'},
                {data: 'join_date'},
                {data: 'actions', orderable: false}
            ],
            pageLength: 50,
            order: [[4, 'desc']], // default sort: Order Date descending
            columnDefs: [
                {targets: [1, 2, 3], orderable: true}, // allow sorting on these columns
                {targets: [0, 4, 5, 6], orderable: false} // actions column not sortable
            ],
            drawCallback: function () {
                // handle delete buttons etc.
                KTMenu.createInstances();
            }
        });

        $('[data-kt-user-table-filter="search"]').on('keyup', function () {
            table.search(this.value).draw();
        });

        $("#search_by_role").on('change', function () {
            table.ajax.reload();
        });
    });

</script>
</body>
</html>