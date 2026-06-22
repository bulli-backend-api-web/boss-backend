<?php
include("config/database.php");
include("config/auth_check.php");
include("includes/sidemenu.php");

error_reporting(E_ALL);
ini_set('display_errors',1);

$batch_id = isset($_GET['batch_id']) ?  my_simple_crypt($_GET['batch_id'],'decrypt_1') : 0;

if ($batch_id <= 0) {
    die("Invalid Batch");
}

/*
  |--------------------------------------------------------------------------
  | Batch Details
  |--------------------------------------------------------------------------
 */
$stmt = $con->prepare("
    SELECT *
    FROM stock_inward_batch
    WHERE id = ?
    LIMIT 1
");
$stmt->bind_param("i", $batch_id);
$stmt->execute();
$result = $stmt->get_result();
$batch = $result->fetch_assoc();
$stmt->close();

if (!$batch) {
    die("Batch not found");
}

/*
  |--------------------------------------------------------------------------
  | Barcode Summary
  |--------------------------------------------------------------------------
 */
$barcode_summary = [
    'total' => 0,
    'printed' => 0,
    'scanned' => 0
];

$sql = "
SELECT
    COUNT(id) total,
    SUM(print_status) printed,
    SUM(scan_status) scanned
FROM stock_inward_qr
WHERE batch_id = '$batch_id'
";

$res = $con->query($sql);

if ($row = $res->fetch_assoc()) {

    $barcode_summary['total'] = $row['total'];
    $barcode_summary['printed'] = $row['printed'];
    $barcode_summary['scanned'] = $row['scanned'];
}

/*
  |--------------------------------------------------------------------------
  | Product Summary
  |--------------------------------------------------------------------------
 */
$product_summary = [];

$sql = "
SELECT
    p.name product_name,
    p.sku,
    q.size,
    COUNT(q.id) qty
FROM stock_inward_qr q
LEFT JOIN product p
ON p.id = q.product_id
WHERE q.batch_id = '$batch_id'
GROUP BY q.product_id,q.size
ORDER BY p.name ASC
";

$res = $con->query($sql);

while ($row = $res->fetch_assoc()) {
    $product_summary[] = $row;
}
?>

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">

    <div class="d-flex flex-column flex-column-fluid">

        <div id="kt_app_toolbar" class="app-toolbar pt-7 pt-lg-10">

            <div id="kt_app_toolbar_container"
                 class="app-container container-fluid d-flex align-items-stretch">

                <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">

                    <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">

                        <h1 class="page-heading text-gray-900 fw-bold fs-3 m-0">
                            Stock Inward Details
                        </h1>

                    </div>

                    <div>

                        <a href="<?php echo $site_path; ?>/stock-inward-print?batch_id=<?php echo my_simple_crypt($batch_id,'encrypt_1'); ?>"
                           class="btn btn-light-success me-2">
                            Print Labels
                        </a>

                        <a href="<?php echo $site_path; ?>/scan-stock-inwards?batch_id=<?php echo my_simple_crypt($batch_id,'encrypt_1'); ?>"
                           class="btn btn-primary">
                            Scan Stock
                        </a>

                    </div>

                </div>
            </div>
        </div>

        <div id="kt_app_content" class="app-content">

            <div id="kt_app_content_container"
                 class="app-container container-fluid">

                <!-- Summary Cards -->

                <div class="row g-5 g-xl-8 mb-8">

                    <div class="col-xl-3">
                        <div class="card card-flush shadow-sm">
                            <div class="card-body">
                                <div class="text-muted fw-semibold">Batch No</div>
                                <div class="fs-3 fw-bold text-primary">
                                    <?php echo $batch['batch_no']; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3">
                        <div class="card card-flush shadow-sm">
                            <div class="card-body">
                                <div class="text-muted fw-semibold">Total Units</div>
                                <div class="fs-2hx fw-bold">
                                    <?php echo number_format($barcode_summary['total']); ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3">
                        <div class="card card-flush shadow-sm">
                            <div class="card-body">
                                <div class="text-muted fw-semibold">Printed</div>
                                <div class="fs-2hx fw-bold text-success">
                                    <?php echo number_format($barcode_summary['printed']); ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3">
                        <div class="card card-flush shadow-sm">
                            <div class="card-body">
                                <div class="text-muted fw-semibold">Scanned</div>
                                <div class="fs-2hx fw-bold text-info">
                                    <?php echo number_format($barcode_summary['scanned']); ?>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Challan Details -->

                <div class="card card-flush shadow-sm mb-8">

                    <div class="card-header">
                        <div class="card-title">
                            <h3>Challan Information</h3>
                        </div>
                    </div>

                    <div class="card-body">

                        <div class="row">

                            <div class="col-md-3 mb-5">
                                <label class="text-muted fw-semibold">Challan No</label>
                                <div class="fw-bold">
                                    <?php echo $batch['challan_no']; ?>
                                </div>
                            </div>

                            <div class="col-md-3 mb-5">
                                <label class="text-muted fw-semibold">Inward Date</label>
                                <div class="fw-bold">
                                    <?php echo date('d M Y', strtotime($batch['inward_date'])); ?>
                                </div>
                            </div>

                            <div class="col-md-3 mb-5">
                                <label class="text-muted fw-semibold">Status</label>
                                <div>

                                    <?php
                                    $status_badge = '';

                                    switch ($batch['challan_status']) {

                                        case 'CREATED':
                                            $status_badge = '<span class="badge badge-light-warning">
        Created
        </span>';
                                            break;

                                        case 'PRINTED':
                                            $status_badge = '<span class="badge badge-light-primary">
        Printed
        </span>';
                                            break;

                                        case 'SCANNING':
                                            $status_badge = '<span class="badge badge-light-info">
        Scanning
        </span>';
                                            break;

                                        case 'COMPLETED':
                                            $status_badge = '<span class="badge badge-light-success">
        Completed
        </span>';
                                            break;
                                    }

                                    echo $status_badge;
                                    ?>

                                </div>
                            </div>

                            <div class="col-md-3 mb-5">
                                <label class="text-muted fw-semibold">Remarks</label>
                                <div class="fw-bold">
<?php echo!empty($batch['remarks']) ? $batch['remarks'] : '-'; ?>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>

                <!-- Product Summary -->

                <div class="card card-flush shadow-sm mb-8">

                    <div class="card-header">
                        <div class="card-title">
                            <h3>Product Summary</h3>
                        </div>
                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-row-dashed align-middle">

                                <thead>
                                    <tr>
                                        <th>SKU</th>
                                        <th>Product</th>
                                        <th>Size</th>
                                        <th>Qty</th>
                                    </tr>
                                </thead>

                                <tbody>

<?php foreach ($product_summary as $product) { ?>

                                        <tr>

                                            <td>
                                        <?php echo $product['sku']; ?>
                                            </td>

                                            <td>
    <?php echo $product['product_name']; ?>
                                            </td>

                                            <td>
                                                <span class="badge badge-light-dark">
                                                <?php echo $product['size']; ?>
                                                </span>
                                            </td>

                                            <td>
                                                <span class="badge badge-light-primary">
                                                    <?php echo $product['qty']; ?>
                                                </span>
                                            </td>

                                        </tr>

                                                <?php } ?>

                                </tbody>

                            </table>

                        </div>

                    </div>
                </div>

                <!-- Barcode List -->

                <div class="card card-flush shadow-sm">

                    <div class="card-header">

                        <div class="card-title">
                            <h3>Generated Barcode List</h3>
                        </div>

                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-row-dashed align-middle">

                                <thead>

                                    <tr>
                                        <th>#</th>
                                        <th>Barcode</th>
                                        <th>Product</th>
                                        <th>Size</th>
                                        <th>Print Status</th>
                                        <th>Scan Status</th>
                                    </tr>

                                </thead>

                                <tbody>

<?php
$sql = "
SELECT
    q.*,
    p.name product_name
FROM stock_inward_qr q
LEFT JOIN product p
ON p.id=q.product_id
WHERE q.batch_id='$batch_id'
ORDER BY q.id ASC
";

$res = $con->query($sql);

$i = 1;

while ($row = $res->fetch_assoc()) {
    ?>

                                        <tr>

                                            <td><?php echo $i++; ?></td>

                                            <td>
                                                <span class="fw-bold">
    <?php echo $row['qr_code']; ?>
                                                </span>
                                            </td>

                                            <td>
    <?php echo $row['product_name']; ?>
                                            </td>

                                            <td>
                                                <span class="badge badge-light-dark">
    <?php echo $row['size']; ?>
                                                </span>
                                            </td>

                                            <td>

                                                    <?php
                                                    echo $row['print_status'] ? '<span class="badge badge-light-success">Printed</span>' : '<span class="badge badge-light-warning">Pending</span>';
                                                    ?>

                                            </td>

                                            <td>

                                                <?php
                                                echo $row['scan_status'] ? '<span class="badge badge-light-success">Scanned</span>' : '<span class="badge badge-light-danger">Pending</span>';
                                                ?>

                                            </td>

                                        </tr>

                                            <?php } ?>

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