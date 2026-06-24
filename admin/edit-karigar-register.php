<?php
include("config/database.php");
include("config/auth_check.php");
include("includes/sidemenu.php");

$emp_id = generate_staff_code();
$banks = json_decode(file_get_contents('bank.json'), true);
$id = my_simple_crypt($_GET['id'], 'decrypt_1');
if ($id > 0) {
    $stmt = $con->prepare("SELECT * FROM karigar_registration WHERE id = ?");
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
        $gender = $row['gender'];
        $address = $row['address'];
        $dob = $row['dob'];
        $doj = $row['doj'];
        $job_type = $row['job_type'];
        $skills = $row['skills'];
        $reference_name = $row['reference_name'];
        $identity_proof = $row['identity_proof'];
        $identity_proof_number = $row['identity_proof_number'];
        $identity_proof_name = $row['identity_proof_name'];
        $identity_proof_front_doc = $row['identity_proof_front_doc'];
        $identity_proof_back_doc = $row['identity_proof_back_doc'];
        $bank_name = $row['bank_name'];
        $branch_name = $row['branch_name'];
        $ifsc_code = $row['ifsc_code'];
        $account_number = $row['account_number'];
        $qrcode = $row['qrcode'];
        $salary_type = $row['salary_type'];
        $monthly_salary = $row['monthly_salary'];
        $payment_date = $row['payment_date'];
        $work_start_time = $row['work_start_time'];
        $work_end_time = $row['work_end_time'];
        $break_duration = $row['break_duration'];
        $weekly_off = $row['weekly_off'];
        $holiday_remakrs = $row['holiday_remakrs'];
        $salary_remarks = $row['salary_remarks'];
        $avg_monthly_earning = $row['avg_monthly_earning'];
        $avg_pcs_per_day = $row['avg_pcs_per_day'];
        $working_day_per_month = $row['working_day_per_month'];
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
                        Update Karigar Registration Details
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
                            Karigar Registration 
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                <div class="registration-stepper">
                    <div class="step active" data-step="1">
                        <span class="step-no">01</span>
                        <span class="step-title">Basic Info</span>
                    </div>
                    <div class="step" data-step="2">
                        <span class="step-no">02</span>
                        <span class="step-title">Identity Proof</span>
                    </div>
                    <div class="step" data-step="3">
                        <span class="step-no">03</span>
                        <span class="step-title">Bank Details</span>
                    </div>
                    <div class="step" data-step="4">
                        <span class="step-no">04</span>
                        <span class="step-title">Salary Setup</span>
                    </div>
                    <div class="step" data-step="6">
                        <span class="step-no">05</span>
                        <span class="step-title">Review & Save</span>
                    </div>

                </div>
                <form id="regForm" class="form staff-form" action="<?php echo $site_path; ?>/ajax/add-update-karigar-details">
                    <input type="hidden" name="redirect_url" value="<?php echo $site_path; ?>/karigar-registration"/>
                    <input type="hidden" name="action" value="update-staff-details"/>
                    <input type="hidden" name="karigar_id" value="<?php echo $id; ?>"/>
                    <input type="hidden" name="front_adhar_proof" value="<?php echo $identity_proof_front_doc; ?>"/>
                    <input type="hidden" name="back_adhar_proof" value="<?php echo $identity_proof_back_doc; ?>"/>
                    <input type="hidden" name="old_qrcode" value="<?php echo $qrcode; ?>"/>
                    <div class="form-step active" data-step="1">
                        <div class="card" id="sec1">
                            <div class="card-header">
                                <div class="card-header-left">
                                    <div class="section-badge"><i class="ki-outline ki-profile-user fs-4"></i></div>
                                    <span class="card-title">Personal Details</span>
                                </div>
                                <span class="badge-req">Required</span>
                            </div>
                            <div class="card-body">
                                <div class="row col-3">
                                    <div class="fv-row field mb-4">
                                        <label>First Name <span class="req">*</span></label>
                                        <input type="text" id="firstName" name="firstName" placeholder="e.g. Valentina" class="form-control form-control-lg form-control-solid" value="<?php echo $firstname; ?>" required/>
                                        <span class="field-error">First name is required</span>
                                    </div>
                                    <div class="fv-row field mb-4">
                                        <label>Middle Name</label>
                                        <input type="text" id="middleName" name="middleName" placeholder="Middle Name" class="form-control form-control-lg form-control-solid" value="<?php echo $middlename; ?>"/>
                                    </div>
                                    <div class="fv-row field mb-4">
                                        <div class="fav-row field mb-4">
                                            <label>Last Name <span class="req">*</span></label>
                                            <input type="text" id="lastname" name="lastname" placeholder="Last Name" class="form-control form-control-lg form-control-solid" value="<?php echo $lastname; ?>" required/>
                                        </div>
                                    </div>
                                </div>
                                <div class="row col-3">
                                    <div class="field fv-row mb-4">
                                        <label>Date of Birth <span class="req">*</span></label>
                                        <input type="date" id="dob" name="dob" class="form-control form-control-lg form-control-solid" value="<?php echo $dob; ?>" required/>
                                    </div>
                                    <div class="field fv-row mb-4" id="f-gender">
                                        <label>Gender <span class="req">*</span></label>
                                        <select id="gender" name="gender" class="form-control form-control-lg form-control-solid">
                                            <option value="">Select Gender</option>
                                            <option value="Male" <?php if($gender == 'Male') { echo 'selected';} ?>>Male</option>
                                            <option value="Female" <?php if($gender == 'Female') { echo 'selected';} ?>>Female</option>
                                            <option value="Other" <?php if($gender == 'Other') { echo 'selected';} ?>>Female</option>
                                        </select>
                                        <span class="field-error">Gender is required</span>
                                    </div>
                                    <div class="field fv-row mb-4" id="f-mobile1">
                                        <label>Mobile <span class="req">*</span></label>
                                        <input type="tel" id="mobile1" name="mobile1" placeholder="+91 98765 43210" class="form-control form-control-lg form-control-solid" value="<?php echo $mobile_number; ?>" required/>
                                        <span class="field-error">Mobile Number is required</span>
                                    </div>
                                </div>
                                <div class="row col-full">
                                    <div class="field fv-row mb-2">
                                        <label>Current Address <span class="req">*</span></label>
                                        <textarea id="address" name="address" placeholder="Flat / House No., Building, Street, Area…" rows="2" class="form-control form-control-lg form-control-solid" required><?php echo $address; ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card" id="sec2">
                            <div class="card-header">
                                <div class="card-header-left">
                                    <div class="section-badge"><i class="ki-outline ki-briefcase fs-2"></i></div>
                                    <span class="card-title">Job Details</span>
                                </div>
                            </div>
                            <div class="card-body">
                            <div class="row col-2">
                                <div class="field fv-row mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="mb-0">
                                            Job Type <span class="req">*</span>
                                        </label>
                                        <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#departmentModal" class="quick-add-link"> + Quick Add</a>
                                    </div>
                                    <select id="job_type" name="job_type" class="form-control form-control-lg form-control-solid" required>
                                        <option value="">Select job type</option>
                                        <option value="1" <?php if($job_type == 1) { echo 'selected';} ?>>Farma Master</option>
                                        <option value="2" <?php if($job_type == 2) { echo 'selected';} ?>>Cutting Master</option>
                                        <option value="3" <?php if($job_type == 3) { echo 'selected';} ?>>Sampler</option>
                                    </select>
                                </div>
                                <div class="field fv-row mb-4">
                                    <div class="field fv-row mb-4">
                                    <label>Joing Date <span class="req">*</span></label>
                                    <input type="date" id="doj" name="doj" class="form-control form-control-lg form-control-solid" value="<?php echo $doj; ?>" required/>
                                </div>
                                </div>
                            </div>
                            <div class="row col-2">
                                <div class="field fv-row mb-4">
                                    <label>Speciality / Skills</label>
                                    <input type="text" id="speciality" name="speciality" placeholder="e.g. Zardozi, Smocking, Heavy embroidery" class="form-control form-control-lg form-control-solid" value="<?php echo $skills; ?>"/>
                                </div>
                                <div class="field fv-row mb-4">
                                    <label>Reference (who referred</label>
                                    <input type="text" id="reference_name" name="reference_name" placeholder="staff name or other karigar name" class="form-control form-control-lg form-control-solid" value="<?php echo $reference_name; ?>"/>
                                </div>
                                
                            </div>
                        </div>
                        </div>
                    </div>

                    <div class="form-step" data-step="2">
                        <div class="card" id="sec3">
                            <div class="card-header">
                                <div class="card-header-left">
                                    <div class="section-badge"><i class="ki-outline ki-shield-tick fs-2"></i></div>
                                    <span class="card-title">Identity Proof</span>
                                </div>
                            </div>
                            <div class="card-body">
                            <div class="row col-4">
                                <div class="field mb-4">
                                    <label>ID type <span class="text-danger">*</span></label>
                                    <div class="increment-tabs">
                                        <button type="button" class="increment-btn id_type <?php if($identity_proof == 'aadhar_card') { echo 'active';} ?>" data-type="aadhar_card">Aadhar Card</button>
                                        <button type="button" class="increment-btn id_type <?php if($identity_proof == 'driving_license') { echo 'active';} ?>" data-type="driving_license">Driving License</button>
                                        <button type="button" class="increment-btn id_type <?php if($identity_proof == 'voter_card') { echo 'active';} ?>" data-type="voter_card">Voter / Election Card</button>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="id_type" id="id_type" value="aadhar_card">
                            <div class="row col-2">
                                <div class="fv-row field mb-4">
                                    <label id="id_number_label">Aadhaar number <span class="req">*</span></label>
                                    <input type="text" id="aadhaar_no" name="aadhaar_no" placeholder="XXX XXX XXX" maxlength="14" class="form-control form-control-lg form-control-solid" value="<?php echo $identity_proof_number; ?>"/>
                                </div>
                                <div class="fv-row field mb-4">
                                    <label id="id_name_label">Name as on Aadhaar <span class="req">*</span></label>
                                    <input type="text" id="aadhaar_name" name="aadhaar_name" placeholder="Aadhar Name" class="form-control form-control-lg form-control-solid" value="<?php echo $identity_proof_name; ?>"/>
                                </div>
                            </div>
                            <div class="row col-2">
                                <div class="field mb-4">
                                    <label>Upload ID document <span style="font-weight:400;font-size:10px;color:var(--text-3)">(PNG / JPG · max 2 MB)</span></label>
                                    <label class="upload-zone" style="padding:14px;cursor:pointer;">
                                        <input type="file" id="front_id" name="front_id" accept=".png,.jpg,.jpeg" style="position:absolute;opacity:0;width:100%;height:100%"/>
                                        <div class="upload-icon">📷</div>
                                        <div class="upload-title">Upload ID Front</div>
                                    </label>
                                    <div id="front-doc-preview">
                                        <?php if(!empty($identity_proof_front_doc)){ ?>
                                            <img
                                                src="<?php echo $define_company_website; ?>/uploads/karigar/documents/<?php echo $identity_proof_front_doc; ?>"
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
                                        <input type="file" id="back_id" name="back_id" accept=".png,.jpg,.jpeg" style="position:absolute;opacity:0;width:100%;height:100%"/>
                                        <div class="upload-icon">📷</div>
                                        <div class="upload-title">Upload ID Back</div>
                                    </label>
                                    <div id="back-doc-preview">
                                        <?php if(!empty($identity_proof_back_doc)){ ?>
                                            <img
                                                src="<?php echo $define_company_website; ?>/uploads/karigar/documents/<?php echo $identity_proof_back_doc; ?>"
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

                        
                    </div>
                    <div class="form-step" data-step="3">
                        <div class="card" id="sec5">
                            <div class="card-header">
                                <div class="card-header-left">
                                    <div class="section-badge"> <i class="ki-outline ki-bank fs-2"></i></div>
                                    <span class="card-title">Bank Details</span>
                                </div>
                            </div>

                            <div class="card-body">
                            <div class="row col-2">
                                <div class="fv-row field mb-4">
                                    <label>Bank Name <span class="req">*</span></label>
                                    <select name="bank_name" id="bank_name" class="form-control form-control-lg form-control-solid">
                                        <option value="">Select Bank</option>
                                        <?php if($banks){
                                            foreach($banks as $single_bank){ ?>
                                                <option value="<?php echo $single_bank['name']; ?>" <?php if($bank_name == $single_bank['name']) { echo 'selected';} ?>><?php echo $single_bank['name']; ?></option>
                                            <?php }

                                            } ?>
                                        
                                    </select>
                                </div>
                                <div class="fv-row field mb-4">
                                    <label>Branch Name <span class="req">*</span></label>
                                    <input type="text" id="branch_name" name="branch_name" placeholder="Branch Name" class="form-control form-control-lg form-control-solid" value="<?php echo $branch_name ?>"/>
                                </div>
                            </div>
                            <div class="row col-3">
                                <div class="fv-row field mb-4">
                                    <label>IFSC Code <span class="req">*</span></label>
                                    <input type="text" id="ifsc_code" name="ifsc_code" placeholder="IFSC COde" maxlength="11" class="form-control form-control-lg form-control-solid" value="<?php echo $ifsc_code ?>"/>
                                    <small id="ifsc_msg"></small>
                                </div>
                                <div class="fv-row field mb-4">
                                    <label>Account Number <span class="req">*</span></label>
                                    <input type="text" id="account_number" name="account_number" placeholder="Account Number" class="form-control form-control-lg form-control-solid" value="<?php echo $account_number ?>"/>
                                    <small id="account_msg"></small>
                                </div>
                                
                                <div class="fv-row field mb-4">
                                    <label>Confirm Account Number <span class="req">*</span></label>
                                    <div class="position-relative">
                                        <input type="text" id="confirm_account_number" name="confirm_account_number" placeholder="Confirm Account Number" class="form-control form-control-lg form-control-solid"/>
                                        <span id="account_match_icon"
                                            style="position:absolute;right:15px;top:50%;transform:translateY(-50%);font-size:20px;">
                                        </span>
                                    </div>

                                    <small id="match_msg"></small>
                                </div>
                            </div>
                            <div class="row col-2">
                                    <div class="field mb-4">
                                        <label>UPI / QR code (optional)<span style="font-weight:400;font-size:10px;color:var(--text-3)">(PNG / JPG · max 2 MB)</span></label>
                                        <label class="upload-zone" style="padding:14px;cursor:pointer;">
                                            <input type="file" id="qrcode" name="qrcode" accept=".png,.jpg,.jpeg" style="position:absolute;opacity:0;width:100%;height:100%"/>
                                            <div class="upload-icon">📷</div>
                                            <div class="upload-title">Upload UPI QR Code</div>
                                        </label>
                                        <div id="qr-preview">
                                            <?php if(!empty($qrcode)){ ?>
                                            <img
                                                src="<?php echo $define_company_website; ?>/uploads/karigar/documents/<?php echo $qrcode; ?>"
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
                    </div>

                    <div class="form-step" data-step="4">
                        <div class="card" id="sec6">
                            <div class="card-header">
                                <div class="card-header-left">
                                    <div class="section-badge"><i class="ki-outline ki-wallet fs-2"></i></div>
                                    <span class="card-title">Salary Setup</span>
                                </div>
                                <span class="badge-opt">Optional · Add multiple employers</span>
                            </div>

                            <div class="card-body">
                            <div class="row col-4">
                                <div class="field mb-4">
                                    <label>Select salary type <span class="text-danger">*</span></label>
                                    <div class="increment-tabs">
                                        <button type="button" class="increment-btn <?php if($salary_type == 'fixed_monthly_salary') { echo 'active';} ?>" data-type="fixed_monthly_salary">Fixed monthly salary</button>
                                        <button type="button" class="increment-btn <?php if($salary_type == 'piece_rate') { echo 'active';} ?>" data-type="piece_rate">Piece rate</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Hidden by default -->
                            <div id="fixedMonthlySalary">
                                <div class="row col-2">
                                    <div class="field mb-4">
                                        <label>Monthly salary <span class="req">*</span></label>
                                        <input type="text" name="monthly_salary" id="monthly_salary" class="form-control form-control-lg form-control-solid" placeholder="e.g.15000" value="<?php echo $monthly_salary; ?>">
                                    </div>
                                    <input type="hidden" name="salary_type" id="salary_type" value="<?php echo $salary_type; ?>">
                                    <div class="field mb-4">
                                        <label>Payment date <span class="req">*</span></label>
                                        <select name="payment_date" id="payment_date" class="form-control form-control-lg form-control-solid">
                                            <option value="">Select</option>
                                            <option value="1st of every month" <?php if($payment_date == '1st of every month'){ echo 'selected';} ?>>1st of every month</option>
                                            <option value="5th of every month" <?php if($payment_date == '5th of every month'){ echo 'selected';} ?>>5th of every month</option>
                                            <option value="7th of every month" <?php if($payment_date == '7th of every month'){ echo 'selected';} ?>>7th of every month</option>
                                            <option value="Last working day" <?php if($payment_date == 'Last working day'){ echo 'selected';} ?>>Last working day</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row col-3">
                                    <div class="field mb-4">
                                        <label>Work start time</label>
                                            <input type="text" name="work_start_time" id="work_start_time" placeholder="" class="form-control form-control-lg form-control-solid" value="<?php echo $work_start_time; ?>">
                                        </select>
                                    </div>

                                    <div class="field mb-4">
                                        <label>Work end time</label>
                                        <input type="text" name="work_end_time" id="work_end_time" placeholder="" class="form-control form-control-lg form-control-solid" value="<?php echo $work_end_time; ?>">
                                    </div>
                                    
                                    <div class="field mb-4">
                                        <label>Break duration</label>
                                        <select name="break_duration" id="break_duration" class="form-control form-control-lg form-control-solid">
                                            <option value="">Select</option>
                                            <option value="No break" <?php if($break_duration == 'No break'){ echo 'selected';} ?>>No break</option>
                                            <option value="30 min" <?php if($break_duration == '30 min'){ echo 'selected';} ?>>30 min</option>
                                            <option value="1 hour" <?php if($break_duration == '1 hour'){ echo 'selected';} ?>>1 hour</option>
                                            <option value="1.5 hours" <?php if($break_duration == '1.5 hours'){ echo 'selected';} ?>>1.5 hours</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="row col-2">
                                    <div class="field mb-4">
                                        <label>Weekly off day</label>
                                        <select name="weekly_off_day" id="weekly_off_day" class="form-control form-control-lg form-control-solid">
                                            <option value="">Select</option>
                                            <option value="Sunday" <?php if($weekly_off == 'Sunday') { echo 'selected';} ?>>Sunday</option>
                                            <option value="Saturday" <?php if($weekly_off == 'Saturday') { echo 'selected';} ?>>Saturday</option>
                                            <option value="No fixed off day" <?php if($weekly_off == 'No fixed off day') { echo 'selected';} ?>>No fixed off day</option>
                                            <option value="Rotating off" <?php if($weekly_off == 'Rotating off') { echo 'selected';} ?>>Rotating off</option>
                                        </select>
                                    </div>

                                    <div class="field mb-4">
                                        <label>Holiday / off days remark</label>
                                        <input type="text" name="holiday_remarks" id="holiday_remarks" placeholder="Holiday Remarks" class="form-control form-control-lg form-control-solid" value="<?php echo $holiday_remakrs; ?>">
                                    </div>
                                </div>
                                <div class="row col-2">
                                    <div class="field mb-4">
                                        <label>Remark / special conditions</label>
                                        <input type="text" name="salary_remarks" id="salary_remarks" placeholder="Remarks" class="form-control form-control-lg form-control-solid" value="<?php echo $salary_remarks; ?>">
                                    </div>
                                </div>
                            </div>
                            <div id="pieceRateSection" style="display:none;">
                            <div class="alert alert-warning mb-5">
                                <strong>How piece rate works in BOSS</strong><br>
                                Piece rate is set per design when a design is assigned for production.
                            </div>

                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5>Earnings from previous employer / work</h5>

                                    <div class="row col-3">
                                        <div class="field mb-4">
                                            <label>Avg monthly earning (Rs.) *</label>
                                            <input type="number" name="avg_monthly_earning" class="form-control form-control-lg form-control-solid" placeholder="e.g. 12000">
                                        </div>

                                        <div class="field mb-4">
                                            <label>Avg pieces completed per day *</label>
                                            <input type="number" name="avg_pieces_per_day" class="form-control form-control-lg form-control-solid" placeholder="e.g. 3">
                                        </div>

                                        <div class="field mb-4">
                                            <label>Working days per month</label>
                                            <input type="number" name="working_days" class="form-control form-control-lg form-control-solid" value="26">
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        </div>
                    </div>
                        </div>
                     </form>
                    <div class="form-step" data-step="5">
                        <div class="card">
                            <div class="card-body">
                                <div id="reviewData"></div>
                            </div>
                        </div>
                    </div>
               
                <div class="wizard-footer">
                    <button type="button" class="btn btn-light" id="prevBtn">Previous</button>
                    <button type="button" class="btn btn-primary" id="nextBtn">Next</button>
                    <button type="button" class="btn btn-success" id="saveBtn" style="display:none;">Save Changes</button>
                </div>
                <div class="toast" id="formToast"></div>
            </div>
        </div>
        <?php include("includes/footer.php"); ?>
    </div>
</div>
<script>var hostUrl = "<?php echo $site_path; ?>/";</script>
<script src="<?php echo $site_path; ?>/assets/plugins/global/plugins.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/scripts.bundle.js"></script>
<script>
    $('#ifsc_code').on('keyup blur', function () {
        let ifsc = $(this).val().toUpperCase();
        $(this).val(ifsc);
        let regex = /^[A-Z]{4}0[A-Z0-9]{6}$/;
        if (ifsc == '') {
            $('#ifsc_msg').html('');
        }
        else if (regex.test(ifsc)) {
            $('#ifsc_msg').html('<span class="text-success">✓ Valid IFSC Code</span>');
        }
        else {
            $('#ifsc_msg').html('<span class="text-danger">✗ Invalid IFSC Code</span>');
        }
    });


    // Account Number Validation
    $('#account_number').on('keyup blur', function () {
        let account = $(this).val();
        if (!/^\d+$/.test(account) && account !== '') {
            $('#account_msg').html('<span class="text-danger">✗ Numbers only allowed</span>');
            return;
        }

        if (account.length >= 9 && account.length <= 18) {
            $('#account_msg').html('<span class="text-success">✓ Valid Account Number</span>');
        }
        else if (account !== '') {
            $('#account_msg').html('<span class="text-danger">✗ Account number should be 9-18 digits</span>');
        }
        else {
            $('#account_msg').html('');
        }

        checkAccountMatch();
    });


    // Confirm Account Validation
    $('#confirm_account_number').on('keyup blur', function () {
        checkAccountMatch();
    });


    function checkAccountMatch() {
        let acc = $('#account_number').val();
        let confirmAcc = $('#confirm_account_number').val();
        if (confirmAcc == '') {
            $('#account_match_icon').html('');
            $('#match_msg').html('');
            return;
        }
        if (acc === confirmAcc) {
            $('#account_match_icon').html('<i class="ki-outline ki-check-circle text-success fs-2"></i>');
            $('#match_msg').html('<span class="text-success">✓ Account numbers matched</span>');
        } else {
            $('#account_match_icon').html('<i class="ki-outline ki-cross-circle text-danger fs-2"></i>');
            $('#match_msg').html('<span class="text-danger">✗ Account numbers do not match</span>');
        }
    }
    
    $('#account_number, #confirm_account_number').on('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    $('#fixedMonthlySalary').show();
    $('#pieceRateSection').hide();

    $('.increment-btn').on('click', function () {
        $('.increment-btn').removeClass('active');
        $(this).addClass('active');
        let type = $(this).data('type');
        if(type === 'fixed_monthly_salary'){
            $('#fixedMonthlySalary').show();
            $('#pieceRateSection').hide();
            $("#salary_type").val(type);
        }
        else if(type === 'piece_rate'){
            $('#fixedMonthlySalary').hide();
            $('#pieceRateSection').show();
            $("#salary_type").val(type);
        }

    });
    
    $("#work_start_time").flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i:s",   // value stored in input (24-hour format)
        altInput: true,
        altFormat: "h:i K"     // display as 09:00 AM
    });
    
    $("#work_end_time").flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i:s",   // value stored in input (24-hour format)
        altInput: true,
        altFormat: "h:i K"     // display as 09:00 AM
    });
    
    $(document).on('click', '.id_type', function () {
        $('.id_type').removeClass('active');
        $(this).addClass('active');

        let type = $(this).data('type');

        $('#id_type').val(type);

        if(type === 'aadhar_card'){

            $('#id_number_label').html('Aadhaar Number <span class="req">*</span>');
            $('#id_name_label').html('Name as on Aadhaar <span class="req">*</span>');

            $('#id_number').attr('placeholder','XXXX XXXX XXXX');
            $('#id_name').attr('placeholder','Name as per Aadhaar');

        }
        else if(type === 'driving_license'){
            $('#id_number_label').html('Driving License Number <span class="req">*</span>');
            $('#id_name_label').html('Name as on Driving License <span class="req">*</span>');

            $('#id_number').attr('placeholder','DL-XXXXXXXXXXXX');
            $('#id_name').attr('placeholder','Name as per Driving License');
        }
        else if(type === 'voter_card'){

            $('#id_number_label').html('Voter ID Number <span class="req">*</span>');
            $('#id_name_label').html('Name as on Voter ID <span class="req">*</span>');

            $('#id_number').attr('placeholder','ABC1234567');
            $('#id_name').attr('placeholder','Name as per Voter ID');
        }
    });
    
    let currentStep = 1;
    const totalSteps = 5;
    
    const stepNames = {
        1: 'Identity Proof',
        2: 'Bank Details',
        3: 'Salary Setup',
        4: 'Review & Save'
    };
    
    showStep(1);
    
    function showStep(step){
        $('.form-step').removeClass('active');
        $(`.form-step[data-step="${step}"]`).addClass('active');

        $('.registration-stepper .step').removeClass('active');
        $(`.registration-stepper .step[data-step="${step}"]`).addClass('active');

        if(step == 1){
            $('#prevBtn').hide();
        }else{
            $('#prevBtn').show();
        }
        if(step == totalSteps){
            $('#nextBtn').hide();
            $('#saveBtn').show();
            generateReview();
        }else{
            $('#nextBtn').show();
            $('#saveBtn').hide();
            $('#nextBtn').html(`Next : ${stepNames[step]}<i class="ki-outline ki-arrow-right fs-5 ms-2"></i>`);
        }
    }

    $('#nextBtn').click(function () {
        if (!validateStep(currentStep)) {
            return;
        }
        currentStep++;
        showStep(currentStep);
    });

    $('#prevBtn').click(function () {
        currentStep--;
        showStep(currentStep);
    });
    function validateStep(step) {
        let valid = true;
        $(`.form-step[data-step="${step}"]`).find('[required]').each(function () {
            if ($(this).val() === '') {
                $(this).addClass('is-invalid');
                valid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        return valid;
    }
    
    /* Genearate Review */
    function generateReview(){
        let FrontPhoto = $('#front_id')[0].files.length > 0 ? 'Captured ✓' : 'Pending';
        let BackPhoto = $('#back_id')[0].files.length > 0 ? 'Uploaded ✓' : 'Pending';
        let qrphoto = $('#qrcode')[0].files.length > 0 ? 'Uploaded ✓' : 'Pending';
        let html = `
        <div class="registration-summary-card">

            <div class="summary-header">
                <i class="ki-outline ki-document fs-2"></i>
                Registration Summary
            </div>

            <div class="summary-row">
                <span>Staff ID</span>
                <span class="badge badge-light-warning">
                    <?= $emp_id ?>
                </span>
            </div>

            <div class="summary-row">
                <span>Name</span>
                <strong>
                    ${$('#firstName').val()}
                    ${$('#middleName').val()}
                    ${$('#lastname').val()}
                </strong>
            </div>

            <div class="summary-row">
                <span>Job Type</span>
                <strong>
                    ${$('#job_type option:selected').text()}
                </strong>
            </div>

            <div class="summary-row">
                <span>Joining Date</span>
                <strong>${$('#doj').val()}</strong>
            </div>
            <div class="summary-row">
                <span>Skills</span>
                <strong>
                    ${$("#speciality").val()}
                </strong>
            </div>
            <div class="summary-row">
                <span>Reference</span>
                <strong>
                    ${$("#reference_name").val()}
                </strong>
            </div>
            <div class="summary-row">
                <span>ID Type</span>
                <strong>
                    ${$("#id_type").val()}
                </strong>
            </div>
            <div class="summary-row">
                <span>Document Number</span>
                <strong>
                    ${$("#aadhaar_no").val()}
                </strong>
            </div>
            <div class="summary-row">
                <span>Document Name</span>
                <strong>
                    ${$("#aadhaar_name").val()}
                </strong>
            </div>
            <div class="summary-row">
                <span>Document Fron Photo</span>
                <span class="badge badge-light-success">
                    ${FrontPhoto}
                </span>
            </div>
            <div class="summary-row">
                <span>Document Back Photo</span>
                <span class="badge badge-light-success">
                    ${BackPhoto}
                </span>
            </div>
            <div class="summary-row">
                <span>Bank Name</span>
                <strong>
                    ${$("#bank_name").val()}
                </strong>
            </div>
            <div class="summary-row">
                <span>Branch Name</span>
                <strong>
                    ${$("#branch_name").val()}
                </strong>
            </div>
            <div class="summary-row">
                <span>IFSC Code</span>
                <strong>
                    ${$("#ifsc_code").val()}
                </strong>
            </div>
            <div class="summary-row">
                <span>Account Number</span>
                <strong>
                    ${$("#account_number").val()}
                </strong>
            </div>
            <div class="summary-row">
                <span>UPI QRCode Photo</span>
                <span class="badge badge-light-success">
                    ${qrphoto}
                </span>
            </div>
        </div>`;

        $('#reviewData').html(html);
}

$('#saveBtn').click(function(){
    let formData = new FormData(document.getElementById('regForm'));
    $.ajax({
        url: $('#regForm').attr('action'),
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        beforeSend:function(){
            $('#submitBtn').prop('disabled', true).text('Saving...');
        },

        success:function(response){
            let res = JSON.parse(response);
            if(res.status == 'success'){
                Swal.fire({
                    icon:'success',
                    title:'Karigar Registered',
                    text:'Staff saved successfully'
                }).then(() => {
                    window.location.href =
                        $('input[name="redirect_url"]').val();

                });

            }else{
                Swal.fire({
                    icon:'error',
                    text:res.message
                });
            }
        },

        complete:function(){
            $('#submitBtn').prop('disabled', false).text('Submit & Save');
        }
    });

});
</script>
