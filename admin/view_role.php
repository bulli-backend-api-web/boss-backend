<?php
include("config/database.php");
include("config/auth_check.php");
include("includes/sidemenu.php");
$role_id = my_simple_crypt($_GET['id'], 'decrypt_1');
$roleDetails = getData("role", ["role_name", "slug", "id"], ["id" => $role_id], "", "id DESC");
$role_name = !empty($roleDetails[0]['role_name']) ? $roleDetails[0]['role_name'] : "";
$role_slug = !empty($roleDetails[0]['slug']) ? $roleDetails[0]['slug'] : "";
$getUsersByRole = getUsersByRole($role_id);
$role_id = !empty($roleDetails[0]['id']) ? $roleDetails[0]['id'] : "";
$totalAssigneduser = !empty($getUsersByRole[$role_slug]['list']) ? count($getUsersByRole[$role_slug]['list']) : 0;

$userListArray = !empty($getUsersByRole[$role_slug]['list']) ? $getUsersByRole[$role_slug]['list'] : [];
$allModules = getAllModules();
$assignedModules = getAssignedPermissions($role_id);
?>
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_toolbar" class="app-toolbar pt-7 pt-lg-10">
            <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
                <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                    <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                        <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">View Role Details</h1>
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                            <li class="breadcrumb-item text-muted">
                                <a href="<?php echo $site_path ?>dashboard" class="text-muted text-hover-primary">Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted">User Management</li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted">Roles</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div id="kt_app_content" class="app-content">
            <div id="kt_app_content_container" class="app-container container-fluid">
                <div class="d-flex flex-column flex-lg-row">
                    <div class="flex-column flex-lg-row-auto w-100 w-lg-200px w-xl-300px mb-10">
                        <div class="card card-flush">
                            <div class="card-header">
                                <div class="card-title">
                                    <h2 class="mb-0"><?php echo $role_name; ?></h2>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                
                            </div>
                            <div class="card-footer pt-0">
                                <button type="button" class="btn btn-light btn-active-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_update_role">Edit Role</button>
                            </div>
                        </div>
                        <div class="modal fade" id="kt_modal_update_role" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered mw-750px">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h2 class="fw-bold">Update Role</h2>
                                        <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-roles-modal-action="close">
                                            <i class="ki-outline ki-cross fs-1"></i>
                                        </div>
                                    </div>
                                    <div class="modal-body scroll-y mx-5 my-7">
                                        <form id="kt_modal_update_role_form" class="form" action="<?php echo $site_path ?>/add-update-role">
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
                                                            <!--begin::Table body-->
                                                            <tbody class="text-gray-600 fw-semibold">
                                                                <!--begin::Table row-->
                                                                <tr>
                                                                    <td class="text-gray-800">Administrator Access 
                                                                        <span class="ms-1" data-bs-toggle="tooltip" title="Allows a full access to the system">
                                                                            <i class="ki-outline ki-information-5 text-gray-500 fs-6"></i>
                                                                        </span></td>
                                                                    <td>
                                                                        <!--begin::Checkbox-->
                                                                        <label class="form-check form-check-sm form-check-custom form-check-solid me-9">
                                                                            <input class="form-check-input" type="checkbox" value="" id="kt_roles_select_all" />
                                                                            <span class="form-check-label" for="kt_roles_select_all">Select all</span>
                                                                        </label>
                                                                        <!--end::Checkbox-->
                                                                    </td>
                                                                </tr>
                                                                <?php
                                                                if ($allModules) {
                                                                    foreach ($allModules as $single_module) {
                                                                        $checked = in_array($single_module['id'], $assignedModules) ? "checked" : "";
                                                                        ?>
                                                                        <tr>
                                                                            <!--begin::Label-->
                                                                            <td class="text-gray-800"><?php echo $single_module['module_name']; ?></td>
                                                                            <!--end::Label-->
                                                                            <!--begin::Options-->
                                                                            <td>
                                                                                <!--begin::Wrapper-->
                                                                                <div class="d-flex">
                                                                                    <!--begin::Checkbox-->
                                                                                    <label class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
                                                                                        <input class="form-check-input" type="checkbox" value="<?php echo $single_module['id']; ?>" name="modules[]" <?php echo $checked; ?> />
                                                                                    </label>
                                                                                    <!--end::Checkbox-->
                                                                                </div>
                                                                                <!--end::Wrapper-->
                                                                            </td>
                                                                            <!--end::Options-->
                                                                        </tr>
                                                                    <?php }
                                                                } ?>
                                                            </tbody>
                                                            <!--end::Table body-->
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
                        <!--end::Modal-->
                    </div>
                    <!--end::Sidebar-->
                    <!--begin::Content-->
                    <div class="flex-lg-row-fluid ms-lg-10">
                        <!--begin::Card-->
                        <div class="card card-flush mb-6 mb-xl-9">
                            <!--begin::Card header-->
                            <div class="card-header pt-5">
                                <!--begin::Card title-->
                                <div class="card-title">
                                    <h2 class="d-flex align-items-center">Users Assigned
                                        <span class="text-gray-600 fs-6 ms-1">(<?php echo $totalAssigneduser; ?>)</span></h2>
                                </div>
                                <!--end::Card title-->
                                <!--begin::Card toolbar-->
                                <div class="card-toolbar">
                                    <!--begin::Search-->
                                    <div class="d-flex align-items-center position-relative my-1" data-kt-view-roles-table-toolbar="base">
                                        <i class="ki-outline ki-magnifier fs-1 position-absolute ms-6"></i>
                                        <input type="text" data-kt-roles-table-filter="search" class="form-control form-control-solid w-250px ps-15" placeholder="Search Users" />
                                    </div>
                                    <!--end::Search-->
                                    <!--begin::Group actions-->
                                    <div class="d-flex justify-content-end align-items-center d-none" data-kt-view-roles-table-toolbar="selected">
                                        <div class="fw-bold me-5">
                                            <span class="me-2" data-kt-view-roles-table-select="selected_count"></span>Selected</div>
                                        <button type="button" class="btn btn-danger" data-kt-view-roles-table-select="delete_selected">Delete Selected</button>
                                    </div>
                                    <!--end::Group actions-->
                                </div>
                                <!--end::Card toolbar-->
                            </div>
                            <!--end::Card header-->
                            <!--begin::Card body-->
                            <div class="card-body pt-0">
                                <!--begin::Table-->
                                <table class="table align-middle table-row-dashed fs-6 gy-5 mb-0" id="kt_roles_view_table">
                                    <thead>
                                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                            <th class="w-10px pe-2">
                                                <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                                    <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_roles_view_table .form-check-input" value="1" />
                                                </div>
                                            </th>
                                            <th class="min-w-50px">ID</th>
                                            <th class="min-w-150px">User</th>
                                            <th class="min-w-125px">Joined Date</th>
                                            <th class="text-end min-w-100px">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="fw-semibold text-gray-600">
                                        <?php if ($userListArray) {
                                            foreach ($userListArray as $singal_user) {
                                                ?>
                                                <tr>
                                                    <td>
                                                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                            <input class="form-check-input" type="checkbox" value="1" />
                                                        </div>
                                                    </td>
                                                    <td><?php echo $singal_user['id']; ?></td>
                                                    <td class="d-flex align-items-center">
                                                        <!--begin:: Avatar -->
                                                        <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                            <a href="apps/user-management/users/view.html">
                                                                <div class="symbol-label">
                                                                    <img src="<?php echo $site_path ?>/assets/media/avatars/300-6.jpg" alt="Emma Smith" class="w-100" />
                                                                </div>
                                                            </a>
                                                        </div>
                                                        <!--end::Avatar-->
                                                        <!--begin::User details-->
                                                        <div class="d-flex flex-column">
                                                            <a href="apps/user-management/users/view.html" class="text-gray-800 text-hover-primary mb-1"><?php echo $singal_user['name']; ?></a>
                                                            <span><?php echo $singal_user['email']; ?></span>
                                                        </div>
                                                        <!--begin::User details-->
                                                    </td>
                                                    <td>NA</td>
                                                    <td class="text-end">
                                                        <a href="#" class="btn btn-sm btn-light btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions 
                                                            <i class="ki-outline ki-down fs-5 m-0"></i></a>
                                                        <!--begin::Menu-->
                                                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                            <!--begin::Menu item-->
                                                            <div class="menu-item px-3">
                                                                <a href="apps/user-management/users/view.html" class="menu-link px-3">View</a>
                                                            </div>
                                                            <!--end::Menu item-->
                                                            <!--begin::Menu item-->
                                                            <div class="menu-item px-3">
                                                                <a href="#" class="menu-link px-3" data-kt-roles-table-filter="delete_row">Delete</a>
                                                            </div>
                                                            <!--end::Menu item-->
                                                        </div>
                                                        <!--end::Menu-->
                                                    </td>
                                                </tr>
    <?php }
} ?>
                                    </tbody>
                                </table>
                                <!--end::Table-->
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Card-->
                    </div>
                    <!--end::Content-->
                </div>
                <!--end::Layout-->
            </div>
            <!--end::Content container-->
        </div>
        <!--end::Content-->
    </div>
    <!--end::Content wrapper-->
    <!--begin::Footer-->
    <div id="kt_app_footer" class="app-footer">
        <!--begin::Footer container-->
        <div class="app-container container-fluid d-flex flex-column flex-md-row flex-center flex-md-stack py-3">
            <div class="text-gray-900 order-2 order-md-1">
                <span class="text-muted fw-semibold me-1"><?php echo date('Y'); ?>&copy;</span>
                <a href="https://vastranand.in" target="_blank" class="text-gray-800 text-hover-primary">vastranand. All Rights Reserved.Powered by Vastranand Pvt Ltd.</a>
            </div>
        </div>
    </div>
    <!--end::Footer-->
