<?php
include("../config/database.php");

error_reporting(E_ALL);
ini_set('display_errors',1);

$action = isset($_POST['action']) ? $_POST['action'] : "";
if ($action == 'add-staff-details') {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $fullname = $firstName." ".$lastName;
    $dob = $_POST['dob'];
    $doj = $_POST['doj'];
    $gender = $_POST['gender'];
    $employment_type = $_POST['employment_type'];
    $employ_id = $_POST['employ_id'];
    $contract_start = !empty($_POST['contract_start']) ? $_POST['contract_start'] : "";
    $contract_end = !empty($_POST['contract_end']) ? $_POST['contract_end'] : "";
    $mobile1 = !empty($_POST['mobile1']) ? $_POST['mobile1'] : "";
    $mobile2 = !empty($_POST['mobile2']) ? $_POST['mobile2'] : "";
    $email = !empty($_POST['email']) ? $_POST['email'] : "";
    $address = !empty($_POST['address']) ? $_POST['address'] : "";
    $city = !empty($_POST['city']) ? $_POST['city'] : "";
    $state = !empty($_POST['state']) ? $_POST['state'] : "";
    $pin = !empty($_POST['pin']) ? $_POST['pin'] : "";
    $id_proof_no = !empty($_POST['id_proof_no']) ? $_POST['id_proof_no'] : "";
    $bankName = !empty($_POST['bankName']) ? $_POST['bankName'] : "";
    $acHolder = !empty($_POST['acHolder']) ? $_POST['acHolder'] : "";
    $acNo = !empty($_POST['acNo']) ? $_POST['acNo'] : "";
    $ifsc = !empty($_POST['ifsc']) ? $_POST['ifsc'] : "";
    $branch = !empty($_POST['branch']) ? $_POST['branch'] : "";
    $notes = !empty($_POST['notes']) ? $_POST['notes'] : "";
    $salary = !empty($_POST['salary']) ? $_POST['salary'] : "";
    $id_proof = $cancelled_cheque = $profile_picture = '';
    if (!empty($_FILES['id_proof']['name'])) {
        $tmp_name = $_FILES['id_proof']['tmp_name'];
        $error = $_FILES['id_proof']['error'];
        $file_name = $_FILES['id_proof']['name'];
        if ($error == 0) {
            $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $id_proof = "id_proof_" . time() . '.' . $extension;
            move_uploaded_file($tmp_name, '../../uploads/staff/documents/' . $id_proof);
        }
    }

    if (!empty($_FILES['cencelled_cheque']['name'])) {
        $tmp_name = $_FILES['cencelled_cheque']['tmp_name'];
        $error = $_FILES['cencelled_cheque']['error'];
        $file_name = $_FILES['cencelled_cheque']['name'];
        if ($error == 0) {
            $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $cancelled_cheque = "cheque_" . time() . '.' . $extension;
            move_uploaded_file($tmp_name, '../../uploads/staff/documents/' . $cancelled_cheque);
        }
    }


    $Insertsql = "INSERT INTO staff_register (
        employee_code,
        fullname,
        mobile_number,
        email,
        gender,
        address,
        dob,
        doj,
        employment_type,
        contract_start,
        contract_end,
        city,
        state,
        pincode,
        bank_name,
        account_holder_name,
        account_number,
        ifsc_code,
        branch_name,
        cheque_image,
        document_no,
        doc_image,
        salary,
        profile_picture,
        notes,
        created_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

    $stmt = $con->prepare($Insertsql);

    if (!$stmt) {
        echo json_encode([
            "status" => "error",
            "message" => "Prepare failed: " . $con->error
        ]);
        exit;
    }

    $stmt->bind_param(
        "sssssssssssssssssssssssss",
        $employ_id,
        $fullname,       
        $mobile1,        
        $email,         
        $gender,          
        $address,         
        $dob,          
        $doj,             
        $employment_type,
        $contract_start, 
        $contract_end,  
        $city,    
        $state,           
        $pin,           
        $bankName,
        $acHolder,        
        $acNo,        
        $ifsc,            
        $branch,          
        $cancelled_cheque,
        $id_proof_no,
        $id_proof,    
        $salary,       
        $profile_picture,
        $notes
    );

    if ($stmt->execute()) {
        echo json_encode([
            "status" => "success",
            "message" => "Staff registered successfully."
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Insert failed: " . $stmt->error
        ]);
    }

    $stmt->close();
}