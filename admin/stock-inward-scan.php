<?php
include "config/database.php";
include "config/auth_check.php";
include "includes/sidemenu.php";

$batch_id = my_simple_crypt($_GET['batch_id'],'decrypt_1');

/*
|--------------------------------------------------------------------------
| Get Batch Details
|--------------------------------------------------------------------------
*/

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
| Get Scan Count
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

$scan_count = $result->fetch_assoc();

$stmt->close();


/*
|--------------------------------------------------------------------------
| Pending Qty
|--------------------------------------------------------------------------
*/

$total_qty   = $scan_count['total_qty'] ?? 0;
$scanned_qty = $scan_count['scanned_qty'] ?? 0;

$pending = $total_qty - $scanned_qty;


/*
|--------------------------------------------------------------------------
| Get Scanned List
|--------------------------------------------------------------------------
*/

$stmt = $con->prepare("
    SELECT * 
    FROM stock_inward_qr
    WHERE batch_id = ?
    ORDER BY id DESC
");

$stmt->bind_param("i", $batch_id);

$stmt->execute();

$result = $stmt->get_result();

$scanned_list = [];

while ($row = $result->fetch_assoc()) {
    $scanned_list[] = $row;
}

$stmt->close();
?>

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
<div class="d-flex flex-column flex-column-fluid">

<div id="kt_app_toolbar" class="app-toolbar pt-7 pt-lg-10">
<div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
<div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
    <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
        <h1 class="page-heading text-gray-900 fw-bold fs-3 m-0">Scan Stock Inward</h1>
        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
            <li class="breadcrumb-item text-muted">Inventory</li>
            <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
            <li class="breadcrumb-item text-muted">Scan Inward</li>
        </ul>
    </div>
</div>
</div>
</div>

<div id="kt_app_content" class="app-content">
<div id="kt_app_content_container" class="app-container container-fluid">

<div class="row g-5 mb-6">
    <div class="col-md-4">
        <div class="card card-flush">
            <div class="card-body">
                <span class="text-muted fw-semibold">Total QR</span>
                <div class="fs-2hx fw-bold"><?php echo $scan_count['total_qty']; ?></div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-flush">
            <div class="card-body">
                <span class="text-muted fw-semibold">Scanned</span>
                <div class="fs-2hx fw-bold text-success"><?php echo $scan_count['scanned_qty']; ?></div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-flush">
            <div class="card-body">
                <span class="text-muted fw-semibold">Pending</span>
                <div class="fs-2hx fw-bold text-danger"><?php echo $pending; ?></div>
            </div>
        </div>
    </div>
</div>

<div class="card card-flush">
<div class="card-header">
    <div class="card-title">
        <h3 class="fw-bold">Batch: <?php echo $batch['batch_no']; ?></h3>
    </div>
</div>

<div class="card-body border-top p-9">

    <div id="scan_msg"></div>

    <?php if($pending > 0){ ?>
        <form id="scan_form">
            <input type="hidden" id="batch_id" value="<?php echo $batch_id; ?>">

            <div class="mb-6">
                <label class="form-label fw-bold fs-5">Scanner Input</label>
                <input type="text"
                       id="qr_code"
                       class="form-control form-control-solid scan-input"
                       placeholder="Scan QR here"
                       autocomplete="off"
                       autofocus>
            </div>
        </form>
    <?php } else { ?>
        <div class="scan-success mb-5">All QR scanned successfully.</div>
        <a href="<?php echo $site_path; ?>/stock-inward-summary?batch_id=<?php echo $batch_id; ?>"
           class="btn btn-success">View Summary</a>
    <?php } ?>

    <div class="table-responsive mt-8">
        <table class="table align-middle table-row-dashed fs-6 gy-4">
            <thead>
                <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase">
                    <th>QR Code</th>
                    <th>Size</th>
                    <th>Status</th>
                    <th>Scanned At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($scanned_list as $row){ ?>
                    <tr>
                        <td class="fw-bold"><?php echo $row['qr_code']; ?></td>
                        <td><?php echo $row['size']; ?></td>
                        <td>
                            <?php if($row['scan_status'] == 1){ ?>
                                <span class="badge badge-light-success">Scanned</span>
                            <?php } else { ?>
                                <span class="badge badge-light-warning">Pending</span>
                            <?php } ?>
                        </td>
                        <td><?php echo $row['scanned_at']; ?></td>
                    </tr>
                <?php } ?>
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
<script src="<?php echo $site_path; ?>/assets/plugins/global/plugins.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/scripts.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<script>
const input = document.getElementById('qr_code');

if(input){
    input.focus();

    input.addEventListener('change', function(){
        let qrCode = this.value.trim();
        let batchId = document.getElementById('batch_id').value;
        if(qrCode === '') return;

        fetch('<?php echo $site_path; ?>/ajax/scan-stock-inward-qr', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'batch_id=' + encodeURIComponent(batchId) + '&qr_code=' + encodeURIComponent(qrCode)
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === true){
                document.getElementById('scan_msg').innerHTML =
                    '<div class="scan-success mb-5">'+data.message+'</div>';
                setTimeout(() => location.reload(), 500);
            } else {
                document.getElementById('scan_msg').innerHTML =
                    '<div class="scan-error mb-5">'+data.message+'</div>';
                input.value = '';
                input.focus();
            }
        });
    });
}
</script>