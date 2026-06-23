<?php
use Google\Auth\ApplicationDefaultCredentials;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions; 
    
// ================== getData Function ==================
function getData($table, $fields = "*", $where = [], $limit = null, $orderBy = null) {
    global $con;

    // Sanitize table and fields (basic – for column/table names we can't bind)
    $table = preg_replace('/[^a-zA-Z0-9_]/', '', $table);

    if (is_array($fields)) {
        $fields = implode(",", array_map(function ($f) {
            return preg_replace('/[^a-zA-Z0-9_]/', '', $f);
        }, $fields));
    }

    $sql = "SELECT $fields FROM `$table`";
    $types = "";
    $values = [];

    // Add WHERE conditions with placeholders
    if (!empty($where)) {
        $conditions = [];
        foreach ($where as $col => $val) {
            $col = preg_replace('/[^a-zA-Z0-9_]/', '', $col);
            $conditions[] = "`$col` = ?";
            $types .= "s"; // all values as string, adjust if you want int (i), double (d), etc.
            $values[] = $val;
        }
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    // Add ORDER BY (safe only for fixed column names)
    if ($orderBy) {
        $sql .= " ORDER BY $orderBy";
    }

    // Add LIMIT
    if ($limit) {
        $sql .= " LIMIT ?";
        $types .= "i";
        $values[] = (int)$limit;
    }

    $stmt = mysqli_prepare($con, $sql);
    if (!$stmt) {
        die("Prepare failed: " . mysqli_error($con));
    }

    // Bind parameters dynamically
    if (!empty($values)) {
        mysqli_stmt_bind_param($stmt, $types, ...$values);
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $data = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
    }

    mysqli_stmt_close($stmt);
    return $data;
}

// ================== timeAgo Function ==================
function timeAgo($date) {
    $given = new DateTime($date);
    $now   = new DateTime();
    $seconds = $now->getTimestamp() - $given->getTimestamp();
    $isFuture = $seconds < 0;
    $seconds = abs($seconds);

    // NOW
    if ($seconds < 5) {
        return "Now";
    }

    // SECONDS
    if ($seconds < 60) {
        return $isFuture
            ? "In {$seconds} seconds"
            : "{$seconds} seconds ago";
    }

    // MINUTES
    $minutes = floor($seconds / 60);
    if ($minutes < 60) {
        return $isFuture
            ? "In {$minutes} minute" . ($minutes > 1 ? "s" : "")
            : "{$minutes} minute" . ($minutes > 1 ? "s" : "") . " ago";
    }

    // HOURS
    $hours = floor($minutes / 60);
    if ($hours < 24) {
        return $isFuture
            ? "In {$hours} hour" . ($hours > 1 ? "s" : "")
            : "{$hours} hour" . ($hours > 1 ? "s" : "") . " ago";
    }

    // DAYS
    $days = floor($hours / 24);

    if ($days == 1 && !$isFuture) {
        return "Yesterday";
    }

    if ($days == 0 && !$isFuture) {
        return "Today";
    }

    return $isFuture
        ? "In {$days} day" . ($days > 1 ? "s" : "")
        : "{$days} day" . ($days > 1 ? "s" : "") . " ago";
}

// ================== Encryption/Decryption Function ==================
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
    global $con;

    $roles = [];

    $sql = "SELECT id,slug,role_name from role";
    $stmt = mysqli_prepare($con, $sql);
    if (!$stmt) {
        die("Prepare failed: " . mysqli_error($con));
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($result)) {
       $roles[] = $row;
    }

    mysqli_stmt_close($stmt);
    return $roles;
}

function getRoleCount($typee) {
    global $con;

    $sql = "SELECT COUNT(id) AS cnt FROM user WHERE typee = ?";
    $stmt = mysqli_prepare($con, $sql);
    if (!$stmt) {
        die("Prepare failed: " . mysqli_error($con));
    }

    // Bind the role (typee)
    mysqli_stmt_bind_param($stmt, "s", $typee);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $count = 0;
    if ($row = mysqli_fetch_assoc($result)) {
        $count = $row['cnt'];
    }

    mysqli_stmt_close($stmt);
    return $count;
}

