<?php
include("../config/database.php");
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

function jsonResponse(bool $success, string $message, array $data = [], int $httpCode = 200): void
{
    http_response_code($httpCode);
    echo json_encode(['success' => $success, 'message' => $message, 'data' => $data]);
    exit;
}

function clean(?string $value): ?string
{
    if ($value === null || trim($value) === '') return null;
    return trim(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
}

function cleanFloat(?string $value): ?float
{
    if ($value === null || trim($value) === '') return null;
    return (float) $value;
}

function cleanInt(?string $value): ?int
{
    if ($value === null || trim($value) === '') return null;
    return (int) $value;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Method not allowed.', [], 405);
}

$p       = $_POST;

$v_model_id            = clean($p['modelId'] ?? null);
$v_first_name          = clean($p['firstName']     ?? null);
$v_last_name           = clean($p['lastName']      ?? null);
$v_dob                 = clean($p['dob']           ?? null);
$v_age                 = cleanInt($p['age']         ?? null);
$v_gender              = clean($p['gender']        ?? null);
$v_agency_division     = clean($p['agency']        ?? null);
$v_mobile_primary      = clean($p['mobile1']       ?? null);
$v_mobile_alternate    = clean($p['mobile2']       ?? null);
$v_email               = clean($p['email']         ?? null);
$v_instagram           = clean($p['instagram']     ?? null);
$v_youtube             = clean($p['youtube']       ?? null);
$v_address             = clean($p['address']       ?? null);
$v_city                = clean($p['city']          ?? null);
$v_state               = clean($p['state']         ?? null);
$v_pincode             = clean($p['pin']           ?? null);
$v_pan_number          = !empty($p['panNumber'])   ? strtoupper(clean($p['panNumber'])) : null;
$v_aadhaar_number      = clean($p['aadhaarNumber'] ?? null);
$v_bank_name           = clean($p['bankName']      ?? null);
$v_account_holder_name = clean($p['acHolder']      ?? null);
$v_account_number      = clean($p['acNo']          ?? null);
$v_ifsc_code           = !empty($p['ifsc'])        ? strtoupper(clean($p['ifsc'])) : null;
$v_branch              = clean($p['branch']        ?? null);
$v_bank_state          = clean($p['bankState']     ?? null);
$v_upi                 = clean($p['upi']           ?? null);
$v_rate_type           = clean($p['rateType']      ?? null);
$v_amount_per_day      = cleanFloat($p['amtDay']    ?? null);
$v_amount_per_outfit   = cleanFloat($p['amtOutfit'] ?? null);
$v_availability        = clean($p['availability']  ?? null);
$v_hometown_city       = clean($p['hometown']      ?? null);
$v_travel_notes        = clean($p['travelNotes']   ?? null);
$v_height              = clean($p['height']        ?? null);
$v_weight              = clean($p['weight']        ?? null);
$v_internal_notes      = clean($p['notes']         ?? null);
$v_posture_type        = clean($p['posture']       ?? null);
$v_created_at          = date('Y-m-d H:i:s');
$v_updated_at          = date('Y-m-d H:i:s');

// ─────────────────────────────────────────────
//  SANITISED VARIABLES — model_measurements table
// ─────────────────────────────────────────────
$v_measurement_unit = clean($p['measurementUnit'] ?? 'inches');
$v_neck             = cleanFloat($p['neck']       ?? null);
$v_bust             = cleanFloat($p['bust']       ?? null);
$v_underbust        = cleanFloat($p['underbust']  ?? null);
$v_waist            = cleanFloat($p['waist']      ?? null);
$v_high_hip         = cleanFloat($p['highHip']    ?? null);
$v_full_hip         = cleanFloat($p['fullHip']    ?? null);
$v_bicep            = cleanFloat($p['bicep']      ?? null);
$v_wrist            = cleanFloat($p['wrist']      ?? null);
$v_thigh            = cleanFloat($p['thigh']      ?? null);
$v_knee             = cleanFloat($p['knee']       ?? null);
$v_calf             = cleanFloat($p['calf']       ?? null);
$v_shoulder_width   = cleanFloat($p['shoulderW']  ?? null);
$v_shoulder_end     = cleanFloat($p['shoulderE']  ?? null);
$v_across_back      = cleanFloat($p['acrossBack'] ?? null);
$v_bust_apex        = cleanFloat($p['bustApex']   ?? null);
$v_nw_front         = cleanFloat($p['nwFront']    ?? null);
$v_nw_back          = cleanFloat($p['nwBack']     ?? null);
$v_back_waist       = cleanFloat($p['backWaist']  ?? null);
$v_waist_len        = cleanFloat($p['waistLen']   ?? null);
$v_back_hip         = cleanFloat($p['backHip']    ?? null);
$v_crotch           = cleanFloat($p['crotch']     ?? null);
$v_inseam           = cleanFloat($p['inseam']     ?? null);
$v_sleeve           = cleanFloat($p['sleeve']     ?? null);
$v_meas_height      = cleanFloat($p['height']     ?? null);
$v_dress_size       = clean($p['dressSize']       ?? null);
$v_posture          = clean($p['posture']         ?? null);
$v_notes            = clean($p['notes']           ?? null);
$v_meas_created_at  = date('Y-m-d H:i:s');
$v_meas_updated_at  = date('Y-m-d H:i:s');

mysqli_begin_transaction($con);

try {
    $sqlModel = "INSERT INTO models (
                    model_id, first_name, last_name, dob, age, gender,
                    agency_division, mobile_primary, mobile_alternate,
                    email, instagram, youtube_channel_id,
                    address, city, state, pincode,
                    pan_number, aadhaar_number,
                    bank_name, account_holder_name, account_number,
                    ifsc_code, branch, bank_state, upi_id,
                    rate_type, amount_per_day, amount_per_outfit,
                    availability, hometown_city, travel_notes,
                    height, weight, internal_notes, posture_type,
                    created_at, updated_at
                ) VALUES (
                    ?, ?, ?, ?, ?, ?,
                    ?, ?, ?,
                    ?, ?, ?,
                    ?, ?, ?, ?,
                    ?, ?,
                    ?, ?, ?,
                    ?, ?, ?, ?,
                    ?, ?, ?,
                    ?, ?, ?,
                    ?, ?, ?, ?,
                    ?, ?
                )";

    $stmtModel = mysqli_prepare($con, $sqlModel);
    if (!$stmtModel) {
        throw new Exception('Prepare failed (models): ' . mysqli_error($con));
    }

    $modelTypes = 'ssssisssssssssssssssssssssddsssssssss';

    mysqli_stmt_bind_param(
        $stmtModel,
        $modelTypes,
        $v_model_id,
        $v_first_name,
        $v_last_name,
        $v_dob,
        $v_age,
        $v_gender,
        $v_agency_division,
        $v_mobile_primary,
        $v_mobile_alternate,
        $v_email,
        $v_instagram,
        $v_youtube,
        $v_address,
        $v_city,
        $v_state,
        $v_pincode,
        $v_pan_number,
        $v_aadhaar_number,
        $v_bank_name,
        $v_account_holder_name,
        $v_account_number,
        $v_ifsc_code,
        $v_branch,
        $v_bank_state,
        $v_upi,
        $v_rate_type,
        $v_amount_per_day,
        $v_amount_per_outfit,
        $v_availability,
        $v_hometown_city,
        $v_travel_notes,
        $v_height,
        $v_weight,
        $v_internal_notes,
        $v_posture_type,
        $v_created_at,
        $v_updated_at
    );

    if (!mysqli_stmt_execute($stmtModel)) {
        throw new Exception('Execute failed (models): ' . mysqli_stmt_error($stmtModel));
    }

    $insertedModelId = mysqli_insert_id($con);
    mysqli_stmt_close($stmtModel);

    $sqlMeas = "INSERT INTO model_measurements (
                model_id,
                measurement_unit,
                neck,
                bust_chest,
                under_bust,
                natural_waist,
                high_hip,
                full_hip,
                bicep,
                wrist,
                thigh,
                knee,
                calf,
                shoulder_width,
                shoulder_end,
                across_back,
                bust_apex_height,
                neck_waist_front,
                neck_waist_back,
                back_waist_length,
                waist_to_length,
                back_hip_seat,
                crotch_length,
                inseam,
                sleeve_length,
                dress_size,
                posture
            ) VALUES (
                ?,
                ?,
                ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?,
                ?, ?, ?,
                ?, ?, ?,
                ?, ?, ?,
                ?, ?, ?,
                ?, ?
            )";
 
