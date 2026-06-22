<?php
include("config/database.php");
include("config/auth_check.php");
include("includes/sidemenu.php");


$all_department_list = getAllDepartments();

$user_id = isset($_GET['id']) ? my_simple_crypt($_GET['id'], 'decrypt_1') : "";
$userDetails = getData("user", ["id", "name", "typee", "email", "address", "state", "pincode", "country", "mobile", "profile_picture", "birth_date", "joining_date", "remarks", "status","department_id","company_id","face_attendance"], ["id" => $user_id], "", "id DESC");

$currentEmail = !empty($userDetails) ? $userDetails[0]['email'] : "";
$Fullname = !empty($userDetails) ? $userDetails[0]['name'] : "";
$address = !empty($userDetails) ? $userDetails[0]['address'] : "";
$state = !empty($userDetails) ? $userDetails[0]['state'] : "";
$pincode = !empty($userDetails) ? $userDetails[0]['pincode'] : "";
$country = !empty($userDetails) ? $userDetails[0]['country'] : "";
$mobile_number = !empty($userDetails) ? $userDetails[0]['mobile'] : "";
$birth_date = !empty($userDetails) ? $userDetails[0]['birth_date'] : "";
$joining_date = !empty($userDetails) ? $userDetails[0]['joining_date'] : "";
$profile_image = !empty($userDetails) ? $userDetails[0]['profile_picture'] : "";
$remarks = !empty($userDetails) ? $userDetails[0]['remarks'] : "";
$status = $userDetails[0]['status'];
$department_id = !empty($userDetails[0]['department_id']) ? $userDetails[0]['department_id'] : "";
$company_id = !empty($userDetails[0]['company_id']) ? $userDetails[0]['company_id'] : '';
$face_attendance = !empty($userDetails[0]['face_attendance']) ? $userDetails[0]['face_attendance'] : '';
$all_activity = [];
$all_otp_history = [];
$user_passbook = [];
$loginHistory = getData("login_history", ["login_datetime", "ip_address", "browser_name", "logout_datetime"], ["user_id" => $user_id], "", "id DESC");

$lastLogin = isset($loginHistory[0]['login_datetime']) ? date('d M Y, h:i A', strtotime($loginHistory[0]['login_datetime'])) : "N/A";
$roleList = getUniqueRoles();

$status_names = [
    1 => 'Pending Order',
    2 => 'Confirm Order',
    3 => 'Dispatched Order',
    4 => 'Rejected Order',
    5 => 'Delivered Closed Order',
    6 => 'Cancel Order',
    7 => 'RTO Delivered',
];

$brand_name = [
    1 => "Bullion Knot",
    2 => "Under3k",
    3 => "All"
];

