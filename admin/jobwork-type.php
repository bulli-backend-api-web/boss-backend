<?php
include("config/database.php");
include("config/auth_check.php");
include("includes/sidemenu.php");
$users_role = getUniqueRoles();
?>
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <!--begin::Content wrapper-->
    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Toolbar-->
        <div id="kt_app_toolbar" class="app-toolbar pt-7 pt-lg-10">
            <!--begin::Toolbar container-->
            <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
                <!--begin::Toolbar wrapper-->
                <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                    <!--begin::Page title-->
                    <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                        <!--begin::Title-->
                        <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">Manage Jobwork Type</h1>
                        <!--end::Title-->
                        <!--begin::Breadcrumb-->
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <a href="<?php echo $site_path ?>/dashboard" class="text-muted text-hover-primary">Home</a>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">Jobwork List</li>
                            <!--end::Item-->

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
                <!--begin::Card-->
                <div class="card">
                    <!--begin::Card header-->
                    <div class="card-header border-0 pt-6">
                        <!--begin::Card title-->
                        <div class="card-title">
                            <!--begin::Search-->
                            <div class="d-flex align-items-center position-relative my-1" style="margin-right: 15px;">
                                <i class="ki-outline ki-magnifier fs-3 position-absolute ms-5"></i>
                                <input type="text" data-kt-user-table-filter="search" class="form-control form-control-solid w-250px ps-13" placeholder="Search Party" />
                            </div>
                        </div>
                        <div class="card-toolbar">
                            <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_jobwork_type">
                                    <i class="ki-outline ki-plus fs-2"></i>Add Jobwork Type</button>
                            </div>
                            <div class="modal fade" id="kt_modal_add_jobwork_type" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered mw-650px">
                                    <div class="modal-content">
                                        <div class="modal-header" id="kt_modal_add_user_header">
                                            <h2 class="fw-bold">Add Jobwork Type Details</h2>
                                            <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-jobwork-modal-action="close">
                                                <i class="ki-outline ki-cross fs-1"></i>
                                            </div>
                                        </div>
                                        <div class="modal-body px-5 my-7">
                                            <form id="kt_modal_add_jobwork_type_form" class="form" action="<?php echo $site_path; ?>/ajax/add-update-jobwork-type-details" enctype="multipart/form-data">
                                                <div class="d-flex flex-column scroll-y px-5 px-lg-10" id="kt_modal_add_user_scroll" data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_add_user_header" data-kt-scroll-wrappers="#kt_modal_add_user_scroll" data-kt-scroll-offset="300px">
                                                    <div class="fv-row mb-7 fv-row">
                                                        <label class="required fw-semibold fs-6 mb-2">Name</label>
                                                        <input type="text" name="jobwork_name" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Jobwork Name" value="" />
                                                    </div>
                                                    <div class="fv-row mb-7 fv-row">
                                                        <label class="required fw-semibold fs-6 mb-2">Amount</label>
                                                        <input type="text" name="amount" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Amount" value="" />
                                                    </div>
                                                </div>
                                                <div class="text-center pt-10">
                                                    <button type="reset" class="btn btn-light me-3" data-kt-jobwork-modal-action="cancel">Discard</button>
                                                    <button type="submit" class="btn btn-primary" data-kt-jobwork-modal-action="submit">
                                                        <span class="indicator-label">Submit</span>
                                                        <span class="indicator-progress">Please wait... 
                                                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="kt_modal_update_jobwork_type" tabindex="-1" aria-hidden="true">
                                <!--begin::Modal dialog-->
                                <div class="modal-dialog modal-dialog-centered mw-650px">
                                    <!--begin::Modal content-->
                                    <div class="modal-content">
                                        <!--begin::Modal header-->
                                        <div class="modal-header" id="kt_modal_add_user_header">
                                            <!--begin::Modal title-->
                                            <h2 class="fw-bold">Update Jobwork Details</h2>
                                            <!--end::Modal title-->
                                            <!--begin::Close-->
                                            <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-update-jobwork-modal-action="close">
                                                <i class="ki-outline ki-cross fs-1"></i>
                                            </div>
                                            <!--end::Close-->
                                        </div>
                                        <!--end::Modal header-->
                                        <!--begin::Modal body-->
                                        <div class="modal-body px-5 my-7">
                                            <!--begin::Form-->
                                            <form id="kt_modal_update_jobwork_form" class="form" action="<?php echo $site_path; ?>/ajax/add-update-jobwork-type-details" enctype="multipart/form-data">
                                                <!--begin::Scroll-->
                                                <div class="d-flex flex-column scroll-y px-5 px-lg-10" id="kt_modal_add_user_scroll" data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_add_user_header" data-kt-scroll-wrappers="#kt_modal_add_user_scroll" data-kt-scroll-offset="300px">
                                                    <div class="fv-row mb-7 fv-row">
                                                        <label class="required fw-semibold fs-6 mb-2">Name</label>
                                                        <input type="text" name="jobwork_name" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Jobwork Name" value="" />
                                                    </div>
                                                    <div class="fv-row mb-7 fv-row">
                                                        <label class="required fw-semibold fs-6 mb-2">Amount</label>
                                                        <input type="text" name="amount" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Amount" value="" />
                                                    </div>
                                                     <div class="fv-row mb-7">
                                                        <label class="fs-6 fw-semibold mb-2">
                                                        <span>Status</span>
                                                        </label>
                                                        <div class="col-md-12">
                                                            <div class="row row-cols-1 row-cols-md-3 row-cols-lg-1 row-cols-xl-3 g-9" data-kt-buttons="true" data-kt-buttons-target="[data-kt-button='true']">
                                                        <div class="col">
                                                            <label class="btn btn-outline btn-outline-dashed btn-active-light-primary d-flex text-start p-6" data-kt-button="true">
                                                                <span class="form-check form-check-custom form-check-solid form-check-sm align-items-start mt-1">
                                                                    <input class="form-check-input" type="radio" name="status" value="1" />
                                                                </span>
                                                                <span class="ms-5">
                                                                    <span class="fs-4 fw-bold text-gray-800 d-block">Active</span>
                                                                </span>
                                                            </label>
                                                        </div>
                                                        <div class="col">
                                                            <label class="btn btn-outline btn-outline-dashed btn-active-light-primary d-flex text-start p-6" data-kt-button="true">
                                                                <span class="form-check form-check-custom form-check-solid form-check-sm align-items-start mt-1">
                                                                    <input class="form-check-input" type="radio" name="status" value="0" />
                                                                </span>
                                                                <span class="ms-5">
                                                                    <span class="fs-4 fw-bold text-gray-800 d-block">Inactive</span>
                                                                </span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                
                                                <div class="text-center pt-10">
                                                    <button type="reset" class="btn btn-light me-3" data-kt-update-jobwork-modal-action="cancel">Discard</button>
                                                    <button type="submit" class="btn btn-primary" data-kt-update-jobwork-modal-action="submit">
                                                        <span class="indicator-label">Submit</span>
                                                        <span class="indicator-progress">Please wait... 
                                                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body py-4">
                        <div class="table-responsive">
                        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_jobwork_type_list">
                                <thead class="bg-light border-bottom">
                                <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                    <th class="min-w-100px">SR No.</th>
                                    <th class="min-w-100px">Name</th>
                                    <th class="min-w-100px">Amount</th>
                                    <th class="min-w-100px">Status</th>
                                    <th class="text-end min-w-100px">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-semibold">
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
</div>
</div>
</div>
<div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
    <i class="ki-outline ki-arrow-up"></i>
