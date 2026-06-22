<?php
include("config/database.php");
include("config/auth_check.php");
include("includes/sidemenu.php");

$permissionList = getAllModules();
$permission_category_list = getAllPermissionCategory();
?>
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_toolbar" class="app-toolbar pt-7 pt-lg-10">
            <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
                <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                    <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                        <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">Permissions List</h1>
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                            <li class="breadcrumb-item text-muted">
                                <a href="<?php echo $site_path; ?>/dashboard" class="text-muted text-hover-primary">Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted">User Management</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div id="kt_app_content" class="app-content">
            <div id="kt_app_content_container" class="app-container container-fluid">
                <div class="card card-flush">
                    <div class="card-header mt-6 d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <!-- Search -->
                        <div class="card-title">
                            <div class="d-flex align-items-center position-relative my-1">
                                <i class="ki-outline ki-magnifier fs-3 position-absolute ms-5"></i>
                                <input type="text" data-kt-permissions-table-filter="search" class="form-control form-control-solid ps-13" style="min-width:220px" placeholder="Search Permissions"/>
                            </div>

                        </div>

                        <!-- Buttons -->
                        <div class="card-toolbar d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-light-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#kt_modal_add_module_category">
                                <i class="ki-outline ki-plus-square fs-3 me-2"></i>
                                Add Module Category
                            </button>

                            <button type="button" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#kt_modal_add_permission">
                                <i class="ki-outline ki-plus-square fs-3 me-2"></i>
                                Add Permission
                            </button>
                    </div>
                    </div>
                    <div class="card-body py-4">
                        <div class="table-responsive">
                        <table class="table align-middle table-row-dashed fs-6 gy-5 mb-0" id="kt_permissions_table">
                                <thead class="bg-light border-bottom">
                                <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                    <th class="min-w-125px">Name</th>
                                    <th class="min-w-250px">Assigned to</th>
                                    <th class="min-w-125px">Created Date</th>
                                    <th class="text-end min-w-100px">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="fw-semibold text-gray-600">
                                <?php
                                if ($permissionList) {
                                    foreach ($permissionList as $single_permission) {
                                        $module_id = $single_permission['id'];
                                        $assignToList = getListpermissionAssigned($module_id);
                                        ?>
                                        <tr>
                                            <td><?php echo $single_permission['module_name']; ?></td>
                                            <td>
                                                <?php
                                                if ($assignToList) {
                                                    foreach ($assignToList as $single_value) {
                                                        $badgeClass = 'badge-light-primary';
                                                        if ($single_value['id'] == 1) {
                                                            $badgeClass = 'badge-light-primary';
                                                        } else if ($single_value['id'] == 2) {
                                                            $badgeClass = 'badge badge-light-success';
                                                        }
                                                        ?>
                                                        <a href="<?php echo $site_path ?>/view_role?id=<?php echo my_simple_crypt($single_value['role_id'], 'encrypt_1'); ?>" class="<?php echo $badgeClass; ?> fs-7 m-1"><?php echo $single_value['role_name']; ?></a>
            <?php }
        } ?>

                                            </td>
                                            <td><?php echo date('d M Y, h:i:A', strtotime($single_permission['created_date'])); ?></td>
                                            <td class="text-end">
                                                <button class="btn btn-icon btn-active-light-primary w-30px h-30px me-3 btn-update-permission" data-bs-toggle="modal" data-bs-target="#kt_modal_update_permission" data-id="<?php echo $single_permission['id']; ?>" data-name="<?php echo $single_permission['module_name']; ?>">
                                                    <i class="ki-outline ki-setting-3 fs-3"></i>
                                                </button>
                                                <button class="btn btn-icon btn-active-light-primary w-30px h-30px" data-kt-permissions-table-filter="delete_row" data-id="<?php echo $single_permission['id']; ?>">
                                                    <i class="ki-outline ki-trash fs-3" ></i>
                                                </button>
                                            </td>
                                        </tr>
    <?php }
} ?>
                            </tbody>
                        </table>
                        <!--end::Table-->
                    </div>
                    </div>
                    <!--end::Card body-->
                </div>
                <div class="modal fade" id="kt_modal_add_permission" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered mw-650px">
                        <!--begin::Modal content-->
                        <div class="modal-content">
                            <!--begin::Modal header-->
                            <div class="modal-header">
                                <!--begin::Modal title-->
                                <h2 class="fw-bold">Add a Permission</h2>
                                <!--end::Modal title-->
                                <!--begin::Close-->
                                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-permissions-modal-action="close">
                                    <i class="ki-outline ki-cross fs-1"></i>
                                </div>
                                <!--end::Close-->
                            </div>
                            <!--end::Modal header-->
                            <!--begin::Modal body-->
                            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                                <!--begin::Form-->
                                <form id="kt_modal_add_permission_form" class="form" action="<?php echo $site_path; ?>/add-update-permission">
                                    <!--begin::Input group-->
                                    <div class="fv-row mb-7">
                                        <!--begin::Label-->
                                        <label class="fs-6 fw-semibold form-label mb-2">
                                            <span class="required">Permission Category</span>
                                            <span class="ms-2" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-html="true" data-bs-content="Permission names is required to be unique.">
                                                <i class="ki-outline ki-information fs-7"></i>
                                            </span>
                                        </label>
                                        <!--end::Label-->
                                        <!--begin::Input-->
                                        <select name="permission_category" id="permission_category" class="form-control form-control-solid">
                                            <option></option>
                                            <?php if ($permission_category_list) {
                                                foreach ($permission_category_list as $single_category) {
                                                    ?>
                                                    <option value="<?php echo $single_category['id']; ?>"><?php echo $single_category['category_name']; ?></option>
    <?php }
} ?>
                                        </select>
                                    </div>


                                    <div class="fv-row mb-7">
                                        <!--begin::Label-->
                                        <label class="fs-6 fw-semibold form-label mb-2">
                                            <span class="required">Permission Name</span>
                                            <span class="ms-2" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-html="true" data-bs-content="Permission names is required to be unique.">
                                                <i class="ki-outline ki-information fs-7"></i>
                                            </span>
                                        </label>
                                        <!--end::Label-->
                                        <!--begin::Input-->
                                        <input class="form-control form-control-solid" placeholder="Enter a permission name" name="permission_name" />

                                        <!--end::Input-->
                                    </div>

                                    <!--end::Input group-->
                                    <!--begin::Input group-->
                                    <div class="fv-row mb-7">
                                        <!--begin::Checkbox-->
                                        <label class="form-check form-check-custom form-check-solid me-9">
                                            <input class="form-check-input" type="checkbox" value="" name="permissions_core" id="kt_permissions_core" />
                                            <span class="form-check-label" for="kt_permissions_core">Set as core permission</span>
                                        </label>
                                        <!--end::Checkbox-->
                                    </div>
                                    <!--end::Input group-->
                                    <!--begin::Disclaimer-->
                                    <div class="text-gray-600">Permission set as a 
                                        <strong class="me-1">Core Permission</strong>will be locked and 
                                        <strong class="me-1">not editable</strong>in future</div>
                                    <!--end::Disclaimer-->
                                    <!--begin::Actions-->
                                    <div class="text-center pt-15">
                                        <button type="reset" class="btn btn-light me-3" data-kt-permissions-modal-action="cancel">Discard</button>
                                        <button type="submit" class="btn btn-primary" data-kt-permissions-modal-action="submit">
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
                </div>
                <div class="modal fade" id="kt_modal_add_module_category" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered mw-650px">
                        <!--begin::Modal content-->
                        <div class="modal-content">
                            <!--begin::Modal header-->
                            <div class="modal-header">
                                <!--begin::Modal title-->
                                <h2 class="fw-bold">Add a Module Category</h2>
                                <!--end::Modal title-->
                                <!--begin::Close-->
                                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-category-modal-action="close">
                                    <i class="ki-outline ki-cross fs-1"></i>
                                </div>
                                <!--end::Close-->
                            </div>
                            <!--end::Modal header-->
                            <!--begin::Modal body-->
                            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                                <!--begin::Form-->
                                <form id="kt_modal_add_module_permission_form" class="form" action="<?php echo $site_path; ?>/add-update-permission">
                                    <div class="fv-row mb-7">
                                        <!--begin::Label-->
                                        <label class="fs-6 fw-semibold form-label mb-2">
                                            <span class="required">Category Name</span>
                                            <span class="ms-2" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-html="true" data-bs-content="category names is required to be unique.">
                                                <i class="ki-outline ki-information fs-7"></i>
                                            </span>
                                        </label>
                                        <!--end::Label-->
                                        <!--begin::Input-->
                                        <input class="form-control form-control-solid" placeholder="Enter a category name" name="category_name" />
                                    </div>
                                    <!--end::Input group-->
                                    <!--begin::Actions-->
                                    <div class="text-center pt-15">
                                        <button type="reset" class="btn btn-light me-3" data-kt-category-modal-action="cancel">Discard</button>
                                        <button type="submit" class="btn btn-primary" data-kt-add-category-modal-action="submit">
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
                </div>
                <div class="modal fade" id="kt_modal_update_permission" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered mw-650px">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h2 class="fw-bold">Update Permission</h2>
                                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-permissions-modal-action="close">
                                    <i class="ki-outline ki-cross fs-1"></i>
                                </div>
                            </div>
                            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                                <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed mb-9 p-6">
                                    <i class="ki-outline ki-information fs-2tx text-warning me-4"></i>
                                    <div class="d-flex flex-stack flex-grow-1">
                                        <div class="fw-semibold">
                                            <div class="fs-6 text-gray-700">
                                                <strong class="me-1">Warning!</strong>By editing the permission name, you might break the system permissions functionality. Please ensure you're absolutely certain before proceeding.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <form id="kt_modal_update_permission_form" class="form" action="#">
                                    <input type="hidden" name="permission_id" id="permission_id">
                                    <div class="fv-row mb-7">
                                        <label class="fs-6 fw-semibold form-label mb-2">
                                            <span class="required">Permission Name</span>
                                            <span class="ms-2" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-html="true" data-bs-content="Permission names is required to be unique.">
                                                <i class="ki-outline ki-information fs-7"></i>
                                            </span>
                                        </label>
                                        <input class="form-control form-control-solid" placeholder="Enter a permission name" name="permission_name" id="permission_name" />
                                    </div>
                                    <div class="text-center pt-15">
                                        <button type="reset" class="btn btn-light me-3" data-kt-permissions-modal-action="cancel">Discard</button>
                                        <button type="submit" class="btn btn-primary" data-kt-permissions-modal-action="submit">
                                            <span class="indicator-label">Submit</span>
                                            <span class="indicator-progress">Please wait... 
                                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="kt_app_footer" class="app-footer">
        <div class="app-container container-fluid d-flex flex-column flex-md-row flex-center flex-md-stack py-3">
            <div class="text-gray-900 order-2 order-md-1">
                <span class="text-muted fw-semibold me-1"><?php echo date('Y') ?>©</span>
                <a href="https://vastranand.in" target="_blank" class="text-gray-800 text-hover-primary"> vastranand. All Rights Reserved.Powered by Vastranand Pvt Ltd.</a>
            </div>
        </div>
    </div>
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
<script src="<?php echo $site_path; ?>/assets/js/custom/apps/user-management/permissions/list.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/custom/apps/user-management/permissions/add-permission.js?v=<?php echo time(); ?>"></script>
<script src="<?php echo $site_path; ?>/assets/js/custom/apps/user-management/permissions/add-caetgory.js?v=<?php echo time(); ?>"></script>
<script src="<?php echo $site_path; ?>/assets/js/custom/apps/user-management/permissions/update-permission.js?v=<?php echo time(); ?>"></script>
<script src="<?php echo $site_path; ?>/assets/js/widgets.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/custom/widgets.js"></script>
<script>
    document.querySelectorAll(".btn-update-permission").forEach(btn => {
        btn.addEventListener("click", function () {
            let id = this.getAttribute("data-id");
            let name = this.getAttribute("data-name");

            // Fill modal inputs
            document.getElementById("permission_id").value = id;
            document.getElementById("permission_name").value = name;
        });
    });

    /*$('#permission_category').change(function(){
     var category_id = $(this).val();
     if(category_id != '') {
     $.ajax({
     url: '<?php echo $site_path; ?>/fetch_modules',
     type: 'POST',
     data: {category_id: category_id},
     dataType: 'json',
     success: function(response){
     $('#permission_module').html('<option value="">Select Module</option>'); // reset
     $.each(response, function(index, module){
     $('#permission_module').append('<option value="'+module.id+'">'+module.module_name+'</option>');
     });
     }
     });
     } else {
     $('#permission_module').html('<option value="">Select Module</option>');
     }
     });*/

</script>
</body>
</html>