$scan_app_modules = getScanAppModules();
$all_assinged_module = allScanAPPAssignModule($user_id);
$modules = array_column($all_assinged_module, 'module_id');
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
                        <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">View User Details</h1>
                        <!--end::Title-->
                        <!--begin::Breadcrumb-->
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <a href="<?php echo $site_path ?>/dashboard" class="text-muted text-hover-primary">Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted">User Management</li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted">Users</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div id="kt_app_content" class="app-content">
            <div id="kt_app_content_container" class="app-container container-fluid">
                <div class="d-flex flex-column flex-lg-row">
                    <div class="flex-column flex-lg-row-auto w-lg-250px w-xl-350px mb-10">
                        <div class="card mb-5 mb-xl-8">
                            <div class="card-body">
                                <div class="d-flex flex-center flex-column py-5">
                                    <div class="symbol symbol-100px symbol-circle mb-7">
                                        <?php if ($profile_image) { ?>
                                            <img src="<?php echo $define_company_website; ?>uploads/staff/<?php echo $profile_image; ?>" alt="image" />
                                        <?php } else { ?> 
                                            <img src="<?php echo $site_path; ?>/images/default_image.jpeg" alt="image" />
                                        <?php } ?>
                                    </div>
                                    <a href="javascript:void(0);" class="fs-3 text-gray-800 text-hover-primary fw-bold mb-3"><?php echo $userDetails[0]['name']; ?></a>
                                    <div class="mb-9">
                                        <div class="badge badge-lg badge-light-primary d-inline"><?php echo $userDetails[0]['typee']; ?></div>
                                    </div>
                                </div>
                                <!--end::User Info-->
                                <!--end::Summary-->
                                <!--begin::Details toggle-->
                                <div class="d-flex flex-stack fs-4 py-3">
                                    <div class="fw-bold rotate collapsible" data-bs-toggle="collapse" href="#kt_user_view_details" role="button" aria-expanded="false" aria-controls="kt_user_view_details">Details 
                                        <span class="ms-2 rotate-180">
                                            <i class="ki-outline ki-down fs-3"></i>
                                        </span>
                                    </div>
                                    <span data-bs-toggle="tooltip" data-bs-trigger="hover" title="Edit customer details">
                                        <a href="#" class="btn btn-sm btn-light-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_update_details">Edit</a>
                                    </span>
                                </div>
                                <div class="separator"></div>
                                <div id="kt_user_view_details" class="collapse show">
                                    <div class="pb-5 fs-6">
                                        <div class="fw-bold mt-5">Account ID</div>
                                        <div class="text-gray-600">ID-<?php echo $userDetails[0]['id']; ?></div>
                                        <div class="fw-bold mt-5">Email</div>
                                        <div class="text-gray-600">
                                            <a href="#" class="text-gray-600 text-hover-primary"><?php echo $userDetails[0]['email']; ?></a>
                                        </div>
                                        <div class="fw-bold mt-5">Last Login</div>
                                        <div class="text-gray-600"><?php echo $lastLogin; ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex-lg-row-fluid ms-lg-15">
                        <ul class="nav nav-custom nav-tabs nav-line-tabs nav-line-tabs-2x border-0 fs-4 fw-semibold mb-8">
                            <li class="nav-item">
                                <a class="nav-link text-active-primary pb-4 active" data-bs-toggle="tab" href="#kt_user_view_overview_tab">Overview</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-active-primary pb-4" data-kt-countup-tabs="true" data-bs-toggle="tab" href="#kt_user_view_overview_security">Security</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-active-primary pb-4" data-bs-toggle="tab" href="#kt_user_view_overview_events_and_logs_tab">Events & Logs</a>
                            </li>
                            <li class="nav-item ms-auto" 
                                data-customer-id="<?php echo $user_id; ?>" 
                                data-customer-name="<?php echo $Fullname; ?>">
                                <a href="#" class="btn btn-primary ps-7" 
                                   data-kt-menu-trigger="click" 
                                   data-kt-menu-attach="parent" 
                                   data-kt-menu-placement="bottom-end">
                                    Actions <i class="ki-outline ki-down fs-2 me-0"></i>
                                </a>
                                <!--begin::Menu-->
                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold py-4 w-250px fs-6" data-kt-menu="true">
                                    <div class="separator my-3"></div>
                                    <div class="menu-item px-5">
                                        <a data-kt-users-modal-action="delete-customer" href="#" class="menu-link text-danger px-5">Delete customer</a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="kt_user_view_overview_tab" role="tabpanel">
                                <div class="card card-flush mb-6 mb-xl-9">
                                    <!--begin::Card header-->
                                    <div class="card-header mt-6">
                                        <!--begin::Card title-->
                                        <div class="card-title flex-column">
                                            <h2 class="mb-1">User's Schedule</h2>
                                            <div class="fs-6 fw-semibold text-muted">2 upcoming meetings</div>
                                        </div>
                                        <!--end::Card title-->
                                        <!--begin::Card toolbar-->
                                        <div class="card-toolbar">
                                            <button type="button" class="btn btn-light-primary btn-sm" data-bs-toggle="modal" data-bs-target="#kt_modal_add_schedule">
                                                <i class="ki-outline ki-brush fs-3"></i>Add Schedule</button>
                                        </div>
                                        <!--end::Card toolbar-->
                                    </div>
                                    <!--end::Card header-->
                                    <!--begin::Card body-->
                                    <div class="card-body p-9 pt-4">
                                        <!--begin::Dates-->
                                        <ul class="nav nav-pills d-flex flex-nowrap hover-scroll-x py-2">
                                            <!--begin::Date-->
                                            <li class="nav-item me-1">
                                                <a class="nav-link btn d-flex flex-column flex-center rounded-pill min-w-40px me-2 py-4 btn-active-primary" data-bs-toggle="tab" href="#kt_schedule_day_0">
                                                    <span class="opacity-50 fs-7 fw-semibold">Su</span>
                                                    <span class="fs-6 fw-bolder">21</span>
                                                </a>
                                            </li>
                                            <!--end::Date-->
                                            <!--begin::Date-->
                                            <li class="nav-item me-1">
                                                <a class="nav-link btn d-flex flex-column flex-center rounded-pill min-w-40px me-2 py-4 btn-active-primary active" data-bs-toggle="tab" href="#kt_schedule_day_1">
                                                    <span class="opacity-50 fs-7 fw-semibold">Mo</span>
                                                    <span class="fs-6 fw-bolder">22</span>
                                                </a>
                                            </li>
                                            <!--end::Date-->
                                            <!--begin::Date-->
                                            <li class="nav-item me-1">
                                                <a class="nav-link btn d-flex flex-column flex-center rounded-pill min-w-40px me-2 py-4 btn-active-primary" data-bs-toggle="tab" href="#kt_schedule_day_2">
                                                    <span class="opacity-50 fs-7 fw-semibold">Tu</span>
                                                    <span class="fs-6 fw-bolder">23</span>
                                                </a>
                                            </li>
                                            <!--end::Date-->
                                            <!--begin::Date-->
                                            <li class="nav-item me-1">
                                                <a class="nav-link btn d-flex flex-column flex-center rounded-pill min-w-40px me-2 py-4 btn-active-primary" data-bs-toggle="tab" href="#kt_schedule_day_3">
                                                    <span class="opacity-50 fs-7 fw-semibold">We</span>
                                                    <span class="fs-6 fw-bolder">24</span>
                                                </a>
                                            </li>
                                            <!--end::Date-->
                                            <!--begin::Date-->
                                            <li class="nav-item me-1">
                                                <a class="nav-link btn d-flex flex-column flex-center rounded-pill min-w-40px me-2 py-4 btn-active-primary" data-bs-toggle="tab" href="#kt_schedule_day_4">
                                                    <span class="opacity-50 fs-7 fw-semibold">Th</span>
                                                    <span class="fs-6 fw-bolder">25</span>
                                                </a>
                                            </li>
                                            <!--end::Date-->
                                            <!--begin::Date-->
                                            <li class="nav-item me-1">
                                                <a class="nav-link btn d-flex flex-column flex-center rounded-pill min-w-40px me-2 py-4 btn-active-primary" data-bs-toggle="tab" href="#kt_schedule_day_5">
                                                    <span class="opacity-50 fs-7 fw-semibold">Fr</span>
                                                    <span class="fs-6 fw-bolder">26</span>
                                                </a>
                                            </li>
                                            <!--end::Date-->
                                            <!--begin::Date-->
                                            <li class="nav-item me-1">
                                                <a class="nav-link btn d-flex flex-column flex-center rounded-pill min-w-40px me-2 py-4 btn-active-primary" data-bs-toggle="tab" href="#kt_schedule_day_6">
                                                    <span class="opacity-50 fs-7 fw-semibold">Sa</span>
                                                    <span class="fs-6 fw-bolder">27</span>
                                                </a>
                                            </li>
                                            <!--end::Date-->
                                            <!--begin::Date-->
                                            <li class="nav-item me-1">
                                                <a class="nav-link btn d-flex flex-column flex-center rounded-pill min-w-40px me-2 py-4 btn-active-primary" data-bs-toggle="tab" href="#kt_schedule_day_7">
                                                    <span class="opacity-50 fs-7 fw-semibold">Su</span>
                                                    <span class="fs-6 fw-bolder">28</span>
                                                </a>
                                            </li>
                                            <!--end::Date-->
                                            <!--begin::Date-->
                                            <li class="nav-item me-1">
                                                <a class="nav-link btn d-flex flex-column flex-center rounded-pill min-w-40px me-2 py-4 btn-active-primary" data-bs-toggle="tab" href="#kt_schedule_day_8">
                                                    <span class="opacity-50 fs-7 fw-semibold">Mo</span>
                                                    <span class="fs-6 fw-bolder">29</span>
                                                </a>
                                            </li>
                                            <!--end::Date-->
                                            <!--begin::Date-->
                                            <li class="nav-item me-1">
                                                <a class="nav-link btn d-flex flex-column flex-center rounded-pill min-w-40px me-2 py-4 btn-active-primary" data-bs-toggle="tab" href="#kt_schedule_day_9">
                                                    <span class="opacity-50 fs-7 fw-semibold">Tu</span>
                                                    <span class="fs-6 fw-bolder">30</span>
                                                </a>
                                            </li>
                                            <!--end::Date-->
                                            <!--begin::Date-->
                                            <li class="nav-item me-1">
                                                <a class="nav-link btn d-flex flex-column flex-center rounded-pill min-w-40px me-2 py-4 btn-active-primary" data-bs-toggle="tab" href="#kt_schedule_day_10">
                                                    <span class="opacity-50 fs-7 fw-semibold">We</span>
                                                    <span class="fs-6 fw-bolder">31</span>
                                                </a>
                                            </li>
                                            <!--end::Date-->
                                        </ul>
                                        <!--end::Dates-->
                                        <!--begin::Tab Content-->
                                        <div class="tab-content">
                                            <!--begin::Day-->
                                            <div id="kt_schedule_day_0" class="tab-pane fade show">
                                                <!--begin::Time-->
                                                <div class="d-flex flex-stack position-relative mt-6">
                                                    <!--begin::Bar-->
                                                    <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                                                    <!--end::Bar-->
                                                    <!--begin::Info-->
                                                    <div class="fw-semibold ms-5">
                                                        <!--begin::Time-->
                                                        <div class="fs-7 mb-1">14:30 - 15:30 
                                                            <span class="fs-7 text-muted text-uppercase">pm</span>
                                                        </div>
                                                        <!--end::Time-->
                                                        <!--begin::Title-->
                                                        <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Dashboard UI/UX Design Review</a>
                                                        <!--end::Title-->
                                                        <!--begin::User-->
                                                        <div class="fs-7 text-muted">Lead by 
                                                            <a href="#">Naomi Hayabusa</a>
                                                        </div>
                                                        <!--end::User-->
                                                    </div>
                                                    <!--end::Info-->
                                                    <!--begin::Action-->
                                                    <a href="#" class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                    <!--end::Action-->
                                                </div>
                                                <!--end::Time-->
                                                <!--begin::Time-->
                                                <div class="d-flex flex-stack position-relative mt-6">
                                                    <!--begin::Bar-->
                                                    <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                                                    <!--end::Bar-->
                                                    <!--begin::Info-->
                                                    <div class="fw-semibold ms-5">
                                                        <!--begin::Time-->
                                                        <div class="fs-7 mb-1">13:00 - 14:00 
                                                            <span class="fs-7 text-muted text-uppercase">pm</span>
                                                        </div>
                                                        <!--end::Time-->
                                                        <!--begin::Title-->
                                                        <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Team Backlog Grooming Session</a>
                                                        <!--end::Title-->
                                                        <!--begin::User-->
                                                        <div class="fs-7 text-muted">Lead by 
                                                            <a href="#">Michael Walters</a>
                                                        </div>
                                                        <!--end::User-->
                                                    </div>
                                                    <!--end::Info-->
                                                    <!--begin::Action-->
                                                    <a href="#" class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                    <!--end::Action-->
                                                </div>
                                                <!--end::Time-->
                                                <!--begin::Time-->
                                                <div class="d-flex flex-stack position-relative mt-6">
                                                    <!--begin::Bar-->
                                                    <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                                                    <!--end::Bar-->
                                                    <!--begin::Info-->
                                                    <div class="fw-semibold ms-5">
                                                        <!--begin::Time-->
                                                        <div class="fs-7 mb-1">13:00 - 14:00 
                                                            <span class="fs-7 text-muted text-uppercase">pm</span>
                                                        </div>
                                                        <!--end::Time-->
                                                        <!--begin::Title-->
                                                        <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Weekly Team Stand-Up</a>
                                                        <!--end::Title-->
                                                        <!--begin::User-->
                                                        <div class="fs-7 text-muted">Lead by 
                                                            <a href="#">Bob Harris</a>
                                                        </div>
                                                        <!--end::User-->
                                                    </div>
                                                    <!--end::Info-->
                                                    <!--begin::Action-->
                                                    <a href="#" class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                    <!--end::Action-->
                                                </div>
                                                <!--end::Time-->
                                                <!--begin::Time-->
                                                <div class="d-flex flex-stack position-relative mt-6">
                                                    <!--begin::Bar-->
                                                    <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                                                    <!--end::Bar-->
                                                    <!--begin::Info-->
                                                    <div class="fw-semibold ms-5">
                                                        <!--begin::Time-->
                                                        <div class="fs-7 mb-1">14:30 - 15:30 
                                                            <span class="fs-7 text-muted text-uppercase">pm</span>
                                                        </div>
                                                        <!--end::Time-->
                                                        <!--begin::Title-->
                                                        <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Lunch & Learn Catch Up</a>
                                                        <!--end::Title-->
                                                        <!--begin::User-->
                                                        <div class="fs-7 text-muted">Lead by 
                                                            <a href="#">Walter White</a>
                                                        </div>
                                                        <!--end::User-->
                                                    </div>
                                                    <!--end::Info-->
                                                    <!--begin::Action-->
                                                    <a href="#" class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                    <!--end::Action-->
                                                </div>
                                                <!--end::Time-->
                                                <!--begin::Time-->
                                                <div class="d-flex flex-stack position-relative mt-6">
                                                    <!--begin::Bar-->
                                                    <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                                                    <!--end::Bar-->
                                                    <!--begin::Info-->
                                                    <div class="fw-semibold ms-5">
                                                        <!--begin::Time-->
                                                        <div class="fs-7 mb-1">13:00 - 14:00 
                                                            <span class="fs-7 text-muted text-uppercase">pm</span>
                                                        </div>
                                                        <!--end::Time-->
                                                        <!--begin::Title-->
                                                        <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Team Backlog Grooming Session</a>
                                                        <!--end::Title-->
                                                        <!--begin::User-->
                                                        <div class="fs-7 text-muted">Lead by 
                                                            <a href="#">Caleb Donaldson</a>
                                                        </div>
                                                        <!--end::User-->
                                                    </div>
                                                    <!--end::Info-->
                                                    <!--begin::Action-->
                                                    <a href="#" class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                    <!--end::Action-->
                                                </div>
                                                <!--end::Time-->
                                            </div>
                                            <!--end::Day-->
                                            <!--begin::Day-->
                                            <div id="kt_schedule_day_1" class="tab-pane fade show active">
                                                <!--begin::Time-->
                                                <div class="d-flex flex-stack position-relative mt-6">
                                                    <!--begin::Bar-->
                                                    <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                                                    <!--end::Bar-->
                                                    <!--begin::Info-->
                                                    <div class="fw-semibold ms-5">
                                                        <!--begin::Time-->
                                                        <div class="fs-7 mb-1">9:00 - 10:00 
                                                            <span class="fs-7 text-muted text-uppercase">am</span>
                                                        </div>
                                                        <!--end::Time-->
                                                        <!--begin::Title-->
                                                        <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Committee Review Approvals</a>
                                                        <!--end::Title-->
                                                        <!--begin::User-->
                                                        <div class="fs-7 text-muted">Lead by 
                                                            <a href="#">David Stevenson</a>
                                                        </div>
                                                        <!--end::User-->
                                                    </div>
                                                    <!--end::Info-->
                                                    <!--begin::Action-->
                                                    <a href="#" class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                    <!--end::Action-->
                                                </div>
                                                <!--end::Time-->
                                                <!--begin::Time-->
                                                <div class="d-flex flex-stack position-relative mt-6">
                                                    <!--begin::Bar-->
                                                    <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                                                    <!--end::Bar-->
                                                    <!--begin::Info-->
                                                    <div class="fw-semibold ms-5">
                                                        <!--begin::Time-->
                                                        <div class="fs-7 mb-1">16:30 - 17:30 
                                                            <span class="fs-7 text-muted text-uppercase">pm</span>
                                                        </div>
                                                        <!--end::Time-->
                                                        <!--begin::Title-->
                                                        <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Marketing Campaign Discussion</a>
                                                        <!--end::Title-->
                                                        <!--begin::User-->
                                                        <div class="fs-7 text-muted">Lead by 
                                                            <a href="#">Karina Clarke</a>
                                                        </div>
                                                        <!--end::User-->
                                                    </div>
                                                    <!--end::Info-->
                                                    <!--begin::Action-->
                                                    <a href="#" class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                    <!--end::Action-->
                                                </div>
                                                <!--end::Time-->
                                                <!--begin::Time-->
                                                <div class="d-flex flex-stack position-relative mt-6">
                                                    <!--begin::Bar-->
                                                    <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                                                    <!--end::Bar-->
                                                    <!--begin::Info-->
                                                    <div class="fw-semibold ms-5">
                                                        <!--begin::Time-->
                                                        <div class="fs-7 mb-1">10:00 - 11:00 
                                                            <span class="fs-7 text-muted text-uppercase">am</span>
                                                        </div>
                                                        <!--end::Time-->
                                                        <!--begin::Title-->
                                                        <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Project Review & Testing</a>
                                                        <!--end::Title-->
                                                        <!--begin::User-->
                                                        <div class="fs-7 text-muted">Lead by 
                                                            <a href="#">Peter Marcus</a>
                                                        </div>
                                                        <!--end::User-->
                                                    </div>
                                                    <!--end::Info-->
                                                    <!--begin::Action-->
                                                    <a href="#" class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                    <!--end::Action-->
                                                </div>
                                                <!--end::Time-->
                                                <!--begin::Time-->
                                                <div class="d-flex flex-stack position-relative mt-6">
                                                    <!--begin::Bar-->
                                                    <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                                                    <!--end::Bar-->
                                                    <!--begin::Info-->
                                                    <div class="fw-semibold ms-5">
                                                        <!--begin::Time-->
                                                        <div class="fs-7 mb-1">16:30 - 17:30 
                                                            <span class="fs-7 text-muted text-uppercase">pm</span>
                                                        </div>
                                                        <!--end::Time-->
                                                        <!--begin::Title-->
                                                        <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Sales Pitch Proposal</a>
                                                        <!--end::Title-->
                                                        <!--begin::User-->
                                                        <div class="fs-7 text-muted">Lead by 
                                                            <a href="#">David Stevenson</a>
                                                        </div>
                                                        <!--end::User-->
                                                    </div>
                                                    <!--end::Info-->
                                                    <!--begin::Action-->
                                                    <a href="#" class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                    <!--end::Action-->
                                                </div>
                                                <!--end::Time-->
                                            </div>
                                            <!--end::Day-->
                                            <!--begin::Day-->
                                            <div id="kt_schedule_day_2" class="tab-pane fade show">
                                                <!--begin::Time-->
                                                <div class="d-flex flex-stack position-relative mt-6">
                                                    <!--begin::Bar-->
                                                    <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                                                    <!--end::Bar-->
                                                    <!--begin::Info-->
                                                    <div class="fw-semibold ms-5">
                                                        <!--begin::Time-->
                                                        <div class="fs-7 mb-1">16:30 - 17:30 
                                                            <span class="fs-7 text-muted text-uppercase">pm</span>
                                                        </div>
                                                        <!--end::Time-->
                                                        <!--begin::Title-->
                                                        <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Sales Pitch Proposal</a>
                                                        <!--end::Title-->
                                                        <!--begin::User-->
                                                        <div class="fs-7 text-muted">Lead by 
                                                            <a href="#">Terry Robins</a>
                                                        </div>
                                                        <!--end::User-->
                                                    </div>
                                                    <!--end::Info-->
                                                    <!--begin::Action-->
                                                    <a href="#" class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                    <!--end::Action-->
                                                </div>
                                                <!--end::Time-->
                                                <!--begin::Time-->
                                                <div class="d-flex flex-stack position-relative mt-6">
                                                    <!--begin::Bar-->
                                                    <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                                                    <!--end::Bar-->
                                                    <!--begin::Info-->
                                                    <div class="fw-semibold ms-5">
                                                        <!--begin::Time-->
                                                        <div class="fs-7 mb-1">10:00 - 11:00 
                                                            <span class="fs-7 text-muted text-uppercase">am</span>
                                                        </div>
                                                        <!--end::Time-->
                                                        <!--begin::Title-->
                                                        <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Project Review & Testing</a>
                                                        <!--end::Title-->
                                                        <!--begin::User-->
                                                        <div class="fs-7 text-muted">Lead by 
                                                            <a href="#">Michael Walters</a>
                                                        </div>
                                                        <!--end::User-->
                                                    </div>
                                                    <!--end::Info-->
                                                    <!--begin::Action-->
                                                    <a href="#" class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                    <!--end::Action-->
                                                </div>
                                                <!--end::Time-->
                                                <!--begin::Time-->
                                                <div class="d-flex flex-stack position-relative mt-6">
                                                    <!--begin::Bar-->
                                                    <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                                                    <!--end::Bar-->
                                                    <!--begin::Info-->
                                                    <div class="fw-semibold ms-5">
                                                        <!--begin::Time-->
                                                        <div class="fs-7 mb-1">16:30 - 17:30 
                                                            <span class="fs-7 text-muted text-uppercase">pm</span>
                                                        </div>
                                                        <!--end::Time-->
                                                        <!--begin::Title-->
                                                        <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Team Backlog Grooming Session</a>
                                                        <!--end::Title-->
                                                        <!--begin::User-->
                                                        <div class="fs-7 text-muted">Lead by 
                                                            <a href="#">Walter White</a>
                                                        </div>
                                                        <!--end::User-->
                                                    </div>
                                                    <!--end::Info-->
                                                    <!--begin::Action-->
                                                    <a href="#" class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                    <!--end::Action-->
                                                </div>
                                                <!--end::Time-->
                                            </div>
                                            <!--end::Day-->
                                            <!--begin::Day-->
                                            <div id="kt_schedule_day_3" class="tab-pane fade show">
                                                <!--begin::Time-->
                                                <div class="d-flex flex-stack position-relative mt-6">
                                                    <!--begin::Bar-->
                                                    <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                                                    <!--end::Bar-->
                                                    <!--begin::Info-->
                                                    <div class="fw-semibold ms-5">
                                                        <!--begin::Time-->
                                                        <div class="fs-7 mb-1">13:00 - 14:00 
                                                            <span class="fs-7 text-muted text-uppercase">pm</span>
                                                        </div>
                                                        <!--end::Time-->
                                                        <!--begin::Title-->
                                                        <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Marketing Campaign Discussion</a>
                                                        <!--end::Title-->
                                                        <!--begin::User-->
                                                        <div class="fs-7 text-muted">Lead by 
                                                            <a href="#">Mark Randall</a>
                                                        </div>
                                                        <!--end::User-->
                                                    </div>
                                                    <!--end::Info-->
                                                    <!--begin::Action-->
                                                    <a href="#" class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                    <!--end::Action-->
                                                </div>
                                                <!--end::Time-->
                                                <!--begin::Time-->
                                                <div class="d-flex flex-stack position-relative mt-6">
                                                    <!--begin::Bar-->
                                                    <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                                                    <!--end::Bar-->
                                                    <!--begin::Info-->
                                                    <div class="fw-semibold ms-5">
                                                        <!--begin::Time-->
                                                        <div class="fs-7 mb-1">13:00 - 14:00 
                                                            <span class="fs-7 text-muted text-uppercase">pm</span>
                                                        </div>
                                                        <!--end::Time-->
                                                        <!--begin::Title-->
                                                        <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">9 Degree Project Estimation Meeting</a>
                                                        <!--end::Title-->
                                                        <!--begin::User-->
                                                        <div class="fs-7 text-muted">Lead by 
                                                            <a href="#">Caleb Donaldson</a>
                                                        </div>
                                                        <!--end::User-->
                                                    </div>
                                                    <!--end::Info-->
                                                    <!--begin::Action-->
                                                    <a href="#" class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                    <!--end::Action-->
                                                </div>
                                                <!--end::Time-->
                                                <!--begin::Time-->
                                                <div class="d-flex flex-stack position-relative mt-6">
                                                    <!--begin::Bar-->
                                                    <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                                                    <!--end::Bar-->
                                                    <!--begin::Info-->
                                                    <div class="fw-semibold ms-5">
                                                        <!--begin::Time-->
                                                        <div class="fs-7 mb-1">9:00 - 10:00 
                                                            <span class="fs-7 text-muted text-uppercase">am</span>
                                                        </div>
                                                        <!--end::Time-->
                                                        <!--begin::Title-->
                                                        <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Development Team Capacity Review</a>
                                                        <!--end::Title-->
                                                        <!--begin::User-->
                                                        <div class="fs-7 text-muted">Lead by 
                                                            <a href="#">Karina Clarke</a>
                                                        </div>
                                                        <!--end::User-->
                                                    </div>
                                                    <!--end::Info-->
                                                    <!--begin::Action-->
                                                    <a href="#" class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                    <!--end::Action-->
                                                </div>
                                                <!--end::Time-->
                                                <!--begin::Time-->
                                                <div class="d-flex flex-stack position-relative mt-6">
                                                    <!--begin::Bar-->
                                                    <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                                                    <!--end::Bar-->
                                                    <!--begin::Info-->
                                                    <div class="fw-semibold ms-5">
                                                        <!--begin::Time-->
                                                        <div class="fs-7 mb-1">13:00 - 14:00 
                                                            <span class="fs-7 text-muted text-uppercase">pm</span>
                                                        </div>
                                                        <!--end::Time-->
                                                        <!--begin::Title-->
                                                        <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Development Team Capacity Review</a>
                                                        <!--end::Title-->
                                                        <!--begin::User-->
                                                        <div class="fs-7 text-muted">Lead by 
                                                            <a href="#">Yannis Gloverson</a>
                                                        </div>
                                                        <!--end::User-->
                                                    </div>
                                                    <!--end::Info-->
                                                    <!--begin::Action-->
                                                    <a href="#" class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                    <!--end::Action-->
                                                </div>
                                                <!--end::Time-->
                                                <!--begin::Time-->
                                                <div class="d-flex flex-stack position-relative mt-6">
                                                    <!--begin::Bar-->
                                                    <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                                                    <!--end::Bar-->
                                                    <!--begin::Info-->
                                                    <div class="fw-semibold ms-5">
                                                        <!--begin::Time-->
                                                        <div class="fs-7 mb-1">11:00 - 11:45 
                                                            <span class="fs-7 text-muted text-uppercase">am</span>
                                                        </div>
                                                        <!--end::Time-->
                                                        <!--begin::Title-->
                                                        <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Development Team Capacity Review</a>
                                                        <!--end::Title-->
                                                        <!--begin::User-->
                                                        <div class="fs-7 text-muted">Lead by 
                                                            <a href="#">David Stevenson</a>
                                                        </div>
                                                        <!--end::User-->
                                                    </div>
                                                    <!--end::Info-->
                                                    <!--begin::Action-->
                                                    <a href="#" class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                    <!--end::Action-->
                                                </div>
                                                <!--end::Time-->
                                            </div>
                                            <!--end::Day-->
                                            <!--begin::Day-->
                                            <div id="kt_schedule_day_4" class="tab-pane fade show">
                                                <!--begin::Time-->
                                                <div class="d-flex flex-stack position-relative mt-6">
                                                    <!--begin::Bar-->
                                                    <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                                                    <!--end::Bar-->
                                                    <!--begin::Info-->
                                                    <div class="fw-semibold ms-5">
                                                        <!--begin::Time-->
                                                        <div class="fs-7 mb-1">13:00 - 14:00 
                                                            <span class="fs-7 text-muted text-uppercase">pm</span>
                                                        </div>
                                                        <!--end::Time-->
                                                        <!--begin::Title-->
                                                        <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Creative Content Initiative</a>
                                                        <!--end::Title-->
                                                        <!--begin::User-->
                                                        <div class="fs-7 text-muted">Lead by 
                                                            <a href="#">Terry Robins</a>
                                                        </div>
                                                        <!--end::User-->
                                                    </div>
                                                    <!--end::Info-->
                                                    <!--begin::Action-->
                                                    <a href="#" class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                    <!--end::Action-->
                                                </div>
                                                <!--end::Time-->
                                                <!--begin::Time-->
                                                <div class="d-flex flex-stack position-relative mt-6">
                                                    <!--begin::Bar-->
                                                    <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                                                    <!--end::Bar-->
                                                    <!--begin::Info-->
                                                    <div class="fw-semibold ms-5">
                                                        <!--begin::Time-->
                                                        <div class="fs-7 mb-1">14:30 - 15:30 
                                                            <span class="fs-7 text-muted text-uppercase">pm</span>
                                                        </div>
                                                        <!--end::Time-->
                                                        <!--begin::Title-->
                                                        <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Sales Pitch Proposal</a>
                                                        <!--end::Title-->
                                                        <!--begin::User-->
                                                        <div class="fs-7 text-muted">Lead by 
                                                            <a href="#">Caleb Donaldson</a>
                                                        </div>
                                                        <!--end::User-->
                                                    </div>
                                                    <!--end::Info-->
                                                    <!--begin::Action-->
                                                    <a href="#" class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                    <!--end::Action-->
                                                </div>
                                                <!--end::Time-->
                                                <!--begin::Time-->
                                                <div class="d-flex flex-stack position-relative mt-6">
                                                    <!--begin::Bar-->
                                                    <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                                                    <!--end::Bar-->
                                                    <!--begin::Info-->
                                                    <div class="fw-semibold ms-5">
                                                        <!--begin::Time-->
                                                        <div class="fs-7 mb-1">13:00 - 14:00 
                                                            <span class="fs-7 text-muted text-uppercase">pm</span>
                                                        </div>
                                                        <!--end::Time-->
                                                        <!--begin::Title-->
                                                        <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Weekly Team Stand-Up</a>
                                                        <!--end::Title-->
                                                        <!--begin::User-->
                                                        <div class="fs-7 text-muted">Lead by 
                                                            <a href="#">Michael Walters</a>
                                                        </div>
                                                        <!--end::User-->
                                                    </div>
                                                    <!--end::Info-->
                                                    <!--begin::Action-->
                                                    <a href="#" class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                    <!--end::Action-->
                                                </div>
                                                <!--end::Time-->
                                                <!--begin::Time-->
                                                <div class="d-flex flex-stack position-relative mt-6">
                                                    <!--begin::Bar-->
                                                    <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                                                    <!--end::Bar-->
                                                    <!--begin::Info-->
                                                    <div class="fw-semibold ms-5">
                                                        <!--begin::Time-->
                                                        <div class="fs-7 mb-1">12:00 - 13:00 
                                                            <span class="fs-7 text-muted text-uppercase">pm</span>
                                                        </div>
                                                        <!--end::Time-->
                                                        <!--begin::Title-->
                                                        <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Lunch & Learn Catch Up</a>
                                                        <!--end::Title-->
                                                        <!--begin::User-->
                                                        <div class="fs-7 text-muted">Lead by 
                                                            <a href="#">Naomi Hayabusa</a>
                                                        </div>
                                                        <!--end::User-->
                                                    </div>
                                                    <!--end::Info-->
                                                    <!--begin::Action-->
                                                    <a href="#" class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                    <!--end::Action-->
                                                </div>
                                                <!--end::Time-->
                                            </div>
                                            <!--end::Day-->
                                            <!--begin::Day-->
                                            <div id="kt_schedule_day_5" class="tab-pane fade show">
                                                <!--begin::Time-->
                                                <div class="d-flex flex-stack position-relative mt-6">
                                                    <!--begin::Bar-->
                                                    <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                                                    <!--end::Bar-->
                                                    <!--begin::Info-->
                                                    <div class="fw-semibold ms-5">
                                                        <!--begin::Time-->
                                                        <div class="fs-7 mb-1">14:30 - 15:30 
                                                            <span class="fs-7 text-muted text-uppercase">pm</span>
                                                        </div>
                                                        <!--end::Time-->
                                                        <!--begin::Title-->
                                                        <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Dashboard UI/UX Design Review</a>
                                                        <!--end::Title-->
                                                        <!--begin::User-->
                                                        <div class="fs-7 text-muted">Lead by 
                                                            <a href="#">Terry Robins</a>
                                                        </div>
                                                        <!--end::User-->
                                                    </div>
                                                    <!--end::Info-->
                                                    <!--begin::Action-->
                                                    <a href="#" class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                    <!--end::Action-->
                                                </div>
                                                <!--end::Time-->
                                                <!--begin::Time-->
                                                <div class="d-flex flex-stack position-relative mt-6">
                                                    <!--begin::Bar-->
                                                    <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                                                    <!--end::Bar-->
                                                    <!--begin::Info-->
                                                    <div class="fw-semibold ms-5">
                                                        <!--begin::Time-->
                                                        <div class="fs-7 mb-1">11:00 - 11:45 
                                                            <span class="fs-7 text-muted text-uppercase">am</span>
                                                        </div>
                                                        <!--end::Time-->
                                                        <!--begin::Title-->
                                                        <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Marketing Campaign Discussion</a>
                                                        <!--end::Title-->
                                                        <!--begin::User-->
                                                        <div class="fs-7 text-muted">Lead by 
                                                            <a href="#">Michael Walters</a>
                                                        </div>
                                                        <!--end::User-->
                                                    </div>
                                                    <!--end::Info-->
                                                    <!--begin::Action-->
                                                    <a href="#" class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                    <!--end::Action-->
                                                </div>
                                                <!--end::Time-->
                                                <!--begin::Time-->
                                                <div class="d-flex flex-stack position-relative mt-6">
                                                    <!--begin::Bar-->
                                                    <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                                                    <!--end::Bar-->
                                                    <!--begin::Info-->
                                                    <div class="fw-semibold ms-5">
                                                        <!--begin::Time-->
                                                        <div class="fs-7 mb-1">13:00 - 14:00 
                                                            <span class="fs-7 text-muted text-uppercase">pm</span>
                                                        </div>
                                                        <!--end::Time-->
                                                        <!--begin::Title-->
                                                        <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Dashboard UI/UX Design Review</a>
                                                        <!--end::Title-->
                                                        <!--begin::User-->
                                                        <div class="fs-7 text-muted">Lead by 
                                                            <a href="#">Kendell Trevor</a>
                                                        </div>
                                                        <!--end::User-->
                                                    </div>
                                                    <!--end::Info-->
                                                    <!--begin::Action-->
                                                    <a href="#" class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                    <!--end::Action-->
                                                </div>
                                                <!--end::Time-->
                                            </div>
                                            <!--end::Day-->
                                            <!--begin::Day-->
                                            <div id="kt_schedule_day_6" class="tab-pane fade show">
                                                <!--begin::Time-->
                                                <div class="d-flex flex-stack position-relative mt-6">
                                                    <!--begin::Bar-->
                                                    <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                                                    <!--end::Bar-->
                                                    <!--begin::Info-->
                                                    <div class="fw-semibold ms-5">
                                                        <!--begin::Time-->
                                                        <div class="fs-7 mb-1">13:00 - 14:00 
                                                            <span class="fs-7 text-muted text-uppercase">pm</span>
                                                        </div>
                                                        <!--end::Time-->
                                                        <!--begin::Title-->
                                                        <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Project Review & Testing</a>
                                                        <!--end::Title-->
                                                        <!--begin::User-->
                                                        <div class="fs-7 text-muted">Lead by 
                                                            <a href="#">Peter Marcus</a>
                                                        </div>
                                                        <!--end::User-->
                                                    </div>
                                                    <!--end::Info-->
                                                    <!--begin::Action-->
                                                    <a href="#" class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                    <!--end::Action-->
                                                </div>
                                                <!--end::Time-->
                                                <!--begin::Time-->
                                                <div class="d-flex flex-stack position-relative mt-6">
                                                    <!--begin::Bar-->
                                                    <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                                                    <!--end::Bar-->
                                                    <!--begin::Info-->
                                                    <div class="fw-semibold ms-5">
                                                        <!--begin::Time-->
                                                        <div class="fs-7 mb-1">10:00 - 11:00 
                                                            <span class="fs-7 text-muted text-uppercase">am</span>
                                                        </div>
                                                        <!--end::Time-->
                                                        <!--begin::Title-->
                                                        <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Team Backlog Grooming Session</a>
                                                        <!--end::Title-->
                                                        <!--begin::User-->
                                                        <div class="fs-7 text-muted">Lead by 
                                                            <a href="#">Peter Marcus</a>
                                                        </div>
                                                        <!--end::User-->
                                                    </div>
                                                    <!--end::Info-->
                                                    <!--begin::Action-->
                                                    <a href="#" class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                    <!--end::Action-->
                                                </div>
                                                <!--end::Time-->
                                                <!--begin::Time-->
                                                <div class="d-flex flex-stack position-relative mt-6">
                                                    <!--begin::Bar-->
                                                    <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                                                    <!--end::Bar-->
                                                    <!--begin::Info-->
                                                    <div class="fw-semibold ms-5">
                                                        <!--begin::Time-->
                                                        <div class="fs-7 mb-1">12:00 - 13:00 
                                                            <span class="fs-7 text-muted text-uppercase">pm</span>
                                                        </div>
                                                        <!--end::Time-->
                                                        <!--begin::Title-->
                                                        <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Marketing Campaign Discussion</a>
                                                        <!--end::Title-->
                                                        <!--begin::User-->
                                                        <div class="fs-7 text-muted">Lead by 
                                                            <a href="#">Sean Bean</a>
                                                        </div>
                                                        <!--end::User-->
                                                    </div>
                                                    <!--end::Info-->
                                                    <!--begin::Action-->
                                                    <a href="#" class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                    <!--end::Action-->
                                                </div>
                                                <!--end::Time-->
                                                <!--begin::Time-->
                                                <div class="d-flex flex-stack position-relative mt-6">
                                                    <!--begin::Bar-->
                                                    <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                                                    <!--end::Bar-->
                                                    <!--begin::Info-->
                                                    <div class="fw-semibold ms-5">
                                                        <!--begin::Time-->
                                                        <div class="fs-7 mb-1">13:00 - 14:00 
                                                            <span class="fs-7 text-muted text-uppercase">pm</span>
                                                        </div>
                                                        <!--end::Time-->
                                                        <!--begin::Title-->
                                                        <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Marketing Campaign Discussion</a>
                                                        <!--end::Title-->
                                                        <!--begin::User-->
                                                        <div class="fs-7 text-muted">Lead by 
                                                            <a href="#">David Stevenson</a>
                                                        </div>
                                                        <!--end::User-->
                                                    </div>
                                                    <!--end::Info-->
                                                    <!--begin::Action-->
                                                    <a href="#" class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                    <!--end::Action-->
                                                </div>
                                                <!--end::Time-->
                                                <!--begin::Time-->
                                                <div class="d-flex flex-stack position-relative mt-6">
                                                    <!--begin::Bar-->
                                                    <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                                                    <!--end::Bar-->
                                                    <!--begin::Info-->
                                                    <div class="fw-semibold ms-5">
                                                        <!--begin::Time-->
                                                        <div class="fs-7 mb-1">13:00 - 14:00 
                                                            <span class="fs-7 text-muted text-uppercase">pm</span>
                                                        </div>
                                                        <!--end::Time-->
                                                        <!--begin::Title-->
                                                        <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Creative Content Initiative</a>
                                                        <!--end::Title-->
                                                        <!--begin::User-->
                                                        <div class="fs-7 text-muted">Lead by 
                                                            <a href="#">Yannis Gloverson</a>
                                                        </div>
                                                        <!--end::User-->
                                                    </div>
                                                    <!--end::Info-->
                                                    <!--begin::Action-->
                                                    <a href="#" class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                    <!--end::Action-->
                                                </div>
                                                <!--end::Time-->
                                            </div>
                                            <!--end::Day-->
                                            <!--begin::Day-->
                                            <div id="kt_schedule_day_7" class="tab-pane fade show">
                                                <!--begin::Time-->
                                                <div class="d-flex flex-stack position-relative mt-6">
                                                    <!--begin::Bar-->
                                                    <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                                                    <!--end::Bar-->
                                                    <!--begin::Info-->
                                                    <div class="fw-semibold ms-5">
                                                        <!--begin::Time-->
                                                        <div class="fs-7 mb-1">13:00 - 14:00 
                                                            <span class="fs-7 text-muted text-uppercase">pm</span>
                                                        </div>
                                                        <!--end::Time-->
                                                        <!--begin::Title-->
                                                        <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Lunch & Learn Catch Up</a>
                                                        <!--end::Title-->
                                                        <!--begin::User-->
                                                        <div class="fs-7 text-muted">Lead by 
                                                            <a href="#">Karina Clarke</a>
                                                        </div>
                                                        <!--end::User-->
                                                    </div>
                                                    <!--end::Info-->
                                                    <!--begin::Action-->
                                                    <a href="#" class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                    <!--end::Action-->
                                                </div>
                                                <!--end::Time-->
                                                <!--begin::Time-->
                                                <div class="d-flex flex-stack position-relative mt-6">
                                                    <!--begin::Bar-->
                                                    <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                                                    <!--end::Bar-->
                                                    <!--begin::Info-->
                                                    <div class="fw-semibold ms-5">
                                                        <!--begin::Time-->
                                                        <div class="fs-7 mb-1">16:30 - 17:30 
                                                            <span class="fs-7 text-muted text-uppercase">pm</span>
                                                        </div>
                                                        <!--end::Time-->
                                                        <!--begin::Title-->
                                                        <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Sales Pitch Proposal</a>
                                                        <!--end::Title-->
                                                        <!--begin::User-->
                                                        <div class="fs-7 text-muted">Lead by 
                                                            <a href="#">Mark Randall</a>
                                                        </div>
                                                        <!--end::User-->
                                                    </div>
                                                    <!--end::Info-->
                                                    <!--begin::Action-->
                                                    <a href="#" class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                    <!--end::Action-->
                                                </div>
                                                <!--end::Time-->
                                                <!--begin::Time-->
                                                <div class="d-flex flex-stack position-relative mt-6">
                                                    <!--begin::Bar-->
                                                    <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                                                    <!--end::Bar-->
                                                    <!--begin::Info-->
                                                    <div class="fw-semibold ms-5">
                                                        <!--begin::Time-->
                                                        <div class="fs-7 mb-1">9:00 - 10:00 
                                                            <span class="fs-7 text-muted text-uppercase">am</span>
                                                        </div>
                                                        <!--end::Time-->
                                                        <!--begin::Title-->
                                                        <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Development Team Capacity Review</a>
                                                        <!--end::Title-->
                                                        <!--begin::User-->
                                                        <div class="fs-7 text-muted">Lead by 
                                                            <a href="#">Naomi Hayabusa</a>
                                                        </div>
                                                        <!--end::User-->
                                                    </div>
                                                    <!--end::Info-->
                                                    <!--begin::Action-->
                                                    <a href="#" class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                    <!--end::Action-->
                                                </div>
                                                <!--end::Time-->
                                                <!--begin::Time-->
                                                <div class="d-flex flex-stack position-relative mt-6">
                                                    <!--begin::Bar-->
                                                    <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                                                    <!--end::Bar-->
                                                    <!--begin::Info-->
                                                    <div class="fw-semibold ms-5">
                                                        <!--begin::Time-->
                                                        <div class="fs-7 mb-1">16:30 - 17:30 
                                                            <span class="fs-7 text-muted text-uppercase">pm</span>
                                                        </div>
                                                        <!--end::Time-->
                                                        <!--begin::Title-->
                                                        <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Marketing Campaign Discussion</a>
                                                        <!--end::Title-->
                                                        <!--begin::User-->
                                                        <div class="fs-7 text-muted">Lead by 
                                                            <a href="#">Sean Bean</a>
                                                        </div>
                                                        <!--end::User-->
                                                    </div>
                                                    <!--end::Info-->
                                                    <!--begin::Action-->
                                                    <a href="#" class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                    <!--end::Action-->
                                                </div>
                                                <!--end::Time-->
                                            </div>
                                            <!--end::Day-->
                                            <!--begin::Day-->
                                            <div id="kt_schedule_day_8" class="tab-pane fade show">
                                                <!--begin::Time-->
                                                <div class="d-flex flex-stack position-relative mt-6">
                                                    <!--begin::Bar-->
                                                    <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                                                    <!--end::Bar-->
                                                    <!--begin::Info-->
                                                    <div class="fw-semibold ms-5">
                                                        <!--begin::Time-->
                                                        <div class="fs-7 mb-1">9:00 - 10:00 
                                                            <span class="fs-7 text-muted text-uppercase">am</span>
                                                        </div>
                                                        <!--end::Time-->
                                                        <!--begin::Title-->
                                                        <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Creative Content Initiative</a>
                                                        <!--end::Title-->
                                                        <!--begin::User-->
                                                        <div class="fs-7 text-muted">Lead by 
                                                            <a href="#">Naomi Hayabusa</a>
                                                        </div>
                                                        <!--end::User-->
                                                    </div>
                                                    <!--end::Info-->
                                                    <!--begin::Action-->
                                                    <a href="#" class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                    <!--end::Action-->
                                                </div>
                                                <!--end::Time-->
                                                <!--begin::Time-->
                                                <div class="d-flex flex-stack position-relative mt-6">
                                                    <!--begin::Bar-->
                                                    <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                                                    <!--end::Bar-->
                                                    <!--begin::Info-->
                                                    <div class="fw-semibold ms-5">
                                                        <!--begin::Time-->
                                                        <div class="fs-7 mb-1">9:00 - 10:00 
                                                            <span class="fs-7 text-muted text-uppercase">am</span>
                                                        </div>
                                                        <!--end::Time-->
                                                        <!--begin::Title-->
                                                        <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Marketing Campaign Discussion</a>
                                                        <!--end::Title-->
                                                        <!--begin::User-->
                                                        <div class="fs-7 text-muted">Lead by 
                                                            <a href="#">Karina Clarke</a>
                                                        </div>
                                                        <!--end::User-->
                                                    </div>
                                                    <!--end::Info-->
                                                    <!--begin::Action-->
                                                    <a href="#" class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                    <!--end::Action-->
                                                </div>
                                                <!--end::Time-->
                                                <!--begin::Time-->
                                                <div class="d-flex flex-stack position-relative mt-6">
                                                    <!--begin::Bar-->
                                                    <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                                                    <!--end::Bar-->
                                                    <!--begin::Info-->
                                                    <div class="fw-semibold ms-5">
                                                        <!--begin::Time-->
                                                        <div class="fs-7 mb-1">10:00 - 11:00 
                                                            <span class="fs-7 text-muted text-uppercase">am</span>
                                                        </div>
                                                        <!--end::Time-->
                                                        <!--begin::Title-->
                                                        <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Team Backlog Grooming Session</a>
                                                        <!--end::Title-->
                                                        <!--begin::User-->
                                                        <div class="fs-7 text-muted">Lead by 
                                                            <a href="#">Yannis Gloverson</a>
                                                        </div>
                                                        <!--end::User-->
                                                    </div>
                                                    <!--end::Info-->
                                                    <!--begin::Action-->
                                                    <a href="#" class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                    <!--end::Action-->
                                                </div>
                                                <!--end::Time-->
                                                <!--begin::Time-->
                                                <div class="d-flex flex-stack position-relative mt-6">
                                                    <!--begin::Bar-->
                                                    <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                                                    <!--end::Bar-->
                                                    <!--begin::Info-->
                                                    <div class="fw-semibold ms-5">
                                                        <!--begin::Time-->
                                                        <div class="fs-7 mb-1">14:30 - 15:30 
                                                            <span class="fs-7 text-muted text-uppercase">pm</span>
                                                        </div>
                                                        <!--end::Time-->
                                                        <!--begin::Title-->
                                                        <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Dashboard UI/UX Design Review</a>
                                                        <!--end::Title-->
                                                        <!--begin::User-->
                                                        <div class="fs-7 text-muted">Lead by 
                                                            <a href="#">Peter Marcus</a>
                                                        </div>
                                                        <!--end::User-->
                                                    </div>
                                                    <!--end::Info-->
                                                    <!--begin::Action-->
                                                    <a href="#" class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                    <!--end::Action-->
                                                </div>
                                                <!--end::Time-->
                                            </div>
                                            <!--end::Day-->
                                            <!--begin::Day-->
                                            <div id="kt_schedule_day_9" class="tab-pane fade show">
                                                <!--begin::Time-->
                                                <div class="d-flex flex-stack position-relative mt-6">
                                                    <!--begin::Bar-->
                                                    <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                                                    <!--end::Bar-->
                                                    <!--begin::Info-->
                                                    <div class="fw-semibold ms-5">
                                                        <!--begin::Time-->
                                                        <div class="fs-7 mb-1">16:30 - 17:30 
                                                            <span class="fs-7 text-muted text-uppercase">pm</span>
                                                        </div>
                                                        <!--end::Time-->
                                                        <!--begin::Title-->
                                                        <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Development Team Capacity Review</a>
                                                        <!--end::Title-->
                                                        <!--begin::User-->
                                                        <div class="fs-7 text-muted">Lead by 
                                                            <a href="#">Mark Randall</a>
                                                        </div>
                                                        <!--end::User-->
                                                    </div>
                                                    <!--end::Info-->
                                                    <!--begin::Action-->
                                                    <a href="#" class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                    <!--end::Action-->
                                                </div>
                                                <!--end::Time-->
                                                <!--begin::Time-->
                                                <div class="d-flex flex-stack position-relative mt-6">
                                                    <!--begin::Bar-->
                                                    <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                                                    <!--end::Bar-->
                                                    <!--begin::Info-->
                                                    <div class="fw-semibold ms-5">
                                                        <!--begin::Time-->
                                                        <div class="fs-7 mb-1">13:00 - 14:00 
                                                            <span class="fs-7 text-muted text-uppercase">pm</span>
                                                        </div>
                                                        <!--end::Time-->
                                                        <!--begin::Title-->
                                                        <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Dashboard UI/UX Design Review</a>
                                                        <!--end::Title-->
                                                        <!--begin::User-->
                                                        <div class="fs-7 text-muted">Lead by 
                                                            <a href="#">Terry Robins</a>
                                                        </div>
                                                        <!--end::User-->
                                                    </div>
                                                    <!--end::Info-->
                                                    <!--begin::Action-->
                                                    <a href="#" class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                    <!--end::Action-->
                                                </div>
                                                <!--end::Time-->
                                                <!--begin::Time-->
                                                <div class="d-flex flex-stack position-relative mt-6">
                                                    <!--begin::Bar-->
                                                    <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                                                    <!--end::Bar-->
                                                    <!--begin::Info-->
                                                    <div class="fw-semibold ms-5">
                                                        <!--begin::Time-->
                                                        <div class="fs-7 mb-1">11:00 - 11:45 
                                                            <span class="fs-7 text-muted text-uppercase">am</span>
                                                        </div>
                                                        <!--end::Time-->
                                                        <!--begin::Title-->
                                                        <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Weekly Team Stand-Up</a>
                                                        <!--end::Title-->
                                                        <!--begin::User-->
                                                        <div class="fs-7 text-muted">Lead by 
                                                            <a href="#">Sean Bean</a>
                                                        </div>
                                                        <!--end::User-->
                                                    </div>
                                                    <!--end::Info-->
                                                    <!--begin::Action-->
                                                    <a href="#" class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                    <!--end::Action-->
                                                </div>
                                                <!--end::Time-->
                                                <!--begin::Time-->
                                                <div class="d-flex flex-stack position-relative mt-6">
                                                    <!--begin::Bar-->
                                                    <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                                                    <!--end::Bar-->
                                                    <!--begin::Info-->
                                                    <div class="fw-semibold ms-5">
                                                        <!--begin::Time-->
                                                        <div class="fs-7 mb-1">13:00 - 14:00 
                                                            <span class="fs-7 text-muted text-uppercase">pm</span>
                                                        </div>
                                                        <!--end::Time-->
                                                        <!--begin::Title-->
                                                        <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Development Team Capacity Review</a>
                                                        <!--end::Title-->
                                                        <!--begin::User-->
                                                        <div class="fs-7 text-muted">Lead by 
                                                            <a href="#">Yannis Gloverson</a>
                                                        </div>
                                                        <!--end::User-->
                                                    </div>
                                                    <!--end::Info-->
                                                    <!--begin::Action-->
                                                    <a href="#" class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                    <!--end::Action-->
                                                </div>
                                                <!--end::Time-->
                                                <!--begin::Time-->
                                                <div class="d-flex flex-stack position-relative mt-6">
                                                    <!--begin::Bar-->
                                                    <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                                                    <!--end::Bar-->
                                                    <!--begin::Info-->
                                                    <div class="fw-semibold ms-5">
                                                        <!--begin::Time-->
                                                        <div class="fs-7 mb-1">13:00 - 14:00 
                                                            <span class="fs-7 text-muted text-uppercase">pm</span>
                                                        </div>
                                                        <!--end::Time-->
                                                        <!--begin::Title-->
                                                        <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">9 Degree Project Estimation Meeting</a>
                                                        <!--end::Title-->
                                                        <!--begin::User-->
                                                        <div class="fs-7 text-muted">Lead by 
                                                            <a href="#">Yannis Gloverson</a>
                                                        </div>
                                                        <!--end::User-->
                                                    </div>
                                                    <!--end::Info-->
                                                    <!--begin::Action-->
                                                    <a href="#" class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                    <!--end::Action-->
                                                </div>
                                                <!--end::Time-->
                                            </div>
                                            <!--end::Day-->
                                            <!--begin::Day-->
                                            <div id="kt_schedule_day_10" class="tab-pane fade show">
                                                <!--begin::Time-->
                                                <div class="d-flex flex-stack position-relative mt-6">
                                                    <!--begin::Bar-->
                                                    <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                                                    <!--end::Bar-->
                                                    <!--begin::Info-->
                                                    <div class="fw-semibold ms-5">
                                                        <!--begin::Time-->
                                                        <div class="fs-7 mb-1">9:00 - 10:00 
                                                            <span class="fs-7 text-muted text-uppercase">am</span>
                                                        </div>
                                                        <!--end::Time-->
                                                        <!--begin::Title-->
                                                        <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Creative Content Initiative</a>
                                                        <!--end::Title-->
                                                        <!--begin::User-->
                                                        <div class="fs-7 text-muted">Lead by 
                                                            <a href="#">Michael Walters</a>
                                                        </div>
                                                        <!--end::User-->
                                                    </div>
                                                    <!--end::Info-->
                                                    <!--begin::Action-->
                                                    <a href="#" class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                    <!--end::Action-->
                                                </div>
                                                <!--end::Time-->
                                                <!--begin::Time-->
                                                <div class="d-flex flex-stack position-relative mt-6">
                                                    <!--begin::Bar-->
                                                    <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                                                    <!--end::Bar-->
                                                    <!--begin::Info-->
                                                    <div class="fw-semibold ms-5">
                                                        <!--begin::Time-->
                                                        <div class="fs-7 mb-1">13:00 - 14:00 
                                                            <span class="fs-7 text-muted text-uppercase">pm</span>
                                                        </div>
                                                        <!--end::Time-->
                                                        <!--begin::Title-->
                                                        <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Project Review & Testing</a>
                                                        <!--end::Title-->
                                                        <!--begin::User-->
                                                        <div class="fs-7 text-muted">Lead by 
                                                            <a href="#">Mark Randall</a>
                                                        </div>
                                                        <!--end::User-->
                                                    </div>
                                                    <!--end::Info-->
                                                    <!--begin::Action-->
                                                    <a href="#" class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                    <!--end::Action-->
                                                </div>
                                                <!--end::Time-->
                                                <!--begin::Time-->
                                                <div class="d-flex flex-stack position-relative mt-6">
                                                    <!--begin::Bar-->
                                                    <div class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0"></div>
                                                    <!--end::Bar-->
                                                    <!--begin::Info-->
                                                    <div class="fw-semibold ms-5">
                                                        <!--begin::Time-->
                                                        <div class="fs-7 mb-1">16:30 - 17:30 
                                                            <span class="fs-7 text-muted text-uppercase">pm</span>
                                                        </div>
                                                        <!--end::Time-->
                                                        <!--begin::Title-->
                                                        <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Dashboard UI/UX Design Review</a>
                                                        <!--end::Title-->
                                                        <!--begin::User-->
                                                        <div class="fs-7 text-muted">Lead by 
                                                            <a href="#">Walter White</a>
                                                        </div>
                                                        <!--end::User-->
                                                    </div>
                                                    <!--end::Info-->
                                                    <!--begin::Action-->
                                                    <a href="#" class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                    <!--end::Action-->
                                                </div>
                                                <!--end::Time-->
                                            </div>
                                            <!--end::Day-->
                                        </div>
                                        <!--end::Tab Content-->
                                    </div>
                                    <!--end::Card body-->
                                </div>
                                <!--end::Card-->
                            </div>
                            <!--end:::Tab pane-->
                            <!--begin:::Tab pane-->
                            <div class="tab-pane fade" id="kt_user_view_overview_security" role="tabpanel">
                                <!--begin::Card-->
                                <div class="card pt-4 mb-6 mb-xl-9">
                                    <!--begin::Card header-->
                                    <div class="card-header border-0">
                                        <!--begin::Card title-->
                                        <div class="card-title">
                                            <h2>Profile</h2>
                                        </div>
                                        <!--end::Card title-->
                                    </div>
                                    <!--end::Card header-->
                                    <!--begin::Card body-->
                                    <div class="card-body pt-0 pb-5">
                                        <!--begin::Table wrapper-->
                                        <div class="table-responsive">
                                            <!--begin::Table-->
                                            <table class="table align-middle table-row-dashed gy-5" id="kt_table_users_login_session">
                                                <tbody class="fs-6 fw-semibold text-gray-600">
                                                    <tr>
                                                        <td>Email</td>
                                                        <td><?php echo $userDetails[0]['email']; ?></td>
                                                        <td class="text-end">
                                                            <button type="button" class="btn btn-icon btn-active-light-primary w-30px h-30px ms-auto" data-bs-toggle="modal" data-bs-target="#kt_modal_update_email">
                                                                <i class="ki-outline ki-pencil fs-3"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Password</td>
                                                        <td>******</td>
                                                        <td class="text-end">
                                                            <button type="button" class="btn btn-icon btn-active-light-primary w-30px h-30px ms-auto" data-bs-toggle="modal" data-bs-target="#kt_modal_update_password">
                                                                <i class="ki-outline ki-pencil fs-3"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Role</td>
                                                        <td><?php echo $userDetails[0]['typee']; ?></td>
                                                        <td class="text-end">
                                                            <button type="button" class="btn btn-icon btn-active-light-primary w-30px h-30px ms-auto" data-bs-toggle="modal" data-bs-target="#kt_modal_update_role">
                                                                <i class="ki-outline ki-pencil fs-3"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <!--end::Table-->
                                        </div>
                                        <!--end::Table wrapper-->
                                    </div>
                                    <!--end::Card body-->
                                </div>
                                <!--end::Card-->
                            </div>
                            <!--end:::Tab pane-->
                            <!--begin:::Tab pane-->
                            <div class="tab-pane fade" id="kt_user_view_overview_events_and_logs_tab" role="tabpanel">
                                <!--begin::Card-->
                                <div class="card pt-4 mb-6 mb-xl-9">
                                    <!--begin::Card header-->
                                    <div class="card-header border-0">
                                        <!--begin::Card title-->
                                        <div class="card-title">
                                            <h2>Login Sessions</h2>
                                        </div>
                                        <!--end::Card title-->
                                        <!--begin::Card toolbar-->
                                    </div>
                                    <!--end::Card header-->
                                    <!--begin::Card body-->
                                    <div class="card-body pt-0 pb-5">
                                        <!--begin::Table wrapper-->
                                        <div class="table-responsive">
                                            <!--begin::Table-->
                                            <table class="table align-middle table-row-dashed gy-5" id="kt_table_users_login_session">
                                                <thead class="border-bottom border-gray-200 fs-7 fw-bold">
                                                    <tr class="text-start text-muted text-uppercase gs-0">
                                                        <th class="min-w-100px">Location</th>
                                                        <th class="min-w-70px">Device</th>
                                                        <th class="min-w-80px">IP Address</th>
                                                        <th class="min-w-125px">Time</th>
                                                        <th class="min-w-70px">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="fs-6 fw-semibold text-gray-600">
                                                    <?php
                                                    if ($loginHistory) {
                                                        foreach ($loginHistory as $single_session) {
                                                            $date = new DateTime($single_session['login_datetime'], new DateTimeZone('UTC')); // or your original timezone
                                                            $date->setTimezone(new DateTimeZone('Asia/Kolkata')); // Set to IST
                                                            $login_datetime = $date->format('d-m-Y h:i:s');
                                                            ?>
                                                            <tr>
                                                                <td>India</td>
                                                                <td><?php echo $single_session['browser_name']; ?></td>
                                                                <td><?php echo $single_session['ip_address']; ?></td>
                                                                <td><?php echo $login_datetime; ?></td>
                                                                <td>
                                                                    <?php
                                                                    if ($single_session['logout_datetime']) {
                                                                        echo "Logout";
                                                                    } else {
                                                                        echo "Current Session";
                                                                    }
                                                                    ?>   
                                                                </td>
                                                            </tr>
                                                            <?php
                                                        }
                                                    } else {
                                                        echo "<tr colspan='5'>No Current Session Found</tr>";
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="kt_modal_update_details" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered mw-650px">
                        <div class="modal-content">
                            <form class="form" action="<?php echo $site_path; ?>/ajax/add-update-user-details" id="kt_modal_update_user_form">
                                <input type="hidden"  name="update_user_role_id" id="update_user_role_id" value="<?php echo $user_id; ?>">
                                <input type="hidden"  name="profile_picture_hidden" id="profile_picture_hidden" value="<?php echo $profile_image; ?>">
                                <div class="modal-header" id="kt_modal_update_user_header">
                                    <h2 class="fw-bold">Update User Details</h2>
                                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-users-modal-action="close">
                                        <i class="ki-outline ki-cross fs-1"></i>
                                    </div>                                    
                                </div>
                                <div class="modal-body py-10 px-lg-17">
                                    <div class="d-flex flex-column scroll-y me-n7 pe-7" id="kt_modal_update_user_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_update_user_header" data-kt-scroll-wrappers="#kt_modal_update_user_scroll" data-kt-scroll-offset="300px">
                                        <div class="fw-bolder fs-3 rotate collapsible mb-7" data-bs-toggle="collapse" href="#kt_modal_update_user_user_info" role="button" aria-expanded="false" aria-controls="kt_modal_update_user_user_info">User Information 
                                            <span class="ms-2 rotate-180">
                                                <i class="ki-outline ki-down fs-3"></i>
                                            </span>
                                        </div>
                                        <div id="kt_modal_update_user_user_info" class="collapse show">
                                            <div class="fv-row mb-7">
                                                <label class="fs-6 fw-semibold mb-2">Full Name</label>
                                                <input type="text" class="form-control form-control-solid" placeholder="" name="name" value="<?php echo $Fullname; ?>" />
                                            </div>
                                            <div class="fv-row mb-7">
                                                <label class="required fw-semibold fs-6 mb-2">Email ID</label>
                                                <input type="email" name="user_email" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="example@domain.com" value="<?php echo $currentEmail; ?>" />
                                            </div>
                                            <!--end::Input group-->
                                            <!--begin::Input group-->
                                            <div class="fv-row mb-7">
                                                <!--begin::Label-->
                                                <label class="fs-6 fw-semibold mb-2">Mobile Number</label>
                                                <input type="text" class="form-control form-control-solid" placeholder="" name="mobile_number" value="<?php echo $mobile_number; ?>" />
                                            </div>
                                            
                                            <div class="fv-row mb-7">
                                                <label class="form-label fw-semibold fs-6">Department</label>
                                                    <select name="department_id" id="department_id" aria-label="Department" data-control="select2" data-placeholder="Department" class="form-select form-select-solid form-select-lg fw-semibold">
                                                        <option value="">Select Department</option>
                                                        <?php if($all_department_list){
                                                                foreach($all_department_list as $single_dept){?>

                                                        <option value="<?php echo $single_dept['id']; ?>" <?php if($department_id == $single_dept['id']){ echo 'selected';} ?>><?php echo $single_dept['department_name']; ?></option>
                                                        <?php } } ?>
                                                    </select>
                                            </div>
                                            
                                            <div class="fav-row mb-7">
                                                <label class="required fw-semibold fs-6 mb-5">Brand Name</label>
                                                <select name="brand_name" id="brand_name" aria-label="Brand Name" data-control="select2" data-placeholder="Brand Name" class="form-select form-select-solid form-select-lg fw-semibold">
                                                    <option value="">Select Brand Name</option>
                                                    <option value="1" <?php if($company_id == 1) { echo 'selected';} ?>>Bullion Knot</option>
                                                    <option value="2" <?php if($company_id == 2) { echo 'selected';} ?>>Under3k</option>
                                                    <option value="3" <?php if($company_id == 3) { echo 'selected';} ?>>All Brand</option>
                                                </select>
                                            </div>

                                            <div class="fv-row mb-7">
                                                <label class="fw-semibold fs-6 mb-2">Mobile Module Access</label>
                                                <div class="d-flex align-items-center flex-wrap mt-3">

                                                    <?php
                                                    if ($scan_app_modules) {
                                                        foreach ($scan_app_modules as $single_module) {
                                                            $checked = '';
                                                            if (in_array($single_module['id'], $modules)) {
                                                                $checked = 'checked';
                                                            }
                                                            ?>

                                                            <label class="form-check form-check-custom form-check-inline form-check-solid me-5 mb-3">
                                                                <input class="form-check-input permission-checkbox" 
                                                                       type="checkbox" 
                                                                       <?php echo $checked; ?>
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
                                        </div>
                                        <div class="fv-row mb-7 mb-6">
                                            <label class="fs-6 fw-semibold mb-2">
                                                <span>Status</span>
                                            </label>
                                            <div class="col-md-12">
                                                <div class="row row-cols-1 row-cols-md-3 row-cols-lg-1 row-cols-xl-3 g-9" data-kt-buttons="true" data-kt-buttons-target="[data-kt-button='true']">
                                                    <div class="col">
                                                        <?php
                                                        $class = $checked = '';
                                                        if ($status == 1) {
                                                            $class = 'active';
                                                            $checked = 'checked';
                                                        }
                                                        ?>
                                                        <label class="btn btn-outline btn-outline-dashed btn-active-light-primary d-flex text-start p-6 <?php echo $class; ?>" data-kt-button="true">
                                                            <span class="form-check form-check-custom form-check-solid form-check-sm align-items-start mt-1">
                                                                <input class="form-check-input" type="radio" name="status" value="1" <?php echo $checked; ?> />
                                                            </span>
                                                            <span class="ms-5">
                                                                <span class="fs-4 fw-bold text-gray-800 d-block">Active</span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                    <div class="col">
                                                        <?php
                                                        $class = $checked = '';
                                                        if ($status == 0) {
                                                            $class = 'active';
                                                            $checked = 'checked';
                                                        }
                                                        ?>
                                                        <label class="btn btn-outline btn-outline-dashed btn-active-light-primary d-flex text-start p-6 <?php echo $class; ?>" data-kt-button="true">
                                                            <span class="form-check form-check-custom form-check-solid form-check-sm align-items-start mt-1">
                                                                <input class="form-check-input" type="radio" name="status" value="0" <?php echo $checked; ?> />
                                                            </span>
                                                            <span class="ms-5">
                                                                <span class="fs-4 fw-bold text-gray-800 d-block">Inactive</span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="fv-row mb-7 mb-6">
                                            <label class="fs-6 fw-semibold mb-2">
                                                <span>Face Attendance</span>
                                            </label>
                                            <div class="col-md-12">
                                                <div class="row row-cols-1 row-cols-md-3 row-cols-lg-1 row-cols-xl-3 g-9" data-kt-buttons="true" data-kt-buttons-target="[data-kt-button='true']">
                                                    <div class="col">
                                                        <?php
                                                        $class = $checked = '';
                                                        if ($face_attendance == 1) {
                                                            $class = 'active';
                                                            $checked = 'checked';
                                                        }
                                                        ?>
                                                        <label class="btn btn-outline btn-outline-dashed btn-active-light-primary d-flex text-start p-6 <?php echo $class; ?>" data-kt-button="true">
                                                            <span class="form-check form-check-custom form-check-solid form-check-sm align-items-start mt-1">
                                                                <input class="form-check-input" type="radio" name="face_attendance" value="<?php echo $face_attendance; ?>" <?php echo $checked; ?> />
                                                            </span>
                                                            <span class="ms-5">
                                                                <span class="fs-4 fw-bold text-gray-800 d-block">Yes</span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                    <div class="col">
                                                        <?php
                                                        $class = $checked = '';
                                                        if ($face_attendance == 0) {
                                                            $class = 'active';
                                                            $checked = 'checked';
                                                        }
                                                        ?>
                                                        <label class="btn btn-outline btn-outline-dashed btn-active-light-primary d-flex text-start p-6 <?php echo $class; ?>" data-kt-button="true">
                                                            <span class="form-check form-check-custom form-check-solid form-check-sm align-items-start mt-1">
                                                                <input class="form-check-input" type="radio" name="face_attendance" value="<?php echo $face_attendance; ?>" <?php echo $checked; ?>/>
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
                                </div>
                                <div class="modal-footer flex-center">
                                    <button type="reset" class="btn btn-light me-3" data-kt-users-modal-action="cancel">Discard</button>
                                    <button type="submit" class="btn btn-primary" data-kt-users-modal-action="submit">
                                        <span class="indicator-label">Submit</span>
                                        <span class="indicator-progress">Please wait... 
                                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="kt_modal_update_email" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered mw-650px">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h2 class="fw-bold">Update Email Address</h2>
                                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-users-modal-action="close">
                                    <i class="ki-outline ki-cross fs-1"></i>
                                </div>
                            </div>
                            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                                <form id="kt_modal_update_email_form" class="form" action="<?php echo $site_path; ?>/ajax/add-update-user-details">
                                    <input type="hidden" name="role_id" id="role_id" value="<?php echo $user_id; ?>">
                                    <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed mb-9 p-6">
                                        <i class="ki-outline ki-information fs-2tx text-primary me-4"></i>
                                        <div class="d-flex flex-stack flex-grow-1">
                                            <div class="fw-semibold">
                                                <div class="fs-6 text-gray-700">Please note that a valid email address is required to complete the email verification.</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="fv-row mb-7">
                                        <label class="fs-6 fw-semibold form-label mb-2">
                                            <span class="required">Email Address</span>
                                        </label>
                                        <input class="form-control form-control-solid" placeholder="" name="profile_email" value="<?php echo $currentEmail; ?>" />
                                        <!--end::Input-->
                                    </div>
                                    <!--end::Input group-->
                                    <!--begin::Actions-->
                                    <div class="text-center pt-15">
                                        <button type="reset" class="btn btn-light me-3" data-kt-users-modal-action="cancel">Discard</button>
                                        <button type="submit" class="btn btn-primary" data-kt-users-modal-action="submit">
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
                <div class="modal fade" id="kt_modal_update_password" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered mw-650px">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h2 class="fw-bold">Update Password</h2>
                                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-users-modal-action="close">
                                    <i class="ki-outline ki-cross fs-1"></i>
                                </div>
                            </div>
                            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                                <form id="kt_modal_update_password_form" class="form" action="<?php echo $site_path; ?>/ajax/add-update-user-detailsp">
                                    <input type="hidden" name="update_password_role_id" id="update_password_role_id" value="<?php echo $user_id; ?>">
                                    <div class="fv-row mb-10">
                                        <label class="required form-label fs-6 mb-2">Current Password</label>
                                        <input class="form-control form-control-lg form-control-solid" type="password" placeholder="" name="current_password" autocomplete="off" />
                                    </div>
                                    <div class="mb-10 fv-row" data-kt-password-meter="true">
                                        <div class="mb-1">
                                            <!--begin::Label-->
                                            <label class="form-label fw-semibold fs-6 mb-2">New Password</label>
                                            <!--end::Label-->
                                            <!--begin::Input wrapper-->
                                            <div class="position-relative mb-3">
                                                <input class="form-control form-control-lg form-control-solid" type="password" placeholder="" name="new_password" autocomplete="off" />
                                                <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2" data-kt-password-meter-control="visibility">
                                                    <i class="ki-outline ki-eye-slash fs-1"></i>
                                                    <i class="ki-outline ki-eye d-none fs-1"></i>
                                                </span>
                                            </div>
                                            <!--end::Input wrapper-->
                                            <!--begin::Meter-->
                                            <div class="d-flex align-items-center mb-3" data-kt-password-meter-control="highlight">
                                                <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                                                <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                                                <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                                                <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px"></div>
                                            </div>
                                            <!--end::Meter-->
                                        </div>
                                        <!--end::Wrapper-->
                                        <!--begin::Hint-->
                                        <div class="text-muted">Use 8 or more characters with a mix of letters, numbers & symbols.</div>
                                        <!--end::Hint-->
                                    </div>
                                    <!--end::Input group=-->
                                    <!--begin::Input group=-->
                                    <div class="fv-row mb-10">
                                        <label class="form-label fw-semibold fs-6 mb-2">Confirm New Password</label>
                                        <input class="form-control form-control-lg form-control-solid" type="password" placeholder="" name="confirm_password" autocomplete="off" />
                                    </div>
                                    <!--end::Input group=-->
                                    <!--begin::Actions-->
                                    <div class="text-center pt-15">
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
                <!--end::Modal - Update password-->
                <!--begin::Modal - Update role-->
                <div class="modal fade" id="kt_modal_update_role" tabindex="-1" aria-hidden="true">
                    <!--begin::Modal dialog-->
                    <div class="modal-dialog modal-dialog-centered mw-650px">
                        <!--begin::Modal content-->
                        <div class="modal-content">
                            <!--begin::Modal header-->
                            <div class="modal-header">
                                <h2 class="fw-bold">Update User Role</h2>
                                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-users-modal-action="close">
                                    <i class="ki-outline ki-cross fs-1"></i>
                                </div>
                            </div>
                            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                                <form id="kt_modal_update_role_form" class="form" action="<?php echo $site_path; ?>/update-user-details">
                                    <input type="hidden" name="role_id" id="role_id" value="<?php echo $user_id; ?>">
                                    <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed mb-9 p-6">
                                        <i class="ki-outline ki-information fs-2tx text-primary me-4"></i>
                                        <div class="d-flex flex-stack flex-grow-1">
                                            <div class="fw-semibold">
                                                <div class="fs-6 text-gray-700">Please note that reducing a user role rank, that user will lose all priviledges that was assigned to the previous role.</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="fv-row mb-7">
                                        <label class="fs-6 fw-semibold form-label mb-5">
                                            <span class="required">Select a user role</span>
                                        </label>
                                        <?php
                                        $current_role_name = isset($userDetails[0]['typee']) ? explode(",", $userDetails[0]['typee']) : "";
                                        if ($roleList) {
                                            foreach ($roleList as $single_role) {
                                                $checked = '';

                                                if (in_array($single_role['slug'], $current_role_name)) {
                                                    $checked = "checked";
                                                }
                                                ?>
                                                <div class="d-flex">
                                                    <!--begin::Radio-->
                                                    <div class="form-check form-check-custom form-check-solid">
                                                        <!--begin::Input-->
                                                        <input class="form-check-input me-3" name="user_role[]" type="checkbox" data-id="<?php echo $single_role['id']; ?>" value="<?php echo $single_role['slug'] ?>" id="kt_modal_update_role_option_<?php echo $single_role['slug'] ?>" <?php echo $checked; ?>  />
                                                        <!--end::Input-->
                                                        <!--begin::Label-->
                                                        <label class="form-check-label" for="kt_modal_update_role_option_<?php echo $single_role['slug'] ?>">
                                                            <div class="fw-bold text-gray-800"><?php echo $single_role['role_name'] ?></div>
                                                        </label>
                                                    </div>
                                                </div>
                                                <!--end::Input row-->
                                                <div class='separator separator-dashed my-5'></div>
                                            <?php
                                            }
                                        }
                                        ?>
                                    </div>
                                    <!--end::Input group-->
                                    <!--begin::Actions-->
                                    <div class="text-center pt-15">
                                        <button type="reset" class="btn btn-light me-3" data-kt-users-modal-action="cancel">Discard</button>
                                        <button type="submit" class="btn btn-primary" data-kt-users-modal-action="submit">
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
                <!--end::Modal - Add task-->
                <!--begin::Modal - Add task-->
                <div class="modal fade" id="kt_modal_add_one_time_password" tabindex="-1" aria-hidden="true">
                    <!--begin::Modal dialog-->
                    <div class="modal-dialog modal-dialog-centered mw-650px">
                        <!--begin::Modal content-->
                        <div class="modal-content">
                            <!--begin::Modal header-->
                            <div class="modal-header">
                                <!--begin::Modal title-->
                                <h2 class="fw-bold">Enable One Time Password</h2>
                                <!--end::Modal title-->
                                <!--begin::Close-->
                                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-users-modal-action="close">
                                    <i class="ki-outline ki-cross fs-1"></i>
                                </div>
                                <!--end::Close-->
                            </div>
                            <!--end::Modal header-->
                            <!--begin::Modal body-->
                            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                                <!--begin::Form-->
                                <form class="form" id="kt_modal_add_one_time_password_form">
                                    <!--begin::Label-->
                                    <div class="fw-bold mb-9">Enter the new phone number to receive an SMS to when you log in.</div>
                                    <!--end::Label-->
                                    <!--begin::Input group-->
                                    <div class="fv-row mb-7">
                                        <!--begin::Label-->
                                        <label class="fs-6 fw-semibold form-label mb-2">
                                            <span class="required">Mobile number</span>
                                            <span class="ms-2" data-bs-toggle="tooltip" title="A valid mobile number is required to receive the one-time password to validate your account login.">
                                                <i class="ki-outline ki-information fs-7"></i>
                                            </span>
                                        </label>
                                        <!--end::Label-->
                                        <!--begin::Input-->
                                        <input type="text" class="form-control form-control-solid" name="otp_mobile_number" placeholder="+6123 456 789" value="" />
                                        <!--end::Input-->
                                    </div>
                                    <!--end::Input group-->
                                    <!--begin::Separator-->
                                    <div class="separator saperator-dashed my-5"></div>
                                    <!--end::Separator-->
                                    <!--begin::Input group-->
                                    <div class="fv-row mb-7">
                                        <!--begin::Label-->
                                        <label class="fs-6 fw-semibold form-label mb-2">
                                            <span class="required">Email</span>
                                        </label>
                                        <!--end::Label-->
                                        <!--begin::Input-->
                                        <input type="email" class="form-control form-control-solid" name="otp_email" value="smith@kpmg.com" readonly="readonly" />
                                        <!--end::Input-->
                                    </div>
                                    <!--end::Input group-->
                                    <!--begin::Input group-->
                                    <div class="fv-row mb-7">
                                        <!--begin::Label-->
                                        <label class="fs-6 fw-semibold form-label mb-2">
                                            <span class="required">Confirm password</span>
                                        </label>
                                        <!--end::Label-->
                                        <!--begin::Input-->
                                        <input type="password" class="form-control form-control-solid" name="otp_confirm_password" value="" />
                                        <!--end::Input-->
                                    </div>
                                    <!--end::Input group-->
                                    <!--begin::Actions-->
                                    <div class="text-center pt-15">
                                        <button type="reset" class="btn btn-light me-3" data-kt-users-modal-action="cancel">Cancel</button>
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
                <!--end::Modal - Add task-->
                <!--end::Modals-->
            </div>
            <!--end::Content container-->
        </div>
        <!--end::Content-->
    </div>
    <!--end::Content wrapper-->
    <!--begin::Footer-->
     <?php include("includes/footer.php"); ?>
</div>
<!--end:::Main-->
</div>
<!--end::aside-->
</div>
<!--end::Wrapper-->
</div>
<!--end::Page-->
</div>
<!--end::App-->
<!--begin::Scrolltop-->
<div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
    <i class="ki-outline ki-arrow-up"></i>
</div>
<!--end::Scrolltop-->
<!--end::Modals-->
<!--begin::Javascript-->
<script>var hostUrl = "<?php echo $site_path; ?>/";</script>
<!--begin::Global Javascript Bundle(mandatory for all pages)-->
<script src="<?php echo $site_path; ?>/assets/plugins/global/plugins.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/scripts.bundle.js"></script>
<!--end::Global Javascript Bundle-->
<!--begin::Vendors Javascript(used for this page only)-->
<script src="<?php echo $site_path; ?>/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<!--end::Vendors Javascript-->
<!--begin::Custom Javascript(used for this page only)-->
<script src="<?php echo $site_path; ?>/assets/js/custom/apps/user-management/users/view/view.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/custom/apps/user-management/users/view/update-details.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/custom/apps/user-management/users/view/add-schedule.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/custom/apps/user-management/users/view/add-task.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/custom/apps/user-management/users/view/update-email.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/custom/apps/user-management/users/view/update-password.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/custom/apps/user-management/users/view/update-role.js?v=<?php echo time(); ?>"></script>

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

</script>
</body>
</html>