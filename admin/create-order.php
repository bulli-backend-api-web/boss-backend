<?php
include("config/database.php");
include("config/auth_check.php");
include("includes/sidemenu.php");

$channel_list = getChannelList();
$product_list = getAllProductList();
$state_list = getAllStateList();
$country_list = getAllCountryList();
$store_list = getAllStoreList();
$wholesaler_list = getAllWholesalerList();
?>

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_toolbar" class="app-toolbar pt-7 pt-lg-10">
            <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
                <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                    <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                        <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                            Create Order
                        </h1>
                    </div>
                </div>
            </div>
        </div>
        <!-- Content -->
        <div id="kt_app_content" class="app-content">

            <div id="kt_app_content_container" class="app-container container-fluid">

                <form id="kt_create_order_form" action="<?php echo $site_path ?>/ajax/add-update-order-details" class="form" method="POST">
                    <div class="card card-flush">
                        <div class="card-body border-top p-9">
                            <!-- CHANNEL -->
                            <div class="row mb-6">
                                <div class="col-lg-12 fv-row">
                                    <label class="form-label required fw-semibold fs-6">
                                        Channel
                                    </label>
                                    <div class="d-flex flex-wrap gap-3">
                                    <?php
                                    if ($channel_list) {
                                        $i = 1;
                                        foreach ($channel_list as $single_channel) {?>
                                                <input type="radio" class="btn-check channel-radio" name="channel" id="channel_<?php echo $i; ?>" value="<?php echo $single_channel['name']; ?>">
                                                <label class="btn btn-outline btn-outline-dashed btn-active-light-primary" for="channel_<?php echo $i; ?>"><?php echo $single_channel['name']; ?></label>

                                            <?php
                                            $i++;
                                        }
                                    }
                                    ?>

                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 fv-row mb-7 d-none" id="store_details">
                                <label class="form-label fw-semibold fs-6">Store</label>
                                <select name="store_id" id="store_id" class="form-select form-select-solid product-select" data-control="select2">
                                    <option value="">Select Store</option>
                                        <?php
                                        if ($store_list) {
                                            foreach ($store_list as $single_store) {?>
                                            <option value="<?php echo $single_store['id']; ?>"><?php echo $single_store['store_name']; ?></option>
                                                <?php
                                            }
                                        }
                                        ?>

                                </select>
                            </div>
                            <div class="col-lg-4 fv-row mb-7 d-none" id="whole_saler_details">
                                <label class="form-label fw-semibold fs-6">Wholesaler</label>
                                <select name="whole_saler_id" id="whole_saler_id" class="form-select form-select-solid product-select" data-control="select2">
                                    <option value="">Select Wholesaler</option>
                                        <?php
                                        if ($wholesaler_list) {
                                            foreach ($wholesaler_list as $single_whole_saler) {?>
                                            <option value="<?php echo $single_whole_saler['id']; ?>"><?php echo $single_whole_saler['business_name']; ?></option>
                                                <?php
                                            }
                                        }
                                        ?>

                                </select>
                            </div>
                            <!-- ORDER ITEMS -->
                            <div id="order_items">
                                <div class="order-item border rounded p-5 mb-6">

                                    <div class="row">
                                        <!-- PRODUCT -->
                                        <div class="col-lg-4 fv-row">
                                            <label class="form-label required fw-semibold fs-6">
                                                Product
                                            </label>
                                            <select name="product_id[]" class="form-select form-select-solid product-select" data-control="select2">
                                                <option value="">Select Product</option>
                                                    <?php
                                                    if ($product_list) {
                                                        foreach ($product_list as $single_product) {?>
                                                        <option value="<?php echo $single_product['id']; ?>" data-price="<?php echo $single_product['min_price']; ?>">
                                                            <?php echo $single_product['sku']; ?>
                                                            -
                                                            <?php echo $single_product['name']; ?>
                                                        </option>
                                                            <?php
                                                        }
                                                    }
                                                    ?>

                                            </select>
                                        </div>
                                        <!-- QTY -->
                                        <div class="col-lg-2 fv-row">
                                            <label class="form-label required fw-semibold fs-6">
                                                Qty
                                            </label>
                                            <input type="number" name="qty[]" class="form-control form-control-solid qty" value="1" min="1">
                                        </div>

                                        <!-- SIZE -->
                                        <div class="col-lg-3 fv-row">
                                            <label class="form-label required fw-semibold fs-6">
                                                Size
                                            </label>
                                            <select name="size[]" class="form-select form-select-solid">
                                                <option value="XS">XS</option>
                                                <option value="S">S</option>
                                                <option value="M">M</option>
                                                <option value="L">L</option>
                                                <option value="XL">XL</option>
                                                <option value="XXL">XXL</option>
                                                <option value="Unstitched">Unstitched</option>
                                            </select>
                                        </div>

                                        <!-- AMOUNT -->
                                        <div class="col-lg-2 fv-row">
                                            <label class="form-label fw-semibold fs-6">
                                                Amount
                                            </label>
                                            <input type="text" name="amount[]" class="form-control form-control-solid item-amount" readonly>
                                        </div>
                                        <!-- REMOVE -->
                                        <div class="col-lg-1 fv-row d-flex align-items-end" style="margin-bottom:31px !important;">
                                            <button type="button" class="btn btn-danger remove-item">X</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ADD MORE -->
                            <div class="mb-8">
                                <button type="button" id="add_more_item" class="btn btn-primary">
                                    Add More Item
                                </button>
                            </div>
                            <div class="row mb-6">
                                <div class="col-lg-3 fv-row">
                                    <label class="form-label">
                                        Net Total
                                    </label>
                                    <input type="text" id="net_total" name="net_total" class="form-control form-control-solid" readonly>
                                </div>
                                <div class="col-lg-3 fv-row">
                                    <label class="form-label">
                                        COD Charge
                                    </label>
                                    <input type="number" id="cod_charge" name="cod_charge" class="form-control form-control-solid" value="0">
                                </div>

                                <div class="col-lg-3 fv-row">
                                    <label class="form-label">
                                        Discount
                                    </label>
                                    <input type="number" id="discount" name="discount" class="form-control form-control-solid" value="0">
                                </div>
                                <div class="col-lg-3 fv-row">
                                    <label class="form-label">
                                        Grand Total
                                    </label>
                                    <input type="text" id="grand_total" name="grand_total" class="form-control form-control-solid" readonly>
                                </div>
                            </div>
                            <!-- CUSTOMER -->
                            <div class="row mb-6">
                                <div class="col-lg-6 fv-row">
                                    <label class="form-label required fw-semibold fs-6">
                                        Customer Name
                                    </label>
                                    <input type="text" name="fullname" id="fullname" class="form-control form-control-lg form-control-solid" placeholder="Customer Name">
                                </div>

                                <div class="col-lg-6 fv-row">
                                    <label class="form-label required fw-semibold fs-6">
                                        Mobile Number
                                    </label>
                                    <input type="text" name="cmobile" id="cmobile" class="form-control form-control-lg form-control-solid" placeholder="10 Digit Mobile Number">
                                </div>
                            </div>

                            
                            
                            
                             <!-- ADDRESS -->
                            <div class="row mb-6">
                                <div class="col-lg-6 fv-row">
                                    <label class="form-label fw-semibold fs-6">
                                        Shipping Address
                                    </label>
                                    <textarea name="shipping_address" id="shipping_address" class="form-control form-control-solid" placeholder="Shipping Address"></textarea>
                                </div>
                                <div class="col-lg-6 fv-row">
                                    <label class="form-label fw-semibold fs-6">
                                        Pincode
                                    </label>
                                    <input type="text" name="pincode" id="pincode" class="form-control form-control-solid" placeholder="Pincode"/>
                                </div>
                            </div>
                             <div class="row mb-6">
                                 <div class="col-lg-6 fv-row">
                                    <label class="form-label required fw-semibold fs-6">
                                        Country
                                    </label>
                                    <select name="country_id" name="country_id" class="form-select form-select-solid" data-control="select2">
                                        <option value="">Select Country</option>
                                            <?php
                                            if ($country_list) {
                                                foreach ($country_list as $single_country) {?>
                                                <option value="<?php echo $single_country['id']; ?>">
                                                    <?php echo ucfirst($single_country['name']); ?>
                                                </option>
                                                    <?php
                                                }
                                            }
                                            ?>

                                    </select>
                                </div>
                                <div class="col-lg-6 fv-row">
                                    <label class="form-label fw-semibold fs-6">
                                        State
                                    </label>
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

                            <!-- DELIVERY / PAYMENT -->
                            <div class="row mb-6">
                                <div class="col-lg-6 fv-row">
                                    <label class="form-label fw-semibold fs-6">
                                        City
                                    </label>
                                    <input type="text" name="city" id="city" class="form-control form-control-lg form-control-solid" placeholder="City">
                                </div>
                                <div class="col-lg-6 fv-row">
                                    <label class="form-label required fw-semibold fs-6">
                                        Payment Method
                                    </label>
                                    <select name="payment_method" id="payment_method" class="form-select form-select-solid">
                                        <option value="1">COD</option>
                                        <option value="2">Prepaid</option>
                                    </select>
                                </div>
                            </div>

                            <!-- REMARKS -->
                            <div class="row mb-6">
                                <div class="col-lg-8 fv-row">
                                    <label class="form-label">
                                        Special Instructions
                                    </label>
                                    <input type="text" name="remarks" id="remarks" class="form-control form-control-solid" placeholder="Remarks">
                                </div>
                            </div>
                        </div>

                        <!-- FOOTER -->
                        <div class="card-footer d-flex justify-content-end py-6 px-9">
                            <button type="reset" class="btn btn-light btn-active-light-primary me-2">
                                Discard
                            </button>
                            <button type="submit" class="btn btn-primary" id="kt_order_details_submit">
                                Save Order
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php include("includes/footer.php"); ?>

