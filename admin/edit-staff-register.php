<?php
include("config/database.php");
include("config/auth_check.php");
include("includes/sidemenu.php");

$emp_id = generate_staff_code();
$department_list = getAllDepartments();
$role_list = getUniqueRoles();
$employment_type_list = get_employment_type_list();
$id = my_simple_crypt($_GET['id'], 'decrypt_1');
if ($id > 0) {
    $stmt = $con->prepare("SELECT * FROM staff_register WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $employee_code = $row['employee_code'];
        $firstname = $row['firstname'];
        $middlename = $row['middlename'];
        $lastname = $row['lastname'];
        $mobile_number = $row['mobile_number'];
        $email = $row['email'];
        $gender = $row['gender'];
        $address = $row['address'];
        $dob = $row['dob'];
        $doj = $row['doj'];
        $employment_type = $row['employment_type'];
        $contract_start = $row['contract_start'];
        $contract_end = $row['contract_end'];
        $profile_picture = $row['profile_picture'];
        $emergancy_contact_person = $row['emergancy_contact_person'];
        $emergancy_contact_number = $row['emergancy_contact_number'];
        $department_id  = $row['department_id'];
        $is_department_head  = $row['is_department_head'];
        $role_id  = $row['role_id'];
        $work_location  = $row['work_location'];
        $is_bond_applicable  = $row['is_bond_applicable'];
        $bond_tenure  = $row['bond_tenure'];
        $bond_amount  = $row['bond_amount'];
        $bond_doc  = $row['bond_doc'];
        $prevoius_work_history  = $row['prevoius_work_history'];
        $increment_basis  = $row['increment_basis'];
        $first_increment_after  = $row['first_increment_after'];
        $increment_frequency  = $row['increment_frequency'];
        $periodic_reminder_days  = $row['periodic_reminder_days'];
        $review_cycle  = $row['review_cycle'];
        $performance_score  = $row['performance_score'];
        $performance_reminder_days  = $row['performance_reminder_days'];
        $blood_group  = $row['blood_group'];
        $aadhar_number  = $row['aadhar_number'];
        $aadhaar_name  = $row['aadhaar_name'];
        $aadhar_front_image  = $row['aadhar_front_image'];
        $aadhar_back_image  = $row['aadhar_back_image'];
    }

    $stmt->close();

}
?>
<link href="<?php echo $site_path; ?>/assets/css/staff-register.css?v=<?php echo time(); ?>" rel="stylesheet" type="text/css" />
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading text-gray-900 fw-bold fs-3 my-0">
                        Staff Registration
                    </h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="<?php echo $site_path; ?>/dashboard" class="text-muted text-hover-primary">
                                Home
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">
                            Staff Registration 
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                <form id="updateForm" class="form staff-form" action="<?php echo $site_path; ?>/ajax/add-update-staff-details">
                    <input type="hidden" name="previous_employers" id="previous_employers">
                    <input type="hidden" name="staff_id" value="<?php echo $id; ?>"/>
                    <input type="hidden" name="redirect_url" value="<?php echo $site_path; ?>/staff-registry"/>
                    <div class="card" id="sec1">
                        <div class="card-header">
                            <div class="card-header-left">
                                <div class="section-badge">1</div>
                                <span class="card-title">Personal Details</span>
                            </div>
                            <span class="badge-req">Required</span>
                        </div>
                        <div class="card-body">
                            <div class="row col-3">
                                <div class="fv-row field mb-4">
                                    <label>First Name <span class="req">*</span></label>
                                    <input type="text" id="firstName" name="firstName" placeholder="e.g. Valentina" class="form-control form-control-lg form-control-solid" value="<?php echo $firstname; ?>"/>
                                    <span class="field-error">First name is required</span>
                                </div>
                                <div class="fv-row field mb-4">
                                    <label>Middle Name</label>
                                    <input type="text" id="middleName" name="middleName" placeholder="Middle Name" class="form-control form-control-lg form-control-solid" value="<?php echo $middlename; ?>"/>
                                </div>
                                <div class="fv-row field mb-4">
                                    <div class="fav-row field mb-4">
                                        <label>Last Name <span class="req">*</span></label>
                                        <input type="text" id="lastname" name="lastname" placeholder="Last Name" class="form-control form-control-lg form-control-solid" value="<?php echo $lastname; ?>"/>
                                    </div>
                                </div>
                            </div>
                            <div class="row col-3">
                                <div class="field fv-row mb-4">
                                    <label>Date of Birth <span class="req">*</span></label>
                                    <input type="date" id="dob" name="dob" class="form-control form-control-lg form-control-solid" value="<?php echo $dob; ?>"/>
                                </div>
                                <div class="field fv-row mb-4">
                                    <label>Gender <span class="req">*</span></label>
                                    <select id="gender" name="gender" class="form-control form-control-lg form-control-solid">
                                        <option value="">Select Gender</option>
                                        <option value="Male" <?php if($gender == 'Male') { echo 'selected';} ?>>Male</option>
                                        <option value="Female" <?php if($gender == 'Female') { echo 'selected';} ?>>Female</option>
                                        <option value="Other" <?php if($gender == 'Other') { echo 'selected';} ?>>Female</option>
                                    </select>
                                    <span class="field-error">Gender is required</span>
                                </div>
                                <div class="field fv-row mb-4">
                                    <label>Blood Group</label>
                                    <select id="blood_group" name="blood_group" class="form-control form-control-lg form-control-solid">
                                        <option value="">Select</option>
                                        <option value="A+" <?php if($blood_group == 'A+') { echo 'selected';} ?>>A+</option>
                                        <option value="A-" <?php if($blood_group == 'A-') { echo 'selected';} ?>>A-</option>
                                        <option value="B+" <?php if($blood_group == 'B+') { echo 'selected';} ?>>B+</option>
                                        <option value="B-" <?php if($blood_group == 'B-') { echo 'selected';} ?>>B-</option>
                                        <option value="O+" <?php if($blood_group == 'O+') { echo 'selected';} ?>>O+</option>
                                        <option value="O-" <?php if($blood_group == 'O-') { echo 'selected';} ?>>O-</option>
                                        <option value="AB+" <?php if($blood_group == 'AB+') { echo 'selected';} ?>>AB+</option>
                                        <option value="AB-" <?php if($blood_group == 'AB-') { echo 'selected';} ?>>AB-</option>
                                    </select>
                                    <span class="field-error">Gender is required</span>
                                </div>
                            </div>
                            <div class="row col-2">
                                <div class="field fv-row mb-4" id="f-mobile1">
                                    <label>Mobile <span class="req">*</span></label>
                                    <input type="tel" id="mobile1" name="mobile1" placeholder="+91 98765 43210" class="form-control form-control-lg form-control-solid" value="<?php echo $mobile_number; ?>"/>
                                    <span class="field-error">Mobile Number is required</span>
                                </div>
                                 <div class="field fv-row mb-4"  id="f-email">
                                     <label>Email</label>
                                    <input type="email" id="email" name="email" placeholder="model@email.com" class="form-control form-control-lg form-control-solid" value="<?php echo $email; ?>"/>
                                    <span class="field-error">Enter a valid email address</span>
                                </div>
                            </div>
                            <div class="row col-full">
                                <div class="field fv-row mb-2">
                                    <label>Current Address <span class="req">*</span></label>
                                    <textarea id="address" name="address" placeholder="Flat / House No., Building, Street, Area…" rows="2" class="form-control form-control-lg form-control-solid"><?php echo $address; ?></textarea>
                                </div>
                            </div>
                            <div class="row col-2">
                                <div class="fv-row field mb-2">
                                    <label>Emergency contact name <span class="req">*</span></label>
                                    <input type="text" id="emergancy_name" name="emergancy_name" placeholder="Emergancy Contact Name" class="form-control form-control-lg form-control-solid" value="<?php echo $emergancy_contact_number; ?>">
                                </div>
                                <div class="fv-row field mb-4">
                                    <label>Emergency contact phone <span class="req">*</span></label>
                                    <input type="text" id="emergancy_phone" name="emergancy_phone" placeholder="Emergancy Contact Phone" class="form-control form-control-lg form-control-solid" value="<?php echo $emergancy_contact_number; ?>"/>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card" id="sec2">
                        <div class="card-header">
                            <div class="card-header-left">
                                <div class="section-badge">2</div>
                                <span class="card-title">Employment Details</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row col-2">
                                <div class="field fv-row mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="mb-0">
                                            Department <span class="req">*</span>
                                        </label>
                                        <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#departmentModal" class="quick-add-link"> + Quick Add</a>
                                    </div>

                                    <select id="department_id" name="department_id" class="form-control form-control-lg form-control-solid">
                                        <option value="">Select department</option>
                                        <?php foreach($department_list as $dept){ ?>
                                            <option value="<?= $dept['id'] ?>" <?php if($department_id == $dept['id']) { echo 'selected';} ?>>
                                                <?= $dept['department_name'] ?>
                                            </option>
                                        <?php } ?>
                                    </select>

                                </div>
                                <div class="field fv-row mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label>Role / Designation <span class="req">*</span></label>
                                        <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#roleModal" class="quick-add-link"> + Quick Add</a>
                                    </div>
                                    <select id="role_id" name="role_id" class="form-control form-control-lg form-control-solid">
                                        <option value="">Select role</option>
                                        <?php if($role_list){
                                                foreach($role_list as $single_role){?>
                                                    <option value="<?php echo $single_role['id']; ?>" <?php if($role_id == $single_role['id']) { echo 'selected';} ?>><?php echo $single_role['role_name']; ?></option>
                                        <?php } } ?>
                                    </select>
                                    <span class="field-error">Gender is required</span>
                                </div>
                            </div>
                            <div class="row col-2">
                                <div class="field fv-row mb-4">
                                    <label>Is Department Head <span class="req">*</span></label>
                                    <div class="increment-tabs">
                                        <button type="button" class="dept-head <?php if($is_department_head == 1) { echo 'active';} ?>" data-type="yes" >
                                            Yes — dept head
                                        </button>

                                        <button type="button" class="<?php if($is_department_head == 0) { echo 'active';} ?> dept-head" data-type="no">
                                            No
                                        </button>
                                    </div>
                                </div>
                                <div class="field fv-row mb-4">
                                    <div class="field fv-row mb-4">
                                    <label>Date of Join <span class="req">*</span></label>
                                    <input type="date" id="doj" name="doj" class="form-control form-control-lg form-control-solid" value="<?php echo $doj; ?>"/>
                                </div>
                                </div>
                            </div>
                            <div class="row col-2">
                                <div class="field fv-row mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label>Employment <span class="req">*</span></label>
                                        <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#employmentTypeModal" class="quick-add-link"> + Quick Add</a>
                                    </div>
                                    <select id="employment_type" name="employment_type" class="form-control form-control-lg form-control-solid">
                                        <?php if($employment_type_list){
                                                foreach($employment_type_list as $single_val){?>
                                                    <option value="<?php echo $single_val['id']; ?>" <?php if($employment_type == $single_val['id']) { echo 'selected';} ?>><?php echo $single_val['name']; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="field fv-row mb-4">
                                    <label>Work location</label>
                                    <select id="work_location" name="work_location" class="form-control form-control-lg form-control-solid">
                                        <option <?php if($work_location == 'BullionKnot Factory, Surat') { echo 'selected';} ?>>BullionKnot Factory, Surat</option>
                                        <option <?php if($work_location == 'BullionKnot Office, Surat') { echo 'selected';} ?>>BullionKnot Office, Surat</option>
                                        <option <?php if($work_location == 'Remote (with approval)') { echo 'selected';} ?>>Remote (with approval)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card" id="sec3">
                        <div class="card-header">
                            <div class="card-header-left">
                                <div class="section-badge">3</div>
                                <span class="card-title">Profile Photo</span>
                            </div>
                            <span class="badge-opt">Used for Face Recognition login</span>
                        </div>
                        <div class="card-body">
                            <div class="row col-3">
                                <div class="field mb-4">
                                    <label>Profile Picture <span style="font-weight:400;font-size:10px;color:var(--text-3)">(PNG / JPG · max 2 MB)</span></label>
                                    <label class="upload-zone" style="padding:14px;cursor:pointer;">
                                        <input type="file" id="profile_picture" name="profile_picture" accept=".png,.jpg,.jpeg" style="position:absolute;opacity:0;width:100%;height:100%"/>
                                        <div class="upload-icon">📷</div>
                                        <div class="upload-title">Upload ID Proof</div>
                                    </label>
                                    <div id="profile-preview">
                                        <?php if(!empty($profile_picture)){ ?>
                                                <img
                                                    src="<?php echo $define_company_website; ?>/uploads/staff/<?php echo $profile_picture; ?>"
                                                    alt="Profile Picture"
                                                    style="max-width:200px;
                                                           border:1px solid #ddd;
                                                           border-radius:8px;
                                                           padding:5px;">
                                            <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card" id="sec4">
                        <div class="card-header">
                            <div class="card-header-left">
                                <div class="section-badge">4</div>
                                <span class="card-title">Adhar Details</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row col-2">
                                <div class="fv-row field mb-4">
                                    <label>Aadhaar number <span class="req">*</span></label>
                                    <input type="text" id="aadhaar_no" name="aadhaar_no" placeholder="XXX XXX XXX" maxlength="14" class="form-control form-control-lg form-control-solid" value="<?php echo $aadhar_number; ?>"/>
                                </div>
                                <div class="fv-row field mb-4">
                                    <label>Name as on Aadhaar <span class="req">*</span></label>
                                    <input type="text" id="aadhaar_name" name="aadhaar_name" placeholder="Aadhar Name" class="form-control form-control-lg form-control-solid" value="<?php echo $aadhaar_name; ?>"/>
                                </div>
                            </div>
                            <div class="row col-2">
                                <div class="field mb-4">
                                    <label>Aadhaar document upload <span style="font-weight:400;font-size:10px;color:var(--text-3)">(PNG / JPG · max 2 MB)</span></label>
                                    <label class="upload-zone" style="padding:14px;cursor:pointer;">
                                        <input type="file" id="front_aadhar" name="front_aadhar" accept=".png,.jpg,.jpeg" style="position:absolute;opacity:0;width:100%;height:100%"/>
                                        <div class="upload-icon">📷</div>
                                        <div class="upload-title">Upload Aadhar Front</div>
                                    </label>
                                    <div id="adhar-front-preview">
                                        <?php if(!empty($aadhar_front_image)){ ?>
                                                <img
                                                    src="<?php echo $define_company_website; ?>/uploads/staff/documents/<?php echo $aadhar_front_image; ?>"
                                                    alt="Profile Picture"
                                                    style="max-width:200px;
                                                           border:1px solid #ddd;
                                                           border-radius:8px;
                                                           padding:5px;">
                                            <?php } ?>
                                    </div>
                                </div>
                                <div class="field mb-4">
                                    <label><span style="font-weight:400;font-size:10px;color:var(--text-3)">(PNG / JPG · max 2 MB)</span></label>
                                    <label class="upload-zone" style="padding:14px;cursor:pointer;">
                                        <input type="file" id="back_aadhar" name="back_aadhar" accept=".png,.jpg,.jpeg" style="position:absolute;opacity:0;width:100%;height:100%"/>
                                        <div class="upload-icon">📷</div>
                                        <div class="upload-title">Upload Aadhar Back</div>
                                    </label>
                                    <div id="adhar-back-preview">
                                        <?php if(!empty($aadhar_back_image)){ ?>
                                                <img
                                                    src="<?php echo $define_company_website; ?>/uploads/staff/documents/<?php echo $aadhar_back_image; ?>"
                                                    alt="Profile Picture"
                                                    style="max-width:200px;
                                                           border:1px solid #ddd;
                                                           border-radius:8px;
                                                           padding:5px;">
                                            <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card" id="sec5">
                        <div class="card-header">
                            <div class="card-header-left">
                                <div class="section-badge">5</div>
                                <span class="card-title">Bond Details</span>
                            </div>
                        </div>

                        <div class="card-body">
                            <!-- Toggle -->
                            <div class="d-flex justify-content-between align-items-center mb-5 border-bottom pb-5">
                                <div>
                                    <h5 class="mb-1">Bond applicable for this staff?</h5>
                                    <small class="text-muted">
                                        Toggle on only if a service bond is signed
                                    </small>
                                </div>

                                <div class="form-check form-switch form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" id="bondApplicable" name="bondApplicable" <?= ($is_bond_applicable == 1) ? 'checked' : ''; ?>>
                                </div>
                            </div>

                            <!-- Hidden by default -->
                            <div id="bondFields" style="display:<?php if($is_bond_applicable == 1) { echo 'block';} else { echo 'none';} ?>;">
                                <div class="row col-2">

                                    <div class="field mb-4">
                                        <label>Bond Start Date <span class="req">*</span></label>
                                        <input type="date" name="bond_start_date" id="bond_start_date" class="form-control form-control-lg form-control-solid" value="<?php echo $contract_start; ?>">
                                    </div>

                                    <div class="field mb-4">
                                        <label>Bond End Date <span class="req">*</span></label>
                                        <input type="date" name="bond_end_date" id="bond_end_date" class="form-control form-control-lg form-control-solid" value="<?php echo $contract_end; ?>">
                                    </div>
                                </div>

                                <div class="row col-2">

                                    <div class="field mb-4">
                                        <label>Bond Tenure</label>
                                        <select name="bond_tenure" id="bond_tenure" class="form-control form-control-lg form-control-solid">
                                            <option value="">Select</option>
                                            <option value="6 Months" <?php if($bond_tenure == '6 Months'){ echo 'selected';} ?>>6 Months</option>
                                            <option value="1 Year" <?php if($bond_tenure == '1 Year'){ echo 'selected';} ?>>1 Year</option>
                                            <option value="2 Years" <?php if($bond_tenure == '2 Years'){ echo 'selected';} ?>>2 Years</option>
                                            <option value="3 Years" <?php if($bond_tenure == '3 Years'){ echo 'selected';} ?>>3 Years</option>
                                        </select>
                                    </div>

                                    <div class="field mb-4">
                                        <label>Bond Amount (₹)</label>
                                        <input type="number" name="bond_amount" id="bond_amount" placeholder="e.g. 50000" class="form-control form-control-lg form-control-solid" value="<?php echo $bond_amount; ?>">
                                    </div>
                                </div>

                                <div class="row col-2">
                                    <div class="field mb-4">
                                        <label>Bond Document<span style="font-weight:400;font-size:10px;color:var(--text-3)">(PNG / JPG · max 2 MB)</span></label>
                                        <label class="upload-zone" style="padding:14px;cursor:pointer;">
                                            <input type="file" id="bond_doc" name="bond_doc" accept=".png,.jpg,.jpeg" style="position:absolute;opacity:0;width:100%;height:100%"/>
                                            <div class="upload-icon">📷</div>
                                            <div class="upload-title">Upload Signed Bond</div>
                                        </label>
                                        <div id="bond-doc-preview">
                                            <?php if(!empty($bond_doc)){ ?>
                                                <img
                                                    src="<?php echo $define_company_website; ?>/uploads/staff/documents/<?php echo $bond_doc; ?>"
                                                    alt="Profile Picture"
                                                    style="max-width:200px;
                                                           border:1px solid #ddd;
                                                           border-radius:8px;
                                                           padding:5px;">
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-warning">
                                    Auto-alert will be sent 30 days before bond expiry.
                                </div>

                            </div>

                        </div>
                    </div>
                    <div class="card" id="sec6">
                        <div class="card-header">
                            <div class="card-header-left">
                                <div class="section-badge">6</div>
                                <span class="card-title">Previous Work History</span>
                            </div>
                            <span class="badge-opt">Optional · Add multiple employers</span>
                        </div>

                        <div class="card-body">
                            <div class="alert-work-history">
                                <strong>Why we collect this</strong><br>
                                Work history helps assess experience and is referenced during appraisals.
                                Visible to Admin and HR only.
                            </div>
                            <div id="employerList">
                                <?php if($prevoius_work_history){
                                            $work_history = json_decode($prevoius_work_history,true);
                                            foreach($work_history as $single_history){?>
                                                <div class="employer-card">
                                                    <h5><?php echo $single_history['employer_name']; ?></h5>
                                                    <div>
                                                        Role: <?php echo $single_history['designation']; ?>
                                                    </div>
                                                    <div>
                                                        Duration: <?php echo $single_history['duration']; ?>
                                                    </div>
                                                    <div>
                                                        Left: <?php echo $single_history['reason']; ?>
                                                    </div>
                                                </div>
                               <?php             
                               }
                                } ?>
                            </div>
                            <button type="button" class="btn-add-employer" data-bs-toggle="modal" data-bs-target="#previousEmployerModal"> <i class="fas fa-plus"></i> Add previous employer</button>
                        </div>
                    </div>
                    <div class="card" id="sec7">
                        <div class="card-header">
                            <div class="card-header-left">
                                <div class="section-badge">7</div>
                                <span class="card-title">Increment Reminder Setup</span>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="alert-work-history mb-5">
                                Increment reminders appear during salary payout time and in the Finance module for salary cost calculations.
                            </div>

                            <div class="field mb-4">
                                <label>Increment basis <span class="text-danger">*</span></label>
                                <div class="increment-tabs">
                                    <button type="button" class="increment-btn <?= ($increment_basis == 'periodic') ? 'active' : '' ?>" data-type="periodic">
                                        Periodic — fixed time intervals
                                    </button>

                                    <button type="button" class="increment-btn <?= ($increment_basis == 'performance') ? 'active' : '' ?>" data-type="performance">
                                        Performance-based
                                    </button>
                                </div>

                                <input type="hidden" name="increment_basis" id="increment_basis" value="<?= $increment_basis ?>">
                                <input type="hidden" name="is_dept_head" id="is_dept_head" value="0">
                            </div>

                            <!-- Periodic Section -->
                            <div id="periodicSection" style="display:<?= ($increment_basis == 'periodic') ? '' : 'none' ?>">
                                <div class="row col-3">
                                    <div class="field mb-4">
                                        <label>First increment after *</label>
                                        <select name="first_increment_after" class="form-control form-control-lg form-control-solid">
                                            <option value="">Select</option>
                                            <option value="3 Months" <?php if($first_increment_after == '3 Months') { echo 'selected';}  ?>>3 Months</option>
                                            <option value="6 Months" <?php if($first_increment_after == '6 Months') { echo 'selected';}  ?>>6 Months</option>
                                            <option value="12 Months" <?php if($first_increment_after == '12 Months') { echo 'selected';}  ?>>12 Months</option>
                                        </select>
                                    </div>

                                    <div class="field mb-4">
                                        <label>Then every *</label>
                                        <select name="increment_frequency" class="form-control form-control-lg form-control-solid">
                                            <option value="">Select</option>
                                            <option value="6 Months" <?php if($increment_frequency == '6 Months') { echo 'selected';}?>>6 Months</option>
                                            <option value="12 Months" <?php if($increment_frequency == '12 Months') { echo 'selected';}?>>12 Months</option>
                                            <option value="24 Months" <?php if($increment_frequency == '24 Months') { echo 'selected';}?>>24 Months</option>
                                        </select>
                                    </div>

                                    <div class="field mb-4">
                                        <label>Remind Admin before (days)</label>
                                        <select name="periodic_reminder_days" class="form-control form-control-lg form-control-solid">
                                            <option value="">Select</option>
                                            <option value="7" <?php if($periodic_reminder_days == '7'){ echo 'selected';} ?>>7 Days</option>
                                            <option value="15" <?php if($periodic_reminder_days == '15'){ echo 'selected';} ?>>15 Days</option>
                                            <option value="30" <?php if($periodic_reminder_days == '30'){ echo 'selected';} ?>>30 Days</option>
                                        </select>
                                    </div>

                                </div>

                            </div>

                            <!-- Performance Section -->
                            <div id="performanceSection" style="display:<?= ($increment_basis == 'performance') ? '' : 'none' ?>">
                                <div class="row col-2">
                                    <div class="field mb-4">
                                        <label>Review cycle</label>
                                        <select name="review_cycle" class="form-control form-control-lg form-control-solid">
                                            <option value="">Select</option>
                                            <option value="Every 3 Months" <?php if($review_cycle == 'Every 3 Months'){echo 'selected';} ?>>Every 3 Months</option>
                                            <option value="Every 6 Months" <?php if($review_cycle == 'Every 6 Months'){echo 'selected';} ?>>Every 6 Months</option>
                                            <option value="Every 12 Months" <?php if($review_cycle == 'Every 12 Months'){echo 'selected';} ?>>Every 12 Months</option>
                                        </select>
                                    </div>

                                    <div class="field mb-4">
                                        <label>Minimum performance score</label>
                                        <select name="performance_score" class="form-control form-control-lg form-control-solid">
                                            <option value="">Select</option>
                                            <option value="60%" <?php if($performance_score == '60%'){echo 'selected';} ?>>60% and above</option>
                                            <option value="70%" <?php if($performance_score == '70%'){echo 'selected';} ?>>70% and above</option>
                                            <option value="80%" <?php if($performance_score == '80%'){echo 'selected';} ?>>80% and above</option>
                                            <option value="90%" <?php if($performance_score == '90%'){echo 'selected';} ?>>90% and above</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row col-1">
                                    <div class="field mb-4">
                                        <label>Remind Admin before review (days)</label>
                                        <select name="performance_reminder_days" class="form-control form-control-lg form-control-solid">
                                            <option value="">Select</option>
                                            <option value="7" <?php if($performance_reminder_days == '7'){echo 'selected';} ?>>7 Days</option>
                                            <option value="15" <?php if($performance_reminder_days == '15'){echo 'selected';} ?>>15 Days</option>
                                            <option value="30" <?php if($performance_reminder_days == '30'){echo 'selected';} ?>>30 Days</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="increment-alert">
                                🔔 Reminder sent to: Admin, HR Head, Finance module salary cost view.
                            </div>
                        </div>
                    </div>

                    <div class="action-bar">
                        <div class="action-bar-left">
                            <button type="button" class="btn" onclick="discardForm()">Discard</button>
                        </div>
                        <div class="action-bar-right">
                            <button type="submit" class="btn btn-primary" id="kt_update_staff_details_submit">Update Changes</button>
                        </div>
                    </div>
                </form>

                <div class="toast" id="formToast"></div>
            </div>
        </div>
        <?php include("includes/footer.php"); ?>
    </div>
</div>
<div class="modal fade" id="previousEmployerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Add Previous Employer</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-4">
                    <label>Employer Name *</label>
                    <input type="text" class="form-control" id="employer_name">
                </div>

                <div class="mb-4">
                    <label>Address</label>
                    <textarea class="form-control" id="employer_address"></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label>Designation Held</label>
                        <input type="text" class="form-control" id="designation">
                    </div>

                    <div class="col-md-6">
                        <label>Duration</label>
                        <input type="text" class="form-control" id="duration" placeholder="Jan 2022 - Mar 2024">
                    </div>
                </div>

                <div class="mt-4">
                    <label>Last Drawn Salary</label>
                    <input type="number" class="form-control" id="salary">
                </div>

                <div class="mt-4">
                    <label>Reason For Leaving *</label>
                    <select class="form-select" id="reason">
                        <option value="">Select Reason</option>
                        <option>Better Opportunity</option>
                        <option>Salary Issue</option>
                        <option>Relocation</option>
                        <option>Personal Reason</option>
                    </select>
                </div>

                <div class="mt-4">
                    <label>Additional Notes</label>
                    <input type="text" class="form-control" id="notes">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" id="saveEmployer">Add Employer</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="departmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Add Department</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-4">
                    <label>Department Name</label>
                    <input type="text" id="department_name" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    Cancel
                </button>
                <button type="button" class="btn btn-primary" id="saveDepartment">
                    Save
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="roleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Add Role</h3>
            </div>
            <div class="modal-body">
                <input type="text" id="role_name" class="form-control" placeholder="Role Name">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="saveRole">Save</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="employmentTypeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Add Employment Type</h3>
            </div>
            <div class="modal-body">
                <input type="text" id="employment_type_name" class="form-control" placeholder="Employment Type">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="saveEmploymentType">
                    Save
                </button>
            </div>
        </div>
    </div>
</div>
<script>var hostUrl = "<?php echo $site_path; ?>/";</script>
<script src="<?php echo $site_path; ?>/assets/plugins/global/plugins.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/scripts.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/custom/update-staff.js?v=<?php echo time(); ?>"></script>
<script>
    $(document).on('change', '#bondApplicable', function () {
        if ($(this).is(':checked')) {
            $('#bondFields').slideDown();
            $('#bond_start_date').prop('required', true);
            $('#bond_end_date').prop('required', true);
        } else {
            $('#bondFields').slideUp();
            $('#bond_start_date').prop('required', false);
            $('#bond_end_date').prop('required', false);
            $('#bondFields')
                .find('input, select, textarea')
                .not('#bondApplicable')
                .val('');
        }
    });
    document.getElementById('profile_picture').addEventListener('change', function () {
        const file = this.files[0];
        const preview = document.getElementById('profile-preview');
        preview.innerHTML = '';
        if (!file) return;

        // Validate size (2 MB)
        if (file.size > 2 * 1024 * 1024) {
            alert('File size must be less than 2 MB');
            this.value = '';
            return;
        }
        const reader = new FileReader();
        reader.onload = function (e) {
            preview.innerHTML = `
                <img src="${e.target.result}" alt="Profile Preview" style="max-width:200px; max-height:200px; border:1px solid #ddd; border-radius:8px; padding:5px;">`;
        };
        reader.readAsDataURL(file);
    });
    
    let employers = [];

    $('#saveEmployer').on('click', function(){

        let employer = {
            employer_name : $('#employer_name').val(),
            employer_address : $('#employer_address').val(),
            designation : $('#designation').val(),
            duration : $('#duration').val(),
            salary : $('#salary').val(),
            reason : $('#reason').val(),
            notes : $('#notes').val()
        };

        if(employer.employer_name == ''){
            alert('Employer Name Required');
            return false;
        }

        if(employer.reason == ''){
            alert('Reason Required');
            return false;
        }

        employers.push(employer);

        renderEmployers();

        $('#previous_employers').val(
            JSON.stringify(employers)
        );

        $('#previousEmployerModal').modal('hide');

        $('#previousEmployerModal')
            .find('input,textarea,select')
            .val('');
    });

    function renderEmployers(){

        let html = '';

        employers.forEach(function(emp,index){

            html += `
            <div class="employer-card">

                <span class="remove-employer"
                      onclick="removeEmployer(${index})">
                      ✕
                </span>

                <h5>${emp.employer_name}</h5>

                <div>
                    Role: ${emp.designation}
                </div>

                <div>
                    Duration: ${emp.duration}
                </div>

                <div>
                    Left: ${emp.reason}
                </div>

            </div>`;
        });

        $('#employerList').html(html);
    }

    function removeEmployer(index){

        employers.splice(index,1);

        renderEmployers();

        $('#previous_employers').val(
            JSON.stringify(employers)
        );
    }
    
    $(document).on('click', '.dept-head', function(){

    $('.dept-head').removeClass('active');
        $(this).addClass('active');
        let type = $(this).data('type');
        $('#is_dept_head').val(type);
    });

$(document).on('click', '.increment-btn', function(){
    $('.increment-btn').removeClass('active');
    $(this).addClass('active');

    let type = $(this).data('type');

    $('#increment_basis').val(type);

    if(type == 'periodic'){

        $('#periodicSection').show();
        $('#performanceSection').hide();

    }else{

        $('#periodicSection').hide();
        $('#performanceSection').show();
    }
});
$('#saveDepartment').click(function(){
    let department_name = $('#department_name').val();
    if(department_name == ''){
        alert('Enter Department Name');
        return false;
    }
    $.ajax({
        url: '<?php echo $site_path; ?>/ajax/add-update-department',
        type: 'POST',
        dataType:'json',
        data:{
            action : 'add-dept',
            department_name: department_name
        },
        success:function(res){
            if(res.status == 'success'){
                $('#department_id').append(
                    `<option value="${res.id}" selected>
                        ${department_name}
                    </option>`
                );
                $('#departmentModal').modal('hide');
                $('#department_name').val('');
            }else{
                alert(res.message);
            }
        }
    });

});
$('#saveRole').click(function(){
    let role_name = $('#role_name').val();
    if(role_name == ''){
        alert('Enter Role Name');
        return false;
    }
    $.ajax({
        url: '<?php echo $site_path; ?>/ajax/add-update-role',
        type:'POST',
        dataType:'json',
        data:{
            role_name: role_name,
            action : 'add_role'
        },
        success:function(res){
            $('#role_id').append(
                `<option value="${res.id}" selected>
                    ${$('#role_name').val()}
                </option>`
            );
            $('#roleModal').modal('hide');
        }
    });

});
$('#saveEmploymentType').click(function(){
    let employment_type_name = $('#employment_type_name').val();
    if(employment_type_name == ''){
        alert('Enter Employment Type Name');
        return false;
    }
    $.ajax({
        url: '<?php echo $site_path; ?>/ajax/add-update-employment-type',
        type:'POST',
        dataType:'json',
        data:{
            name: employment_type_name,
            action : 'add-employment-type'
        },
        success:function(res){
            $('#employment_type').append(
                `<option value="${res.id}" selected>
                    ${$('#employment_type_name').val()}
                </option>`
            );
            $('#employmentTypeModal').modal('hide');
        }
    });

});
</script>
