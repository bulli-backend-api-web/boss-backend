<?php
include "config/database.php";
include "config/auth_check.php";
include "includes/sidemenu.php";

$category_list = get_all_tag_list();
$staff_list = departmentwise_user_list(8);
?>
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_toolbar" class="app-toolbar pt-7 pt-lg-10">
            <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
                <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                    <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                        <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">Create Sample</h1>
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <a href="<?php echo $site_path; ?>/dashboard" class="text-muted text-hover-primary">Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted">Create Sample</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div id="kt_app_content" class="app-content">
            <div id="kt_app_content_container" class="app-container container-fluid">
                <form id="kt_create_sample_form" action="<?php echo $site_path ?>/ajax/add-update-sample-details" class="form" method="POST" enctype="multipart/form-data">
                    <!--begin::Card body-->
                    <input name="redirect_page" type="hidden" id="redirect_page" value="<?php echo $site_path ?>/sampling-list" />
                    <div class="card-body border-top p-9">
                        <div class="row mb-6">
                            <div class="col-lg-6 fv-row">
                                <label class="form-label required fw-semibold fs-6">Design brief ID</label>
                                <input type="text" name="design_id" id="design_id" class="form-control form-control-lg form-control-solid" placeholder="e.g D-0001" />
                            </div>
                            <div class="col-lg-6 fv-row">
                                <label class="form-label required fw-semibold fs-6">Sample name / description</label>
                                <input type="text" name="sample_name" id="sample_name" class="form-control form-control-lg form-control-solid" placeholder="e.g. Mirror work suit" />
                            </div>
                        </div>
                        <div class="row mb-6">
                             <div class="col-lg-6 fv-row">
                                <label class="form-label fw-semibold fs-6">Category</label>
                                <select name="category" id="category" aria-label="Category" data-control="select2" data-placeholder="Category" class="form-select form-select-solid form-select-lg fw-semibold">
                                    <option value="">Select Category</option>
                                    <?php if($category_list){
                                            foreach($category_list as $single_category){?>
                                    
                                    <option value="<?php echo $single_category['id']; ?>"><?php echo $single_category['name']; ?></option>
                                    <?php } } ?>
                                </select>
                            </div>
                            <div class="col-lg-6 fv-row">
                                <label class="form-label fw-semibold fs-6">Assign sampler</label>
                                <select name="assign_to" id="assign_to" aria-label="Category" data-control="select2" data-placeholder="Sampler" class="form-select form-select-solid form-select-lg fw-semibold">
                                    <option value="">Select Sampler</option>
                                    <?php if($staff_list){
                                            foreach($staff_list as $single_user){?>
                                    
                                    <option value="<?php echo $single_user['id']; ?>"><?php echo $single_user['name']; ?></option>
                                    <?php } } ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-6">
                             <div class="col-lg-6 fv-row">
                                <label class="form-label required fw-semibold fs-6">Budget(₹)</label>
                                <input type="text" name="budget" id="budget" class="form-control form-control-lg form-control-solid" placeholder="Budget"/>
                            </div>
                            <div class="col-lg-6 fv-row">
                                <label class="form-label">Target days</label>
                                <input type="text" name="target_days" id="target_days" class="form-control form-control-lg form-control-solid"/>
                            </div>
                        </div>
                        <div class="row mb-6">
                             <div class="col-lg-6 fv-row">
                                <label class="form-label">Fabric / materials list</label>
                                <input type="text" name="fabric" id="fabric" class="form-control form-control-lg form-control-solid deduction" placeholder="Fabric"/>
                            </div>
                            <div class="col-lg-6 fv-row">
                                <label class="form-label">Special instructions</label>
                                <input type="text" name="remarks" id="remarks" class="form-control form-control-lg form-control-solid deduction" placeholder="Remarks"/>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-end py-6 px-9">
                        <button type="reset" class="btn btn-light btn-active-light-primary me-2" onclick="window.location.href = '<?php echo $site_path; ?>/design-list'">Discard</button>
                        <button type="submit" class="btn btn-primary" id="kt_create_sample_details_submit">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php include("includes/footer.php"); ?>
</div>
</div>
</div>
<script>var hostUrl = "<?php echo $site_path; ?>/";</script>
<script src="<?php echo $site_path; ?>/assets/plugins/global/plugins.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/scripts.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/custom/create-sample.js?v=<?php echo time(); ?>"></script>
<script>
    $('#minimum_sketch,#budget').on('keypress', function (e) {
        if (e.which < 48 || e.which > 57) {
            e.preventDefault();
        }
    });
</script>
</body>
</html>