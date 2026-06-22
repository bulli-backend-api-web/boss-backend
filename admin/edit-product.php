<?php

include("config/database.php");
include("config/auth_check.php");
include("includes/sidemenu.php");


$product_id = my_simple_crypt($_GET['id'], 'decrypt_1');

$collection_list = [];//getCollection();
$special_note = [];//getProductSpecialNote();
$product_activity = [];//getProductActivityLog($product_id);
$collection_name = '';
$collection = [];
if ($collection_list) {
    foreach ($collection_list as $single_collection) {
        $collection[] = $single_collection['name'];
    }
}
$collectionData = json_encode($collection);
$collection_name = implode(",", $collection);
$productDetails = getData("product", ["tags","img1", "name", "longd", "min_price", "stockstatus", "couponcode_apply", "status", "collection_id", "max_price",  "product_stock", "m_title", "m_desc", "m_keyword", "weight","shipping_type","cod_type","shopify_product_id","sku"], ["id" => $product_id], "", "id DESC");
if ($productDetails) {
    $img1 = $productDetails[0]['img1'];
    $product_name = $productDetails[0]['name'];
    $longd = $productDetails[0]['longd'];
    $plain_description = $longd;
    
    $sellprice = $productDetails[0]['min_price'];
    $mrpprice = $productDetails[0]['max_price'];
    $stockstatus = $productDetails[0]['stockstatus'];
    $couponcode_apply = $productDetails[0]['couponcode_apply'];
    $status = $productDetails[0]['status'];
    $collection_id = $productDetails[0]['collection_id'];
    $product_stock = $productDetails[0]['product_stock'];
    $m_title = $productDetails[0]['m_title'];
    $m_desc = $productDetails[0]['m_desc'];
    $m_keyword = $productDetails[0]['m_keyword'];
    $weight = $productDetails[0]['weight'];
    $shipping_type = $productDetails[0]['shipping_type'];
    $cod_type = $productDetails[0]['cod_type'];
    $shopify_product_id = $productDetails[0]['shopify_product_id'];
    $sku = $productDetails[0]['sku'];
}
$selected_col = [];
$selected_col_String = $productDetails[0]['tags'];;

$product_images = [];
$qry_ss="select id,img,timg from product_img WHERE product_id=$product_id";
$result_ss = $con->query($qry_ss);
if($result_ss->num_rows > 0){
    while($imgRow = $result_ss->fetch_assoc()){
        $product_images[] = $imgRow['img'];
    }
}
$variant_list = [];
$sql_varint = "SELECT product_variant_id,size,color,product_id,frabic,barcode,sku,stock,mrp,sellprice,weight from product_variants where product_id = $product_id";
$result_variant = $con->query($sql_varint);
if($result_variant->num_rows > 0){
    while($varint_row = $result_variant->fetch_assoc()){
        $variant_list[] = $varint_row;
    }
}

$dynamic_field = [];
$saved_values = [];

?>
<style>
    .view_only{
        background-color : #f8f9fa;
        opacity: 0.9;
    }