</div>
<!--end:::Main-->

</div>
<!--end::Wrapper-->
</div>
<!--end::Page-->
</div>
<!--end::App-->
<!--begin::Drawers-->


<!--end::Drawers-->
<!--begin::Scrolltop-->
<div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
    <i class="ki-outline ki-arrow-up"></i>
</div>
<!--end::Scrolltop-->

<!--end::Modals-->
<!--begin::Javascript-->
<script>var hostUrl = "assets/";</script>
<!--begin::Global Javascript Bundle(mandatory for all pages)-->
<script src="<?php echo $site_path ?>/assets/plugins/global/plugins.bundle.js"></script>
<script src="<?php echo $site_path ?>/assets/js/scripts.bundle.js"></script>
<!--end::Global Javascript Bundle-->
<!--begin::Vendors Javascript(used for this page only)-->
<script src="<?php echo $site_path ?>/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<!--end::Vendors Javascript-->
<!--begin::Custom Javascript(used for this page only)-->
<script src="<?php echo $site_path ?>/assets/js/custom/apps/user-management/roles/view/view.js"></script>
<script src="<?php echo $site_path ?>/assets/js/custom/apps/user-management/roles/view/update-role.js?v=<?php echo time(); ?>"></script>
<script src="<?php echo $site_path ?>/assets/js/widgets.bundle.js"></script>
<script src="<?php echo $site_path ?>/assets/js/custom/widgets.js"></script>
<script src="<?php echo $site_path ?>/assets/js/custom/apps/chat/chat.js"></script>
<script src="<?php echo $site_path ?>/assets/js/custom/utilities/modals/upgrade-plan.js"></script>
<script src="<?php echo $site_path ?>/assets/js/custom/utilities/modals/create-campaign.js"></script>
<script src="<?php echo $site_path ?>/assets/js/custom/utilities/modals/users-search.js"></script>
<!--end::Custom Javascript-->
<!--end::Javascript-->
</body>
<!--end::Body-->
</html>