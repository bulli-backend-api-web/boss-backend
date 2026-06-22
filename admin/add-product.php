<?php
include("config/database.php");
include("config/auth_check.php");
include("includes/sidemenu.php");
?>
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">Edit Product</h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="index.html" class="text-muted text-hover-primary">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">eCommerce</li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">Catalog</li>
                    </ul>
                </div>
            </div>
        </div>
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                <form id="kt_ecommerce_add_product_form" class="form d-flex flex-column flex-lg-row" data-kt-redirect="/apps/ecommerce/catalog/products.html">
                    <div class="d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-300px mb-7 me-lg-10">
                        <div class="card card-flush py-4">
                            <div class="card-header">
                                <div class="card-title">
                                    <h2>Thumbnail</h2>
                                </div>
                            </div>
                            <div class="card-body text-center pt-0">
                                <div class="image-input image-input-empty image-input-outline image-input-placeholder mb-3" data-kt-image-input="true">
                                    <div class="image-input-wrapper w-150px h-150px" style="background-image: url(/assets/media//stock/ecommerce/78.png)"></div>
                                    <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change avatar">
                                        <i class="ki-duotone ki-pencil fs-7">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <input type="file" name="avatar" accept=".png, .jpg, .jpeg" />
                                        <input type="hidden" name="avatar_remove" />
                                    </label>
                                    <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel avatar">
                                        <i class="ki-duotone ki-cross fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                    <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove avatar">
                                        <i class="ki-duotone ki-cross fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                </div>
                                <div class="text-muted fs-7">Set the product thumbnail image. Only *.png, *.jpg and *.jpeg image files are accepted</div>
                            </div>
                        </div>
                        <div class="card card-flush py-4">
                            <div class="card-header">
                                <div class="card-title">
                                    <h2>Status</h2>
                                </div>
                                <div class="card-toolbar">
                                    <div class="rounded-circle bg-success w-15px h-15px" id="kt_ecommerce_add_product_status"></div>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <select class="form-select mb-2" data-control="select2" data-hide-search="true" data-placeholder="Select an option" id="kt_ecommerce_add_product_status_select">
                                    <option></option>
                                    <option value="published" selected="selected">Published</option>
                                    <option value="draft">Draft</option>
                                    <option value="scheduled">Scheduled</option>
                                    <option value="inactive">Inactive</option>
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
                                <label class="form-label">Categories</label>
                                <select class="form-select mb-2" data-control="select2" data-placeholder="Select an option" data-allow-clear="true" multiple="multiple">
                                    <option></option>
                                    <option value="Computers">Computers</option>
                                    <option value="Watches">Watches</option>
                                    <option value="Headphones">Headphones</option>
                                    <option value="Footwear">Footwear</option>
                                    <option value="Cameras">Cameras</option>
                                    <option value="Shirts">Shirts</option>
                                    <option value="Household">Household</option>
                                    <option value="Handbags">Handbags</option>
                                    <option value="Wines">Wines</option>
                                    <option value="Sandals">Sandals</option>
                                </select>
                                <div class="text-muted fs-7 mb-7">Add product to a category.</div>
                                <label class="form-label d-block">Tags</label>
                                <input id="kt_ecommerce_add_product_tags" name="kt_ecommerce_add_product_tags" class="form-control mb-2" value="new, trending, sale" />
                                <div class="text-muted fs-7">Add tags to a product.</div>
                            </div>
                            <!--end::Card body-->
                        </div>
                    </div>
                    <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
                        <ul class="nav nav-custom nav-tabs nav-line-tabs nav-line-tabs-2x border-0 fs-4 fw-semibold mb-n2">
                            <li class="nav-item">
                                <a class="nav-link text-active-primary pb-4 active" data-bs-toggle="tab" href="#kt_ecommerce_add_product_general">General</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-active-primary pb-4" data-bs-toggle="tab" href="#kt_ecommerce_add_product_advanced">Advanced</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="kt_ecommerce_add_product_general" role="tab-panel">
                                <div class="d-flex flex-column gap-7 gap-lg-10">
                                    <div class="card card-flush py-4">
                                        <div class="card-header">
                                            <div class="card-title">
                                                <h2>General</h2>
                                            </div>
                                        </div>
                                        <div class="card-body pt-0">
                                            <div class="mb-10 fv-row">
                                                <label class="required form-label">Product Name</label>
                                                <input type="text" name="product_name" class="form-control mb-2" placeholder="Product name" value="" />
                                                <div class="text-muted fs-7">A product name is required and recommended to be unique.</div>
                                            </div>
                                            <div>
                                                <label class="form-label">Description</label>
                                                <div id="kt_ecommerce_add_product_description" name="kt_ecommerce_add_product_description" class="min-h-200px mb-2"></div>
                                                <div class="text-muted fs-7">Set a description to the product for better visibility.</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card card-flush py-4">
                                        <div class="card-header">
                                            <div class="card-title">
                                                <h2>Media</h2>
                                            </div>
                                        </div>
                                        <div class="card-body pt-0">
                                            <div class="fv-row mb-2">
                                                <div class="dropzone" id="kt_ecommerce_add_product_media">
                                                    <div class="dz-message needsclick">
                                                        <i class="ki-duotone ki-file-up text-primary fs-3x">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>
                                                        <div class="ms-4">
                                                            <h3 class="fs-5 fw-bold text-gray-900 mb-1">Drop files here or click to upload.</h3>
                                                            <span class="fs-7 fw-semibold text-gray-500">Upload up to 10 files</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-muted fs-7">Set the product media gallery.</div>
                                        </div>
                                    </div>
                                    <div class="card card-flush py-4">
                                        <div class="card-header">
                                            <div class="card-title">
                                                <h2>Pricing</h2>
                                            </div>
                                        </div>
                                        <div class="card-body pt-0">
                                            <!--begin::Input group-->
                                            <div class="mb-10 fv-row">
                                                <label class="required form-label">Base Price</label>
                                                <input type="text" name="price" class="form-control mb-2" placeholder="Product price" value="" />
                                                <div class="text-muted fs-7">Set the product price.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="kt_ecommerce_add_product_advanced" role="tab-panel">
                                <div class="d-flex flex-column gap-7 gap-lg-10">
                                    <div class="card card-flush py-4">
                                        <div class="card-header">
                                            <div class="card-title">
                                                <h2>Inventory</h2>
                                            </div>
                                        </div>
                                        <div class="card-body pt-0">
                                            <!--begin::Input group-->
                                            <div class="mb-10 fv-row">
                                                <!--begin::Label-->
                                                <label class="required form-label">SKU</label>
                                                <input type="text" name="sku" class="form-control mb-2" placeholder="SKU Number" value="" />
                                                <div class="text-muted fs-7">Enter the product SKU.</div>
                                            </div>
                                            <div class="mb-10 fv-row">
                                                <label class="required form-label">Barcode</label>
                                                <input type="text" name="barcode" class="form-control mb-2" placeholder="Barcode Number" value="" />
                                                <div class="text-muted fs-7">Enter the product barcode number.</div>
                                            </div>
                                            <div class="mb-10 fv-row">
                                                <label class="required form-label">Quantity</label>
                                                <div class="d-flex gap-3">
                                                    <input type="number" name="shelf" class="form-control mb-2" placeholder="On shelf" value="" />
                                                    <input type="number" name="warehouse" class="form-control mb-2" placeholder="In warehouse" />
                                                </div>
                                                <div class="text-muted fs-7">Enter the product quantity.</div>
                                            </div>
                                            <div class="fv-row">
                                                <label class="form-label">Allow Backorders</label>
                                                <div class="form-check form-check-custom form-check-solid mb-2">
                                                    <input class="form-check-input" type="checkbox" value="" />
                                                    <label class="form-check-label">Yes</label>
                                                </div>
                                                <div class="text-muted fs-7">Allow customers to purchase products that are out of stock.</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card card-flush py-4">
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
                                                        <div data-repeater-list="kt_ecommerce_add_product_options" class="d-flex flex-column gap-3">
                                                            <div data-repeater-item="" class="form-group d-flex flex-wrap align-items-center gap-5">
                                                                <!--begin::Select2-->
                                                                <div class="w-100 w-md-200px">
                                                                    <select class="form-select" name="product_option" data-placeholder="Select a variation" data-kt-ecommerce-catalog-add-product="product_option">
                                                                        <option></option>
                                                                        <option value="color">Color</option>
                                                                        <option value="size">Size</option>
                                                                        <option value="material">Material</option>
                                                                        <option value="style">Style</option>
                                                                    </select>
                                                                </div>
                                                                <input type="text" class="form-control mw-100 w-200px" name="product_option_value" placeholder="Variation" />
                                                                <!--end::Input-->
                                                                <button type="button" data-repeater-delete="" class="btn btn-sm btn-icon btn-light-danger">
                                                                    <i class="ki-duotone ki-cross fs-1">
                                                                        <span class="path1"></span>
                                                                        <span class="path2"></span>
                                                                    </i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group mt-5">
                                                        <button type="button" data-repeater-create="" class="btn btn-sm btn-light-primary">
                                                            <i class="ki-duotone ki-plus fs-2"></i>Add another variation</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card card-flush py-4">
                                        <div class="card-header">
                                            <div class="card-title">
                                                <h2>Shipping</h2>
                                            </div>
                                        </div>

                                        <div class="card-body pt-0">
                                            <div class="fv-row">
                                                <div class="form-check form-check-custom form-check-solid mb-2">
                                                    <input class="form-check-input" type="checkbox" id="kt_ecommerce_add_product_shipping_checkbox" value="1" checked="checked" />
                                                    <label class="form-check-label">This is a physical product</label>
                                                </div>                                                
                                                <div class="text-muted fs-7">Set if the product is a physical or digital item. Physical products may require shipping.</div>
                                            </div>
                                            <div id="kt_ecommerce_add_product_shipping" class="mt-10">
                                                <div class="mb-10 fv-row">
                                                    <label class="form-label">Weight</label>
                                                    <input type="text" name="weight" class="form-control mb-2" placeholder="Product weight" value="4.3" />
                                                    <div class="text-muted fs-7">Set a product weight in kilograms (kg).</div>
                                                </div>
                                                <div class="fv-row">
                                                    <label class="form-label">Dimension</label>
                                                    <div class="d-flex flex-wrap flex-sm-nowrap gap-3">
                                                        <input type="number" name="width" class="form-control mb-2" placeholder="Width (w)" value="12" />
                                                        <input type="number" name="height" class="form-control mb-2" placeholder="Height (h)" value="4" />
                                                        <input type="number" name="length" class="form-control mb-2" placeholder="Lengtn (l)" value="8.5" />
                                                    </div>
                                                    <div class="text-muted fs-7">Enter the product dimensions in centimeters (cm).</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <a href="<?php echo $site_path; ?>/product-listing" id="kt_ecommerce_add_product_cancel" class="btn btn-light me-5">Cancel</a>
                            <button type="submit" id="kt_ecommerce_add_product_submit" class="btn btn-primary">
                                <span class="indicator-label">Save Changes</span>
                                <span class="indicator-progress">Please wait... 
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php include("includes/footer.php"); ?>
</div>

</div>
</div>
</div>
<script src="<?php echo $site_path; ?>/assets/plugins/global/plugins.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/scripts.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/plugins/custom/formrepeater/formrepeater.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/custom/apps/ecommerce/catalog/save-product.js?v=<?php echo time(); ?>"></script>
<script>
function openPopupCentered(imageUrl) {
    const width = 600;
    const height = 600;
    const left = (screen.width / 2) - (width / 2);
    const top = (screen.height / 2) - (height / 2);

    const popup = window.open('', 'ImagePopup', `width=${width},height=${height},top=${top},left=${left},resizable=yes`);

    popup.document.write(`<html><head><title>Image Preview</title></head><body style="margin:0; background:#000; display:flex; align-items:center; justify-content:center;"><img src="${imageUrl}" style="max-width:100%; max-height:100%;"></body></html>`);
    popup.document.close();
}    
    
$('#kt_ecommerce_add_product_meta_description').attr('contenteditable', 'false').css({'pointer-events': 'none', 'background-color': '#f8f9fa', 'opacity': '0.9'});
</script>
</body>
<!--end::Body-->
</html>