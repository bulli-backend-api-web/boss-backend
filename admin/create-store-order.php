<?php
include("config/database.php");
include("config/auth_check.php");
include("includes/sidemenu.php");

$bank_list = [];
?>
<style>
    #product-list {
        list-style: none;
        margin: 0;
        padding: 0;
        background: #fff;
        border: 1px solid #ccc;
        border-radius: 6px;
        width: 100%;
        max-height: 250px;
        overflow-y: auto;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    #product-list li {
        padding: 10px 12px;
        border-bottom: 1px solid #eee;
        cursor: pointer;
        font-size: 14px;
        color: #333;
    }
    #product-list li:last-child {
        border-bottom: none;
    }
    #product-list li:hover {
        background-color: #f4f6fa;
        color: #000;
        font-weight: 500;
        transition: all 0.2s ease-in-out;
    }
    .suggesstion-box {
        position: absolute;
        background: #fff;
        z-index: 9999;
        width: 100%;
        /*border: 1px solid #ddd;*/
    }
</style>
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <!--begin::Content wrapper-->
    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Toolbar-->
        <div id="kt_app_toolbar" class="app-toolbar pt-7 pt-lg-10">
            <!--begin::Toolbar container-->
            <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
                <!--begin::Toolbar wrapper-->
                <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                    <!--begin::Page title-->
                    <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                        <!--begin::Title-->
                        <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">Create</h1>
                        <!--end::Title-->
                        <!--begin::Breadcrumb-->
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <a href="<?php echo $site_path ?>/dashboard" class="text-muted text-hover-primary">Home</a>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">Create Store Order</li>
                        </ul>
                    </div>
                </div>
                <!--end::Toolbar wrapper-->
            </div>
            <!--end::Toolbar container-->
        </div>
        <!--end::Toolbar-->
        <!--begin::Content-->
        <div id="kt_app_content" class="app-content">
            <!--begin::Content container-->
            <div id="kt_app_content_container" class="app-container container-fluid">
                <!--begin::Layout-->
                <div class="d-flex flex-column flex-lg-row">
                    <!--begin::Content-->
                    <div class="flex-lg-row-fluid mb-10 mb-lg-0 me-lg-7 me-xl-10">
                        <!--begin::Card-->
                        <div class="card">
                            <!--begin::Card body-->
                            <div class="card-body p-12">
                                <!--begin::Form-->
                                <form action="" id="kt_invoice_form">
                                    <!--begin::Wrapper-->
                                    <div class="d-flex flex-column align-items-start flex-xxl-row">
                                        <!--begin::Input group-->
                                        <div class="d-flex align-items-center flex-equal fw-row me-4 order-2" data-bs-toggle="tooltip" data-bs-trigger="hover" title="Specify invoice date">
                                            <!--begin::Date-->
                                            <div class="fs-6 fw-bold text-gray-700 text-nowrap">Date:</div>
                                            <!--end::Date-->
                                            <!--begin::Input-->
                                            <div class="position-relative d-flex align-items-center w-150px">
                                                <!--begin::Datepicker-->
                                                <input class="form-control form-control-transparent fw-bold pe-5" placeholder="Select date" name="invoice_date" value="<?php echo date('d,M Y'); ?>" />
                                                <!--end::Datepicker-->
                                                <!--begin::Icon-->
                                                <i class="ki-outline ki-down fs-4 position-absolute ms-4 end-0"></i>
                                                <!--end::Icon-->
                                            </div>
                                            <!--end::Input-->
                                        </div>
                                        <!--end::Input group-->
                                        <!--begin::Input group-->
                                        <div class="d-flex flex-center flex-equal fw-row text-nowrap order-1 order-xxl-2 me-4" data-bs-toggle="tooltip" data-bs-trigger="hover" title="Enter invoice number">
                                            <span class="fs-2x fw-bold text-gray-800">Order #</span>
                                            <input type="text" class="form-control form-control-flush fw-bold text-muted fs-3 w-125px" value="<?php echo date('Ymd') ?>" placehoder="..." />
                                        </div>
                                    </div>
                                    <!--end::Top-->
                                    <!--begin::Separator-->
                                    <div class="separator separator-dashed my-10"></div>
                                    <!--end::Separator-->
                                    <!--begin::Wrapper-->
                                    <div class="mb-0">
                                        <!--begin::Row-->
                                        <div class="row gx-10 mb-5">
                                            <div class="col-lg-8 mb-6">
                                                <label class="form-label fw-bold fs-6 text-gray-700">Order Type</label>
                                                    <!--end::Label-->
                                                    <!--begin::Select-->
                                                    <select name="order_type" id="order_type" aria-label="Select a Order Type" data-control="select2" data-placeholder="Select order type" class="form-select form-select-solid">
                                                        <option value="">Select Order Type</option>
                                                        <option value="b2c-cash">B2C Cash</option>
                                                        <option value="b2b-cash">B2B Cash</option>
                                                    </select>
                                            </div>
                                            <!--begin::Col-->
                                            <div class="col-lg-6">
                                                <label class="form-label fs-6 fw-bold text-gray-700 mb-3">Bill From</label>
                                                <!--begin::Input group-->
                                                <div class="mb-5">
                                                    <input type="text" name="bill_from_name" class="form-control form-control-solid" placeholder="Name" value="Vastranand Private Limited" readonly />
                                                </div>
                                                <!--end::Input group-->
                                                <!--begin::Input group-->
                                                <div class="mb-5">
                                                    <input type="text" name="bill_from_mobile" class="form-control form-control-solid" placeholder="Mobile number" value="+918154000063" />
                                                </div>
                                                <!--end::Input group-->
                                                </div>
                                            <!--end::Col-->
                                            <!--begin::Col-->
                                            <div class="col-lg-6">
                                                <label class="form-label fs-6 fw-bold text-gray-700 mb-3">Bill To</label>
                                                <div class="mb-5">
                                                    <input type="text" name="bill_to_mobile_number" id="bill_to_mobile_number" class="form-control form-control-solid" placeholder="Mobile Number" maxlength="10" inputmode="numeric" pattern="[0-9]{10}" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)" />
                                                </div>
                                                <div class="mb-5">
                                                    <input name="bill_to_name" id="bill_to_name" type="text" class="form-control form-control-solid" placeholder="Name" />
                                                </div>
                                                <div class="mb-5">
                                                    <input type="text" name="reference_name" class="form-control form-control-solid" placeholder="Reference Name" />
                                                </div>
                                                <div class="mb-5">
                                                    <input type="text" name="reference_number" class="form-control form-control-solid" placeholder="Reference Number" />
                                                </div>
                                            </div>
                                        </div>
                                        <!--end::Row-->
                                        <!--begin::Table wrapper-->
                                        <div class="table-responsive mb-10">
                                            <!--begin::Table-->
                                            <table class="table g-5 gs-0 mb-0 fw-bold text-gray-700" data-kt-element="items">
                                                <!--begin::Table head-->
                                                <thead>
                                                    <tr class="border-bottom fs-7 fw-bold text-gray-700 text-uppercase">
                                                        <th class="min-w-300px w-475px">Item</th>
                                                        <th class="min-w-100px w-100px">QTY</th>
                                                        <th class="min-w-150px w-150px">Price</th>
                                                        <th class="min-w-100px w-150px text-end">Total</th>
                                                        <th class="min-w-75px w-75px text-end">Action</th>
                                                    </tr>
                                                </thead>
                                                <!--end::Table head-->
                                                <!--begin::Table body-->
                                                <tbody>
                                                    <tr class="border-bottom border-bottom-dashed" data-kt-element="item">
                                                        <td class="pe-7">
                                                            <input type="text" class="form-control form-control-solid mb-2 search_product" name="name[]" placeholder="Item name" id="search_product" />
                                                            <div id="suggesstion-box" class="suggesstion-box"></div>
                                                            <input type="text" class="form-control form-control-solid" name="description[]" placeholder="Description" />
                                                        </td>
                                                        <td class="ps-0">
                                                            <input class="form-control form-control-solid" type="number" min="1" name="quantity[]" placeholder="1" value="1" data-kt-element="quantity" />
                                                            <input class="form-control form-control-solid" type="hidden" name="search_product_id[]" />
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control form-control-solid text-end" name="price[]" placeholder="0.00" value="0.00" data-kt-element="price" />
                                                        </td>
                                                        <td class="pt-8 text-end text-nowrap">₹
                                                            <span data-kt-element="total">0.00</span></td>
                                                        <td class="pt-5 text-end">
                                                            <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-kt-element="remove-item">
                                                                <i class="ki-outline ki-trash fs-3"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                                <!--end::Table body-->
                                                <!--begin::Table foot-->
                                                <tfoot>
                                                    <tr class="border-top border-top-dashed align-top fs-6 fw-bold text-gray-700">
                                                        <th class="text-primary">
                                                            <button type="button" class="btn btn-link py-1" data-kt-element="add-item">Add item</button>
                                                        </th>
                                                        <th colspan="2" class="border-bottom border-bottom-dashed ps-0">
                                                            <div class="d-flex flex-column align-items-start">
                                                                <div class="fs-5">Subtotal</div>
                                                            </div>
                                                        </th>
                                                        <th colspan="2" class="border-bottom border-bottom-dashed text-end">₹
                                                            <span data-kt-element="sub-total">0.00</span></th>
                                                        
                                                    </tr>
                                                    <tr class="align-top fw-normal text-gray-700">
                                                        <th></th>
                                                        <th colspan="2" class="fs-4 ps-0">Discount</th>
                                                        <th colspan="2" class="text-end fs-4 text-nowrap">
                                                            <input type="text" name="discount" class="form-control form-control-solid discount" id="discount" value="0" inputmode="numeric" pattern="[0-9]*" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                                        </th>
                                                    </tr>
                                                    <tr class="align-top fw-bold text-gray-700">
                                                        <th></th>
                                                        <th colspan="2" class="fs-4 ps-0">Total</th>
                                                        <th colspan="2" class="text-end fs-4 text-nowrap">₹
                                                            <span data-kt-element="grand-total">0.00</span></th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                        <table class="table d-none" data-kt-element="item-template">
                                            <tr class="border-bottom border-bottom-dashed" data-kt-element="item">
                                                <td class="pe-7 position-relative">
                                                    <input type="text"
                                                           class="form-control form-control-solid mb-2 search_product"
                                                           name="name[]"
                                                           placeholder="Item name" />

                                                    <!-- THIS WAS MISSING -->
                                                    <div class="suggesstion-box"></div>

                                                    <input type="text"
                                                           class="form-control form-control-solid"
                                                           name="description[]"
                                                           placeholder="Description" />
                                                </td>

                                                <td class="ps-0">
                                                    <input class="form-control form-control-solid"
                                                           type="number"
                                                           min="1"
                                                           name="quantity[]"
                                                           placeholder="1"
                                                           data-kt-element="quantity" />
                                                    <input class="form-control form-control-solid" type="hidden"  name="search_product_id[]" />
                                                </td>

                                                <td>
                                                    <input type="text"
                                                           class="form-control form-control-solid text-end"
                                                           name="price[]"
                                                           placeholder="0.00"
                                                           data-kt-element="price" />
                                                </td>

                                                <td class="pt-8 text-end">₹ <span data-kt-element="total">0.00</span></td>

                                                <td class="pt-5 text-end">
                                                    <button type="button"
                                                            class="btn btn-sm btn-icon btn-active-color-primary"
                                                            data-kt-element="remove-item">
                                                        <i class="ki-outline ki-trash fs-3"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </table>
                                        <table class="table d-none" data-kt-element="empty-template">
                                            <tr data-kt-element="empty">
                                                <th colspan="5" class="text-muted text-center py-10">No items</th>
                                            </tr>
                                        </table>
                                        <!--end::Item template-->
                                        <!--begin::Notes-->
                                        <div class="mb-0">
                                            <label class="form-label fs-6 fw-bold text-gray-700">Notes</label>
                                            <textarea name="notes" class="form-control form-control-solid" rows="3" placeholder="notes"></textarea>
                                        </div>
                                        <!--end::Notes-->
                                    </div>
                                    <!--end::Wrapper-->
                                </form>
                                <!--end::Form-->
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Card-->
                    </div>
                    <!--end::Content-->
                    <!--begin::Sidebar-->
                    <div class="flex-lg-auto min-w-lg-300px">
                        <!--begin::Card-->
                        <div class="card" data-kt-sticky="true" data-kt-sticky-name="invoice" data-kt-sticky-offset="{default: false, lg: '200px'}" data-kt-sticky-width="{lg: '250px', lg: '300px'}" data-kt-sticky-left="auto" data-kt-sticky-top="150px" data-kt-sticky-animation="false" data-kt-sticky-zindex="95">
                            <!--begin::Card body-->
                            <div class="card-body p-10">
                                <!--begin::Input group-->
                                <div class="mb-10">
                                    <!--begin::Label-->
                                    <label class="form-label fw-bold fs-6 text-gray-700">Currency</label>
                                    <!--end::Label-->
                                    <!--begin::Select-->
                                    <select name="currnecy" aria-label="Select a Timezone" data-control="select2" data-placeholder="Select currency" class="form-select form-select-solid">
                                        <option data-kt-flag="flags/india.svg" value="INR">
                                        <b>INR</b>&nbsp;-&nbsp;India</option>
                                    </select>
                                    <!--end::Select-->
                                </div>
                                <!--end::Input group-->
                                <!--begin::Separator-->
                                <div class="separator separator-dashed mb-8"></div>
                                <!--end::Separator-->
                                <!--begin::Input group-->
                                <div class="mb-8">
                                    <!--begin::Option-->
                                    <label class="form-check form-switch form-switch-sm form-check-custom form-check-solid flex-stack mb-5">
                                        <span class="form-check-label ms-0 fw-bold fs-6 text-gray-700">Payment Method</span>
                                        : 
                                        <select name="bank_id" id="bank_id" aria-label="Select a Bank" data-control="select2" data-placeholder="Select Bank" class="form-select form-select-solid">
                                            <option value="">Bank</option>
                                            <?php if ($bank_list) {
                                                foreach ($bank_list as $single_bank) {
                                                    ?>
                                                    <option value="<?php echo $single_bank['id']; ?>"><?php echo $single_bank['name']; ?></option>
                                                <?php }
                                            } ?>
                                        </select>
                                    </label>
                                    <!--end::Option-->
                                    <!--begin::Option-->
                                    <label class="d-none form-check form-switch form-switch-sm form-check-custom form-check-solid flex-stack mb-5">
                                        <span class="form-check-label ms-0 fw-bold fs-6 text-gray-700">Late fees</span>
                                        <input class="form-check-input" type="checkbox" value="" />
                                    </label>
                                    <!--end::Option-->
                                    <!--begin::Option-->
                                    <label class="d-none form-check form-switch form-switch-sm form-check-custom form-check-solid flex-stack">
                                        <span class="form-check-label ms-0 fw-bold fs-6 text-gray-700">Notes</span>
                                        <input class="form-check-input" type="checkbox" value="" />
                                    </label>
                                    <!--end::Option-->
                                </div>
                                <!--end::Input group-->
                                <!--begin::Separator-->
                                <div class="separator separator-dashed mb-8"></div>
                                <!--end::Separator-->
                                <!--begin::Actions-->
                                <div class="mb-0">
                                    <!--begin::Row-->
                                    <div class="row mb-5">
                                        <!--begin::Col-->
                                        <div class="col">
                                            <a href="javascript:void(0);" class="btn btn-light btn-active-light-primary w-100"  id="invoicePreviewBtn">Preview</a>
                                        </div>
                                        <!--end::Col-->
                                        <!--begin::Col-->
                                        <div class="col">
                                            <a href="javascript:void(0);" class="btn btn-light btn-active-light-primary w-100 downloadInvoicePdf">Download</a>
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Row-->
                                    
                                    <button type="submit" href="#" class="btn btn-primary w-100" id="kt_invoice_submit_button">
                                        <i class="ki-outline ki-triangle fs-3"></i>Place Order</button>
                                </div>
                                <!--end::Actions-->
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Card-->
                    </div>
                    <!--end::Sidebar-->
                </div>
                <!--end::Layout-->
            </div>
            <!--end::Content container-->
        </div>
        <!--end::Content-->
    </div>
    <!--end::Content wrapper-->
    <!--begin::Footer-->
    <div id="kt_app_footer" class="app-footer">
        <!--begin::Footer container-->
        <div class="app-container container-fluid d-flex flex-column flex-md-row flex-center flex-md-stack py-3">
            <!--begin::Copyright-->
            <div class="text-gray-900 order-2 order-md-1">
                <span class="text-muted fw-semibold me-1"><?php echo date('Y') ?>©</span>
                <a href="https://vastranand.in" target="_blank" class="text-gray-800 text-hover-primary"> vastranand. All Rights Reserved.Powered by Vastranand Pvt Ltd.</a>
            </div>
        </div>
    </div>
    <div class="modal fade" id="invoicePreviewModal" tabindex="-1">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">

            <!-- Header with Close Icon -->
            <div class="modal-header">
                <h5 class="modal-title">Invoice Preview</h5>
                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>

            <!-- Body -->
            <div class="modal-body" id="invoicePreviewContainer">
                <!-- AJAX invoice HTML loads here -->
            </div>

            <!-- Footer with Close Button -->
            <div class="modal-footer">
                <button type="button"
                        class="btn btn-light"
                        data-bs-dismiss="modal">
                    Close
                </button>
            </div>

        </div>
    </div>
