<?php
include("config/database.php");
include("config/auth_check.php");
include("includes/sidemenu.php");

$todays_orders = $vastranand_order = $siripattu_order = $today_all_order = $total_users_this_month = $total_activeuser = $total_app_user = $total_shopify_user = 0;
?>
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_toolbar" class="app-toolbar pt-7 pt-lg-10">
            <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
                <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                    <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                        <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">Bullionknot Dashboard</h1>
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                            <li class="breadcrumb-item text-muted">
                                <a href="javascript:void(0);" class="text-muted text-hover-primary">Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted">Dashboards</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div id="kt_app_content" class="app-content">
            <div id="kt_app_content_container" class="app-container container-fluid">
                <div class="row gx-5 gx-xl-10 mb-xl-10">
                    <div class="col-md-4 col-lg-4 col-xl-4 col-xxl-3 mb-10">
                        <div class="card card-flush h-md-10 mb-5">
                            <div class="card-body d-flex justify-content-between align-items-center py-5 px-6">
                                <div class="d-flex flex-column">
                                    <span class="text-gray-500 fw-semibold fs-7 text-uppercase">Active Orders</span>
                                    <span class="fs-2hx fw-bold text-gray-900 mt-2" id="todayOrdersCount"><?php echo $today_all_order; ?></span>
                                </div>
                                <div class="symbol symbol-50px symbol-light-primary">
                                    <span class="symbol-label">
                                        <i class="ki-outline ki-notepad fs-2x text-primary"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="card-footer py-3 border-0 bg-light">
                                <a href="javascript:void(0);" class="text-primary fw-semibold d-flex align-items-center justify-content-center">
                                    More Info <i class="ki-outline ki-right fs-5 ms-2"></i>
                                </a>
                            </div>
                        </div>

                    </div>
                    <div class="col-md-4 col-lg-4 col-xl-4 col-xxl-3 mb-10">
                        <div class="card card-flush h-md-10 mb-5">
                            <div class="card-body d-flex justify-content-between align-items-center py-5 px-6">
                                <div class="d-flex flex-column">
                                    <span class="text-gray-500 fw-semibold fs-7 text-uppercase">In Production</span>
                                    <span class="fs-2hx fw-bold text-gray-900 mt-2" id="todayOrdersCount">0</span>
                                </div>
                                <div class="symbol symbol-50px symbol-light-primary">
                                    <span class="symbol-label">
                                        <i class="ki-outline ki-notepad fs-2x text-primary"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="card-footer py-3 border-0 bg-light">
                                <a href="javascript:void(0);" class="text-primary fw-semibold d-flex align-items-center justify-content-center">
                                    More Info <i class="ki-outline ki-right fs-5 ms-2"></i>
                                </a>
                            </div>
                            <!--end::Footer-->
                        </div>
                    </div>
                    
                    <div class="col-md-4 col-lg-4 col-xl-4 col-xxl-3 mb-10">
                        <div class="card card-flush h-md-10 mb-5">
                            <div class="card-body d-flex justify-content-between align-items-center py-5 px-6">
                                <div class="d-flex flex-column">
                                    <span class="text-gray-500 fw-semibold fs-7 text-uppercase">Ready to Ship</span>
                                    <span class="fs-2hx fw-bold text-gray-900 mt-2" id="todayOrdersCount">0</span>
                                </div>
                                <div class="symbol symbol-50px symbol-light-primary">
                                    <span class="symbol-label">
                                        <i class="ki-outline ki-notepad fs-2x text-primary"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="card-footer py-3 border-0 bg-light">
                                <a href="javascript:void(0);" class="text-primary fw-semibold d-flex align-items-center justify-content-center">
                                    More Info <i class="ki-outline ki-right fs-5 ms-2"></i>
                                </a>
                            </div>
                            <!--end::Footer-->
                        </div>
                    </div>
                    
                    <div class="col-md-4 col-lg-4 col-xl-4 col-xxl-3 mb-10">
                        <div class="card card-flush h-md-10 mb-5">
                            <div class="card-body d-flex justify-content-between align-items-center py-5 px-6">
                                <div class="d-flex flex-column">
                                    <span class="text-gray-500 fw-semibold fs-7 text-uppercase">Today's Revenue</span>
                                    <span class="fs-2hx fw-bold text-gray-900 mt-2" id="todayOrdersCount">0</span>
                                </div>
                                <div class="symbol symbol-50px symbol-light-primary">
                                    <span class="symbol-label">
                                        <i class="ki-outline ki-dollar fs-2x text-success"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="card-footer py-3 border-0 bg-light">
                                <a href="javascript:void(0);" class="text-primary fw-semibold d-flex align-items-center justify-content-center">
                                    More Info <i class="ki-outline ki-right fs-5 ms-2"></i>
                                </a>
                            </div>
                            <!--end::Footer-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--begin::Footer-->
    <?php include("includes/footer.php"); ?>
</div>
</div>
</div>
</div>
<script>var hostUrl = "<?php echo $site_path; ?>/";</script>
<script src="<?php echo $site_path; ?>/assets/plugins/global/plugins.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/scripts.bundle.js"></script>
</body>
<!--end::Body-->
</html>