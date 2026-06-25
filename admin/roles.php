<?php
include("config/database.php");
include("config/auth_check.php");
include("includes/sidemenu.php");
$users_role = getUniqueRoles();
$allModules = getAllModules();
$assignedModules = getAssignedPermissions($typee_id);
$permission_category_list = getAllPermissionCategory();
$allPermissions = getAllModules();

$dept_icons = [
    'design' => ['icon' => '✏️', 'color' => '#EDE9FF', 'accent' => '#7C6FF7'],
    'qc' => ['icon' => '📋', 'color' => '#FFF3E0', 'accent' => '#F59E0B'],
    'quality' => ['icon' => '📋', 'color' => '#FFF3E0', 'accent' => '#F59E0B'],
    'production' => ['icon' => '⚙️', 'color' => '#E8F5E9', 'accent' => '#22C55E'],
    'cutting' => ['icon' => '✂️', 'color' => '#E8F5E9', 'accent' => '#22C55E'],
    'purchase' => ['icon' => '🛒', 'color' => '#FFF8E1', 'accent' => '#D97706'],
    'dispatch' => ['icon' => '🚚', 'color' => '#FDE8E8', 'accent' => '#EF4444'],
    'hr' => ['icon' => '📝', 'color' => '#E0F2FE', 'accent' => '#3B82F6'],
    'admin' => ['icon' => '🔐', 'color' => '#E0F2FE', 'accent' => '#3B82F6'],
    'finance' => ['icon' => '💰', 'color' => '#ECFDF5', 'accent' => '#10B981'],
    'account' => ['icon' => '💰', 'color' => '#ECFDF5', 'accent' => '#10B981'],
    'sampling' => ['icon' => '🧵', 'color' => '#F3E8FF', 'accent' => '#A855F7'],
    'sampler' => ['icon' => '🧵', 'color' => '#F3E8FF', 'accent' => '#A855F7'],
    'it' => ['icon' => '💻', 'color' => '#E0F2FE', 'accent' => '#0EA5E9'],
    'default' => ['icon' => '👤', 'color' => '#F1F5F9', 'accent' => '#64748B'],
];

function getRoleStyle($role_name, $dept_name, $icons) {
    $search = strtolower($role_name . ' ' . $dept_name);
    foreach ($icons as $key => $val) {
        if ($key !== 'default' && strpos($search, $key) !== false)
            return $val;
    }
    return $icons['default'];
}
?>

<link href="<?php echo $site_path; ?>/assets/css/roles.css?v=<?php echo time(); ?>" rel="stylesheet" type="text/css" />

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_content" class="app-content">
            <div id="kt_app_content_container" class="app-container container-fluid">
                <div class="rm">

                    <!-- Header -->
                    <div class="rm-header">
                        <h2 class="rm-title">User Management &amp; Role Matrix</h2>
                        <p class="rm-sub">Admin only <span>·</span> Define roles <span>·</span> Set permissions per module <span>·</span> Assign users <span>·</span> Activate logins</p>
                    </div>

                    <!-- Tabs -->
                    <div class="rm-tabs">
                        <button class="rm-tab active" data-tab="roles">
                            <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="10" cy="8" r="3"/><path d="M3 17c0-3.3 3.1-6 7-6s7 2.7 7 6"/><path d="M15 3l1.5 1.5L18 3" stroke-linecap="round"/></svg>
                            Roles
                        </button>
                        <button class="rm-tab" data-tab="matrix">
                            <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="6" height="6" rx="1"/><rect x="11" y="3" width="6" height="6" rx="1"/><rect x="3" y="11" width="6" height="6" rx="1"/><rect x="11" y="11" width="6" height="6" rx="1"/></svg>
                            Permission Matrix
                        </button>
                        <button class="rm-tab" data-tab="assign">
                            <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="8" cy="7" r="3"/><path d="M2 17c0-3 2.7-5 6-5s6 2 6 5"/><path d="M14 10l2 2 3-3"/></svg>
                            User Assignment
                        </button>
                        <button class="rm-tab" data-tab="viewperms">
                            <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8"><ellipse cx="10" cy="10" rx="8" ry="5"/><circle cx="10" cy="10" r="2.5"/></svg>
                            View Permissions
                        </button>
                    </div>

                    <!-- ══ TAB 1: ROLES ══ -->
                    <div class="rm-panel active" id="tab-roles">
                        <div class="rm-roles-bar">
                            <p class="rm-roles-desc">Roles define what a group of staff can do. Each role has a permission set. Staff are assigned a role when their system login is activated.</p>
                            <button class="btn-gold" data-bs-toggle="modal" data-bs-target="#kt_modal_add_role">+ Add new role</button>
                        </div>

                        <div class="rm-grid">
                            <?php
                            if ($users_role): foreach ($users_role as $sv):
                                    $dept = $sv['department_name'] ?? '';
                                    $style = getRoleStyle($sv['role_name'], $dept, $dept_icons);
                                    $count = getRoleCount($sv['slug']);
                                    $is_head = !empty($sv['is_dept_head']);
                                    ?>
                                    <div class="rm-card editRoleBtn"
                                         data-bs-toggle="modal" data-bs-target="#kt_modal_update_role"
                                         data-role-id="<?= $sv['id'] ?>"
                                         data-role-name="<?= htmlspecialchars($sv['role_name']) ?>"
                                         data-role-modules='<?= json_encode(getAssignedPermissions($sv['id'])) ?>'>
                                        <button class="rm-card-edit-btn" title="Edit">✏️</button>
                                        <div class="rm-card-ico" style="background:<?= $style['color'] ?>"><?= $style['icon'] ?></div>
                                        <div class="rm-card-role"><?= htmlspecialchars($sv['role_name']) ?></div>
                                        <div class="rm-card-dept"><?= htmlspecialchars($dept ?: 'General') ?></div>
                                        <div class="rm-card-footer">
                                            <span class="badge-users"><?= $count ?> <?= $count == 1 ? 'user' : 'users' ?></span>
                                    <?php if ($is_head): ?><span class="badge-head">Dept head eligible</span><?php endif; ?>
                                        </div>
                                    </div>
    <?php endforeach;
