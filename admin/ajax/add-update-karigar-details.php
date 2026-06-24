<?php
include("../config/database.php");
include("../config/auth_check.php");
$employ_id = generate_staff_code();


$action = isset($_POST['action']) ? $_POST['action'] : "add-karigar-details";
if ($action == 'add-karigar-details') {
    $firstName = trim($_POST['firstName'] ?? '');
    $middleName = trim($_POST['middleName'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');
    $dob = $_POST['dob'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $mobile1 = trim($_POST['mobile1'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $job_type = (int) ($_POST['job_type'] ?? 0);
    $doj = $_POST['doj'] ?? '';
    $speciality = $_POST['speciality'] ?? '';
    $reference_name = trim($_POST['reference_name'] ?? '');
    $id_type = trim($_POST['id_type'] ?? '');
    $aadhaar_no = trim($_POST['aadhaar_no'] ?? '');
    $aadhaar_name = trim($_POST['aadhaar_name'] ?? '');
    $bank_name = $_POST['bank_name'] ?? '';
    $branch_name = $_POST['branch_name'] ?? '';
    $ifsc_code = $_POST['ifsc_code'] ?? '';
    $account_number = $_POST['account_number'] ?? '';
    $monthly_salary = $_POST['monthly_salary'] ?? '';
    $payment_date = $_POST['payment_date'] ?? '';
    $work_start_time = $_POST['work_start_time'] ?? '';
    $work_end_time = $_POST['work_end_time'] ?? '';
    $break_duration = $_POST['break_duration'] ?? '';
    $weekly_off_day = $_POST['weekly_off_day'] ?? '';
    $holiday_remarks = $_POST['holiday_remarks'] ?? '';
    $salary_remarks = $_POST['salary_remarks'] ?? '';
    $avg_monthly_earning = $_POST['avg_monthly_earning'] ?? '';
    $avg_pieces_per_day = $_POST['avg_pieces_per_day'] ?? '';
    $working_days = $_POST['working_days'] ?? '';
    $salary_type = $_POST['salary_type'] ?? '';
    $redirect_url = $_POST['redirect_url'];
    
    $front_adhar_proof = $back_adhar_proof  = $qrcode = '';
    if (!empty($_FILES['front_id']['name'])) {
        $tmp_name = $_FILES['front_id']['tmp_name'];
        $error = $_FILES['front_id']['error'];
        $file_name = $_FILES['front_id']['name'];
        if ($error == 0) {
            $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $front_adhar_proof = "front_adhar_" . time() . '.' . $extension;
            move_uploaded_file($tmp_name, '../../uploads/karigar/documents/' . $front_adhar_proof);
        }
    }

    if (!empty($_FILES['back_id']['name'])) {
        $tmp_name = $_FILES['back_id']['tmp_name'];
        $error = $_FILES['back_id']['error'];
        $file_name = $_FILES['back_id']['name'];
        if ($error == 0) {
            $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $back_adhar_proof = "back_adhar_" . time() . '.' . $extension;
            move_uploaded_file($tmp_name, '../../uploads/karigar/documents/' . $back_adhar_proof);
        }
    }
    
    if (!empty($_FILES['qrcode']['name'])) {
        $tmp_name = $_FILES['qrcode']['tmp_name'];
        $error = $_FILES['qrcode']['error'];
        $file_name = $_FILES['qrcode']['name'];
        if ($error == 0) {
            $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $qrcode = "bank_qr_" . time() . '.' . $extension;
            move_uploaded_file($tmp_name, '../../uploads/karigar/documents/' . $qrcode);
        }
    }
    
    /* Check Mobile Or Email Already Exist Or not */
    $checkSql = "SELECT id  FROM karigar_registration  WHERE mobile_number = ?  LIMIT 1";
    $checkStmt = $con->prepare($checkSql);
    $checkStmt->bind_param("s", $mobile1);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    if ($result->num_rows > 0) {
        echo json_encode([
            "status" => "error",
            "message" => "Mobile number already exists."
        ]);
        exit;
    }
    
    $insertSql = "INSERT INTO karigar_registration(
                employee_code,
                firstname,
                middlename,
                lastname,
                dob,
                gender,
                mobile_number,
                address,
                job_type,
                doj,
                skills,
                reference_name,
                identity_proof,
                identity_proof_number,
                identity_proof_name,
                identity_proof_front_doc,
                identity_proof_back_doc,
                bank_name,
                branch_name,
                ifsc_code,
                account_number,
                qrcode,
                salary_type,
                monthly_salary,
                payment_date,
                work_start_time,
                work_end_time,
                break_duration,
                weekly_off,
                holiday_remakrs,
                salary_remarks,
                avg_monthly_earning,
                avg_pcs_per_day,
                working_day_per_month,
                created_by,
                created_at
            ) VALUES (
                ?,?,?,?,?,?,?,?,?,?,
                ?,?,?,?,?,?,?,?,?,?,
                ?,?,?,?,?,?,?,?,?,?,
                ?,?,?,?,?,NOW()
            )";

    $stmt = $con->prepare($insertSql);
    if (!$stmt) {
        die($con->error);
    }
    
    $stmt->bind_param(
            str_repeat('s', 35),
            $employ_id,
            $firstName,
            $middleName,
            $lastname,
            $dob,
            $gender,
            $mobile1,
            $address,
            $job_type,
            $doj,
            $speciality,
            $reference_name,
            $id_type,
            $aadhaar_no,
            $aadhaar_name,
            $front_adhar_proof,
            $back_adhar_proof,
            $bank_name,
            $branch_name,
            $ifsc_code,
            $account_number,
            $qrcode,
            $salary_type,
            $monthly_salary,
            $payment_date,
            $work_start_time,
            $work_end_time,
            $break_duration,
            $weekly_off_day,
            $holiday_remarks,
            $salary_remarks,
            $avg_monthly_earning,
            $avg_pieces_per_day,
            $working_days,
            $uid
    );
    
    if ($stmt->execute()) {
        echo json_encode([
            "status" => "success",
            "message" => "Karigar registered successfully.",
            "redirect_url" => $redirect_url
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Insert failed: " . $stmt->error
        ]);
    }

    $stmt->close();

}else if($action == 'update-staff-details'){
    $karigar_id = $_POST['karigar_id'];
    $firstName = trim($_POST['firstName'] ?? '');
    $middleName = trim($_POST['middleName'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');
    $dob = $_POST['dob'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $mobile1 = trim($_POST['mobile1'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $job_type = (int) ($_POST['job_type'] ?? 0);
    $doj = $_POST['doj'] ?? '';
    $speciality = $_POST['speciality'] ?? '';
    $reference_name = trim($_POST['reference_name'] ?? '');
    $id_type = trim($_POST['id_type'] ?? '');
    $aadhaar_no = trim($_POST['aadhaar_no'] ?? '');
    $aadhaar_name = trim($_POST['aadhaar_name'] ?? '');
    $bank_name = $_POST['bank_name'] ?? '';
    $branch_name = $_POST['branch_name'] ?? '';
    $ifsc_code = $_POST['ifsc_code'] ?? '';
    $account_number = $_POST['account_number'] ?? '';
    $monthly_salary = $_POST['monthly_salary'] ?? '';
    $payment_date = $_POST['payment_date'] ?? '';
    $work_start_time = $_POST['work_start_time'] ?? '';
    $work_end_time = $_POST['work_end_time'] ?? '';
    $break_duration = $_POST['break_duration'] ?? '';
    $weekly_off_day = $_POST['weekly_off_day'] ?? '';
    $holiday_remarks = $_POST['holiday_remarks'] ?? '';
    $salary_remarks = $_POST['salary_remarks'] ?? '';
    $avg_monthly_earning = $_POST['avg_monthly_earning'] ?? '';
    $avg_pieces_per_day = $_POST['avg_pieces_per_day'] ?? '';
    $working_days = $_POST['working_days'] ?? '';
    $salary_type = $_POST['salary_type'] ?? '';
    $redirect_url = $_POST['redirect_url'];
    
    $front_adhar_proof = $back_adhar_proof  = $qrcode = '';
    if (!empty($_FILES['front_id']['name'])) {
        $tmp_name = $_FILES['front_id']['tmp_name'];
        $error = $_FILES['front_id']['error'];
        $file_name = $_FILES['front_id']['name'];
        if ($error == 0) {
            $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $front_adhar_proof = "front_adhar_" . time() . '.' . $extension;
            move_uploaded_file($tmp_name, '../../uploads/karigar/documents/' . $front_adhar_proof);
        }
    }else{
        $front_adhar_proof = $_POST['front_adhar_proof'];
    }

    if (!empty($_FILES['back_id']['name'])) {
        $tmp_name = $_FILES['back_id']['tmp_name'];
        $error = $_FILES['back_id']['error'];
        $file_name = $_FILES['back_id']['name'];
        if ($error == 0) {
            $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $back_adhar_proof = "back_adhar_" . time() . '.' . $extension;
            move_uploaded_file($tmp_name, '../../uploads/karigar/documents/' . $back_adhar_proof);
        }
    }else{
        $back_adhar_proof = $_POST['back_adhar_proof'];
    }
    
    if (!empty($_FILES['qrcode']['name'])) {
        $tmp_name = $_FILES['qrcode']['tmp_name'];
        $error = $_FILES['qrcode']['error'];
        $file_name = $_FILES['qrcode']['name'];
        if ($error == 0) {
            $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $qrcode = "bank_qr_" . time() . '.' . $extension;
            move_uploaded_file($tmp_name, '../../uploads/karigar/documents/' . $qrcode);
        }
    }else{
        $qrcode = $_POST['old_qrcode'];
    }
    
    /* Check Mobile Or Email Already Exist Or not */
    $checkSql = "SELECT id FROM karigar_registration WHERE mobile_number = ? AND id != ? LIMIT 1";

    $checkStmt = $con->prepare($checkSql);
    $checkStmt->bind_param("si", $mobile1, $karigar_id);
    $checkStmt->execute();

    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode([
            "status" => "error",
            "message" => "Mobile number already exists."
        ]);
        exit;
    }
    
    $updateSql = "UPDATE karigar_registration SET
                firstname = ?,
                middlename = ?,
                lastname = ?,
                dob = ?,
                gender = ?,
                mobile_number = ?,
                address = ?,
                job_type = ?,
                doj = ?,
                skills = ?,
                reference_name = ?,
                identity_proof = ?,
                identity_proof_number = ?,
                identity_proof_name = ?,
                identity_proof_front_doc = ?,
                identity_proof_back_doc = ?,
                bank_name = ?,
                branch_name = ?,
                ifsc_code = ?,
                account_number = ?,
                qrcode = ?,
                salary_type = ?,
                monthly_salary = ?,
                payment_date = ?,
                work_start_time = ?,
                work_end_time = ?,
                break_duration = ?,
                weekly_off = ?,
                holiday_remakrs = ?,
                salary_remarks = ?,
                avg_monthly_earning = ?,
                avg_pcs_per_day = ?,
                working_day_per_month = ?,
                updated_by = ?,
                updated_at = NOW()
            WHERE id = ?";

    $stmt = $con->prepare($updateSql);
    if (!$stmt) {
        die($con->error);
    }
    
    $stmt->bind_param(
        str_repeat('s', 35),

        $firstName,
        $middleName,
        $lastname,
        $dob,
        $gender,
        $mobile1,
        $address,
        $job_type,
        $doj,
        $speciality,
        $reference_name,
        $id_type,
        $aadhaar_no,
        $aadhaar_name,
        $front_adhar_proof,
        $back_adhar_proof,
        $bank_name,
        $branch_name,
        $ifsc_code,
        $account_number,
        $qrcode,
        $salary_type,
        $monthly_salary,
        $payment_date,
        $work_start_time,
        $work_end_time,
        $break_duration,
        $weekly_off_day,
        $holiday_remarks,
        $salary_remarks,
        $avg_monthly_earning,
        $avg_pieces_per_day,
        $working_days,
        $uid,
        $karigar_id
    );
    
    if ($stmt->execute()) {
        echo json_encode([
            "status" => "success",
            "message" => "Karigar Update successfully.",
            "redirect_url" => $redirect_url
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Insert failed: " . $stmt->error
        ]);
    }

    $stmt->close();
}