function getUsersByRole($typee = null) {
    global $con;
    $users = [];

    if ($typee) {
        $sql = "SELECT user.id, username, name, email, mobile, typee FROM user JOIN role on role.slug = user.typee WHERE typee_id = ?";
        $stmt = mysqli_prepare($con, $sql);
        if (!$stmt) {
            die("Prepare failed: " . mysqli_error($con));
        }
        mysqli_stmt_bind_param($stmt, "i", $typee);
    } else {
        $sql = "SELECT id, username, name, email, mobile, typee FROM user";
        $stmt = mysqli_prepare($con, $sql);
        if (!$stmt) {
            die("Prepare failed: " . mysqli_error($con));
        }
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($result)) {
        $key   = strtolower(trim($row['typee']));
        $label = ucwords(str_replace("-", " ", $key));

        // Group users by role
        $users[$key]['label'] = $label;
        $users[$key]['list'][] = [
            "id"       => $row['id'],
            "username" => $row['username'],
            "name"     => $row['name'],
            "email"    => $row['email'],
            "mobile"   => $row['mobile']
        ];
    }

    mysqli_stmt_close($stmt);

    return $users;
}

function getAllModules() {
    global $con;
    $sql = "SELECT id,category_id, module_name, slug,created_date FROM modules ORDER BY module_name ASC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $modules = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $modules[] = $row;
    }
    return $modules;
}

function getBrowserName() {
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $browser = "Unknown Browser";

    if (strpos($userAgent, 'Edge') !== false) {
        $browser = "Microsoft Edge";
    } elseif (strpos($userAgent, 'OPR') !== false || strpos($userAgent, 'Opera') !== false) {
        $browser = "Opera";
    } elseif (strpos($userAgent, 'Chrome') !== false) {
        $browser = "Google Chrome";
    } elseif (strpos($userAgent, 'Safari') !== false) {
        $browser = "Safari";
    } elseif (strpos($userAgent, 'Firefox') !== false) {
        $browser = "Mozilla Firefox";
    } elseif (strpos($userAgent, 'MSIE') !== false || strpos($userAgent, 'Trident/') !== false) {
        $browser = "Internet Explorer";
    }

    return $browser;
}

function getListpermissionAssigned($module_id){
    global $con;  
    $returnArray = [];  
    $sql = "SELECT role.role_name,role.id,role.slug,role_modules.role_id from role LEFT JOIN role_modules ON role_modules.role_id = role.id where module_id = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $module_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while($row = mysqli_fetch_assoc($result)) {
        $returnArray[] = $row;
    }
    return $returnArray;
}

function getAssignedPermissions($role_id) {
    global $con;  
    $assignedModules = [];

    $sql = "SELECT module_id FROM role_modules WHERE role_id = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $role_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($result)) {
        $assignedModules[] = $row['module_id'];
    }

    return $assignedModules;
}

function logActivity($user_id, $action, $details = null) {
    global $con; 
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN';

    $sql = "INSERT INTO activity_log (user_id, action, details, ip_address, user_agent) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "issss", $user_id, $action, $details, $ip, $userAgent);
    mysqli_stmt_execute($stmt);
}

function getUserActivity($user_id) {
    global $con;
    $all_activity = [];
    $sql = "SELECT a.*, u.username 
            FROM activity_log a 
            JOIN user u ON a.user_id = u.id 
            WHERE a.user_id = ? 
            ORDER BY a.created_at DESC";

    if ($stmt = mysqli_prepare($con, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result)) {
            $all_activity[] = $row;
        }

        mysqli_stmt_close($stmt);
    }

    return $all_activity;
}

function getAllPermissionCategory() {
    global $con;
    $allModuleCategory = [];
    $sql = "SELECT id,category_name FROM module_category";

    if ($stmt = mysqli_prepare($con, $sql)) {
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result)) {
            $allModuleCategory[] = $row;
        }

        mysqli_stmt_close($stmt);
    }

    return $allModuleCategory;
}

