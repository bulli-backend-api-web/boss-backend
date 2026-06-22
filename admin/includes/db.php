<?php
/**
 * Database Connection (PDO)
 */
function db_connect(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, DB_CHARSET);
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            die('Database connection failed: ' . $e->getMessage());
        }
    }
    return $pdo;
}

function db_query(string $sql, array $params = []): array {
    $stmt = db_connect()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function db_row(string $sql, array $params = []): ?array {
    $stmt = db_connect()->prepare($sql);
    $stmt->execute($params);
    $row = $stmt->fetch();
    return $row ?: null;
}

function db_execute(string $sql, array $params = []): int {
    $stmt = db_connect()->prepare($sql);
    $stmt->execute($params);
    return (int) db_connect()->lastInsertId() ?: $stmt->rowCount();
}
function my_simple_crypt($string, $action = 'encrypt_1') {
    $encrypt_method = "aes-192-cfb";
    $key = "adgUY";
    $iv  = "1234567812345678"; // 16 bytes for AES

    if ($action == 'encrypt_1') {
        return base64_encode(openssl_encrypt($string, $encrypt_method, $key, 0, $iv));
    } elseif ($action == 'decrypt_1') {
        return openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }
    return false;
}

function getUniqueRoles() {
    return db_query("SELECT id, slug, role_name FROM role");
}

function getSingleRoleCount($role) {
    $row = db_row(
        "SELECT COUNT(id) AS cnt FROM admin_users WHERE role = ?", 
        [$role]
    );

    return $row['cnt'] ?? 0;
}

function getAssignedPermissions($role_id) {
    return db_query("SELECT module_id FROM role_modules WHERE role_id = ?", [$role_id]);
}

function getAllPermissionCategory() {
    return db_query("SELECT id,category_name FROM module_category");
}

function getAllModules(){
    return db_query("SELECT id,category_id, module_name, slug,created_date FROM modules ORDER BY module_name ASC");
}
function makeSlug($string) {
    // Convert to lowercase
    $slug = strtolower($string);
    // Replace spaces & underscores with hyphen
    $slug = preg_replace('/[\s_]+/', '-', $slug);
    // Remove special characters
    $slug = preg_replace('/[^a-z0-9\-]/', '', $slug);
    // Remove multiple hyphens
    $slug = preg_replace('/-+/', '-', $slug);
    // Trim hyphens from ends
    return trim($slug, '-');
}
function getUserModules($role_id) {
    return db_query("SELECT module_id FROM role_modules WHERE role_id IN($role_id)");
    
}
function renderSidebar($site_path, $userModules, $currentPage) {
    global $con; 

    /* =============================
     * 1️⃣ Dashboard (Fixed)
     * ============================= */
    $dashboardActive = ($currentPage == 'dashboard') ? 'active' : '';
    ?>
    <div class="menu-item">
        <a class="menu-link <?php echo $dashboardActive; ?>" href="<?php echo $site_path; ?>/pages/dashboard">
            <span class="menu-icon"><i class="ki-outline ki-chart-line-up fs-2"></i></span>
            <span class="menu-title">Dashboard</span>
        </a>
    </div>
    <?php 

    /* =============================
     * 2️⃣ Fetch Categories
     * ============================= */
    $categories = [];

    $catRows = db_query("SELECT * FROM module_category ORDER BY sort_order ASC");

    foreach ($catRows as $cat) {
        $categories[$cat['id']] = $cat['category_name'];
    }


    // 2️⃣ Fetch Modules
    $modulesByCategory = [];

    $modRows = db_query("SELECT * FROM modules ORDER BY sort_order ASC");

    foreach ($modRows as $mod) {
        $modulesByCategory[$mod['category_id']][] = $mod;
    }
    /* =============================
     * 4️⃣ Render Menu
     * ============================= */
    foreach ($categories as $catId => $catTitle) {

        if (!isset($modulesByCategory[$catId])) continue;

        // Check access
        $hasAccess = false;
        foreach ($modulesByCategory[$catId] as $mod) {
            if (in_array($mod['id'], $userModules)) {
                $hasAccess = true;
                break;
            }
        }
        if (!$hasAccess) continue;

        // Category active state
        $isActive = '';
        foreach ($modulesByCategory[$catId] as $mod) {
            if ($currentPage == $mod['slug']) {
                $isActive = 'show';
                break;
            }
        }

        /* =============================
         * 5️⃣ Category Icons
         * ============================= */
        if ($catTitle == 'User Management') {
            $icon_class = 'ki-outline ki-abstract-28 fs-2';
        } elseif ($catTitle == 'Order Management') {
            $icon_class = 'ki-outline ki-basket fs-2';
        } elseif ($catTitle == 'Customer Management') {
            $icon_class = 'ki-outline ki-abstract-38 fs-2';
        } elseif ($catTitle == 'Report') {
            $icon_class = 'ki-outline ki-notepad-bookmark fs-2';
        } elseif ($catTitle == 'Print Label') {
            $icon_class = 'ki-outline ki-file-up fs-2';
        } elseif ($catTitle == 'Product Management') {
            $icon_class = 'ki-outline ki-tag fs-2';
        } elseif ($catTitle == ' Inquiry Management') {
            $icon_class = 'ki-outline ki-question fs-2';
        } elseif ($catTitle == 'Reviews Management') {
            $icon_class = 'ki-outline ki-star fs-2';
        } elseif ($catTitle == 'Job In / Out') {
            $icon_class = 'ki-outline ki-arrow-down fs-2';
        } elseif ($catTitle == 'DTO Management') {
            $icon_class = 'ki-outline ki-arrows-loop fs-2';
        } elseif ($catTitle == 'Master') {
            $icon_class = 'ki-outline ki-chart-line fs-2';
        }else if($catTitle == 'Mobile Notification'){
            $icon_class = 'ki-outline ki-notification-status fs-2';
        }else if($catTitle == 'Coupon Code'){
            $icon_class = 'ki-outline ki-discount fs-2';
        }else if($catTitle == 'Wish List Management'){
            $icon_class = 'ki-outline ki-heart fs-2';
        }else if($catTitle == 'Wallet Management'){
            $icon_class = 'ki-outline ki-wallet fs-2';
        }else if($catTitle == 'Help Management'){
            $icon_class = 'ki-outline ki-call fs-2';
        }else if($catTitle == 'Event Management'){
            $icon_class = 'ki-outline ki-calendar fs-2';
        }else if($catTitle == 'Ledger'){
            $icon_class = 'ki-outline ki-book-open fs-2';
        }else if($catTitle == 'OMS'){
            $icon_class = 'ki-outline ki-package fs-2';
        }else if($catTitle == 'User App'){
            $icon_class = 'ki-outline ki-setting-2 fs-2';
        }else if($catTitle == 'Become Reseller'){
            $icon_class = 'ki-outline ki-shop fs-2';
        }else if($catTitle == 'Become Whole Seller'){
            $icon_class = 'ki-outline ki-truck fs-2';
        } else {
            $icon_class = 'ki-outline ki-briefcase fs-2';
        }
        ?>

        <div data-kt-menu-trigger="click" class="menu-item menu-accordion <?php echo $isActive; ?> draggable-category" data-id="<?= $catId; ?>">
            <span class="menu-link">
                <span class="menu-icon"><i class="<?php echo $icon_class; ?>"></i></span>
                <span class="menu-title"><?php echo htmlspecialchars($catTitle); ?></span>
                <span class="menu-arrow"></span>
            </span>

            <?php
            /* =============================
             * 6️⃣ OMS → GROUPED MENU
             * ============================= */
            if ($catTitle == 'OMS') {

                $grouped = [];
                foreach ($modulesByCategory[$catId] as $mod) {
                    if (!in_array($mod['id'], $userModules)) continue;
                    if (empty($mod['group_name'])) continue;
                    $grouped[$mod['group_name']][] = $mod;
                }
                ?>

            <div class="menu-sub menu-sub-accordion">
                    <?php foreach ($grouped as $groupName => $mods) {

                        $groupActive = '';
                        foreach ($mods as $m) {
                            if ($currentPage == $m['slug']) {
                                $groupActive = 'show';
                                break;
                            }
                        }
                        ?>
                        <div data-kt-menu-trigger="click" class="menu-item menu-accordion <?php echo $groupActive; ?>">
                            <span class="menu-link">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title"><?php echo htmlspecialchars($groupName); ?></span>
                                <span class="menu-arrow"></span>
                            </span>

                            <div class="menu-sub menu-sub-accordion">
                                <?php foreach ($mods as $mod) {
                                    $active = ($currentPage == $mod['slug']) ? 'active' : '';
                                    ?>
                                    <div class="menu-item draggable-module" data-module-id="<?= $mod['id'] ?>">
                                        <a class="menu-link <?php echo $active; ?>" href="<?php echo $site_path.'/'.$mod['slug']; ?>">
                                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                            <span class="menu-title"><?php echo htmlspecialchars($mod['module_name']); ?></span>
                                        </a>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>

                <?php
            }
            /* =============================
             * 7️⃣ NORMAL MENU (All Others)
             * ============================= */
            else {
                    ?>
                <div class="menu-sub menu-sub-accordion">
                    <?php
                    foreach ($modulesByCategory[$catId] as $mod) {
                        if (!in_array($mod['id'], $userModules)) continue;
                        $active = ($currentPage == $mod['slug']) ? 'active' : '';
                        ?>
                        <div class="menu-item draggable-module" data-id="<?= $mod['id'] ?>">
                            <a class="menu-link <?php echo $active; ?>" href="<?php echo $site_path.'/'.$mod['slug']; ?>" >
                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                            <span class="menu-title"><?php echo htmlspecialchars($mod['module_name']); ?></span>
                        </a>
                    </div>
                    <?php
                }
                ?>
            </div>
            <?php } ?>
        </div>
        <?php
    }
}