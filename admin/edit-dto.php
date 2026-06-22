<?php
include "config/database.php";
include "config/auth_check.php";
include "includes/sidemenu.php";

$reason_qry = "SELECT id,name,status from m_order_return_remark";
$reasonRes = $con->query($reason_qry);
$reasonArray = [];
if ($reasonRes && $reasonRes->num_rows > 0) {
    while ($reason_row = $reasonRes->fetch_assoc()) {
        $reasonArray[] = $reason_row;
    }
}
$courierArray = [];

if ((my_simple_crypt($_GET['id'], 'decrypt_1')) > 0) {
    $id = my_simple_crypt($_GET['id'], 'decrypt_1');
    $select_qury = "SELECT * from dto_orders where id=" . $id;
    $result = $sq1 = $con->query($select_qury);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $ticket_id = $row['ticket_id'];
        $order_id = $row['order_id'];
        $customer_name = $row['customer_name'];
        $mobile_number = $row['mobile_number'];
        $courier = $row['courier'];
        $tracking_number = $row['tracking_number'];
        $rev_pickup_status = $row['rev_pickup_status'];
        $qty = $row['qty'];
        $amount = $row['amount'];
        $deduction = $row['deduction'];
        $deduction1 = $row['deduction1'];
        $deduction2 = $row['deduction2'];
        $final_amount = $row['final_amount'];
        $reason = $row['reason'];
        $gpay_number = $row['gpay_number'];
        $order_from = $row['order_from'];
        $dto_way = $row['dto_way'];
        $gpay_name = $row['gpay_name'];

        $dto_type = $row['dto_type'];
        $exchange_deduction = !empty($row['exchange_deduction']) ? $row['exchange_deduction'] :$deduction ;
        $extra_paid_customer = $row['extra_paid_customer'];
        $damage_incorrect_proof = $row['damage_incorrect_proof'];
        $exchange_courier_recv_image = $row['exchange_courier_recv_image'];
        $courier_received_image = $row['courier_received_image'];
        $dispatch_saree_pics = $row['dispatch_saree_pics'];
        $saree_sku = $row['saree_sku'];
        $dispatch_order_id = $row['dispatch_order_id'];
        $dispatch_tracking_number = $row['dispatch_tracking_number'];
        $new_item_price  = $row['new_item_price'];
        $bank_id  = $row['bank_id'];
        $damage_proof  = $row['damage_proof'];
        $client_damage_image  = $row['client_damage_image'];
        if($client_damage_image){
            $explode_image_proof = json_decode($client_damage_image,true);
        }
        $unboxing_video  = $row['unboxing_video'];
    }
} else {
    header('Location:/');
}
?>
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_toolbar" class="app-toolbar pt-7 pt-lg-10">
            <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
                <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                    <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                        <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">Update DTO Orders</h1>
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <a href="<?php echo $site_path; ?>/dashboard" class="text-muted text-hover-primary">Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted">Orders</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div id="kt_app_content" class="app-content">
            <div id="kt_app_content_container" class="app-container container-fluid">
                <form id="kt_update_dto_order_form" action="<?php echo $site_path ?>/ajax/add-update-dto-details" class="form" method="POST" enctype="multipart/form-data">
                    <!--begin::Card body-->
                    <input type="hidden" name="dto_id" id="dto_id" value="<?php echo $id; ?>"/>
                    <input name="redirect_page" type="hidden" id="redirect_page" value="<?=$_SERVER['HTTP_REFERER'];?>" />
                    <div class="card-body border-top p-9">
                        <div class="row mb-6">
                            <div class="col-lg-6 fv-row">
                                <label class="form-label required fw-semibold fs-6">DTO Type</label>
                                <select name="dto_type" id="dto_type" aria-label="Select DTO Type" data-control="select2" data-placeholder="Select DTO Type" class="form-select form-select-solid form-select-lg fw-semibold">
                                    <option value="">Select DTO Type</option>
                                    <option value="Return" <?php if ($dto_type == 'Return') {echo 'selected';} ?>>Return</option>
                                    <option value="Exchange" <?php if ($dto_type == 'Exchange') {echo 'selected';} ?>>Exchange</option>
                                </select>
                            </div>
                            <div class="col-lg-6 fv-row">
                                <label class="form-label fw-semibold fs-6">Order From</label>
                                <select name="order_from" id="order_from" aria-label="Select Order From" data-control="select2" data-placeholder="Select Order From" class="form-select form-select-solid form-select-lg fw-semibold">
                                    <option value="Shopify" <?php if ($order_from == 'Bk') {echo 'seelcted';} ?>>Shopify</option>
                                    <option value="Admin" <?php if ($order_from == 'U3K') {echo 'selected';} ?>>Admin</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <div class="col-lg-6 fv-row">
                                <label class="form-label fw-semibold fs-6">DTO Way</label>
                                <select name="dto_way" id="dto_way" aria-label="Select DTo Way" data-control="select2" data-placeholder="Select DTo Way" class="form-select form-select-solid form-select-lg fw-semibold">
                                    <option value="1" <?php if ($dto_way == 1) {echo 'seelcted';} ?>>Self Courier</option>
                                    <option value="2" <?php if ($dto_way == 2) {echo 'seelcted';} ?>>BK Arranged Courier</option>
                                </select>
                            </div>
                            <div class="col-lg-6 fv-row">
                                <label class="form-label required">Ticket ID</label>
                                <input type="text" name="ticket_id" id="ticket_id" class="form-control form-control-lg form-control-solid" placeholder="Ticket Number" value="<?php echo $ticket_id; ?>" />
                            </div>

                        </div>
                        <div class="row mb-6">
                            <div class="col-lg-6 fv-row">
                                <label class="form-label required fw-semibold fs-6">Order Number</label>
                                <input type="text" name="order_number" id="order_number" class="form-control form-control-lg form-control-solid" placeholder="Enter Order Number" value="<?php echo $order_id; ?>" />
                                <input name="h1" type="hidden" id="h1" value="1" />
                                <input name="hiddenId" type="hidden" id="hiddenId" value="<?php echo $id; ?>" />
                            </div>
                            <div class="col-lg-6 fv-row">
                                <label class="form-label required fw-semibold fs-6">Customer Name</label>
                                <input type="text" name="customer_name" id="customer_name" class="form-control form-control-lg form-control-solid" placeholder="Enter Order Number" value="<?php echo $customer_name; ?>" />
                            </div>

                        </div>
                        <div class="row mb-6">
                            <div class="col-lg-6 fv-row">
                                <label class="form-label fw-semibold fs-6">Mobile Number</label>
                                <input type="text" name="mobile_number" id="mobile_number" class="form-control form-control-lg form-control-solid" value="<?php echo $mobile_number; ?>"/>
                            </div>
                            <div class="col-lg-6 fv-row">
                                <label class="form-label required fw-semibold fs-6">Return PCs</label>
                                <input type="text" name="return_pcs" id="return_pcs" class="form-control form-control-lg form-control-solid" placeholder="Qty" value="<?php echo $qty; ?>"/>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <div class="col-lg-6 fv-row">
                                <label class="form-label fw-semibold fs-6">Courier Name</label>
                                <select name="courier_name" id="courier_name" aria-label="Select Courier" data-control="select2" data-placeholder="Select Courier" class="form-select form-select-solid form-select-lg fw-semibold">
                                    <option value="">Select Reason</option>
                                        <?php if ($courierArray) {
                                            foreach ($courierArray as $single_courier) {
                                                ?>
                                                <option value="<?php echo $single_courier['courier_name']; ?>" <?php if ($single_courier['courier_name'] == $row['courier']) {
                                                    echo 'selected';
                                                } ?>><?php echo $single_courier['courier_name']; ?></option>
                                            <?php }
                                        }
                                        ?>
                                </select>
                            </div>
                            <div class="col-lg-6 fv-row">
                                <label class="form-label required fw-semibold fs-6">Tracking Number</label>
                                <input type="text" name="tracking_number" id="tracking_number" class="form-control form-control-lg form-control-solid" value="<?php echo $tracking_number; ?>"/>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <div class="col-lg-6 fv-row">
                                <label class="form-label fw-semibold fs-6">Reverse Pickup Status</label>
                                <select name="rev_pickup_status" id="rev_pickup_status" aria-label="Rev.Pickup Status" data-control="select2" data-placeholder="Rev.Pickup Status" class="form-select form-select-solid form-select-lg fw-semibold">
                                    <option value="Pending" <?php if ($rev_pickup_status == 'Pending') {echo 'selected';} ?>>Pending</option>
                                    <option value="InTransist" <?php if ($rev_pickup_status == 'InTransist') {echo 'selected';} ?>>InTransist</option>
                                    <option value="Delivered" <?php if ($rev_pickup_status == 'Delivered') {echo 'selected';} ?>>Delivered</option>
                                </select>
                            </div>
                            <div class="col-lg-6 fv-row">
                                <label class="form-label fw-semibold fs-6">Reason</label>
                                <select name="reason" id="reason" aria-label="Select a Reason" data-control="select2" data-placeholder="Select a Reason" class="form-select form-select-solid form-select-lg fw-semibold">
                                    <option value="">Select Reason</option>