$stmtMeas = mysqli_prepare($con, $sqlMeas);
if (!$stmtMeas) {
    throw new Exception('Prepare failed (model_measurements): ' . mysqli_error($con));
}
$measTypes = 'is' . str_repeat('d', 23) . 'ss';
 
mysqli_stmt_bind_param(
    $stmtMeas,
    $measTypes,
    $insertedModelId,
    $v_measurement_unit,
    $v_neck,
    $v_bust,
    $v_underbust,
    $v_waist,
    $v_high_hip,
    $v_full_hip,
    $v_bicep,
    $v_wrist,
    $v_thigh,
    $v_knee,
    $v_calf,
    $v_shoulder_width,
    $v_shoulder_end,
    $v_across_back,
    $v_bust_apex,
    $v_nw_front,
    $v_nw_back,
    $v_back_waist,
    $v_waist_len,
    $v_back_hip,
    $v_crotch,
    $v_inseam,
    $v_sleeve,
    $v_dress_size,
    $v_posture
);
 
if (!mysqli_stmt_execute($stmtMeas)) {
    throw new Exception('Execute failed (model_measurements): ' . mysqli_stmt_error($stmtMeas));
}

if (!empty($_FILES['photoFiles']['name'][0])) {
    foreach ($_FILES['photoFiles']['name'] as $key => $file_name) {
        $tmp_name = $_FILES['photoFiles']['tmp_name'][$key];
        $error    = $_FILES['photoFiles']['error'][$key];
        if ($error == 0) {
            $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $new_name = "photo_".time().'_'.$key.'.'.$extension;
            move_uploaded_file(
                $tmp_name,
                '../../uploads/model/'.$new_name
            );

            $insert_query = "INSERT INTO model_photos(model_id,image_path) values (?,?)";
            $stmt1 = $con->prepare($insert_query);
            $stmt1->bind_param("is",$insertedModelId,$new_name);
            $stmt1->execute();
        }
    }
}