endif; ?>

                            <div class="rm-card-add" data-bs-toggle="modal" data-bs-target="#kt_modal_add_role">
                                <div class="rm-add-plus">+</div>
                                <div class="rm-add-lbl">Add new role</div>
                            </div>
                        </div>

                        <div class="rm-info">
                            <div class="rm-info-title">How roles work in BOSS</div>
                            <p class="rm-info-text">Each role has a default permission set defined in the Permission Matrix tab. When Admin assigns a role to a staff member, those permissions auto-apply. Admin can then override individual permissions per staff if needed — overrides are shown with a warning badge so they are always visible.</p>
                        </div>
                    </div>

                    <!-- ══ TAB 2: PERMISSION MATRIX ══ -->
                    <div class="rm-panel" id="tab-matrix">

                        <!-- Role selector pills -->
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:16px;flex-wrap:wrap">
                            <span style="font-size:12px;color:#aaa;font-weight:600;text-transform:uppercase;letter-spacing:.05em">Role:</span>
                                        <?php if ($users_role): foreach ($users_role as $i => $r): ?>
                                    <button class="ua-filter-btn pm-role-pill <?= $i === 0 ? 'active' : '' ?>"
                                            data-role-id="<?= $r['id'] ?>"
                                            data-role-name="<?= htmlspecialchars($r['role_name']) ?>">
        <?= htmlspecialchars($r['role_name']) ?>
                                    </button>
    <?php endforeach;
endif; ?>
                        </div>

                        <div class="pm-wrap">
                            <div class="pm-header">
                                <div class="pm-title">
                                    <svg width="16" height="16" viewBox="0 0 20 20" fill="none" stroke="#B8962E" stroke-width="2"><rect x="3" y="3" width="6" height="6" rx="1"/><rect x="11" y="3" width="6" height="6" rx="1"/><rect x="3" y="11" width="6" height="6" rx="1"/><rect x="11" y="11" width="6" height="6" rx="1"/></svg>
                                    Permission Matrix — <span class="pm-title-role" id="pmRoleName"><?= htmlspecialchars($users_role[0]['role_name'] ?? '') ?></span>
                                </div>
                                <div class="pm-actions">
                                    <button class="btn-outline" id="pmEnableAll">Enable all</button>
                                    <button class="btn-outline" id="pmDisableAll">Disable all</button>
                                    <button class="btn-gold btn-gold-sm" id="saveMatrixBtn">
                                        <svg width="13" height="13" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 10l4 4 7-7"/></svg>
                                        Save
                                    </button>
                                </div>
                            </div>
                            <div style="overflow-x:auto">
                                <table class="pm-table">
                                    <thead>
                                        <tr>
                                            <th style="min-width:220px">Module</th>
                                            <th>View</th>
                                            <th>Add</th>
                                            <th>Edit</th>
                                            <th>Delete</th>
                                            <th>Approve</th>
                                            <th>Export</th>
                                        </tr>
                                    </thead>
                                    <tbody id="pmTbody">
                                        <?php
                                        // Get first role's permissions as default
                                        $firstRoleId = $users_role[0]['id'] ?? 0;
                                        $firstRolePerms = getAssignedPermissions($firstRoleId);
                                        $actions = ['view', 'add', 'edit', 'delete', 'approve', 'export'];

                                        foreach ($permission_category_list as $cat):
                                            $cat_perms = array_filter($allPermissions, fn($p) => $p['category_id'] == $cat['id']);
                                            ?>
                                            <tr class="pm-cat-row"><td colspan="7"><?= htmlspecialchars($cat['category_name']) ?></td></tr>
                                                <?php foreach ($cat_perms as $perm): ?>
                                                <tr>
                                                    <td class="pm-mod-name"><?= htmlspecialchars($perm['module_name']) ?></td>
                                                    <?php
                                                    foreach ($actions as $action):
                                                        $permKey = $perm['id'] . '_' . $action;
                                                        // Check if permission exists in a way compatible with your data
                                                        $has = in_array($perm['id'], $firstRolePerms);
                                                        ?>
                                                        <td><input type="checkbox" class="pm-check" data-perm="<?= $perm['id'] ?>" data-action="<?= $action ?>" <?= ($has && in_array($action, ['view', 'add', 'edit'])) ? 'checked' : '' ?>></td>
        <?php endforeach; ?>
                                                </tr>
    <?php endforeach;
endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- ══ TAB 3: USER ASSIGNMENT ══ -->
                    <div class="rm-panel" id="tab-assign">
                        <div class="ua-top">
                            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                                <span style="font-size:13px;color:#555">Assign roles to registered staff. Activate logins. Override individual permissions per staff if needed.</span>
                            </div>
                            <button class="btn-gold" data-bs-toggle="modal" data-bs-target="#kt_modal_assign_user">
                                <svg width="13" height="13" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="8" cy="7" r="3"/><path d="M2 17c0-3 2.7-5 6-5"/><path d="M14 10l2 2 3-3"/></svg>
                                Assign user
                            </button>
                        </div>

                        <!-- Filters -->
                        <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:16px;flex-wrap:wrap">
                            <div class="ua-filters">
                                <button class="ua-filter-btn active" data-filter="all">All users</button>
                                <button class="ua-filter-btn" data-filter="active">Active</button>
                                <button class="ua-filter-btn" data-filter="inactive">Inactive</button>
                                <button class="ua-filter-btn" data-filter="overrides">Has overrides</button>
                                <button class="ua-filter-btn" data-filter="norole">No role assigned</button>
                            </div>
                            <input type="text" class="ua-search" placeholder="Search staff name or ID…" id="uaSearch">
                        </div>

                        <div class="ua-section">
                            <div class="ua-section-header">
                                <div class="ua-section-title">
                                    <svg width="16" height="16" viewBox="0 0 20 20" fill="none" stroke="#888" stroke-width="1.8"><circle cx="10" cy="7" r="3.5"/><path d="M3 17c0-3.3 3.1-6 7-6s7 2.7 7 6"/></svg>
                                    All system users <span class="ua-count" id="uaCount"></span>
                                </div>
                            </div>
                            <div id="uaUserList">
                                <?php
                                // Placeholder — replace with actual DB call
                                $system_users = getAllStaffList(); // implement this function
                                if ($system_users): foreach ($system_users as $u):
                                        $initials = strtoupper(substr($u['name'], 0, 1));
                                        $is_active = $u['status'] ?? false;
                                        $has_overrides = !empty($u['has_overrides']);
                                        $no_role = empty($u['role_name']);
                                        ?>
                                        <div class="ua-user-row" data-status="<?= $is_active ? 'active' : 'inactive' ?>"
                                             data-overrides="<?= $has_overrides ? '1' : '0' ?>"
                                             data-norole="<?= $no_role ? '1' : '0' ?>"
                                             data-name="<?= htmlspecialchars(strtolower($u['name'])) ?>">
                                            <div class="ua-avatar"><?= $initials ?></div>
                                            <div class="ua-user-info">
                                                <div class="ua-user-name">
        <?= htmlspecialchars($u['name']) ?>
                                                    <span class="ua-staff-id"><?= htmlspecialchars($u['id']) ?></span>
        <?php if ($has_overrides): ?><span class="ua-override-badge">Has overrides</span><?php endif; ?>
                                                </div>
                                                <div class="ua-user-meta">
        <?= htmlspecialchars($no_role ? '— Not assigned' : $u['role_name']) ?>
                                                    &nbsp;·&nbsp; <?= htmlspecialchars($u['department_name'] ?? '') ?>
                                                    &nbsp;·&nbsp; Last login: <?= htmlspecialchars($u['last_login'] ?? 'Never') ?>
                                                </div>
                                            </div>
                                            <div class="ua-status">
                                                <span class="ua-dot <?= $is_active ? 'green' : 'red' ?>"></span>
                                                <span class="ua-status-label <?= $is_active ? 'active' : 'inactive' ?>"><?= $is_active ? 'Active' : 'Inactive' ?></span>
                                            </div>
                                            <div class="ua-actions">
                                                <button class="btn-sm-outline" onclick="openPermissionsModal(<?= $u['id'] ?>)">
                                                    <svg width="13" height="13" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="6" height="6" rx="1"/><rect x="11" y="3" width="6" height="6" rx="1"/><rect x="3" y="11" width="6" height="6" rx="1"/><rect x="11" y="11" width="6" height="6" rx="1"/></svg>
                                                    Permissions
                                                </button>
        <?php if ($is_active): ?>
                                                    <button class="btn-sm-outline" onclick="suspendUser(<?= $u['id'] ?>)">
                                                        <svg width="13" height="13" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="7" y="4" width="2.5" height="12" rx="1"/><rect x="11" y="4" width="2.5" height="12" rx="1"/></svg>
                                                        Suspend
                                                    </button>
        <?php else: ?>
                                                    <button class="btn-sm-activate" onclick="activateUser(<?= $u['id'] ?>)">
                                                        <svg width="13" height="13" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><circle cx="8" cy="7" r="3"/><path d="M2 17c0-3 2.7-5 6-5"/><path d="M14 10l2 2 3-3"/></svg>
                                                        Activate
                                                    </button>
                                        <?php endif; ?>
                                                <button class="btn-sm-outline" onclick="editUserRole(<?= $u['id'] ?>)">
                                                    <svg width="13" height="13" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 14.5V17h2.5l7.3-7.3-2.5-2.5L4 14.5z"/><path d="M14.7 6.3a1 1 0 000-1.4l-1.6-1.6a1 1 0 00-1.4 0l-1.1 1.1 3 3 1.1-1.1z"/></svg>
                                                    Edit role
                                                </button>
                                            </div>
                                        </div>
    <?php endforeach;
