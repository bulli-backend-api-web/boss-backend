<?php
include("config/database.php");
include("config/auth_check.php");
include("includes/sidemenu.php");
$to_date = '';
$from_date = '';
?>
<style>
.status-light{
    width:12px;
    height:12px;
    border-radius:50%;
    display:inline-block;
}

.blue{
    background:#2196f3;
}
.green{
    background:#4caf50;
}

.yellow{
    background:#ffc107;
}

.red{
    background:#ff0000;
    animation: blink 1s infinite;
}

@keyframes blink{
    0%{opacity:1;}
    50%{opacity:0.2;}
    100%{opacity:1;}
}
#dtoTabs .nav-link {
    font-weight: 600;
    padding: 10px 20px;
}

#dtoTabs .nav-link.active {
    color: #009ef7;
    border-bottom: 3px solid #009ef7;
}
</style>
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_toolbar" class="app-toolbar pt-7 pt-lg-10">
            <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
                <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                    <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                        <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">Manage Return DTO Order List</h1>
                    </div>
                </div>
            </div>
        </div>
        <div id="kt_app_content" class="app-content">
            <div id="kt_app_content_container" class="app-container container-fluid">
                <div class="card">
                    <div class="card-header border-0 pt-6">
                        <div class="card-title d-flex flex-wrap align-items-center gap-3">
                            <!--begin::Search-->
                            <div class="d-flex align-items-center position-relative">
                                <i class="ki-outline ki-magnifier fs-3 position-absolute ms-5"></i>
                                <input type="text" data-kt-customer-table-filter="search" class="form-control form-control-solid w-250px ps-12" placeholder="Search Order"/>
                            </div>
                            <button id="daterangeBtn" data-kt-daterangepicker="true" data-kt-daterangepicker-opens="left" class="btn btn-light d-flex align-items-center">
                                <span class="fw-semibold text-gray-600" id="reportrange">All</span>
                                <i class="ki-outline ki-calendar-8 fs-2 ms-2"></i>
                            </button>
                            <div style="width:200px;">
                                <select id="search_by_status" class="form-select form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="Status" data-kt-ecommerce-product-filter="Status">
                                    <option value="all">All</option>
                                    <option value="0" selected>Pending</option>
                                    <option value="1">Close</option>
                                </select>
                            </div>
                            <div style="width:180px;">
                                <button type="button" id="export_return_dto" class="btn btn-primary d-flex align-items-center"><i class="ki-outline ki-exit-up fs-2 me-2"></i>Export</button>
                            </div>
                        </div>
                        
                        <div class="w-100 mt-5">
                            <ul class="nav nav-line-tabs nav-line-tabs-2x fw-semibold fs-6">

                                <li class="nav-item">
                                    <a class="nav-link active dto-tab"
                                       data-status="Pending"
                                       href="javascript:void(0)">
                                        Pending
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link dto-tab"
                                       data-status="InTransist"
                                       href="javascript:void(0)">
                                        In Transit
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link dto-tab"
                                       data-status="Delivered"
                                       href="javascript:void(0)">
                                        Delivered
                                    </a>
                                </li>

                            </ul>
                    </div>
                    </div>
                    
                    <input type="hidden" id="from_date" name="from_date">
                        <input type="hidden" id="to_date" name="to_date">
                        <input type="hidden" id="dto_status" value="Pending">

                    <div class="card-body py-4">
                         <div class="table-responsive">
                        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_order_report_table">
                                <thead class="bg-light border-bottom">
                                <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                    <th class="min-w-125px">Order From</th>
                                    <th class="min-w-125px">DTO Way</th>
                                    <th class="min-w-125px">Date</th>
                                    <th class="min-w-125px">Ticket ID</th>
                                    <th class="min-w-125px">Order ID</th>
                                    <th class="min-w-125px">Customer</th>
                                    <th class="min-w-125px">Mobile Number</th>
                                    <th class="min-w-125px">Agent Name</th>
                                    <th class="min-w-125px">Return Pcs.</th>
                                    <th class="min-w-125px">Courier</th>
                                    <th class="min-w-125px">Tracking No</th>
                                    <th class="min-w-125px">Rev.Pickup Status</th>
                                    <th class="min-w-125px">Reason</th>
                                    <th class="min-w-125px">Damage Proof</th>
                                    <th class="min-w-125px">Client Damage Proof</th>
                                    <th class="min-w-125px">Courier Receive Image</th>
                                    <th class="min-w-125px">Price</th>
                                    <th class="min-w-125px">Deduction A</th>
                                    <th class="min-w-125px">Deduction B</th>
                                    <th class="min-w-125px">Deduction C</th>
                                    <th class="min-w-125px">Total Refund</th>
                                    <th class="min-w-125px">G-PAY Number</th>
                                    <th class="min-w-125px">Whatsapp Number Verify</th>
                                    <th class="min-w-125px">Refund Status</th>
                                    <th class="min-w-125px">Refund Initiate Date</th>
                                    <th class="min-w-125px">Bank</th>
                                    <th class="min-w-125px">Refund Proof Image</th>
                                    <th class="min-w-125px">Image Upload Date</th>
                                    <th class="min-w-125px">Image Upload By</th>
                                    <th class="min-w-125px">APP Remarks</th>
                                    <th class="min-w-125px">Remarks & Close</th>
                                    <th class="min-w-125px">Action</th>
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
      <?php include("includes/footer.php"); ?>

    </div>
