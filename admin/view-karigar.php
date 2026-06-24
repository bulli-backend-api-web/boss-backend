<?php
include("config/database.php");
include("config/auth_check.php");
include("includes/sidemenu.php");

$id = my_simple_crypt($_GET['id'], 'decrypt_1');

if ($id > 0) {
    $stmt = $con->prepare("SELECT kr.* FROM karigar_registration kr WHERE kr.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        header("Location: " . $site_path . "/karigar-registration");
        exit;
    }
    $stmt->close();
} else {
    header("Location: " . $site_path . "/karigar-registration");
    exit;
}

// Helper: safe value
function sv($val, $fallback = '—') {
    return (!empty(trim((string) $val))) ? htmlspecialchars($val) : $fallback;
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
    .btn-edit:hover {
        background: #b8973f;
        color: #fff;
    }
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
    .btn-back:hover {
        color: var(--t1);
    }

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
    .sv-id-banner .bid {
        font-size: 17px;
        font-weight: 700;
        color: var(--gold);
        font-family: monospace;
        letter-spacing: 1px;
    }
    .sv-id-banner .bid-note {
        font-size: 11px;
        color: var(--t3);
        margin-top: 2px;
    }

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
        width: 76px;
        height: 76px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid var(--gold-bd);
        flex-shrink: 0;
        background: var(--bg);
    }
    .sv-avatar-placeholder {
        width: 76px;
        height: 76px;
        border-radius: 50%;
        background: var(--gold-bg);
        border: 2px solid var(--gold-bd);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        font-weight: 700;
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
    .sv-badge.gold  {
        background: var(--gold-bg);
        color: var(--gold);
        border: 0.5px solid var(--gold-bd);
    }
    .sv-badge.green {
        background: var(--suc-bg);
        color: var(--suc-t);
    }
    .sv-badge.warn  {
        background: var(--warn-bg);
        color: var(--warn-t);
    }
    .sv-badge.grey  {
        background: var(--bg);
        color: var(--t2);
        border: 0.5px solid var(--br);
    }

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
    .sv-card-header .sv-card-icon {
        font-size: 16px;
        color: var(--gold);
    }
    .sv-card-header .sv-card-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--t1);
    }
    .sv-card-header .sv-card-badge {
        margin-left: auto;
        font-size: 10px;
        font-weight: 600;
        padding: 2px 10px;
        border-radius: 20px;
        background: var(--suc-bg);
        color: var(--suc-t);
    }
    .sv-card-body {
        padding: 18px 20px;
    }

    /* ── Field grid ── */
    .sv-grid {
        display: grid;
        gap: 16px;
        margin-bottom: 16px;
    }
    .sv-grid:last-child {
        margin-bottom: 0;
    }
    .sv-grid.g2 {
        grid-template-columns: 1fr 1fr;
    }
    .sv-grid.g3 {
        grid-template-columns: 1fr 1fr 1fr;
    }
    .sv-grid.g4 {
        grid-template-columns: 1fr 1fr 1fr 1fr;
    }
    .sv-grid.g1 {
        grid-template-columns: 1fr;
    }

    @media (max-width: 640px) {
        .sv-grid.g2, .sv-grid.g3, .sv-grid.g4 {
            grid-template-columns: 1fr;
        }
    }

    /* ── Single field ── */
    .sv-field {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
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
    .sv-field .sv-value.empty {
        color: var(--t3);
        font-weight: 400;
        font-style: italic;
    }

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
    .sv-row-list {
        display: flex;
        flex-direction: column;
    }
    .sv-row-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 12px;
        padding: 9px 0;
        border-bottom: 0.5px solid var(--br);
    }
    .sv-row-item:last-child {
        border-bottom: none;
    }
    .sv-row-key {
        color: var(--t2);
    }
    .sv-row-val {
        font-weight: 500;
    }

    /* ── Work history card ── */
    .sv-employer-card {
        background: #F7F6F3;
        border: 0.5px solid var(--br);
        border-radius: var(--r2);
        padding: 14px 16px;
        margin-bottom: 10px;
        position: relative;
    }
    .sv-employer-card:last-child {
        margin-bottom: 0;
    }
    .sv-employer-name {
        font-size: 13px;
        font-weight: 700;
        color: var(--t1);
        margin-bottom: 6px;
    }
    .sv-employer-meta {
        font-size: 11px;
        color: var(--t2);
        margin-bottom: 2px;
        display: flex;
        gap: 6px;
        align-items: center;
    }
    .sv-employer-meta span {
        color: var(--t3);
    }

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
    .sv-alert.warn {
        background: var(--warn-bg);
        color: var(--warn-t);
    }
    .sv-alert.info {
        background: var(--gold-bg);
        color: #6B5000;
    }

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
    .sv-empty-state i {
        font-size: 28px;
        display: block;
        margin-bottom: 6px;
        opacity: .4;
    }
