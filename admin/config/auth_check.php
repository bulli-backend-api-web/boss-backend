<?php
/**
 * BASIC LOGIN CHECK
 */
if (empty($_SESSION['user']) || empty($_SESSION['login_session'])) {
    header("Location: index.php");
    exit;
}

$user          = $_SESSION['user'];
$login_session = $_SESSION['login_session'];

/**
 * SESSION TIMEOUT (24 MINUTES)
 */
$SESSION_EXPIRE_SECONDS = $_SESSION['SESSION_TIMEOUT'] ?? (24 * 60);
$current_time = time();

/**
 * IDLE AUTO LOGOUT CHECK
 */
if (isset($_SESSION['LAST_ACTIVITY'])) {
    if (($current_time - $_SESSION['LAST_ACTIVITY']) > $SESSION_EXPIRE_SECONDS) {
        $logout_time = date('Y-m-d H:i:s');
        $login_history_id = $_SESSION['login_history_id'] ?? 0;
        if ($login_history_id > 0) {
            $stmt = $con->prepare("
                UPDATE login_history
                SET logout_datetime =  NOW()
                WHERE id = ?
            ");
            $stmt->bind_param("i", $login_history_id);
            $stmt->execute();
            $user_id = '';
            $sql = "SELECT user_id FROM login_history where id = $login_history_id";
            $res = $con->query($sql);
            if($res && $res->num_rows > 0){
                $user_row = $res->fetch_assoc();
                $user_id = $user_row['user_id'];
            }
        }

        // Insert user activity
        $activity = "Auto logout due to inactivity (24 minutes)";
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $browser = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $action = "Auto Logout";
        $details = "Auto logout due to inactivity (24 minutes)";
        $stmt = $con->prepare("
            INSERT INTO activity_log
            (user_id, action, details, ip_address, user_agent)
            VALUES (?,?,?,?,?)
        ");
        $stmt->bind_param("issss", $user_id, $action,$activity, $ip, $browser);
        $stmt->execute();
        session_unset();
        session_destroy();
       header("Location: index.php");
        exit;
    }
}

/**
 * UPDATE ACTIVITY TIME
 * (THIS PREVENTS LOGOUT WHILE USER IS ACTIVE)
 */
$_SESSION['LAST_ACTIVITY'] = $current_time;

/**
 * VERIFY LOGIN SESSION FROM DATABASE
 */
$stmt = $con->prepare("
    SELECT id, typee, typee_id, username, email, profile_picture
                       FROM user 
    WHERE username = ? AND login_session = ?
    LIMIT 1
");
$stmt->bind_param("ss", $user, $login_session);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    session_destroy();
    header("Location: logout.php");
    exit;
}

/**
 * USER DATA
 */
$row              = $result->fetch_assoc();
$designation      = $row['typee'];
$typee_id         = $row['typee_id'];
$uname            = $row['username'];
$uid              = $row['id'];
$uemail           = $row['email'];
$profile_picture  = $row['profile_picture'];

/**
 * REMAINING IDLE TIME (FOR HEADER TIMER)
 */
$remaining_seconds =
    ($_SESSION['LAST_ACTIVITY'] + $SESSION_EXPIRE_SECONDS) - $current_time;

$remaining_seconds = max(0, $remaining_seconds);
