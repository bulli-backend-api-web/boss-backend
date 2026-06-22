<?php
include("config/database.php");
include("config/auth_check.php");
include("includes/sidemenu.php");
?>

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">

        <div id="kt_app_toolbar" class="app-toolbar pt-7 pt-lg-10">
            <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
                <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                    <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                        <h1 class="page-heading text-gray-900 fw-bold fs-3 m-0">Stock Out</h1>
                    </div>
                </div>
            </div>
        </div>

        <div id="kt_app_content" class="app-content">
            <div id="kt_app_content_container" class="app-container container-fluid">

                <div class="card mb-6">
                    <div class="card-body p-12">
                        <div class="row">
                            <div class="col-lg-8 mb-6">
                                <label class="form-label fw-bold fs-6 text-gray-700">Scan Barcode</label>
                                <input type="text"
                                       id="scan_barcode"
                                       class="form-control form-control-lg form-control-solid"
                                       placeholder="Scan Barcode"
                                       autocomplete="off"
                                       autofocus>
                            </div>
                        </div>

                        <div id="scan_message"></div>
                    </div>
                </div>

                <div id="order_details_box"></div>

            </div>
        </div>

        <?php include("includes/footer.php"); ?>
    </div>
</div>

<script src="<?php echo $site_path; ?>/assets/plugins/global/plugins.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/scripts.bundle.js"></script>

<script>
$(document).ready(function () {

    $('#scan_barcode').focus();

    $('#scan_barcode').on('keypress', function (e) {

        if (e.which === 13) {
            e.preventDefault();

            let barcode = $(this).val().trim();

            if (barcode === '') {
                return;
            }

            $('#scan_message').html('<div class="alert alert-primary mt-4">Searching barcode...</div>');
            $('#order_details_box').html('');

            $.ajax({
                url: "<?php echo $site_path; ?>/ajax/get-stock-out-order-details.php",
                type: "POST",
                dataType: "json",
                data: {
                    barcode: barcode
                },
                success: function (res) {

                    if (res.status === true) {
                        $('#scan_message').html('');
                        $('#order_details_box').html(res.html);
                        $('#scan_barcode').val('').focus();
                    } else {
                        $('#scan_message').html('<div class="alert alert-danger mt-4">'+res.message+'</div>');
                        $('#scan_barcode').val('').focus();
                    }
                },
                error: function () {
                    $('#scan_message').html('<div class="alert alert-danger mt-4">Something went wrong.</div>');
                    $('#scan_barcode').val('').focus();
                }
            });
        }
    });

});
</script>