</div>
    <!--end::Footer-->
</div>
<!--end:::Main-->
</div>
<!--end::Wrapper-->
</div>
<!--end::Page-->
</div>

<!--end::Modals-->
<!--begin::Javascript-->
<script>var hostUrl = "<?php echo $site_path; ?>/";</script>
<script src="<?php echo $site_path; ?>/assets/plugins/global/plugins.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/scripts.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/custom/apps/invoices/create.js?v=<?php echo time(); ?>"></script>
<script src="<?php echo $site_path; ?>/assets/js/widgets.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/custom/widgets.js"></script>
<script>
    $(document).on('click', '.downloadInvoicePdf', function (e) {
        e.preventDefault();
        let btn = $(this);
        // Prevent multiple clicks
        if (btn.hasClass('disabled')) return;
        btn
          .addClass('disabled')
          .text('Downloading...');
        $.ajax({
            url: '<?php echo $site_path; ?>/ajax/store-order-download-invoice-pdf',
            type: 'POST',
            data: $('#kt_invoice_form').serialize(), // IMPORTANT
            xhrFields: {
                responseType: 'blob'
            },
            success: function (blob) {
                let url = window.URL.createObjectURL(blob);
                let a = document.createElement('a');
                a.href = url;
                a.download = 'Invoice.pdf';
                document.body.appendChild(a);
                a.click();
                a.remove();
                 btn
              .removeClass('disabled')
              .text('Download');
            },
            error: function () {
                alert('Failed to generate PDF');
                 btn
              .removeClass('disabled')
              .text('Download');
            }
        });
    });
    document.getElementById('invoicePreviewBtn').addEventListener('click', function () {
        const form = document.getElementById('kt_invoice_form');
        const formData = new FormData(form);
         formData.append('bank_id',$("#bank_id").val());

        fetch('<?php echo $site_path; ?>/ajax/invoice-preview-ajax', {
            method: 'POST',
            body: formData
        })
        .then(res => res.text())
        .then(html => {

            document.getElementById('invoicePreviewContainer').innerHTML = html;

            new bootstrap.Modal(
                document.getElementById('invoicePreviewModal')
            ).show();
        });
    });
