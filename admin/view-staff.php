<?php
include("config/database.php");
include("config/auth_check.php");
include("includes/sidemenu.php");

$id = my_simple_crypt($_GET['id'], 'decrypt_1');

if ($id > 0) {
    $stmt = $con->prepare("SELECT sr.*, d.department_name, r.role_name, et.name as employment_type_name
        FROM staff_register sr
        LEFT JOIN departments d ON d.id = sr.department_id
        LEFT JOIN role r ON r.id = sr.role_id
        LEFT JOIN employment_type et ON et.id = sr.employment_type
        WHERE sr.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        header("Location: " . $site_path . "/staff-registry");
        exit;
    }
    $stmt->close();
} else {
    header("Location: " . $site_path . "/staff-registry");
    exit;
}

// Helper: safe value
function sv($val, $fallback = '—') {
    return (!empty(trim((string)$val))) ? htmlspecialchars($val) : $fallback;
}

// Parse work history
$work_history = [];
if (!empty($row['prevoius_work_history'])) {
    $work_history = json_decode($row['prevoius_work_history'], true) ?? [];
}
?>
<link href="<?php echo $site_path; ?>/assets/css/staff-register.css?v=<?php echo time(); ?>" rel="stylesheet" type="text/css" />
<style>
/* ── View page variables ── */
:root {
    --gold:       #C9A84C;
    --gold-bg:    rgba(201,168,76,0.08);
    --gold-bd:    rgba(201,168,76,0.25);
    --bg:         #F7F6F3;
    --surface:    #fff;
    --br:         #E5E3DC;
    --t1:         #1A1916;
    --t2:         #6B6860;
    --t3:         #A09D96;
    --suc-bg:     rgba(61,190,122,0.10);
    --suc-t:      #1A6B41;
    --warn-bg:    rgba(239,159,39,0.10);
    --warn-t:     #854F0B;
    --dan-bg:     rgba(226,75,74,0.10);
    --dan-t:      #8B1F1F;
    --r2: 6px;
    --r3: 10px;
}

/* ── Page wrapper ── */
.sv-wrap {
    max-width: 100%;
    margin: 0 auto;
    padding: 24px 20px 60px;
    font-family: Inter, sans-serif;
    font-size: 13px;
    color: var(--t1);
}

/* ── Top action bar ── */
.sv-topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 10px;
}
.sv-topbar-left h1 {
    font-size: 20px;
    font-weight: 700;
    margin: 0 0 2px;
    color: var(--t1);
}
.sv-topbar-left .sv-sub {
    font-size: 11px;
    color: var(--t3);
}
.sv-topbar-right {
    display: flex;
    gap: 8px;
    align-items: center;
}
.btn-edit {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 9px 20px;
    background: var(--gold);
    color: #fff;
    border: none;
    border-radius: var(--r2);
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: background .15s;
}
.btn-edit:hover { background: #b8973f; color: #fff; }
.btn-back {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    background: var(--surface);
    color: var(--t2);
    border: 0.5px solid var(--br);
    border-radius: var(--r2);
    font-size: 13px;
    cursor: pointer;
    text-decoration: none;
    transition: color .15s;
}
.btn-back:hover { color: var(--t1); }

/* ── Staff ID banner ── */
.sv-id-banner {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    background: var(--gold-bg);
    border: 0.5px solid var(--gold-bd);
    border-radius: var(--r2);
    margin-bottom: 16px;
}
.sv-id-banner .bid { font-size: 17px; font-weight: 700; color: var(--gold); font-family: monospace; letter-spacing: 1px; }
.sv-id-banner .bid-note { font-size: 11px; color: var(--t3); margin-top: 2px; }

/* ── Profile header card ── */
.sv-profile-card {
    background: var(--surface);
    border: 0.5px solid var(--br);
    border-radius: var(--r3);
    padding: 20px 22px;
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    gap: 20px;
}
.sv-avatar {
    width: 76px; height: 76px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid var(--gold-bd);
    flex-shrink: 0;
    background: var(--bg);
}
.sv-avatar-placeholder {
    width: 76px; height: 76px;
    border-radius: 50%;
    background: var(--gold-bg);
    border: 2px solid var(--gold-bd);
    display: flex; align-items: center; justify-content: center;
    font-size: 28px; font-weight: 700;
    color: var(--gold);
    flex-shrink: 0;
}
.sv-profile-info h2 {
    font-size: 18px;
    font-weight: 700;
    color: var(--t1);
    margin: 0 0 4px;
}
.sv-profile-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 6px;
}
.sv-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 11px;
    padding: 3px 10px;
    border-radius: 20px;
    font-weight: 500;
}
.sv-badge.gold  { background: var(--gold-bg);  color: var(--gold);   border: 0.5px solid var(--gold-bd); }
.sv-badge.green { background: var(--suc-bg);   color: var(--suc-t);  }
.sv-badge.warn  { background: var(--warn-bg);  color: var(--warn-t); }
.sv-badge.grey  { background: var(--bg);       color: var(--t2);     border: 0.5px solid var(--br); }