<?php if ($reasonArray) {
                                            foreach ($reasonArray as $single_reason) {?>
                                                <option value="<?php echo $single_reason['name']; ?>" <?php if ($single_reason['name'] == $row['reason']) {echo 'selected';} ?>><?php echo $single_reason['name']; ?></option>
                                            <?php } } ?>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-6">
                            <div class="col-lg-6 fv-row">
                                <label class="form-label">Total Amount</label>
                                <input type="text" name="total_amount" id="total_amount" class="form-control form-control-lg form-control-solid" value="<?php echo $amount; ?>"/>
                            </div>
                            <div class="col-lg-6 fv-row">
                                <label class="form-label">New Item Price:</label>
                                <input name="new_item_price" id="new_item_price" type="text" class="form-control form-control-lg form-control-solid" value="<?php echo $new_item_price; ?>"/>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <div class="col-lg-6 fv-row">
                                <label class="form-label">Deduction</label>
                                <input type="text" name="exchange_deduction" id="exchange_deduction" class="form-control form-control-lg form-control-solid" value="<?php echo $exchange_deduction; ?>"/>
                            </div>
                            <div class="col-lg-6 fv-row">
                                <label class="form-label">Deduction Operation (A) Charges</label>
                                <input type="text" name="deduction" id="deduction" class="form-control form-control-lg form-control-solid deduction" value="<?php echo $deduction; ?>"/>
                            </div>
                            <div class="col-lg-6 fv-row">
                                <label class="form-label">Extra Paid By Customer</label>
                                <input type="text" name="extra_paid_customer" id="extra_paid_customer" class="form-control form-control-lg form-control-solid" value="<?php echo $extra_paid_customer; ?>"/>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <div class="col-lg-4 fv-row">
                                <label class="form-label">Damage Proof</label>
                                <?php if($damage_proof){?>
                                         
                                <div class="file-preview-frame" style="display:flex; gap:10px; flex-wrap:wrap;">
                                    
                                    <img height="100" width="200" src="<?=$damage_proof;?>" class="file-preview-image preview-image" style="cursor:pointer;">
                                </div>
                                <?php } ?>
                                <input type="hidden" name="damage_incorrect_proof_hdden" value="<?php echo $explode_image_proof ?>"/>
                            </div>
                            <div class="col-lg-4 fv-row">
                                <label class="form-label">Client Damage Proof</label>
                                <?php if($explode_image_proof){?>
                                         
                                <div class="file-preview-frame" style="display:flex; gap:10px; flex-wrap:wrap;">
                                    <?php foreach($explode_image_proof as $single_image){?>
                                    <img height="100" width="200" src="<?=$site_path."/".$single_image;?>" class="file-preview-image preview-image" style="cursor:pointer;">
                                    <?php }?>
                                </div>
                                <?php } ?>
                                <input type="hidden" name="damage_incorrect_proof_hdden" value="<?php echo $explode_image_proof ?>"/>
                            </div>
                            <div class="col-lg-4 fv-row">
                                <label class="form-label">Unboxing Vide</label>
                                <div class="file-preview-frame">
                                    <video width="200" controls class="preview-video">
                                        <source src="<?php echo $row['unboxing_video']; ?>" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                </div>
                                <input type="hidden" name="courier_received_image_hdden" value="<?php echo $courier_received_image ?>"/>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <div class="col-lg-6 fv-row">
                                <label class="form-label">Damage / Incorrect Proof</label>
                                <input type="file" name="damage_incorrect_proof" id="damage_incorrect_proof" class="form-control form-control-lg form-control-solid" value="<?php echo $deduction; ?>"/>