function selectproductsku(el, product_id, product_sku, product_name, product_img, product_qty, product_price, gst_tax, image_id, weight) {
    let row = $(el).closest('tr');
    row.find('input[name="name[]"]').val(product_sku);
    row.find('input[name="search_product_id[]"]').val(product_id);
    row.find('input[name="quantity[]"]').val(product_qty);
    row.find('input[name="description[]"]').val(product_name);
    row.find('input[name="price[]"]').val(parseFloat(product_price).toFixed(2));
    row.find('.suggesstion-box').hide().html('');

    calculateRowTotal(row);
    calculateInvoiceTotal();
}
function calculateRowTotal(row) {
    let qty = parseFloat(row.find('input[name="quantity[]"]').val()) || 0;
    let price = parseFloat(
        row.find('input[name="price[]"]').val().replace(/,/g, '')
    ) || 0;

    let total = qty * price;
    row.find('[data-kt-element="total"]').text(total.toFixed(2));
}

function calculateInvoiceTotal() {
    let subtotal = 0;

    $('[data-kt-element="item"]').each(function () {
        let qty = parseFloat($(this).find('input[name="quantity[]"]').val()) || 0;
        let price = parseFloat(
            $(this).find('input[name="price[]"]').val().replace(/,/g, '')
        ) || 0;

        subtotal += qty * price;
    });
    var grandTotal = subtotal - parseFloat($("#discount").val());

    $('[data-kt-element="sub-total"]').text(subtotal.toFixed(2));
    $('[data-kt-element="grand-total"]').text(grandTotal.toFixed(2));
}
    $(document).on('input', 'input[name="quantity[]"], input[name="price[]"]', function () {
    let row = $(this).closest('[data-kt-element="item"]');
    calculateRowTotal(row);
    calculateInvoiceTotal();
});



    $(document).ready(function () {
        $(document).on('keyup', '.discount', function () {
            calculateInvoiceTotal();
        });
   $(document).on('keyup', '.search_product', function () {
        var search_keyword = $(this).val();
        var order_type = $("#order_type").val();
        let row = $(this).closest('tr');
        let suggestionBox = row.find('.suggesstion-box');
        $.ajax({
            type: "POST",
            url: "<?php echo $site_path ?>/ajax/auto-complete-serach-find-product_barcode_name",
            data: {
                keyword: search_keyword,
                    order_type: order_type
            },
            success: function (result) {
                suggestionBox.show();
                suggestionBox.html(result);
            }
        });
    }); 
        
        // Fetch Customer name from mobile number
        $("#bill_to_mobile_number").on('keyup',function(){
            var customer_mobile_numebr = $(this).val();
            if(customer_mobile_numebr!=''){
            $.ajax({
               type : "POST",
               url : "<?php echo $site_path ?>/ajax/fetch-customer-details-by-mobile",
               data : {'mobile_number' : customer_mobile_numebr},
               success:function(response){
                   if (response.success) {
                       $("#bill_to_name").val(response.data.customer_name);
                   }
               }
            });
            }
        });
    });