</div>

<script src="<?php echo $site_path; ?>/assets/plugins/global/plugins.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/scripts.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/custom/create-order.js?v=<?php echo time(); ?>"></script>

<script>

    $(document).ready(function () {
        $('.channel-radio').on('change', function () {

        let selectedValue = $(this).val().toLowerCase();
        if (selectedValue === 'store') {
            $('#store_details').removeClass('d-none');
        } else {
            $('#store_details').addClass('d-none');
        }
        
        if(selectedValue == 'wholesale'){
            $("#whole_saler_details").removeClass('d-none');
        }else{
            $('#whole_saler_details').addClass('d-none');
        }

    });
        // DATE PICKER
        $("#delivery_date").flatpickr({
            altInput: true,
            altFormat: "Y-m-d",
            dateFormat: "Y-m-d"
        });

        // PRODUCT CHANGE
        $(document).on('change', '.product-select', function () {

            let row = $(this).closest('.order-item');

            let price = $(this).find(':selected').data('price') || 0;

            let qty = row.find('.qty').val() || 1;

            let total = parseFloat(price) * parseInt(qty);

            row.find('.item-amount').val(total);

            calculateTotals();
        });

        // QTY CHANGE
        $(document).on('keyup change', '.qty', function () {

            let row = $(this).closest('.order-item');

            let price = row.find('.product-select option:selected').data('price') || 0;

            let qty = $(this).val() || 1;

            let total = parseFloat(price) * parseInt(qty);

            row.find('.item-amount').val(total);

            calculateTotals();
        });

        // ADD MORE ITEM
        $('#add_more_item').click(function () {

            let cloned = $('.order-item:first').clone();

            cloned.find('select').val('');

            cloned.find('.qty').val(1);

            cloned.find('.item-amount').val('');

            $('#order_items').append(cloned);

            $('.product-select').select2();
        });

        // REMOVE ITEM
        $(document).on('click', '.remove-item', function () {

            if ($('.order-item').length > 1) {

                $(this).closest('.order-item').remove();

                calculateTotals();
            }

        });

        // COD / DISCOUNT
        $('#cod_charge, #discount').on('keyup change', function () {

            calculateTotals();

        });

        // TOTAL FUNCTION
        function calculateTotals() {

            let netTotal = 0;

            $('.item-amount').each(function () {

                netTotal += parseFloat($(this).val()) || 0;

            });

            $('#net_total').val(netTotal);

            let codCharge = parseFloat($('#cod_charge').val()) || 0;

            let discount = parseFloat($('#discount').val()) || 0;

            let grandTotal = netTotal + codCharge - discount;

            $('#grand_total').val(grandTotal);
        }

    });

</script>

</body>
</html>