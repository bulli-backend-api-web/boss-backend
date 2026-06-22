<?php
include("config/database.php");
include("config/auth_check.php");
include("includes/sidemenu.php");
$user_details = getUserDetailsByID($uid);

$loginHistory = getData("login_history", ["login_datetime", "ip_address", "browser_name"], ["user_id" => $uid], "", "id DESC");
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
                        <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">Logs & Notifcations</h1>
                        <!--end::Title-->
                        <!--begin::Breadcrumb-->
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <a href="index.html" class="text-muted text-hover-primary">Home</a>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">Account</li>
                            <!--end::Item-->
                        </ul>
                        <!--end::Breadcrumb-->
                    </div>
                    <!--end::Page title-->
                    <!--begin::Actions-->
                    <div class="d-flex align-items-center gap-2 gap-lg-3">
                        <a href="#" class="btn btn-flex btn-outline btn-color-gray-700 btn-active-color-primary bg-body h-40px fs-7 fw-bold" data-bs-toggle="modal" data-bs-target="#kt_modal_view_users">Add Member</a>
                        <a href="#" class="btn btn-flex btn-primary h-40px fs-7 fw-bold" data-bs-toggle="modal" data-bs-target="#kt_modal_create_campaign">New Campaign</a>
                    </div>
                    <!--end::Actions-->
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
                <!--begin::Navbar-->
                <div class="card mb-5 mb-xl-10">
                    <div class="card-body pt-9 pb-0">
                        <!--begin::Details-->
                        <div class="d-flex flex-wrap flex-sm-nowrap">
                            <!--begin: Pic-->
                            <div class="me-7 mb-4">
                                <div class="symbol symbol-100px symbol-lg-160px symbol-fixed position-relative">
                                     <?php if(!empty($user_details[0]['profile_picture'])){
                                        $profiel_pic_url = $user_details[0]['profile_picture'];
                                    }else{
                                        $profiel_pic_url = $site_path."/assets/media/default_logo.png";
                                    } ?>
                                    <img src="<?php echo $profiel_pic_url; ?>" alt="image" />
                                    <div class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border border-4 border-body h-20px w-20px"></div>
                                </div>
                            </div>
                            <!--end::Pic-->
                            <!--begin::Info-->
                            <div class="flex-grow-1">
                                <!--begin::Title-->
                                <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                                    <!--begin::User-->
                                    <div class="d-flex flex-column">
                                        <!--begin::Name-->
                                        <div class="d-flex align-items-center mb-2">
                                            <a href="#" class="text-gray-900 text-hover-primary fs-2 fw-bold me-1"><?php echo $user_details[0]['name']; ?></a>
                                            <a href="#">
                                                <i class="ki-outline ki-verify fs-1 text-primary"></i>
                                            </a>
                                        </div>
                                        <!--end::Name-->
                                        <!--begin::Info-->
                                        <div class="d-flex flex-wrap fw-semibold fs-6 mb-4 pe-2">
                                            <a href="#" class="d-flex align-items-center text-gray-500 text-hover-primary me-5 mb-2">
                                                <i class="ki-outline ki-profile-circle fs-4 me-1"></i><?php echo $user_details[0]['typee']; ?></a>
                                            <a href="#" class="d-flex align-items-center text-gray-500 text-hover-primary me-5 mb-2">
                                                <i class="ki-outline ki-geolocation fs-4 me-1"></i><?php echo $user_details[0]['address']; ?></a>
                                            <a href="#" class="d-flex align-items-center text-gray-500 text-hover-primary mb-2">
                                                <i class="ki-outline ki-sms fs-4"></i><?php echo $user_details[0]['email']; ?></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end::Info-->
                        </div>
                        <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold">
                            <li class="nav-item mt-2">
                                <a class="nav-link text-active-primary ms-0 me-10 py-5" href="<?php echo $site_path; ?>/profile-overview">Overview</a>
                            </li>
                            <li class="nav-item mt-2">
                                <a class="nav-link text-active-primary ms-0 me-10 py-5" href="<?php echo $site_path; ?>/settings">Settings</a>
                            </li>
                            <li class="nav-item mt-2">
                                <a class="nav-link text-active-primary ms-0 me-10 py-5 active" href="<?php echo $site_path; ?>/users-logs">Logs</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <!--end::Navbar-->
                <!--begin::Login sessions-->
                <div class="card mb-5 mb-lg-10">
                    <!--begin::Card header-->
                    <div class="card-header">
                        <!--begin::Heading-->
                        <div class="card-title">
                            <h3>Login Sessions</h3>
                        </div>
                        <!--end::Heading-->
                        <!--begin::Toolbar-->
                        <div class="card-toolbar">

                        </div>
                        <!--end::Toolbar-->
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body p-0">
                        <!--begin::Table wrapper-->
                        <div class="table-responsive">
                            <!--begin::Table-->
                            <table class="table align-middle table-row-bordered table-row-solid gy-4 gs-9">
                                <!--begin::Thead-->
                                <thead class="border-gray-200 fs-5 fw-semibold bg-lighten">
                                    <tr>
                                        <th class="min-w-250px">Location</th>
                                        <th class="min-w-100px">Status</th>
                                        <th class="min-w-150px">Device</th>
                                        <th class="min-w-150px">IP Address</th>
                                        <th class="min-w-150px">Time</th>
                                    </tr>
                                </thead>
                                <!--end::Thead-->
                                <!--begin::Tbody-->
                                <tbody class="fw-6 fw-semibold text-gray-600">
                                    <?php
                                    if ($loginHistory) {
                                        foreach ($loginHistory as $single_session) {
                                            ?>
                                            <tr>
                                                <td>
                                                    <a href="#" class="text-hover-primary text-gray-600">India</a>
                                                </td>
                                                <td>
                                                    <span class="badge badge-light-success fs-7 fw-bold">OK</span>
                                                </td>
                                                <td><?php echo $single_session['browser_name']; ?></td>
                                                <td><?php echo $single_session['ip_address']; ?></td>
                                                <td><?php echo timeAgo(date('Y-m-d', strtotime($single_session['login_datetime']))); ?></td>
                                            </tr>
    <?php }
} ?>
                                </tbody>
                                <!--end::Tbody-->
                            </table>
                            <!--end::Table-->
                        </div>
                        <!--end::Table wrapper-->
                    </div>
                </div>

                <!--begin::Card-->
                <!--end:::Main-->
            </div>
            <!--end::Wrapper-->
        </div>
        <!--end::Page-->
    </div>
    <!--end::App-->
    <!--begin::Javascript-->
    <script>var hostUrl = "assets/";</script>
    <!--begin::Global Javascript Bundle(mandatory for all pages)-->
    <script src="<?php echo $site_path; ?>/assets/plugins/global/plugins.bundle.js"></script>
    <script src="<?php echo $site_path; ?>/assets/js/scripts.bundle.js"></script>
    <!--end::Global Javascript Bundle-->
    <!--begin::Vendors Javascript(used for this page only)-->
    <script src="<?php echo $site_path; ?>/assets/plugins/custom/datatables/datatables.bundle.js"></script>
    <!--end::Vendors Javascript-->
    <!--begin::Custom Javascript(used for this page only)-->
    <script src="<?php echo $site_path; ?>/assets/js/custom/pages/user-profile/general.js"></script>
    <script src="<?php echo $site_path; ?>/assets/js/widgets.bundle.js"></script>
    <script src="<?php echo $site_path; ?>/assets/js/custom/widgets.js"></script>
    <script src="<?php echo $site_path; ?>/assets/js/custom/utilities/modals/upgrade-plan.js"></script>
    <script src="<?php echo $site_path; ?>/assets/js/custom/utilities/modals/create-campaign.js"></script>
    <script src="<?php echo $site_path; ?>/assets/js/custom/utilities/modals/offer-a-deal/type.js"></script>
    <script src="<?php echo $site_path; ?>/assets/js/custom/utilities/modals/offer-a-deal/details.js"></script>
    <script src="<?php echo $site_path; ?>/assets/js/custom/utilities/modals/offer-a-deal/finance.js"></script>
    <script src="<?php echo $site_path; ?>/assets/js/custom/utilities/modals/offer-a-deal/complete.js"></script>
    <script src="<?php echo $site_path; ?>/assets/js/custom/utilities/modals/offer-a-deal/main.js"></script>
    <script src="<?php echo $site_path; ?>/assets/js/custom/utilities/modals/create-app.js"></script>
    <script src="<?php echo $site_path; ?>/assets/js/custom/utilities/modals/users-search.js"></script>
    <!--end::Custom Javascript-->
    <!--end::Javascript-->
</body>
<!--end::Body-->
</html>