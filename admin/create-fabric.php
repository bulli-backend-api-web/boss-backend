<?php
include "config/database.php";
include "config/auth_check.php";
include "includes/sidemenu.php";

$supplier_list = [];//getSupplierList();      // implement similar to getAllStaffList()
$fabric_code   = '123';//generate_fabric_code(); // implement similar to generate_design_code()
?>
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_toolbar" class="app-toolbar pt-7 pt-lg-10">
            <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
                <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                    <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                        <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">CREATE FABRIC</h1>
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <a href="<?php echo $site_path; ?>/dashboard" class="text-muted text-hover-primary">Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted">CREATE FABRIC</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div id="kt_app_content" class="app-content">
            <div id="kt_app_content_container" class="app-container container-fluid">
                <form id="kt_create_fabric_form" action="<?php echo $site_path ?>/ajax/add-update-fabric-details" class="form" method="POST" enctype="multipart/form-data">
                    <!--begin::Card body-->
                    <input name="redirect_page" type="hidden" id="redirect_page" value="<?php echo $site_path ?>/fabric" />
                    <div class="card-body border-top p-9">
                        <div class="row mb-6">
                            <div class="col-lg-6 fv-row">
                                <label class="form-label required fw-semibold fs-6">Fabric Name</label>
                                <input type="text" name="fabric_name" id="fabric_name" class="form-control form-control-lg form-control-solid" placeholder="Fabric Name" />
                            </div>
                            <div class="col-lg-6 fv-row">
                                <label class="form-label required fw-semibold fs-6">Fabric Code</label>
                                <input type="text" name="fabric_code" id="fabric_code" class="form-control form-control-lg form-control-solid" placeholder="Fabric Code" value="<?php echo $fabric_code; ?>" />
                            </div>
                        </div>
                        <div class="row mb-6">
                            <div class="col-lg-6 fv-row">
                                <label class="form-label required fw-semibold fs-6">Fabric Type</label>
                                <select name="fabric_type" id="fabric_type" aria-label="Fabric Type" data-control="select2" data-placeholder="Fabric Type" class="form-select form-select-solid form-select-lg fw-semibold">
                                    <option value="">Select Type</option>
                                    <option value="Woven">Woven</option>
                                    <option value="Knitted">Knitted</option>
                                    <option value="Denim">Denim</option>
                                    <option value="Satin">Satin</option>
                                    <option value="Velvet">Velvet</option>
                                    <option value="Linen">Linen</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-lg-6 fv-row">
                                <label class="form-label fw-semibold fs-6">Color</label>
                                <input type="text" name="color" id="color" class="form-control form-control-lg form-control-solid" placeholder="Color" />
                            </div>
                        </div>
                        <div class="row mb-6">
                            <div class="col-lg-6 fv-row">
                                <label class="form-label">Composition</label>
                                <input type="text" name="composition" id="composition" class="form-control form-control-lg form-control-solid" placeholder="e.g. 100% Cotton" />
                            </div>
                            <div class="col-lg-6 fv-row">
                                <label class="form-label">GSM</label>
                                <input type="text" name="gsm" id="gsm" class="form-control form-control-lg form-control-solid numeric-only" placeholder="GSM" />
                            </div>
                        </div>
                        <div class="row mb-6">
                            <div class="col-lg-6 fv-row">
                                <label class="form-label">Panno</label>
                                <input type="text" name="width" id="width" class="form-control form-control-lg form-control-solid numeric-only" placeholder="Width (inches)" />
                            </div>
                            <div class="col-lg-6 fv-row">
                                <label class="form-label">Unit</label>
                                <select name="unit" id="unit" aria-label="Unit" data-control="select2" data-placeholder="Unit" class="form-select form-select-solid form-select-lg fw-semibold">
                                    <option value="Meter">Meter</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <div class="col-lg-6 fv-row">
                                <label class="form-label">Default Rate (per Meter)</label>
                                <input type="text" name="default_rate" id="default_rate" class="form-control form-control-lg form-control-solid numeric-only" placeholder="Rate" />
                            </div>
                            <div class="col-lg-6 fv-row">
                                <label class="form-label">Supplier / Vendor</label>
                                <select name="supplier_id" id="supplier_id" aria-label="Supplier" data-control="select2" data-placeholder="Supplier" class="form-select form-select-solid form-select-lg fw-semibold">
                                    <option value="">Select Supplier</option>
                                    <?php if ($supplier_list) {
                                            foreach ($supplier_list as $single_supplier) { ?>
                                                <option value="<?php echo $single_supplier['id']; ?>"><?php echo $single_supplier['name']; ?></option>
                                          <?php }
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <div class="col-lg-6 fv-row">
                                <label class="form-label">Stock Quantity</label>
                                <input type="text" name="stock_qty" id="stock_qty" class="form-control form-control-lg form-control-solid numeric-only" placeholder="Available Stock" />
                            </div>
                            <div class="col-lg-6 fv-row">
                                <label class="form-label">Swatch Image</label>
                                <input type="file" name="swatch_image" id="swatch_image" class="form-control form-control-lg form-control-solid" accept="image/*" />
                            </div>
                        </div>
                        <div class="row mb-6">
                            <div class="col-lg-6 fv-row">
                                <label class="form-label">Status</label>
                                <select name="status" id="status" aria-label="Status" data-control="select2" data-placeholder="Status" class="form-select form-select-solid form-select-lg fw-semibold">
                                    <option value="1" selected>Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                            <div class="col-lg-6 fv-row">
                                <label class="form-label">Remarks</label>
                                <input type="text" name="remarks" id="remarks" class="form-control form-control-lg form-control-solid" placeholder="Remarks" />
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-end py-6 px-9">
                        <button type="reset" class="btn btn-light btn-active-light-primary me-2" onclick="window.location.href = '<?php echo $site_path; ?>/fabric'">Discard</button>
                        <button type="submit" class="btn btn-primary" id="kt_create_fabric_details_submit">Save Changes</button>
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
<script src="<?php echo $site_path; ?>/assets/js/custom/create-fabric.js?v=<?php echo time(); ?>"></script>
<script src="<?php echo $site_path; ?>/assets/js/custom/create-fabric.js?v=<?php echo time(); ?>"></script>
<script>
    $('.numeric-only').on('keypress', function (e) {
        if (e.which < 48 || e.which > 57) {
            e.preventDefault();
        }
    });
</script>
</body>
</html>