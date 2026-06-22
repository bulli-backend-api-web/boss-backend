<?php
include "config/database.php";
include "config/auth_check.php";
include "includes/sidemenu.php";

$return_order_status = [];
$qrys_unit = "SELECT id, name FROM m_order_return_remark WHERE status = 1";
$result_unit = $con->query($qrys_unit);
while ($status_row = $result_unit->fetch_array()) {
    $return_order_status[] = $status_row;
}

$selected_products = [];
$row = [];
$id = 0;
$total_amount = $cod_charge = $deduction1 = $deduction2 = $total_refund_amount = 0;
$image_proof = '';
$explode_image_proof = [];

if ((my_simple_crypt($_GET['id'], 'decrypt_1')) > 0) {
    $id = my_simple_crypt($_GET['id'], 'decrypt_1');

    $select_qury = "SELECT * FROM return_order_inquiry WHERE id = " . intval($id);
    $result = $con->query($select_qury);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $pid = $row['product'];
        $q = $con->query("SELECT id, name FROM product WHERE name = '" . $con->real_escape_string($pid) . "'");
        if ($row1 = $q->fetch_assoc()) {
            $selected_products[] = ["id" => $row1["id"], "text" => $row1["name"]];
        }

        $total_amount        = $row['total_amount'];
        $cod_charge          = $row['cod_charge'];
        $deduction1          = $row['deduction1'];
        $deduction2          = $row['deduction2'];
        $total_refund_amount = $total_amount - $cod_charge - $deduction1 - $deduction2;
        $image_proof         = $row['image_proof'];

        if ($image_proof) {
            $explode_image_proof = json_decode($image_proof,true);
        }
    }
} else {
    header('Location:/');
    exit;
}

// Helper: checked state for radio
function radioChecked(int $current, int $val): string {
    return $current == $val ? 'checked' : '';
}

function radioActive(int $current, int $val): string {
    return $current == $val ? 'active' : '';
}
?>

