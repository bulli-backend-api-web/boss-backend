<?php
include("config/database.php");
include("config/auth_check.php");
include("includes/sidemenu.php");
error_reporting(E_ALL);
ini_set('display_errors', 0);
$id = my_simple_crypt($_GET['id'], 'decrypt_1');
if ($id > 0) {
    $stmt = $con->prepare("SELECT * FROM models WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $model_id = $row['model_id'];
        $first_name = $row['first_name'];
        $last_name = $row['last_name'];
        $dob = $row['dob'];
        $age = $row['age'];
        $gender = $row['gender'];
        $agency_division = $row['agency_division'];
        $mobile_primary = $row['mobile_primary'];
        $mobile_alternate = $row['mobile_alternate'];
        $email = $row['email'];
        $instagram = $row['instagram'];
        $youtube_channel_id = $row['youtube_channel_id'];
        $address = $row['address'];
        $city = $row['city'];
        
    }

    $stmt->close();
    
    $stmt1 = $con->prepare("SELECT * from model_measurements where model_id = ?");
    $stmt1->bind_param("i", $id);
    $stmt1->execute();
    $measurement_result = $stmt1->get_result();
    if ($measurement_result && $measurement_result->num_rows > 0) {
        $mea = $measurement_result->fetch_assoc();
    }
    $stmt1->close();
    
    $photos = [];
    $stmt2 = $con->prepare("SELECT * from model_photos where model_id = ?");
    $stmt2->bind_param("i", $id);
    $stmt2->execute();
    $photos_result = $stmt2->get_result();
    if ($photos_result && $photos_result->num_rows > 0) {
        while($photo_row = $photos_result->fetch_assoc()){
            $photos[] = $photo_row;
        }
    }
}