function getUserModules($role_id) {
    global $con;
    $moduleIds = [];
    if(!$role_id) return $moduleIds;
    // Get module IDs for this role
    $sql = "SELECT module_id FROM role_modules WHERE role_id IN($role_id) ";
    $stmt = mysqli_prepare($con, $sql);
    //mysqli_stmt_bind_param($stmt, "s", $role_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    while($row = mysqli_fetch_assoc($result)){
        $moduleIds[] = $row['module_id'];
    }

    mysqli_stmt_close($stmt);
    return $moduleIds;
}

function getUserDetailsByID($user_id){
    global $con;  
    $returnArray = [];  
    $sql = "SELECT name,mobile,address,email,typee,country,profile_picture FROM user where id = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while($row = mysqli_fetch_assoc($result)) {
        $returnArray[] = $row;
    }
    return $returnArray;
}

function renderSidebar($site_path, $userModules, $currentPage) {
    global $con; 

    /* =============================
     * 1️⃣ Dashboard (Fixed)
     * ============================= */
    $dashboardActive = ($currentPage == 'dashboard') ? 'active' : '';
    $alertActive = ($currentPage == 'alerts') ? 'active' : '';
    ?>
    <div class="menu-item">
        <a class="menu-link <?php echo $dashboardActive; ?>" href="<?php echo $site_path; ?>/dashboard">
            <span class="menu-icon"><i class="ki-outline ki-chart-line-up fs-2"></i></span>
            <span class="menu-title">Dashboard</span>
        </a>
    </div>

    <div class="menu-item">
        <a class="menu-link <?php echo $alertActive; ?>" href="<?php echo $site_path; ?>/live-alerts">
            <span class="menu-icon"><i class="ki-outline ki-notification-bing fs-1"></i></span>
            <span class="menu-title">Alerts</span>
        </a>
    </div>
    <?php 

    /* =============================
     * 2️⃣ Fetch Categories
     * ============================= */
    $categories = [];
    $catSql = "SELECT * FROM module_category ORDER BY sort_order ASC";
    $catResult = mysqli_query($con, $catSql);
    while ($cat = mysqli_fetch_assoc($catResult)) {
        $categories[$cat['id']] = $cat['category_name'];
    }

    /* =============================
     * 3️⃣ Fetch Modules
     * ============================= */
    $modulesByCategory = [];
    $modSql = "SELECT * FROM modules ORDER BY sort_order ASC";
    $modResult = mysqli_query($con, $modSql);
    while ($mod = mysqli_fetch_assoc($modResult)) {
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
        
        $classDetails = get_module_class_by_category();
        $icon_class = isset($classDetails[$catTitle]) ? $classDetails[$catTitle] : "ki-outline ki-briefcase fs-2";
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
            if ($catTitle == 'Design Studio') {

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
            }else if($catTitle == 'Production'){
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
            } else if($catTitle == 'Inventory & QC'){
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
            } else if($catTitle == 'Staff & HR'){
                $grouped = [];
                foreach ($modulesByCategory[$catId] as $mod) {
                    if (!in_array($mod['id'], $userModules)) continue;
                        if (empty(trim($mod['group_name']))) {
                            $ungrouped1[] = $mod;
                        } else {
                            $grouped[$mod['group_name']][] = $mod;
                        }
                    }
                ?>

            <div class="menu-sub menu-sub-accordion">
                 <!-- Grouped Menu Items -->
                <?php foreach ($grouped as $groupName => $mods) {

                    $groupActive = '';
                    foreach ($mods as $m) {
                        if ($currentPage == $m['slug']) {
                            $groupActive = 'show';
                            break;
                        }
                    }
                ?>
                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion <?= $groupActive ?>">
                        <span class="menu-link">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title"><?= htmlspecialchars($groupName) ?></span>
                            <span class="menu-arrow"></span>
                        </span>

                        <div class="menu-sub menu-sub-accordion">
                            <?php foreach ($mods as $mod) {
                                $active = ($currentPage == $mod['slug']) ? 'active' : '';
                            ?>
                                <div class="menu-item draggable-module" data-module-id="<?= $mod['id'] ?>">
                                    <a class="menu-link <?= $active ?>" href="<?= $site_path.'/'.$mod['slug'] ?>">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title"><?= htmlspecialchars($mod['module_name']) ?></span>
                                    </a>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
                 
                <!-- Direct Menu Items (No Group) -->
                <?php foreach ($ungrouped1 as $mod) {
                    $active = ($currentPage == $mod['slug']) ? 'active' : '';
                ?>
                    <div class="menu-item draggable-module" data-module-id="<?= $mod['id'] ?>">
                        <a class="menu-link <?= $active ?>" href="<?= $site_path.'/'.$mod['slug'] ?>">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title"><?= htmlspecialchars($mod['module_name']) ?></span>
                        </a>
                    </div>
                <?php } ?>
            </div>
            <?php }
            else if($catTitle == 'Sales'){
                $grouped = [];
                foreach ($modulesByCategory[$catId] as $mod) {
                    if (!in_array($mod['id'], $userModules)) continue;
                        if (empty(trim($mod['group_name']))) {
                            $ungrouped[] = $mod;
                        } else {
                            $grouped[$mod['group_name']][] = $mod;
                        }
                    }
                ?>

            <div class="menu-sub menu-sub-accordion">
                 <!-- Grouped Menu Items -->
                <?php foreach ($grouped as $groupName => $mods) {

                    $groupActive = '';
                    foreach ($mods as $m) {
                        if ($currentPage == $m['slug']) {
                            $groupActive = 'show';
                            break;
                        }
                    }
                ?>
                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion <?= $groupActive ?>">
                        <span class="menu-link">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title"><?= htmlspecialchars($groupName) ?></span>
                            <span class="menu-arrow"></span>
                        </span>

                        <div class="menu-sub menu-sub-accordion">
                            <?php foreach ($mods as $mod) {
                                $active = ($currentPage == $mod['slug']) ? 'active' : '';
                            ?>
                                <div class="menu-item draggable-module" data-module-id="<?= $mod['id'] ?>">
                                    <a class="menu-link <?= $active ?>" href="<?= $site_path.'/'.$mod['slug'] ?>">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title"><?= htmlspecialchars($mod['module_name']) ?></span>
                                    </a>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
                 
                <!-- Direct Menu Items (No Group) -->
                <?php foreach ($ungrouped as $mod) {
                    $active = ($currentPage == $mod['slug']) ? 'active' : '';
                ?>
                    <div class="menu-item draggable-module" data-module-id="<?= $mod['id'] ?>">
                        <a class="menu-link <?= $active ?>" href="<?= $site_path.'/'.$mod['slug'] ?>">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title"><?= htmlspecialchars($mod['module_name']) ?></span>
                        </a>
                    </div>
                <?php } ?>
            </div>
            <?php }
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

function checkEmail($email) { 
    if ( strpos($email, '@') !== false ) { 
        $split = explode('@', $email); return (strpos($split['1'], '.') !== false ? true : false); } else 
        { 
            return false; 
        }  
}

function is_digits($element) {
    return !preg_match ("/[^0-9]/", $element); 
}


function getScanAppModules() {
    global $con;
    $app_modules = [];
    $sql = "SELECT id,name from app_modules";
    $stmt = mysqli_prepare($con, $sql);
    if (!$stmt) {
        die("Prepare failed: " . mysqli_error($con));
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($result)) {
       $app_modules[] = $row;
    }

    mysqli_stmt_close($stmt);
    return $app_modules;
}

function allScanAPPAssignModule($user_id){
    global $con;  
    $returnArray = [];  
    $sql = "SELECT module_id from app_assign_modules where user_id=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while($row = mysqli_fetch_assoc($result)) {
        $returnArray[] = $row;
    }
    return $returnArray;
}
function getAllStaffList(){
    global $con;
    $user_list = [];
    $user_sql = "SELECT id,name from user where status = 1";
    if ($stmt = mysqli_prepare($con, $user_sql)) {
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result)) {
            $user_list[] = $row;
        }

        mysqli_stmt_close($stmt);
    }
    return $user_list;
}

function getClientIpAddress()
{
    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        // Cloudflare
        return $_SERVER['HTTP_CF_CONNECTING_IP'];
    }

    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // Can contain multiple IPs – take the first one
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($ips[0]);
    }

    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    }

    return $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
}