// =====================================================
// SEND INVOICE (AJAX SUBMIT)
// =====================================================
$(document).on('click', '#kt_invoice_submit_button', function () {
    var form = document.getElementById('kt_invoice_form');
    if ($('#order_type').val() === '') {
        alert('Please select Order Type');
        return;
    }

    var hasItem = false;
    $('[name="name[]"]').each(function () {
        if ($(this).val().trim() !== '') {
            hasItem = true;
        }
    });

    if (!hasItem) {
        alert('Please add at least one item');
        return;
    }
    var btn = $(this);
    btn.prop('disabled', true).html(
        '<span class="spinner-border spinner-border-sm"></span> Sending...'
    );
    var formData = new FormData(form);
        formData.append('bank_id',$("#bank_id").val());

    $.ajax({
        url: "<?= $site_path ?>/ajax/add-store-order-details", // CHANGE THIS
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,

        success: function (res) {
                btn.prop('disabled', false).html('<i class="ki-outline ki-triangle fs-3"></i> Place Order');
            try {
                res = typeof res === 'string' ? JSON.parse(res) : res;
                } catch (e) {
                }
 
            if (res.status === 'success') {
                alert('Invoice created successfully');
                window.location.reload(true);
            } else {
                alert(res.message || 'Something went wrong');
            }
        },

        error: function () {
            btn.prop('disabled', false).html(
                '<i class="ki-outline ki-triangle fs-3"></i> Send Invoice'
            );
            alert('Server error');
        }
    });
});
</script>
</body>
</html>