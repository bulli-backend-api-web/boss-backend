<?php
include "config/database.php";
include "config/auth_check.php";
include "includes/sidemenu.php";

$state_list = getAllStateList();
?>
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_toolbar" class="app-toolbar pt-7 pt-lg-10">
            <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
                <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                    <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                        <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">Store Registration</h1>
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <a href="<?php echo $site_path; ?>/dashboard" class="text-muted text-hover-primary">Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted">Store List</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div id="kt_app_content" class="app-content">
            <div id="kt_app_content_container" class="app-container container-fluid">
                <form id="kt_create_store_form" action="<?php echo $site_path ?>/ajax/add-update-store-details" class="form" method="POST" enctype="multipart/form-data">
                    <!--begin::Card body-->
                    <input name="redirect_page" type="hidden" id="redirect_page" value="<?php echo $site_path ?>/store" />
                    <div class="card-body border-top p-9">
                        <div class="row mb-6">
                            <div class="col-lg-6 fv-row">
                                <label class="form-label required fw-semibold fs-6">Store Name</label>
                                <input type="text" name="store_name" id="store_name" class="form-control form-control-lg form-control-solid" placeholder="Store Name" />
                            </div>
                            <div class="col-lg-6 fv-row">
                                <label class="form-label required fw-semibold fs-6">Store Code</label>
                                <input type="text" name="store_code" id="store_code" class="form-control form-control-lg form-control-solid" placeholder="Store Code" />
                            </div>
                        </div>
                        <div class="row mb-6">
                            <div class="col-lg-6 fv-row">
                                <label class="form-label required fw-semibold fs-6">Ownership Model</label>
                                <select name="ownership_model" id="ownership_model" aria-label="Assign To" data-control="select2" data-placeholder="Ownership Model" class="form-select form-select-solid form-select-lg fw-semibold">
                                    <option value="Company Owned">Company Owned</option>
                                    <option value="Franchise">Franchise</option>
                                    <option value="Royalty Based">Royalty Based</option>
                                    <option value="Revenue Sharing">Revenue Sharing</option>
                                    <option value="Semi Owned(JV)">Semi Owned(JV)</option>
                                    <option value="Pop-up">Pop-up / Seasonal</option>

                                </select>
                            </div>
                            <div class="col-lg-6 fv-row">
                                <label class="form-label required fw-semibold fs-6">Store Type</label>
                                <select name="store_type" id="store_type" aria-label="Assign To" data-control="select2" data-placeholder="Store Type" class="form-select form-select-solid form-select-lg fw-semibold">
                                    <option value="Company Owned">Standalone Store</option>
                                    <option value="Franchise">Mall Store</option>
                                    <option value="Royalty Based">High Street</option>
                                    <option value="Revenue Sharing">Shop-in-Shop</option>
                                    <option value="Semi Owned(JV)">Exhibition Stall</option>
                                    <option value="Pop-up">Online Pickup Point</option>

                                </select>
                            </div>
                        </div>
                        <div class="row mb-6">
                             <div class="col-lg-6 fv-row">
                                <label class="form-label required fw-semibold fs-6">City</label>
                                <input type="text" name="city" id="city" class="form-control form-control-lg form-control-solid" placeholder="City"/>
                            </div>
                            <div class="col-lg-6 fv-row">
                                <label class="form-label">State</label>
                                <select name="state_id" name="state_id" class="form-select form-select-solid" data-control="select2">
                                    <option value="">Select State</option>
                                        <?php
                                        if ($state_list) {
                                            foreach ($state_list as $single_state) {?>
                                            <option value="<?php echo $single_state['id']; ?>">
                                                <?php echo $single_state['name']; ?>
                                                -
                                                <?php echo $single_state['state_code']; ?>
                                            </option>
                                                <?php
                                            }
                                        }
                                        ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-6">
                             <div class="col-lg-12 fv-row">
                                <label class="form-label">Full Address</label>
                                <textarea class="form-control form-control-lg form-control-solid deduction" name="address"></textarea>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <div class="col-lg-6 fv-row">
                                <label class="form-label fw-semibold fs-6">Operations Head</label>
                                <input type="text" name="operation_head" id="operation_head" class="form-control form-control-lg form-control-solid" placeholder="Operation Head" />
                            </div>
                            <div class="col-lg-6 fv-row">
                                <label class="form-label fw-semibold fs-6">Ops Head Contact</label>
                                <input type="text" name="operation_head_number" id="operation_head_number" class="form-control form-control-lg form-control-solid" placeholder="Ops Head Contact" />
                            </div>
                        </div>
                        <div class="row mb-6">
                            <div class="col-lg-6 fv-row">
                                <label class="form-label fw-semibold fs-6">Store Manager</label>
                                <input type="text" name="store_manager_name" id="store_manager_name" class="form-control form-control-lg form-control-solid" placeholder="Store Manager" />
                            </div>
                            <div class="col-lg-6 fv-row">
                                <label class="form-label fw-semibold fs-6">Manager Contact</label>
                                <input type="text" name="store_manager_contact" id="store_manager_contact" class="form-control form-control-lg form-control-solid" placeholder="Manager Contact" />
                            </div>
                        </div>
                        <div id="sales_person_area">
                            <div class="sales-person-row row mb-6">
                                <div class="col-lg-5 fv-row">
                                    <label class="form-label fw-semibold fs-6">Sale Person</label>
                                    <input type="text" name="sale_person[]" class="form-control form-control-lg form-control-solid" placeholder="Name" />
                                </div>
                                <div class="col-lg-5 fv-row">
                                    <label class="form-label fw-semibold fs-6">Contact</label>
                                    <input type="text" name="sale_person_contact[]" class="form-control form-control-lg form-control-solid" placeholder="Contact" />
                                </div>
                            </div>
                        </div>
                        <div class="mb-8">
                            <button type="button" id="add_sales_person" class="btn btn-primary">
                                Add Sale Person
                            </button>
                        </div>
                        <div class="row mb-6">
                            <div class="col-lg-6 fv-row">
                                <label class="form-label fw-semibold fs-6">Price Tier / Applicable Rate</label>
                                <select name="applicable_rate" id="applicable_rate" aria-label="Assign To" data-control="select2" data-placeholder="Ownership Model" class="form-select form-select-solid form-select-lg fw-semibold">
                                    <option value="Company Owned">Store Rate (MRP-based)</option>
                                    <option value="Franchise">Franchise Rate</option>
                                    <option value="Royalty Based">Royalty Rate</option>
                                    <option value="Revenue Sharing">Owned Store Cost</option>
                                    <option value="Semi Owned(JV)">Custom</option>
                                </select>
                            </div>
                            <div class="col-lg-6 fv-row">
                                <label class="form-label fw-semibold fs-6">Billing Cycle</label>
                                <select name="billing_cycle" id="billing_cycle" aria-label="Assign To" data-control="select2" data-placeholder="Ownership Model" class="form-select form-select-solid form-select-lg fw-semibold">
                                    <option value="Company Owned">Per Invoice</option>
                                    <option value="Franchise">Weekly</option>
                                    <option value="Royalty Based">Monthly</option>
                                    <option value="Revenue Sharing">Quarterly</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <div class="col-lg-6 fv-row">
                                <label class="form-label required fw-semibold fs-6">Opening Date</label>
                                <input type="text" name="opening_date" id="opening_date" class="form-control form-control-lg form-control-solid" placeholder="Opening Date" />
                            </div>
                            <div class="col-lg-6 fv-row">
                                <label class="form-label required fw-semibold fs-6">Agreement Expire</label>
                                <input type="text" name="agreement_expire" id="agreement_expire" class="form-control form-control-lg form-control-solid" placeholder="Agreement Expire" />
                            </div>
                        </div>
                        <div class="row mb-6">
                            <div class="col-lg-6 fv-row">
                                <label class="form-label required fw-semibold fs-6">Remarks</label>
                                <input type="text" name="remarks" id="remarks" class="form-control form-control-lg form-control-solid" placeholder="Remarks" />
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-end py-6 px-9">
                        <button type="reset" class="btn btn-light btn-active-light-primary me-2" onclick="window.location.href = '<?php echo $site_path; ?>/design-list'">Discard</button>
                        <button type="submit" class="btn btn-primary" id="kt_create_store_details_submit">Save Changes</button>
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
<script src="<?php echo $site_path; ?>/assets/js/custom/create-store.js?v=<?php echo time(); ?>"></script>
<script>
    $(document).ready(function () {
        $("#opening_date").flatpickr({
            altInput: true,
            altFormat: "Y-m-d",
            dateFormat: "Y-m-d"
        });
        
        $("#agreement_expire").flatpickr({
            altInput: true,
            altFormat: "Y-m-d",
            dateFormat: "Y-m-d"
        });
        
        $('#add_sales_person').click(function () {

        let html = `
        
        <div class="sales-person-row row mb-6">

            <div class="col-lg-5 fv-row">

                <input type="text"
                       name="sale_person[]"
                       class="form-control form-control-lg form-control-solid"
                       placeholder="Name" />

            </div>

            <div class="col-lg-5 fv-row">

                <input type="text"
                       name="sale_person_contact[]"
                       class="form-control form-control-lg form-control-solid"
                       placeholder="Contact" />

            </div>

            <div class="col-lg-2 d-flex align-items-end">

                <button type="button"
                        class="btn btn-danger remove-sales-person">

                    Remove

                </button>

            </div>

        </div>`;

        $('#sales_person_area').append(html);

    });

    // REMOVE
    $(document).on('click', '.remove-sales-person', function () {

        if($('.sales-person-row').length > 1){

            $(this).closest('.sales-person-row').remove();

        }

    });
    });
</script>
</body>
</html>