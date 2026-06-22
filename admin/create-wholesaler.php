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
                        <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">Wholesaler Registration</h1>
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <a href="<?php echo $site_path; ?>/dashboard" class="text-muted text-hover-primary">Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted">Wholesaler List</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div id="kt_app_content" class="app-content">
            <div id="kt_app_content_container" class="app-container container-fluid">
                <form id="kt_create_wholeseller_form" action="<?php echo $site_path ?>/ajax/add-update-wholesaler-details" class="form" method="POST" enctype="multipart/form-data">
                    <!--begin::Card body-->
                    <input name="redirect_page" type="hidden" id="redirect_page" value="<?php echo $site_path ?>/wholesalers" />
                    <div class="card-body border-top p-9">
                        <div class="row mb-6">
                            <div class="col-lg-6 fv-row">
                                <label class="form-label required fw-semibold fs-6">Business Name</label>
                                <input type="text" name="business_name" id="business_name" class="form-control form-control-lg form-control-solid" placeholder="Business Name" />
                            </div>
                            <div class="col-lg-6 fv-row">
                                <label class="form-label required fw-semibold fs-6">GST Number</label>
                                <input type="text" name="gst_number" id="gst_number" class="form-control form-control-lg form-control-solid" placeholder="GST Number" />
                            </div>
                        </div>
                        <div class="row mb-6">
                            <div class="col-lg-6 fv-row">
                                <label class="form-label required fw-semibold fs-6">Business Type</label>
                                <select name="business_type" id="business_type" aria-label="Assign To" data-control="select2" data-placeholder="Business Type" class="form-select form-select-solid form-select-lg fw-semibold">
                                    <option value="Wholesaler">Wholesaler</option>
                                    <option value="Distributor">Distributor</option>
                                    <option value="Super Stockist">Super Stockist</option>
                                    <option value="C&F Agent">C&F Agent</option>
                                    <option value="Dealer">Dealer</option>
                                </select>
                            </div>
                            <div class="col-lg-6 fv-row">
                                <label class="form-label required fw-semibold fs-6">City</label>
                                <input type="text" name="city" id="city" class="form-control form-control-lg form-control-solid" placeholder="City"/>
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
                                <label class="form-label fw-semibold fs-6">Primary Contact Person</label>
                                <input type="text" name="primary_contact_person" id="primary_contact_person" class="form-control form-control-lg form-control-solid" placeholder="Primary Contact Person" />
                            </div>
                            <div class="col-lg-6 fv-row">
                                <label class="form-label fw-semibold fs-6">Mobile</label>
                                <input type="text" name="mobile" id="mobile" class="form-control form-control-lg form-control-solid" placeholder="Mobile" />
                            </div>
                        </div>
                        
                        <div class="row mb-6">
                            <div class="col-lg-6 fv-row">
                                <label class="form-label fw-semibold fs-6">Email</label>
                                <input type="text" name="email" id="email" class="form-control form-control-lg form-control-solid" placeholder="Email" />
                            </div>
                            <div class="col-lg-6 fv-row">
                                <label class="form-label fw-semibold fs-6">Whatsapp Number</label>
                                <input type="text" name="whatsapp_number" id="whatsapp_number" class="form-control form-control-lg form-control-solid" placeholder="Whatsapp Number" />
                            </div>
                        </div>
                        <div class="row mb-6">
                            <div class="col-lg-6 fv-row">
                                <label class="form-label fw-semibold fs-6">Assigned Rep. Name</label>
                                <input type="text" name="rep_name" id="rep_name" class="form-control form-control-lg form-control-solid" placeholder="Assigned Rep. Name" />
                            </div>
                            <div class="col-lg-6 fv-row">
                                <label class="form-label fw-semibold fs-6">Rep.Number</label>
                                <input type="text" name="rep_number" id="rep_number" class="form-control form-control-lg form-control-solid" placeholder="Rep Contact" />
                            </div>
                        </div>
                        
                        <div class="row mb-6">
                            <div class="col-lg-6 fv-row">
                                <label class="form-label fw-semibold fs-6">Agent Name</label>
                                <input type="text" name="agent_name" id="agent_name" class="form-control form-control-lg form-control-solid" placeholder="Agent Name" />
                            </div>
                            <div class="col-lg-6 fv-row">
                                <label class="form-label fw-semibold fs-6">Agent Commission</label>
                                <input type="text" name="agent_commission" id="agent_commission" class="form-control form-control-lg form-control-solid" placeholder="Agent Commission" />
                            </div>
                        </div>
                        
                        <div class="row mb-6">
                            <div class="col-lg-6 fv-row">
                                <label class="form-label fw-semibold fs-6">Price Tier / Applicable Rate</label>
                                <select name="price_tier" id="price_tier" aria-label="Assign To" data-control="select2" data-placeholder="Price Tier" class="form-select form-select-solid form-select-lg fw-semibold">
                                    <option value="Standard Wholesale">Standard Wholesale</option>
                                    <option value="Premium (Lower Disc.)">Premium (Lower Disc.)</option>
                                    <option value="Key Account">Key Account</option>
                                    <option value="Custom Rate">Custom Rate</option>
                                </select>
                            </div>
                            <div class="col-lg-6 fv-row">
                                <label class="form-label required fw-semibold fs-6">Base Discount</label>
                                <input type="text" name="base_discount" id="base_discount" class="form-control form-control-lg form-control-solid" placeholder="Base Discount" />
                            </div>
                        </div>
                        <div class="row mb-6">
                            <div class="col-lg-6 fv-row">
                                <label class="form-label required fw-semibold fs-6">Credit Days</label>
                                <input type="text" name="credit_days" id="credit_days" class="form-control form-control-lg form-control-solid" placeholder="Credit Days" />
                            </div>
                            <div class="col-lg-6 fv-row">
                                <label class="form-label required fw-semibold fs-6">Credit Limit</label>
                                <input type="text" name="credit_limit" id="credit_limit" class="form-control form-control-lg form-control-solid" placeholder="Credit Limit" />
                            </div>
                        </div>
                        <div class="row mb-6">
                            <div class="col-lg-6 fv-row">
                                <label class="form-label fw-semibold fs-6">Payment Terms</label>
                                <select name="payment_terms" id="payment_terms" aria-label="Assign To" data-control="select2" data-placeholder="Payment Terms" class="form-select form-select-solid form-select-lg fw-semibold">
                                    <option value="Advance 100%">Advance 100%</option>
                                    <option value="50% Advance + 50% Delivery">50% Advance + 50% Delivery</option>
                                    <option value="30 Days Credit">30 Days Credit</option>
                                    <option value="60 Days Credit">60 Days Credit</option>
                                    <option value="On Delivery">On Delivery</option>
                                </select>
                            </div>
                            <div class="col-lg-6 fv-row">
                                <label class="form-label required fw-semibold fs-6">Territory Region</label>
                                <input type="text" name="territory_region" id="territory_region" class="form-control form-control-lg form-control-solid" placeholder="Territory Region" />
                            </div>
                        </div>
                        <div class="row mb-6">
                            <div class="col-lg-6 fv-row">
                                <label class="form-label fw-semibold fs-6">Exclusivity</label>
                                <select name="exclusivity" id="exclusivity" aria-label="Assign To" data-control="select2" data-placeholder="Ownership Model" class="form-select form-select-solid form-select-lg fw-semibold">
                                    <option value="Non-exclusive">Non-exclusive</option>
                                    <option value="Exclusive Region">Exclusive Region</option>
                                    <option value="Exclusive SKU">Exclusive SKU</option>
                                    <option value="Exclusive Both">Exclusive Both</option>
                                </select>
                            </div>
                            <div class="col-lg-6 fv-row">
                                <label class="form-label required fw-semibold fs-6">Agreement Notes</label>
                                <input type="text" name="agreement_note" id="agreement_note" class="form-control form-control-lg form-control-solid" placeholder="Agreement Note" />
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-end py-6 px-9">
                        <button type="reset" class="btn btn-light btn-active-light-primary me-2" onclick="window.location.href = '<?php echo $site_path; ?>/design-list'">Discard</button>
                        <button type="submit" class="btn btn-primary" id="kt_create_whole_seller_details_submit">Save Changes</button>
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
<script src="<?php echo $site_path; ?>/assets/js/custom/create-wholesaller.js?v=<?php echo time(); ?>"></script>
<script>
    $(document).ready(function () {
       
    });
</script>
</body>
</html>