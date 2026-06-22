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
                    <h1 class="page-heading text-gray-900 fw-bold fs-3 my-0">Submitted Design</h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="<?php echo $site_path; ?>/pages/dashboard" class="text-muted text-hover-primary">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">Submit Design List</li>
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
                                <input type="text" id="design_search" class="form-control form-control-solid w-300px ps-12" placeholder="Search Design Name" />
                            </div>
                        </div>
                    </div>

                    <div class="card-body pt-0">
                        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_design_table">
                            <thead>
                                <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                    <th class="min-w-50px">#</th>
                                    <th class="min-w-150px">Design Name</th>
                                    <th class="min-w-150px">Design Code</th>
                                    <th class="min-w-150px">Occasion</th>
                                    <th class="min-w-150px">Color</th>
                                    <th class="min-w-150px">Style</th>
                                    <th class="min-w-100px">Min.Sketch</th>
                                    <th class="min-w-150px">Sketch</th>
                                    <th class="min-w-150px">Assign To</th>
                                    <th class="min-w-150px">Created Date</th>
                                    <th class="min-w-150px">Due Date</th>
                                    <th class="min-w-100px">Status</th>
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
    
    var table = $('#kt_design_table').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        ajax: {
            url: "<?php echo $site_path; ?>/ajax/fetch-design-list",
            type: "POST",
            data: function (d) {
                d.status = $('#status_filter').val();
                d.design_type = 'approve-design';
            }
        },
        columns: [
            { data: 'sr_no' },
            { data: 'design_name', orderable: false },
            { data: 'design_code', orderable: false },
            { data: 'occasion' },
            { data: 'color'},
            { data: 'style'},
            { data: 'min_sketch'},
            { data: 'sketch'},
            { data: 'assign_to'},
            { data: 'created_date', orderable: false },
            { data: 'due_date',orderable: false },
            { data: 'status',orderable: false },
            { data: 'actions',orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        drawCallback: function () {
            if (typeof KTMenu !== 'undefined') {
                KTMenu.createInstances();
                lazyLoadImages();
            }
        }
    });

    $('#design_search').on('keyup', function () {
        table.search(this.value).draw();
    });

    $('#status_filter').on('change', function () {
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
    
    $(document).on('click', '.approve-design, .reject-design', function() {
          let id = $(this).data('id');
          let status = $(this).hasClass('approve-design') ? 1 : 2;

          if (!confirm('Are you sure?')) return;

          $.ajax({
              url: '<?php echo $site_path; ?>/ajax/approve-reject-design',
              type: 'POST',
              data: { id: id, status: status },
              success: function(response) {
                  // simple reload OR update UI dynamically
                  location.reload();
              },
              error: function() {
                  alert('Something went wrong!');
              }
          });
      });

});
</script>