function getAllProductList(){
    global $con;  
    $product_list = [];
    $user_sql = "SELECT id,name,sku,min_price,category from product where status = 1";
    if ($stmt = mysqli_prepare($con, $user_sql)) {
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result)) {
            $product_list[] = $row;
    }

        mysqli_stmt_close($stmt);
    }
    return $product_list;
}
    
function getCategoryList(){
    global $con;
    $category_list = [];
    $user_sql = "SELECT id,name from category where status = 1 order by name asc";
    if ($stmt = mysqli_prepare($con, $user_sql)) {
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result)) {
            $category_list[] = $row;
        }

        mysqli_stmt_close($stmt);
    }
    return $category_list;
}

function get_inward_no(){
    $year = date('Y');
    global $con;
    $sql = "SELECT id FROM product_wise_stock ORDER BY id DESC LIMIT 1";
    $res = $con->query($sql);

    if($res && $res->num_rows > 0){

        $row = $res->fetch_assoc();

        $next_id = $row['id'] + 1;

    } else {

        $next_id = 1;
    }

    $running_no = str_pad($next_id, 3, '0', STR_PAD_LEFT);

    $inward_no = "IN-".$year."-".$running_no;

    return $inward_no;
}

function get_alteration_no(){
    global $con;
    $sql = "SELECT id FROM alteration_requests ORDER BY id DESC LIMIT 1";
    $res = $con->query($sql);

    if($res && $res->num_rows > 0){

        $row = $res->fetch_assoc();

        $next_id = $row['id'] + 1;

    } else {

        $next_id = 1;
    }

    $running_no = str_pad($next_id, 3, '0', STR_PAD_LEFT);

    $alt_no = "ALT-".$running_no;

    return $alt_no;
}