if (!empty($_FILES['videoFiles']['name'][0])) {
    foreach ($_FILES['videoFiles']['name'] as $key => $file_name) {
        $tmp_name = $_FILES['videoFiles']['tmp_name'][$key];
        $error    = $_FILES['videoFiles']['error'][$key];
        if ($error == 0) {
            $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $new_name = "video_".time().'_'.$key.'.'.$extension;
            move_uploaded_file(
                $tmp_name,
                '../../uploads/model/'.$new_name
            );

            $insert_query = "INSERT INTO model_videos(model_id,video_path) values (?,?)";
            $stmt2 = $con->prepare($insert_query);
            $stmt2->bind_param("is",$insertedModelId,$new_name);
            $stmt2->execute();
        }
    }
}

 
$insertedMeasId = mysqli_insert_id($con);
mysqli_stmt_close($stmtMeas);

    // ── COMMIT ───────────────────────────────
    mysqli_commit($con);

    jsonResponse(true, 'Model registered successfully.', [
        'status'             => "success", 
        'model_row_id'       => $insertedModelId,
        'model_id'           => $modelId,
        'measurement_row_id' => $insertedMeasId,
    ]);

} catch (Exception $e) {
    mysqli_rollback($con);
    jsonResponse(false, 'Error: ' . $e->getMessage(), [], 500);
}