</div>

<script src="<?php echo $site_path; ?>/assets/plugins/global/plugins.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/scripts.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<script>
    
 function openPopupCentered(imageUrl) {
            const width = 600;
            const height = 600;
            const left = (screen.width / 2) - (width / 2);
            const top = (screen.height / 2) - (height / 2);

            const popup = window.open('', 'ImagePopup', `width=${width},height=${height},top=${top},left=${left},resizable=yes`);

            popup.document.write(`
                    <html>
                        <head><title>Image Preview</title></head>
                        <body style="margin:0; background:#000; display:flex; align-items:center; justify-content:center;">
                            <img src="${imageUrl}" style="max-width:100%; max-height:100%;">
                        </body>
                    </html>
                `);
            popup.document.close();
        }

    $(document).ready(function () {
        if (document.referrer.indexOf('return-dto-order-list') === -1) {
            localStorage.removeItem('activeTab');
        }
        let activeTab = localStorage.getItem('activeTab');
        if (activeTab) {
            $('.dto-tab').removeClass('active');
            // find correct tab
            let selectedTab = $('.dto-tab[data-status="' + activeTab + '"]');
            if (selectedTab.length) {
                selectedTab.addClass('active');
                // 🔥 IMPORTANT: trigger click AFTER small delay
                setTimeout(function () {
                    selectedTab.trigger('click');
                }, 100);
            }
        }
        
        // Initialize DataTable
        var table = $('#kt_order_report_table').DataTable({
            processing: true,
            serverSide: true,
            deferRender: true,
            ajax: {
                url: '<?php echo $site_path ?>/ajax/ajax-return-dto-order-list-data', // current PHP page
                type: 'POST',
                data: function (d) {
                    d.ajax = 1;
                    d.from_date = $("#from_date").val();
                    d.to_date = $("#to_date").val();
                    d.search_by_status = $("#search_by_status").val();
                    d.dto_status = $("#dto_status").val();
                }
            },

            columns: [
                {data: 'order_from'},
                {data: 'dto_way'},
                {data: 'date'},
                {data: 'ticket_id'},
                {data: 'order_id'},
                {data: 'customer_name'},
                {data: 'mobile_number'},
                {data: 'agent_name'},
                {data: 'qty'},
                {data: 'courier_name'},
                {data: 'tracking_number'},
                {data: 'rev_pickup_status'},
                {data: 'reason'},
                {data: 'damage_proof'},
                {data: 'client_damage_proof'},
                {data: 'courier_rev_image'},
                {data: 'price'},
                {data: 'deduction'},
                {data: 'deduction1'},
                {data: 'deduction2'},
                {data: 'final_price'},
                {data: 'gpay_number'},
                {data: 'gpay_number_verify'},
                {data: 'refund_status'},
                {data: 'refund_initiate_date'},
                {data: 'bank'},
                {data: 'refund_initiate_photo'},
                {data: 'image_upload_date'},
                {data: 'update_by'},
                {data: 'app_remaks'},
                {data: 'remark'},
                {data: 'action'}
            ],
            pageLength: 50,
            order: [[1, 'desc']], // default sort: Order Date descending
            columnDefs: [
                {targets: [0, 1, 2, 3, 4, 5,11], orderable: true}, // allow sorting on these columns
                {targets: [7, 8, 9,24], orderable: false} // actions column not sortable
            ],
            drawCallback: function () {
                KTMenu.createInstances();
                lazyLoadImages();
            }
        });

        $('[data-kt-customer-table-filter="search"]').on('keyup', function () {
            table.search(this.value).draw();
        });

         $("#search_by_status").on('change', function () {
            table.ajax.reload();
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
                'All': [moment(), moment()],
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
                const picker = pickerEl.data('daterangepicker');
                picker.setStartDate(moment());
                picker.setEndDate(moment());
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


        $("#export_return_dto").on('click', function () {
            var from_date = $("#from_date").val();
            var to_date = $("#to_date").val();
            var $btn = $(this);
            $btn.prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm align-middle me-2"></span>Generating report...');
            
            $.ajax({
                url: '<?php echo $site_path ?>/ajax/return-dto-order-report-export-csv',
                type: 'POST',
                data: {'from_date': from_date, 'to_date': to_date,'search_by_status':$("#search_by_status").val()},
                xhrFields: {responseType: 'blob'},
                success: function (blob) {
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'return-dto-order.csv';
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
        
        $(document).on('change', '#refund_proof_image', function () {
            var formData = new FormData();
            formData.append('refund_proof_image', this.files[0]);
            var rowId = $(this).data('id');
            var order_id = $(this).data('order-id');
            formData.append('row_id', rowId); // Add the row ID into the form data
            formData.append('order_id', order_id); // Add the row ID into the form data

            $.ajax({
                url: '<?php echo $site_path ?>/ajax/ajax-aws-refund-proof-image-upload.php',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    //location.reload(); // This will reload the entire page
                    let activeTab = localStorage.getItem('activeTab');
                    $('.dto-tab[data-status="' + activeTab + '"]').click();
                },
                error: function (xhr, status, error) {
                    alert('Upload failed!');
                }
            });
        });

        $(document).on('change', '.refuncstatuschange', function () {
            var refund_status = $(this).val();
            var id = $(this).data('id');
            var customer_mobile = $(this).data('customer-mobile');
            var customer_name = $(this).data('customer-name');
            var order_id = $(this).data('order-id');
            var bank_id = $('.bank_change[data-id="' + id + '"]').val();
            if(refund_status == 1){
                if (!bank_id || bank_id == '' || bank_id == 0) {
                    alert('Please select bank first');
                    return false;
                }
            }

            $.ajax({
                url: '<?php echo $site_path ?>/ajax/ajax-aws-refund-proof-image-upload.php',
                type: 'POST',
                data: {
                    row_id: id,
                    refund_status: refund_status,
                    customer_mobile : customer_mobile,
                    customer_name : customer_name,
                    order_id : order_id,
                    action: 'udated_refund_status'
                },
                success: function (response) {
                    location.reload();
                },
                error: function () {
                    alert('Error updating refund status.');
                }
            });
        });

        $(document).on('change', '.revpickuchange', function () {
            var refund_status = $(this).val();
            var id = $(this).data('id');

            $.ajax({
                url: '<?php echo $site_path ?>/ajax/ajax-aws-refund-proof-image-upload.php',
                type: 'POST',
                data: {
                    row_id: id,
                    refund_status: refund_status,
                    action: 'rev_pickup_status'
                },
                success: function (response) {
                    location.reload();
                },
                error: function () {
                    alert('Error updating refund status.');
                }
            });
        });

        $(document).on('change', '.remarksupdate', function () {
            var remarksdata = $(this).val();
            var id = $(this).data('id');

            $.ajax({
                url: '<?php echo $site_path ?>/ajax/ajax-aws-refund-proof-image-upload.php',
                type: 'POST',
                data: {
                    row_id: id,
                    remarksdata: remarksdata,
                    action: 'update_notes'
                },
                success: function (response) {
                    location.reload();
                },
                error: function () {
                    alert('Error updating refund status.');
                }
            });
        });

        $(document).on('change', '#damage_proof_image', function () {
            var formData = new FormData();
            formData.append('damage_proof_image', this.files[0]);
            var rowId = $(this).data('id');
            formData.append('row_id', rowId); // Add the row ID into the form data

            $.ajax({
                url: '<?php echo $site_path ?>/ajax/ajax-aws-refund-proof-image-upload.php',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    location.reload(); // This will reload the entire page
                },
                error: function (xhr, status, error) {
                    alert('Upload failed!');
                }
            });
        });
        
        $(document).on('change','.bank_change',function(){
            var bank_id = $(this).val();
            var id = $(this).data('id');

            $.ajax({
                url: '<?php echo $site_path ?>/ajax/ajax-aws-refund-proof-image-upload',
                type: 'POST',
                data: {
                    row_id: id,
                    bank_id: bank_id,
                    action: 'update_bank'
                },
                success: function (response) {
                    location.reload();
                },
                error: function () {
                    alert('Error updating refund status.');
                }
            });
        });
        
        $(document).on('click', '.delete_return_dto_order', function(e) {
            e.preventDefault();

            const btn = this;
            const row = $(btn).closest("tr");
            const dto_order_id = $(btn).data("id");
            const action = $(btn).data("action");
            const userName = "this DTO Order";

            Swal.fire({
                text: "Are you sure you want to delete " + userName + "?",
                icon: "warning",
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: "Yes, delete!",
                cancelButtonText: "No, cancel",
                customClass: {
                    confirmButton: "btn fw-bold btn-danger",
                    cancelButton: "btn fw-bold btn-active-light-primary"
                }
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        url: action,
                        type: "POST",
                        data: {
                            'dto_order_id': dto_order_id,
                            'action': 'delete_dto_order'
                        },
                        dataType: "json",
                        success: function(res) {
                            if (res.status === "success") {
                                Swal.fire({
                                    text: "You have deleted " + userName + "!",
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, got it!",
                                    customClass: {
                                        confirmButton: "btn fw-bold btn-primary"
                                    }
                                }).then(function() {
                                    $('#kt_table_users').DataTable().row(row).remove().draw();
                                    if (typeof a === 'function') a();
                                     window.location.reload();
                                });
                            } else {
                                Swal.fire({
                                    text: res.message || "Could not delete " + userName,
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, got it!",
                                    customClass: {
                                        confirmButton: "btn fw-bold btn-primary"
                                    }
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                text: "Server error! " + userName + " was not deleted.",
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary"
                                }
                            });
                        }
                    });
                } else if (result.dismiss === "cancel") {
                    Swal.fire({
                        text: userName + " was not deleted.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn fw-bold btn-primary"
                        }
                    });
                }
            });
        });
        
        $("#createDTO").on('click',function(){
            window.location.href = "<?php echo $site_path; ?>/create-dto";
        });
        
        $(document).on("click",".dto-tab",function(e){
            e.preventDefault();

            $(".dto-tab").removeClass("active");
            $(this).addClass("active");

            let status = $(this).data("status");
            localStorage.setItem('activeTab', status);
            $("#dto_status").val(status);

            table.ajax.reload();
    });

        function lazyLoadImages(){
            const observer = new IntersectionObserver(function(entries){
                entries.forEach(function(entry){
                    if(entry.isIntersecting){
                        let img = entry.target;
                        img.src = img.dataset.src;
                        observer.unobserve(img);
                    }
                });
            });
            document.querySelectorAll('.lazy-img').forEach(function(img){
                observer.observe(img);
            });
        }
    });

</script>
</body>
</html>