function getChannelList(){
    global $con;
    $channel_list = [];
    $user_sql = "SELECT id,name from channel where status = 1 order by id";
    if ($stmt = mysqli_prepare($con, $user_sql)) {
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result)) {
            $channel_list[] = $row;
        }

        mysqli_stmt_close($stmt);
    }
    return $channel_list;
}

function get_order_invoice_no(){
    global $con;
    $query = "SELECT invoice_no FROM orderr ORDER BY id DESC LIMIT 1";
    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) > 0) {

        $row = mysqli_fetch_assoc($result);

        // Example: BK-001
        $lastInvoice = $row['invoice_no'];

        // Get numeric part
        $number = (int) str_replace('BK-', '', $lastInvoice);

        // Increment number
        $number++;

    } else {

        // First invoice
        $number = 1;
    }

    // Generate new invoice number
    $newInvoice = 'BK-' . str_pad($number, 3, '0', STR_PAD_LEFT);
    return $newInvoice;
}
function getAllStateList(){
    global $con;
    $stateList = [];
    $sql = "SELECT id,name,state_code FROM m_state where status=1";

    if ($stmt = mysqli_prepare($con, $sql)) {
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result)) {
            $stateList[] = $row;
        }

        mysqli_stmt_close($stmt);
    }

    return $stateList;
}

function getAllCountryList(){
    global $con;
    $countryList = [];
    $sql = "SELECT id,name FROM m_country where status=1";

    if ($stmt = mysqli_prepare($con, $sql)) {
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result)) {
            $countryList[] = $row;
        }

        mysqli_stmt_close($stmt);
    }

    return $countryList;
}

function getAllStoreList(){
    global $con;
    $storeList = [];
    $sql = "SELECT id,store_name FROM store";

    if ($stmt = mysqli_prepare($con, $sql)) {
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result)) {
            $storeList[] = $row;
        }

        mysqli_stmt_close($stmt);
    }

    return $storeList;
}

