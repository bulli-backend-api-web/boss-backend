<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

auth_start();

// Redirect if already logged in
if (!empty($_SESSION[SESSION_KEY])) {
    header('Location: ' . SITE_URL . '/pages/dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        $error = 'Invalid request. Please try again.';
    } elseif (auth_login(trim($_POST['username'] ?? ''), $_POST['password'] ?? '')) {
        header('Location: ' . SITE_URL . '/pages/dashboard.php');
        exit;
    } else {
        $error = 'Invalid username or password.';
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
        <link rel="shortcut icon" href="<?php echo SITE_URL; ?>/assets/media/favicon.png" />
        <link href="<?php echo SITE_URL; ?>/assets/css/plugins.bundle.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo SITE_URL; ?>/assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo SITE_URL; ?>/assets/css/styles.css" rel="stylesheet"/>
    </head>
    <body id="kt_body" class="app-blank">
        <div class="d-flex flex-column flex-root" id="kt_app_root">
            <div class="d-flex flex-column flex-lg-row flex-column-fluid">
                <div class="d-flex flex-column flex-lg-row-fluid w-lg-50 p-10 order-2 order-lg-1">
                    <div class="d-flex flex-center flex-column flex-lg-row-fluid">
                        <!--begin::Wrapper-->
                        <div class="w-100 w-md-500px p-5 p-lg-10 mx-auto">
                            <form class="form w-100" method="POST">
                                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                <div class="text-center mb-11">
                                    <h1 class="text-gray-900 fw-bolder mb-3 fs-2 fs-lg-1">Sign In</h1>
                                </div>
                                 <?php if ($error): ?>
                                    <div class="alert alert-danger text-center">Invalid Username or Password</div>
                                    <?php endif; ?>
                                <div class="fv-row mb-8">
                                    <input type="text" placeholder="Username" name="username" required autofocus class="form-control bg-transparent" />
                                </div>
                                <div class="fv-row mb-3">
                                    <input type="password" placeholder="Password" name="password" required class="form-control bg-transparent" />
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
                <div class="d-flex flex-lg-row-fluid w-lg-50 order-1 order-lg-2" style="background-image:url('<?php echo SITE_URL; ?>/assets/media/logos/custom-1.png');background-size:auto;background-position:center;min-height:320px;"></div>
                </div>
            </div>
       
        <script>var hostUrl = "assets/";</script>
        <script src="<?php echo SITE_URL; ?>/assets/js/plugins.bundle.js"></script>
        <script src="<?php echo SITE_URL; ?>/assets/js/scripts.bundle.js"></script>        
    </body>
</html>