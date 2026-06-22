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
                    <h1 class="page-heading text-gray-900 fw-bold fs-3 my-0">Staff Attendance</h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="<?php echo $site_path; ?>/pages/dashboard" class="text-muted text-hover-primary">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">Staff Attendance</li>
                    </ul>
                </div>
            </div>
        </div>

        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                <div class="card card-flush shadow-sm">
                    <div class="card-body pt-0">
                        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_attendance_table">
                            <thead>
                                <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                    <th class="min-w-50px">#</th>
                                    <th class="min-w-150px">Name</th>
                                    <th class="min-w-150px">Date</th>
                                    <th class="min-w-150px">Face</th>
                                    <th class="min-w-150px">IP Address</th>
                                    <th class="min-w-150px">Device Info</th>
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
        var table = $('#kt_attendance_table').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        ajax: {
            url: "<?php echo $site_path; ?>/ajax/fetch-attendance-list",
            type: "POST",
            data: function (d) {
                
            }
        },
        columns: [
            {data: 'sr_no' },
            {data: 'name', orderable: false },
            {data: 'date', orderable: false },
            {data: 'photo' },
            {data: 'ip_address'},
            {data: 'device'}
        ],
        order: [[0, 'desc']],
        drawCallback: function () {
            if (typeof KTMenu !== 'undefined') {
                KTMenu.createInstances();
                lazyLoadImages();
            }
        }
    });
    
        

        $('#sample_search').on('keyup', function () {
            table.search(this.value).draw();
        });

        $('#budget_filter').on('change', function () {
            table.ajax.reload();
        });
        
        $('#category_filter').on('change', function () {
            table.ajax.reload();
        });
    
        function lazyLoadImages(){
            const observer = new IntersectionObserver(function(entries){
                entries.forEach(function(entry){
                    if(entry.isIntersecting){
                        let img = entry.target;
                        img.src = img.dataset.src;
                        observer.unobserve(img);
                    }
                });
            });
            document.querySelectorAll('.lazy-img').forEach(function(img){
                observer.observe(img);
            });
        }
      });
</script>