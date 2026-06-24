
<?php
include("config/database.php");
include("config/auth_check.php");
include("includes/sidemenu.php");
$users_role = getUniqueRoles();
$allModules = getAllModules();
$assignedModules = getAssignedPermissions($typee_id);
$permission_category_list = getAllPermissionCategory();
$allPermissions = getAllModules();

// Icon map by department/role keyword
$dept_icons = [
    'design'      => ['icon'=>'✏️',  'color'=>'#E8E4FF', 'accent'=>'#7C6FF7'],
    'qc'          => ['icon'=>'📋',  'color'=>'#FFF3E0', 'accent'=>'#F59E0B'],
    'quality'     => ['icon'=>'📋',  'color'=>'#FFF3E0', 'accent'=>'#F59E0B'],
    'production'  => ['icon'=>'⚙️',  'color'=>'#E8F5E9', 'accent'=>'#22C55E'],
    'cutting'     => ['icon'=>'✂️',  'color'=>'#E8F5E9', 'accent'=>'#22C55E'],
    'purchase'    => ['icon'=>'🛒',  'color'=>'#FFF8E1', 'accent'=>'#D97706'],
    'dispatch'    => ['icon'=>'🚚',  'color'=>'#FDE8E8', 'accent'=>'#EF4444'],
    'hr'          => ['icon'=>'📝',  'color'=>'#E0F2FE', 'accent'=>'#3B82F6'],
    'admin'       => ['icon'=>'🔐',  'color'=>'#E0F2FE', 'accent'=>'#3B82F6'],
    'finance'     => ['icon'=>'💰',  'color'=>'#ECFDF5', 'accent'=>'#10B981'],
    'account'     => ['icon'=>'💰',  'color'=>'#ECFDF5', 'accent'=>'#10B981'],
    'sampling'    => ['icon'=>'🧵',  'color'=>'#F3E8FF', 'accent'=>'#A855F7'],
    'sampler'     => ['icon'=>'🧵',  'color'=>'#F3E8FF', 'accent'=>'#A855F7'],
    'it'          => ['icon'=>'💻',  'color'=>'#E0F2FE', 'accent'=>'#0EA5E9'],
    'default'     => ['icon'=>'👤',  'color'=>'#F1F5F9', 'accent'=>'#64748B'],
];

function getRoleStyle($role_name, $dept_name, $icons) {
    $search = strtolower($role_name . ' ' . $dept_name);
    foreach ($icons as $key => $val) {
        if ($key !== 'default' && strpos($search, $key) !== false) return $val;
    }
    return $icons['default'];
}
?>

<style>
/* ══ Reset & Base ══ */
.rm-wrap *, .rm-wrap *::before, .rm-wrap *::after { box-sizing: border-box; }
.rm-wrap {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    color: #1A1A1A;
    max-width: 1200px;
    padding: 0 4px;
}