</style>
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_toolbar" class="app-toolbar pt-7 pt-lg-10">
            <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
                <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                    <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                        <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">Edit Product</h1>
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                            <li class="breadcrumb-item text-muted">
                                <a href="<?php echo $site_path ?>/dashboard" class="text-muted text-hover-primary">Home</a>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted">Product List</li>
                        </ul>
                        <!--end::Breadcrumb-->
                    </div>
                    <!--end::Page title-->

                </div>
                <!--end::Toolbar wrapper-->
            </div>
            <!--end::Toolbar container-->
        </div>
        <!--end::Toolbar-->
        <!--begin::Content-->
        <div id="kt_app_content" class="app-content">
            <!--begin::Content container-->
            <div id="kt_app_content_container" class="app-container container-fluid">
                <!--begin::Form-->
                <form id="kt_ecommerce_add_product_form" class="form d-flex flex-column flex-lg-row" data-kt-redirect="" action = "<?php echo $site_path; ?>/ajax/add-update-product-details" enctype="multipart/form-data">
                    <!--begin::Aside column-->
                    <input type="hidden" value="1" name="h1"/>
                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>"/>
                    <input type="hidden" name="shopify_product_id" value="<?php echo $shopify_product_id; ?>"/>
                    <div class="d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-300px mb-7 me-lg-10">
                        <!--begin::Thumbnail settings-->
                        <div class="card card-flush py-4">
                            <!--begin::Card header-->
                            <div class="card-header">
                                <!--begin::Card title-->
                                <div class="card-title">
                                    <h2>Thumbnail</h2>
                                </div>
                                <!--end::Card title-->
                            </div>
                            <!--end::Card header-->
                            <!--begin::Card body-->
                            <div class="card-body text-center pt-0">
                                <!--begin::Image input-->
                                <div class="image-input image-input-empty image-input-outline image-input-placeholder mb-3" data-kt-image-input="true">
                                    <!--begin::Preview existing avatar-->
                                    <div class="image-input-wrapper w-150px h-150px" style="background-image: url(<?php echo $img1; ?>)"></div>
                                    <!--end::Preview existing avatar-->
                                    <!--begin::Label-->
                                    <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change avatar">
                                        <i class="ki-outline ki-pencil fs-7"></i>
                                        <!--begin::Inputs-->
                                        <input type="file" name="avatar" accept=".png, .jpg, .jpeg" />
                                        <input type="hidden" name="avatar_remove" />
                                        <!--end::Inputs-->
                                    </label>
                                    <!--end::Label-->
                                    <!--begin::Cancel-->
                                    <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel avatar">
                                        <i class="ki-outline ki-cross fs-2"></i>
                                    </span>
                                    <!--end::Cancel-->
                                    <!--begin::Remove-->
                                    <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove avatar">
                                        <i class="ki-outline ki-cross fs-2"></i>
                                    </span>
                                    <!--end::Remove-->
                                </div>
                                <!--end::Image input-->
                                <!--begin::Description-->
                                <div class="text-muted fs-7">Set the product thumbnail image. Only *.png, *.jpg and *.jpeg image files are accepted</div>
                                <!--end::Description-->
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Thumbnail settings-->
                        <!--begin::Status-->
                        <div class="card card-flush py-4">
                            <!--begin::Card header-->
                            <div class="card-header">
                                <!--begin::Card title-->
                                <div class="card-title">
                                    <h2>Status</h2>
                                </div>
                                <!--end::Card title-->
                                <!--begin::Card toolbar-->
                                <div class="card-toolbar">
                                    <?php if($status == 1){ ?>
                                    <div class="rounded-circle bg-success w-15px h-15px" id="kt_ecommerce_add_product_status"></div>
                                    <?php } else {?>
                                    <div class="rounded-circle bg-danger w-15px h-15px" id="kt_ecommerce_add_product_status"></div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <select class="form-select mb-2" data-control="select2" data-hide-search="true" data-placeholder="Select an option" id="kt_ecommerce_add_product_status_select" name="status">
                                    <option value="1" <?php if ($status == 1) {echo "selected";} ?>>Active</option>
                                    <option value="0" <?php if ($status == 0) {echo "selected";} ?>>Draft</option>
                                </select>
                                <div class="text-muted fs-7">Set the product status.</div>
                                <div class="d-none mt-10">
                                    <label for="kt_ecommerce_add_product_status_datepicker" class="form-label">Select publishing date and time</label>
                                    <input class="form-control" id="kt_ecommerce_add_product_status_datepicker" placeholder="Pick date & time" />
                                </div>
                            </div>
                        </div>
                        <div class="card card-flush py-4">
                            <div class="card-header">
                                <div class="card-title">
                                    <h2>Product Details</h2>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <label class="form-label d-block">Tags</label>
                                <input id="kt_ecommerce_add_product_tags" name="kt_ecommerce_add_product_tags" class="form-control mb-2" value="<?php echo $selected_col_String; ?>" />
                                <div class="text-muted fs-7">Add tags to a product.</div>
                            </div>

                    </div>
                    </div>
                    <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
                        <ul class="nav nav-custom nav-tabs nav-line-tabs nav-line-tabs-2x border-0 fs-4 fw-semibold mb-n2">
                            <!--begin:::Tab item-->
                            <li class="nav-item">
                                <a class="nav-link text-active-primary pb-4 active" data-bs-toggle="tab" href="#kt_ecommerce_add_product_general">General</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-active-primary pb-4" data-bs-toggle="tab" href="#kt_ecommerce_add_product_advanced">Advanced</a>
                            </li>
                        </ul>
                        <!--end:::Tabs-->
                        <!--begin::Tab content-->
                        <div class="tab-content">
                            <!--begin::Tab pane-->
                            <div class="tab-pane fade show active" id="kt_ecommerce_add_product_general" role="tab-panel">
                                <div class="d-flex flex-column gap-7 gap-lg-10">
                                    <!--begin::General options-->
                                    <div class="card card-flush py-4">
                                        <!--begin::Card header-->
                                        <div class="card-header">
                                            <div class="card-title">
                                                <h2>General</h2>
                                            </div>
                                        </div>
                                        <div class="card-body pt-0">
                                            <div class="mb-10 fv-row">
                                                <label class="required form-label">Product Name</label>
                                                <input type="text" name="name" class="form-control mb-2" placeholder="Product name" value="<?php echo $product_name; ?>" />  
                                            </div>
                                            <div>
                                                <!--begin::Label-->
                                                <label class="form-label">Description</label>
                                                <!--end::Label-->
                                                <!--begin::Editor-->
                                                <div id="kt_ecommerce_add_product_description" name="kt_ecommerce_add_product_description" class="min-h-200px mb-2"><?php echo $plain_description; ?></div>
                                                <input type="hidden" name="description" id="hidden_description">

                                                <!--end::Editor-->
                                                <!--begin::Description-->
                                                <div class="text-muted fs-7">Set a description to the product for better visibility.</div>
                                                <!--end::Description-->
                                            </div>
                                            <!--end::Input group-->
                                        </div>
                                        <!--end::Card header-->
                                    </div>
                                    <!--end::General options-->
                                    <!--begin::Media-->
                                    <div class="card card-flush py-4">
                                        <!--begin::Card header-->
                                        <div class="card-header">
                                            <div class="card-title">
                                                <h2>Media</h2>
                                            </div>
                                        </div>
                                        <!--end::Card header-->
                                        <!--begin::Card body-->
                                        <div class="card-body pt-0">
                                            <?php if($product_images){ 
                                                    foreach($product_images as $single_image){?>
                                            <div class="image-input image-input-empty image-input-outline image-input-placeholder mb-3" data-kt-image-input="true">
                                                <!--begin::Preview existing avatar-->
                                                <div class="image-input-wrapper w-150px h-150px" onclick="openPopupCentered('<?php echo $single_image; ?>')" style="background-image: url(<?php echo $single_image; ?>)"></div>
                                                <!--end::Preview existing avatar-->
                                                <!--begin::Label-->
                                                <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change avatar">
                                                    <i class="ki-outline ki-pencil fs-7"></i>
                                                    <!--begin::Inputs-->
                                                    <input type="file" name="avatar" accept=".png, .jpg, .jpeg" />
                                                    <input type="hidden" name="avatar_remove" />
                                                    <!--end::Inputs-->
                                                </label>
                                                <!--end::Label-->
                                                <!--begin::Cancel-->
                                                <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel avatar">
                                                    <i class="ki-outline ki-cross fs-2"></i>
                                                </span>
                                                <!--end::Cancel-->
                                                <!--begin::Remove-->
                                                <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove avatar">
                                                    <i class="ki-outline ki-cross fs-2"></i>
                                                </span>
                                                <!--end::Remove-->
                                            </div>
                                                    <?php } }?>
                                        </div>
                                        <!--end::Card header-->
                                    </div>
                                    <!--end::Media-->
                                    <!--begin::Pricing-->
                                    <div class="card card-flush py-4">
                                        <!--begin::Card header-->
                                        <div class="card-header">
                                            <div class="card-title">
                                                <h2>Pricing</h2>
                                            </div>
                                        </div>
                                        <!--end::Card header-->
                                        <!--begin::Card body-->
                                        <div class="card-body pt-0">
                                            <div class="d-flex flex-wrap gap-5 mb-5">
                                                <div class="fv-row w-100 flex-md-root">
                                                    <label class="required form-label">MRP Price</label>
                                                    <input type="text" name="mrpprice" class="form-control mb-2" value="<?php echo $mrpprice; ?>" />
                                                </div>
                                                <!--end::Input group-->
                                                <!--begin::Input group-->
                                                <div class="fv-row w-100 flex-md-root">
                                                    <!--begin::Label-->
                                                    <label class="required form-label">B2C Price</label>
                                                    <!--end::Label-->
                                                    <!--begin::Input-->
                                                    <input type="text" name="sellprice" class="form-control mb-2" value="<?php echo $sellprice; ?>" />
                                                    <!--end::Input-->
                                                </div>
                                                <!--end::Input group-->
                                            </div>
                                            <div class="fv-row mb-10">
                                                <!--begin::Label-->
                                                <label class="fs-6 fw-semibold mb-2">Stock Status 
                                                    <span class="ms-1" data-bs-toggle="tooltip" title="Select a discount type that will be applied to this product">
                                                        <i class="ki-outline ki-information-5 text-gray-500 fs-6"></i>
                                                    </span></label>
                                                <!--End::Label-->
                                                <!--begin::Row-->
                                                <div class="row row-cols-1 row-cols-md-3 row-cols-lg-1 row-cols-xl-3 g-9" data-kt-buttons="true" data-kt-buttons-target="[data-kt-button='true']">
                                                    <div class="col">
                                                        <?php
                                                        $active_class = '';
                                                        if ($stockstatus == 1) {
                                                            $active_class = 'active';
                                                        }
                                                        ?>
                                                        <label class="btn btn-outline btn-outline-dashed btn-active-light-primary <?php echo $active_class; ?> d-flex text-start p-6" data-kt-button="true">
                                                            <!--begin::Radio-->
                                                            <span class="form-check form-check-custom form-check-solid form-check-sm align-items-start mt-1">
                                                                <input class="form-check-input" type="radio" name="stockstatus" value="1" <?php
                                                                if ($stockstatus == 1) {
                                                            echo "checked";
                                                                }
                                                                ?> />
                                                            </span>
                                                            <span class="ms-5">
                                                                <span class="fs-4 fw-bold text-gray-800 d-block">In Stock</span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                    <div class="col">
                                                        <?php
                                                        $active_class = '';
                                                        if ($stockstatus == 0) {
                                                            $active_class = 'active';
                                                        }
                                                        ?>
                                                        <label class="btn btn-outline btn-outline-dashed btn-active-light-primary <?php echo $active_class; ?> d-flex text-start p-6" data-kt-button="true">
                                                            <!--begin::Radio-->
                                                            <span class="form-check form-check-custom form-check-solid form-check-sm align-items-start mt-1">
                                                                <input class="form-check-input" type="radio" name="stockstatus" value="0" <?php
                                                                if ($stockstatus == 0) {
                                                            echo "checked";
                                                                }
                                                                ?> />
                                                            </span>
                                                            <span class="ms-5">
                                                                <span class="fs-4 fw-bold text-gray-800 d-block">Out Of Stock</span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="fv-row mb-10">
                                                <!--begin::Label-->
                                                <label class="fs-6 fw-semibold mb-2">Coupon Code Apply 
                                                    <span class="ms-1" data-bs-toggle="tooltip" title="Select a discount type that will be applied to this product">
                                                        <i class="ki-outline ki-information-5 text-gray-500 fs-6"></i>
                                                    </span></label>
                                                <!--End::Label-->
                                                <!--begin::Row-->
                                                <div class="row row-cols-1 row-cols-md-3 row-cols-lg-1 row-cols-xl-3 g-9" data-kt-buttons="true" data-kt-buttons-target="[data-kt-button='true']">
                                                    <div class="col">
                                                        <?php
                                                        $active_class = '';
                                                        if ($couponcode_apply == 1) {
                                                            $active_class = 'active';
                                                        }
                                                        ?>
                                                        <label class="btn btn-outline btn-outline-dashed btn-active-light-primary <?php echo $active_class; ?> d-flex text-start p-6" data-kt-button="true">
                                                            <!--begin::Radio-->
                                                            <span class="form-check form-check-custom form-check-solid form-check-sm align-items-start mt-1">
                                                                <input class="form-check-input" type="radio" name="couponcode_apply" value="1" <?php
                                                                if ($couponcode_apply == 1) {
                                                                    echo "checked";
                                                                }
                                                                ?> />
                                                            </span>
                                                            <span class="ms-5">
                                                                <span class="fs-4 fw-bold text-gray-800 d-block">Yes</span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                    <div class="col">
                                                        <?php
                                                        $active_class = '';
                                                        if ($couponcode_apply == 0) {
                                                            $active_class = 'active';
                                                        }
                                                        ?>
                                                        <label class="btn btn-outline btn-outline-dashed btn-active-light-primary <?php echo $active_class; ?> d-flex text-start p-6" data-kt-button="true">
                                                            <!--begin::Radio-->
                                                            <span class="form-check form-check-custom form-check-solid form-check-sm align-items-start mt-1">
                                                                <input class="form-check-input" type="radio" name="couponcode_apply" value="0" <?php
                                                                if ($couponcode_apply == 0) {
                                                                    echo "checked";
                                                                }
                                                                ?> />
                                                            </span>
                                                            <span class="ms-5">
                                                                <span class="fs-4 fw-bold text-gray-800 d-block">No</span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end::Tab pane-->
                            <!--begin::Tab pane-->
                            <div class="tab-pane fade" id="kt_ecommerce_add_product_advanced" role="tab-panel">
                                <div class="d-flex flex-column gap-7 gap-lg-10">
                                    <!--begin::Inventory-->
                                    <div class="card card-flush py-4">
                                        <!--begin::Card header-->
                                        <div class="card-header">
                                            <div class="card-title">
                                                <h2>Inventory</h2>
                                            </div>
                                        </div>
                                        <!--end::Card header-->
                                        <!--begin::Card body-->
                                        <div class="card-body pt-0">
                                            <!--begin::Input group-->
                                            <div class="mb-10 fv-row">
                                                <label class="required form-label">SKU</label>
                                                <input type="text" name="sku" class="form-control mb-2 view_only" placeholder="SKU Number" value="<?php echo $sku; ?>" readonly />
                                            </div>
                                            
                                            <div class="mb-10 fv-row">
                                                <label class="required form-label">Quantity</label>
                                                <div class="d-flex gap-3">
                                                    <input type="number" name="shelf" class="form-control mb-2 view_only" placeholder="On shelf" value="<?php echo $product_stock; ?>"/>
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                    <!--end::Inventory-->
                                    <!--begin::Variations-->
                                    <div class="card card-flush py-4">
                                        <!--begin::Card header-->
                                        <div class="card-header">
                                            <div class="card-title">
                                                <h2>Variations</h2>
                                            </div>
                                        </div>
                                        <!--end::Card header-->
                                        <!--begin::Card body-->
                                        <div class="card-body pt-0">
                                            <!--begin::Input group-->
                                            <div class="" data-kt-ecommerce-catalog-add-product="auto-options">
                                                <!--begin::Label-->
                                                <label class="form-label">Add Product Variations</label>
                                                <!--end::Label-->
                                                <!--begin::Repeater-->
                                                <div id="kt_ecommerce_add_product_options">
                                                    <!--begin::Form group-->
                                                    <div class="form-group">
                                                        <div data-repeater-list="kt_ecommerce_add_product_options" class="d-flex flex-column gap-6">
                                                            <div data-repeater-item class="border rounded p-4 mb-5 bg-light">
                                                                <div class="row g-3">
                                                                    <div class="col-md-4">
                                                                        <label class="form-label fw-semibold">Size</label>
                                                                        <input type="text" class="form-control view_only" name="size" placeholder="Enter size" readonly>
                                                                    </div>

                                                                    <div class="col-md-4">
                                                                        <label class="form-label fw-semibold">SKU</label>
                                                                        <input type="text" class="form-control" name="sku" placeholder="Enter SKU">
                                                                    </div>

                                                                    <div class="col-md-4">
                                                                        <label class="form-label fw-semibold">Inventory Stock</label>
                                                                        <input type="text" class="form-control" name="inventory_stock" placeholder="Enter stock">
                                                                    </div>

                                                                    <div class="col-md-4">
                                                                        <label class="form-label fw-semibold">Color</label>
                                                                        <input type="text" class="form-control view_only" name="color" placeholder="Enter color" readonly>
                                                                    </div>

                                                                    <div class="col-md-4">
                                                                        <label class="form-label fw-semibold">Material</label>
                                                                        <input type="text" class="form-control" name="material" placeholder="Enter material">
                                                                    </div>

                                                                    <div class="col-md-4">
                                                                        <label class="form-label fw-semibold">MRP</label>
                                                                        <input type="text" class="form-control" name="mrp" placeholder="Enter MRP">
                                                                    </div>
                                                                    
                                                                    <div class="col-md-4">
                                                                        <label class="form-label fw-semibold">Price</label>
                                                                        <input type="text" class="form-control" name="price" placeholder="Enter Price">
                                                                    </div>
                                                                    
                                                                     <div class="col-md-4">
                                                                        <label class="form-label fw-semibold">Weight</label>
                                                                        <input type="text" class="form-control" name="weight" placeholder="Enter Price">
                                                                    </div>
                                                                    
                                                                    <div class="col-md-4">
                                                                        <label class="form-label fw-semibold">Barcode</label>
                                                                        <input type="text" class="form-control view_only" name="barcode" placeholder="Enter barcode">
                                                                    </div>
                                                                    <input type="hidden" name="variant_id"/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end::Repeater-->
                                            </div>
                                            <!--end::Input group-->
                                        </div>
                                        <!--end::Card header-->
                                    </div>
                                    <!--end::Variations-->
                                    <!--begin::Shipping-->
                                    <div class="card card-flush py-4">
                                        <!--begin::Card header-->
                                        <div class="card-header">
                                            <div class="card-title">
                                                <h2>Shipping</h2>
                                            </div>
                                        </div>
                                        <!--end::Card header-->
                                        <!--begin::Card body-->
                                        <div class="card-body pt-0">
                                            <!--begin::Input group-->
                                            <div class="fv-row">
                                                <!--begin::Input-->
                                                <div class="form-check form-check-custom form-check-solid mb-2">
                                                    <input class="form-check-input" type="checkbox" id="kt_ecommerce_add_product_shipping_checkbox" value="1" checked="checked" />
                                                    <label class="form-check-label">This is a physical product</label>
                                                </div>
                                                <!--end::Input-->
                                                <!--begin::Description-->
                                                <div class="text-muted fs-7">Set if the product is a physical or digital item. Physical products may require shipping.</div>
                                                <!--end::Description-->
                                            </div>
                                            <!--end::Input group-->
                                            <!--begin::Shipping form-->
                                            <div id="kt_ecommerce_add_product_shipping" class="mt-10">
                                                <!--begin::Input group-->
                                                <div class="mb-10 fv-row">
                                                    <!--begin::Label-->
                                                    <label class="form-label">Weight</label>
                                                    <!--end::Label-->
                                                    <!--begin::Editor-->
                                                    <input type="text" name="weight" class="form-control mb-2 view_only" placeholder="Product weight" value="<?php echo $weight; ?>" readonly />
                                                    <!--end::Editor-->
                                                    <!--begin::Description-->
                                                    <div class="text-muted fs-7">Set a product weight in grams (gm).</div>
                                                    <!--end::Description-->
                                                </div>
                                                <!--end::Input group-->
                                                <!--begin::Input group-->
                                                <div class="fv-row">
                                                    <!--begin::Label-->
                                                    <label class="form-label">Dimension</label>
                                                    <!--end::Label-->
                                                    <!--begin::Input-->
                                                    <div class="d-flex flex-wrap flex-sm-nowrap gap-3">
                                                        <input type="number" name="width" class="form-control mb-2 view_only" placeholder="Width (w)" value="12" readonly />
                                                        <input type="number" name="height" class="form-control mb-2 view_only" placeholder="Height (h)" value="4" readonly />
                                                        <input type="number" name="length" class="form-control mb-2 view_only" placeholder="Lengtn (l)" value="8.5" readonly />
                                                    </div>
                                                    <!--end::Input-->
                                                    <!--begin::Description-->
                                                    <div class="text-muted fs-7">Enter the product dimensions in centimeters (cm).</div>
                                                    <!--end::Description-->
                                                </div>
                                                <!--end::Input group-->
                                            </div>
                                            <!--end::Shipping form-->
                                        </div>
                                        <!--end::Card header-->
                                    </div>
                                    <!--end::Shipping-->
                                    <!--begin::Meta options-->
                                    <div class="card card-flush py-4">
                                        <!--begin::Card header-->
                                        <div class="card-header">
                                            <div class="card-title">
                                                <h2>Meta Options</h2>
                                            </div>
                                        </div>
                                        <!--end::Card header-->
                                        <!--begin::Card body-->
                                        <div class="card-body pt-0">
                                            <!--begin::Input group-->
                                            <div class="mb-10">
                                                <!--begin::Label-->
                                                <label class="form-label">Meta Tag Title</label>
                                                <!--end::Label-->
                                                <!--begin::Input-->
                                                <input type="text" class="form-control mb-2 view_only" name="m_title" placeholder="Meta tag name" value="<?php echo $m_title ?>" readonly />
                                                <!--end::Input-->
                                                <!--begin::Description-->
                                                <div class="text-muted fs-7">Set a meta tag title. Recommended to be simple and precise keywords.</div>
                                                <!--end::Description-->
                                            </div>
                                            <!--end::Input group-->
                                            <!--begin::Input group-->
                                            <div class="mb-10">
                                                <!--begin::Label-->
                                                <label class="form-label">Meta Tag Description</label>
                                                <!--end::Label-->
                                                <!--begin::Editor-->
                                                <div id="kt_ecommerce_add_product_meta_description" name="m_desc" class="min-h-100px mb-2"><?php echo $m_desc; ?></div>
                                                <!--end::Editor-->
                                                <!--begin::Description-->
                                                <div class="text-muted fs-7">Set a meta tag description to the product for increased SEO ranking.</div>
                                                <!--end::Description-->
                                            </div>
                                            <!--end::Input group-->
                                            <!--begin::Input group-->
                                            <div>
                                                <!--begin::Label-->
                                                <label class="form-label">Meta Tag Keywords</label>
                                                <!--end::Label-->
                                                <!--begin::Editor-->
                                                <input id="kt_ecommerce_add_product_meta_keywords" name="m_keywords" class="form-control mb-2 view_only" value="<?php echo $m_keyword ?>" readonly />
                                                <!--end::Editor-->
                                                <!--begin::Description-->
                                                <div class="text-muted fs-7">Set a list of keywords that the product is related to. Separate the keywords by adding a comma 
                                                    <code>,</code>between each keyword.</div>
                                                <!--end::Description-->
                                            </div>
                                            <!--end::Input group-->
                                        </div>
                                        <!--end::Card header-->
                                    </div>
                                    <!--end::Meta options-->
                                </div>
                            </div>
                        </div>
                        <!--end::Tab content-->
                        <div class="d-flex justify-content-end">
                            <!--begin::Button-->
                            <a href="apps/ecommerce/catalog/products.html" id="kt_ecommerce_add_product_cancel" class="btn btn-light me-5">Cancel</a>
                            <!--end::Button-->
                            <!--begin::Button-->
                            <button type="submit" id="kt_ecommerce_add_product_submit" class="btn btn-primary">
                                <span class="indicator-label">Save Changes</span>
                                <span class="indicator-progress">Please wait... 
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                            <!--end::Button-->
                        </div>
                    </div>
                    <!--end::Main column-->
                </form>
                <!--end::Form-->
            </div>
            <!--end::Content container-->
        </div>
        <!--end::Content-->
    </div>
    <!--end::Content wrapper-->
    <!--begin::Footer-->
    <div id="kt_app_footer" class="app-footer">
        <!--begin::Footer container-->
        <div class="app-container container-fluid d-flex flex-column flex-md-row flex-center flex-md-stack py-3">
            <!--begin::Copyright-->
            <div class="text-gray-900 order-2 order-md-1">
                <span class="text-muted fw-semibold me-1"><?php echo date('Y'); ?>&copy;</span>
                <a href="https://vastranand.in" target="_blank" class="text-gray-800 text-hover-primary">vastranand. All Rights Reserved.Powered by Vastranand Pvt Ltd.</a>
            </div>
        </div>
        <!--end::Footer container-->
    </div>
    <!--end::Footer-->
</div>

</div>
<!--end::Wrapper-->
</div>
<!--end::Page-->
</div>
<script>
    //const whitelist = <?php echo json_encode($collection); ?>;
    window.whitelist = <?php echo json_encode($collection); ?>;

</script>
<script>
    var existingVariants = <?php echo json_encode($variant_list); ?>;
</script>
<script>var hostUrl = "assets/";</script>
<script src="<?php echo $site_path; ?>/assets/plugins/global/plugins.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/scripts.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/plugins/custom/formrepeater/formrepeater.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/custom/apps/ecommerce/catalog/save-product.js?v=<?php echo time(); ?>"></script>
<script>

if (existingVariants.length > 0) {
    var $repeater = $('#kt_ecommerce_add_product_options').repeater({
        initEmpty: false
    });

    var data = [];
    existingVariants.forEach(function (variant) {
        data.push({
            variant_id: variant.product_variant_id || "",   // 👈 add hidden variant ID here
            size: variant.size || "",
            sku: variant.sku || "",
            inventory_stock: variant.stock || "",
            color: variant.color || "",
            material: variant.frabic || "",
            mrp: variant.mrp || "",
            price: variant.sellprice || "",
            weight: variant.weight || "",
            barcode: variant.barcode || ""
        });
    });

    // Set data to repeater
    $repeater.setList(data);
}
function openPopupCentered(imageUrl) {
    const width = 600;
    const height = 600;
    const left = (screen.width / 2) - (width / 2);
    const top = (screen.height / 2) - (height / 2);

    const popup = window.open('', 'ImagePopup', `width=${width},height=${height},top=${top},left=${left},resizable=yes`);

    popup.document.write(`
        <html>
            <head><title>Image Preview</title></head>
            <body style="margin:0; background:#000; display:flex; align-items:center; justify-content:center;">
                <img src="${imageUrl}" style="max-width:100%; max-height:100%;">
            </body>
        </html>
    `);
    popup.document.close();
}

   
$('#kt_ecommerce_add_product_meta_description').attr('contenteditable', 'false').css({'pointer-events':'none','background-color':'#f8f9fa','opacity':'0.9'});
  
  
</script>
</body>
<!--end::Body-->
</html>