<?php
include("config/database.php");
include("config/auth_check.php");
include("includes/sidemenu.php");

/*
|--------------------------------------------------------------------------
| Product list from inward stock only
|--------------------------------------------------------------------------
*/
$product_list = [];

$sql = "
    SELECT 
        p.id,
        p.sku,
        p.name,
        SUM(
            CASE 
                WHEN sl.movement_type IN ('IN','UNRESERVE') THEN sl.qty
                WHEN sl.movement_type IN ('OUT','RESERVE') THEN -sl.qty
                ELSE 0
            END
        ) AS available_stock
    FROM stock_ledger sl
    INNER JOIN product p ON p.id = sl.product_id
    GROUP BY p.id
    HAVING available_stock > 0
    ORDER BY p.name ASC
";

$result = mysqli_query($con, $sql);

while ($row = mysqli_fetch_assoc($result)) {
    $product_list[] = $row;
}
?>

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">

        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">

                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">

                    <h1 class="page-heading text-gray-900 fw-bold fs-3 my-0">
                        Create Alteration Request
                    </h1>

                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">

                        <li class="breadcrumb-item text-muted">
                            <a href="<?php echo $site_path; ?>/pages/dashboard" class="text-muted text-hover-primary">
                                Home
                            </a>
                        </li>

                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>

                        <li class="breadcrumb-item text-muted">
                            Inventory
                        </li>

                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>

                        <li class="breadcrumb-item text-muted">
                            Create Alteration Request
                        </li>

                    </ul>

                </div>

            </div>
        </div>

        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">

                <form id="kt_alteration_request_form"
                      action="<?php echo $site_path; ?>/ajax/create-alteration-request"
                      method="POST"
                      class="form">

                    <div class="row g-5">

                        <div class="col-xl-8">

                            <div class="card card-flush shadow-sm">

                                <div class="card-header">
                                    <div class="card-title">
                                        <h3 class="fw-bold text-gray-900">
                                            Alteration Request Details
                                        </h3>
                                    </div>
                                </div>

                                <div class="card-body border-top p-9">

                                    <div class="row mb-6">

                                        <div class="col-lg-12 fv-row">
                                            <label class="required form-label fw-semibold fs-6">
                                                Select Existing Product
                                            </label>

                                            <select name="product_id"
                                                    id="product_id"
                                                    data-control="select2"
                                                    data-placeholder="Select Product"
                                                    class="form-select form-select-solid form-select-lg fw-semibold"
                                                    required>
                                                <option value="">Select Product</option>

                                                <?php foreach ($product_list as $product) { ?>
                                                    <option value="<?php echo $product['id']; ?>">
                                                        <?php echo $product['sku'] . " - " . $product['name'] . " | Stock: " . $product['available_stock']; ?>
                                                    </option>
                                                <?php } ?>

                                            </select>
                                        </div>

                                    </div>

                                    <div class="row mb-6">

                                        <div class="col-lg-6 fv-row">
                                            <label class="required form-label fw-semibold fs-6">
                                                Current Size / From Size
                                            </label>

                                            <select name="from_size"
                                                    id="from_size"
                                                    data-control="select2"
                                                    data-placeholder="Select Current Size"
                                                    class="form-select form-select-solid form-select-lg fw-semibold"
                                                    required>
                                                <option value="">Select Current Size</option>
                                            </select>
                                        </div>

                                        <div class="col-lg-6 fv-row">
                                            <label class="required form-label fw-semibold fs-6">
                                                Target Size / To Size
                                            </label>

                                            <select name="to_size"
                                                    id="to_size"
                                                    data-control="select2"
                                                    data-placeholder="Select Target Size"
                                                    class="form-select form-select-solid form-select-lg fw-semibold"
                                                    required>
                                                <option value="">Select Target Size</option>
                                                <option value="S">S</option>
                                                <option value="M">M</option>
                                                <option value="L">L</option>
                                                <option value="XL">XL</option>
                                                <option value="XXL">XXL</option>
                                                <option value="3XL">3XL</option>
                                                <option value="FREE">FREE</option>
                                            </select>
                                        </div>

                                    </div>

                                    <div class="row mb-6">

                                        <div class="col-lg-6 fv-row">
                                            <label class="required form-label fw-semibold fs-6">
                                                Enter Quantity
                                            </label>

                                            <input type="number"
                                                   name="qty"
                                                   id="qty"
                                                   min="1"
                                                   class="form-control form-control-lg form-control-solid"
                                                   placeholder="Enter Quantity"
                                                   required>
                                        </div>

                                        <div class="col-lg-6 fv-row">
                                            <label class="form-label fw-semibold fs-6">
                                                Available Stock
                                            </label>

                                            <input type="text"
                                                   id="available_stock"
                                                   class="form-control form-control-lg form-control-solid bg-light-success fw-bold"
                                                   readonly>
                                        </div>

                                    </div>

                                    <div class="row mb-6">

                                        <div class="col-lg-6 fv-row">
                                            <label class="required form-label fw-semibold fs-6">
                                                Assign To Person / Vendor
                                            </label>
                                            <select name="assigned_to" id="assigned_to" class="form-select form-select-solid form-select-lg" data-control="select2">
                                                <option value="">Select Vendor</option>

                                                <?php
                                                $vendors = mysqli_query($con,"
                                                    SELECT id,vendor_name
                                                    FROM vendors
                                                    WHERE status='1'
                                                    ORDER BY vendor_name ASC
                                                ");

                                                while($vendor = mysqli_fetch_assoc($vendors)){
                                                ?>
                                                    <option value="<?php echo $vendor['vendor_name']; ?>">
                                                        <?php echo $vendor['vendor_name']; ?>
                                                    </option>
                                                <?php } ?>

                                            </select>
                                        </div>

                                        <div class="col-lg-6 fv-row">
                                            <label class="form-label fw-semibold fs-6">
                                                Mobile Number
                                            </label>

                                            <input type="text" name="assigned_mobile" id="assigned_mobile" maxlength="10" class="form-control form-control-lg form-control-solid" placeholder="Mobile Number">
                                        </div>
                                    </div>

                                    <div class="row mb-6">

                                        <div class="col-lg-6 fv-row">
                                            <label class="form-label fw-semibold fs-6">
                                                Expected Return Date
                                            </label>

                                            <input type="date"
                                                   name="expected_return_date"
                                                   class="form-control form-control-lg form-control-solid">
                                        </div>

                                        <div class="col-lg-6 fv-row">
                                            <label class="form-label fw-semibold fs-6">
                                                Priority
                                            </label>

                                            <select name="priority"
                                                    class="form-select form-select-solid form-select-lg fw-semibold">
                                                <option value="Low">Low</option>
                                                <option value="Medium" selected>Medium</option>
                                                <option value="High">High</option>
                                            </select>
                                        </div>

                                    </div>

                                    <div class="row mb-6">

                                        <div class="col-lg-12 fv-row">
                                            <label class="form-label fw-semibold fs-6">
                                                Remarks
                                            </label>

                                            <textarea name="remarks"
                                                      rows="4"
                                                      class="form-control form-control-lg form-control-solid"
                                                      placeholder="Enter Remarks"></textarea>
                                        </div>

                                    </div>

                                </div>

                                <div class="card-footer d-flex justify-content-end py-6 px-9">

                                    <a href="<?php echo $site_path; ?>/alteration-request-list"
                                       class="btn btn-light btn-active-light-primary me-2">
                                        Discard
                                    </a>

                                    <button type="submit"
                                            class="btn btn-primary"
                                            id="kt_alteration_request_submit">
                                        Generate Alteration Request
                                    </button>

                                </div>

                            </div>

                        </div>

                        <div class="col-xl-4">

                            <div class="card card-flush shadow-sm">

                                <div class="card-header">
                                    <div class="card-title">
                                        <h3 class="fw-bold text-gray-900">
                                            Inventory Impact
                                        </h3>
                                    </div>
                                </div>

                                <div class="card-body border-top p-9">

                                    <div class="alert alert-primary">
                                        <div class="fw-bold mb-2">
                                            At Request Creation
                                        </div>
                                        Source size stock will be reserved.
                                    </div>

                                    <div class="mb-5">
                                        <div class="text-muted fw-semibold">
                                            Example:
                                        </div>

                                        <div class="mt-3">
                                            Size M = 10
                                            <br>
                                            Request Qty = 3
                                            <br>
                                            Available = 7
                                            <br>
                                            Reserved = 3
                                        </div>
                                    </div>

                                    <div class="separator my-5"></div>

                                    <div class="d-flex flex-column gap-4">

                                        <div class="d-flex align-items-center">
                                            <span class="badge badge-light-primary me-3">1</span>
                                            Generate Alteration ID
                                        </div>

                                        <div class="d-flex align-items-center">
                                            <span class="badge badge-light-warning me-3">2</span>
                                            Reserve Source Stock
                                        </div>

                                        <div class="d-flex align-items-center">
                                            <span class="badge badge-light-info me-3">3</span>
                                            Send for Alteration
                                        </div>

                                        <div class="d-flex align-items-center">
                                            <span class="badge badge-light-success me-3">4</span>
                                            Receive + QC
                                        </div>

                                        <div class="d-flex align-items-center">
                                            <span class="badge badge-light-success me-3">5</span>
                                            Add Stock To New Size
                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </form>

            </div>
        </div>

        <?php include("includes/footer.php"); ?>

    </div>
</div>

<script src="<?php echo $site_path; ?>/assets/plugins/global/plugins.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/scripts.bundle.js"></script>

<script>
$(document).ready(function () {
    $('#assigned_to').select2({
        placeholder: 'Search Vendor',
        allowClear: true,
        language: {
            noResults: function () {
                return `
                    <button type="button"
                            class="btn btn-sm btn-primary w-100"
                            id="add_new_vendor_btn">
                        Add New Vendor
                    </button>
                `;
            }
        },
        escapeMarkup: function (markup) {
            return markup;
        }
    });
    
    $(document).on('click', '#add_new_vendor_btn', function () {
        let vendorName = $('.select2-search__field').val();
        let mobile = $('#assigned_mobile').val();
        if ($.trim(vendorName) === '') {
            alert('Please enter vendor name');
            return;
        }

        $.ajax({
            url: '<?php echo $site_path; ?>/ajax/add-vendor',
            type: 'POST',
            dataType: 'json',
            data: {
                vendor_name: vendorName,
                mobile: mobile
            },
            success: function (res) {
                if (res.status === true) {
                    let newOption = new Option(res.vendor_name, res.vendor_id, true, true);
                    $('#assigned_to').append(newOption).trigger('change');
                    $('#assigned_to').select2('close');
                } else {
                    alert(res.message);
                }
            }
        });
    });
    
    $('#product_id').on('change', function () {

        let product_id = $(this).val();

        $('#from_size').html('<option value="">Select Current Size</option>').trigger('change');
        $('#available_stock').val('');

        if (product_id === '') {
            return;
        }

        $.ajax({
            url: "<?php echo $site_path; ?>/ajax/get-alteration-product-sizes.php",
            type: "POST",
            dataType: "json",
            data: {
                product_id: product_id
            },
            success: function (res) {

                if (res.status === true) {
                    $('#from_size').html(res.size_options).trigger('change');
                } else {
                    alert(res.message);
                }

            }
        });

    });

    $('#from_size').on('change', function () {

        let product_id = $('#product_id').val();
        let size = $(this).val();
        $('#available_stock').val('');

        if (product_id === '' || size === '') {
            return;
        }

        $.ajax({
            url: "<?php echo $site_path; ?>/ajax/get-alteration-product-stock.php",
            type: "POST",
            dataType: "json",
            data: {
                product_id: product_id,
                size: size
            },
            success: function (res) {

                if (res.status === true) {
                    $('#available_stock').val(res.stock);
                }

            }
        });
        
        $('#to_size option').prop('disabled', false);

        $('#to_size option[value="' + size + '"]')
            .prop('disabled', true);

        $('#to_size').val('').trigger('change');

    });

    $('#kt_alteration_request_form').on('submit', function (e) {

        let fromSize = $('#from_size').val();
        let toSize = $('#to_size').val();
        let qty = parseInt($('#qty').val());
        let availableStock = parseInt($('#available_stock').val());

        if (fromSize === toSize) {
            e.preventDefault();
            alert('From Size and To Size cannot be same.');
            return false;
        }

        if (qty > availableStock) {
            e.preventDefault();
            alert('Quantity cannot be greater than available stock.');
            return false;
        }

    });

});
</script>