/* ── Section card ── */
.sv-card {
    background: var(--surface);
    border: 0.5px solid var(--br);
    border-radius: var(--r3);
    margin-bottom: 14px;
    overflow: hidden;
}
.sv-card-header {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 0 20px;
    min-height: 48px;
    border-bottom: 0.5px solid var(--br);
    background: #FDFAF6;
}
.sv-card-header .sv-card-icon { font-size: 16px; color: var(--gold); }
.sv-card-header .sv-card-title { font-size: 14px; font-weight: 700; color: var(--t1); }
.sv-card-header .sv-card-badge {
    margin-left: auto;
    font-size: 10px;
    font-weight: 600;
    padding: 2px 10px;
    border-radius: 20px;
    background: var(--suc-bg);
    color: var(--suc-t);
}
.sv-card-body { padding: 18px 20px; }

/* ── Field grid ── */
.sv-grid {
    display: grid;
    gap: 16px;
    margin-bottom: 16px;
}
.sv-grid:last-child { margin-bottom: 0; }
.sv-grid.g2 { grid-template-columns: 1fr 1fr; }
.sv-grid.g3 { grid-template-columns: 1fr 1fr 1fr; }
.sv-grid.g4 { grid-template-columns: 1fr 1fr 1fr 1fr; }
.sv-grid.g1 { grid-template-columns: 1fr; }

@media (max-width: 640px) {
    .sv-grid.g2, .sv-grid.g3, .sv-grid.g4 { grid-template-columns: 1fr; }
}

/* ── Single field ── */
.sv-field { display: flex; flex-direction: column; gap: 4px; }
.sv-field .sv-label {
    font-size: 10px;
    font-weight: 600;
    color: var(--t3);
    text-transform: uppercase;
    letter-spacing: .06em;
}
.sv-field .sv-value {
    font-size: 13px;
    color: var(--t1);
    font-weight: 500;
    line-height: 1.4;
}
.sv-field .sv-value.empty { color: var(--t3); font-weight: 400; font-style: italic; }

/* ── Divider ── */
.sv-divider {
    height: 0.5px;
    background: var(--br);
    margin: 16px 0;
}

/* ── Image preview ── */
.sv-doc-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: 12px;
    margin-top: 4px;
}
.sv-doc-item {
    border: 0.5px solid var(--br);
    border-radius: var(--r2);
    overflow: hidden;
    background: var(--bg);
}
.sv-doc-item img {
    width: 100%;
    height: 110px;
    object-fit: cover;
    display: block;
}
.sv-doc-item .sv-doc-label {
    font-size: 10px;
    color: var(--t2);
    padding: 6px 8px;
    font-weight: 500;
}

/* ── Bond / status rows ── */
.sv-row-list { display: flex; flex-direction: column; }
.sv-row-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 12px;
    padding: 9px 0;
    border-bottom: 0.5px solid var(--br);
}
.sv-row-item:last-child { border-bottom: none; }
.sv-row-key { color: var(--t2); }
.sv-row-val { font-weight: 500; }