</div>

<script src="<?php echo $site_path; ?>/assets/plugins/global/plugins.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/scripts.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/custom/add-jobwork-details.js?v=<?php echo time(); ?>"></script>
<script src="<?php echo $site_path; ?>/assets/js/custom/update-jobwork-details.js?v=<?php echo time(); ?>"></script>
<script>
    $(document).ready(function () {
        var table = $('#kt_jobwork_type_list').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?php echo $site_path ?>/ajax/fetch-jobwork-type-list',
                type: 'POST'
            },

            columns: [
                {data: 'sr_no'},
                {data: 'namae'},
                {data: 'amount'},
                {data: 'status'},
                {data: 'actions', orderable: false, className: "text-end"}
            ],
            pageLength: 50,
            order: [[0, 'desc']],
            columnDefs: [
                {targets: [1, 2], orderable: true},
                {targets: [0,3,4], orderable: false}
            ],
            drawCallback: function () {
                KTMenu.createInstances();
            }
        });
        $('[data-kt-user-table-filter="search"]').on('keyup', function () {
            table.search(this.value).draw();
        });
        
         $(document).on("click", ".edit-jobwork-type", function() {
            const id = $(this).data("id");
            const jobwork_name = $(this).data("jobwork-name");
            const amount = $(this).data("amount");
            const status = $(this).data("status");

            const form = $("#kt_modal_update_jobwork_form");
            const modal = $("#kt_modal_update_jobwork_type");
            form.find('input[name="jobwork_name"]').val(jobwork_name);
            form.find('input[name="amount"]').val(amount);
            
            modal.on('shown.bs.modal', function() {
            const $radio = modal.find('input[name="status"][value="' + status + '"]');

            if ($radio.length) {
                modal.find('input[name="status"]').prop('checked', false);
                modal.find('[data-kt-button="true"]').removeClass('active');

                // Trigger click to update KT button styling
                $radio.trigger('click');
            } else {
                console.error("Radio input not found for status:", status);
            }

            form.find('input[name="id"]').remove();
            form.append('<input type="hidden" name="id" value="'+id+'">');
             });
        });
        
        $(document).on('click', '.delete_jobwork', function(e) {
            e.preventDefault();

            const btn = this;
            const row = $(btn).closest("tr");
            const job_id = $(btn).data("id");
            const action = $(btn).data("action");
            const userName = row.find("td").eq(1).text().trim() || "this user";

            Swal.fire({
                text: "Are you sure you want to delete " + userName + "?",
                icon: "warning",
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: "Yes, delete!",
                cancelButtonText: "No, cancel",
                customClass: {
                    confirmButton: "btn fw-bold btn-danger",
                    cancelButton: "btn fw-bold btn-active-light-primary"
                }
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        url: action,
                        type: "POST",
                        data: {
                            'job_id': job_id,
                            'action': 'delete_jobwork'
                        },
                        dataType: "json",
                        success: function(res) {
                            if (res.status === "success") {
                                Swal.fire({
                                    text: "You have deleted " + userName + "!",
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, got it!",
                                    customClass: {
                                        confirmButton: "btn fw-bold btn-primary"
                                    }
                                }).then(function() {
                                    $('#kt_table_users').DataTable().row(row).remove().draw();
                                    if (typeof a === 'function') a();
                                     window.location.reload();
                                });
                            } else {
                                Swal.fire({
                                    text: res.message || "Could not delete " + userName,
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, got it!",
                                    customClass: {
                                        confirmButton: "btn fw-bold btn-primary"
                                    }
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                text: "Server error! " + userName + " was not deleted.",
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary"
                                }
                            });
                        }
                    });
                } else if (result.dismiss === "cancel") {
                    Swal.fire({
                        text: userName + " was not deleted.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn fw-bold btn-primary"
                        }
                    });
                }
            });
        });
    });

</script>
</body>
</html>