endif; ?>
                            </div>
                        </div>

                        <div class="ua-login-info">
                            <div class="ua-login-title">Login state after activation</div>
                            <p class="ua-login-text">When Admin activates a user — login is <strong>Active but limited</strong>. Staff can only access modules permitted by their role. All other modules show "Access denied". Staff receives SMS with Staff ID + temporary password on activation.</p>
                        </div>
                    </div>

                    <!-- ══ TAB 4: VIEW PERMISSIONS ══ -->
                    <div class="rm-panel" id="tab-viewperms">
                        <div class="vp-filter-card">
                            <div class="vp-filter-title">
                                <svg width="14" height="14" viewBox="0 0 20 20" fill="none" stroke="#888" stroke-width="1.8"><path d="M3 6h14M6 10h8M9 14h2"/></svg>
                                Drill down to view permissions — select up to three levels
                            </div>
                            <div class="vp-selects">
                                <div class="vp-select-group">
                                    <label class="vp-select-label">
                                        <span class="vp-select-num">1</span> Department
                                    </label>
                                    <select class="vp-select" id="vpDeptSelect" onchange="vpOnDeptChange()">
                                        <option value="">— Select department</option>
<?php
$deptRes = mysqli_query($con, "SELECT id,department_name FROM departments ORDER BY department_name");
while ($d = mysqli_fetch_assoc($deptRes)):
    ?>
                                            <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['department_name']) ?></option>
<?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="vp-select-group">
                                    <label class="vp-select-label">
                                        <span class="vp-select-num dim" id="vpNum2">2</span> Role
                                    </label>
                                    <select class="vp-select" id="vpRoleSelect" disabled onchange="vpOnRoleChange()">
                                        <option value="">— Select department first</option>
                                    </select>
                                </div>
                                <div class="vp-select-group">
                                    <label class="vp-select-label">
                                        <span class="vp-select-num dim" id="vpNum3">3</span> Staff Member
                                    </label>
                                    <select class="vp-select" id="vpStaffSelect" disabled onchange="vpLoad()">
                                        <option value="">— Select role first</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div id="vpResult"></div>
                    </div>

                </div><!-- /rm -->
            </div>
        </div>
<?php include("includes/footer.php"); ?>
    </div>
</div>

<!-- ══ MODAL: Add Role ══ -->
<div class="modal fade" id="kt_modal_add_role" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-750px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Add a Role</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-roles-modal-action="close">
                    <i class="ki-outline ki-cross fs-1"></i>
                </div>
            </div>
            <div class="modal-body rm-modal-body">
                <form id="kt_modal_add_role_form" action="<?= $site_path ?>/ajax/add-update-role">
                    <div class="row g-4">
                        <div class="col-md-6 fv-row">
                            <label class="rm-form-label">Role Name <span style="color:#DC2626">*</span></label>
                            <input type="text" name="role_name" class="rm-role-input" placeholder="e.g. Store Manager, Dispatch Executive">
                        </div>
                        <div class="col-md-6">
                            <label class="rm-form-label">Primary Department</label>
                            <select name="department_id" class="rm-role-input" style="padding:9px 12px">
                                <option value="">Any department</option>
<?php
$deptSql = mysqli_query($con, "SELECT id,department_name FROM departments ORDER BY department_name");
while ($d = mysqli_fetch_assoc($deptSql)):
    ?>
                                    <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['department_name']) ?></option>
<?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="rm-form-label">Description</label>
                            <textarea name="description" rows="3" class="rm-role-input" style="resize:vertical" placeholder="Brief description of this role's responsibilities"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="rm-form-label">Can be department head?</label>
                            <div style="display:flex;gap:20px;padding-top:4px">
                                <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer">
                                    <input type="radio" name="is_department_head" value="1"> Yes
                                </label>
                                <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer">
                                    <input type="radio" name="is_department_head" value="0" checked> No
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="rm-form-label">Copy permissions from existing role</label>
                            <select name="copy_role_id" class="rm-role-input" style="padding:9px 12px">
                                <option value="">— Start with blank permissions</option>