<?php
if ($damage_incorrect_proof) {
    $ext = strtolower(pathinfo($damage_incorrect_proof, PATHINFO_EXTENSION));
    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
        echo "<img src='$damage_incorrect_proof' class='thumbnail' height='100' width='100'/>";
    } elseif (in_array($ext, ['mp4', 'webm', 'ogg'])) {
        echo "<video width='200' height='150' controls>
                                                    <source src='$damage_incorrect_proof' type='video/$ext'>
                                                    Your browser does not support the video tag.
                                                  </video>";
    }
}
?>

                                <input type="hidden" name="damage_incorrect_proof_hdden" value="<?php echo $damage_incorrect_proof ?>"/>
                            </div>
                            <div class="col-lg-6 fv-row">
                                <label class="form-label">Courier Received Image</label>
                                <input type="file" name="courier_received_image" id="courier_received_image" class="form-control form-control-lg form-control-solid" value="<?php echo $extra_paid_customer; ?>"/>
<?php if ($courier_received_image) { ?>
                                    <img src="<?php echo $courier_received_image; ?>" class="thumbnail" height="100" width="100"/>
<?php } ?>
                                <input type="hidden" name="courier_received_image_hdden" value="<?php echo $courier_received_image ?>"/>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <div class="col-lg-6 fv-row">
                                <label class="form-label">Dispatch Saree Pic's</label>
                                <input type="file" name="dispatch_saree_pics" id="dispatch_saree_pics" class="form-control form-control-lg form-control-solid" value="<?php echo $deduction; ?>"/>