?>
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">

        <!-- Toolbar -->
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading text-gray-900 fw-bold fs-3 my-0">
                        Model Profile
                    </h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="<?php echo $site_path; ?>/dashboard" class="text-muted text-hover-primary">Home</a>
                        </li>
                        <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                        <li class="breadcrumb-item text-muted">
                            <a href="<?php echo $site_path; ?>/model-registration" class="text-muted text-hover-primary">Models</a>
                        </li>
                        <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                        <li class="breadcrumb-item text-muted">
                            <?php echo htmlspecialchars($first_name . ' ' . $last_name); ?>
                        </li>
                    </ul>
                </div>
                <!-- Top action buttons -->
                <div class="d-flex gap-3">
                    <a href="<?php echo $site_path; ?>/edit-model?id=<?php echo my_simple_crypt($id, 'encrypt_1'); ?>"
                       class="btn btn-sm btn-light-primary fw-bold">
                        <i class="ki-duotone ki-pencil fs-4 me-1"><span class="path1"></span><span class="path2"></span></i>
                        Edit Profile
                    </a>
                    <a href="<?php echo $site_path; ?>/model-registration" class="btn btn-sm btn-light fw-bold">
                        <i class="ki-duotone ki-arrow-left fs-4 me-1"><span class="path1"></span><span class="path2"></span></i>
                        Back to List
                    </a>
                </div>
            </div>
        </div>
        <!-- /Toolbar -->

        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">

                <style>
                    /* ── View page tokens ──────────────────── */
                    .vp-card          { background:#fff; border:1px solid #E9E9E9; border-radius:12px; margin-bottom:20px; overflow:hidden; }
                    .vp-card-header   { display:flex; align-items:center; justify-content:space-between; padding:16px 24px; border-bottom:1px solid #F1F1F1; }
                    .vp-card-header-left { display:flex; align-items:center; gap:12px; }
                    .vp-section-badge { width:28px; height:28px; border-radius:50%; background:#FEF3EC; color:#C97B4B; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700; flex-shrink:0; }
                    .vp-section-badge.teal { background:#E1F5EE; color:#1D9E75; }
                    .vp-card-title    { font-size:15px; font-weight:600; color:#1a1a1a; }
                    .vp-card-body     { padding:20px 24px; }

                    /* ── Hero card ─────────────────────────── */
                    .hero-card        { background:#fff; border:1px solid #E9E9E9; border-radius:12px; margin-bottom:20px; overflow:hidden; }
                    .hero-cover       { height:120px; background:linear-gradient(135deg,#C97B4B 0%,#8C4E22 100%); position:relative; }
                    .hero-cover-pattern { position:absolute; inset:0; opacity:.08; background-image:repeating-linear-gradient(45deg,#fff 0,#fff 1px,transparent 0,transparent 50%); background-size:12px 12px; }
                    .hero-body        { padding:0 24px 24px; }
                    .hero-avatar-wrap { display:flex; align-items:flex-end; gap:16px; margin-top:-44px; margin-bottom:12px; }
                    .hero-avatar      { width:88px; height:88px; border-radius:50%; border:4px solid #fff; background:#F0EDE8; display:flex; align-items:center; justify-content:center; font-size:28px; font-weight:700; color:#C97B4B; flex-shrink:0; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,.12); }
                    .hero-avatar img  { width:100%; height:100%; object-fit:cover; }
                    .hero-meta        { padding-top:48px; }
                    .hero-name        { font-size:22px; font-weight:700; color:#1a1a1a; line-height:1.2; }
                    .hero-model-id    { font-size:12px; color:#9C9890; font-weight:500; margin-top:2px; }
                    .hero-badges      { display:flex; flex-wrap:wrap; gap:6px; margin-top:8px; }
                    .badge-status     { display:inline-flex; align-items:center; gap:4px; padding:3px 10px; border-radius:999px; font-size:11px; font-weight:600; }
                    .badge-active     { background:#E1F5EE; color:#1D9E75; }
                    .badge-draft      { background:#FFF7E0; color:#B07C00; }
                    .badge-inactive   { background:#F5F5F5; color:#888; }
                    .badge-gender     { background:#F0EDE8; color:#6B5C52; padding:3px 10px; border-radius:999px; font-size:11px; font-weight:500; }
                    .badge-agency     { background:#F0EDE8; color:#6B5C52; padding:3px 10px; border-radius:999px; font-size:11px; font-weight:500; }
                    .hero-stats       { display:grid; grid-template-columns:repeat(auto-fit,minmax(100px,1fr)); gap:0; border-top:1px solid #F1F1F1; margin-top:16px; }
                    .hero-stat        { padding:14px 16px; border-right:1px solid #F1F1F1; text-align:center; }
                    .hero-stat:last-child { border-right:none; }
                    .hero-stat-val    { font-size:18px; font-weight:700; color:#1a1a1a; line-height:1; }
                    .hero-stat-lbl    { font-size:11px; color:#9C9890; margin-top:3px; }

                    /* ── Info grid ─────────────────────────── */
                    .info-grid        { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:0; }
                    .info-item        { padding:14px 0; border-bottom:1px solid #F7F7F7; }
                    .info-item:nth-child(odd)  { padding-right:24px; }
                    .info-item:nth-child(even) { padding-left:24px; border-left:1px solid #F7F7F7; }
                    .info-label       { font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.04em; color:#9C9890; margin-bottom:4px; }
                    .info-value       { font-size:13px; color:#1a1a1a; font-weight:500; word-break:break-word; }
                    .info-value.empty { color:#C5C2BC; font-style:italic; font-weight:400; }
                    .info-value a     { color:#C97B4B; text-decoration:none; }
                    .info-value a:hover { text-decoration:underline; }

                    /* ── Measurement table ─────────────────── */
                    .meas-group-title { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#9C9890; margin:18px 0 10px; padding-bottom:6px; border-bottom:1px solid #F1F1F1; }
                    .meas-group-title:first-child { margin-top:0; }
                    .meas-grid        { display:grid; grid-template-columns:repeat(auto-fill,minmax(160px,1fr)); gap:8px; margin-bottom:4px; }
                    .meas-item        { background:#F9F8F6; border-radius:8px; padding:10px 12px; }
                    .meas-item-label  { font-size:10px; font-weight:600; text-transform:uppercase; letter-spacing:.04em; color:#9C9890; margin-bottom:2px; }
                    .meas-item-value  { font-size:15px; font-weight:700; color:#1a1a1a; }
                    .meas-item-value span { font-size:11px; font-weight:400; color:#9C9890; margin-left:2px; }
                    .meas-item-value.empty { color:#C5C2BC; font-size:13px; font-weight:400; }
                    .unit-pill        { display:inline-flex; align-items:center; padding:3px 10px; border-radius:999px; font-size:11px; font-weight:600; background:#F0EDE8; color:#8C4E22; margin-bottom:14px; }

                    /* ── Style tags ────────────────────────── */
                    .style-tags-view  { display:flex; flex-wrap:wrap; gap:8px; }
                    .style-tag-view   { padding:5px 14px; border-radius:999px; font-size:12px; font-weight:500; background:#FEF3EC; color:#8C4E22; border:1px solid #F5D6BC; }

                    /* ── Media grid ────────────────────────── */
                    .media-photo-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(140px,1fr)); gap:10px; margin-top:4px; }
                    .media-photo-item { border-radius:10px; overflow:hidden; aspect-ratio:3/4; background:#F0EDE8; cursor:pointer; position:relative; }
                    .media-photo-item img { width:100%; height:100%; object-fit:cover; display:block; transition:transform .2s; }
                    .media-photo-item:hover img { transform:scale(1.04); }
                    .media-photo-overlay { position:absolute; inset:0; background:rgba(0,0,0,0); transition:.2s; display:flex; align-items:center; justify-content:center; }
                    .media-photo-item:hover .media-photo-overlay { background:rgba(0,0,0,.18); }
                    .media-photo-overlay i { color:#fff; font-size:24px; opacity:0; transition:.2s; }
                    .media-photo-item:hover .media-photo-overlay i { opacity:1; }
                    .media-video-list { display:flex; flex-direction:column; gap:8px; margin-top:4px; }
                    .media-video-item { display:flex; align-items:center; gap:12px; padding:10px 14px; background:#F9F8F6; border-radius:8px; }
                    .media-video-icon { width:36px; height:36px; background:#FEF3EC; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:18px; }
                    .media-video-name { font-size:13px; font-weight:500; color:#1a1a1a; flex:1; }
                    .media-video-size { font-size:11px; color:#9C9890; }
                    .media-pdf-item   { display:flex; align-items:center; gap:12px; padding:10px 14px; background:#F9F8F6; border-radius:8px; }
                    .media-pdf-icon   { width:36px; height:36px; background:#FDECEA; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:18px; }
                    .media-empty      { padding:24px; text-align:center; color:#C5C2BC; font-size:13px; }

                    /* ── Bank card ─────────────────────────── */
                    .bank-display     { background:linear-gradient(135deg,#2C2A27 0%,#4a3728 100%); border-radius:12px; padding:20px 24px; color:#fff; position:relative; overflow:hidden; }
                    .bank-display::after { content:''; position:absolute; top:-40px; right:-40px; width:160px; height:160px; border-radius:50%; background:rgba(255,255,255,.04); }
                    .bank-display-name { font-size:11px; letter-spacing:.08em; text-transform:uppercase; opacity:.6; margin-bottom:4px; }
                    .bank-display-val  { font-size:15px; font-weight:600; letter-spacing:.02em; }
                    .bank-display-row  { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-top:14px; }
                    .bank-display-upi  { margin-top:14px; padding-top:14px; border-top:1px solid rgba(255,255,255,.1); display:flex; align-items:center; gap:10px; }
                    .upi-label         { font-size:11px; opacity:.6; text-transform:uppercase; letter-spacing:.06em; }
                    .upi-val           { font-size:13px; font-weight:500; }

                    /* ── Commercial ────────────────────────── */
                    .commercial-card   { border:1px solid #E9E9E9; border-radius:10px; padding:16px 20px; }
                    .commercial-type   { font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.06em; color:#9C9890; margin-bottom:6px; }
                    .commercial-amount { font-size:28px; font-weight:700; color:#1a1a1a; line-height:1; }
                    .commercial-amount span { font-size:14px; font-weight:400; color:#9C9890; margin-left:4px; }
                    .commercial-sub    { font-size:12px; color:#9C9890; margin-top:4px; }

                    /* ── Location ──────────────────────────── */
                    .avail-badge       { display:inline-flex; align-items:center; gap:6px; padding:5px 14px; border-radius:999px; font-size:12px; font-weight:600; background:#1a1a1a; color:#fff; }

                    /* ── Notes ─────────────────────────────── */
                    .notes-box         { background:#FAFAF8; border:1px solid #F1F1F1; border-radius:8px; padding:16px; font-size:13px; color:#3d3d3a; line-height:1.7; white-space:pre-wrap; }

                    /* ── Responsive ────────────────────────── */
                    @media(max-width:600px) {
                        .info-item:nth-child(odd)  { padding-right:0; }
                        .info-item:nth-child(even) { padding-left:0; border-left:none; }
                        .bank-display-row { grid-template-columns:1fr; }
                        .hero-stats { grid-template-columns:1fr 1fr; }
                        .hero-stat  { border-bottom:1px solid #F1F1F1; }
                    }
                </style>

                <?php
                // ── Helpers ────────────────────────────────────────────────
                function vval($v, $fallback = '—') {
                    return (!empty($v)) ? htmlspecialchars($v) : '<span class="empty">' . $fallback . '</span>';
                }
                function vmeas($v, $unit) {
                    if ($v === null || $v === '') {
                        return '<span class="empty">—</span>';
                    }
                    $u = $unit === 'cm' ? 'cm' : '"';
                    return htmlspecialchars($v) . '<span>' . $u . '</span>';
                }
                ?>

                <div class="vp-card">
                    <div class="vp-card-header">
                        <div class="vp-card-header-left">
                            <div class="vp-section-badge">1</div>
                            <span class="vp-card-title">Basic Information</span>
                        </div>
                    </div>
                    <div class="vp-card-body">
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-label">First Name</div>
                                <div class="info-value"><?php echo vval($first_name); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Last Name</div>
                                <div class="info-value"><?php echo vval($last_name); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Date of Birth</div>
                                <div class="info-value">
                                    <?php echo !empty($row['dob']) ? date('d M Y', strtotime($row['dob'])) : '<span class="empty">—</span>'; ?>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Age</div>
                                <div class="info-value"><?php echo vval($row['age']); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Gender</div>
                                <div class="info-value"><?php echo vval($row['gender']); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Model ID</div>
                                <div class="info-value"><?php echo vval($row['model_id']); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Agency / Division</div>
                                <div class="info-value"><?php echo vval($row['agency_division']); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Registered On</div>
                                <div class="info-value">
                                    <?php echo !empty($row['created_at']) ? date('d M Y', strtotime($row['created_at'])) : '<span class="empty">—</span>'; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ══════════════════════════════════════════
                     SECTION 2 · CONTACT DETAILS
                ═══════════════════════════════════════════ -->
                <div class="vp-card">
                    <div class="vp-card-header">
                        <div class="vp-card-header-left">
                            <div class="vp-section-badge">2</div>
                            <span class="vp-card-title">Contact Details</span>
                        </div>
                    </div>
                    <div class="vp-card-body">
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-label">Mobile (Primary)</div>
                                <div class="info-value">
                                    <?php if (!empty($row['mobile_primary'])): ?>
                                        <a href="tel:<?php echo htmlspecialchars($row['mobile_primary']); ?>"><?php echo htmlspecialchars($row['mobile_primary']); ?></a>
                                    <?php else: echo '<span class="empty">—</span>'; endif; ?>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Mobile (Alternate)</div>
                                <div class="info-value">
                                    <?php if (!empty($row['mobile_alternate'])): ?>
                                        <a href="tel:<?php echo htmlspecialchars($row['mobile_alternate']); ?>"><?php echo htmlspecialchars($row['mobile_alternate']); ?></a>
                                    <?php else: echo '<span class="empty">—</span>'; endif; ?>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Email</div>
                                <div class="info-value">
                                    <?php if (!empty($row['email'])): ?>
                                        <a href="mailto:<?php echo htmlspecialchars($row['email']); ?>"><?php echo htmlspecialchars($row['email']); ?></a>
                                    <?php else: echo '<span class="empty">—</span>'; endif; ?>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Instagram</div>
                                <div class="info-value">
                                    <?php if (!empty($row['instagram'])): ?>
                                        <a href="https://instagram.com/<?php echo ltrim(htmlspecialchars($row['instagram']),'@'); ?>" target="_blank"><?php echo htmlspecialchars($row['instagram']); ?></a>
                                    <?php else: echo '<span class="empty">—</span>'; endif; ?>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">YouTube Channel</div>
                                <div class="info-value">
                                    <?php if (!empty($row['youtube_channel_id'])): ?>
                                        <a href="<?php echo htmlspecialchars($row['youtube_channel_id']); ?>" target="_blank"><?php echo htmlspecialchars($row['youtube_channel_id']); ?></a>
                                    <?php else: echo '<span class="empty">—</span>'; endif; ?>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">PIN Code</div>
                                <div class="info-value"><?php echo vval($row['pincode']); ?></div>
                            </div>
                        </div>
                        <?php if (!empty($row['address']) || !empty($row['city']) || !empty($row['state'])): ?>
                        <div style="margin-top:16px;padding-top:16px;border-top:1px solid #F7F7F7;">
                            <div class="info-label" style="margin-bottom:6px;">Residential Address</div>
                            <div class="info-value" style="line-height:1.7;">
                                <?php
                                $addrParts = array_filter([
                                    $row['address'] ?? '',
                                    $row['city']    ?? '',
                                    $row['state']   ?? '',
                                    $row['pincode'] ?? '',
                                ]);
                                echo htmlspecialchars(implode(', ', $addrParts));
                                ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- ══════════════════════════════════════════
                     SECTION 3 · IDENTITY DOCUMENTS
                ═══════════════════════════════════════════ -->
                <div class="vp-card">
                    <div class="vp-card-header">
                        <div class="vp-card-header-left">
                            <div class="vp-section-badge">3</div>
                            <span class="vp-card-title">Identity Documents</span>
                        </div>
                        <span class="badge badge-light-warning fw-bold fs-8">Optional</span>
                    </div>
                    <div class="vp-card-body">
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-label">PAN Number</div>
                                <div class="info-value">
                                    <?php echo !empty($row['pan_number'])
                                        ? '<span style="letter-spacing:.08em;font-family:monospace;">' . htmlspecialchars($row['pan_number']) . '</span>'
                                        : '<span class="empty">—</span>'; ?>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Aadhaar Number</div>
                                <div class="info-value">
                                    <?php if (!empty($row['aadhaar_number'])): ?>
                                        <span style="letter-spacing:.08em;font-family:monospace;">
                                            XXXX XXXX <?php echo substr(htmlspecialchars($row['aadhaar_number']), -4); ?>
                                        </span>
                                    <?php else: echo '<span class="empty">—</span>'; endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ══════════════════════════════════════════
                     SECTION 4 · BANK DETAILS
                ═══════════════════════════════════════════ -->
                <div class="vp-card">
                    <div class="vp-card-header">
                        <div class="vp-card-header-left">
                            <div class="vp-section-badge">4</div>
                            <span class="vp-card-title">Bank Details</span>
                        </div>
                    </div>
                    <div class="vp-card-body">
                        <?php if (!empty($row['bank_name']) || !empty($row['account_number'])): ?>
                        <div class="bank-display mb-4">
                            <div class="bank-display-name">Bank Name</div>
                            <div class="bank-display-val"><?php echo htmlspecialchars($row['bank_name'] ?? '—'); ?></div>
                            <div class="bank-display-row">
                                <div>
                                    <div class="bank-display-name">Account Holder</div>
                                    <div class="bank-display-val"><?php echo htmlspecialchars($row['account_holder_name'] ?? '—'); ?></div>
                                </div>
                                <div>
                                    <div class="bank-display-name">Account Number</div>
                                    <div class="bank-display-val">
                                        <?php if (!empty($row['account_number'])): ?>
                                            XXXX XXXX <?php echo substr(htmlspecialchars($row['account_number']), -4); ?>
                                        <?php else: echo '—'; endif; ?>
                                    </div>
                                </div>
                                <div>
                                    <div class="bank-display-name">IFSC Code</div>
                                    <div class="bank-display-val" style="letter-spacing:.06em;"><?php echo htmlspecialchars($row['ifsc_code'] ?? '—'); ?></div>
                                </div>
                                <div>
                                    <div class="bank-display-name">Branch · State</div>
                                    <div class="bank-display-val"><?php echo htmlspecialchars(($row['branch'] ?? '') . ', ' . ($row['bank_state'] ?? '')); ?></div>
                                </div>
                            </div>
                            <?php if (!empty($row['upi_id'])): ?>
                            <div class="bank-display-upi">
                                <div>
                                    <div class="upi-label">UPI ID</div>
                                    <div class="upi-val"><?php echo htmlspecialchars($row['upi_id']); ?></div>
                                </div>
                                <?php if (!empty($m['qr_code_url'])): ?>
                                <img src="<?php echo htmlspecialchars($m['qr_code_url']); ?>" alt="QR Code"
                                     style="width:56px;height:56px;border-radius:6px;background:#fff;padding:4px;margin-left:auto;"/>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php else: ?>
                        <p style="color:#C5C2BC;font-size:13px;text-align:center;padding:24px 0;">No bank details added yet.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- ══════════════════════════════════════════
                     SECTION 5 · COMMERCIALS
                ═══════════════════════════════════════════ -->
                <div class="vp-card">
                    <div class="vp-card-header">
                        <div class="vp-card-header-left">
                            <div class="vp-section-badge">5</div>
                            <span class="vp-card-title">Commercials</span>
                        </div>
                    </div>
                    <div class="vp-card-body">
                        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px;">
                            <div class="commercial-card">
                                <div class="commercial-type">Rate Type</div>
                                <div class="commercial-amount" style="font-size:16px;">
                                    <?php echo $row['rate_type'] === 'per_day' ? 'Per Day' : ($row['rate_type'] === 'per_outfit' ? 'Per Outfit' : '—'); ?>
                                </div>
                            </div>
                            <?php if ($row['rate_type'] === 'per_day' && !empty($row['amount_per_day'])): ?>
                            <div class="commercial-card">
                                <div class="commercial-type">Amount per Day</div>
                                <div class="commercial-amount">₹<?php echo number_format($row['amount_per_day'], 0); ?><span>/ day</span></div>
                                <?php if (!empty($row['per_day_max_outfit'])): ?>
                                <div class="commercial-sub">Max <?php echo htmlspecialchars($row['per_day_max_outfit']); ?> outfits/day</div>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                            <?php if ($m['rate_type'] === 'per_outfit' && !empty($m['amount_per_outfit'])): ?>
                            <div class="commercial-card">
                                <div class="commercial-type">Amount per Outfit</div>
                                <div class="commercial-amount">₹<?php echo number_format($m['amount_per_outfit'], 0); ?><span>/ outfit</span></div>
                                <?php if (!empty($m['max_outfits_day'])): ?>
                                <div class="commercial-sub">Max <?php echo htmlspecialchars($m['max_outfits_day']); ?> outfits/day</div>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- ══════════════════════════════════════════
                     SECTION 6 · LOCATION FLEXIBILITY
                ═══════════════════════════════════════════ -->
                <div class="vp-card">
                    <div class="vp-card-header">
                        <div class="vp-card-header-left">
                            <div class="vp-section-badge">6</div>
                            <span class="vp-card-title">Location Flexibility</span>
                        </div>
                    </div>
                    <div class="vp-card-body">
                        <?php
                        $availLabels = [
                            'pan_india'     => 'Pan-India (Flexible)',
                            'hometown_only' => 'Hometown Only',
                            'both'          => 'Both',
                        ];
                        $availLabel = $availLabels[$row['availability'] ?? ''] ?? ($row['availability'] ?? '—');
                        ?>
                        <div style="margin-bottom:16px;">
                            <div class="info-label" style="margin-bottom:8px;">Availability</div>
                            <span class="avail-badge"><?php echo htmlspecialchars($availLabel); ?></span>
                        </div>
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-label">Hometown / Base City</div>
                                <div class="info-value"><?php echo vval($row['hometown_city']); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Travel Notes</div>
                                <div class="info-value"><?php echo vval($row['travel_notes']); ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ══════════════════════════════════════════
                     SECTION 7 · BODY MEASUREMENTS
                ═══════════════════════════════════════════ -->
                <div class="vp-card">
                    <div class="vp-card-header">
                        <div class="vp-card-header-left">
                            <div class="vp-section-badge teal">7</div>
                            <span class="vp-card-title">Body Measurements</span>
                        </div>
                    </div>
                    <div class="vp-card-body">
                        <div class="unit-pill">📐 Unit: <?php echo $mea['measurement_unit'];$unit = ''; ?></div>

                        <div class="meas-group-title">Circumferences</div>
                        <div class="meas-grid">
                            <?php
                            $circum = [
                                'A · Neck'          => 'neck',
                                '1 · Bust / Chest'  => 'bust_chest',
                                'C · Under Bust'    => 'under_bust',
                                '2 · Natural Waist' => 'natural_waist',
                                'D · High Hip'      => 'high_hip',
                                '3 · Full Hip'      => 'full_hip',
                                'E · Bicep'         => 'bicep',
                                'F · Wrist'         => 'wrist',
                                'G · Thigh'         => 'thigh',
                                'H · Knee'          => 'knee',
                                'I · Calf'          => 'calf',
                            ];
                            foreach ($circum as $label => $key): ?>
                            <div class="meas-item">
                                <div class="meas-item-label"><?php echo $label; ?></div>
                                <div class="meas-item-value <?php echo empty($mea[$key]) ? 'empty' : ''; ?>">
                                    <?php echo vmeas($mea[$key] ?? null, $unit); ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="meas-group-title">Shoulder</div>
                        <div class="meas-grid">
                            <?php
                            $shoulder = [
                                'B · Shoulder Width'   => 'shoulder_width',
                                '5 · Shoulder End-End' => 'shoulder_end',
                                'J · Across Back'      => 'across_back',
                            ];
                            foreach ($shoulder as $label => $key): ?>
                            <div class="meas-item">
                                <div class="meas-item-label"><?php echo $label; ?></div>
                                <div class="meas-item-value <?php echo empty($mea[$key]) ? 'empty' : ''; ?>">
                                    <?php echo vmeas($mea[$key] ?? null, $unit); ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="meas-group-title">Lengths — Front</div>
                        <div class="meas-grid">
                            <?php
                            $front = [
                                '4 · Bust Apex Height'   => 'bust_apex_height',
                                '6 · Neck-Waist (Front)' => 'neck_waist_front',
                                'K · Neck-Waist (Back)'  => 'neck_waist_back',
                                '7 · Back Waist Length'  => 'back_waist_length',
                                '8 · Waist to Length'    => 'waist_to_length',
                                'L · Back Hip (Seat)'    => 'back_hip_seat',
                            ];
                            foreach ($front as $label => $key): ?>
                            <div class="meas-item">
                                <div class="meas-item-label"><?php echo $label; ?></div>
                                <div class="meas-item-value <?php echo empty($mea[$key]) ? 'empty' : ''; ?>">
                                    <?php echo vmeas($mea[$key] ?? null, $unit); ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="meas-group-title">Lengths — Lower Body</div>
                        <div class="meas-grid">
                            <?php
                            $lower = [
                                '9 · Crotch Length' => 'crotch_length',
                                'M · Inseam'        => 'inseam',
                                '10 · Arm / Sleeve' => 'sleeve_length',
                                'P · Total Height'  => 'height',
                            ];
                            foreach ($lower as $label => $key): ?>
                            <div class="meas-item">
                                <div class="meas-item-label"><?php echo $label; ?></div>
                                <div class="meas-item-value <?php echo empty($mea[$key]) ? 'empty' : ''; ?>">
                                    <?php echo vmeas($mea[$key] ?? null, $unit); ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="meas-group-title">General</div>
                        <div class="meas-grid">
                            <div class="meas-item">
                                <div class="meas-item-label">Dress / Size Label</div>
                                <div class="meas-item-value <?php echo empty($mea['dress_size']) ? 'empty' : ''; ?>">
                                    <?php echo !empty($mea['dress_size']) ? htmlspecialchars($mea['dress_size']) : '—'; ?>
                                </div>
                            </div>
                            <div class="meas-item">
                                <div class="meas-item-label">Posture Type</div>
                                <div class="meas-item-value <?php echo empty($mea['posture']) ? 'empty' : ''; ?>">
                                    <?php echo !empty($mea['posture']) ? htmlspecialchars($mea['posture']) : '—'; ?>
                                </div>
                            </div>
                            <div class="meas-item">
                                <div class="meas-item-label">Weight</div>
                                <div class="meas-item-value <?php echo empty($row['weight']) ? 'empty' : ''; ?>">
                                    <?php echo !empty($row['weight']) ? htmlspecialchars($row['weight']) . '<span>kg</span>' : '—'; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ══════════════════════════════════════════
                     SECTION 8 · OUTFIT STYLE TAGS
                ═══════════════════════════════════════════ -->
                <div class="vp-card">
                    <div class="vp-card-header">
                        <div class="vp-card-header-left">
                            <div class="vp-section-badge teal">8</div>
                            <span class="vp-card-title">Outfit Style Tags</span>
                        </div>
                        <span style="font-size:12px;color:#9C9890;"><?php echo count($style_tags ?? []); ?> tags</span>
                    </div>
                    <div class="vp-card-body">
                        <?php if (!empty($style_tags)): ?>
                        <div class="style-tags-view">
                            <?php foreach ($style_tags as $tag): ?>
                                <span class="style-tag-view"><?php echo htmlspecialchars($tag['name']); ?></span>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <p style="color:#C5C2BC;font-size:13px;text-align:center;padding:16px 0;">No style tags selected.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- ══════════════════════════════════════════
                     SECTION 9 · PROFILE MEDIA
                ═══════════════════════════════════════════ -->
                <div class="vp-card">
                    <div class="vp-card-header">
                        <div class="vp-card-header-left">
                            <div class="vp-section-badge">9</div>
                            <span class="vp-card-title">Profile Media</span>
                        </div>
                    </div>
                    <div class="vp-card-body">

                        <!-- Photos -->
                        <div style="margin-bottom:24px;">
                            <div class="info-label" style="margin-bottom:10px;">
                                Profile Photos
                                <span style="font-weight:400;color:#C5C2BC;margin-left:4px;">(<?php echo count($photos ?? []); ?>/10)</span>
                            </div>
                            <?php if (!empty($photos)): ?>
                            <div class="media-photo-grid" id="photoLightboxGrid">
                                <?php foreach ($photos as $i => $photo):?>
                                <div class="media-photo-item" onclick="openLightbox(<?php echo $i; ?>)">
                                    <img src="<?php echo $define_company_website."/uploads/model/".htmlspecialchars($photo['image_path']); ?>" alt="Photo" loading="lazy"/>
                                    <div class="media-photo-overlay">
                                        <i class="ki-duotone ki-eye fs-2x"><span class="path1"></span><span class="path2"></span></i>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php else: ?>
                            <div class="media-empty">No photos uploaded yet.</div>
                            <?php endif; ?>
                        </div>

                        <!-- Videos -->
                        <div style="margin-bottom:24px;">
                            <div class="info-label" style="margin-bottom:10px;">
                                Profile Videos / Reels
                                <span style="font-weight:400;color:#C5C2BC;margin-left:4px;">(<?php echo count($videos ?? []); ?>/3)</span>
                            </div>
                            <?php if (!empty($videos)): ?>
                            <div class="media-video-list">
                                <?php foreach ($videos as $video): ?>
                                <div class="media-video-item">
                                    <div class="media-video-icon">🎬</div>
                                    <div class="media-video-name"><?php echo htmlspecialchars($video['file_name'] ?? 'Video'); ?></div>
                                    <div class="media-video-size"><?php echo !empty($video['file_size_kb']) ? round($video['file_size_kb']/1024, 1) . ' MB' : ''; ?></div>
                                    <a href="<?php echo htmlspecialchars($video['file_url']); ?>" target="_blank"
                                       class="btn btn-sm btn-light-primary">View</a>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php else: ?>
                            <div class="media-empty">No videos uploaded yet.</div>
                            <?php endif; ?>
                        </div>

                        <!-- PDFs -->
                        <div>
                            <div class="info-label" style="margin-bottom:10px;">
                                Documents (PDF)
                                <span style="font-weight:400;color:#C5C2BC;margin-left:4px;">(<?php echo count($pdfs ?? []); ?>/5)</span>
                            </div>
                            <?php if (!empty($pdfs)): ?>
                            <div class="media-video-list">
                                <?php foreach ($pdfs as $pdf): ?>
                                <div class="media-pdf-item">
                                    <div class="media-pdf-icon">📄</div>
                                    <div class="media-video-name"><?php echo htmlspecialchars($pdf['file_name'] ?? 'Document'); ?></div>
                                    <div class="media-video-size"><?php echo !empty($pdf['file_size_kb']) ? round($pdf['file_size_kb']/1024, 1) . ' MB' : ''; ?></div>
                                    <a href="<?php echo htmlspecialchars($pdf['file_url']); ?>" target="_blank"
                                       class="btn btn-sm btn-light-danger">View PDF</a>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php else: ?>
                            <div class="media-empty">No documents uploaded yet.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- ══════════════════════════════════════════
                     SECTION 10 · REMARKS / NOTES
                ═══════════════════════════════════════════ -->
                <div class="vp-card">
                    <div class="vp-card-header">
                        <div class="vp-card-header-left">
                            <div class="vp-section-badge">10</div>
                            <span class="vp-card-title">Remarks / Notes</span>
                        </div>
                        <span class="badge badge-light-warning fw-bold fs-8">Optional</span>
                    </div>
                    <div class="vp-card-body">
                        <?php if (!empty($row['internal_notes'])): ?>
                        <div class="notes-box"><?php echo htmlspecialchars($row['internal_notes']); ?></div>
                        <?php else: ?>
                        <p style="color:#C5C2BC;font-size:13px;text-align:center;padding:16px 0;">No internal notes added.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Bottom actions -->
                <div style="display:flex;justify-content:space-between;align-items:center;padding:16px 0 40px;">
                    <a href="<?php echo $site_path; ?>/pages/models" class="btn btn-light fw-bold">
                        ← Back to Model List
                    </a>
                    <a href="<?php echo $site_path; ?>/pages/models/edit?id=<?php echo my_simple_crypt($model['id'], 'encrypt_1'); ?>"
                       class="btn btn-primary fw-bold">
                        Edit Profile
                    </a>
                </div>

            </div><!-- /container -->
        </div><!-- /content -->

        <?php include("includes/footer.php"); ?>
    </div>
</div>

<!-- ══════════════════════════════════════════
     PHOTO LIGHTBOX
═══════════════════════════════════════════ -->
<div id="lightbox" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.9);z-index:9999;align-items:center;justify-content:center;flex-direction:column;">
    <button onclick="closeLightbox()" style="position:absolute;top:20px;right:24px;background:none;border:none;color:#fff;font-size:32px;cursor:pointer;line-height:1;">×</button>
    <button onclick="prevPhoto()" style="position:absolute;left:16px;top:50%;transform:translateY(-50%);background:rgba(255,255,255,.15);border:none;color:#fff;font-size:24px;width:44px;height:44px;border-radius:50%;cursor:pointer;">‹</button>
    <img id="lightboxImg" src="" alt="" style="max-width:90vw;max-height:88vh;border-radius:8px;object-fit:contain;"/>
    <div id="lightboxCaption" style="color:rgba(255,255,255,.5);font-size:13px;margin-top:12px;"></div>
    <button onclick="nextPhoto()" style="position:absolute;right:16px;top:50%;transform:translateY(-50%);background:rgba(255,255,255,.15);border:none;color:#fff;font-size:24px;width:44px;height:44px;border-radius:50%;cursor:pointer;">›</button>
</div>
<script>var hostUrl = "<?php echo $site_path; ?>/";</script>
<script src="<?php echo $site_path; ?>/assets/plugins/global/plugins.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/scripts.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/custom/create-task.js?v=<?php echo time(); ?>"></script>
<script>
// ── Lightbox ────────────────────────────────────────────
const photoUrls = <?php echo json_encode(array_column($photos ?? [], 'file_url')); ?>;
let lbIndex = 0;

function openLightbox(i) {
    lbIndex = i;
    showLbPhoto();
    document.getElementById('lightbox').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closeLightbox() {
    document.getElementById('lightbox').style.display = 'none';
    document.body.style.overflow = '';
}
function showLbPhoto() {
    document.getElementById('lightboxImg').src = photoUrls[lbIndex];
    document.getElementById('lightboxCaption').textContent = (lbIndex + 1) + ' / ' + photoUrls.length;
}
function prevPhoto() { lbIndex = (lbIndex - 1 + photoUrls.length) % photoUrls.length; showLbPhoto(); }
function nextPhoto() { lbIndex = (lbIndex + 1) % photoUrls.length; showLbPhoto(); }

document.getElementById('lightbox').addEventListener('click', function(e) {
    if (e.target === this) closeLightbox();
});
document.addEventListener('keydown', function(e) {
    const lb = document.getElementById('lightbox');
    if (lb.style.display !== 'flex') return;
    if (e.key === 'ArrowLeft')  prevPhoto();
    if (e.key === 'ArrowRight') nextPhoto();
    if (e.key === 'Escape')     closeLightbox();
});
</script>