<?php
$roleSql = mysqli_query($con, "SELECT id,role_name FROM role ORDER BY role_name");
while ($r = mysqli_fetch_assoc($roleSql)):
    ?>
                                    <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['role_name']) ?></option>
<?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <div style="background:#F0F7FF;border:1px solid #BAD5F5;border-left:3px solid #3B82F6;border-radius:8px;padding:12px 16px;font-size:12px;color:#1E40AF">
                                After adding, go to the Permission Matrix tab to set what this role can access.
                            </div>
                        </div>
                    </div>
                    <div style="border-top:1px solid #F0EDE5;margin-top:20px;padding-top:16px;display:flex;justify-content:flex-end;gap:10px">
                        <button type="button" class="btn-outline" data-bs-dismiss="modal" data-kt-roles-modal-action="cancel">Cancel</button>
                        <button type="submit" class="btn-gold" data-kt-roles-modal-action="submit">Add Role</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ══ MODAL: Update Role ══ -->
<div class="modal fade" id="kt_modal_update_role" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-750px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Update Role</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-roles-modal-action="close"><i class="ki-outline ki-cross fs-1"></i></div>
            </div>
            <div class="modal-body rm-modal-body">
                <form id="kt_modal_update_role_form" action="<?= $site_path ?>/ajax/add-update-role">
                    <input type="hidden" name="role_id" id="update_role_id">
                    <div class="col-md-6 fv-row">
                        <label class="rm-form-label">Role Name <span style="color:#DC2626">*</span></label>
                        <input type="text" name="role_name" id="update_role_name" class="rm-role-input" placeholder="e.g. Store Manager, Dispatch Executive">
                    </div>
                    <label class="rm-form-label">Role Permissions</label>
                    <div style="max-height:380px;overflow-y:auto;border:1px solid #F0EDE5;border-radius:8px">
                        <table class="rm-perm-table">
                            <tbody>
                                <?php foreach ($permission_category_list as $cat):
                                    $cat_perms = array_filter($allPermissions, fn($p) => $p['category_id'] == $cat['id']);
                                    ?>
                                    <tr class="cat-row">
                                        <td><?= htmlspecialchars($cat['category_name']) ?></td>
                                        <td style="text-align:right">
                                            <label style="font-size:11px;color:#aaa;cursor:pointer;display:flex;align-items:center;gap:4px;justify-content:flex-end">
                                                <input type="checkbox" class="rm-check category-checkbox" data-category="<?= $cat['id'] ?>"> Select all
                                            </label>
                                        </td>
                                    </tr>
    <?php foreach ($cat_perms as $perm): ?>
                                        <tr>
                                            <td class="perm-name"><?= htmlspecialchars($perm['module_name']) ?></td>
                                            <td style="text-align:right">
                                                <input class="rm-check permission-checkbox" type="checkbox"
                                                       name="permissions[<?= $perm['id'] ?>]"
                                                       data-category="<?= $cat['id'] ?>"
                                                       data-permission-id="<?= $perm['id'] ?>" value="1">
                                            </td>
                                        </tr>
    <?php endforeach;
endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;margin-top:20px;padding-top:16px;border-top:1px solid #F0EDE5">
                        <button type="button" class="btn btn-danger btn-sm" id="deleteRoleBtn">Delete Role</button>
                        <div style="display:flex;gap:10px">
                            <button type="button" class="btn-outline" data-kt-roles-modal-action="cancel">Discard</button>
                            <button type="submit" class="btn-gold" data-kt-roles-modal-action="submit">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ══ MODAL: Assign User ══ -->
<div class="modal fade" id="kt_modal_assign_user" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Assign User to Role</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body rm-modal-body">
                <div class="rm-assign-user-modal">
                    <div class="form-group">
                        <label class="rm-form-label">Staff Member <span style="color:#DC2626">*</span></label>
                        <select class="rm-role-input" style="padding:9px 12px" id="assignStaffSelect">
                            <option value="">Select a staff member…</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="rm-form-label">Role <span style="color:#DC2626">*</span></label>
                        <select class="rm-role-input" style="padding:9px 12px" id="assignRoleSelect">
                            <option value="">Select a role…</option>
<?php if ($users_role): foreach ($users_role as $r): ?>
                                    <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['role_name']) ?></option>
    <?php endforeach;
endif; ?>
                        </select>
                    </div>
                </div>
                <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:8px;padding-top:16px;border-top:1px solid #F0EDE5">
                    <button type="button" class="btn-outline" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn-gold" onclick="doAssignUser()">Assign Role</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= $site_path ?>/assets/plugins/global/plugins.bundle.js"></script>
