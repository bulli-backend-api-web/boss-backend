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
                    <h1 class="page-heading text-gray-900 fw-bold fs-3 my-0">Model</h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="<?php echo $site_path; ?>/pages/dashboard" class="text-muted text-hover-primary">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">Model List</li>
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
                                <input type="text" id="model_search" class="form-control form-control-solid w-300px ps-12" placeholder="Search" />
                            </div>
                        </div>
                        <a href="<?php echo $site_path; ?>/create-model" class="btn btn-primary"><i class="fa fa-plus"></i>Register New Model</a>
                    </div>

                    <div class="card-body pt-0">
                        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_models_table">
                            <thead>
                                <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                    <th class="min-w-50px">#</th>
                                    <th class="min-w-150px">Name</th>
                                    <th class="min-w-150px">Mobile Number</th>
                                    <th class="min-w-150px">Email</th>
                                    <th class="min-w-150px">DOB</th>
                                    <th class="min-w-150px">Gender</th>
                                    <th class="min-w-100px">City</th>
                                    <th class="min-w-150px">State</th>
                                    <th class="min-w-180px">Actions</th>
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
<script src="<?php echo $site_path; ?>/assets/js/custom/apps/user-management/users/list/table.js?v=<?php echo time(); ?>"></script>

<script>
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
$(document).ready(function () {
    
    var table = $('#kt_models_table').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        ajax: {
            url: "<?php echo $site_path; ?>/ajax/fetch-model-list",
            type: "POST",
            data: function (d) {
                d.status = $('#status_filter').val();
            }
        },
        columns: [
            { data: 'sr_no' },
            { data: 'name'},
            { data: 'mobile_number'},
            { data: 'email'},
            { data: 'dob'},
            { data: 'gender'},
            { data: 'city'},
            { data: 'state'},
            { data: 'actions',orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        drawCallback: function () {
            if (typeof KTMenu !== 'undefined') {
                KTMenu.createInstances();
            }
        }
    });

    $('#model_search').on('keyup', function () {
        table.search(this.value).draw();
    });
});
</script>