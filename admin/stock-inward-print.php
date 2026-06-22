<?php
include "config/database.php";
include "config/auth_check.php";
include "includes/sidemenu.php";

require_once "vendor/autoload.php";

use Picqer\Barcode\BarcodeGeneratorPNG;

$batch_id = isset($_GET['batch_id']) ? my_simple_crypt($_GET['batch_id'], 'decrypt_1') : 0;

if ($batch_id <= 0) {
    die("Invalid Batch");
}

/* Batch Details */
$stmt = $con->prepare("
    SELECT *
    FROM stock_inward_batch
    WHERE id = ?
");
$stmt->bind_param("i", $batch_id);
$stmt->execute();
$batch_result = $stmt->get_result();
$batch = $batch_result->fetch_assoc();
$stmt->close();

if (!$batch) {
    die("Batch not found");
}

/* Labels */
$stmt = $con->prepare("
    SELECT *
    FROM stock_inward_qr
    WHERE batch_id = ?
    ORDER BY id ASC
");
$stmt->bind_param("i", $batch_id);
$stmt->execute();
$labels_result = $stmt->get_result();

$labels = [];

while ($row = $labels_result->fetch_assoc()) {
    $labels[] = $row;
}

$stmt->close();

$generator = new BarcodeGeneratorPNG();
?>

<style>

    .barcode-label {
        width: 280px;
        height: 140px;
        border: 1px dashed #b5b5c3;
        border-radius: 10px;
        background: #fff;
        text-align: center;
        padding: 10px;
        margin: 8px;
        display: inline-block;
        vertical-align: top;
    }

    .barcode-img {
        width: 230px;
        height: 60px;
        object-fit: contain;
    }

    .barcode-text {
        font-size: 14px;
        font-weight: 700;
        margin-top: 5px;
    }

    @media print {

        body * {
            visibility: hidden;
        }

        #print_area,
        #print_area * {
            visibility: visible;
        }

        #print_area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }

        .no-print {
            display: none !important;
        }
    }
</style>

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">

    <div class="d-flex flex-column flex-column-fluid">

        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6 no-print">

            <div id="kt_app_toolbar_container"
                 class="app-container container-xxl d-flex flex-stack">

                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">

                    <h1 class="page-heading text-gray-900 fw-bold fs-3 my-0">
                        Print Barcode Labels
                    </h1>

                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">

                        <li class="breadcrumb-item text-muted">
                            Inventory
                        </li>

                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>

                        <li class="breadcrumb-item text-muted">
                            Barcode Printing
                        </li>

                    </ul>

                </div>

            </div>

        </div>

        <div id="kt_app_content" class="app-content flex-column-fluid">

            <div id="kt_app_content_container" class="app-container container-xxl">

                <div class="card card-flush shadow-sm no-print">

                    <div class="card-header">

                        <div class="card-title">

                            <h3 class="fw-bold">
                                Batch : <?php echo htmlspecialchars($batch['batch_no']); ?>
                            </h3>

                        </div>

                    </div>

                    <div class="card-body">

                        <div class="mb-5">
                            <div class="text-muted">
                                All barcode labels will print in one go.
                                Once printing is completed, assign label attachment work below.
                            </div>
                        </div>

                        <button type="button"
                                onclick="window.print()"
                                class="btn btn-light-primary me-2">
                            Reprint Barcodes
                        </button>

                        <!-- Assign Work Box -->
                        <div id="scan_continue_box" class="mt-5">

                            <div class="card border">

                                <div class="card-header">
                                    <h3 class="card-title">
                                        Assign Label Attachment Work
                                    </h3>
                                </div>

                                <div class="card-body">

                                    <div class="row">

                                        <div class="col-md-6">

                                            <label class="form-label">
                                                Assign To User
                                            </label>

                                            <select id="assigned_user" class="form-select">

                                                <option value="">Select User</option>

                                                <?php
                                                $users = mysqli_query(
                                                    $con,
                                                    "SELECT id, name FROM user WHERE status=1 ORDER BY name"
                                                );
                                                while ($u = mysqli_fetch_assoc($users)) { ?>
                                                    <option value="<?php echo $u['id']; ?>">
                                                        <?php echo $u['name']; ?>
                                                    </option>
                                                <?php } ?>

                                            </select>

                                        </div>

                                        <div class="col-md-6">

                                            <label class="form-label">Remarks</label>

                                            <input type="text"
                                                   id="remarks"
                                                   class="form-control">

                                        </div>

                                    </div>

                                    <div class="mt-4">

                                        <button class="btn btn-primary"
                                                onclick="assignLabelWork()">
                                            Assign Work
                                        </button>

                                    </div>

                                </div>

                            </div>

                        </div>
                        <!-- /Assign Work Box -->

                    </div>

                </div>

                <!-- PRINT AREA -->
                <div id="print_area">

                    <?php foreach ($labels as $row) {

                        $barcode_no = $row['qr_code'];

                        $barcode = base64_encode(
                            $generator->getBarcode(
                                $barcode_no,
                                $generator::TYPE_CODE_128,
                                2,
                                60
                            )
                        );
                    ?>

                        <div class="barcode-label">

                            <div class="fw-bold mb-2">
                                STOCK INWARD LABEL
                            </div>

                            <img class="barcode-img"
                                 src="data:image/png;base64,<?php echo $barcode; ?>">

                            <div class="barcode-text">
                                <?php echo htmlspecialchars($barcode_no); ?>
                            </div>

                            <div style="font-size:12px;color:#666;">
                                Size : <?php echo htmlspecialchars($row['size']); ?>
                            </div>

                        </div>

                    <?php } ?>

                </div>
                <!-- /PRINT AREA -->

            </div>

        </div>

        <?php include("includes/footer.php"); ?>

    </div>

</div>

<script src="<?php echo $site_path; ?>/assets/plugins/global/plugins.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/scripts.bundle.js"></script>

<script>

    var batch_id = <?php echo $batch_id; ?>;

    /*
     |--------------------------------------------------------------------------
     | On Page Load — Mark All Printed, Then Auto Print
     |--------------------------------------------------------------------------
     */
    window.onload = function () {

        // Mark all labels in this batch as printed
        fetch('<?php echo $site_path; ?>/ajax/mark-single-label-printed', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'batch_id=' + batch_id + '&mark_all=1'
        })
        .then(res => res.json())
        .then(data => {

            // Auto print all labels
            window.print();

        })
        .catch(err => {

            // Even if the API call fails, still print
            window.print();

        });

    };


    /*
     |--------------------------------------------------------------------------
     | Assign Label Work
     |--------------------------------------------------------------------------
     */
    function assignLabelWork() {

        let user_id = $("#assigned_user").val();

        if (user_id == "") {
            alert("Please select user");
            return;
        }

        $.ajax({

            url: "<?php echo $site_path; ?>/ajax/assign-label-work",

            type: "POST",

            data: {
                batch_id: batch_id,
                user_id: user_id,
                remarks: $("#remarks").val()
            },

            success: function (res) {

                let data = JSON.parse(res);

                if (data.status) {

                    Swal.fire(
                        'Assigned',
                        'Label attachment assigned successfully',
                        'success'
                    ).then(() => {

                        window.location.href =
                            "<?php echo $site_path; ?>/inward-challan-dashboard";

                    });

                }

            }

        });

    }

</script>