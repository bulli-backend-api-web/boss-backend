<?php
include "config/database.php";
include "config/auth_check.php";
include "includes/sidemenu.php";

$batch_id = (int) $_GET['batch_id'];

$stmt = $con->prepare("
    SELECT * 
    FROM stock_inward_batch 
    WHERE id = ?
");

$stmt->bind_param("i", $batch_id);
$stmt->execute();
$result = $stmt->get_result();
$batch = $result->fetch_assoc();
$stmt->close();
/*
  |--------------------------------------------------------------------------
  | Get Summary
  |--------------------------------------------------------------------------
 */

$stmt = $con->prepare("
    SELECT 
        COUNT(*) AS total_qty,
        SUM(
            CASE 
                WHEN scan_status = 1 THEN 1 
                ELSE 0 
            END
        ) AS scanned_qty
    FROM stock_inward_qr
    WHERE batch_id = ?
");

$stmt->bind_param("i", $batch_id);
$stmt->execute();
$result = $stmt->get_result();
$summary = $result->fetch_assoc();

$stmt->close();

/*
  |--------------------------------------------------------------------------
  | Get QR List
  |--------------------------------------------------------------------------
 */

$stmt = $con->prepare("
    SELECT * 
    FROM stock_inward_qr
    WHERE batch_id = ?
    ORDER BY id ASC
");

$stmt->bind_param("i", $batch_id);

$stmt->execute();

$result = $stmt->get_result();

$list = [];

while ($row = $result->fetch_assoc()) {
    $list[] = $row;
}

$stmt->close();
?>

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">

        <div id="kt_app_toolbar" class="app-toolbar pt-7 pt-lg-10">
            <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
                <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                    <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                        <h1 class="page-heading text-gray-900 fw-bold fs-3 m-0">Inward Summary</h1>
                    </div>
                </div>
            </div>
        </div>

        <div id="kt_app_content" class="app-content">
            <div id="kt_app_content_container" class="app-container container-fluid">


                <div class="card card-flush">
                    <div class="card-header">
                        <div class="card-title">
                            <h3 class="fw-bold text-success">Inward Completed Successfully</h3>
                        </div>
                    </div>

                    <div class="card-body border-top p-9">

                        <div class="row g-5 mb-8">
                            <div class="col-md-4">
                                <div class="card bg-light-primary">
                                    <div class="card-body">
                                        <span class="text-muted fw-semibold">Batch No</span>
                                        <div class="fs-3 fw-bold"><?php echo $batch['batch_no']; ?></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card bg-light-success">
                                    <div class="card-body">
                                        <span class="text-muted fw-semibold">Total Inward</span>
                                        <div class="fs-2hx fw-bold"><?php echo $summary['scanned_qty']; ?></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card bg-light-info">
                                    <div class="card-body">
                                        <span class="text-muted fw-semibold">Status</span>
                                        <div class="fs-3 fw-bold"><?php echo $batch['status']; ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-6 gy-4">
                                <thead>
                                    <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase">
                                        <th>#</th>
                                        <th>QR Code</th>
                                        <th>Product</th>
                                        <th>Size</th>
                                        <th>Scanned At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1;
                                    foreach ($list as $row) { ?>
                                        <tr>
                                            <td><?php echo $i++; ?></td>
                                            <td class="fw-bold"><?php echo $row['qr_code']; ?></td>
                                            <td><?php echo $row['product_id']; ?></td>
                                            <td><?php echo $row['size']; ?></td>
                                            <td><?php echo $row['scanned_at']; ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>

                    </div>

                    <div class="card-footer d-flex justify-content-end py-6 px-9">
                        <a href="<?php echo $site_path; ?>/create-product-wise-stock" class="btn btn-primary">
                            Create New Batch
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>

<?php include("includes/footer.php"); ?>
</div>
<script src="<?php echo $site_path; ?>/assets/plugins/global/plugins.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/scripts.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/plugins/custom/datatables/datatables.bundle.js"></script>