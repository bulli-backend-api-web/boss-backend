<?php
include("config/database.php");
include("config/auth_check.php");
include("includes/sidemenu.php");

$id = my_simple_crypt($_GET['id'],'decrypt_1');

if ($id <= 0) {
    die("Invalid request");
}

/*
|--------------------------------------------------------------------------
| Get request details
|--------------------------------------------------------------------------
*/
$stmt = $con->prepare("
    SELECT 
        ar.*,
        p.sku,
        p.name AS product_name
    FROM alteration_requests ar
    LEFT JOIN product p ON p.id = ar.product_id
    WHERE ar.id = ?
    LIMIT 1
");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$request = $result->fetch_assoc();
$stmt->close();

if (!$request) {
    die("Alteration request not found");
}

/*
|--------------------------------------------------------------------------
| Stock ledger movements
|--------------------------------------------------------------------------
*/
$stmt = $con->prepare("
    SELECT *
    FROM stock_ledger
    WHERE reference_id = ?
    AND reference_type IN (
        'ALTERATION_RESERVE',
        'ALTERATION_COMPLETE',
        'ALTERATION_REJECT'
    )
    ORDER BY id ASC
");
$stmt->bind_param("i", $id);
$stmt->execute();
$ledger_result = $stmt->get_result();

$ledger = [];
while ($row = $ledger_result->fetch_assoc()) {
    $ledger[] = $row;
}
$stmt->close();

$status = $request['status'];

function alterationStatusBadge($status)
{
    if ($status == 'STOCK_RESERVED') {
        return '<span class="badge badge-light-warning">Stock Reserved</span>';
    } elseif ($status == 'SENT_FOR_ALTERATION') {
        return '<span class="badge badge-light-info">Sent For Alteration</span>';
    } elseif ($status == 'READY_FOR_RECEIVE') {
        return '<span class="badge badge-light-primary">Ready For Receive</span>';
    } elseif ($status == 'RECEIVED') {
        return '<span class="badge badge-light-primary">Received</span>';
    } elseif ($status == 'QC_APPROVED') {
        return '<span class="badge badge-light-success">QC Approved</span>';
    } elseif ($status == 'COMPLETED') {
        return '<span class="badge badge-light-success">Completed</span>';
    } elseif ($status == 'REJECTED') {
        return '<span class="badge badge-light-danger">Rejected</span>';
    }

    return '<span class="badge badge-light-dark">'.$status.'</span>';
}

$next_action = '';

if ($status == 'STOCK_RESERVED') {
    $next_action = '
        <a href="'.$site_path.'/alteration-status-update?id='.my_simple_crypt($id,'encrypt_1').'&status=SENT_FOR_ALTERATION"
           class="btn btn-light-info">
            Send For Alteration
        </a>
    ';
} elseif ($status == 'SENT_FOR_ALTERATION') {
    $next_action = '
        <a href="'.$site_path.'/alteration-status-update?id='.my_simple_crypt($id,'encrypt_1').'&status=READY_FOR_RECEIVE"
           class="btn btn-light-primary">
                Ready For Receive
        </a>
    ';
} elseif ($status == 'RECEIVED') {
    $next_action = '
        <a href="'.$site_path.'/alteration-status-update?id='.my_simple_crypt($id,'encrypt_1').'&status=QC_APPROVED"
           class="btn btn-light-success">
            QC Approve
        </a>
    ';
}elseif ($status == 'RECEIVED') {
    $next_action = '
        <a href="'.$site_path.'/alteration-status-update?id='.my_simple_crypt($id,'encrypt_1').'&status=QC_APPROVED"
           class="btn btn-light-success">
            QC Approve
        </a>
    ';
} elseif ($status == 'QC_APPROVED') {
    $next_action = '
        <a href="'.$site_path.'/alteration-status-update?id='.my_simple_crypt($id,'encrypt_1').'&status=COMPLETED"
           class="btn btn-success">
            Complete Request
        </a>
    ';
}

$reject_action = '';

if ($status != 'COMPLETED' && $status != 'REJECTED') {
    $reject_action = '
        <a href="'.$site_path.'/alteration-status-update?id='.my_simple_crypt($id,'encrypt_1').'&status=REJECTED"
           class="btn btn-light-danger"
           onclick="return confirm(\'Are you sure you want to reject this request?\')">
            Reject
        </a>
    ';
}
?>

<style>
.alt-timeline {
    display: flex;
    flex-direction: column;
    gap: 18px;
}

.alt-step {
    display: flex;
    gap: 14px;
    align-items: flex-start;
}

.alt-step-icon {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: #f1f1f2;
    color: #7e8299;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
}

.alt-step.done .alt-step-icon {
    background: #50cd89;
    color: #fff;
}

.alt-step.active .alt-step-icon {
    background: #009ef7;
    color: #fff;
}

.alt-step-title {
    font-weight: 700;
    color: #181c32;
}

.alt-step-desc {
    font-size: 12px;
    color: #7e8299;
}
</style>

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">

        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">

                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading text-gray-900 fw-bold fs-3 my-0">
                        Alteration Details
                    </h1>

                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="<?php echo $site_path; ?>/pages/dashboard" class="text-muted text-hover-primary">
                                Home
                            </a>
                        </li>

                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>

                        <li class="breadcrumb-item text-muted">
                            Inventory
                        </li>

                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>

                        <li class="breadcrumb-item text-muted">
                            <?php echo htmlspecialchars($request['alteration_id']); ?>
                        </li>
                    </ul>
                </div>

                <div class="d-flex gap-2">
                    <a href="<?php echo $site_path; ?>/alteration"
                       class="btn btn-light">
                        Back
                    </a>

                    <?php echo $next_action; ?>

                    <?php echo $reject_action; ?>
                </div>

            </div>
        </div>

        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">

                <div class="row g-5">

                    <div class="col-xl-8">

                        <div class="card card-flush shadow-sm mb-5">

                            <div class="card-header">
                                <div class="card-title">
                                    <h3 class="fw-bold">
                                        Request Information
                                    </h3>
                                </div>

                                <div class="card-toolbar">
                                    <?php echo alterationStatusBadge($request['status']); ?>
                                </div>
                            </div>

                            <div class="card-body border-top p-9">

                                <div class="row mb-6">

                                    <div class="col-lg-6">
                                        <div class="text-muted fw-semibold mb-1">
                                            Alteration ID
                                        </div>
                                        <div class="fw-bold text-gray-900 fs-5">
                                            <?php echo htmlspecialchars($request['alteration_id']); ?>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="text-muted fw-semibold mb-1">
                                            Product
                                        </div>
                                        <div class="fw-bold text-gray-900">
                                            <?php echo htmlspecialchars($request['product_name']); ?>
                                        </div>
                                        <div class="text-muted fs-7">
                                            <?php echo htmlspecialchars($request['sku']); ?>
                                        </div>
                                    </div>

                                </div>

                                <div class="row mb-6">

                                    <div class="col-lg-4">
                                        <div class="text-muted fw-semibold mb-1">
                                            From Size
                                        </div>
                                        <span class="badge badge-light-danger fs-7">
                                            <?php echo htmlspecialchars($request['old_size']); ?>
                                        </span>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="text-muted fw-semibold mb-1">
                                            To Size
                                        </div>
                                        <span class="badge badge-light-success fs-7">
                                            <?php echo htmlspecialchars($request['new_size']); ?>
                                        </span>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="text-muted fw-semibold mb-1">
                                            Quantity
                                        </div>
                                        <div class="fw-bold fs-5">
                                            <?php echo (int)$request['qty']; ?>
                                        </div>
                                    </div>

                                </div>

                                <div class="row mb-6">

                                    <div class="col-lg-6">
                                        <div class="text-muted fw-semibold mb-1">
                                            Assigned To
                                        </div>
                                        <div class="fw-bold">
                                            <?php echo htmlspecialchars($request['assign_to']); ?>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="text-muted fw-semibold mb-1">
                                            Expected Return Date
                                        </div>
                                        <div class="fw-bold">
                                            <?php
                                            echo !empty($request['expected_return_date'])
                                                ? date('d M Y', strtotime($request['expected_return_date']))
                                                : '-';
                                            ?>
                                        </div>
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="text-muted fw-semibold mb-1">
                                            Created At
                                        </div>
                                        <div class="fw-bold">
                                            <?php echo date('d M Y h:i A', strtotime($request['created_at'])); ?>
                                        </div>
                                    </div>

                                </div>

                                <?php if (!empty($request['remarks'])) { ?>
                                    <div class="separator my-6"></div>

                                    <div>
                                        <div class="text-muted fw-semibold mb-1">
                                            Remarks
                                        </div>
                                        <div class="fw-semibold">
                                            <?php echo nl2br(htmlspecialchars($request['remarks'])); ?>
                                        </div>
                                    </div>
                                <?php } ?>

                            </div>

                        </div>

                        <div class="card card-flush shadow-sm">

                            <div class="card-header">
                                <div class="card-title">
                                    <h3 class="fw-bold">
                                        Stock Ledger Impact
                                    </h3>
                                </div>
                            </div>

                            <div class="card-body pt-0">

                                <div class="table-responsive">
                                    <table class="table align-middle table-row-dashed fs-6 gy-5">
                                        <thead>
                                            <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase">
                                                <th>#</th>
                                                <th>Date</th>
                                                <th>Size</th>
                                                <th>Type</th>
                                                <th>Qty</th>
                                                <th>Reference</th>
                                            </tr>
                                        </thead>

                                        <tbody class="fw-semibold text-gray-600">
                                            <?php if (!empty($ledger)) { ?>
                                                <?php $i = 1; foreach ($ledger as $l) { ?>
                                                    <tr>
                                                        <td><?php echo $i++; ?></td>
                                                        <td><?php echo date('d M Y h:i A', strtotime($l['created_at'])); ?></td>
                                                        <td>
                                                            <span class="badge badge-light-dark">
                                                                <?php echo htmlspecialchars($l['size']); ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <?php if ($l['movement_type'] == 'IN') { ?>
                                                                <span class="badge badge-light-success">IN</span>
                                                            <?php } elseif ($l['movement_type'] == 'OUT') { ?>
                                                                <span class="badge badge-light-danger">OUT</span>
                                                            <?php } elseif ($l['movement_type'] == 'RESERVE') { ?>
                                                                <span class="badge badge-light-warning">RESERVE</span>
                                                            <?php } else { ?>
                                                                <span class="badge badge-light-primary">UNRESERVE</span>
                                                            <?php } ?>
                                                        </td>
                                                        <td><?php echo (int)$l['qty']; ?></td>
                                                        <td><?php echo htmlspecialchars($l['reference_type']); ?></td>
                                                    </tr>
                                                <?php } ?>
                                            <?php } else { ?>
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted">
                                                        No stock movement found
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="col-xl-4">

                        <div class="card card-flush shadow-sm">

                            <div class="card-header">
                                <div class="card-title">
                                    <h3 class="fw-bold">
                                        Alteration Timeline
                                    </h3>
                                </div>
                            </div>

                            <div class="card-body border-top p-9">

                                <?php
                                $steps = [
                                    'STOCK_RESERVED'      => 'Stock Reserved',
                                    'SENT_FOR_ALTERATION' => 'Sent For Alteration',
                                    'READY_FOR_RECEIVE'   =>'Ready For Receive',
                                    'RECEIVED'            => 'Received Product',
                                    'QC_APPROVED'         => 'QC Approved',
                                    'COMPLETED'           => 'Completed'
                                ];

                                $status_order = array_keys($steps);
                                $current_index = array_search($status, $status_order);
                                ?>

                                <div class="alt-timeline">

                                    <?php
                                    $i = 1;
                                    foreach ($steps as $key => $label) {

                                        $class = '';

                                        $step_index = array_search($key, $status_order);

                                        if ($status == 'REJECTED') {
                                            $class = '';
                                        } elseif ($step_index < $current_index) {
                                            $class = 'done';
                                        } elseif ($step_index == $current_index) {
                                            $class = 'active';
                                        }
                                    ?>

                                        <div class="alt-step <?php echo $class; ?>">
                                            <div class="alt-step-icon">
                                                <?php echo $i++; ?>
                                            </div>

                                            <div>
                                                <div class="alt-step-title">
                                                    <?php echo $label; ?>
                                                </div>

                                                <div class="alt-step-desc">
                                                    <?php
                                                    if ($key == 'STOCK_RESERVED') {
                                                        echo 'Source stock reserved';
                                                    } elseif ($key == 'SENT_FOR_ALTERATION') {
                                                        echo 'Assigned vendor/person working';
                                                    }else if($key == 'READY_FOR_RECEIVE'){
                                                        echo 'Product Ready For Receive';
                                                    } elseif ($key == 'RECEIVED') {
                                                        echo 'Product received back';
                                                    } elseif ($key == 'QC_APPROVED') {
                                                        echo 'Quality check approved';
                                                    } else {
                                                        echo 'New size stock added';
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>

                                    <?php } ?>

                                </div>

                                <?php if ($status == 'REJECTED') { ?>
                                    <div class="alert alert-danger mt-6">
                                        This alteration request has been rejected and reserved stock has been released.
                                    </div>
                                <?php } ?>

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