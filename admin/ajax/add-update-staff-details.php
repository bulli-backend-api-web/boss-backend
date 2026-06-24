<?php
include("../config/database.php");
$employ_id = generate_staff_code();

$action = isset($_POST['action']) ? $_POST['action'] : "add-staff-details";
if ($action == 'add-staff-details') {
    $firstName = trim($_POST['firstName'] ?? '');
    $middleName = trim($_POST['middleName'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');

    $dob = $_POST['dob'] ?? '';
    $doj = $_POST['doj'] ?? '';

    $gender = $_POST['gender'] ?? '';
    $blood_group = $_POST['blood_group'] ?? '';

    $mobile1 = trim($_POST['mobile1'] ?? '');
    $email = trim($_POST['email'] ?? '');

    $address = trim($_POST['address'] ?? '');

    $emergancy_name = trim($_POST['emergancy_name'] ?? '');
    $emergancy_phone = trim($_POST['emergancy_phone'] ?? '');

    $department_id = (int) ($_POST['department_id'] ?? 0);
    $role_id = (int) ($_POST['role_id'] ?? 0);

    $employment_type = $_POST['employment_type'] ?? '';
    $work_location = trim($_POST['work_location'] ?? '');

    $aadhaar_no = trim($_POST['aadhaar_no'] ?? '');
    $aadhaar_name = trim($_POST['aadhaar_name'] ?? '');

    $bondApplicable = !empty($_POST['bondApplicable']) ? 1 : 0;

    $bond_start_date = $_POST['bond_start_date'] ?? '';
    $bond_end_date = $_POST['bond_end_date'] ?? '';
    $bond_tenure = $_POST['bond_tenure'] ?? '';
    $bond_amount = $_POST['bond_amount'] ?? '';

    $increment_basis = $_POST['increment_basis'] ?? 'periodic';

    $is_dept_head = (isset($_POST['is_dept_head']) && $_POST['is_dept_head'] == 'yes') ? 1 : 0;

    $first_increment_after = $_POST['first_increment_after'] ?? '';
    $increment_frequency = $_POST['increment_frequency'] ?? '';
    $periodic_reminder_days = $_POST['periodic_reminder_days'] ?? '';

    $review_cycle = $_POST['review_cycle'] ?? '';
    $performance_score = $_POST['performance_score'] ?? '';
    $performance_reminder_days = $_POST['performance_reminder_days'] ?? '';

    $previous_employers = $_POST['previous_employers'] ?? '';
    
    $redirect_url = $_POST['redirect_url'];

    $front_adhar_proof = $back_adhar_proof = $bond_doc = $profile_picture = '';
    if (!empty($_FILES['front_aadhar']['name'])) {
        $tmp_name = $_FILES['front_aadhar']['tmp_name'];
        $error = $_FILES['front_aadhar']['error'];
        $file_name = $_FILES['front_aadhar']['name'];
        if ($error == 0) {
            $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $front_adhar_proof = "front_adhar_" . time() . '.' . $extension;
            move_uploaded_file($tmp_name, '../../uploads/staff/documents/' . $front_adhar_proof);
        }
    }

    if (!empty($_FILES['back_aadhar']['name'])) {
        $tmp_name = $_FILES['back_aadhar']['tmp_name'];
        $error = $_FILES['back_aadhar']['error'];
        $file_name = $_FILES['back_aadhar']['name'];
        if ($error == 0) {
            $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $back_adhar_proof = "back_adhar_" . time() . '.' . $extension;
            move_uploaded_file($tmp_name, '../../uploads/staff/documents/' . $back_adhar_proof);
        }
    }

    if (!empty($_FILES['bond_doc']['name'])) {
        $tmp_name = $_FILES['bond_doc']['tmp_name'];
        $error = $_FILES['bond_doc']['error'];
        $file_name = $_FILES['bond_doc']['name'];
        if ($error == 0) {
            $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $bond_doc = "bond_doc_" . time() . '.' . $extension;
            move_uploaded_file($tmp_name, '../../uploads/staff/documents/' . $bond_doc);
        }
    }

    if (!empty($_FILES['profile_picture']['name'])) {
        $tmp_name = $_FILES['profile_picture']['tmp_name'];
        $file_name = $_FILES['profile_picture']['name'];
        if ($_FILES['profile_picture']['error'] == 0) {
            $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $profile_picture = "profile_" . time() . '.' . $extension;
            move_uploaded_file($tmp_name, '../../uploads/staff/' . $profile_picture);
        }
    }

    /* Check Mobile Or Email Already Exist Or not */
    $checkSql = "SELECT id  FROM staff_register  WHERE mobile_number = ? OR email = ? LIMIT 1";
    $checkStmt = $con->prepare($checkSql);
    $checkStmt->bind_param("ss", $mobile1, $email);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {

        echo json_encode([
            "status" => "error",
            "message" => "Mobile number or email address already exists."
        ]);
        exit;
    }


    $insertSql = "INSERT INTO staff_register(
                employee_code,
                firstname,
                middlename,
                lastname,
                dob,
                doj,
                gender,
                blood_group,
                mobile_number,
                email,
                address,
                emergancy_contact_person,
                emergancy_contact_number,
                department_id,
                role_id,
                is_department_head,
                employment_type,
                work_location,
                profile_picture,
                aadhar_number,
                aadhaar_name,
                aadhar_front_image,
                aadhar_back_image,
                is_bond_applicable,
                contract_start,
                contract_end,
                bond_tenure,
                bond_amount,
                bond_doc,
                prevoius_work_history,
                increment_basis,
                first_increment_after,
                increment_frequency,
                periodic_reminder_days,
                review_cycle,
                performance_score,
                performance_reminder_days,
                created_at
            ) VALUES (
                ?,?,?,?,?,?,?,?,?,?,
                ?,?,?,?,?,?,?,?,?,?,
                ?,?,?,?,?,?,?,?,?,?,
                ?,?,?,?,?,?,?,NOW()
            )";

    $stmt = $con->prepare($insertSql);

    if (!$stmt) {
        die($con->error);
    }

    $stmt->bind_param(
            str_repeat('s', 37),
            $employ_id,
            $firstName,
            $middleName,
            $lastname,
            $dob,
            $doj,
            $gender,
            $blood_group,
            $mobile1,
            $email,
            $address,
            $emergancy_name,
            $emergancy_phone,
            $department_id,
            $role_id,
            $is_dept_head,
            $employment_type,
            $work_location,
            $profile_picture,
            $aadhaar_no,
            $aadhaar_name,
            $front_adhar_proof,
            $back_adhar_proof,
            $bondApplicable,
            $bond_start_date,
            $bond_end_date,
            $bond_tenure,
            $bond_amount,
            $bond_doc,
            $previous_employers,
            $increment_basis,
            $first_increment_after,
            $increment_frequency,
            $periodic_reminder_days,
            $review_cycle,
            $performance_score,
            $performance_reminder_days
    );

    if ($stmt->execute()) {
        echo json_encode([
            "status" => "success",
            "message" => "Staff registered successfully.",
            "redirect_url" => $redirect_url
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Insert failed: " . $stmt->error
        ]);
    }

    $stmt->close();
} else if ($action == 'update-staff-details') {
    $redirect_url = $_POST['redirect_url'];
    $staff_id = $_POST['staff_id'];
    
    $firstName = trim($_POST['firstName'] ?? '');
    $middleName = trim($_POST['middleName'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');

    $dob = $_POST['dob'] ?? '';
    $doj = $_POST['doj'] ?? '';

    $gender = $_POST['gender'] ?? '';
    $blood_group = $_POST['blood_group'] ?? '';

    $mobile1 = trim($_POST['mobile1'] ?? '');
    $email = trim($_POST['email'] ?? '');

    $address = trim($_POST['address'] ?? '');

    $emergancy_name = trim($_POST['emergancy_name'] ?? '');
    $emergancy_phone = trim($_POST['emergancy_phone'] ?? '');

    $department_id = (int) ($_POST['department_id'] ?? 0);
    $role_id = (int) ($_POST['role_id'] ?? 0);

    $employment_type = $_POST['employment_type'] ?? '';
    $work_location = trim($_POST['work_location'] ?? '');

    $aadhaar_no = trim($_POST['aadhaar_no'] ?? '');
    $aadhaar_name = trim($_POST['aadhaar_name'] ?? '');

    $bondApplicable = !empty($_POST['bondApplicable']) ? 1 : 0;

    $bond_start_date = $_POST['bond_start_date'] ?? '';
    $bond_end_date = $_POST['bond_end_date'] ?? '';
    $bond_tenure = $_POST['bond_tenure'] ?? '';
    $bond_amount = $_POST['bond_amount'] ?? '';

    $increment_basis = $_POST['increment_basis'] ?? 'periodic';

    $is_dept_head = (isset($_POST['is_dept_head']) && $_POST['is_dept_head'] == 'yes') ? 1 : 0;

    $first_increment_after = $_POST['first_increment_after'] ?? '';
    $increment_frequency = $_POST['increment_frequency'] ?? '';
    $periodic_reminder_days = $_POST['periodic_reminder_days'] ?? '';

    $review_cycle = $_POST['review_cycle'] ?? '';
    $performance_score = $_POST['performance_score'] ?? '';
    $performance_reminder_days = $_POST['performance_reminder_days'] ?? '';

    $previous_employers = $_POST['previous_employers'] ?? '';

    $front_adhar_proof = $back_adhar_proof = $bond_doc = $profile_picture = '';
    if (!empty($_FILES['front_aadhar']['name'])) {
        $tmp_name = $_FILES['front_aadhar']['tmp_name'];
        $error = $_FILES['front_aadhar']['error'];
        $file_name = $_FILES['front_aadhar']['name'];
        if ($error == 0) {
            $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $front_adhar_proof = "front_adhar_" . time() . '.' . $extension;
            move_uploaded_file($tmp_name, '../../uploads/staff/documents/' . $front_adhar_proof);
        }
    }else{
        $existing = $con->query("SELECT aadhar_front_image FROM staff_register WHERE id = '$staff_id'")->fetch_assoc();
        $front_adhar_proof = $existing['aadhar_front_image'] ?? '';
    }

    if (!empty($_FILES['back_aadhar']['name'])) {
        $tmp_name = $_FILES['back_aadhar']['tmp_name'];
        $error = $_FILES['back_aadhar']['error'];
        $file_name = $_FILES['back_aadhar']['name'];
        if ($error == 0) {
            $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $back_adhar_proof = "back_adhar_" . time() . '.' . $extension;
            move_uploaded_file($tmp_name, '../../uploads/staff/documents/' . $back_adhar_proof);
        }
    }else{
        $existing = $con->query("SELECT aadhar_back_image FROM staff_register WHERE id = '$staff_id'")->fetch_assoc();
        $back_adhar_proof = $existing['aadhar_back_image'] ?? '';
    }

    if (!empty($_FILES['bond_doc']['name'])) {
        $tmp_name = $_FILES['bond_doc']['tmp_name'];
        $error = $_FILES['bond_doc']['error'];
        $file_name = $_FILES['bond_doc']['name'];
        if ($error == 0) {
            $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $bond_doc = "bond_doc_" . time() . '.' . $extension;
            move_uploaded_file($tmp_name, '../../uploads/staff/documents/' . $bond_doc);
        }
    }else{
        $existing = $con->query("SELECT bond_doc FROM staff_register WHERE id = '$staff_id'")->fetch_assoc();
        $back_adhar_proof = $existing['bond_doc'] ?? '';
    }

    if (!empty($_FILES['profile_picture']['name'])) {
        $tmp_name = $_FILES['profile_picture']['tmp_name'];
        $file_name = $_FILES['profile_picture']['name'];
        if ($_FILES['profile_picture']['error'] == 0) {
            $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $profile_picture = "profile_" . time() . '.' . $extension;
            move_uploaded_file($tmp_name, '../../uploads/staff/' . $profile_picture);
        }
    }else{
        $existing = $con->query("SELECT profile_picture FROM staff_register WHERE id = '$staff_id'")->fetch_assoc();
        $profile_picture = $existing['profile_picture'] ?? '';
    }
    
    $checkSql = "SELECT id 
             FROM staff_register 
             WHERE (mobile_number = ? OR email = ?)
             AND id != ?
             LIMIT 1";

    $checkStmt = $con->prepare($checkSql);
    $checkStmt->bind_param("ssi", $mobile1, $email, $staff_id);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode([
            "status" => "error",
            "message" => "Mobile number or email address already exists."
        ]);
        exit;
    }

    $updateSql = "UPDATE staff_register SET
        firstname=?,
        middlename=?,
        lastname=?,
        dob=?,
        doj=?,
        gender=?,
        blood_group=?,
        mobile_number=?,
        email=?,
        address=?,
        emergancy_contact_person=?,
        emergancy_contact_number=?,
        department_id=?,
        role_id=?,
        is_department_head=?,
        employment_type=?,
        work_location=?,
        profile_picture=?,
        aadhar_number=?,
        aadhaar_name=?,
        aadhar_front_image=?,
        aadhar_back_image=?,
        is_bond_applicable=?,
        contract_start=?,
        contract_end=?,
        bond_tenure=?,
        bond_amount=?,
        bond_doc=?,
        prevoius_work_history=?,
        increment_basis=?,
        first_increment_after=?,
        increment_frequency=?,
        periodic_reminder_days=?,
        review_cycle=?,
        performance_score=?,
        performance_reminder_days=?,
        updated_at = NOW()
    WHERE id=?";

   $stmt = $con->prepare($updateSql);
    if (!$stmt) {
        echo json_encode([
            "status" => "error",
            "message" => "Prepare failed: " . $con->error
        ]);
        exit;
    }
    
    $stmt->bind_param(
        "sssssssssssssssssssssssssssssssssssii",
        $firstName,
        $middleName,
        $lastname,
        $dob,
        $doj,
        $gender,
        $blood_group,
        $mobile1,
        $email,
        $address,
        $emergancy_name,
        $emergancy_phone,
        $department_id,
        $role_id,
        $is_dept_head,
        $employment_type,
        $work_location,
        $profile_picture,
        $aadhaar_no,
        $aadhaar_name,
        $front_adhar_proof,
        $back_adhar_proof,
        $bondApplicable,
        $bond_start_date,
        $bond_end_date,
        $bond_tenure,
        $bond_amount,
        $bond_doc,
        $previous_employers,
        $increment_basis,
        $first_increment_after,
        $increment_frequency,
        $periodic_reminder_days,
        $review_cycle,
        $performance_score,
        $performance_reminder_days,
        $staff_id
    );

    if ($stmt->execute()) {
        echo json_encode([
            "status" => "success",
            "message" => "Staff updated successfully.",
            "redirect_url" => $redirect_url
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Update failed: " . $stmt->error
        ]);
    }

    $stmt->close();
}