<script src="<?= $site_path ?>/assets/js/scripts.bundle.js"></script>
<script src="<?= $site_path ?>/assets/js/custom/apps/user-management/roles/list/add.js?v=<?= time() ?>"></script>
<script src="<?= $site_path ?>/assets/js/custom/apps/user-management/roles/list/update-role.js?v=<?= time() ?>"></script>

<script>
              /* ── TAB SWITCHING ── */
              document.querySelectorAll('.rm-tab').forEach(tab => {
                  tab.addEventListener('click', function () {
                      document.querySelectorAll('.rm-tab').forEach(t => t.classList.remove('active'));
                      document.querySelectorAll('.rm-panel').forEach(p => p.classList.remove('active'));
                      this.classList.add('active');
                      document.getElementById('tab-' + this.dataset.tab).classList.add('active');
                  });
              });

              /* ── EDIT ROLE MODAL ── */
              document.addEventListener('DOMContentLoaded', () => {
                  document.querySelectorAll('.editRoleBtn').forEach(card => {
                      card.addEventListener('click', function () {
                          const roleId = this.dataset.roleId;
                          const roleName = this.dataset.roleName;
                          const assigned = JSON.parse(this.dataset.roleModules || '[]');
                          document.getElementById('update_role_id').value = roleId;
                          document.getElementById('update_role_name').value = roleName;
                          document.getElementById('deleteRoleBtn').dataset.roleId = roleId;
                          document.querySelectorAll('#kt_modal_update_role_form .permission-checkbox')
                                  .forEach(ch => ch.checked = false);
                          if (Array.isArray(assigned)) {
                              assigned.forEach(permId => {
                                  const ch = document.querySelector(
                                          '#kt_modal_update_role_form .permission-checkbox[data-permission-id="' + permId + '"]'
                                          );
                                  if (ch)
                                      ch.checked = true;
                              });
                          }
                          syncCategoryCheckboxes('#kt_modal_update_role_form');
                      });
                  });

                  // Load staff list for assign user modal
                  loadStaffForAssignModal();
                  updateUaCount();
              });

              /* ── CATEGORY SELECT ALL ── */
              document.querySelectorAll('.category-checkbox').forEach(cat => {
                  cat.addEventListener('change', function () {
                      const catId = this.getAttribute('data-category');
                      this.closest('form').querySelectorAll('.permission-checkbox[data-category="' + catId + '"]')
                              .forEach(ch => ch.checked = this.checked);
                  });
              });

              function syncCategoryCheckboxes(formSel) {
                  const form = document.querySelector(formSel);
                  if (!form)
                      return;
                  form.querySelectorAll('.category-checkbox').forEach(cat => {
                      const catId = cat.getAttribute('data-category');
                      const children = form.querySelectorAll('.permission-checkbox[data-category="' + catId + '"]');
                      cat.checked = children.length > 0 && Array.from(children).every(c => c.checked);
                  });
              }

              /* ── PERMISSION MATRIX ── */
