<?php
include "config/database.php";
include "config/auth_check.php";
include "includes/sidemenu.php";
?>
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_toolbar" class="app-toolbar pt-7 pt-lg-10">
            <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
                <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                    <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                        <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">CREATE MATERIAL</h1>
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                            <li class="breadcrumb-item text-muted">
                                <a href="<?php echo $site_path; ?>/dashboard" class="text-muted text-hover-primary">Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted">CREATE MATERIAL</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div id="kt_app_content" class="app-content">
            <div id="kt_app_content_container" class="app-container container-fluid">
                <form id="kt_create_material_form" action="<?php echo $site_path ?>/ajax/add-update-material-details" class="form" method="POST" enctype="multipart/form-data">
                    <input name="redirect_page" type="hidden" id="redirect_page" value="<?php echo $site_path ?>/material" />
                    <div class="card-body border-top p-9">
                        <div class="row mb-6">
                            <div class="col-lg-6 fv-row">
                                <label class="form-label required fw-semibold fs-6">Material Name</label>
                                <input type="text" name="material_name" id="material_name" class="form-control form-control-lg form-control-solid" placeholder="Material Name" />
                            </div>
                            <div class="col-lg-6 fv-row">
                                <label class="form-label required fw-semibold fs-6">Unit</label>
                                <input type="text" name="unit" id="unit" class="form-control form-control-lg form-control-solid" placeholder="Unit"/>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-end py-6 px-9">
                        <button type="reset" class="btn btn-light btn-active-light-primary me-2" onclick="window.location.href = '<?php echo $site_path; ?>/material-list'">Discard</button>
                        <button type="submit" class="btn btn-primary" id="kt_create_material_details_submit">Save Changes</button>
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
<script src="<?php echo $site_path; ?>/assets/js/custom/create-material.js?v=<?php echo time(); ?>"></script>
<script>
    $('.numeric-only').on('keypress', function (e) {
        if (e.which < 48 || e.which > 57) {
            e.preventDefault();
        }
    });
</script>
</body>
</html>