/* ══ Page Header ══ */
.rm-page-header { margin-bottom: 24px; }
.rm-page-title { font-size: 22px; font-weight: 700; color: #1A1A1A; margin: 0 0 4px; }
.rm-page-sub { font-size: 12px; color: #999; margin: 0; }
.rm-page-sub span { margin: 0 4px; }

/* ══ Tab Bar ══ */
.rm-tabs {
    display: flex;
    gap: 0;
    border-bottom: 1px solid #E5E2D9;
    margin-bottom: 20px;
}
.rm-tab {
    display: flex;
    align-items: center;
    gap: 7px;
    padding: 10px 18px;
    font-size: 13px;
    font-weight: 500;
    color: #888;
    cursor: pointer;
    border-bottom: 2px solid transparent;
    margin-bottom: -1px;
    transition: all .15s;
    background: none;
    border-top: none;
    border-left: none;
    border-right: none;
    white-space: nowrap;
}
.rm-tab:hover { color: #555; }
.rm-tab.active { color: #B8962E; border-bottom-color: #B8962E; font-weight: 600; }
.rm-tab-icon { font-size: 14px; opacity: .8; }

/* ══ Tab Content ══ */
.rm-tab-panel { display: none; }
.rm-tab-panel.active { display: block; }

/* ══ Roles Tab ══ */
.rm-roles-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}
.rm-roles-desc {
    font-size: 13px;
    color: #555;
    line-height: 1.6;
    max-width: 740px;
}
.rm-add-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 9px 18px;
    background: #B8962E;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    white-space: nowrap;
    transition: background .15s;
    font-family: inherit;
    flex-shrink: 0;
}
.rm-add-btn:hover { background: #9E7E20; }

/* ══ Role Cards Grid ══ */
.rm-cards-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 14px;
}
@media (max-width: 1100px) { .rm-cards-grid { grid-template-columns: repeat(4, 1fr); } }
@media (max-width: 860px)  { .rm-cards-grid { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 600px)  { .rm-cards-grid { grid-template-columns: repeat(2, 1fr); } }

/* ══ Role Card ══ */
.rm-card {
    background: #fff;
    border: 1px solid #EBEBEB;
    border-radius: 12px;
    padding: 18px 16px 16px;
    cursor: pointer;
    transition: box-shadow .15s, transform .12s;
    position: relative;
}
.rm-card:hover {
    box-shadow: 0 4px 16px rgba(0,0,0,.08);
    transform: translateY(-1px);
}
.rm-card-icon {
    width: 46px; height: 46px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px;
    margin-bottom: 12px;
    flex-shrink: 0;
}
.rm-card-role { font-size: 14px; font-weight: 600; color: #1A1A1A; margin: 0 0 3px; }
.rm-card-dept { font-size: 12px; color: #999; margin: 0 0 10px; }
.rm-card-footer { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
.rm-user-badge {
    display: inline-flex; align-items: center; gap: 4px;
    background: #F4F4F4; border-radius: 20px;
    padding: 3px 10px; font-size: 11px; color: #555; font-weight: 500;
}
.rm-dept-head-badge {
    display: inline-flex; align-items: center; gap: 4px;
    background: #FDF6E3; border: 1px solid #E8D5A3; border-radius: 20px;
    padding: 3px 9px; font-size: 11px; color: #7A5C00; font-weight: 500;
}
.rm-card-edit {
    position: absolute; top: 12px; right: 12px;
    width: 28px; height: 28px;
    border-radius: 6px;
    background: transparent;
    border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; color: #ccc;
    opacity: 0; transition: opacity .15s, background .15s;
}
.rm-card:hover .rm-card-edit { opacity: 1; }
.rm-card-edit:hover { background: #F4F4F4; color: #666; }

/* ══ Add new role card ══ */
.rm-card-add {
    background: #fff;
    border: 1.5px dashed #D1CCBF;
    border-radius: 12px;
    padding: 18px 16px;
    cursor: pointer;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: border-color .15s, background .15s;
    min-height: 140px;
}
.rm-card-add:hover { border-color: #B8962E; background: #FDF9F0; }
.rm-add-plus {
    width: 36px; height: 36px;
    border-radius: 50%;
    background: #F4F4F0;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px; color: #aaa;
    transition: background .15s, color .15s;
}
.rm-card-add:hover .rm-add-plus { background: #FDF6E3; color: #B8962E; }
.rm-add-label { font-size: 13px; color: #aaa; font-weight: 500; }
.rm-card-add:hover .rm-add-label { color: #B8962E; }

/* ══ Info Box ══ */
.rm-info-box {
    margin-top: 28px;
    background: #FFFDF5;
    border: 1px solid #E8D9A3;
    border-left: 3px solid #B8962E;
    border-radius: 8px;
    padding: 14px 18px;
}
.rm-info-title { font-size: 12px; font-weight: 700; color: #B8962E; margin-bottom: 5px; }
.rm-info-text { font-size: 12px; color: #666; line-height: 1.6; margin: 0; }

/* ══ Permission Matrix Tab ══ */
.rm-matrix-wrap { overflow-x: auto; }
.rm-matrix-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12px;
    min-width: 600px;
}
.rm-matrix-table th {
    padding: 10px 12px;
    text-align: left;
    font-size: 11px;
    font-weight: 600;
    color: #888;
    text-transform: uppercase;
    letter-spacing: .05em;
    border-bottom: 1px solid #EBEBEB;
    background: #FAFAFA;
    white-space: nowrap;
}
.rm-matrix-table td {
    padding: 9px 12px;
    border-bottom: 1px solid #F3F3F3;
    vertical-align: middle;
}
.rm-matrix-table tr:hover td { background: #FAFAF8; }
.rm-cat-row td {
    background: #F7F5F0 !important;
    font-weight: 600;
    font-size: 12px;
    color: #555;
    padding: 7px 12px;
}
.rm-perm-name { color: #1A1A1A; padding-left: 24px !important; }
.rm-check {
    width: 16px; height: 16px;
    accent-color: #B8962E;
    cursor: pointer;
}
.rm-role-col { text-align: center; }

/* ══ User Assignment Tab ══ */
.rm-assign-desc {
    font-size: 13px; color: #666; margin-bottom: 20px; line-height: 1.6;
}
.rm-assign-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 14px;
}
@media (max-width: 700px) { .rm-assign-grid { grid-template-columns: 1fr; } }
.rm-assign-card {
    background: #fff;
    border: 1px solid #EBEBEB;
    border-radius: 10px;
    padding: 14px 16px;
}
.rm-assign-card-title { font-size: 13px; font-weight: 600; margin-bottom: 10px; color: #1A1A1A; }
.rm-assign-select {
    width: 100%;
    padding: 7px 10px;
    border: 1px solid #E0DDD4;
    border-radius: 7px;
    font-size: 12px;
    color: #333;
    font-family: inherit;
    outline: none;
    margin-bottom: 10px;
}
.rm-assign-select:focus { border-color: #B8962E; }
.rm-assign-btn {
    width: 100%;
    padding: 7px;
    background: #B8962E;
    color: #fff;
    border: none;
    border-radius: 7px;
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    font-family: inherit;
}
.rm-assign-btn:hover { background: #9E7E20; }

/* ══ View Permissions Tab ══ */
.rm-vperm-select-wrap {
    display: flex; gap: 12px; align-items: center; margin-bottom: 20px; flex-wrap: wrap;
}
.rm-vperm-select {
    padding: 8px 12px;
    border: 1px solid #E0DDD4;
    border-radius: 8px;
    font-size: 13px;
    font-family: inherit;
    outline: none;
    color: #333;
    min-width: 200px;
}
.rm-vperm-select:focus { border-color: #B8962E; }
.rm-vperm-view-btn {
    padding: 8px 16px;
    background: #B8962E;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    font-family: inherit;
}
.rm-perm-pill {
    display: inline-block;
    background: #F0FDF4;
    border: 1px solid #BBF7D0;
    color: #166534;
    border-radius: 20px;
    padding: 3px 10px;
    font-size: 11px;
    font-weight: 500;
    margin: 3px;
}
.rm-perm-section { margin-bottom: 16px; }
.rm-perm-section-title {
    font-size: 11px; font-weight: 700; color: #aaa;
    text-transform: uppercase; letter-spacing: .06em;
    margin-bottom: 8px; padding-bottom: 6px;
    border-bottom: 1px solid #F0EEE8;
}

/* ══ Modals (override Bootstrap minimally) ══ */
.rm-modal-body { padding: 20px 24px !important; }
.rm-perm-table { width: 100%; font-size: 12px; border-collapse: collapse; }
.rm-perm-table .cat-row td { background: #F7F5F0; font-weight: 600; padding: 7px 10px; }
.rm-perm-table td { padding: 7px 10px; border-bottom: 1px solid #F3F3F3; }
.rm-perm-table .perm-name { padding-left: 22px; color: #444; }
.rm-role-input {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #E0DDD4;
    border-radius: 8px;
    font-size: 13px;
    font-family: inherit;
    outline: none;
    margin-bottom: 16px;
}
.rm-role-input:focus { border-color: #B8962E; box-shadow: 0 0 0 3px rgba(184,150,46,.1); }
.rm-form-label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #555;
    margin-bottom: 6px;
}
</style>

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_content" class="app-content">
            <div id="kt_app_content_container" class="app-container container-fluid">
                <div class="rm-wrap">

                    <!-- Page Header -->
                    <div class="rm-page-header">
                        <h2 class="rm-page-title">User Management &amp; Role Matrix</h2>
                        <p class="rm-page-sub">
                            Admin only <span>·</span> Define roles <span>·</span> Set permissions per module <span>·</span> Assign users <span>·</span> Activate logins
                        </p>
                    </div>

                    <!-- Tab Bar -->
                    <div class="rm-tabs">
                        <button class="rm-tab active" data-tab="roles">
                            <span class="rm-tab-icon">🔑</span> Roles
                        </button>
                        <button class="rm-tab" data-tab="matrix">
                            <span class="rm-tab-icon">⊞</span> Permission Matrix
                        </button>
                        <button class="rm-tab" data-tab="assign">
                            <span class="rm-tab-icon">👤</span> User Assignment
                        </button>
                        <button class="rm-tab" data-tab="viewperms">
                            <span class="rm-tab-icon">👁</span> View Permissions
                        </button>
                    </div>

                    <!-- ══ TAB 1: ROLES ══ -->
                    <div class="rm-tab-panel active" id="tab-roles">
                        <div class="rm-roles-top">
                            <p class="rm-roles-desc">
                                Roles define what a group of staff can do. Each role has a permission set. Staff are assigned a role when their system login is activated.
                            </p>
                            <button class="rm-add-btn" data-bs-toggle="modal" data-bs-target="#kt_modal_add_role">
                                + Add new role
                            </button>
                        </div>

                        <div class="rm-cards-grid">
                            <?php if ($users_role) {
                                foreach ($users_role as $single_value) {
                                    $dept = $single_value['department_name'] ?? '';
                                    $style = getRoleStyle($single_value['role_name'], $dept, $dept_icons);
                                    $count = getRoleCount($single_value['slug']);
                                    $is_dept_head = !empty($single_value['is_dept_head']);
                            ?>
                            <div class="rm-card editRoleBtn"
                                 data-bs-toggle="modal" data-bs-target="#kt_modal_update_role"
                                 data-role-id="<?= $single_value['id'] ?>"
                                 data-role-name="<?= htmlspecialchars($single_value['role_name']) ?>"
                                 data-role-modules='<?= json_encode(getAssignedPermissions($single_value['id'])) ?>'>
                                <button class="rm-card-edit" title="Edit role">✏️</button>
                                <div class="rm-card-icon" style="background:<?= $style['color'] ?>">
                                    <?= $style['icon'] ?>
                                </div>
                                <div class="rm-card-role"><?= htmlspecialchars($single_value['role_name']) ?></div>
                                <div class="rm-card-dept"><?= htmlspecialchars($dept ?: 'General') ?></div>
                                <div class="rm-card-footer">
                                    <span class="rm-user-badge">
                                        <?= $count ?> <?= $count == 1 ? 'user' : 'users' ?>
                                    </span>
                                    <?php if ($is_dept_head) { ?>
                                    <span class="rm-dept-head-badge">Dept head eligible</span>
                                    <?php } ?>
                                </div>
                            </div>
                            <?php } } ?>

                            <!-- Add new role card -->
                            <div class="rm-card-add" data-bs-toggle="modal" data-bs-target="#kt_modal_add_role">
                                <div class="rm-add-plus">+</div>
                                <div class="rm-add-label">Add new role</div>
                            </div>
                        </div>

                        <div class="rm-info-box">
                            <div class="rm-info-title">How roles work in BOSS</div>
                            <p class="rm-info-text">
                                Each role has a default permission set defined in the Permission Matrix tab. When Admin assigns a role to a staff member, those permissions auto-apply. Admin can then override individual permissions per staff if needed — overrides are shown with a warning badge so they are always visible.
                            </p>
                        </div>
                    </div>

                    <!-- ══ TAB 2: PERMISSION MATRIX ══ -->
                    <div class="rm-tab-panel" id="tab-matrix">
                        <div class="rm-matrix-wrap">
                            <table class="rm-matrix-table">
                                <thead>
                                    <tr>
                                        <th style="min-width:200px">Module / Permission</th>
                                        <?php if ($users_role) 
                                            foreach ($users_role as $r) { ?>
                                        <th class="rm-role-col"><?= htmlspecialchars($r['role_name']) ?></th>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($permission_category_list as $cat) {
                                        $cat_perms = array_filter($allPermissions, fn($p) => $p['category_id'] == $cat['id']);
                                        ?>
                                    <tr class="rm-cat-row">
                                        <td colspan="<?= count($users_role) + 1 ?>"><?= htmlspecialchars($cat['category_name']) ?></td>
                                    </tr>
                                    <?php foreach ($cat_perms as $perm) { ?>
                                    <tr>
                                        <td class="rm-perm-name"><?= htmlspecialchars($perm['module_name']) ?></td>
                                        <?php if ($users_role) foreach ($users_role as $r) {
                                            $assigned = getAssignedPermissions($r['id']);
                                            $has = in_array($perm['id'], $assigned);
                                            ?>
                                        <td class="rm-role-col">
                                            <input type="checkbox" class="rm-check matrix-perm-check"
                                                   data-role="<?= $r['id'] ?>"
                                                   data-perm="<?= $perm['id'] ?>"
                                                   <?= $has ? 'checked' : '' ?>>
                                        </td>
                                        <?php } ?>
                                    </tr>
                                    <?php } } ?>
                                </tbody>
                            </table>
                        </div>
                        <div style="margin-top:16px;text-align:right">
                            <button class="rm-add-btn" id="saveMatrixBtn">Save Matrix</button>
                        </div>
                    </div>

                    <!-- ══ TAB 3: USER ASSIGNMENT ══ -->
                    <div class="rm-tab-panel" id="tab-assign">
                        <p class="rm-assign-desc">
                            Assign system login roles to staff members. A staff member must have an active login to use the system. Roles control which modules they can access.
                        </p>
                        <div class="rm-assign-grid">
                            <?php if ($users_role) {foreach ($users_role as $r) {
                                $style = getRoleStyle($r['role_name'], $r['department_name'] ?? '', $dept_icons);
                            ?>
                            <div class="rm-assign-card">
                                <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px">
                                    <div class="rm-card-icon" style="background:<?= $style['color'] ?>;width:36px;height:36px;font-size:16px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                        <?= $style['icon'] ?>
                                    </div>
                                    <div>
                                        <div class="rm-assign-card-title" style="margin-bottom:1px"><?= htmlspecialchars($r['role_name']) ?></div>
                                        <div style="font-size:11px;color:#aaa"><?= htmlspecialchars($r['department_name'] ?? 'General') ?></div>
                                    </div>
                                </div>
                                <select class="rm-assign-select" data-role-id="<?= $r['id'] ?>">
                                    <option value="">Assign a staff member…</option>
                                    <!-- Populated via JS/AJAX from staff list -->
                                </select>
                                <button class="rm-assign-btn" onclick="assignRole(<?= $r['id'] ?>, this)">Assign Role</button>
                            </div>
                            <?php } } ?>
                        </div>
                    </div>

                    <!-- ══ TAB 4: VIEW PERMISSIONS ══ -->
                    <div class="rm-tab-panel" id="tab-viewperms">
                        <div class="rm-vperm-select-wrap">
                            <select class="rm-vperm-select" id="vpermRoleSelect">
                                <option value="">Select a role to view permissions…</option>
                                <?php if ($users_role) foreach ($users_role as $r) { ?>
                                <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['role_name']) ?></option>
                                <?php } ?>
                            </select>
                            <button class="rm-vperm-view-btn" onclick="loadViewPerms()">View Permissions</button>
                        </div>
                        <div id="vpermResult"></div>
                    </div>

                </div><!-- /rm-wrap -->
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
                <form id="kt_modal_add_role_form" action="<?php echo $site_path; ?>/ajax/add-update-role">
                    <div class="row g-5">
                        <div class="col-md-6 fv-row">
                            <label class="form-label fw-semibold">Role Name <span class="text-danger">*</span></label>
                            <input type="text" name="role_name" class="form-control form-control-solid" placeholder="e.g. Store Manager, Dispatch Executive">
                        </div>

                        <!-- Department -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Primary Department</label>
                            <select name="department_id" class="form-select form-select-solid" data-control="select2" data-hide-search="true">
                                <option value="">Any department</option>
                                    <?php
                                    $deptSql = mysqli_query($con,"SELECT id,department_name FROM departments ORDER BY department_name");
                                    while($dept = mysqli_fetch_assoc($deptSql)){
                                    ?>
                                        <option value="<?= $dept['id'] ?>">
                                            <?= $dept['department_name'] ?>
                                        </option>
                                    <?php } ?>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" rows="3" class="form-control form-control-solid" placeholder="Brief description of this role's responsibilities"></textarea>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold d-block">Can be department head?</label>
                            <div class="d-flex gap-8">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" type="radio" name="is_department_head" value="1">
                                    <label class="form-check-label">Yes</label>
                                </div>

                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" type="radio" name="is_department_head" value="0" checked>
                                    <label class="form-check-label">No</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">
                                Copy permissions from existing role (optional)
                            </label>

                            <select name="copy_role_id"
                                    class="form-select form-select-solid"
                                    data-control="select2"
                                    data-hide-search="true">

                                <option value="">
                                    — Start with blank permissions
                                </option>

                                <?php
                                $roleSql = mysqli_query($con,"SELECT id,role_name FROM role ORDER BY role_name");
                                while($role = mysqli_fetch_assoc($roleSql)){
                                ?>
                                    <option value="<?= $role['id'] ?>">
                                        <?= $role['role_name'] ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <!-- Info Alert -->
                        <div class="col-md-12">
                            <div class="alert alert-primary d-flex align-items-center p-4">
                                <i class="ki-outline ki-information-5 fs-2 me-3"></i>

                                <div>
                                    After adding, go to Permission Matrix tab to set what this role can access.
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="separator my-8"></div>

                    <div class="text-end">
                        <button type="button" class="btn btn-light me-3" data-kt-roles-modal-action="cancel">Cancel</button>
                        <button type="submit" id="addRoleBtn" class="btn btn-primary" data-kt-roles-modal-action="submit">Add Role</button>
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
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body rm-modal-body">
                <form id="kt_modal_update_role_form" action="<?php echo $site_path; ?>/ajax/add-update-role">
                    <input type="hidden" name="role_id" id="update_role_id">
                    <label class="rm-form-label">Role name <span style="color:#DC2626">*</span></label>
                    <input class="rm-role-input" placeholder="Enter a role name" name="role_name" id="update_role_name"/>
                    <label class="rm-form-label">Role Permissions</label>
                    <div style="max-height:380px;overflow-y:auto">
                        <table class="rm-perm-table">
                            <tbody>
                                <?php foreach ($permission_category_list as $cat) { ?>
                                <tr class="cat-row">
                                    <td><?= htmlspecialchars($cat['category_name']) ?></td>
                                    <td style="text-align:right">
                                        <label style="font-size:11px;color:#888;cursor:pointer">
                                            <input type="checkbox" class="rm-check category-checkbox" data-category="<?= $cat['id'] ?>">
                                            Select all
                                        </label>
                                    </td>
                                </tr>
                                <?php $cat_perms = array_filter($allPermissions, fn($p) => $p['category_id'] == $cat['id']);
                                foreach ($cat_perms as $perm) { ?>
                                <tr>
                                    <td class="perm-name"><?= htmlspecialchars($perm['module_name']) ?></td>
                                    <td style="text-align:right">
                                        <input class="rm-check permission-checkbox"
                                               type="checkbox"
                                               name="permissions[<?= $perm['id'] ?>]"
                                               data-category="<?= $cat['id'] ?>"
                                               data-permission-id="<?= $perm['id'] ?>"
                                               value="1">
                                    </td>
                                </tr>
                                <?php } } ?>
                            </tbody>
                        </table>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;margin-top:20px;padding-top:16px;border-top:1px solid #F0EEE8">
                        <button type="button" class="btn btn-danger btn-sm deleteRoleBtn" id="deleteRoleBtn">Delete Role</button>
                        <div style="display:flex;gap:10px">
                            <button type="reset" class="btn btn-light" data-bs-dismiss="modal">Discard</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>var hostUrl = "assets/";</script>
<script src="<?php echo $site_path; ?>/assets/plugins/global/plugins.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/scripts.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/custom/apps/user-management/roles/list/add.js?v=<?php echo time(); ?>"></script>
<script src="<?php echo $site_path; ?>/assets/js/custom/apps/user-management/roles/list/update-role.js?v=<?php echo time(); ?>"></script>

<script>
/* ── Tab switching ── */
document.querySelectorAll('.rm-tab').forEach(function(tab) {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.rm-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.rm-tab-panel').forEach(p => p.classList.remove('active'));
        this.classList.add('active');
        document.getElementById('tab-' + this.dataset.tab).classList.add('active');
    });
});

/* ── Edit Role Modal ── */
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.editRoleBtn').forEach(function(card) {
        card.addEventListener('click', function() {
            var roleId      = this.dataset.roleId;
            var roleName    = this.dataset.roleName;
            var assigned    = JSON.parse(this.dataset.roleModules || '[]');

            document.getElementById('update_role_id').value   = roleId;
            document.getElementById('update_role_name').value = roleName;
            document.getElementById('deleteRoleBtn').dataset.roleId = roleId;

            // Uncheck all first
            document.querySelectorAll('#kt_modal_update_role_form .permission-checkbox').forEach(function(chk) {
                chk.checked = false;
            });
            // Re-check assigned
            if (Array.isArray(assigned)) {
                assigned.forEach(function(permId) {
                    var chk = document.querySelector(
                        '#kt_modal_update_role_form .permission-checkbox[data-permission-id="' + permId + '"]'
                    );
                    if (chk) chk.checked = true;
                });
            }
            // Sync category checkboxes
            syncCategoryCheckboxes('#kt_modal_update_role_form');
        });
    });
});

/* ── Category "Select all" ── */
document.querySelectorAll('.category-checkbox').forEach(function(cat) {
    cat.addEventListener('change', function() {
        var catId = this.getAttribute('data-category');
        var form  = this.closest('form');
        form.querySelectorAll('.permission-checkbox[data-category="' + catId + '"]').forEach(function(ch) {
            ch.checked = cat.checked;
        });
    });
});

function syncCategoryCheckboxes(formSel) {
    var form = document.querySelector(formSel);
    if (!form) return;
    form.querySelectorAll('.category-checkbox').forEach(function(cat) {
        var catId    = cat.getAttribute('data-category');
        var children = form.querySelectorAll('.permission-checkbox[data-category="' + catId + '"]');
        var allChk   = Array.from(children).every(c => c.checked);
        cat.checked  = allChk && children.length > 0;
    });
}

/* ── Save Permission Matrix ── */
document.getElementById('saveMatrixBtn').addEventListener('click', function() {
    var checks = document.querySelectorAll('.matrix-perm-check');
    var payload = [];
    checks.forEach(function(ch) {
        payload.push({ role: ch.dataset.role, perm: ch.dataset.perm, val: ch.checked ? 1 : 0 });
    });
    $.ajax({
        url: '<?php echo $site_path; ?>/ajax/save-permission-matrix',
        type: 'POST',
        data: { matrix: JSON.stringify(payload) },
        success: function(res) { alert('Matrix saved!'); },
        error:   function()    { alert('Error saving matrix.'); }
    });
});

/* ── View Permissions ── */
function loadViewPerms() {
    var roleId = document.getElementById('vpermRoleSelect').value;
    if (!roleId) { alert('Please select a role'); return; }
    $.ajax({
        url: '<?php echo $site_path; ?>/ajax/get-role-permissions',
        type: 'GET',
        data: { role_id: roleId },
        success: function(res) {
            var html = '';
            if (res && res.categories) {
                res.categories.forEach(function(cat) {
                    html += '<div class="rm-perm-section">';
                    html += '<div class="rm-perm-section-title">' + cat.name + '</div>';
                    cat.permissions.forEach(function(p) {
                        html += '<span class="rm-perm-pill">✓ ' + p + '</span>';
                    });
                    html += '</div>';
                });
            } else {
                html = '<p style="color:#aaa;font-size:13px">No permissions assigned to this role yet.</p>';
            }
            document.getElementById('vpermResult').innerHTML = html;
        },
        error: function() {
            document.getElementById('vpermResult').innerHTML =
                '<p style="color:#aaa;font-size:13px">No permissions data available.</p>';
        }
    });
}

/* ── User Assignment ── */
function assignRole(roleId, btn) {
    var sel = btn.closest('.rm-assign-card').querySelector('select');
    var staffId = sel.value;
    if (!staffId) { alert('Please select a staff member'); return; }
    $.ajax({
        url: '<?php echo $site_path; ?>/ajax/assign-user-role',
        type: 'POST',
        data: { role_id: roleId, staff_id: staffId },
        success: function() { alert('Role assigned successfully'); sel.value = ''; },
        error:   function() { alert('Error assigning role'); }
    });
}

/* ── Delete Role ── */
document.getElementById('deleteRoleBtn').addEventListener('click', function() {
    var roleId = this.dataset.roleId;
    if (!confirm('Are you sure you want to delete this role?')) return;
    $.ajax({
        url: '<?php echo $site_path; ?>/ajax/add-update-role',
        type: 'POST',
        data: { action: 'delete', role_id: roleId },
        success: function() {
            $('#kt_modal_update_role').modal('hide');
            location.reload();
        }
    });
});

/* ── Load staff for assignment dropdowns ── */
$(document).ready(function() {
    $.ajax({
        url: '<?php echo $site_path; ?>/ajax/get-staff-list',
        type: 'GET',
        success: function(res) {
            if (!res || !res.staff) return;
            document.querySelectorAll('.rm-assign-select').forEach(function(sel) {
                res.staff.forEach(function(s) {
                    var opt = document.createElement('option');
                    opt.value = s.id;
                    opt.textContent = s.name;
                    sel.appendChild(opt);
                });
            });
        }
    });
});
</script>
