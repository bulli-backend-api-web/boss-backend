<?php
include("config/database.php");
include("config/auth_check.php");
include("includes/sidemenu.php");
$users_role = getUniqueRoles();
$allModules = getAllModules();
$assignedModules = getAssignedPermissions($typee_id);
$permission_category_list = getAllPermissionCategory();
$allPermissions = getAllModules();
?>
<!--begin::Main-->
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
                        <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">Roles List</h1>
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
                            <li class="breadcrumb-item text-muted">Roles</li>
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
                <!--begin::Row-->
                <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-5 g-xl-9">
                    <!--begin::Col-->
                    <?php if ($users_role) {
                        foreach ($users_role as $single_value) {
                            ?>
                            <div class="col-md-4">
                                <!--begin::Card-->
                                <div class="card card-flush h-md-100">
                                    <!--begin::Card header-->
                                    <div class="card-header">
                                        <!--begin::Card title-->
                                        <div class="card-title">
                                            <h2><?php echo $single_value['role_name']; ?></h2>
                                        </div>
                                        <!--end::Card title-->
                                    </div>
                                    <!--end::Card header-->
                                    <!--begin::Card body-->
                                    <div class="card-body pt-1">
                                        <!--begin::Users-->
                                        <div class="fw-bold text-gray-600 mb-5">Total users with this role: <?php echo getRoleCount($single_value['slug']); ?></div>
                                        <!--end::Users-->
                                    </div>
                                    <!--end::Card body-->
                                    <!--begin::Card footer-->
                                    <div class="card-footer flex-wrap pt-0">
                                        <a href="<?php echo $site_path ?>/view_role?id=<?php echo my_simple_crypt($single_value['id'], 'encrypt_1'); ?>" class="btn btn-light btn-active-primary my-1 me-2 ">View Role</a>
                                        <button type="button" class="btn btn-light btn-active-light-primary my-1 editRoleBtn" data-bs-toggle="modal" data-bs-target="#kt_modal_update_role"  data-role-id="<?php echo $single_value['id']; ?>"
                                                data-role-name="<?php echo $single_value['role_name']; ?>" data-role-modules='<?php echo json_encode(getAssignedPermissions($single_value['id'])); ?>'>Edit Role</button>
                                    </div>
                                    <!--end::Card footer-->
                                </div>
                                <!--end::Card-->
                            </div>
                                <?php }
                            } ?>
                    <!--end::Col-->
                    <!--begin::Add new card-->
                    <div class="ol-md-4">
                        <!--begin::Card-->
                        <div class="card h-md-100">
                            <!--begin::Card body-->
                            <div class="card-body d-flex flex-center">
                                <!--begin::Button-->
                                <button type="button" class="btn btn-clear d-flex flex-column flex-center" data-bs-toggle="modal" data-bs-target="#kt_modal_add_role">
                                    <!--begin::Illustration-->
                                    <img src="<?php echo $site_path; ?>/assets/media/illustrations/sketchy-1/4.png" alt="" class="mw-100 mh-150px mb-7" />
                                    <!--end::Illustration-->
                                    <!--begin::Label-->
                                    <div class="fw-bold fs-3 text-gray-600 text-hover-primary">Add New Role</div>
                                    <!--end::Label-->
                                </button>
                            </div>                            
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="kt_modal_add_role" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered mw-750px">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h2 class="fw-bold">Add a Role</h2>
                                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-roles-modal-action="close">
                                    <i class="ki-outline ki-cross fs-1"></i>
                                </div>
                            </div>>
                            <div class="modal-body scroll-y mx-lg-5 my-7">
                                <!--begin::Form-->
                                <form id="kt_modal_add_role_form" class="form" action="<?php echo $site_path; ?>/ajax/add-update-role">
                                    <!--begin::Scroll-->
                                    <div class="d-flex flex-column scroll-y me-n7 pe-7" id="kt_modal_add_role_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_add_role_header" data-kt-scroll-wrappers="#kt_modal_add_role_scroll" data-kt-scroll-offset="300px">
                                        <!--begin::Input group-->
                                        <div class="fv-row mb-10">
                                            <!--begin::Label-->
                                            <label class="fs-5 fw-bold form-label mb-2">
                                                <span class="required">Role name</span>
                                            </label>
                                            <!--end::Label-->
                                            <!--begin::Input-->
                                            <input class="form-control form-control-solid" placeholder="Enter a role name" name="role_name" />
                                            <!--end::Input-->
                                        </div>
                                        <div class="fv-row">                                            
                                            <label class="fs-5 fw-bold form-label mb-2">Role Permissions</label>
                                            <div class="table-responsive">
                                                <table class="table align-middle table-row-dashed fs-6 gy-5">
                                                    <tbody class="text-gray-600 fw-semibold">
                                                        <?php foreach ($permission_category_list as $cat) { ?>
                                                            <tr class="bg-light">
                                                                <td class="text-gray-800 fw-bold">
                                                                    <?= $cat['category_name']; ?>
                                                                </td>
                                                                <td>
                                                                    <label class="form-check form-check-custom form-check-solid">
                                                                        <input type="checkbox" class="form-check-input category-checkbox category-checkbox"
                                                                               data-category="<?= $cat['id']; ?>">
                                                                        <span class="form-check-label">Select all</span>
                                                                    </label>
                                                                </td>
                                                            </tr>
                                                            <?php
                                                            $permissions = array_filter($allPermissions, function ($p) use ($cat) {
                                                                return $p['category_id'] == $cat['id'];
                                                            });

                                                            foreach ($permissions as $perm) {?>
                                                                <tr>
                                                                    <td class="ps-10"><?= $perm['module_name']; ?></td>
                                                                    <td>
                                                                        <label class="form-check form-check-sm form-check-custom form-check-solid me-5">
                                                                            <input class="form-check-input permission-checkbox"
                                                                                   type="checkbox"
                                                                                   name="permissions[<?= $perm['id']; ?>]"
                                                                                   data-category="<?= $cat['id']; ?>"
                                                                                   value="1">
                                                                        </label>
                                                                    </td>
                                                                </tr>
                                                                    <?php }
                                                                 } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-center pt-15">
                                        <button type="reset" class="btn btn-light me-3" data-kt-roles-modal-action="cancel">Discard</button>
                                        <button id="addRoleBtn" type="submit" class="btn btn-primary" data-kt-roles-modal-action="submit">
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
                <div class="modal fade" id="kt_modal_update_role" tabindex="-1" aria-hidden="true">
                    <!--begin::Modal dialog-->
                    <div class="modal-dialog modal-dialog-centered mw-750px">
                        <!--begin::Modal content-->
                        <div class="modal-content">
                            <!--begin::Modal header-->
                            <div class="modal-header">
                                <!--begin::Modal title-->
                                <h2 class="fw-bold">Update Role</h2>
                                <!--end::Modal title-->
                                <!--begin::Close-->
                                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-roles-modal-action="close">
                                    <i class="ki-outline ki-cross fs-1"></i>
                                </div>
                                <!--end::Close-->
                            </div>
                            <!--end::Modal header-->
                            <!--begin::Modal body-->
                            <div class="modal-body scroll-y mx-5 my-7">
                                <!--begin::Form-->
                                <form id="kt_modal_update_role_form" class="form" action="<?php echo $site_path; ?>/ajax/add-update-role">
                                    <input type="hidden" name="role_id" value="<?php echo $role_id; ?>">
                                    <!--begin::Scroll-->
                                    <div class="d-flex flex-column scroll-y me-n7 pe-7" id="kt_modal_update_role_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_update_role_header" data-kt-scroll-wrappers="#kt_modal_update_role_scroll" data-kt-scroll-offset="300px">
                                        <!--begin::Input group-->
                                        <div class="fv-row mb-10">
                                            <!--begin::Label-->
                                            <label class="fs-5 fw-bold form-label mb-2">
                                                <span class="required">Role name</span>
                                            </label>
                                            <!--end::Label-->
                                            <!--begin::Input-->
                                            <input class="form-control form-control-solid" placeholder="Enter a role name" name="role_name" value="<?php echo $role_name; ?>" />
                                            <!--end::Input-->
                                        </div>
                                        <!--end::Input group-->
                                        <!--begin::Permissions-->
                                        <div class="fv-row">
                                            <!--begin::Label-->
                                            <label class="fs-5 fw-bold form-label mb-2">Role Permissions</label>
                                            <!--end::Label-->
                                            <!--begin::Table wrapper-->
                                            <div class="table-responsive">
                                                <!--begin::Table-->
                                                <table class="table align-middle table-row-dashed fs-6 gy-5">
                                                    <tbody class="text-gray-600 fw-semibold">
                                                                <?php foreach ($permission_category_list as $cat) { ?>
                                                            <!-- Category Row -->
                                                            <tr class="bg-light">
                                                                <td class="text-gray-800 fw-bold">
                                                                    <?= $cat['category_name']; ?>
                                                                </td>
                                                                <td>
                                                                    <label class="form-check form-check-custom form-check-solid">
                                                                        <input type="checkbox" class="form-check-input category-checkbox category-checkbox"
                                                                               data-category="<?= $cat['id']; ?>">
                                                                        <span class="form-check-label">Select all</span>
                                                                    </label>
                                                                </td>
                                                            </tr>
                                                            <?php
                                                            // get permissions for this category
                                                            $permissions = array_filter($allPermissions, function ($p) use ($cat) {
                                                                return $p['category_id'] == $cat['id'];
                                                            });

                                                            foreach ($permissions as $perm) {
                                                                ?>
                                                                <!-- Permission Row -->
                                                                <tr>
                                                                    <td class="ps-10"><?= $perm['module_name']; ?></td>
                                                                    <td>
                                                                        <label class="form-check form-check-sm form-check-custom form-check-solid me-5">
                                                                            <input class="form-check-input permission-checkbox"
                                                                                   type="checkbox"
                                                                                   name="permissions[<?= $perm['id']; ?>]"
                                                                                   data-category="<?= $cat['id']; ?>"
                                                                                   data-permission-id="<?= $perm['id']; ?>"
                                                                                   value="1">
                                                                        </label>
                                                                    </td>
                                                                </tr>
                                                                <?php } ?>
                                                            <?php } ?>
                                                    </tbody>
                                                </table>
                                                <!--end::Table-->
                                            </div>
                                            <!--end::Table wrapper-->
                                        </div>
                                        <!--end::Permissions-->
                                    </div>
                                    <!--end::Scroll-->
                                    <!--begin::Actions-->
                                    <div class="text-center pt-15">
                                        <button type="reset" class="btn btn-light me-3" data-kt-roles-modal-action="cancel">Discard</button>
                                        <button type="submit" class="btn btn-primary" data-kt-roles-modal-action="submit">
                                            <span class="indicator-label">Submit</span>
                                            <span class="indicator-progress">Please wait... 
                                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                        </button>
                                        <button type="button" data-role-id="<?php echo $single_value['id']; ?>" class="btn btn-light me-3 deleteRoleBtn" data-kt-roles-modal-action="delete">Delete</button>
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
                <!--end::Modal - Update role-->
                <!--end::Modals-->
            </div>
            <!--end::Content container-->
        </div>
        <!--end::Content-->
        <!--end::Content wrapper-->
        <!--begin::Footer-->
         <?php include("includes/footer.php"); ?>
    </div>
    <!--end:::Main-->
