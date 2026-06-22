<?php
include("config/database.php");
function cleanInput($data) {
    return htmlspecialchars(trim(stripslashes($data)), ENT_QUOTES, 'UTF-8');
}
$loginError = false;
$unauthorize = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = cleanInput($_POST['myusername'] ?? '');
    $password = cleanInput($_POST['mypassword'] ?? '');

   $stmt = $con->prepare("SELECT id, username,name, typee,typee_id, mobile FROM user WHERE username = ? AND password = ? AND status = 1");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($user = $result->fetch_assoc()) {
        $_SESSION['username'] = $user['username'];
        $_SESSION['user'] = $user['username'];
        $_SESSION['typee'] = $user['typee'];
        $_SESSION['role_id'] = $user['id'];
        $browser_name = getBrowserName();

        // Generate random 6-digit login session
        $login_session = substr(str_shuffle("123456789"), 0, 6);
        $_SESSION['login_session'] = $login_session;
        $_SESSION['LOGIN_TIME'] = time(); 
        $_SESSION['LAST_ACTIVITY'] = time();      // Track user activity
        $_SESSION['SESSION_TIMEOUT'] = 24 * 60;
       
        // --- Update session token in user table ---
        $sql_update = "UPDATE user SET login_session = ? WHERE id = ?";
        $stmt_update = $con->prepare($sql_update);
        $stmt_update->bind_param("si", $login_session, $user['id']);
        $stmt_update->execute();

        // --- Log login history ---
        $ip = getClientIpAddress();
        $sql_history = "INSERT INTO login_history 
                        (user_id, typee_id, typee, login_date, login_datetime, ip_address,browser_name) 
                        VALUES (?, ?, ?, CURDATE(), NOW(), ?,?)";
        $stmt_history = $con->prepare($sql_history);
        $stmt_history->bind_param("iisss", $user['id'], $user['typee_id'], $user['typee'], $ip, $browser_name);
        $stmt_history->execute();

        $_SESSION['login_history_id'] = $stmt_history->insert_id;
        header("Location: dashboard");
        exit;
        
    }else{
       $loginError = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <!--begin::Head-->
    <head>
<base href="../../../" />
        <title>Ecommerce Admin Panel</title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="shortcut icon" href="<?php echo $site_path; ?>/assets/media/favicon.png" />
        <link href="<?php echo $site_path; ?>/assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $site_path; ?>/assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $site_path; ?>/assets/css/styles.css" rel="stylesheet"/>
    </head>
    <body id="kt_body" class="app-blank">
        <div class="d-flex flex-column flex-root" id="kt_app_root">
            <div class="d-flex flex-column flex-lg-row flex-column-fluid">
                <div class="d-flex flex-column flex-lg-row-fluid w-lg-50 p-10 order-2 order-lg-1">
                    <div class="d-flex flex-center flex-column flex-lg-row-fluid">
                        <!--begin::Wrapper-->
                        <div class="w-100 w-md-500px p-5 p-lg-10 mx-auto">
                            <form class="form w-100" method="POST">
                                <div class="text-center mb-11">
                                    <h1 class="text-gray-900 fw-bolder mb-3 fs-2 fs-lg-1">Welcome to Vastranand family</h1>
                                </div>
                                 <?php if ($loginError): ?>
                                    <div class="alert alert-danger text-center">Invalid Username or Password</div>
                                    <?php endif; ?>
                                    <?php if(!empty($unauthorize)){ ?>
                                    <div class="alert alert-danger text-center"><?php echo $unauthorize; ?></div>
                                    <?php }?>
                                <div class="fv-row mb-8">
                                    <input type="text" placeholder="Username" name="myusername" required autofocus class="form-control bg-transparent" />
                                </div>
                                <div class="fv-row mb-3">
                                    <input type="password" placeholder="Password" name="mypassword" required class="form-control bg-transparent" />
                                </div>
                                <div class="d-grid mb-10">
                                    <button type="submit" id="kt_sign_in_submit" class="btn btn-primary" style="background-color: #663E33;">
                                        <span class="indicator-label">Sign In</span>
                                        <span class="indicator-progress">Please wait... 
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
               <div class="d-flex flex-lg-row-fluid w-lg-50 order-1 order-lg-2" style="background-image:url('<?php echo $site_path; ?>/assets/media/images/custom-1.png');background-position:top center;min-height:320px;"></div>
                </div>
            </div>
        <script src="<?php echo $site_path; ?>/assets/plugins/global/plugins.bundle.js"></script>
        <script src="<?php echo $site_path; ?>/assets/js/scripts.bundle.js"></script>        
    </body>
</html>