/* ── Work history card ── */
.sv-employer-card {
    background: #F7F6F3;
    border: 0.5px solid var(--br);
    border-radius: var(--r2);
    padding: 14px 16px;
    margin-bottom: 10px;
    position: relative;
}
.sv-employer-card:last-child { margin-bottom: 0; }
.sv-employer-name { font-size: 13px; font-weight: 700; color: var(--t1); margin-bottom: 6px; }
.sv-employer-meta { font-size: 11px; color: var(--t2); margin-bottom: 2px; display: flex; gap: 6px; align-items: center; }
.sv-employer-meta span { color: var(--t3); }

/* ── Increment section ── */
.sv-inc-type {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    background: var(--gold-bg);
    border: 0.5px solid var(--gold-bd);
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    color: var(--gold);
    margin-bottom: 14px;
}

/* ── Alert box ── */
.sv-alert {
    display: flex;
    gap: 8px;
    align-items: flex-start;
    padding: 10px 14px;
    border-radius: var(--r2);
    font-size: 11px;
    line-height: 1.5;
    margin-top: 12px;
}
.sv-alert.warn { background: var(--warn-bg); color: var(--warn-t); }
.sv-alert.info { background: var(--gold-bg); color: #6B5000; }

/* ── Bond not applicable ── */
.sv-not-applicable {
    padding: 20px;
    text-align: center;
    color: var(--t3);
    font-size: 12px;
}

/* ── No data placeholder ── */
.sv-empty-state {
    padding: 24px;
    text-align: center;
    color: var(--t3);
    font-size: 12px;
}
.sv-empty-state i { font-size: 28px; display: block; margin-bottom: 6px; opacity: .4; }
</style>

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
<div class="d-flex flex-column flex-column-fluid">

    

    <div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-xxl">
    <div class="sv-wrap">

        <?php
        // Build full name & initials
        $fullname = trim(sv($row['firstname'],'') . ' ' . sv($row['middlename'],'') . ' ' . sv($row['lastname'],''));
        if (empty($fullname)) $fullname = '—';
        $initials = '';
        if (!empty($row['firstname'])) $initials .= strtoupper($row['firstname'][0]);
        if (!empty($row['lastname']))  $initials .= strtoupper($row['lastname'][0]);
        if (empty($initials)) $initials = '?';

        $encrypted_id = my_simple_crypt($id, 'encrypt_1');
        ?>

        <!-- ── Top action bar ── -->
        <div class="sv-topbar">
            <div class="sv-topbar-left">
                <h1>Staff Profile</h1>
                <div class="sv-sub"><?php echo sv($row['employee_code']); ?> &middot; View only</div>
            </div>
            <div class="sv-topbar-right">
                <a href="<?php echo $site_path; ?>/staff-registry" class="btn-back">
                    ← Back to List
                </a>
                <a href="<?php echo $site_path; ?>/edit-staff-register?id=<?php echo $encrypted_id; ?>" class="btn-edit">
                    ✏️ Edit Profile
                </a>
            </div>
        </div>

        <!-- ── Staff ID banner ── -->
        <div class="sv-id-banner">
            <span style="font-size:22px;">🪪</span>
            <div>
                <div class="bid"><?php echo sv($row['employee_code']); ?></div>
                <div class="bid-note">Staff ID &middot; Registered on <?php echo !empty($row['created_at']) ? date('d M Y', strtotime($row['created_at'])) : '—'; ?></div>
            </div>
        </div>

        <!-- ── Profile header ── -->
        <div class="sv-profile-card">
            <?php if (!empty($row['profile_picture'])): ?>
                <img class="sv-avatar"
                     src="<?php echo $define_company_website; ?>/uploads/staff/<?php echo htmlspecialchars($row['profile_picture']); ?>"
                     alt="<?php echo $fullname; ?>">
            <?php else: ?>
                <div class="sv-avatar-placeholder"><?php echo $initials; ?></div>
            <?php endif; ?>

            <div class="sv-profile-info">
                <h2><?php echo $fullname; ?></h2>
                <div style="font-size:12px;color:var(--t2);margin-bottom:6px;">
                    <?php echo sv($row['role_name']); ?>
                    <?php if(!empty($row['department_name'])): ?>
                        &nbsp;&middot;&nbsp; <?php echo sv($row['department_name']); ?>
                    <?php endif; ?>
                </div>
                <div class="sv-profile-meta">
                    <?php if(!empty($row['mobile_number'])): ?>
                        <span class="sv-badge grey">📞 <?php echo sv($row['mobile_number']); ?></span>
                    <?php endif; ?>
                    <?php if(!empty($row['email'])): ?>
                        <span class="sv-badge grey">✉️ <?php echo sv($row['email']); ?></span>
                    <?php endif; ?>
                    <?php if(!empty($row['gender'])): ?>
                        <span class="sv-badge grey"><?php echo sv($row['gender']); ?></span>
                    <?php endif; ?>
                    <?php if(!empty($row['blood_group'])): ?>
                        <span class="sv-badge warn">🩸 <?php echo sv($row['blood_group']); ?></span>
                    <?php endif; ?>
                    <?php if($row['is_department_head'] == 1): ?>
                        <span class="sv-badge gold">⭐ Dept Head</span>
                    <?php endif; ?>
                    <span class="sv-badge green">✓ Active</span>
                </div>
            </div>
        </div>

        <!-- ══════════════════════════════════
             SECTION 1 — PERSONAL DETAILS
        ══════════════════════════════════ -->
        <div class="sv-card">
            <div class="sv-card-header">
                <span class="sv-card-icon">👤</span>
                <span class="sv-card-title">Personal Details</span>
            </div>
            <div class="sv-card-body">

                <div class="sv-grid g3">
                    <div class="sv-field">
                        <span class="sv-label">First Name</span>
                        <span class="sv-value <?php echo empty($row['firstname']) ? 'empty' : ''; ?>">
                            <?php echo sv($row['firstname']); ?>
                        </span>
                    </div>
                    <div class="sv-field">
                        <span class="sv-label">Middle Name</span>
                        <span class="sv-value <?php echo empty($row['middlename']) ? 'empty' : ''; ?>">
                            <?php echo sv($row['middlename']); ?>
                        </span>
                    </div>
                    <div class="sv-field">
                        <span class="sv-label">Last Name</span>
                        <span class="sv-value <?php echo empty($row['lastname']) ? 'empty' : ''; ?>">
                            <?php echo sv($row['lastname']); ?>
                        </span>
                    </div>
                </div>

                <div class="sv-divider"></div>

                <div class="sv-grid g3">
                    <div class="sv-field">
                        <span class="sv-label">Date of Birth</span>
                        <span class="sv-value">
                            <?php echo !empty($row['dob']) ? date('d M Y', strtotime($row['dob'])) : '—'; ?>
                        </span>
                    </div>
                    <div class="sv-field">
                        <span class="sv-label">Gender</span>
                        <span class="sv-value"><?php echo sv($row['gender']); ?></span>
                    </div>
                    <div class="sv-field">
                        <span class="sv-label">Blood Group</span>
                        <span class="sv-value"><?php echo sv($row['blood_group']); ?></span>
                    </div>
                </div>

                <div class="sv-divider"></div>

                <div class="sv-grid g2">
                    <div class="sv-field">
                        <span class="sv-label">Mobile Number</span>
                        <span class="sv-value"><?php echo sv($row['mobile_number']); ?></span>
                    </div>
                    <div class="sv-field">
                        <span class="sv-label">Email Address</span>
                        <span class="sv-value"><?php echo sv($row['email']); ?></span>
                    </div>
                </div>

                <div class="sv-divider"></div>

                <div class="sv-grid g1">
                    <div class="sv-field">
                        <span class="sv-label">Current Address</span>
                        <span class="sv-value"><?php echo sv($row['address']); ?></span>
                    </div>
                </div>

                <div class="sv-divider"></div>

                <div class="sv-grid g2">
                    <div class="sv-field">
                        <span class="sv-label">Emergency Contact Name</span>
                        <span class="sv-value"><?php echo sv($row['emergancy_contact_person']); ?></span>
                    </div>
                    <div class="sv-field">
                        <span class="sv-label">Emergency Contact Phone</span>
                        <span class="sv-value"><?php echo sv($row['emergancy_contact_number']); ?></span>
                    </div>
                </div>

            </div>
        </div>

        <!-- ══════════════════════════════════
             SECTION 2 — EMPLOYMENT DETAILS
        ══════════════════════════════════ -->
        <div class="sv-card">
            <div class="sv-card-header">
                <span class="sv-card-icon">🏢</span>
                <span class="sv-card-title">Employment Details</span>
            </div>
            <div class="sv-card-body">

                <div class="sv-grid g2">
                    <div class="sv-field">
                        <span class="sv-label">Department</span>
                        <span class="sv-value"><?php echo sv($row['department_name']); ?></span>
                    </div>
                    <div class="sv-field">
                        <span class="sv-label">Role / Designation</span>
                        <span class="sv-value"><?php echo sv($row['role_name']); ?></span>
                    </div>
                </div>

                <div class="sv-divider"></div>

                <div class="sv-grid g2">
                    <div class="sv-field">
                        <span class="sv-label">Is Department Head</span>
                        <span class="sv-value">
                            <?php if ($row['is_department_head'] == 1): ?>
                                <span class="sv-badge gold">⭐ Yes — Dept Head</span>
                            <?php else: ?>
                                <span class="sv-badge grey">No</span>
                            <?php endif; ?>
                        </span>
                    </div>
                    <div class="sv-field">
                        <span class="sv-label">Date of Joining</span>
                        <span class="sv-value">
                            <?php echo !empty($row['doj']) ? date('d M Y', strtotime($row['doj'])) : '—'; ?>
                        </span>
                    </div>
                </div>

                <div class="sv-divider"></div>

                <div class="sv-grid g2">
                    <div class="sv-field">
                        <span class="sv-label">Employment Type</span>
                        <span class="sv-value"><?php echo sv($row['employment_type_name']); ?></span>
                    </div>
                    <div class="sv-field">
                        <span class="sv-label">Work Location</span>
                        <span class="sv-value"><?php echo sv($row['work_location']); ?></span>
                    </div>
                </div>

            </div>
        </div>

        <!-- ══════════════════════════════════
             SECTION 3 — AADHAAR DETAILS
        ══════════════════════════════════ -->
        <div class="sv-card">
            <div class="sv-card-header">
                <span class="sv-card-icon">🪪</span>
                <span class="sv-card-title">Aadhaar Details</span>
            </div>
            <div class="sv-card-body">

                <div class="sv-grid g2">
                    <div class="sv-field">
                        <span class="sv-label">Aadhaar Number</span>
                        <span class="sv-value">
                            <?php
                            // Mask middle digits: XXXX XXXX 9012
                            $aadhar = sv($row['aadhar_number'], '');
                            if (strlen(preg_replace('/\s/', '', $aadhar)) >= 8) {
                                $clean = preg_replace('/\s/', '', $aadhar);
                                echo 'XXXX XXXX ' . substr($clean, -4);
                            } else {
                                echo sv($row['aadhar_number']);
                            }
                            ?>
                        </span>
                    </div>
                    <div class="sv-field">
                        <span class="sv-label">Name as on Aadhaar</span>
                        <span class="sv-value"><?php echo sv($row['aadhaar_name']); ?></span>
                    </div>
                </div>

                <?php if (!empty($row['aadhar_front_image']) || !empty($row['aadhar_back_image'])): ?>
                <div class="sv-divider"></div>
                <div class="sv-field" style="margin-bottom:8px;">
                    <span class="sv-label">Aadhaar Documents</span>
                </div>
                <div class="sv-doc-grid">
                    <?php if (!empty($row['aadhar_front_image'])): ?>
                    <div class="sv-doc-item">
                        <img src="<?php echo $define_company_website; ?>/uploads/staff/documents/<?php echo htmlspecialchars($row['aadhar_front_image']); ?>"
                             alt="Aadhaar Front">
                        <div class="sv-doc-label">📄 Aadhaar Front</div>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($row['aadhar_back_image'])): ?>
                    <div class="sv-doc-item">
                        <img src="<?php echo $define_company_website; ?>/uploads/staff/documents/<?php echo htmlspecialchars($row['aadhar_back_image']); ?>"
                             alt="Aadhaar Back">
                        <div class="sv-doc-label">📄 Aadhaar Back</div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

            </div>
        </div>

        <!-- ══════════════════════════════════
             SECTION 4 — BOND DETAILS
        ══════════════════════════════════ -->
        <div class="sv-card">
            <div class="sv-card-header">
                <span class="sv-card-icon">📋</span>
                <span class="sv-card-title">Bond Details</span>
                <?php if ($row['is_bond_applicable'] == 1): ?>
                    <span class="sv-card-badge">Bond Active</span>
                <?php endif; ?>
            </div>
            <div class="sv-card-body">

                <?php if ($row['is_bond_applicable'] == 1): ?>

                    <div class="sv-row-list">
                        <div class="sv-row-item">
                            <span class="sv-row-key">Bond Applicable</span>
                            <span class="sv-badge green">✓ Yes</span>
                        </div>
                        <div class="sv-row-item">
                            <span class="sv-row-key">Bond Start Date</span>
                            <span class="sv-row-val">
                                <?php echo !empty($row['contract_start']) ? date('d M Y', strtotime($row['contract_start'])) : '—'; ?>
                            </span>
                        </div>
                        <div class="sv-row-item">
                            <span class="sv-row-key">Bond End Date</span>
                            <span class="sv-row-val">
                                <?php echo !empty($row['contract_end']) ? date('d M Y', strtotime($row['contract_end'])) : '—'; ?>
                            </span>
                        </div>
                        <div class="sv-row-item">
                            <span class="sv-row-key">Bond Tenure</span>
                            <span class="sv-row-val"><?php echo sv($row['bond_tenure']); ?></span>
                        </div>
                        <div class="sv-row-item">
                            <span class="sv-row-key">Bond Amount</span>
                            <span class="sv-row-val">
                                <?php echo !empty($row['bond_amount']) ? '₹ ' . number_format($row['bond_amount']) : '—'; ?>
                            </span>
                        </div>
                    </div>

                    <?php if (!empty($row['bond_doc'])): ?>
                    <div style="margin-top:14px;">
                        <div class="sv-label" style="font-size:10px;color:var(--t3);text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px;">Bond Document</div>
                        <div class="sv-doc-grid">
                            <div class="sv-doc-item">
                                <img src="<?php echo $define_company_website; ?>/uploads/staff/documents/<?php echo htmlspecialchars($row['bond_doc']); ?>"
                                     alt="Bond Document">
                                <div class="sv-doc-label">📄 Signed Bond</div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="sv-alert warn">
                        🔔 Auto-alert will be sent to Admin + HR Head 30 days before bond expiry.
                    </div>

                <?php else: ?>
                    <div class="sv-not-applicable">
                        <div style="font-size:28px;margin-bottom:6px;">📋</div>
                        Bond is <strong>not applicable</strong> for this staff member.
                    </div>
                <?php endif; ?>

            </div>
        </div>

        <!-- ══════════════════════════════════
             SECTION 5 — WORK HISTORY
        ══════════════════════════════════ -->
        <div class="sv-card">
            <div class="sv-card-header">
                <span class="sv-card-icon">💼</span>
                <span class="sv-card-title">Previous Work History</span>
                <?php if (count($work_history) > 0): ?>
                    <span class="sv-card-badge"><?php echo count($work_history); ?> Employer<?php echo count($work_history) > 1 ? 's' : ''; ?></span>
                <?php endif; ?>
            </div>
            <div class="sv-card-body">

                <?php if (count($work_history) > 0): ?>
                    <?php foreach ($work_history as $idx => $job): ?>
                    <div class="sv-employer-card">
                        <div class="sv-employer-name">
                            <?php echo htmlspecialchars($job['employer_name'] ?? '—'); ?>
                        </div>
                        <div class="sv-employer-meta">
                            <span>Role:</span>
                            <?php echo htmlspecialchars($job['designation'] ?? '—'); ?>
                        </div>
                        <?php if (!empty($job['duration'])): ?>
                        <div class="sv-employer-meta">
                            <span>Duration:</span>
                            <?php echo htmlspecialchars($job['duration']); ?>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($job['salary'])): ?>
                        <div class="sv-employer-meta">
                            <span>Last Salary:</span>
                            ₹<?php echo number_format($job['salary']); ?>/month
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($job['reason'])): ?>
                        <div class="sv-employer-meta">
                            <span>Reason for leaving:</span>
                            <?php echo htmlspecialchars($job['reason']); ?>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($job['employer_address'])): ?>
                        <div class="sv-employer-meta">
                            <span>Address:</span>
                            <?php echo htmlspecialchars($job['employer_address']); ?>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($job['notes'])): ?>
                        <div class="sv-employer-meta">
                            <span>Notes:</span>
                            <?php echo htmlspecialchars($job['notes']); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>

                <?php else: ?>
                    <div class="sv-empty-state">
                        <div style="font-size:28px;margin-bottom:6px;">💼</div>
                        No previous work history added.
                    </div>
                <?php endif; ?>

            </div>
        </div>

        <!-- ══════════════════════════════════
             SECTION 6 — INCREMENT SETUP
        ══════════════════════════════════ -->
        <div class="sv-card">
            <div class="sv-card-header">
                <span class="sv-card-icon">📈</span>
                <span class="sv-card-title">Increment Reminder Setup</span>
            </div>
            <div class="sv-card-body">

                <?php if (!empty($row['increment_basis'])): ?>

                    <div class="sv-inc-type">
                        <?php echo $row['increment_basis'] === 'periodic' ? '🔁 Periodic — fixed time intervals' : '🎯 Performance-based'; ?>
                    </div>

                    <?php if ($row['increment_basis'] === 'periodic'): ?>
                        <div class="sv-grid g3">
                            <div class="sv-field">
                                <span class="sv-label">First Increment After</span>
                                <span class="sv-value"><?php echo sv($row['first_increment_after']); ?></span>
                            </div>
                            <div class="sv-field">
                                <span class="sv-label">Then Every</span>
                                <span class="sv-value"><?php echo sv($row['increment_frequency']); ?></span>
                            </div>
                            <div class="sv-field">
                                <span class="sv-label">Remind Admin Before</span>
                                <span class="sv-value">
                                    <?php echo !empty($row['periodic_reminder_days']) ? $row['periodic_reminder_days'] . ' Days' : '—'; ?>
                                </span>
                            </div>
                        </div>

                    <?php else: ?>
                        <div class="sv-grid g2">
                            <div class="sv-field">
                                <span class="sv-label">Review Cycle</span>
                                <span class="sv-value"><?php echo sv($row['review_cycle']); ?></span>
                            </div>
                            <div class="sv-field">
                                <span class="sv-label">Min. Performance Score</span>
                                <span class="sv-value"><?php echo sv($row['performance_score']); ?></span>
                            </div>
                        </div>
                        <div class="sv-divider"></div>
                        <div class="sv-field">
                            <span class="sv-label">Remind Admin Before Review</span>
                            <span class="sv-value">
                                <?php echo !empty($row['performance_reminder_days']) ? $row['performance_reminder_days'] . ' Days' : '—'; ?>
                            </span>
                        </div>
                    <?php endif; ?>

                    <div class="sv-alert info">
                        🔔 Reminder sent to: Admin, HR Head, Finance module salary cost view.
                    </div>

                <?php else: ?>
                    <div class="sv-empty-state">
                        <div style="font-size:28px;margin-bottom:6px;">📈</div>
                        Increment setup not configured.
                    </div>
                <?php endif; ?>

            </div>
        </div>

        <!-- ── Bottom action bar ── -->
        <div style="display:flex;justify-content:space-between;align-items:center;padding:16px 0 0;border-top:0.5px solid var(--br);margin-top:4px;">
            <a href="<?php echo $site_path; ?>/staff-registry" class="btn-back">← Back to List</a>
            <a href="<?php echo $site_path; ?>/edit-staff-register?id=<?php echo $encrypted_id; ?>" class="btn-edit">✏️ Edit Profile</a>
        </div>

    </div><!-- /sv-wrap -->
    </div>
    </div>

    <?php include("includes/footer.php"); ?>
</div>
</div>
<script src="<?php echo $site_path; ?>/assets/plugins/global/plugins.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/scripts.bundle.js"></script>