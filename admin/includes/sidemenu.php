<?php
$uri = $_SERVER['REQUEST_URI'];
$uri = strtok($uri, '?');
$page = basename($uri);
$page = strtok($page, '?');
$allowedModules = getUserModules($typee_id);
//
$user_history_sql = "SELECT login_datetime,ip_address FROM login_history where user_id = {$uid} ORDER BY id DESC LIMIT 1";
$user_history_res = $con->query($user_history_sql);
if ($user_history_res && $user_history_res->num_rows > 0) {
    $user_history_row = $user_history_res->fetch_assoc();
    $utcTime = new DateTime($user_history_row['login_datetime'], new DateTimeZone('UTC'));
    $utcTime->setTimezone(new DateTimeZone('Asia/Kolkata'));
    $last_login_time = $utcTime->format('d-m-Y h:i A');

    $last_login_ip = $user_history_row['ip_address'];
}
if ($remaining_seconds < 0) {
    $remaining_seconds = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
    <!--begin::Head-->
    <head>
        <base href="../../" />
        <title><?php echo $softtitle; ?></title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="shortcut icon" href="<?php echo $site_path; ?>/assets/media/favicon.png" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link href="<?php echo $site_path; ?>/assets/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $site_path; ?>/assets/plugins/global/plugins.bundle.css?v=<?php echo time(); ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo $site_path; ?>/assets/css/style.bundle.css?v=<?php echo time(); ?>" rel="stylesheet" type="text/css" />
        <style>
            /* 1. Main Text and Section Heading Color */
            #kt_app_sidebar .menu-item .menu-link .menu-title,
            #kt_app_sidebar .menu-section {
                color: #ffffff !important; /* Pure white text */
                font-weight: 500;
            }

            /* 2. Icon Color */
            #kt_app_sidebar .menu-item .menu-link .menu-icon i {
                color: #ffffff !important;
            }

            /* 3. Arrow (Bullet) Color */
            #kt_app_sidebar .menu-item .menu-link .menu-arrow:after {
                background-color: #ffffff !important;
            }

            /* 4. Hover State Styles */
            #kt_app_sidebar .menu-item:hover:not(.here) > .menu-link {
                background-color: rgba(255, 255, 255, 0.1) !important; /* Subtle light overlay */
                transition: color 0.2s ease, background-color 0.2s ease;
            }



            /* 5. Active (Selected) Item Style */
            /* This handles the 'Dashboard' white background item in your screenshot */
            #kt_app_sidebar .menu-item.here > .menu-link,
            #kt_app_sidebar .menu-item.active > .menu-link {
                background-color: #ffffff !important;
            }

            #kt_app_sidebar .menu-item.here > .menu-link .menu-title,
            #kt_app_sidebar .menu-item.here > .menu-link .menu-icon i {
                color: #1e1e2d !important; /* Dark text for the active white button */
            }
            .app-header-center {
                pointer-events: none; /* prevents hover issues */
            }

            .bg-dark {
                background-color: #6C3520 !important;
            }


        </style>
    </head>
    <!--end::Head-->
    <!--begin::Body-->
    <body id="kt_app_body" data-kt-app-header-fixed="true" data-kt-app-header-fixed-mobile="true" data-kt-app-sidebar-enabled="true" data-kt-app-sidebar-fixed="true" data-kt-app-sidebar-push-toolbar="true" data-kt-app-sidebar-push-footer="true" data-kt-app-toolbar-enabled="true" data-kt-app-aside-enabled="true" data-kt-app-aside-fixed="false" class="app-default">

        <div class="d-flex flex-column flex-root app-root" id="kt_app_root">
            <div class="app-page flex-column flex-column-fluid" id="kt_app_page">
                <div id="kt_app_header" class="app-header">
                    <div class="app-header-logo d-flex align-items-center ps-lg-10 gap-4 gap-lg-6">
                        <a href="<?php echo $site_path; ?>/dashboard">
                            <img alt="Logo" src="<?php echo $site_path; ?>/assets/media/logos/logo.png" class="h-20px h-lg-25px theme-light-show d-none d-sm-inline" />
                        </a>
                    </div>

                    <div class="app-header-wrapper">
                        <div class="app-container container-fluid">
                            <div class="app-navbar-item flex-lg-grow-1 me-1 me-lg-0"></div>

                            <div class="app-navbar flex-shrink-0">
                                <div class="app-navbar-item ms-1 ms-lg-3 me-2 me-lg-4">
                                    <div class="d-flex align-items-center gap-3 flex-wrap" id="kt_header_filters">
                                        <select class="form-select form-select-sm w-150px" id="filterCompany" data-filter="company_id">
                                           <option value="">All Companies</option>
                                           <option value="1">BullionKnot</option>
                                           <option value="2">Under3k</option>
                                        </select>
                                        <select class="form-select form-select-sm w-150px" id="filterDepartment" data-filter="department_id">
                                            <option value="">All Departments</option>
                                            <?php
                                            $dept_res = $con->query("SELECT id, department_name FROM departments ORDER BY department_name ASC");
                                            while ($dept = $dept_res->fetch_assoc()):
                                            ?>
                                                <option value="<?= $dept['id'] ?>"><?= htmlspecialchars($dept['department_name']) ?></option>
                                            <?php endwhile; ?>
                                        </select>
                                        <select class="form-select form-select-sm w-150px" id="filterStaff" data-filter="staff_id">
                                            <option value="">All Staff</option>
                                            <?php
                                            $staff_res = $con->query("SELECT id, name FROM user ORDER BY name ASC");
                                            while ($staff = $staff_res->fetch_assoc()):
                                            ?>
                                                <option value="<?= $staff['id'] ?>"><?= htmlspecialchars($staff['name']) ?></option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="app-navbar-item ms-1 ms-lg-3 me-2 me-lg-4" id="kt_header_notifications_toggle">
                                    <div class="cursor-pointer symbol symbol-35px symbol-md-40px position-relative"
                                         data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
                                         data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                                        <i class="ki-outline ki-notification-bing fs-1"></i>
                                        <span class="badge badge-circle badge-danger position-absolute top-0 start-100 translate-middle fs-9 d-none" id="kt_notification_badge">0</span>
                                    </div>

                                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-325px" data-kt-menu="true">
                                        <div class="d-flex justify-content-between align-items-center px-5 py-2">
                                            <div class="fw-bold fs-5">Notifications</div>
                                        </div>
                                        <div class="separator mb-2"></div>
                                        <div class="scroll-y mh-300px px-3" id="kt_notification_list">
                                            <div class="text-muted text-center fs-7 py-5">Loading...</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="app-navbar-item ms-1 ms-lg-3 me-2 me-lg-6" id="kt_header_user_menu_toggle">
                                    <div class="cursor-pointer symbol symbol-35px symbol-md-40px" data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                                        <?php if ($profile_picture) { ?>
                                            <img class="symbol symbol-circle symbol-35px symbol-md-40px" alt="Logo" src="<?php echo $profile_picture; ?>" />
                                        <?php } else { ?> 
                                            <img alt="Logo" class="symbol symbol-circle symbol-35px symbol-md-40px"  src="<?php echo $site_path; ?>/assets/media/misc/1.png" />
                                        <?php } ?>
                                    </div>
                                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px" data-kt-menu="true">
                                        <div class="menu-item px-3">
                                            <div class="menu-content d-flex align-items-center px-3">
                                                <div class="symbol symbol-50px me-5">
                                                    <?php if ($profile_picture) { ?>
                                                        <img alt="Logo" src="<?php echo $profile_picture; ?>" />
                                                    <?php } else { ?> 
                                                        <img alt="Logo" src="<?php echo $site_path; ?>/assets/media/misc/1.png" />
                                                    <?php } ?>
                                                </div>
                                                <div class="d-flex flex-column">
                                                    <div class="fw-bold d-flex align-items-center fs-5"><?php echo $uname; ?>
                                                        <span class="badge badge-light-success fw-bold fs-8 px-2 py-1 ms-2"><?php echo $designation; ?></span></div>
                                                    <a href="#" class="fw-semibold text-muted text-hover-primary fs-7"><?php echo $uemail; ?></a>
                                                </div>
                                                <!--end::Username-->
                                            </div>
                                        </div>
                                        <!--end::Menu item-->
                                        <!--begin::Menu separator-->
                                        <div class="separator my-2"></div>
                                        <!--end::Menu separator-->

                                        <!--begin::Menu item-->
                                        <div class="menu-item px-5">
                                            <a href="<?php echo $site_path ?>/profile-overview" class="menu-link px-5">My Profile</a>
                                        </div>
                                        <div class="menu-item px-5">
                                            <a href="<?php echo $site_path ?>/logout" class="menu-link px-5">Sign Out</a>
                                        </div>

                                    </div>
                                </div>
                                <div class="app-navbar-item ms-1 ms-lg-3 me-n4 d-flex d-lg-none">
                                    <button id="kt_app_sidebar_mobile_toggle" class="btn btn-icon w-35px h-35px w-md-40px h-md-40px">
                                        <i class="ki-outline ki-burger-menu-2 fs-2"></i>
                                    </button>
                                </div>
                            </div>
                            <!--end::Navbar-->
                        </div>
                    </div>
                </div>
                <!--end::Header-->
                <!--begin::Wrapper-->
                <div class="app-wrapper d-flex" id="kt_app_wrapper">
                    <!--begin::Sidebar-->
                    <div id="kt_app_sidebar" class="app-sidebar flex-column bg-dark" data-kt-drawer="true" data-kt-drawer-name="app-sidebar" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="auto" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">
                        <div id="kt_app_sidebar_wrapper" class="app-sidebar-wrapper hover-scroll-y mx-3 my-2" data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-height="auto" data-kt-scroll-dependencies="#kt_app_sidebar_header" data-kt-scroll-offset="5px">
                            <!--begin::Secondary menu-->
                            <div id="kt_app_sidebar_menu" class="menu menu-sub-indention menu-rounded menu-column fw-semibold fs-6 py-4 py-lg-6 px-2" data-kt-menu="true">
                                <?php
                                renderSidebar($site_path, $allowedModules, $page);
                                ?>
                            </div>
                        </div>
                    </div>
                    <!--end::Sidebar-->