</style>

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">



        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                <div class="sv-wrap">

                    <?php
                    // Build full name & initials
                    $fullname = trim(sv($row['firstname'], '') . ' ' . sv($row['middlename'], '') . ' ' . sv($row['lastname'], ''));
                    if (empty($fullname))
                        $fullname = '—';
                    $initials = '';
                    if (!empty($row['firstname']))
                        $initials .= strtoupper($row['firstname'][0]);
                    if (!empty($row['lastname']))
                        $initials .= strtoupper($row['lastname'][0]);
                    if (empty($initials))
                        $initials = '?';

                    $encrypted_id = my_simple_crypt($id, 'encrypt_1');
                    ?>

                    <!-- ── Top action bar ── -->
                    <div class="sv-topbar">
                        <div class="sv-topbar-left">
                            <h1>Staff Profile</h1>
                            <div class="sv-sub"><?php echo sv($row['employee_code']); ?> &middot; View only</div>
                        </div>
                        <div class="sv-topbar-right">
                            <a href="<?php echo $site_path; ?>/karigar-registration" class="btn-back">
                                ← Back to List
                            </a>
                            <a href="<?php echo $site_path; ?>/edit-karigar-register?id=<?php echo $encrypted_id; ?>" class="btn-edit">
                                ✏️ Edit Profile
                            </a>
                        </div>
                    </div>

                    <!-- ── Staff ID banner ── -->
                    <div class="sv-id-banner">
                        <span style="font-size:22px;">🪪</span>
                        <div>
                            <div class="bid"><?php echo sv($row['employee_code']); ?></div>
                            <div class="bid-note">Staff ID &middot; Registered on <?php echo!empty($row['created_at']) ? date('d M Y', strtotime($row['created_at'])) : '—'; ?></div>
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
                            <div class="sv-profile-meta">
                                <?php if (!empty($row['mobile_number'])): ?>
                                    <span class="sv-badge grey">📞 <?php echo sv($row['mobile_number']); ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($row['gender'])): ?>
                                    <span class="sv-badge grey"><?php echo sv($row['gender']); ?></span>
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
                                        <?php echo!empty($row['dob']) ? date('d M Y', strtotime($row['dob'])) : '—'; ?>
                                    </span>
                                </div>
                                <div class="sv-field">
                                    <span class="sv-label">Gender</span>
                                    <span class="sv-value"><?php echo sv($row['gender']); ?></span>
                                </div>
                                <div class="sv-field">
                                    <span class="sv-label">Mobile Number</span>
                                    <span class="sv-value"><?php echo sv($row['mobile_number']); ?></span>
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
                        </div>
                    </div>

                    <!-- ══════════════════════════════════
                         SECTION 2 — EMPLOYMENT DETAILS
                    ══════════════════════════════════ -->
                    <div class="sv-card">
                        <div class="sv-card-header">
                            <span class="sv-card-icon">🏢</span>
                            <span class="sv-card-title">Job Details</span>
                        </div>
                        <div class="sv-card-body">

                            <div class="sv-grid g2">
                                <div class="sv-field">
                                    <span class="sv-label">Job Type</span>
                                    <span class="sv-value"><?php echo sv($row['job_type']); ?></span>
                                </div>
                                <div class="sv-field">
                                    <span class="sv-label">Date of Joining</span>
                                    <span class="sv-value">
                                        <?php echo!empty($row['doj']) ? date('d M Y', strtotime($row['doj'])) : '—'; ?>
                                    </span>
                                </div>
                            </div>

                            <div class="sv-divider"></div>

                            <div class="sv-grid g2">
                                <div class="sv-field">
                                    <span class="sv-label">Speciality</span>
                                    <span class="sv-value"><?php echo sv($row['skills']); ?></span>
                                </div>
                                <div class="sv-field">
                                    <span class="sv-label">Reference Name</span>
                                    <span class="sv-value"><?php echo sv($row['reference_name']); ?></span>
                                </div>
                            </div>
                            <div class="sv-divider"></div>
                        </div>
                    </div>

                    <!-- ══════════════════════════════════
                         SECTION 3 — AADHAAR DETAILS
                    ══════════════════════════════════ -->
                    <div class="sv-card">
                        <div class="sv-card-header">
                            <span class="sv-card-icon">🪪</span>
                            <span class="sv-card-title">Identity Proof</span>
                        </div>
                        <div class="sv-card-body">

                            <div class="sv-grid g3">
                                <div class="sv-field">
                                    <span class="sv-label">Document Type</span>
                                    <span class="sv-value"><?php echo sv($row['identity_proof']); ?></span>
                                </div>
                                <div class="sv-field">
                                    <span class="sv-label">Aadhaar Number</span>
                                    <span class="sv-value">
                                        <?php
                                        // Mask middle digits: XXXX XXXX 9012
                                        $aadhar = sv($row['identity_proof_number'], '');
                                        if (strlen(preg_replace('/\s/', '', $aadhar)) >= 8) {
                                            $clean = preg_replace('/\s/', '', $aadhar);
                                            echo 'XXXX XXXX ' . substr($clean, -4);
                                        } else {
                                            echo sv($row['identity_proof_number']);
                                        }
                                        ?>
                                    </span>
                                </div>
                                <div class="sv-field">
                                    <span class="sv-label">Name as on Aadhaar</span>
                                    <span class="sv-value"><?php echo sv($row['identity_proof_name']); ?></span>
                                </div>
                            </div>

                            <?php if (!empty($row['identity_proof_front_doc']) || !empty($row['identity_proof_front_doc'])): ?>
                                <div class="sv-divider"></div>
                                <div class="sv-field" style="margin-bottom:8px;">
                                    <span class="sv-label">Aadhaar Documents</span>
                                </div>
                                <div class="sv-doc-grid">
                                    <?php if (!empty($row['identity_proof_front_doc'])): ?>
                                        <div class="sv-doc-item">
                                            <img src="<?php echo $define_company_website; ?>/uploads/karigar/documents/<?php echo htmlspecialchars($row['identity_proof_front_doc']); ?>"
                                                 alt="Aadhaar Front">
                                            <div class="sv-doc-label">📄 Aadhaar Front</div>
                                        </div>
                                        <?php endif; ?>
                                        <?php if (!empty($row['identity_proof_back_doc'])): ?>
                                        <div class="sv-doc-item">
                                            <img src="<?php echo $define_company_website; ?>/uploads/karigar/documents/<?php echo htmlspecialchars($row['identity_proof_back_doc']); ?>"
                                                 alt="Aadhaar Back">
                                            <div class="sv-doc-label">📄 Aadhaar Back</div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>
                    <div class="sv-card">
                        <div class="sv-card-header">
                            <span class="sv-card-icon">📈</span>
                            <span class="sv-card-title">Bank Details</span>
                        </div>
                        <div class="sv-card-body">

                            <div class="sv-grid g2">
                                <div class="sv-field">
                                    <span class="sv-label">Bank Name</span>
                                    <span class="sv-value"><?php echo sv($row['bank_name']); ?></span>
                                </div>
                                <div class="sv-field">
                                    <span class="sv-label">Branch Name</span>
                                    <span class="sv-value">
                                        <?php echo sv($row['branch_name']); ?>
                                    </span>
                                </div>
                            </div>

                            <div class="sv-divider"></div>

                            <div class="sv-grid g2">
                                <div class="sv-field">
                                    <span class="sv-label">IFSC Code</span>
                                    <span class="sv-value"><?php echo sv($row['ifsc_code']); ?></span>
                                </div>
                                <div class="sv-field">
                                    <span class="sv-label">Account Number</span>
                                    <span class="sv-value"><?php echo sv($row['account_number']); ?></span>
                                </div>
                            </div>
                            <div class="sv-divider"></div>
                        </div>
                    </div>

                    <div class="sv-card">
                        <div class="sv-card-header">
                            <span class="sv-card-icon">📈</span>
                            <span class="sv-card-title">Salary Setup</span>
                        </div>
                        <div class="sv-card-body">

                            <div class="sv-grid g2">
                                <div class="sv-field">
                                    <span class="sv-label">Salary Type</span>
                                    <span class="sv-value"><?php echo sv($row['salary_type']); ?></span>
                                </div>
                                <div class="sv-field">
                                    <span class="sv-label">Montly Salary</span>
                                    <span class="sv-value">
                                        <?php echo sv($row['monthly_salary']); ?>
                                    </span>
                                </div>
                            </div>

                            <div class="sv-divider"></div>

                            <div class="sv-grid g2">
                                <div class="sv-field">
                                    <span class="sv-label">Payment Date</span>
                                    <span class="sv-value"><?php echo sv($row['payment_date']); ?></span>
                                </div>
                                <div class="sv-field">
                                    <span class="sv-label">Work Start Time</span>
                                    <span class="sv-value"><?php echo sv($row['work_start_time']); ?></span>
                                </div>
                            </div>
                            <div class="sv-divider"></div>

                            <div class="sv-grid g2">
                                <div class="sv-field">
                                    <span class="sv-label">Work End Time</span>
                                    <span class="sv-value"><?php echo sv($row['work_end_time']); ?></span>
                                </div>

                                <div class="sv-field">
                                    <span class="sv-label">Break Duration</span>
                                    <span class="sv-value"><?php echo sv($row['break_duration']); ?></span>
                                </div>

                            </div>

                            <div class="sv-divider"></div>

                            <div class="sv-grid g2">
                                <div class="sv-field">
                                    <span class="sv-label">Weekly Off Day</span>
                                    <span class="sv-value"><?php echo sv($row['weekly_off']); ?></span>
                                </div>
                                <div class="sv-field">
                                    <span class="sv-label">Holiday Remarks</span>
                                    <span class="sv-value"><?php echo sv($row['weekly_off']); ?></span>
                                </div>

                            </div>
                            <div class="sv-divider"></div>

                            <div class="sv-grid g2">
                                <div class="sv-field">
                                    <span class="sv-label">Salary Remarks</span>
                                    <span class="sv-value"><?php echo sv($row['salary_remarks']); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div style="display:flex;justify-content:space-between;align-items:center;padding:16px 0 0;border-top:0.5px solid var(--br);margin-top:4px;">
                        <a href="<?php echo $site_path; ?>/karigar-registration" class="btn-back">← Back to List</a>
                        <a href="<?php echo $site_path; ?>/edit-staff-register?id=<?php echo $encrypted_id; ?>" class="btn-edit">✏️ Edit Profile</a>
                    </div>
                </div>
            </div>
        </div>

<?php include("includes/footer.php"); ?>
    </div>
</div>
<script src="<?php echo $site_path; ?>/assets/plugins/global/plugins.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/scripts.bundle.js"></script>