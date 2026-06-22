<?php
include("config/database.php");
include("config/auth_check.php");
include("includes/sidemenu.php");
?>
<style>
    .truncate-text {
    display: inline-block;
    max-width: 200px;     /* adjust to your column width */
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    vertical-align: middle;
}
    .variant-input {
        width: 80px !important;
        padding: 2px 4px;
        text-align: center;
    }
    .variant-input1 {
        width: 180px !important;
        padding: 2px 4px;
        text-align: center;
    }
</style>
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <!--begin::Content wrapper-->
    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Toolbar-->
        <div id="kt_app_toolbar" class="app-toolbar py-6">
            <!--begin::Toolbar container-->
            <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack flex-wrap gap-4">
                <!--begin::Toolbar wrapper-->
                <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                    <!--begin::Page title-->
                    <div class="page-title d-flex flex-column justify-content-center gap-1">
                        <!--begin::Title-->
                        <h1 class="page-heading text-gray-900 fw-bold fs-3 m-0">Quick Update Product</h1>
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
                            <li class="breadcrumb-item text-muted">Quick Update Product</li>
                            <!--end::Item-->

                        </ul>
                        <!--end::Breadcrumb-->
                    </div>
                    <!--end::Page title-->
                </div>
                <!--end::Toolbar wrapper-->
            </div>
            <!--end::Toolbar container-->
        </div>
        <div id="kt_app_content" class="app-content">
            <!--begin::Content container-->

            <div id="kt_app_content_container" class="app-container container-fluid">
                <!--begin::Card-->

                <div class="card">

                    <!--begin::Card header-->
                    <div class="card-header border-0 pt-6">
                        <!--begin::Card title-->
                        <div class="card-title d-flex flex-wrap align-items-center gap-3 w-100">
                            <!--begin::Search-->
                            <div class="d-flex align-items-center position-relative">
                                <i class="ki-outline ki-magnifier fs-3 position-absolute ms-5"></i>
                                <input type="text" data-kt-customer-table-filter="search" class="form-control form-control-solid w-250px ps-12" placeholder="Search Order"/>
                            </div>
                            <input type="hidden" id="from_date" name="from_date">
                            <input type="hidden" id="to_date" name="to_date">
                            <div style="width:200px;">
                                <!--begin::Select2-->
                                <select id="stock_status" class="form-select form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="Verified Status" data-kt-ecommerce-product-filter="User Status">
                                    <option value="-1">All</option>
                                    <option value="1">In Stock</option>
                                    <option value="0">Out Stock</option>
                                </select>
                            </div>
                            <div style="width:180px;">
                                <button type="button" id="export_product" class="btn btn-primary d-flex align-items-center"><i class="ki-outline ki-exit-up fs-2 me-2"></i>Export</button>
                            </div>

                        </div>
                    </div>
                    <div class="card-body py-4">
                        <div class="table-responsive">
                        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_order_return_status_list">
                                <thead class="bg-light border-bottom">
                                <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                    <th class="min-w-80px">Image</th>
                                    <th class="min-w-80px">Product Name</th>
                                    <th class="min-w-80px">Status</th>
                                    <th class="min-w-200px">SKU</th>
                                    <th class="min-w-50px">Product Stock</th>
                                    <th class="min-w-80px">Weight</th>
                                    <th class="min-w-80px">Price</th>
                                    <th class="min-w-80px">Stock Status</th>
                                    <th class="min-w-100px">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-semibold">
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
<div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
    <i class="ki-outline ki-arrow-up"></i>
</div>

<script>var hostUrl = "<?php echo $site_path; ?>/";</script>
<script src="<?php echo $site_path; ?>/assets/plugins/global/plugins.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/scripts.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/custom/apps/user-management/users/list/table.js?v=<?php echo time(); ?>"></script>

<script>
    $(document).ready(function () {
        // Initialize DataTable
        var table = $('#kt_order_return_status_list').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?php echo $site_path ?>/ajax/fetch-quick-product-update-details',
                type: 'POST',
                data: function (d) {
                    d.stock_status = $("#stock_status").val();
                    d.from_date = $('#from_date').val();
                    d.to_date = $('#to_date').val();
                }
            },

            columns: [
                {data: 'image'},
                {data: 'product_name'},
                {data: 'status'},
                {data: 'code'},
                {data: 'product_stock'},
                {data: 'weight'},
                {data: 'price'},
                {data: 'stock_status'},
                {data: 'actions', orderable: false}

            ],
            pageLength: 50,
            order: [[1, 'desc']],
            columnDefs: [
                {targets: [1, 2,3, 4, 6,7], orderable: true},
                {targets: [5,8], orderable: false}
            ],
            drawCallback: function () {
                KTMenu.createInstances();
                lazyLoadImages();
            }
        });
        $('[data-kt-customer-table-filter="search"]').on('keyup', function () {
            table.search(this.value).draw();
        });
        
        $("#stock_status").on('change', function () {
            table.ajax.reload();
        });
        
        $("#export_product").on('click', function () {
            var from_date = $("#from_date").val();
            var to_date = $("#to_date").val();
            var stock_status = $("#stock_status").val();
            var $btn = $(this);
            $btn.prop('disabled', true)
                .html('<span class="spinner-border spinner-border-sm align-middle me-2"></span>Generating report...');

            $.ajax({
                url: '<?php echo $site_path ?>/ajax/report-quick-product-update-csv',
                type: 'POST',
                data: {'from_date': from_date, 'to_date': to_date, 'stock_status': stock_status},
                xhrFields: {responseType: 'blob'},
                success: function (blob) {
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'product_quick_update.csv';
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

</script>
</body>
</html>