<?php if ($dispatch_saree_pics) { ?>
                                    <img src="<?php echo $dispatch_saree_pics; ?>" class="thumbnail" height="100" width="100"/>
<?php } ?>
                                <input type="hidden" name="dispatch_saree_pics_hdden" value="<?php echo $dispatch_saree_pics ?>"/>
                            </div>
                            <div class="col-lg-6 fv-row">
                                <label class="form-label">Dispatch Saree SKU</label>
                                <input type="text" name="saree_sku" id="saree_sku" class="form-control form-control-lg form-control-solid" value="<?php echo $saree_sku; ?>"/>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <div class="col-lg-6 fv-row">
                                <label class="form-label">Dispatch ORDER ID</label>
                                <input type="text" name="dispatch_order_id" id="dispatch_order_id" class="form-control form-control-lg form-control-solid" value="<?php echo $dispatch_order_id; ?>"/>
                            </div>
                            <div class="col-lg-6 fv-row">
                                <label class="form-label">Dispatch Tracking No.</label>
                                <input type="text" name="dispatch_tracking_number" id="dispatch_tracking_number" class="form-control form-control-lg form-control-solid" value="<?php echo $dispatch_tracking_number; ?>"/>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <div class="col-lg-6 fv-row">
                                <label class="form-label">Deduction (B) COD Charge</label>
                                <input type="text" name="deduction1" id="deduction1" class="form-control form-control-lg form-control-solid" value="<?php echo $deduction1; ?>"/>
                            </div>
                            <div class="col-lg-6 fv-row">
                                <label class="form-label">Deduction (C) Other</label>
                                <input type="text" name="deduction2" id="deduction2" class="form-control form-control-lg form-control-solid" value="<?php echo $deduction2; ?>"/>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <div class="col-lg-6 fv-row">
                                <label class="form-label">Refund Amount</label>
                                <input type="text" name="refund_amount" id="refund_amount" class="form-control form-control-lg form-control-solid" value="<?php echo $final_amount; ?>"/>
                            </div>
                            <div class="col-lg-3 fv-row">
                                <label class="form-label">Gpay Number</label>
                                <input type="text" name="gpay_number" id="gpay_number" class="form-control form-control-lg form-control-solid" value="<?php echo $gpay_number; ?>"/>
                            </div>
                            <div class="col-lg-3 fv-row">
                                <label class="form-label">Gpay Name</label>
                                <input type="text" name="gpay_name" id="gpay_name" class="form-control form-control-lg form-control-solid" value="<?php echo $gpay_name; ?>"/>
                            </div>
                            <input type="hidden" id="is_verified" name="is_verified" value="0">
                            <span id="status"></span>
                        </div>
                        <div class="row mb-6">
                            <div class="col-lg-6 fv-row">
                                <label class="form-label">Account Holder Name</label>
                                <input type="text" name="account_holder_name" id="account_holder_name" placeholder="Account Name" readonly class="form-control form-control-lg form-control-solid" value="<?php echo $row['account_name']; ?>"/>
                    </div>
                            <div class="col-lg-6 fv-row">
                                <label class="form-label">Account Number</label>
                                <input type="text" name="account_number" id="account_number" placeholder="Account Number" readonly class="form-control form-control-lg form-control-solid" value="<?php echo $row['account_number']; ?>"/>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <div class="col-lg-4 fv-row">
                                <label class="form-label">IFSC Code</label>
                                <input type="text" name="ifsc_code" id="ifsc_code" placeholder="IFSC Code" readonly class="form-control form-control-lg form-control-solid" value="<?php echo $row['ifsc_code']; ?>"/>
                            </div>
                            <div class="col-lg-4 fv-row">
                                <label class="form-label">Bank Name</label>
                                <input type="text" name="bank_name" id="bank_name" placeholder="Bank Name" readonly class="form-control form-control-lg form-control-solid" value="<?php echo $row['bank_name']; ?>"/>
                            </div>
                            <div class="col-lg-4 fv-row">
                                <label class="form-label">UPI</label>
                                <input type="text" name="upi_id" id="upi_id" placeholder="UPI ID" readonly class="form-control form-control-lg form-control-solid" value="<?php echo $row['upi_id']; ?>"/>
                            </div>
                        </div>
                    </div>
                    <!--begin::Card footer-->
                    <div class="card-footer d-flex justify-content-end py-6 px-9">
                        <button type="reset" class="btn btn-light btn-active-light-primary me-2">Discard</button>
                        <button type="submit" class="btn btn-primary" id="kt_dto_details_submit">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<div id="kt_app_footer" class="app-footer">
    <div class="app-container container-fluid d-flex flex-column flex-md-row flex-center flex-md-stack py-3">
        <div class="text-gray-900 order-2 order-md-1">
            <span class="text-muted fw-semibold me-1"><?php
                echo date("Y");?>©</span>
            <a href="https://vastranand.in" target="_blank" class="text-gray-800 text-hover-primary"> vastranand. All Rights Reserved.Powered by Vastranand Pvt Ltd.</a>
        </div>
    </div>
