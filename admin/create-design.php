<?php
include "config/database.php";
include "config/auth_check.php";
include "includes/sidemenu.php";

$staff_list = getAllStaffList();
$model_list = model_list();
$design_code = generate_design_code();
$category_list = getCategoryList();
?>
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_toolbar" class="app-toolbar pt-7 pt-lg-10">
            <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
                <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                    <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                        <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">CREATE DESIGN</h1>
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <a href="<?php echo $site_path; ?>/dashboard" class="text-muted text-hover-primary">Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted">CREATE DESIGN</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div id="kt_app_content" class="app-content">
            <div id="kt_app_content_container" class="app-container container-fluid">
                <form id="kt_create_design_form" action="<?php echo $site_path ?>/ajax/add-update-design-details" class="form" method="POST" enctype="multipart/form-data">
                    <!--begin::Card body-->
                    <input name="redirect_page" type="hidden" id="redirect_page" value="<?php echo $site_path ?>/design-list" />
                    <div class="card-body border-top p-9">
                        <div class="row mb-6">
                            <div class="col-lg-6 fv-row">
                                <label class="form-label required fw-semibold fs-6">Design Name</label>
                                <input type="text" name="design_name" id="design_name" class="form-control form-control-lg form-control-solid" placeholder="Design Name" />
                            </div>
                            <div class="col-lg-6 fv-row">
                                <label class="form-label required fw-semibold fs-6">Design Code</label>
                                <input type="text" name="design_code" id="design_code" class="form-control form-control-lg form-control-solid" placeholder="Design Code"  value="<?php echo $design_code; ?>"/>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <div class="col-lg-6 fv-row">
                                <label class="form-label required fw-semibold fs-6">Occasion</label>
                                <input type="text" name="occasion" id="occasion" class="form-control form-control-lg form-control-solid" placeholder="Ocassion"/>
                            </div>
                            <div class="col-lg-6 fv-row">
                                <label class="form-label">Style</label>
                                <select name="style" id="style" aria-label="Model" data-control="select2" data-placeholder="Style" class="form-select form-select-solid form-select-lg fw-semibold">
                                    <option value="1">Select Style</option>
                                    <?php if($category_list){
                                            foreach($category_list as $single_cat){?>
                                                <option value="<?php echo $single_cat['id']; ?>"><?php echo $single_cat['name']; ?></option>
                                          <?php  }
                                     }?>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-6">
                             <div class="col-lg-6 fv-row">
                                <label class="form-label required fw-semibold fs-6">Color</label>
                                <input type="text" name="color" id="color" class="form-control form-control-lg form-control-solid" placeholder="color"/>
                            </div>
                            <div class="col-lg-6 fv-row">
                                <label class="form-label">Reference</label>
                                <input type="file" name="reference[]" id="reference" class="form-control form-control-lg form-control-solid"  multiple/>
                            </div>
                        </div>
                        <div class="row mb-6">
                             <div class="col-lg-6 fv-row">
                                <label class="form-label">Budget</label>
                                <input type="text" name="budget" id="budget" class="form-control form-control-lg form-control-solid deduction" placeholder="Budget"/>
                            </div>
                            <div class="col-lg-6 fv-row">
                                <label class="form-label">Minimum Sketches</label>
                                <input type="text" name="minimum_sketch" id="minimum_sketch" class="form-control form-control-lg form-control-solid deduction" placeholder="Minimum Sketch"/>
                            </div>
                        </div>
                        
                        <div class="row mb-6">
                             <div class="col-lg-6 fv-row">
                                <label class="form-label">Reference Link</label>
                                <input type="text" name="reference_link" id="reference_link" class="form-control form-control-lg form-control-solid deduction" placeholder="Reference Link"/>
                            </div>
                            <div class="col-lg-6 fv-row">
                                <label class="form-label">Brand Name</label>
                                <select name="brand_name" id="brand_name" aria-label="Brand Name" data-control="select2" data-placeholder="Brand Name" class="form-select form-select-solid form-select-lg fw-semibold">
                                    <option value="Bullion Knot">Bullion Knot</option>
                                    <option value="Under3K">Under3K</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mb-6">
                            <div class="col-lg-6 fv-row">
                                <label class="form-label">Embrodary</label>
                                <select name="embrodary" id="embrodary" aria-label="Embrodary" data-control="select2" data-placeholder="Embrodary" class="form-select form-select-solid form-select-lg fw-semibold">
                                    <option value="1">Ambrodary 1</option>
                                    <option value="2">Ambrodary 2</option>
                                </select>
                            </div>
                            <div class="col-lg-6 fv-row">
                                <label class="form-label">Model</label>
                                <select name="model" id="model" aria-label="Model" data-control="select2" data-placeholder="Model" class="form-select form-select-solid form-select-lg fw-semibold">
                                    <option value="1">Select Model</option>
                                    <?php if($model_list){
                                            foreach($model_list as $single_model){?>
                                                <option value="<?php echo $single_model['id']; ?>"><?php echo $single_model['first_name']." ".$single_model['last_name']; ?></option>
                                          <?php  }
                                     }?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mb-6">
                            <div class="col-lg-6 fv-row">
                                <label class="form-label fw-semibold fs-6">Assign To</label>
                                <select name="assign_to" id="assign_to" aria-label="Assign To" data-control="select2" data-placeholder="Assign To" class="form-select form-select-solid form-select-lg fw-semibold">
                                    <option value="">Select Staff</option>
                                    <?php if($staff_list){
                                            foreach($staff_list as $single_Staff){?>
                                    
                                    <option value="<?php echo $single_Staff['id']; ?>"><?php echo $single_Staff['name']; ?></option>
                                    <?php } } ?>
                                </select>
                            </div>
                            <div class="col-lg-6 fv-row">
                                <label class="form-label">Due Date</label>
                                <input type="text" name="due_date" id="due_date" class="form-control form-control-lg form-control-solid" placeholder="Due Date" readonly/>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-end py-6 px-9">
                        <button type="reset" class="btn btn-light btn-active-light-primary me-2" onclick="window.location.href = '<?php echo $site_path; ?>/design-list'">Discard</button>
                        <button type="submit" class="btn btn-primary" id="kt_create_design_details_submit">Save Changes</button>
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
<script src="<?php echo $site_path; ?>/assets/js/custom/create-design.js?v=<?php echo time(); ?>"></script>
<script>
    $(document).ready(function () {
        $("#due_date").flatpickr({
            altInput: true,
            altFormat: "Y-m-d H:i",
            dateFormat: "Y-m-d H:i",
            enableTime: true,
            time_24hr: true 
        });
    });

    $('#minimum_sketch,#budget').on('keypress', function (e) {
        if (e.which < 48 || e.which > 57) {
            e.preventDefault();
        }
    });
</script>
</body>
</html>