function getAllWholesalerList(){
    global $con;
    $wholeSalerList = [];
    $sql = "SELECT id,business_name FROM wholesaler";

    if ($stmt = mysqli_prepare($con, $sql)) {
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result)) {
            $wholeSalerList[] = $row;
        }

        mysqli_stmt_close($stmt);
    }

    return $wholeSalerList;
}

function generateChallanNo() 
{
    global $con;

    $sql = "SELECT challan_no
            FROM stock_inward_batch
            ORDER BY id DESC
            LIMIT 1";

    $res = $con->query($sql);

    $next_no = 1;

    if ($res && $res->num_rows > 0)
    {
        $row = $res->fetch_assoc();

        if (!empty($row['challan_no']))
        {
            preg_match('/FSICH-(\d+)/', $row['challan_no'], $match);

            if (!empty($match[1]))
            {
                $next_no = (int)$match[1] + 1;
            }
        }
    }

    return "FSICH-" . $next_no;
}

function generate_task_no(){
    global $con;
    $date = date('Ymd');

    /* Get today's last task */
    $sql = "SELECT task_no FROM task_master WHERE task_no LIKE 'TASK-$date-%' ORDER BY id DESC LIMIT 1";
    $res = mysqli_query($con, $sql);
    if(mysqli_num_rows($res) > 0){
        $row = mysqli_fetch_assoc($res);
        $last_no = explode('-', $row['task_no']);
        $sequence = (int)$last_no[2] + 1;
    }
    else{
        $sequence = 1;
    }

    $task_no = 'TASK-' . $date . '-' . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    return $task_no;
}

function getAllDepartments(){
    global $con;
    $sql = "SELECT id,department_name FROM departments ORDER BY department_name ASC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $departments = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $departments[] = $row;
    }
    return $departments;
}

function getTaskStatusBadge($status)
{
    switch ($status) {
        case 'Pending':
            return '<span class="badge badge-light-warning">Pending</span>';

        case 'Done':
            return '<span class="badge badge-light-success">Done</span>';

        case 'Overdue':
            return '<span class="badge badge-light-danger">Overdue</span>';

        case 'Delay':
            return '<span class="badge badge-light-info">Delayed</span>';

        default:
            return '<span class="badge badge-light-secondary">'
                    .ucfirst($status).
                   '</span>';
    }
}
function getOrderExtraStatus(){
    global $con;
    $order_extra_status = [];
    $sql = "SELECT * FROM m_order_status_extra where status=1";

    if ($stmt = mysqli_prepare($con, $sql)) {
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result)) {
            $order_extra_status[] = $row;
        }

        mysqli_stmt_close($stmt);
    }

    return $order_extra_status;
}

function formatOverdue($due_date) {
    $diff = (new DateTime())->diff(new DateTime($due_date));
    if ($diff->days > 0)  return $diff->days . " day" . ($diff->days > 1 ? "s" : "");
    elseif ($diff->h > 0) return $diff->h . " hr" . ($diff->h > 1 ? "s" : "");
    elseif ($diff->i > 0) return $diff->i . " min";
    else                  return "Just now";
}

function get_department_performance()
{
    global $con;
    $performance_list = [];
    $sql = "SELECT d.id, d.department_name, u.name AS employee_name, COUNT(tm.id) AS total_tasks, SUM(CASE WHEN tm.status = 'Completed' THEN 1 ELSE 0 END) AS completed_tasks,
        ROUND(
            (SUM(CASE WHEN tm.status = 'Completed' THEN 1 ELSE 0 END) * 100) /
            COUNT(tm.id)
        ) AS completion_percentage
    FROM task_master tm
    LEFT JOIN departments d ON d.id = tm.department_id
    LEFT JOIN user u ON u.id = tm.assigned_to
    GROUP BY tm.department_id
";

    $stmt = $con->prepare($sql);
    $stmt->execute();

    $result = $stmt->get_result();
    while($row = $result->fetch_assoc()){
        $performance_list[] = $row;
    }

    return $performance_list;
}