</div>
</div>

</div>

</div>
<script>var hostUrl = "assets/";</script>
<script src="<?php echo $site_path; ?>/assets/plugins/global/plugins.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/scripts.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/custom/update-dto.js?v=<?php echo time(); ?>"></script>
<script src="<?php echo $site_path; ?>/assets/js/widgets.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/custom/widgets.js"></script>
<script>
    $(document).ready(function () {
        $('#order_number').on('blur', function () {
            var orderNo = $(this).val().trim();
            if (orderNo.length === 0)
                return;

            $.ajax({
                url: 'master/ajax/get-order-details.php',
                type: 'POST',
                data: {order_number: orderNo},
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        $('#customer_name').val(response.data.customer_name);
                        $('#mobile_number').val(response.data.mobile_number);
                        $('#return_pcs').val(response.data.return_pcs);
                        $('#courier_name').val(response.data.courier_name);
                        $('#tracking_number').val(response.data.tracking_number);
                        $('#total_amount').val(response.data.total_amount);
                        // Leave deduction & refund_amount empty
                    } else {
                        alert("Order not found");
                    }
                },
                error: function () {
                    alert("Failed to fetch order details.");
                }
            });
        });
    });

    $('#deduction,#deduction1,#deductio2').on('keypress', function (e) {
        if (e.which < 48 || e.which > 57) {
            e.preventDefault(); // Stop non-numeric keys
        }
    });

    function calculateRefund() {
        let totalPrice = parseFloat($('#total_amount').val()) || 0;
        let totalDeduction = 0;

        $('.deduction').each(function () {
            totalDeduction += parseFloat($(this).val()) || 0;
        });

        let refund = totalPrice - totalDeduction;
        $('#refund_amount').val(refund.toFixed(2)); // formats to 2 decimal places
    }

    $('.deduction').on('input', calculateRefund);

    $("#dto_way").on('change', function () {
        var dto_way = $(this).val();
        if (dto_way == 1) {
            $("#deduction").val(0);
            $("#deduction").prop('disabled', true);
        } else {
            $("#deduction").val(150);
            $("#deduction").prop('disabled', false);
        }
    });

    $("#dto_way").trigger('change');

    function checkVerification() {
        let mobile = $('#mobile_number').val().trim();
        let gpay = $('#gpay_number').val().trim();

        if (mobile !== '' && gpay !== '') {
            if (mobile === gpay) {
                $('#status').text('Verified').css('color', 'green');
                $('#is_verified').val('1');
            } else {
                $('#status').text('Unverified').css('color', 'red');
                $('#is_verified').val('0');
            }
        } else {
            $('#status').text('').css('color', '');
            $('#is_verified').val('0');
        }
    }
    $('#gpay_number').on('input', checkVerification);
    $(document).ready(function () {
        $("#dto_type").trigger('change');
    });
    $("#dto_type").on('change', function () {
        var dto_type = $(this).val();
        if (dto_type == 'Exchange') {
            $("#deduction").closest(".fv-row").hide();
            $("#deduction1").closest(".fv-row").hide();
            $("#deduction2").closest(".fv-row").hide();
            $("#refund_amount").closest(".fv-row").hide();
            $("#gpay_number").closest(".fv-row").hide();
            $("#gpay_name").closest(".fv-row").hide();

            $("#extra_paid_customer").closest(".fv-row").show();
            $("#damage_incorrect_proof").closest(".fv-row").show();
            $("#courier_received_image").closest(".fv-row").show();
            $("#dispatch_saree_pics").closest(".fv-row").show();
            $("#saree_sku").closest(".fv-row").show();
            $("#dispatch_order_id").closest(".fv-row").show();
            $("#dispatch_tracking_number").closest(".fv-row").show();
            $("#new_item_price").closest(".fv-row").show();
        } else {
            $("#deduction").closest(".fv-row").show();
            $("#deduction1").closest(".fv-row").show();
            $("#deduction2").closest(".fv-row").show();
            $("#refund_amount").closest(".fv-row").show();
            $("#gpay_number").closest(".fv-row").show();
            $("#gpay_name").closest(".fv-row").show();

            $("#extra_paid_customer").closest(".fv-row").hide();
            $("#damage_incorrect_proof").closest(".fv-row").hide();
            $("#courier_received_image").closest(".fv-row").hide();
            $("#dispatch_saree_pics").closest(".fv-row").hide();
            $("#saree_sku").closest(".fv-row").hide();
            $("#dispatch_order_id").closest(".fv-row").hide();
            $("#dispatch_tracking_number").closest(".fv-row").hide();
            $("#new_item_price").closest(".fv-row").hide();
        }
    });

</script>
</body>
</html>