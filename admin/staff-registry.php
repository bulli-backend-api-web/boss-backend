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
                    <h1 class="page-heading text-gray-900 fw-bold fs-3 my-0">Staff Registration</h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="<?php echo $site_path; ?>/pages/dashboard" class="text-muted text-hover-primary">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">Staff Registered</li>
                    </ul>
                </div>
            </div>
        </div>

        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                <div class="card card-flush shadow-sm">
                    <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                        <div class="card-title">
                            <div class="d-flex align-items-center position-relative my-1">
                                <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <input type="text" id="staff_search" class="form-control form-control-solid w-300px ps-12" placeholder="Search" />
                            </div>
                        </div>
                        <a href="<?php echo $site_path; ?>/create-staff" class="btn btn-primary"><i class="fa fa-plus"></i>Register New Staff</a>
                    </div>
                    <div class="card-body pt-0">
                        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_staff_table">
                            <thead>
                                <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                    <th class="min-w-50px">#</th>
                                    <th class="min-w-150px">Full Name</th>
                                    <th class="min-w-150px">Mobile Number</th>
                                    <th class="min-w-150px">Email</th>
                                    <th class="min-w-150px">Gender</th>
                                    <th class="min-w-150px">DOB</th>
                                    <th class="min-w-150px">DOJ</th>
                                    <th class="min-w-150px">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="fw-semibold text-gray-600"></tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
        <?php include("includes/footer.php"); ?>
    </div>
</div>

<script src="<?php echo $site_path; ?>/assets/plugins/global/plugins.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/scripts.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/plugins/custom/datatables/datatables.bundle.js"></script>

<script>
    $(document).ready(function () {
        var table = $('#kt_staff_table').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            ajax: {
                url: "<?php echo $site_path; ?>/ajax/fetch-staff-list",
                type: "POST",
                data: function (d) {
                    d.status = $("#status_filter").val()
                }
            },
            columns: [
                {data: 'sr_no' },
                {data: 'name', orderable: false },
                {data: 'mobile_number', orderable: false },
                {data: 'email' },
                {data: 'gender'},
                {data: 'dob'},
                {data: 'doj'},
                {data: 'actions'}
            ],
            order: [[0, 'desc']],
            drawCallback: function () {
                if (typeof KTMenu !== 'undefined') {
                    KTMenu.createInstances();
                }
            }
    });
    
        

    $('#staff_search').on('keyup', function () {
        table.search(this.value).draw();
    });

    $('#budget_filter').on('change', function () {
        table.ajax.reload();
    });

    $('#category_filter').on('change', function () {
        table.ajax.reload();
    });
    
    $(document).on('click', '.delete_staff', function(e) {
            e.preventDefault();

            const btn = this;
            const row = $(btn).closest("tr");
            const staff_id = $(btn).data("id");
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
                            'staff_id': staff_id,
                            'action': 'delete_staff'
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