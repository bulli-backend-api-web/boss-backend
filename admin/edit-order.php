<?php
include "config/database.php";
include "config/auth_check.php";
include "includes/sidemenu.php";

error_reporting(E_ALL);
ini_set('display_errors',1);

$state_list = getAllStateList();
$order_extra_status = getOrderExtraStatus();

$order_id = my_simple_crypt($_GET["id"], "decrypt_1");
$qry1 = "SELECT * from shopify_order where order_id = '$order_id'";

$result1 = $con->query($qry1);
$rows = $result1->fetch_array();
$reseller_name = $reseller_id =  '';


$status = $rows["status"];
if($status == 1){
    $statuss = "<span class=\"label label-warning\">Pending</span>";
}else if($status == 2) {
    $statuss = "<span class=\"label label-success\">Confirm</span>";
}else if($status == 3) {
    $statuss = "<span class=\"label label-primary\">Ready For Dispatch</span>";
}

if($rows['payment_method'] == 1){
    $payment_method = 'Prepaid';
}else {
    $payment_method = 'Cod';
}


if($rows['order_from'] == 1){
    $order_create_username = "Shopify";
}else {
    $order_create_username = "U3K";
}
$verifyBy = $verify_time = "";
  

?>
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
   <div class="d-flex flex-column flex-column-fluid">
      <div id="kt_app_toolbar" class="app-toolbar pt-7 pt-lg-10">
         <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
            <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
               <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                  <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">Manage Order Details</h1>
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
      <!--end::Toolbar-->
      <!--begin::Content-->
      <div id="kt_app_content" class="app-content">
         <!--begin::Content container-->

         <div id="kt_app_content_container" class="app-container container-fluid">
            <!-- Manage Order Details -->
            <div class="card mb-5 mb-xl-10">
               <!--begin::Card header-->
            </div>
            
            <div class="card mb-7">
                <div class="card-header">
                    <div class="card-title">
                        <h3 class="fw-bold">Order Information</h3>
                    </div>
                </div>

                <div class="card-body">

                    <div class="row g-5">

                        <div class="col-md-3">
                            <div class="border rounded p-5">
                                <div class="text-muted fs-7">Order ID</div>
                                <div class="fw-bold fs-5">
                                    <?= $rows['order_id'] ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="border rounded p-5">
                                <div class="text-muted fs-7">Invoice No</div>
                                <div class="fw-bold fs-5">
                                    <?= $rows['order_id'] ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="border rounded p-5">
                                <div class="text-muted fs-7">Order Date</div>
                                <div class="fw-bold">
                                    <?= date('d M Y H:i',strtotime($rows['order_date'])) ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="border rounded p-5">
                                <div class="text-muted fs-7">Weight</div>
                                <div class="fw-bold fs-5">
                                    <?= $rows['total_weight'] ?> Gm
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="separator my-7"></div>

                    <div class="row">

                        <div class="col-md-4">
                            <strong>Order From:</strong>
                            <?= $order_create_username; ?>
                        </div>

                        <div class="col-md-4">
                            <strong>Payment Method:</strong>
                            <?= $payment_method; ?>
                        </div>

                        <div class="col-md-4">
                            <strong>Status:</strong>

                            <span>
                                <?= $statuss; ?>
                            </span>
                        </div>

                    </div>

                </div>
            </div>
            
            <div class="card mb-7">
                <div class="card-header">
                    <div class="card-title">
                        <h3>Order Products</h3>
                    </div>
                </div>

                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-row-bordered table-row-dashed align-middle">

                            <thead>
                                <tr class="fw-bold text-gray-700">
                                    <th>#</th>
                                    <th>Image</th>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th>Size</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>

                            <tbody>
                                    <?php
                                    $total_product_qty = 0;
                                    $qry_product_order = "SELECT shopify_order_product.*,product.img1 from shopify_order_product JOIN product on product.id = shopify_order_product.product_id  where orderr_id = '$order_id'";
                                    $result_product_order = $con->query($qry_product_order);
                                    $i = 1;
                                    while ($row_product_order = $result_product_order->fetch_array()) {
                                        $total_product_qty = $total_product_qty + $row_product_order["product_qty"];
                                        $sku_name = $row_product_order['product_sku'];    
                                        $product_image_name = $row_product_order['img1'];
                                        ?>                    
                                    <tr>
                                        <td><?= $i ?></td>
                                        <td>
                                            <img src="<?= $product_image_name ?>" style="width:70px" class="rounded border">
                                        </td>

                                        <td>
                                            <div class="fw-bold">
                                                <?= $row_product_order['product_name'] ?>
                                            </div>
                                            <?php if(!empty($row_product_order['product_additional_parameter_shopcart'])){ ?>
                                                <div class="text-danger fs-8 mt-1">
                                                    Filter :
                                                    <?= $row_product_order['product_additional_parameter_shopcart'] ?>
                                                </div>
                                            <?php } ?>

                                        </td>

                                        <td><?= $row_product_order['product_sku'] ?></td>
                                        <td><?= $row_product_order['size'] ?></td>
                                        <td><?= $row_product_order['product_qty'] ?></td>
                                        <td>
                                            ₹<?= number_format($row_product_order['product_unique_price'],2) ?>
                                        </td>
                                        <td>
                                            ₹<?= number_format($row_product_order['product_price'],2) ?>
                                        </td>
                                    </tr>
                                    <?php
                                    $i++;}
                                    ?>  
                                    <tr>
                                       <td colspan="6">&nbsp;</td>
                                       <td>Sub Total :</td>
                                       <td></td>
                                    </tr>
                                    <tr>
                                       <td colspan="6">&nbsp;</td>
                                       <td>Discount :</td>
                                       <td></td>
                                    </tr>
                                    <?php if (
                                        isset($rowcount_couponcode) &&
                                        $rowcount_couponcode > 0
                                    ) { ?>
                                    <tr>
                                       <td colspan="6">&nbsp;</td>
                                       <td>Coupon Code :</td>
                                       <td></td>
                                    </tr>
                                    <?php } ?>

                                    <tr>
                                       <td colspan="6">&nbsp;</td>
                                       <td>Net Total :</td>
                                       <td></td>
                                    </tr>
                                    <tr>
                                       <td colspan="6">&nbsp;</td>
                                       <td>Grand Total :</td>
                                       <td></td>
                                    </tr>
                                  </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <form id="kt_update_b2c_order_form" action="<?php echo $site_path; ?>/ajax/add-update-order-details" class="form" method="POST" enctype="multipart/form-data">
               <div class="row">
                  <!-- ================= LEFT SIDE ================= -->
                  <div class="col-lg-12">
                    <div class="card card-flush mb-7">
                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="fw-bold">
                                    Customer & Shipping Details
                                </h3>
                            </div>
                        </div>

                        <div class="card-body">
                            <input type="hidden" name="api_grand_total" id="api_grand_total" value="<?= $rows['amount'] ?>">
                            <input type="hidden" name="h1" value="1">
                            <input type="hidden" name="hidden_order_id" value="<?= $order_id ?>">
                            <input type="hidden" name="api_total_product_qty" id="api_total_product_qty" value="<?= $total_product_qty ?>">
                            <input type="hidden" name="redirect_page" value="<?= $site_path ?>/b2c-pending-order">
                            <!-- Customer Information -->
                            <div class="mb-10">
                                <div class="row g-5 fv-row">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold required">Customer Name</label>
                                        <input type="text" name="fullname" class="form-control form-control-solid" value="<?= $rows['customer_name'] ?>">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold required">
                                            Mobile Number
                                        </label>
                                        <input type="text" name="cmobile" class="form-control form-control-solid" value="<?= $rows['cmobile'] ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Country</label>
                                        <select name="country_id" class="form-select form-select-solid">
                                            <option value="1">India</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div class="row g-5 fv-row">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold required">Address Line 1</label>
                                        <input type="text" name="address_1" id="address_1" class="form-control form-control-solid" value="<?= $rows['address1'] ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Address Line 2</label>
                                        <input type="text" name="landmark_1" id="landmark_1" class="form-control form-control-solid" value="<?= $rows['address2'] ?>">
                                    </div>
                                </div>

                                <div class="row g-5 mt-1 fv-row">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold required">Pincode</label>
                                        <input type="text" name="zipcode" id="zipcode" class="form-control form-control-solid" value="<?= $rows['zipcode'] ?>">
                                    </div>
                                    <div class="col-md-4 fv-row">
                                        <label class="form-label fw-semibold required">City</label>
                                        <input type="text" name="city" class="form-control form-control-solid" value="<?= $rows['city'] ?>">
                                    </div>
                                    <div class="col-md-4 fv-row">
                                        <label class="form-label fw-semibold required">State</label>
                                        <select name="state" id="state" class="form-select form-select-solid">
                                            <option value="">Select State</option>
                                            <?php foreach($state_list as $rows_state){ ?>
                                                <option
                                                    value="<?= $rows_state['id'] ?>"
                                                    <?= strtolower($rows['state']) == strtolower($rows_state['name']) ? 'selected' : '' ?>>
                                                    <?= $rows_state['name'] ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-5 mt-1">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold required">Payment Method</label>
                                    <select name="payment_method" id="payment_method" class="form-select form-select-solid">
                                        <option value="">Select</option>
                                        <option value="1" <?= $rows["payment_method"] == 1 ? "selected" : "" ?>>Prepaid</option>
                                        <option value="2" <?= $rows["payment_method"] == 2 ? "selected" : "" ?>>COD</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold required">Status</label>
                                    <select name="status" id="status" class="form-select form-select-solid">
                                        <option value="">Select</option>
                                        <option value="1" <?= $rows["status"] == 1 ? "selected" : "" ?>>Pending</option>
                                        <option value="2" <?= $rows["status"] == 2 ? "selected" : "" ?>>Confirm</option>
                                        <option value="3" <?= $rows["status"] == 3 ? "selected" : "" ?>>Reject</option>
                                        <option value="5" <?= $rows["status"] == 5 ? "selected" : "" ?>>On Hold</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold required">Extra Order Status</label>
                                    <select name="extra_order_id" id="extra_order_id" class="form-select form-select-solid">
                                        <option value="">Select Order Status</option>
                                    <?php if ($order_extra_status) {
                                        foreach ($order_extra_status as $rows_ostatus) { ?>
                                            <option <?php if ($rows["order_status_extra_id"] == $rows_ostatus["id"]) { echo "selected";} ?> value="<?php echo $rows_ostatus["id"]; ?>"><?php echo $rows_ostatus["name"]; ?></option>
                                        <?php }
                                        } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row g-5 mt-1">
                                <div class="col-md-6 fv-row">
                                    <label class="form-label fw-semibold required">Reject Remarks</label>
                                    <textarea name="reject_remarks" id="reject_remarks" class="form-control form-control-solid"><?php echo $rows['reject_remarks']; ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

               </div>
               <!-- Footer -->
               <div class="card-footer d-flex justify-content-end py-6 px-9">
                  <button type="reset" class="btn btn-light btn-active-light-primary me-2">Discard</button>
                  <button type="submit" name="kt_b2c_update_order_details_submit" id="kt_b2c_update_order_details_submit" class="btn btn-primary">Save Changes</button>

               </div>
            </form>
         </div>
      </div>
   </div>
    <?php include("includes/footer.php"); ?>
</div>
<script>var hostUrl = "assets/";</script>
<script src="<?php echo $site_path; ?>/assets/plugins/global/plugins.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/scripts.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/custom/update-order.js?v=<?php echo time(); ?>"></script>
<script>
    
</script>
</body>
</html>