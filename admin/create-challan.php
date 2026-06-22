<?php
include "config/database.php";
include "config/auth_check.php";
include "includes/sidemenu.php";

$product_list = getAllProductList();
$category_list = getCategoryList();
$challan_no = generateChallanNo();
?>

<style>
.size-qty-grid {
    border: 3px solid #000;
    display: inline-flex;
    align-items: stretch;
    background: #fff;
    max-width: 100%;
    overflow-x: auto;
}

.size-qty-box {
    min-width: 72px;
    border-right: 3px solid #000;
    text-align: center;
}

.size-head {
    padding: 8px 10px;
    font-weight: 700;
    font-size: 14px;
    border-bottom: 3px solid #000;
    background: #fff;
}

.size-input {
    width: 100%;
    height: 38px;
    border: 0;
    text-align: center;
    font-weight: 700;
    font-size: 15px;
    outline: none;
}

.total-box {
    min-width: 90px;
    text-align: center;
    border-left: 3px solid #000;
}

.total-box .size-head {
    font-style: italic;
    text-decoration: underline;
}

.total-value {
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-style: italic;
}

.inward-preview-card {
    border: 1px solid #eef0f7;
    border-radius: 14px;
    overflow: hidden;
}

.preview-empty {
    border: 1px dashed #d8dce6;
    border-radius: 14px;
    padding: 35px;
    text-align: center;
    color: #7e8299;
    background: #fbfbfc;
}
</style>

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">

        <div id="kt_app_toolbar" class="app-toolbar pt-7 pt-lg-10">
            <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
                <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                    <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                        <h1 class="page-heading text-gray-900 fw-bold fs-3 m-0">
                            Create Inward Challan Finished Stock
                        </h1>

                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                            <li class="breadcrumb-item text-muted">
                                <a href="<?php echo $site_path; ?>/dashboard" class="text-muted text-hover-primary">Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted">Inventory</li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted">Create Inward Challan Finished Stock</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div id="kt_app_content" class="app-content">
            <div id="kt_app_content_container" class="app-container container-fluid">

                <form id="kt_create_inward_batch_form"
                      action="<?php echo $site_path; ?>/ajax/create-multi-stock-inward-batch"
                      method="POST"
                      enctype="multipart/form-data">

                    <input type="hidden" name="redirect_page" value="<?php echo $site_path; ?>/challan-list">

                    <div class="card card-flush shadow-sm mb-7">
                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="fw-bold text-gray-900">Batch Details</h3>
                            </div>
                        </div>

                        <div class="card-body border-top p-9">

                            <div class="row mb-6">
                                <div class="col-lg-4 fv-row">
                                    <label class="form-label fw-semibold fs-6">Product</label>
                                    <select id="product_id"
                                            data-control="select2"
                                            data-placeholder="Select Product"
                                            class="form-select form-select-solid form-select-lg fw-semibold">
                                        <option value="">Select Product</option>

                                        <?php if ($product_list) {
                                            foreach ($product_list as $single_product) { ?>
                                                <option value="<?php echo $single_product['id']; ?>"
                                                        data-name="<?php echo htmlspecialchars($single_product['name']); ?>"
                                                        data-sku="<?php echo htmlspecialchars($single_product['sku']); ?>"
                                                        data-category="<?php echo htmlspecialchars($single_product['category']); ?>">
                                                    <?php echo $single_product['sku'] . " - " . $single_product['name']; ?>
                                                </option>
                                        <?php } } ?>
                                    </select>
                                </div>

                                <div class="col-lg-4 fv-row">
                                    <label class="form-label">Outfit Type</label>
                                    <select name="outfit_type"
                                            id="outfit_type"
                                            data-control="select2"
                                            data-placeholder="Outfit Type"
                                            class="form-select form-select-solid form-select-lg fw-semibold">
                                        <option value="">Select Outfit Type</option>
                                        <?php if ($category_list) {
                                            foreach ($category_list as $single_cat) { ?>
                                                <option value="<?php echo $single_cat['category']; ?>">
                                                    <?php echo $single_cat['category']; ?>
                                                </option>
                                        <?php } } ?>
                                    </select>
                                </div>
  
                                <div class="col-lg-4 fv-row">
                                    <label class="form-label fw-semibold fs-6">Challan No</label>
                                    <input type="text" name="challan_no" id="challan_no" class="form-control form-control-lg form-control-solid" placeholder="Auto / Manual Challan No" value="<?php echo $challan_no; ?>">
                                    <div id="challan_error" class="text-danger fw-semibold mt-2"></div>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <div class="col-lg-10 fv-row">
                                    <label class="required form-label d-block fw-semibold fs-6">Size Wise Quantity</label>

                                    <div class="size-qty-grid">
                                        <?php foreach (['S','M','L','XL','XXL','3XL','FREE Size'] as $size) { ?>
                                            <div class="size-qty-box">
                                                <div class="size-head"><?php echo $size; ?></div>
                                                <input type="text"
                                                       class="size-input size-qty-input"
                                                       data-size="<?php echo $size; ?>"
                                                       placeholder="0">
                                            </div>
                                        <?php } ?>

                                        <div class="total-box">
                                            <div class="size-head">Total</div>
                                            <div class="total-value" id="size_total_qty">0</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-2 fv-row">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="button" id="add_item_btn" class="btn btn-primary w-100">
                                        Add
                                    </button>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <div class="col-lg-4 fv-row">
                                    <label class="form-label">Inward Date</label>
                                    <input type="text"
                                           name="inward_date"
                                           id="inward_date"
                                           class="form-control form-control-lg form-control-solid"
                                           value="<?php echo date('Y-m-d'); ?>">
                                </div>

                                <div class="col-lg-8 fv-row">
                                    <label class="form-label">Remarks</label>
                                    <textarea name="remarks"
                                              id="remarks"
                                              class="form-control form-control-lg form-control-solid"
                                              placeholder="Remarks"></textarea>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="card card-flush shadow-sm">
                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="fw-bold text-gray-900">Selected Inward Items</h3>
                            </div>

                            <div class="card-toolbar">
                                <span class="badge badge-light-primary" id="total_items_badge">
                                    Total Qty: 0
                                </span>
                            </div>
                        </div>

                        <div class="card-body pt-0">

                            <div id="empty_preview" class="preview-empty">
                                Select product and enter size-wise quantity, then click Add.
                            </div>

                            <div class="table-responsive inward-preview-card d-none" id="preview_table_box">
                                <table class="table align-middle table-row-dashed fs-6 gy-4 mb-0">
                                    <thead>
                                        <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase">
                                            <th>#</th>
                                            <th>SKU</th>
                                            <th>Product</th>
                                            <th>Size</th>
                                            <th>Qty</th>
                                            <th>Units Preview</th>
                                            <th class="text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="selected_items_body" class="fw-semibold text-gray-700"></tbody>
                                </table>
                            </div>

                        </div>

                        <div class="card-footer d-flex justify-content-end py-6 px-9">
                            <a href="<?php echo $site_path; ?>/stock-inwards"
                               class="btn btn-light btn-active-light-primary me-2">
                                Discard
                            </a>

                            <button type="submit" class="btn btn-primary" id="submit_btn">
                                Generate Inward Batch
                            </button>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <?php include("includes/footer.php"); ?>