</div>
<!--end::Wrapper-->
</div>
<!--end::Page-->
</div>
<script>var hostUrl = "assets/";</script>
<script src="<?php echo $site_path; ?>/assets/plugins/global/plugins.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/scripts.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/custom/apps/user-management/roles/list/add.js?v=<?php echo time(); ?>"></script>
<script src="<?php echo $site_path; ?>/assets/js/custom/apps/user-management/roles/list/update-role.js?v=<?php echo time(); ?>"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll(".editRoleBtn").forEach(function (button) {
            button.addEventListener("click", function () {
               let roleId = $(this).data('role-id');
                let roleName = $(this).data('role-name');
                let assignedModules = $(this).data('role-modules'); // JSON array

                // Fill input with role name
                document.querySelector("#kt_modal_update_role_form input[name='role_name']").value = roleName;

                document.querySelectorAll("#kt_modal_update_role_form input[name='modules[]']").forEach(chk => {
                    chk.checked = false;
                });

                // Re-check assigned modules
                if (Array.isArray(assignedModules)) {
        assignedModules.forEach(function (permId) {
            $('.permission-checkbox[data-permission-id="' + permId + '"]').prop('checked', true);
                });
    }

                // If needed, store ID in hidden field
                let hiddenId = document.querySelector("#kt_modal_update_role_form input[name='role_id']");
                if (!hiddenId) {
                    hiddenId = document.createElement("input");
                    hiddenId.type = "hidden";
                    hiddenId.name = "role_id";
                    document.querySelector("#kt_modal_update_role_form").appendChild(hiddenId);
                }
                hiddenId.value = roleId;
            });
        });
    });
    document.querySelectorAll(".category-checkbox").forEach(cat => {
        cat.addEventListener("change", function () {
            let catId = this.getAttribute("data-category");
            let children = document.querySelectorAll(`.permission-checkbox[data-category="${catId}"]`);
            children.forEach(ch => ch.checked = this.checked);
        });
    });
</script>
</body>
<!--end::Body-->
</html>