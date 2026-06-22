<?php
include("config/database.php");
include("config/auth_check.php");
include("includes/sidemenu.php");

$staff_list = getAllStaffList();
$all_department = getAllDepartments();
?>
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading text-gray-900 fw-bold fs-3 my-0">
                        Create New Task
                    </h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="<?php echo $site_path; ?>/pages/dashboard" class="text-muted text-hover-primary">
                                Home
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">
                            Create New Task
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                <form id="kt_create_task_form" action="<?php echo $site_path ?>/ajax/add-update-task-details" class="form" method="POST" enctype="multipart/form-data">
                    <!--begin::Card body-->
                    <input name="redirect_page" type="hidden" id="redirect_page" value="<?php echo $site_path ?>/task-list" />
                    <div class="card-body border-top p-9">
                        <div class="row mb-6">
                            <div class="col-lg-6 fv-row">
                                <label class="form-label required fw-semibold fs-6">Task Title</label>
                                <input type="text" name="task_title" id="task_title" class="form-control form-control-lg form-control-solid" placeholder="Task Title" />
                            </div>
                            <div class="col-lg-6 fv-row">
                                <label class="form-label required fw-semibold fs-6">Task Type</label>
                                <div class="d-flex flex-wrap gap-3">
                                    <?php
                                    if ($task_type) {
                                        $i = 1;
                                        foreach ($task_type as $single_task) {?>
                                                <input type="radio" class="btn-check channel-radio" name="task_type" id="task_type_<?php echo $i; ?>" value="<?php echo $single_task['id']; ?>">
                                                <label class="btn btn-outline btn-outline-dashed btn-active-light-primary" for="task_type_<?php echo $i; ?>"><?php echo $single_task['name']; ?></label>

                                            <?php
                                            $i++;
                                        }
                                    }
                                    ?>

                                    </div>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <div class="col-lg-6 fv-row">
                                <label class="form-label fw-semibold fs-6">Department</label>
                                <select name="department" id="department" aria-label="Assign To" data-control="select2" data-placeholder="Department" class="form-select form-select-solid form-select-lg fw-semibold">
                                    <option value="all">All Department</option>
                                    <?php if($all_department){
                                            foreach($all_department as $single_dept){?>
                                                <option value="<?php echo $single_dept['id']; ?>"><?php echo $single_dept['department_name']; ?></option>
                                    <?php } } ?>
                                </select>
                            </div>
                            <div class="col-lg-6 fv-row">
                                <label class="form-label">Priority</label>
                                <select name="priority" id="priority" aria-label="Assign To" data-control="select2" data-placeholder="Priority" class="form-select form-select-solid form-select-lg fw-semibold">
                                    <option value="Medium">Medium</option>
                                    <option value="High">High</option>
                                    <option value="Low">Low</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mb-6">
                            <div class="col-lg-6 fv-row">
                                <label class="form-label fw-semibold fs-6">Assign To Staff</label>
                                <select name="assign_to[]" id="assign_to" aria-label="Assign To" data-control="select2" data-placeholder="Assign To" class="form-select form-select-solid form-select-lg fw-semibold" multiple>
                                    <option value="">Select Staff</option>
                                    <?php if($staff_list){
                                            foreach($staff_list as $single_Staff){?>
                                    
                                    <option value="<?php echo $single_Staff['id']; ?>"><?php echo $single_Staff['name']; ?></option>
                                    <?php } } ?>
                                </select>
                            </div>
                            <div class="col-lg-6 fv-row">
                                <label class="form-label required fw-semibold fs-6">Recurrence</label>
                                <div class="d-flex flex-wrap gap-3">
                                    <?php
                                    if ($recurrence) {
                                        $i = 1;
                                        foreach ($recurrence as $single_rec) {?>
                                                <input type="radio" class="btn-check channel-radio" name="recurrence" id="recurrence_<?php echo $i; ?>" value="<?php echo $single_rec['id']; ?>">
                                                <label class="btn btn-outline btn-outline-dashed btn-active-light-primary" for="recurrence_<?php echo $i; ?>"><?php echo $single_rec['name']; ?></label>

                                            <?php
                                            $i++;
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                            
                        </div>
                        <div class="row mb-6">
                            <div class="col-lg-6 fv-row">
                                <label class="form-label">Due Date</label>
                                <input type="text" name="due_date" id="due_date" class="form-control form-control-lg form-control-solid" placeholder="Due Date" readonly/>
                            </div>
                            <div class="col-lg-6 fv-row">
                                <label class="form-label fw-semibold fs-6">Proof required on completion</label>
                                <div class="d-flex flex-wrap gap-3">
                                    <?php
                                    if ($task_proof_required) {
                                        $i = 1;
                                        foreach ($task_proof_required as $single_proof) {?>
                                                <input type="radio" class="btn-check channel-radio" name="proof_required" id="proof_required_<?php echo $i; ?>" value="<?php echo $single_proof['id']; ?>">
                                                <label class="btn btn-outline btn-outline-dashed btn-active-light-primary" for="proof_required_<?php echo $i; ?>"><?php echo $single_proof['name']; ?></label>

                                            <?php
                                            $i++;
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <div class="col-lg-12 fv-row">
                                <label class="form-label">Description</label>
                                <textarea id="description" name="description" placeholder="Task Description" class="form-control form-control-lg form-control-solid" rows="5" cols="5"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-end py-6 px-9">
                        <button type="reset" class="btn btn-light btn-active-light-primary me-2" onclick="window.location.href = '<?php echo $site_path; ?>/task-list'">Discard</button>
                        <button type="submit" class="btn btn-primary" id="kt_create_task_details_submit">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
        <?php include("includes/footer.php"); ?>
    </div>
</div>

<script>var hostUrl = "<?php echo $site_path; ?>/";</script>
<script src="<?php echo $site_path; ?>/assets/plugins/global/plugins.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/scripts.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/custom/create-task.js?v=<?php echo time(); ?>"></script>
<script>
    $(document).ready(function () {
        $("#due_date").flatpickr({
            altInput: true,
            altFormat: "Y-m-d H:i",
            dateFormat: "Y-m-d H:i",
            enableTime: true,
            time_24hr: true       // remove this line if you want 12hr AM/PM
        });
    });
</script>