function get_completion_by_task_type(){
    global $con;
    $response = [];
    $sql = "SELECT d.department_name,tm.task_type,COUNT(tm.id) AS total_tasks,
    SUM(
        CASE
            WHEN tm.status = 'Completed'
            AND YEARWEEK(tm.completed_at, 1) = YEARWEEK(CURDATE(), 1)
            THEN 1
            ELSE 0
        END
    ) AS completed_tasks FROM task_master tm INNER JOIN departments d ON d.id = tm.department_id GROUP BY tm.department_id, tm.task_type ORDER BY d.department_name";

    $result = mysqli_query($con, $sql);
    while($row = mysqli_fetch_assoc($result)){
        $response[] = $row;
    }
    
    return $response;
}

function getOverallCompletion()
{
    global $con;

    $sql = "
        SELECT
            COUNT(id) AS total_tasks,

            SUM(
                CASE
                    WHEN status = 'Completed'
                    AND YEARWEEK(completed_at, 1) = YEARWEEK(CURDATE(), 1)
                    THEN 1
                    ELSE 0
                END
            ) AS completed_this_week,

            SUM(
                CASE
                    WHEN status = 'Completed'
                    AND YEARWEEK(completed_at, 1) = YEARWEEK(CURDATE(), 1) - 1
                    THEN 1
                    ELSE 0
                END
            ) AS completed_last_week

        FROM task_master
    ";

    $stmt = $con->prepare($sql);
    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $total_tasks = (int)$row['total_tasks'];
    $completed_this_week = (int)$row['completed_this_week'];
    $completed_last_week = (int)$row['completed_last_week'];

    $completion_percentage = ($total_tasks > 0)
        ? round(($completed_this_week / $total_tasks) * 100)
        : 0;

    $change = $completed_this_week - $completed_last_week;

    $stmt->close();

    return [
        'total_tasks'          => $total_tasks,
        'completed_this_week'  => $completed_this_week,
        'completed_last_week'  => $completed_last_week,
        'completion_percentage'=> $completion_percentage,
        'change'               => $change
    ];
}

function push_notification_android($divice_token,$message,$title,$id){
      
   require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
    
    $projectId = 'bullionknot-5afd2';
    $deviceToken = $divice_token;
    $serviceAccountPath = __DIR__ . '/firebase-service-account.json';
    putenv("GOOGLE_APPLICATION_CREDENTIALS=$serviceAccountPath");
    
    $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];
    $middleware = ApplicationDefaultCredentials::getMiddleware($scopes);
    $stack = \GuzzleHttp\HandlerStack::create();
    $stack->push($middleware);

    $client = new Client([
        'handler' => $stack,
        'auth' => 'google_auth',
    ]);
    
    $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";
	
	$dataPayload = [
            'msg' => $message,
            'title' => $title,
            'task_id' => (string)$id
        ];
        
    $body = [
        'message' => [
            'token' => $deviceToken,
            'android' => [
                'priority' => 'HIGH'
            ],
            'data' => $dataPayload
        ]
    ];

    try {
        $response = $client->post($url, [
            RequestOptions::JSON => $body
        ]);
        $result = json_decode($response->getBody(), true);
        // Optionally log success:
         echo "Sent to $deviceToken: " . json_encode($result) . "\n";
    } catch (Exception $e) {
        // Handle failure
        echo "Failed to send to $deviceToken: " . $e->getMessage() . "\n";
    }
}

function generateTicketId(string $type): string {
    $prefix = strtolower($type) === 'return' ? 'RET' : 'EXCH';
    $date   = date('Ymd');
    $suffix = strtoupper(substr(bin2hex(random_bytes(3)), 0, 5)); // e.g. A3F9C
    return $prefix . '-' . $date . '-' . $suffix;
}

