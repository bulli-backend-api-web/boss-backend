<?php
include "config/database.php";
include "config/auth_check.php";
include "includes/sidemenu.php";

$staff_list = getAllStaffList();
$all_department_list = getAllDepartments();

$id = my_simple_crypt($_GET['id'], 'decrypt_1');
if ($id > 0) {
    $stmt = $con->prepare("SELECT * FROM rule_master WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $rule_name = $row['rule_name'];
        $rule_type = $row['rule_type'];
        $scope_type = $row['scope_type'];
        $department_id = $row['department_id'];
        $staff_id = $row['staff_id'];
        $metric_code = $row['metric_code'];
        $operator = $row['operator'];
        $threshold_value = $row['threshold_value'];
        $unit = $row['unit'];
        $severity = $row['severity'];
        $notify_to = $row['notify_to'];
        $escalation_after = $row['escalation_after'];
        $escalation_days = $row['escalation_days'];
        $remarks = $row['remarks'];
        
    }

    $stmt->close();

}
?>
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_toolbar" class="app-toolbar pt-7 pt-lg-10">
            <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
                <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                    <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                        <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">Update Rule</h1>
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <a href="<?php echo $site_path; ?>/dashboard" class="text-muted text-hover-primary">Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted">Update Rule</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div id="kt_app_content" class="app-content">
            <div id="kt_app_content_container" class="app-container container-fluid">
                <form id="kt_create_role_form" action="<?php echo $site_path ?>/ajax/add-update-design-details" class="form" method="POST" enctype="multipart/form-data">
                    <!--begin::Card body-->
                    <input name="redirect_page" type="hidden" id="redirect_page" value="<?php echo $site_path ?>/rule-book-list" />
                    <input name="action" type="hidden" id="action" value="update-rule" />
                    <input name="hidden_id" type="hidden" id="hidden_id" value="<?php echo $id; ?>" />
                    <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Update Rule Details</h3>
                            </div>
                            <div class="card-body">
                                <!-- Rule Name -->
                                <div class="mb-7">
                                    <label class="required form-label">Rule Name</label>
                                    <input type="text" class="form-control" name="rule_name" id="rule_name" placeholder="e.g. QC rejection rate must stay below 30%" value="<?php echo $rule_name; ?>">
                                </div>

                                <!-- Rule Type -->
                                <div class="mb-7">
                                    <label class="required form-label">Rule Type</label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="d-flex flex-stack cursor-pointer p-5 border rounded">
                                                <span>
                                                    <span class="fw-bold d-block fs-6">Enforceable</span>
                                                    <span class="text-muted">
                                                        System checks automatically and creates violations.
                                                    </span>
                                                </span>
                                                <input type="radio" class="form-check-input" name="rule_type" id="rule_type" value="enforceable" <?php if($rule_type == 'enforceable'){ echo 'checked'; } ?> >
                                            </label>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="d-flex flex-stack cursor-pointer p-5 border rounded">
                                                <span>
                                                    <span class="fw-bold d-block fs-6">Reference Only</span>
                                                    <span class="text-muted">
                                                        Informational rule only.
                                                    </span>
                                                </span>
                                                <input type="radio" class="form-check-input" name="rule_type" value="reference_only" <?php if($rule_type == 'enforceable'){ echo 'reference'; } ?>>
                                            </label>
                                        </div>

                                    </div>
                                </div>

                                <!-- Scope -->
                                <div class="mb-7">
                                    <label class="required form-label">Apply Scope</label>
                                    <select class="form-select" name="scope_type" id="scope_type" data-control="select2">
                                        <option value="">Select Scope</option>
                                        <option value="company" <?php if($scope_type == 'company'){ echo 'selected';} ?>>Company Wide</option>
                                        <option value="department" <?php if($scope_type == 'department'){ echo 'selected';} ?>>Department</option>
                                        <option value="task" <?php if($scope_type == 'task'){ echo 'selected';} ?>>Task Type</option>
                                        <option value="staff" <?php if($scope_type == 'staff'){ echo 'selected';} ?>>Staff</option>
                                    </select>
                                </div>

                                <!-- Department -->
                                <div class="row mb-7">
                                    <div class="col-md-6">
                                        <label class="form-label">Department</label>
                                        <select class="form-select" name="department_id" data-control="select2">
                                            <option>Select Department</option>
                                            <?php if($all_department_list){
                                                       foreach($all_department_list as $single_dept){?>
                                                           <option value="<?php echo $single_dept['id']; ?>" <?php if($department_id == $single_dept['id']){ echo 'selected';} ?>><?php echo $single_dept['department_name']; ?></option>
                                                       <?php } 
                                            } ?>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Staff</label>
                                        <select class="form-select" name="staff_id" data-control="select2">
                                            <option valeu="">Select Staff</option>
                                            <?php if($staff_list){
                                                        foreach($staff_list as $single_staff){?>
                                                            <option value="<?php echo $single_staff['id']; ?>" <?php if($staff_id == $single_staff['id']){ echo 'selected';} ?>><?php echo $single_staff['name']; ?></option>
                                                        <?php }
                                            } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-7">
                                    <div class="col-md-4">
                                        <label class="required form-label">
                                            Metric
                                        </label>

                                        <select class="form-select" name="metric_code" id="metric_code">
                                            <option value="QC Rejection Rate" <?php if($metric_code == 'QC Rejection Rate'){ echo 'selected';} ?>>QC Rejection Rate</option>
                                            <option value="Late Attendance" <?php if($metric_code == 'Late Attendance'){ echo 'selected';} ?>>Late Attendance</option>
                                            <option value="Low Stock" <?php if($metric_code == 'Low Stock'){ echo 'selected';} ?>>Low Stock</option>
                                            <option value="Task Overdue" <?php if($metric_code == 'Task Overdue'){ echo 'selected';} ?>>Task Overdue</option>
                                        </select>
                                    </div>

                                    <div class="col-md-2">
                                        <label class="required form-label">
                                            Operator
                                        </label>

                                        <select class="form-select" name="operator" id="operator">
                                            <option value=">" <?php if($operator == '>'){ echo 'selected';} ?>>></option>
                                            <option value=">=" <?php if($operator == '>='){ echo 'selected';} ?>>>=</option>
                                            <option value="<" <?php if($operator == '<'){ echo 'selected';} ?>><</option>
                                            <option value="<=" <?php if($operator == '<='){ echo 'selected';} ?>><=</option>
                                            <option value="=" <?php if($operator == '='){ echo 'selected';} ?>>=</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="required form-label">
                                            Threshold
                                        </label>
                                        <input type="number" class="form-control" name="threshold_value" id="threshold_value" placeholder="30" value="<?php echo $threshold_value; ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="required form-label">Unit</label>
                                         <select class="form-select" name="unit" id="unit">
                                            <option value="%" <?php if($unit == '%') { echo 'selected';} ?>>%</option>
                                            <option value="units" <?php if($unit == 'units') { echo 'selected';} ?>>units</option>
                                            <option value="hours" <?php if($unit == 'hours') { echo 'selected';} ?>>hours</option>
                                            <option value="₹" <?php if($unit == '₹') { echo 'selected';} ?>>₹</option>
                                            <option value="count" <?php if($unit == 'count') { echo 'selected';} ?>>count</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-7">
                                    <label class="required form-label">Severity</label>
                                    <select class="form-select" name="severity">
                                        <option value="low" <?php if($severity == 'low') { echo 'selected';} ?>>Low</option>
                                        <option value="medium" <?php if($severity == 'medium') { echo 'selected';} ?>>Medium</option>
                                        <option value="high" <?php if($severity == 'high') { echo 'selected';} ?>>High</option>
                                    </select>
                                </div>

                                <div class="row mb-7">
                                    <div class="col-md-6">
                                        <label class="form-label">Notify To</label>
                                        <select class="form-select" name="notify_to" data-control="select2" multiple name="notify_to[]">
                                            <option value="Department Head" <?php if($notify_to == 'Department Head') { echo 'selected';} ?>>Department Head</option>
                                            <option value="Admin" <?php if($notify_to == 'Admin') { echo 'selected';} ?>>Admin</option>
                                            <option value="Owner" <?php if($notify_to == 'Owner') { echo 'selected';} ?>>Owner</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Escalation Rule</label>
                                        <select class="form-select" name="escalation">
                                            <option value="3" <?php if($escalation_after == 3) { echo 'selected';} ?>>3 violations in 30 days</option>
                                            <option value="5" <?php if($escalation_after == 5) { echo 'selected';} ?>>5 violations in 30 days</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Remarks -->
                                <div class="mb-7">
                                    <label class="form-label">Staff Remarks</label>
                                    <textarea class="form-control" rows="5" name="remarks" placeholder="Explain why this rule exists"><?php echo $remarks; ?></textarea>
                                </div>
                            </div>

                            <div class="card-footer d-flex justify-content-end">
                                <button type="button" class="btn btn-light me-3">Cancel</button>
                                <button type="submit" class="btn btn-primary"><i class="ki-outline ki-check fs-2"></i>
                                    Save Rule
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
<script>var hostUrl = "<?php echo $site_path; ?>/";</script>
<script src="<?php echo $site_path; ?>/assets/plugins/global/plugins.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/scripts.bundle.js"></script>
<script>
    $(document).ready(function () {

    $("#kt_create_role_form").submit(function (e) {

        e.preventDefault();

        let rule_name = $("#rule_name").val().trim();
        let scope_type = $("#scope_type").val();
        let metric_code = $("#metric_code").val();
        let operator = $("#operator").val();
        let threshold_value = $("#threshold_value").val();

        if(rule_name == ''){
            toastr.error('Rule Name is required');
            $("#rule_name").focus();
            return false;
        }

        if(scope_type == ''){
            toastr.error('Please select scope');
            return false;
        }

        if(metric_code == ''){
            toastr.error('Please select metric');
            return false;
        }

        if(operator == ''){
            toastr.error('Please select operator');
            return false;
        }

        if(threshold_value == ''){
            toastr.error('Threshold value is required');
            return false;
        }

        $("#saveBtn").attr("data-kt-indicator","on");
        $("#saveBtn").prop("disabled",true);

        $.ajax({

            url: "<?php echo $site_path; ?>/ajax/ajax-save-rule",
            type: "POST",
            data: $("#kt_create_role_form").serialize(),
            dataType: "json",

            success: function(response){

                $("#saveBtn").removeAttr("data-kt-indicator");
                $("#saveBtn").prop("disabled",false);

                if(response.status == 1){

                    toastr.success(response.message);

                    setTimeout(function(){
                        window.location.href = "<?php echo $site_path; ?>/rule-book-list";
                    },1000);

                }else{

                    toastr.error(response.message);
                }
            },

            error:function(){

                $("#saveBtn").removeAttr("data-kt-indicator");
                $("#saveBtn").prop("disabled",false);

                toastr.error('Something went wrong');
            }

        });

    });

});
</script>
</body>
</html>