<style>
    /* ── Page-level overrides ── */
    .section-divider {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 2rem 0 1.25rem;
    }
    .section-divider .sd-icon {
        width: 32px; height: 32px;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 15px; flex-shrink: 0;
    }
    .section-divider .sd-label {
        font-size: 13px; font-weight: 700;
        text-transform: uppercase; letter-spacing: .06em;
        color: var(--bs-gray-600);
    }
    .section-divider::after {
        content: '';
        flex: 1; height: 1px;
        background: var(--bs-border-color);
    }

    /* Read-only field pill */
    .form-control-readonly {
        background: var(--bs-gray-100) !important;
        border-color: transparent !important;
        color: var(--bs-gray-700);
        cursor: default;
    }

    /* Refund summary box */
    .refund-summary {
        background: linear-gradient(135deg, #f0fdf9 0%, #e8f5fe 100%);
        border: 1.5px solid #b2dfdb;
        border-radius: 12px;
        padding: 1.25rem 1.5rem;
    }
    .refund-summary .rs-row {
        display: flex; justify-content: space-between;
        align-items: center;
        padding: 7px 0;
        border-bottom: 1px dashed #b2dfdb;
        font-size: 14px;
    }
    .refund-summary .rs-row:last-child { border-bottom: none; }
    .refund-summary .rs-label { color: #546e7a; }
    .refund-summary .rs-val   { font-weight: 600; color: #1a1a18; }
    .refund-summary .rs-deduct { color: #e53935; }
    .refund-summary .rs-total  {
        font-size: 17px; font-weight: 700; color: #00897b;
    }

    /* Status radio cards */
    .status-radio-card {
        border: 2px solid var(--bs-border-color);
        border-radius: 12px;
        padding: 1rem 1.25rem;
        cursor: pointer;
        transition: all .15s;
        display: flex; align-items: center; gap: 12px;
    }
    .status-radio-card:hover { border-color: var(--bs-primary); }
    .status-radio-card.active { border-color: var(--bs-primary); background: #f0f2ff; }
    .status-radio-card.active-success { border-color: #00897b; background: #f0fdf9; }
    .status-radio-card.active-danger  { border-color: #e53935; background: #fff5f5; }

    .status-radio-card .src-icon {
        width: 36px; height: 36px;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 18px; flex-shrink: 0;
    }

    /* Proof gallery */
    .proof-thumb {
        width: 100px; height: 80px;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid var(--bs-border-color);
        cursor: pointer;
        transition: border-color .15s, transform .15s;
    }
    .proof-thumb:hover { border-color: var(--bs-primary); transform: scale(1.04); }

    .video-preview-wrap {
        position: relative;
        display: inline-block;
        border-radius: 8px;
        overflow: hidden;
        border: 2px solid var(--bs-border-color);
    }
    .video-preview-wrap video { display: block; max-width: 200px; border-radius: 6px; }

    /* Sticky save bar */
    .save-bar {
        position: sticky;
        bottom: 0;
        background: #fff;
        border-top: 1px solid var(--bs-border-color);
        padding: 1rem 1.5rem;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        z-index: 10;
        border-radius: 0 0 12px 12px;
    }
</style>

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
<div class="d-flex flex-column flex-column-fluid">

    <!-- ── Toolbar ── -->
    <div id="kt_app_toolbar" class="app-toolbar pt-7 pt-lg-10">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
            <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">

                    <div class="d-flex align-items-center gap-3">
                        <a href="<?php echo $site_path; ?>/pending-return-order-inquiry"
                           class="btn btn-sm btn-icon btn-light btn-active-light-primary">
                            <i class="ki-outline ki-arrow-left fs-4"></i>
                        </a>
                        <div>
                            <h1 class="page-heading d-flex align-items-center gap-2 text-gray-900 fw-bold fs-3 m-0">
                                <span class="badge badge-light-warning fs-8 fw-semibold">
                                    #<?php echo htmlspecialchars($row['ticket_id']); ?>
                                </span>
                                Return Order Inquiry
                            </h1>
                            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 mt-1">
                                <li class="breadcrumb-item text-muted">
                                    <a href="<?php echo $site_path; ?>/dashboard" class="text-muted text-hover-primary">Home</a>
                                </li>
                                <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                                <li class="breadcrumb-item text-muted">
                                    <a href="<?php echo $site_path; ?>/pending-return-order" class="text-muted text-hover-primary">Return Orders</a>
                                </li>
                                <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                                <li class="breadcrumb-item text-muted">Edit</li>
                            </ul>
                        </div>
                    </div>

                </div>

                <!-- Quick meta badges -->
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <?php
                    $typeLabel = htmlspecialchars($row['request_type'] ?? '—');
                    $typeBadge = strtolower($row['request_type']) === 'return'
                        ? 'badge-light-danger' : 'badge-light-primary';
                    ?>
                    <span class="badge <?php echo $typeBadge; ?> fs-7 fw-semibold px-3 py-2">
                        <i class="ki-outline ki-<?php echo strtolower($row['request_type']) === 'return' ? 'arrow-left' : 'arrows-loop'; ?> fs-7 me-1"></i>
                        <?php echo $typeLabel; ?>
                    </span>
                    <span class="badge badge-light-info fs-7 fw-semibold px-3 py-2">
                        <i class="ki-outline ki-package fs-7 me-1"></i>
                        <?php echo htmlspecialchars($row['order_id'] ?? '—'); ?>
                    </span>
                    <span class="text-muted fs-7">
                        <?php echo date('d M Y', strtotime($row['datee'] ?? 'now')); ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Content ── -->
    <div id="kt_app_content" class="app-content">
    <div id="kt_app_content_container" class="app-container container-fluid">

        <form id="kt_update_return_order_form" action="<?php echo $site_path ?>/ajax/add-update-return-order-inquiry" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="inquiry_id"    value="<?php echo $id; ?>"/>
            <input type="hidden" name="request_from"  value="<?php echo $row['request_from']; ?>"/>
            <input type="hidden" name="h1"            value="1"/>
            <input type="hidden" name="redirect_page" value="<?php echo $site_path; ?>/pending-return-order-inquiry"/>
            <div class="row g-6 align-items-start">
                <div class="col-xl-8">
                    <div class="card card-flush shadow-sm mb-6">
                        <div class="card-header pt-6 pb-0 border-0">
                            <div class="card-title">
                                <span class="card-icon me-3">
                                    <i class="ki-outline ki-profile-circle fs-2 text-primary"></i>
                                </span>
                                <h3 class="card-label fw-bold fs-5 text-gray-800">Customer & Order Info</h3>
                            </div>
                        </div>
                        <div class="card-body pt-5">
                            <div class="row g-5">
                                <div class="col-md-6 fv-row">
                                    <label class="form-label required fw-semibold fs-6">Customer Name</label>
                                    <input type="text" name="name" id="name" class="form-control form-control-solid" placeholder="Full name" value="<?php echo htmlspecialchars($row['name']); ?>" />
                                </div>
                                <div class="col-md-6 fv-row">
                                    <label class="form-label required fw-semibold fs-6">Mobile Number</label>
                                    <div class="input-group input-group-solid">
                                        <span class="input-group-text border-0 bg-transparent pe-0">
                                            <i class="ki-outline ki-phone fs-4 text-gray-500"></i>
                                        </span>
                                        <input type="text" name="mobile" id="mobile" class="form-control form-control-solid" placeholder="10-digit mobile" value="<?php echo htmlspecialchars($row['mobile']); ?>" />
                                    </div>
                                </div>
                                <div class="col-md-6 fv-row">
                                    <label class="form-label fw-semibold fs-6">Order ID</label>
                                    <input type="text" name="order_id" id="order_id" class="form-control form-control-solid form-control-readonly" placeholder="Order ID" value="<?php echo htmlspecialchars($row['order_id']); ?>" readonly />
                                </div>
                                <div class="col-md-6 fv-row">
                                    <label class="form-label fw-semibold fs-6">Ticket ID</label>
                                    <input type="text" name="ticket_id" id="ticket_id" class="form-control form-control-solid form-control-readonly" value="<?php echo htmlspecialchars($row['ticket_id']); ?>" readonly />
                                </div>
                                <div class="col-md-6 fv-row">
                                    <label class="form-label fw-semibold fs-6">Product</label>
                                    <select id="product_id" name="product_id[]" class="form-select form-select-solid" data-control="select2" data-placeholder="Search product" disabled></select>
                                </div>
                                <div class="col-md-6 fv-row">
                                    <label class="form-label fw-semibold fs-6">Return Pcs</label>
                                    <input type="text" name="total_qty" id="total_qty" class="form-control form-control-solid form-control-readonly" value="<?php echo htmlspecialchars($row['total_qty']); ?>" readonly />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ── Card: Request Details ── -->
                    <div class="card card-flush shadow-sm mb-6">
                        <div class="card-header pt-6 pb-0 border-0">
                            <div class="card-title">
                                <span class="card-icon me-3">
                                    <i class="ki-outline ki-switch fs-2 text-warning"></i>
                                </span>
                                <h3 class="card-label fw-bold fs-5 text-gray-800">Request Details</h3>
                            </div>
                        </div>
                        <div class="card-body pt-5">
                            <div class="row g-5">
                                <div class="col-md-6 fv-row">
                                    <label class="form-label fw-semibold fs-6">Request Type</label>
                                    <select name="dto_type" id="dto_type" class="form-select form-select-solid fw-semibold">
                                        <option value="">Select type</option>
                                        <option value="Return"   <?php if (strtolower($row['request_type']) == 'return')   echo 'selected'; ?>>Return</option>
                                        <option value="Exchange" <?php if (strtolower($row['request_type']) == 'exchange') echo 'selected'; ?>>Exchange</option>
                                    </select>
                                </div>
                                <div class="col-md-6 fv-row">
                                    <label class="form-label fw-semibold fs-6">Return Status</label>
                                    <select name="return_status_id" id="return_status_id" data-control="select2" data-placeholder="Select status" class="form-select form-select-solid fw-semibold">
                                        <option value="">Select status</option>
                                        <?php foreach ($return_order_status as $sv): ?>
                                            <option value="<?php echo $sv['id']; ?>"
                                                <?php if ($sv['id'] == $row['return_status_id']) echo 'selected'; ?>>
                                                <?php echo htmlspecialchars($sv['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 fv-row">
                                    <label class="form-label fw-semibold fs-6">Courier Type</label>
                                    <select name="dto_way" id="dto_way" data-control="select2" data-placeholder="Select courier" class="form-select form-select-solid fw-semibold">
                                        <option value="1" <?php if ($row['courier_id'] == 1) echo 'selected'; ?>>Self Courier</option>
                                        <option value="2" <?php if ($row['courier_id'] == 2) echo 'selected'; ?>>BK Arranged Courier</option>
                                    </select>
                                </div>
                                <div class="col-md-6 fv-row">
                                    <label class="form-label fw-semibold fs-6">Courier Name</label>
                                    <input type="text" name="courier_name" id="courier_name" class="form-control form-control-solid" placeholder="Courier company name" value="<?php echo htmlspecialchars($row['courier_name']); ?>" />
                                </div>
                                <div class="col-md-6 fv-row">
                                    <label class="form-label fw-semibold fs-6">AWB Number</label>
                                    <input type="text" name="awb_number" id="awb_number" class="form-control form-control-solid" placeholder="Tracking / AWB number" value="<?php echo htmlspecialchars($row['awb_number']); ?>" />
                                </div>
                                <div class="col-md-6 fv-row">
                                    <label class="form-label fw-semibold fs-6">Admin Remarks</label>
                                    <textarea name="remark" id="remark" class="form-control form-control-solid" rows="2" placeholder="Internal notes"><?php echo htmlspecialchars($row['remark']); ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ── Card: Payment / Refund Details ── -->
                    <div class="card card-flush shadow-sm mb-6">
                        <div class="card-header pt-6 pb-0 border-0">
                            <div class="card-title">
                                <span class="card-icon me-3">
                                    <i class="ki-outline ki-wallet fs-2 text-success"></i>
                                </span>
                                <h3 class="card-label fw-bold fs-5 text-gray-800">Payment & Refund</h3>
                            </div>
                        </div>
                        <div class="card-body pt-5">
                            <div class="row g-5">
                                <div class="col-md-6 fv-row">
                                    <label class="form-label fw-semibold fs-6">Payment Method</label>
                                    <select name="payment_method" id="payment_method" data-control="select2" data-placeholder="Select method" class="form-select form-select-solid fw-semibold">
                                        <option value="1" <?php if ($row['payment_method'] == 1) echo 'selected'; ?>>UPI</option>
                                        <option value="2" <?php if ($row['payment_method'] == 2) echo 'selected'; ?>>Bank Transfer</option>
                                    </select>
                                </div>
                                <div class="col-md-6 fv-row">
                                    <label class="form-label fw-semibold fs-6">UPI ID</label>
                                    <input type="text" name="upi_id" id="upi_id" class="form-control form-control-solid form-control-readonly" placeholder="UPI ID" value="<?php echo htmlspecialchars($row['upi_id']); ?>" readonly />
                                </div>

                                <!-- Divider: Bank details -->
                                <div class="col-12">
                                    <div class="section-divider">
                                        <div class="sd-icon bg-light-primary">
                                            <i class="ki-outline ki-bank fs-5 text-primary"></i>
                                        </div>
                                        <span class="sd-label">Bank Details</span>
                                    </div>
                                </div>

                                <div class="col-md-6 fv-row">
                                    <label class="form-label fw-semibold fs-6">Account Holder Name</label>
                                    <input type="text" name="account_name" id="account_name" class="form-control form-control-solid form-control-readonly" value="<?php echo htmlspecialchars($row['account_name']); ?>" readonly />
                                </div>
                                <div class="col-md-6 fv-row">
                                    <label class="form-label fw-semibold fs-6">Account Number</label>
                                    <input type="text" name="account_number" id="account_number" class="form-control form-control-solid form-control-readonly" value="<?php echo htmlspecialchars($row['account_number']); ?>" readonly />
                                </div>
                                <div class="col-md-6 fv-row">
                                    <label class="form-label fw-semibold fs-6">IFSC Code</label>
                                    <input type="text" name="ifsc_code" id="ifsc_code" class="form-control form-control-solid form-control-readonly" value="<?php echo htmlspecialchars($row['ifsc_code']); ?>" readonly />
                                </div>
                                <div class="col-md-6 fv-row">
                                    <label class="form-label fw-semibold fs-6">Bank Name</label>
                                    <input type="text" name="bank_name" id="bank_name" class="form-control form-control-solid form-control-readonly" value="<?php echo htmlspecialchars($row['bank_name']); ?>" readonly />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ── Card: Proof Media ── -->
                    <div class="card card-flush shadow-sm mb-6">
                        <div class="card-header pt-6 pb-0 border-0">
                            <div class="card-title">
                                <span class="card-icon me-3">
                                    <i class="ki-outline ki-picture fs-2 text-info"></i>
                                </span>
                                <h3 class="card-label fw-bold fs-5 text-gray-800">Proof & Media</h3>
                            </div>
                        </div>
                        <div class="card-body pt-5">
                            <div class="row g-6">

                                <!-- Photo proof -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-6 d-block mb-3">
                                        <i class="ki-outline ki-picture fs-5 text-gray-500 me-2"></i>Photo Proof
                                        <?php if ($explode_image_proof): ?>
                                            <span class="badge badge-light-primary ms-2">
                                                <?php echo count($explode_image_proof); ?> photo<?php echo count($explode_image_proof) > 1 ? 's' : ''; ?>
                                            </span>
                                        <?php endif; ?>
                                    </label>
                                    <?php if ($explode_image_proof): ?>
                                        <div class="d-flex flex-wrap gap-3">
                                            <?php foreach ($explode_image_proof as $si): ?>
                                                <img src="<?php echo $site_path."/".htmlspecialchars($si); ?>"
                                                     class="proof-thumb preview-image"
                                                     alt="Proof photo" />
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-muted fs-7 d-flex align-items-center gap-2 py-3">
                                            <i class="ki-outline ki-information-4 fs-5"></i> No photos uploaded
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Video proof -->
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold fs-6 d-block mb-3">
                                        <i class="ki-outline ki-video fs-5 text-gray-500 me-2"></i>Unboxing Video
                                    </label>
                                    <?php if (!empty($row['unboxing_video'])): ?>
                                        <div class="video-preview-wrap">
                                            <video width="180" controls class="preview-video">
                                                <source src="<?php echo htmlspecialchars($row['unboxing_video']); ?>" type="video/mp4">
                                            </video>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-muted fs-7 d-flex align-items-center gap-2 py-3">
                                            <i class="ki-outline ki-information-4 fs-5"></i> No video uploaded
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- AWB Receipt -->
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold fs-6 d-block mb-3">
                                        <i class="ki-outline ki-document fs-5 text-gray-500 me-2"></i>AWB Receipt
                                    </label>
                                    <?php if (!empty($row['awb_number_receipt'])): ?>
                                        <img src="<?php echo htmlspecialchars($row['awb_number_receipt']); ?>"
                                             class="proof-thumb preview-image"
                                             alt="AWB Receipt" />
                                    <?php else: ?>
                                        <div class="text-muted fs-7 d-flex align-items-center gap-2 py-3">
                                            <i class="ki-outline ki-information-4 fs-5"></i> Not uploaded
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="card card-flush shadow-sm mb-6">
                        <div class="card-header pt-6 pb-0 border-0">
                            <div class="card-title">
                                <span class="card-icon me-3">
                                    <i class="ki-outline ki-calculator fs-2 text-success"></i>
                                </span>
                                <h3 class="card-label fw-bold fs-5 text-gray-800">Refund Summary</h3>
                            </div>
                        </div>
                        <div class="card-body pt-5">
                            <div class="refund-summary">
                                <div class="rs-row">
                                    <span class="rs-label">Total Amount</span>
                                    <span class="rs-val">₹<?php echo number_format($total_amount, 2); ?></span>
                                </div>
                                <div class="rs-row">
                                    <span class="rs-label">COD Charge (A)</span>
                                    <span class="rs-val rs-deduct">− ₹<?php echo number_format($cod_charge, 2); ?></span>
                                </div>
                                <div class="rs-row">
                                    <span class="rs-label">Deduction B</span>
                                    <span class="rs-val rs-deduct">− ₹<?php echo number_format($deduction1, 2); ?></span>
                                </div>
                                <div class="rs-row">
                                    <span class="rs-label">Deduction C</span>
                                    <span class="rs-val rs-deduct">− ₹<?php echo number_format($deduction2, 2); ?></span>
                                </div>
                                <div class="rs-row pt-2">
                                    <span class="rs-label fw-bold">Total Refund</span>
                                    <span class="rs-total">₹<?php echo number_format($total_refund_amount, 2); ?></span>
                                </div>
                            </div>

                            <!-- hidden inputs for form submission -->
                            <input type="hidden" name="total_amount" value="<?php echo $total_amount; ?>"/>
                            <input type="hidden" name="deduction"    value="<?php echo $cod_charge; ?>"/>
                            <input type="hidden" name="deduction1"   value="<?php echo $deduction1; ?>"/>
                            <input type="hidden" name="deduction2"   value="<?php echo $deduction2; ?>"/>
                            <input type="hidden" name="total_refund" value="<?php echo $total_refund_amount; ?>"/>
                        </div>
                    </div>

                    <!-- ── Card: Order Status ── -->
                    <div class="card card-flush shadow-sm mb-6">
                        <div class="card-header pt-6 pb-0 border-0">
                            <div class="card-title">
                                <span class="card-icon me-3">
                                    <i class="ki-outline ki-status fs-2 text-primary"></i>
                                </span>
                                <h3 class="card-label fw-bold fs-5 text-gray-800">Update Status</h3>
                            </div>
                        </div>
                        <div class="card-body pt-5">
                            <div class="d-flex flex-column gap-3">

                                <!-- Pending -->
                                <?php $s = (int)$row['customer_inquiry_status']; ?>
                                <label class="status-radio-card <?php echo $s == 1 ? 'active' : ''; ?>" data-kt-button="true">
                                    <input class="form-check-input flex-shrink-0 mt-0" type="radio" name="customer_inquiry_status" value="1" <?php echo radioChecked($s, 1); ?> />
                                    <div class="src-icon bg-light-warning">
                                        <i class="ki-outline ki-time fs-4 text-warning"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-gray-800 fs-6">Pending</div>
                                        <div class="text-muted fs-7">Awaiting review</div>
                                    </div>
                                </label>

                                <!-- Accepted -->
                                <label class="status-radio-card <?php echo $s == 2 ? 'active-success' : ''; ?>" data-kt-button="true">
                                    <input class="form-check-input flex-shrink-0 mt-0" type="radio" name="customer_inquiry_status" value="2" <?php echo radioChecked($s, 2); ?> />
                                    <div class="src-icon bg-light-success">
                                        <i class="ki-outline ki-check-circle fs-4 text-success"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-gray-800 fs-6">Accepted</div>
                                        <div class="text-muted fs-7">Refund will be processed</div>
                                    </div>
                                </label>

                                <!-- Rejected -->
                                <label class="status-radio-card <?php echo $s == 3 ? 'active-danger' : ''; ?>" data-kt-button="true">
                                    <input class="form-check-input flex-shrink-0 mt-0" type="radio" name="customer_inquiry_status" value="3" <?php echo radioChecked($s, 3); ?> />
                                    <div class="src-icon bg-light-danger">
                                        <i class="ki-outline ki-cross-circle fs-4 text-danger"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-gray-800 fs-6">Rejected</div>
                                        <div class="text-muted fs-7">Request will be declined</div>
                                    </div>
                                </label>

                            </div>

                            <!-- Reject reason (shown when Rejected selected) -->
                            <div id="rejectReasonDiv" class="mt-5 <?php echo $s == 3 ? '' : 'd-none'; ?> fv-row">
                                <label class="form-label required fw-semibold fs-6 text-danger">
                                    <i class="ki-outline ki-information fs-5 me-1 text-danger"></i>
                                    Rejection Reason
                                </label>
                                <textarea name="reject_reason" id="reject_reason" class="form-control form-control-solid border-danger" rows="3" placeholder="Explain why this request is being rejected..."><?php echo htmlspecialchars($row['reject_reason'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- ── Card: Save Actions ── -->
                    <div class="card card-flush shadow-sm">
                        <div class="card-body py-5">
                            <div class="d-grid gap-3">
                                <button type="submit" class="btn btn-primary"
                                        id="kt_update_return_order_details_submit">
                                    <span class="indicator-label">
                                        <i class="ki-outline ki-check fs-4 me-1"></i>
                                        Save Changes
                                    </span>
                                    <span class="indicator-progress">
                                        Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                    </span>
                                </button>
                                <button type="reset" class="btn btn-light btn-active-light-primary">
                                    <i class="ki-outline ki-arrows-circle fs-4 me-1"></i>
                                    Discard Changes
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    </div>

</div>
    <div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center pt-0" id="modalContent"></div>
        </div>
    </div>
</div>
    <!-- ── Image / Video Preview Modal ── -->


<?php include("includes/footer.php"); ?>
    </div>
</div>



<script src="<?php echo $site_path; ?>/assets/plugins/global/plugins.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/scripts.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/plugins/custom/datatables/datatables.bundle.js"></script>

<script>
"use strict";

/* ── Status radio: toggle active classes + reject reason ── */
$(document).on('change', 'input[name="customer_inquiry_status"]', function () {
    // Reset all card highlights
    $('.status-radio-card').removeClass('active active-success active-danger');

    const val = parseInt($(this).val());
    const classMap = { 1: 'active', 2: 'active-success', 3: 'active-danger' };
    $(this).closest('.status-radio-card').addClass(classMap[val] || '');

    // Reject reason
    $('#rejectReasonDiv').toggleClass('d-none', val !== 3);
});

/* ── Image / Video preview modal ── */
$(document).on('click', '.preview-image', function () {
    $('#modalContent').html('<img src="' + $(this).attr('src') + '" class="img-fluid rounded">');
    $('#previewModal').modal('show');
});

$(document).on('click', '.preview-video', function () {
    const src = $(this).find('source').attr('src');
    $('#modalContent').html(
        '<video controls autoplay class="w-100 rounded">' +
        '<source src="' + src + '" type="video/mp4"></video>'
    );
    $('#previewModal').modal('show');
});

$('#previewModal').on('hidden.bs.modal', function () {
    $('#modalContent').html('');
});

/* ── Product Select2 (read-only preloaded) ── */
$(document).ready(function () {
    var preselected = <?php echo json_encode($selected_products); ?>;

    preselected.forEach(function (item) {
        $('#product_id').append(new Option(item.text, item.id, true, true));
    });

    $('#product_id').trigger('change');

    $('#product_id').select2({
        ajax: {
            url: "<?php echo $site_path ?>/ajax/ajax-search-product",
            type: "POST",
            dataType: "json",
            delay: 250,
            data: function (params) { return { search: params.term }; },
            processResults: function (data) {
                return {
                    results: $.map(data, function (item) {
                        return { id: item.id, text: item.text };
                    })
                };
            },
            cache: true
        },
        placeholder: "Search by name",
        minimumInputLength: 2,
        allowClear: true,
        width: '100%'
    });
});

/* ── Form validation + AJAX submit ── */
var KTUpdateReturnOrderInquiry = function () {
    var form, validator, submitButton;

    return {
        init: function () {
            form         = document.getElementById('kt_update_return_order_form');
            submitButton = document.getElementById('kt_update_return_order_details_submit');

            validator = FormValidation.formValidation(form, {
                fields: {
                    name: {
                        validators: {
                            notEmpty: { message: 'Customer name is required' }
                        }
                    },
                    mobile: {
                        validators: {
                            notEmpty:  { message: 'Mobile is required' },
                            stringLength: { min: 10, max: 10, message: 'Enter a valid 10-digit number' },
                            digits:    { message: 'Mobile must contain only digits' }
                        }
                    },
                    reject_reason: {
                        validators: {
                            callback: {
                                message: 'Please provide a rejection reason',
                                callback: function (input) {
                                    var status = $('input[name="customer_inquiry_status"]:checked').val();
                                    return !(status == 3 && input.value.trim() === '');
                                }
                            }
                        }
                    }
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap5: new FormValidation.plugins.Bootstrap5({
                        rowSelector: '.fv-row',
                        eleInvalidClass: '',
                        eleValidClass: ''
                    }),
                    submitButton: new FormValidation.plugins.SubmitButton()
                }
            });

            submitButton.addEventListener('click', function (e) {
                e.preventDefault();

                validator.validate().then(function (status) {
                    if (status !== 'Valid') {
                        Swal.fire({
                            text: 'Please fix the errors in the form.',
                            icon: 'error',
                            buttonsStyling: false,
                            confirmButtonText: 'Got it',
                            customClass: { confirmButton: 'btn btn-primary' }
                        });
                        return;
                    }

                    submitButton.setAttribute('data-kt-indicator', 'on');
                    submitButton.disabled = true;

                    var fd = new FormData(form);
                    fd.append('action', 'update-return-order-inquiry');

                    $.ajax({
                        url: form.getAttribute('action'),
                        type: 'POST',
                        data: fd,
                        processData: false,
                        contentType: false,
                        dataType: 'json',

                        success: function (response) {
                            submitButton.removeAttribute('data-kt-indicator');
                            submitButton.disabled = false;

                            if (response.status === 'success') {
                                Swal.fire({
                                    text: response.message,
                                    icon: 'success',
                                    buttonsStyling: false,
                                    confirmButtonText: 'Done',
                                    customClass: { confirmButton: 'btn btn-primary' }
                                }).then(function () {
                                    window.location.href = response.redirect_page;
                                });
                            } else {
                                Swal.fire({
                                    text: response.message || 'Something went wrong.',
                                    icon: 'error',
                                    buttonsStyling: false,
                                    confirmButtonText: 'Got it',
                                    customClass: { confirmButton: 'btn btn-primary' }
                                });
                            }
                        },

                        error: function () {
                            submitButton.removeAttribute('data-kt-indicator');
                            submitButton.disabled = false;
                            Swal.fire({
                                text: 'Network error. Please try again.',
                                icon: 'error',
                                buttonsStyling: false,
                                confirmButtonText: 'Got it',
                                customClass: { confirmButton: 'btn btn-danger' }
                            });
                        }
                    });
                });
            });
        }
    };
}();

KTUtil.onDOMContentLoaded(function () {
    KTUpdateReturnOrderInquiry.init();
});
</script>
</body>
</html>