function getResonList(){
    global $con;
    $reason_qry = "SELECT id,name,status from m_order_return_remark";
    $reasonRes = $con->query($reason_qry);
    $reasonArray = [];
    if ($reasonRes && $reasonRes->num_rows > 0) {
        while ($reason_row = $reasonRes->fetch_assoc()) {
            $reasonArray[$reason_row['id']] = $reason_row['name'];
        }
    }
    
    return $reasonArray;
}
function generateModelId(){
    global $con;
    $result = mysqli_query($con, "SELECT COUNT(*) AS total FROM models");
    $row    = mysqli_fetch_assoc($result);
    $count  = (int) $row['total'];
    return 'EMI-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);
}

function get_all_tag_list(){
    global $con;
    $tag_sql = "SELECT id,name,status from category";
    $tag_res = $con->query($tag_sql);
    $tag_list = [];
    if ($tag_res && $tag_res->num_rows > 0) {
        while ($tag_row = $tag_res->fetch_assoc()) {
            $tag_list[] = $tag_row;
        }
    }
    
    return $tag_list;
}


function departmentwise_user_list($department_id){
    global $con;
    $user_list = [];
    $user_sql = "SELECT id,name from user where status = 1 and department_id = '$department_id'";
    if ($stmt = mysqli_prepare($con, $user_sql)) {
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result)) {
            $user_list[] = $row;
        }

        mysqli_stmt_close($stmt);
    }
    return $user_list;
}

function generate_sample_no(){
    global $con;
    $result = mysqli_query($con, "SELECT COUNT(*) AS total FROM sampling");
    $row    = mysqli_fetch_assoc($result);
    $count  = (int) $row['total'];
    return 'S-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);
}

function model_list(){
    global $con;
    $model_list = [];
    $model_sql = "SELECT id,first_name, last_name from models order by first_name";
    if ($stmt = mysqli_prepare($con, $model_sql)) {
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result)) {
            $model_list[] = $row;
        }

        mysqli_stmt_close($stmt);
    }
    return $model_list;
}

function generate_design_code(){
    global $con;
    $stmt = $con->prepare("SELECT design_code FROM design WHERE design_code LIKE ? ORDER BY id DESC LIMIT 1");

    $prefix = 'DSN%';
    $stmt->bind_param('s', $prefix);
    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
        $number = (int) substr($row['design_code'], 3);
        $number++;
    } else {
        $number = 1;
    }

    return 'DSN' . str_pad($number, 4, '0', STR_PAD_LEFT);
}

function get_module_class_by_category(){
    global $con;
    $classList = [];
    $sql = "SELECT category_name,icon from module_category order by category_name";
    if ($stmt = mysqli_prepare($con, $sql)) {
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result)) {
            $classList[$row['category_name']] = $row['icon'];
        }

        mysqli_stmt_close($stmt);
    }
    return $classList;
}

function generate_staff_code(){
    global $con;
    $date = date('Y');
    $result = mysqli_query($con, "SELECT COUNT(*) AS total FROM staff_register");
    $row    = mysqli_fetch_assoc($result);
    $count  = (int) $row['total'];
    return 'BK-STF-'.$date."-". str_pad($count + 1, 3, '0', STR_PAD_LEFT);
}

function get_fabric_type_list(){
    global $con;
    $fabric_type_list = [];
    $sql = "SELECT id,name from fabric_type order by name";
    if ($stmt = mysqli_prepare($con, $sql)) {
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result)) {
            $fabric_type_list[] = $row;
        }

        mysqli_stmt_close($stmt);
    }
    
    return $fabric_type_list;
}

function get_register_staff_list() {
    global $con;

    $register_staff_list = [];

    $sql  = "SELECT id, fullname FROM staff_register ORDER BY fullname ASC";
    $stmt = $con->prepare($sql);

    if (!$stmt) {
        return $register_staff_list;
    }

    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $register_staff_list[] = $row;
    }

    $stmt->close();

    return $register_staff_list;
}

function get_employment_type_list() {
    global $con;

    $employment_type_list = [];

    $sql  = "SELECT id, name FROM employment_type ORDER BY id ASC";
    $stmt = $con->prepare($sql);

    if (!$stmt) {
        return $employment_type_list;
    }

    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $employment_type_list[] = $row;
    }

    $stmt->close();

    return $employment_type_list;
}