// Role pill switch
              document.querySelectorAll('.pm-role-pill').forEach(pill => {
                  pill.addEventListener('click', function () {
                      document.querySelectorAll('.pm-role-pill').forEach(p => p.classList.remove('active'));
                      this.classList.add('active');
                      document.getElementById('pmRoleName').textContent = this.dataset.roleName;
                      loadMatrixForRole(this.dataset.roleId);
                  });
              });

              function loadMatrixForRole(roleId) {
                  alert(roleId);
                  $.ajax({
                      url: '<?= $site_path ?>/ajax/get-role-permissions',
                      data: {role_id: roleId},
                      success: function (res) {
                          // Update checkboxes based on response
                          document.querySelectorAll('.pm-check').forEach(ch => {
                              const permId = ch.dataset.perm;
                              const action = ch.dataset.action;
                              ch.checked = res && res.permissions && res.permissions[permId] && res.permissions[permId][action];
                          });
                      }
                  });
              }

              document.getElementById('pmEnableAll').addEventListener('click', () => {
                  document.querySelectorAll('.pm-check').forEach(ch => ch.checked = true);
              });
              document.getElementById('pmDisableAll').addEventListener('click', () => {
                  document.querySelectorAll('.pm-check').forEach(ch => ch.checked = false);
              });
              document.getElementById('saveMatrixBtn').addEventListener('click', function () {
                  const activeRole = document.querySelector('.pm-role-pill.active');
                  if (!activeRole)
                      return;
                  const payload = [];
                  document.querySelectorAll('.pm-check').forEach(ch => {
                      payload.push({perm: ch.dataset.perm, action: ch.dataset.action, val: ch.checked ? 1 : 0});
                  });
                  $.ajax({
                      url: '<?= $site_path ?>/ajax/save-permission-matrix',
                      type: 'POST',
                      data: {role_id: activeRole.dataset.roleId, matrix: JSON.stringify(payload)},
                      success: () => {
                          showToast('Matrix saved successfully!');
                      },
                      error: () => {
                          showToast('Error saving matrix.', true);
                      }
                  });
              });

              /* ── USER ASSIGNMENT FILTERS ── */
              document.querySelectorAll('.ua-filter-btn[data-filter]').forEach(btn => {
                  btn.addEventListener('click', function () {
                      document.querySelectorAll('.ua-filter-btn[data-filter]').forEach(b => b.classList.remove('active'));
                      this.classList.add('active');
                      filterUsers();
                  });
              });
              document.getElementById('uaSearch').addEventListener('input', filterUsers);

              function filterUsers() {
                  const filter = document.querySelector('.ua-filter-btn[data-filter].active')?.dataset.filter || 'all';
                  const search = document.getElementById('uaSearch').value.toLowerCase();
                  let visible = 0;
                  document.querySelectorAll('.ua-user-row').forEach(row => {
                      let show = true;
                      if (filter === 'active')
                          show = row.dataset.status === 'active';
                      if (filter === 'inactive')
                          show = row.dataset.status === 'inactive';
                      if (filter === 'overrides')
                          show = row.dataset.overrides === '1';
                      if (filter === 'norole')
                          show = row.dataset.norole === '1';
                      if (search && !row.dataset.name.includes(search))
                          show = false;
                      row.style.display = show ? '' : 'none';
                      if (show)
                          visible++;
                  });
                  updateUaCount(visible);
              }

              function updateUaCount(n) {
                  const rows = n ?? document.querySelectorAll('.ua-user-row').length;
                  const el = document.getElementById('uaCount');
                  if (el)
                      el.textContent = rows + ' of ' + document.querySelectorAll('.ua-user-row').length + ' users';
              }

              /* ── USER ACTIONS ── */
              function activateUser(userId) {
                  if (!confirm('Activate this user? They will receive an SMS with login credentials.'))
                      return;
                  $.ajax({
                      url: '<?= $site_path ?>/ajax/activate-user',
                      type: 'POST', data: {user_id: userId},
                      success: () => location.reload(),
                      error: () => alert('Error activating user.')
                  });
              }
              function suspendUser(userId) {
                  if (!confirm('Suspend this user? They will lose access immediately.'))
                      return;
                  $.ajax({
                      url: '<?= $site_path ?>/ajax/suspend-user',
                      type: 'POST', data: {user_id: userId},
                      success: () => location.reload()
                  });
              }
              function openPermissionsModal(userId) { /* open override permissions modal */
              }
              function editUserRole(userId) { /* open edit role modal */
              }

              function loadStaffForAssignModal() {
                  $.ajax({
                      url: '<?= $site_path ?>/ajax/get-staff-list',
                      success: function (res) {
                          if (!res || !res.staff)
                              return;
                          const sel = document.getElementById('assignStaffSelect');
                          res.staff.forEach(s => {
                              const opt = document.createElement('option');
                              opt.value = s.id;
                              opt.textContent = s.name;
                              sel.appendChild(opt);
                          });
                      }
                  });
              }
              function doAssignUser() {
                  const staffId = document.getElementById('assignStaffSelect').value;
                  const roleId = document.getElementById('assignRoleSelect').value;
                  if (!staffId || !roleId) {
                      alert('Please select both staff and role.');
                      return;
                  }
                  $.ajax({
                      url: '<?= $site_path ?>/ajax/assign-user-role',
                      type: 'POST', data: {staff_id: staffId, role_id: roleId},
                      success: () => {
                          $('#kt_modal_assign_user').modal('hide');
                          location.reload();
                      },
                      error: () => alert('Error assigning role.')
                  });
              }

              /* ── VIEW PERMISSIONS ── */
              function vpOnDeptChange() {
                  const deptId = document.getElementById('vpDeptSelect').value;
                  const roleEl = document.getElementById('vpRoleSelect');
                  const staffEl = document.getElementById('vpStaffSelect');
                  roleEl.innerHTML = '<option value="">— All roles in this department</option>';
                  staffEl.innerHTML = '<option value="">— Select role first</option>';
                  roleEl.disabled = !deptId;
                  staffEl.disabled = true;
                  document.getElementById('vpNum2').className = deptId ? 'vp-select-num' : 'vp-select-num dim';
                  document.getElementById('vpResult').innerHTML = '';
                  if (!deptId)
                      return;

                  $.ajax({
                      url: '<?= $site_path ?>/ajax/get-roles-by-dept',
                      data: {dept_id: deptId},
                      success: function (res) {
                          if (res && res.roles) {
                              res.roles.forEach(r => {
                                  const opt = document.createElement('option');
                                  opt.value = r.id;
                                  opt.textContent = r.role_name;
                                  roleEl.appendChild(opt);
                              });
                          }
                          vpLoad();
                      }
                  });
              }

              function vpOnRoleChange() {
                  const roleId = document.getElementById('vpRoleSelect').value;
                  const staffEl = document.getElementById('vpStaffSelect');
                  staffEl.innerHTML = '<option value="">— All staff in this role</option>';
                  staffEl.disabled = !roleId;
                  document.getElementById('vpNum3').className = roleId ? 'vp-select-num' : 'vp-select-num dim';
                  if (roleId) {
                      $.ajax({
                          url: '<?= $site_path ?>/ajax/get-staff-by-role',
                          data: {role_id: roleId},
                          success: function (res) {
                              if (res && res.staff) {
                                  res.staff.forEach(s => {
                                      const opt = document.createElement('option');
                                      opt.value = s.id;
                                      opt.textContent = s.name;
                                      staffEl.appendChild(opt);
                                  });
                              }
                          }
                      });
                  }
                  vpLoad();
              }

              function vpLoad() {
                  const deptId = document.getElementById('vpDeptSelect').value;
                  const roleId = document.getElementById('vpRoleSelect').value;
                  const staffId = document.getElementById('vpStaffSelect').value;
                  if (!deptId) {
                      document.getElementById('vpResult').innerHTML = '';
                      return;
                  }

                  $.ajax({
                      url: '<?= $site_path ?>/ajax/get-view-permissions',
                      data: {dept_id: deptId, role_id: roleId, staff_id: staffId},
                      success: function (res) {
                          if (!res)
                              return;
                          const actions = ['view', 'add', 'edit', 'delete', 'approve', 'export'];
                          let note = 'Showing combined highest permissions across all roles in this department';
                          if (staffId)
                              note = 'Showing individual permissions for this staff member';
                          else if (roleId)
                              note = 'Showing permissions for this role';

                          let html = `<div class="vp-result-card">
              <div class="vp-result-header">
                <div class="vp-result-title">
                  <svg width="15" height="15" viewBox="0 0 20 20" fill="none" stroke="#B8962E" stroke-width="1.8"><ellipse cx="10" cy="10" rx="8" ry="5"/><circle cx="10" cy="10" r="2.5"/></svg>
                  ${res.scope_label || 'Department overview'}
                </div>
                <div style="display:flex;align-items:center;gap:12px">`;
                          if (res.role_count)
                              html += `<span class="vp-badge">${res.role_count} roles</span>`;
                          if (res.staff_count)
                              html += `<span class="vp-badge">${res.staff_count} staff</span>`;
                          html += `<span style="font-size:12px;color:#aaa">${note}</span>
                  <button class="btn-outline btn-gold-sm" onclick="exportVPTable()">↓ Export</button>
                </div>
              </div>
              <div style="overflow-x:auto">
                <table class="vp-table" id="vpTable">
                  <thead><tr>
                    <th style="min-width:200px">Module</th>
                    ${actions.map(a => `<th>${a.charAt(0).toUpperCase() + a.slice(1)}</th>`).join('')}
                  </tr></thead>
                  <tbody>`;

                          if (res.categories) {
                              res.categories.forEach(cat => {
                                  html += `<tr class="vp-cat-row"><td colspan="7">${cat.name}</td></tr>`;
                                  cat.modules.forEach(m => {
                                      html += `<tr><td>${m.name}</td>`;
                                      actions.forEach(a => {
                                          html += `<td>${m[a] ? '<span class="vp-tick">✓</span>' : '<span class="vp-cross">✕</span>'}</td>`;
                                      });
                                      html += `</tr>`;
                                  });
                              });
                          }
                          html += `</tbody></table></div></div>`;
                          document.getElementById('vpResult').innerHTML = html;
                      }
                  });
              }

              function exportVPTable() {
                  // Trigger CSV export via AJAX
                  const deptId = document.getElementById('vpDeptSelect').value;
                  const roleId = document.getElementById('vpRoleSelect').value;
                  const staffId = document.getElementById('vpStaffSelect').value;
                  window.location.href = '<?= $site_path ?>/ajax/export-permissions?dept_id=' + deptId + '&role_id=' + roleId + '&staff_id=' + staffId;
              }

              /* ── DELETE ROLE ── */
              document.getElementById('deleteRoleBtn').addEventListener('click', function () {
                  const roleId = this.dataset.roleId;
                  if (!confirm('Delete this role? This cannot be undone.'))
                      return;
                  $.ajax({
                      url: '<?= $site_path ?>/ajax/add-update-role',
                      type: 'POST', data: {action: 'delete', role_id: roleId},
                      success: () => {
                          $('#kt_modal_update_role').modal('hide');
                          location.reload();
                      }
                  });
              });

              /* ── TOAST ── */
              function showToast(msg, isError) {
                  const t = document.createElement('div');
                  t.textContent = msg;
                  t.style.cssText = `position:fixed;bottom:24px;right:24px;padding:12px 20px;border-radius:8px;font-size:13px;font-weight:500;color:#fff;background:${isError ? '#EF4444' : '#22C55E'};z-index:9999;box-shadow:0 4px 12px rgba(0,0,0,.15);transition:opacity .3s`;
                  document.body.appendChild(t);
                  setTimeout(() => {
                      t.style.opacity = 0;
                      setTimeout(() => t.remove(), 300);
                  }, 2500);
              }
</script>