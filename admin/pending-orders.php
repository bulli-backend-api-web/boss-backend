<?php
include("config/database.php");
include("config/auth_check.php");
include("includes/sidemenu.php");
$to_date = '';
$from_date = '';
$channelusers = [];
?>
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_toolbar" class="app-toolbar pt-7 pt-lg-10">
            <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
                <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                    <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                        <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">Pending Order List</h1>
                    </div>
                </div>
            </div>
        </div>
        <div id="kt_app_content" class="app-content">
            <div id="kt_app_content_container" class="app-container container-fluid">
                <div class="card">
                    <div class="card-header border-0 pt-6">

                        <!--begin::Toolbar-->
                        <div class="d-flex flex-wrap align-items-center gap-3 w-100">

                            <!-- Search -->
                            <div class="d-flex align-items-center position-relative flex-grow-1" style="max-width:320px;">
                                <i class="ki-outline ki-magnifier fs-3 position-absolute ms-5"></i>
                                <input type="text" class="form-control form-control-solid ps-12" placeholder="Search Order" data-kt-customer-table-filter="search"/>
                            </div>

                            <!-- Date Range -->
                            <div class="w-auto">
                                <button class="btn btn-light d-flex align-items-center" id="daterangeBtn">
                                    <i class="ki-outline ki-calendar fs-3 me-2"></i>
                                    <span id="reportrange">All</span>
                                </button>
                            </div>
                            <div class="filter-item">
                                <select id="payment_type" class="form-select form-select-solid" data-control="select2" data-hide-search="false" data-placeholder="Payment Type" data-kt-ecommerce-product-filter="Payment Type">
                                    <option></option>
                                    <option value="all">All</option>
                                    <option value="1">Prepaid</option>
                                    <option value="2">COD</option>
                                </select>
                            </div>
                            <div class="filter-item">
                                <select id="order_from" class="form-select form-select-solid" data-control="select2" data-hide-search="false" data-placeholder="Order From" data-kt-ecommerce-product-filter="Order From">
                                    <option></option>
                                    <option value="all">All</option>
                                    <option value="1">Shopify</option>
                                    <option value="2">U3K</option>
                                    
                                </select>
                            </div>
                            <div class="ms-auto">
                                <button type="button" id="export_b2c_order_csv" class="btn btn-primary d-flex align-items-center">
                                    <i class="ki-outline ki-exit-up fs-2 me-2"></i>
                                    Export
                                </button>
                            </div>
                        </div>
                        <input type="hidden" id="from_date" name="from_date">
                        <input type="hidden" id="to_date" name="to_date">
                        <!--end::Toolbar-->

                    </div>
                    <div class="card-body py-4">
                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed table-row-gray-300 gy-5 gs-7" id="kt_customers_table">
                                <thead  class="bg-light border-bottom">
                                    <tr class="text-gray-700 fw-bold fs-7">
                                        <th class="min-w-130px">Order ID</th>
                                        <th class="min-w-130px">Order Date</th>
                                        <th class="min-w-180px">Customer</th>
                                        <th class="min-w-120px">Channel</th>
                                        <th class="min-w-80px">Items</th>
                                        <th class="min-w-80px">City</th>
                                        <th class="min-w-110px">Pincode</th>
                                        <th class="min-w-140px">Payment</th>
                                        <th class="min-w-110px">Amount</th>
                                        <th class="min-w-110px">Delivery</th>
                                        <th class="min-w-110px">Status</th>
                                        <th class="min-w-100px text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="fw-semibold text-gray-600">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include("includes/footer.php"); ?>
</div>
</div>
</div>
</div>
<script>var hostUrl = "<?php echo $site_path; ?>/assets/";</script>
<script src="<?php echo $site_path; ?>/assets/plugins/global/plugins.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/scripts.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<script>
    "use strict";

    $(document).ready(function () {
        // Initialize DataTable
        var table = $('#kt_customers_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?php echo $site_path ?>/ajax/fetch-pending-orders',
                type: 'POST',
                data: function (d) {
                    d.ajax = 1;
                    d.from_date = $('#from_date').val();
                    d.to_date = $('#to_date').val();
                    d.payment_type = $("#payment_type").val();
                    d.order_from = $("#order_from").val();
                }
            },

            columns: [
                {data: 'order_id'},
                {data: 'order_date'},
                {data: 'customer_name'},
                {data: 'order_type'},
                {data: 'items'},
                {data: 'city'},
                {data: 'pincode'},
                {data: 'payment_type'},
                {data: 'grand_total'},
                {data: 'delivery_date'},
                {data: 'status'},
                {data: 'actions', orderable: false ,'className' : 'text-end'}
            ],
            pageLength: 50,
            order: [[0, 'desc']], // default sort: Order Date descending
            columnDefs: [
                {targets: [0, 1, 2, 4, 5,6,7,8], orderable: true}, // allow sorting on these columns
                {targets: [3], orderable: false} // actions column not sortable
            ],
            drawCallback: function () {
                KTMenu.createInstances();
            }
        });

        $('[data-kt-customer-table-filter="search"]').on('keyup', function () {
            table.search(this.value).draw();
        });

        $('[data-kt-ecommerce-product-filter="Channel"]').on('keyup', function () {
            table.search(this.value).draw();
        });

        //data-kt-ecommerce-product-filter

        // Initialize KT Daterangepicker
        const pickerEl = $('#daterangeBtn');
        const displayEl = pickerEl.find('div.text-gray-600');

        $(pickerEl).daterangepicker({
            pens: 'left',
            autoUpdateInput: false,
            startDate: moment().startOf('month'),
            endDate: moment().endOf('month'),
            locale: {format: 'YYYY-MM-DD'},
            ranges: {
                'All': [null, null],
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, function (start, end, label) {
            if (label === 'All') {
                $('#from_date').val('');
                $('#to_date').val('');
                $('#reportrange').text('All');
            } else {
                $('#from_date').val(start.format('YYYY-MM-DD'));
                $('#to_date').val(end.format('YYYY-MM-DD'));
                $('#reportrange').text(
                        start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY')
                        );
            }

            // reload DataTable
            table.ajax.reload();
        });
        $('#from_date').val('');
        $('#to_date').val('');
        displayEl.text('All');

        $("#payment_type").on('change', function () {
            table.ajax.reload();
        });

        $("#order_from").on('change', function () {
            table.ajax.reload();
        });


        $("#export_b2c_order_csv").on('click', function () {
            var from_date = $("#from_date").val();
            var to_date = $("#to_date").val();
            var order_from = $("#order_from").val();
            var payment_type = $("#payment_type").val();
            var product_id = $("#product_id").val();
            var $btn = $(this);
            $btn.prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm align-middle me-2"></span>Generating report...');

            $.ajax({
                url: '<?php echo $site_path ?>/ajax/all-order-export-csv',
                type: 'POST',
                data: {'from_date': from_date, 'to_date': to_date, 'order_from': order_from, 'payment_type': payment_type,'order_type':1},
                xhrFields: {responseType: 'blob'},
                success: function (blob) {
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'pending-order.csv';
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                    window.URL.revokeObjectURL(url);
                    $btn.prop('disabled', false)
                            .html('Export');
                },
                error: function () {
                    Swal.fire({
                        text: 'Error generating CSV. Please try again.',
                        icon: 'error',
                        confirmButtonText: 'Ok, got it!',
                        buttonsStyling: false,
                        customClass: {
                            confirmButton: 'btn fw-bold btn-primary'
                        }
                    });
                }
            });
        });
    });

</script>
</body>
</html>