</div>

<script>
var hostUrl = "<?php echo $site_path; ?>/";
</script>

<script src="<?php echo $site_path; ?>/assets/plugins/global/plugins.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/scripts.bundle.js"></script>

<script>
$(document).ready(function () {
    let challanValid = true;
    let selectedItems = [];
    
    $('#challan_no').on('blur', function(){
        let challan_no = $(this).val().trim();
        if(challan_no == ''){
            return;
        }
        $.ajax({
            url: '<?php echo $site_path; ?>/ajax/check-challan-number',
            type: 'POST',
            dataType: 'json',
            data: {
                challan_no: challan_no
            },
            success: function(res){
                if(res.status == false){
                    challanValid = false;
                    $('#challan_error').html(res.message);
                    $('#submit_btn').prop('disabled', true);
                }
                else{
                    challanValid = true;
                    $('#challan_error').html('');
                    $('#submit_btn').prop('disabled', false);
                }
            }
        });

    });

    $("#inward_date").flatpickr({
        altInput: true,
        altFormat: "Y-m-d",
        dateFormat: "Y-m-d"
    });

    $(document).on('input', '.size-qty-input', function () {
        this.value = this.value.replace(/[^0-9]/g, '');

        let total = 0;

        $('.size-qty-input').each(function () {
            total += parseInt($(this).val()) || 0;
        });

        $('#size_total_qty').text(total);
    });

    $('#product_id').on('change', function () {
        let category = $('#product_id option:selected').data('category');
        $('#outfit_type').val(category).trigger('change');
    });

    $('#add_item_btn').on('click', function () {

        let productId = $('#product_id').val();
        let productOption = $('#product_id option:selected');
        let productName = productOption.data('name');
        let sku = productOption.data('sku');

        if (!productId) {
            alert('Please select product');
            return;
        }

        let added = false;

        $('.size-qty-input').each(function () {

            let size = $(this).data('size');
            let qty = parseInt($(this).val()) || 0;

            if (qty > 0) {

                added = true;

                let existsIndex = selectedItems.findIndex(function (item) {
                    return item.product_id == productId && item.size == size;
                });

                if (existsIndex >= 0) {
                    selectedItems[existsIndex].qty += qty;
                } else {
                    selectedItems.push({
                        product_id: productId,
                        sku: sku,
                        product_name: productName,
                        size: size,
                        qty: qty
                    });
                }
            }
        });

        if (!added) {
            alert('Please enter quantity for at least one size');
            return;
        }

        $('.size-qty-input').val('');
        $('#size_total_qty').text('0');

        renderSelectedItems();
    });

    function renderSelectedItems() {

        let html = '';
        let totalQty = 0;

        if (selectedItems.length === 0) {
            $('#empty_preview').removeClass('d-none');
            $('#preview_table_box').addClass('d-none');
            $('#total_items_badge').text('Total Qty: 0');
            return;
        }

        $('#empty_preview').addClass('d-none');
        $('#preview_table_box').removeClass('d-none');

        selectedItems.forEach(function (item, index) {

            totalQty += parseInt(item.qty);

            html += `
                <tr>
                    <td>${index + 1}</td>

                    <td>
                        <span class="fw-bold">${item.sku}</span>
                        <input type="hidden" name="items[${index}][product_id]" value="${item.product_id}">
                        <input type="hidden" name="items[${index}][sku]" value="${item.sku}">
                        <input type="hidden" name="items[${index}][product_name]" value="${item.product_name}">
                        <input type="hidden" name="items[${index}][size]" value="${item.size}">
                        <input type="hidden" name="items[${index}][qty]" value="${item.qty}">
                    </td>

                    <td>${item.product_name}</td>

                    <td>
                        <span class="badge badge-light-dark">${item.size}</span>
                    </td>

                    <td>
                        <span class="badge badge-light-primary">${item.qty}</span>
                    </td>

                    <td>
                        <span class="text-muted">Auto generate ${item.qty} unique codes</span>
                    </td>

                    <td class="text-end">
                        <button type="button"
                                class="btn btn-icon btn-light-danger btn-sm remove-item"
                                data-index="${index}">
                            <i class="ki-duotone ki-trash fs-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                                <span class="path5"></span>
                            </i>
                        </button>
                    </td>
                </tr>
            `;
        });

        $('#selected_items_body').html(html);
        $('#total_items_badge').text('Total Qty: ' + totalQty);
    }

    $(document).on('click', '.remove-item', function () {
        let index = $(this).data('index');
        selectedItems.splice(index, 1);
        renderSelectedItems();
    });

    $('#kt_create_inward_batch_form').on('submit', function (e) {

        if (selectedItems.length === 0) {
            e.preventDefault();
            alert('Please add at least one product size qty.');
            return false;
        }

    });

});
</script>