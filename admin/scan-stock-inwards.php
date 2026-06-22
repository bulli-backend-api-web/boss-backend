<?php
include("config/database.php");
include("config/auth_check.php");
include("includes/sidemenu.php");
?>

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">

        <div id="kt_app_toolbar" class="app-toolbar pt-7 pt-lg-10">
            <div id="kt_app_toolbar_container"
                 class="app-container container-fluid d-flex align-items-stretch">

                <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">

                    <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                        <h1 class="page-heading text-gray-900 fw-bold fs-3 m-0">
                            Scan Stock Inward
                        </h1>
                    </div>

                </div>
            </div>
        </div>

        <div id="kt_app_content" class="app-content">
            <div id="kt_app_content_container" class="app-container container-fluid">

                <div class="row g-5 g-xl-8">

                    <!-- Scan Box -->
                    <div class="col-xl-12">

                        <div class="card shadow-sm">

                            <div class="card-header">
                                <div class="card-title">
                                    <h3>Barcode Scanner</h3>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="mb-5">

                                    <label class="form-label fw-bold">
                                        Scan Barcode
                                    </label>

                                    <input type="text"
                                           id="scan_barcode"
                                           class="form-control form-control-solid form-control-lg"
                                           placeholder="Scan barcode here..."
                                           autocomplete="off"
                                           autofocus>

                                </div>

                                <div id="scan_message"></div>

                            </div>

                        </div>

                    </div>

                    <!-- Scan Result -->
                    <div class="col-xl-12">

                        <div class="card shadow-sm">

                            <div class="card-header">
                                <div class="card-title">
                                    <h3>Last Scanned Item</h3>
                                </div>
                            </div>

                            <div class="card-body">

                                <div id="scan_result">

                                    <div class="text-muted">
                                        Waiting for scan...
                                    </div>

                                </div>

                            </div>

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

<script>

$(document).ready(function(){

    $("#scan_barcode").focus();

    $("#scan_barcode").on("keypress", function(e){

        if(e.which == 13){

            e.preventDefault();

            let barcode = $(this).val().trim();

            if(barcode == ''){
                return false;
            }

            processBarcode(barcode);
        }

    });

});

function processBarcode(barcode)
{
    $("#scan_message").html(
        '<div class="alert alert-primary">Processing...</div>'
    );

    $.ajax({

        url: "<?php echo $site_path; ?>/ajax/scan-inward-barcode",

        type: "POST",

        dataType: "json",

        data: {
            barcode : barcode
        },

        success:function(response){

            if(response.status == true){

                $("#scan_message").html(
                    '<div class="alert alert-success">'+response.message+'</div>'
                );

                $("#scan_result").html(response.html);

            }else{

                $("#scan_message").html(
                    '<div class="alert alert-danger">'+response.message+'</div>'
                );

            }

            $("#scan_barcode").val('');
            $("#scan_barcode").focus();

        },

        error:function(){

            $("#scan_message").html(
                '<div class="alert alert-danger">Server Error</div>'
            );

            $("#scan_barcode").focus();

        }

    });
}
</script>