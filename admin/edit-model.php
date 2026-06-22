<?php
include("config/database.php");
include("config/auth_check.php");
include("includes/sidemenu.php");
$outfit_style_tag = get_all_tag_list();
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
    
    $stmt1 = $con->prepare("SELECT * FROM model_measurements WHERE model_id = ?");
    $stmt1->bind_param("i", $id);
    $stmt1->execute();
    
    $mes_result = $stmt1->get_result();
    if ($mes_result && $mes_result->num_rows > 0) {
        $mes_row = $mes_result->fetch_assoc();
    }
    
    $stmt2 = $con->prepare("SELECT id, image_path FROM model_photos WHERE model_id = ?");

    $stmt2->bind_param("i", $id);
    $stmt2->execute();

    $photos = $stmt2->get_result();
}
?>
<style>
     .style-tags-wrapper{
    padding:20px;
}

.style-tags{
    display:flex;
    flex-wrap:wrap;
    gap:10px;
    margin-bottom:20px;
}

.style-tag{
    border:1px solid #d8c7b8;
    background:#fff;
    color:#6b4a2e;
    padding:8px 18px;
    border-radius:25px;
    cursor:pointer;
    font-size:14px;
    transition:all .2s ease;
}

.style-tag:hover{
    border-color:#c79b67;
}

.style-tag.active{
    background:#c79b67;
    border-color:#c79b67;
    color:#fff;
    font-weight:600;
}

.style-tag.active::before{
    content:"✓ ";
    font-weight:bold;
}

.tag-add-row{
    display:flex;
    gap:10px;
    align-items:center;
}

.tag-add-row input{
    flex:1;
}

#addTagBtn{
    min-width:120px;
}
    *, *::before, *::after {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    :root {
        --brand:       #C97B4B;
        --brand-light: #F5EDE4;
        --brand-dark:  #8C4E22;
        --teal:        #1D9E75;
        --teal-light:  #E1F5EE;
        --bg:          #F7F5F2;
        --surface:     #FFFFFF;
        --border:      #E2DDD7;
        --border-focus:#C97B4B;
        --text:        #2C2A27;
        --text-2:      #6B6660;
        --text-3:      #9C9890;
        --danger:      red;
        --danger-bg:   #FDECEA;
        --success:     #1D9E75;
        --radius:      10px;
        --radius-sm:   6px;
        --shadow:      0 1px 3px rgba(0,0,0,.07), 0 4px 16px rgba(0,0,0,.04);
    }

    /* ── Top bar ── */
    .topbar {
        background: var(--surface);
        border-bottom: 1px solid var(--border);
        padding: 0 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 56px;
        position: sticky;
        top: 0;
        z-index: 100;
    }
    .topbar-logo {
        font-size: 17px;
        font-weight: 700;
        color: var(--brand);
        letter-spacing: -.3px;
    }
    .topbar-logo span {
        color: var(--text-2);
        font-weight: 400;
    }
    .topbar-right {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    /* ── Page layout ── */
    .page {
        max-width: 860px;
        margin: 32px auto;
        padding: 0 16px;
    }

    .page-title {
        font-size: 22px;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 4px;
    }
    .page-sub   {
        color: var(--text-2);
        margin-bottom: 28px;
        font-size: 13px;
    }

    /* ── Progress stepper ── */
    .stepper {
        display: flex;
        gap: 0;
        margin-bottom: 32px;
        overflow-x: auto;
        padding-bottom: 4px;
    }
    .step {
        display: flex;
        align-items: center;
        gap: 6px;
        flex: 1;
        min-width: 80px;
        cursor: pointer;
    }
    .step-num {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 700;
        flex-shrink: 0;
        background: #E2DDD7;
        color: var(--text-2);
        transition: .2s;
    }
    .step-label {
        font-size: 11px;
        color: var(--text-3);
        white-space: nowrap;
    }
    .step.active .step-num   {
        background: var(--brand);
        color: #fff;
    }
    .step.active .step-label {
        color: var(--brand);
        font-weight: 600;
    }
    .step.done .step-num     {
        background: var(--teal);
        color: #fff;
    }
    .step.done .step-label   {
        color: var(--teal);
    }
    .step-line {
        height: 2px;
        flex: 1;
        background: #E2DDD7;
        margin: 0 4px;
        align-self: center;
        min-width: 12px;
        transition: .2s;
    }
    .step.done + .step-line  {
        background: var(--teal);
    }

    /* ── Card / Section ── */
    .card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        margin-bottom: 20px;
        overflow: hidden;
    }
    .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px 24px 14px;
        border-bottom: 1px solid var(--border);
    }
    .card-header-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .section-badge {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: var(--brand-light);
        color: var(--brand);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
        flex-shrink: 0;
    }
    .section-badge.teal {
        background: var(--teal-light);
        color: var(--teal);
    }
    .card-title  {
        font-size: 15px;
        font-weight: 600;
    }
    .badge-req   {
        font-size: 11px;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 4px;
        background: #FEF3EC;
        color: var(--brand);
        border: 1px solid #F5D6BC;
    }
    .badge-opt   {
        font-size: 11px;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 4px;
        background: var(--teal-light);
        color: var(--teal);
        border: 1px solid #A8DECE;
    }
    .card-body {
        padding: 20px 24px;
    }

    #regForm > .card {
        border: 1px solid var(--border);
        border-radius: 8px;
        box-shadow: none;
        overflow: hidden;
    }
    #regForm > .card .card-header {
        min-height: 56px;
        padding: 0 24px;
        border-bottom-color: #E8DFD6;
    }
    #regForm > .card .card-header-left {
        gap: 12px;
    }
    #regForm > .card .section-badge {
        width: 29px;
        height: 29px;
        font-size: 12px;
    }
    #regForm > .card .card-title {
        color: #000;
        font-size: 16px;
        font-weight: 700;
    }
    #regForm > .card .badge-req,
    #regForm > .card .badge-opt {
        align-self: center;
        border-radius: 999px;
        padding: 3px 10px;
    }
    #regForm > .card .card-body {
        padding: 15px 24px 18px;
    }

    /* ── Grid ── */
    .row      {
        display: grid;
        gap: 16px;
        margin-bottom: 16px;
    }
    .row:last-child {
        margin-bottom: 0;
    }
    .col-2    {
        grid-template-columns: 1fr 1fr;
    }
    .col-3    {
        grid-template-columns: 1fr 1fr 1fr;
    }
    .col-1-2  {
        grid-template-columns: 1fr 2fr;
    }
    .col-2-1  {
        grid-template-columns: 2fr 1fr;
    }
    .col-full {
        grid-template-columns: 1fr;
    }

    #regForm .row {
        display: grid !important;
        width: 100% !important;
        max-width: none !important;
        flex: none !important;
        gap: 11px;
        margin-bottom: 10px;
        margin-left: 0 !important;
        margin-right: 0 !important;
        --bs-gutter-x: 0;
        --bs-gutter-y: 0;
    }
    #regForm .row > * {
        width: 100% !important;
        max-width: none !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
    }
    #regForm .row.col-2 {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    }
    #regForm .row.col-3 {
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    }
    #regForm .row.col-1-2 {
        grid-template-columns: minmax(0, 1fr) minmax(0, 2fr) !important;
    }
    #regForm .row.col-2-1 {
        grid-template-columns: minmax(0, 2fr) minmax(0, 1fr) !important;
    }
    #regForm .row.col-full {
        grid-template-columns: minmax(0, 1fr) !important;
    }

    @media (max-width: 600px) {
        .col-2, .col-3, .col-1-2, .col-2-1,
        #regForm .row.col-2,
        #regForm .row.col-3,
        #regForm .row.col-1-2,
        #regForm .row.col-2-1 {
            grid-template-columns: 1fr !important;
        }
    }

    /* ── Form controls ── */
    .field {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }
    label  {
        font-size: 11px;
        font-weight: 600;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: var(--text-2);
    }
    label .req {
        color: var(--danger);
        margin-left: 2px;
    }

    input[type=text], input[type=email], input[type=tel],
    input[type=number], input[type=date], input[type=password],
    select, textarea {
        width: 100%;
        padding: 9px 12px;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        font-size: 13px;
        color: var(--text);
        background: var(--surface);
        outline: none;
        transition: border-color .15s, box-shadow .15s;
        font-family: inherit;
        -webkit-appearance: none;
    }
    input:focus, select:focus, textarea:focus {
        border-color: var(--border-focus);
        box-shadow: 0 0 0 3px rgba(201,123,75,.12);
    }
    input::placeholder, textarea::placeholder {
        color: var(--text-3);
    }
    input[readonly], input[disabled] {
        background: #F7F5F2;
        color: var(--text-3);
        cursor: not-allowed;
    }
    textarea {
        resize: vertical;
        min-height: 80px;
    }
    select   {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%239C9890' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        padding-right: 32px;
    }

    #regForm input[type=text],
    #regForm input[type=email],
    #regForm input[type=tel],
    #regForm input[type=number],
    #regForm input[type=date],
    #regForm input[type=password],
    #regForm select {
        height: 36px;
        padding: 0 12px;
        font-size: 14px;
        border-color: #DED1C6;
        border-radius: 6px;
        background-color: #FFFDFB;
    }
    #regForm textarea {
        padding: 8px 12px;
        font-size: 14px;
        border-color: #DED1C6;
        border-radius: 6px;
        background-color: #FFFDFB;
        min-height: 70px;
    }

    .field-hint {
        font-size: 11px;
        color: var(--text-3);
    }
    .field-error {
        font-size: 11px;
        color: var(--danger);
        display: none;
    }
    .field.has-error input,
    .field.has-error select,
    .field.has-error textarea {
        border-color: var(--danger);
    }
    .field.has-error .field-error {
        display: block;
    }

    /* ── Basic information layout ── */
    #sec1.card {
        border: 1px solid var(--border);
        border-radius: 8px;
        box-shadow: none;
        overflow: hidden;
    }
    #sec1 .card-header {
        min-height: 56px;
        padding: 0 24px;
        border-bottom-color: #E8DFD6;
    }
    #sec1 .card-header-left {
        gap: 12px;
    }
    #sec1 .section-badge {
        width: 29px;
        height: 29px;
        font-size: 12px;
    }
    #sec1 .card-title {
        color: #000;
        font-size: 16px;
        font-weight: 700;
    }
    #sec1 .badge-req {
        align-self: center;
        width: auto;
        border-radius: 999px;
        padding: 3px 10px;
        background: #FFF1E8;
    }
    #sec1 .card-body {
        padding: 15px 24px 18px;
    }
    #sec1 .row {
        display: grid !important;
        width: 100% !important;
        max-width: none !important;
        flex: none !important;
        align-items: start;
        gap: 11px;
        margin-bottom: 10px;
        --bs-gutter-x: 0;
        --bs-gutter-y: 0;
    }
    #sec1 .col-2 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    #sec1 .col-3 {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
    #sec1 .field {
        gap: 5px;
        min-width: 0;
        width: 100% !important;
        max-width: none !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
    }
    #sec1 label {
        color: #5F4330;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .04em;
        line-height: 1.1;
    }
    #sec1 label span:not(.req) {
        color: var(--text-2);
        font-size: 11px;
        font-weight: 500;
        letter-spacing: 0;
    }
    #sec1 input[type=text],
    #sec1 input[type=date],
    #sec1 select {
        height: 36px;
        padding: 0 12px;
        font-size: 14px;
        border-color: #DED1C6;
        border-radius: 6px;
        background-color: #FFFDFB;
    }
    #sec1 #age {
        text-align: left;
    }
    #sec1 .field-error,
    #sec1 .field-hint {
        color: var(--danger);
        font-size: 12px;
        line-height: 1.35;
    }

    @media (max-width: 600px) {
        #sec1 .card-header {
            padding-left: 18px;
        }
        #sec1 .card-title {
            font-size: 15px;
        }
        #sec1 .card-body {
            padding: 16px;
        }
        #sec1 .row,
        #sec1 .col-2,
        #sec1 .col-3 {
            grid-template-columns: minmax(0, 1fr);
            gap: 14px;
        }
        #sec1 input[type=text],
        #sec1 input[type=date],
        #sec1 select {
            width: 100%;
        }
    }

    /* ── Unit toggle ── */
    .unit-toggle {
        display: inline-flex;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        overflow: hidden;
        margin-bottom: 16px;
    }
    .unit-toggle button {
        padding: 6px 18px;
        border: none;
        background: transparent;
        font-size: 13px;
        font-weight: 500;
        color: var(--text-2);
        cursor: pointer;
        transition: .15s;
    }
    .unit-toggle button.active {
        background: var(--brand);
        color: #fff;
    }

    /* ── Measurement sections ── */
    .meas-section-label {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
        color: var(--text-3);
        margin: 18px 0 10px;
        padding-bottom: 6px;
        border-bottom: 1px solid var(--border);
    }
    .meas-section-label:first-child {
        margin-top: 0;
    }

    /* ── Style tags ── */
    .tag-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 14px;
    }
    .style-tag {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border: 1px solid var(--border);
        border-radius: 999px;
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
        background: var(--surface);
        color: var(--text-2);
        transition: .15s;
        user-select: none;
    }
    .style-tag:hover {
        border-color: var(--brand);
        color: var(--brand);
    }
    .style-tag.selected {
        background: var(--brand-light);
        border-color: var(--brand);
        color: var(--brand-dark);
    }
    .style-tag input {
        display: none;
    }
    .custom-tag-row {
        display: flex;
        gap: 8px;
        margin-top: 4px;
    }
    .custom-tag-row input {
        flex: 1;
    }
    .btn-sm {
        padding: 9px 16px;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        background: var(--surface);
        color: var(--text);
        white-space: nowrap;
        transition: .15s;
    }
    .btn-sm:hover {
        background: var(--brand-light);
        border-color: var(--brand);
        color: var(--brand);
    }

    /* ── Radio cards (rate type, availability) ── */
    .radio-cards {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }
    .radio-card {
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 14px 16px;
        cursor: pointer;
        transition: .15s;
    }
    .radio-card:has(input:checked) {
        border-color: var(--brand);
        background: var(--brand-light);
    }
    .radio-card.active {
        border-color: var(--brand);
        background: var(--brand-light);
    }
    .radio-card input {
        margin-right: 8px;
        accent-color: var(--brand);
    }
    .radio-card-label {
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
    }
    .radio-card-sub   {
        font-size: 12px;
        color: var(--text-2);
        margin-top: 10px;
    }

    /* ── Availability pills ── */
    .avail-pills {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }
    .avail-pill {
        padding: 7px 18px;
        border: 1px solid var(--border);
        border-radius: 999px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        background: var(--surface);
        color: var(--text-2);
        transition: .15s;
    }
    .avail-pill:hover {
        border-color: var(--brand);
    }
    .avail-pill.active {
        background: var(--text);
        color: #fff;
        border-color: var(--text);
    }

    .action-bar-right {
        display: flex;
        gap: 8px;
    }

    /* ── File upload zones ── */
    .upload-zone {
        border: 1.5px dashed var(--border);
        border-radius: var(--radius);
        padding: 28px 16px;
        text-align: center;
        cursor: pointer;
        transition: .15s;
        position: relative;
    }
    .upload-zone:hover {
        border-color: var(--brand);
        background: var(--brand-light);
    }
    .upload-zone input[type=file] {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
        width: 100%;
        height: 100%;
    }
    .upload-icon {
        font-size: 28px;
        margin-bottom: 8px;
    }
    .upload-title {
        font-size: 13px;
        font-weight: 600;
        color: var(--text);
        margin-bottom: 2px;
    }
    .upload-hint  {
        font-size: 11px;
        color: var(--text-3);
    }
    .upload-grid  {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .file-list {
        margin-top: 10px;
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }
    .file-chip {
        display: flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        background: var(--brand-light);
        border-radius: 999px;
        font-size: 11px;
        color: var(--brand-dark);
        font-weight: 500;
    }
    .file-chip button {
        background: none;
        border: none;
        cursor: pointer;
        color: var(--brand-dark);
        font-size: 14px;
        line-height: 1;
        padding: 0;
        margin-left: 2px;
    }

    /* ── Form action bar ── */
    .action-bar {
        border-top: 1px solid var(--border);
        padding: 16px 0 0;
        margin-top: 18px;
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        align-items: center;
    }
    .action-bar-left {
        display: flex;
        gap: 8px;
    }
    .btn {
        padding: 9px 20px;
        border-radius: var(--radius-sm);
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        border: 1px solid var(--border);
        background: var(--surface);
        color: var(--text);
        transition: .15s;
    }
    .btn:hover {
        background: var(--bg);
    }
    .btn-primary {
        background: var(--text);
        color: #fff;
        border-color: var(--text);
        padding: 9px 28px;
        font-size: 14px;
    }
    .btn-primary:hover {
        background: #111;
    }

    /* ── Toast ── */
    .toast {
        position: fixed;
        bottom: 72px;
        left: 50%;
        transform: translateX(-50%);
        background: var(--text);
        color: #fff;
        padding: 10px 20px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 500;
        opacity: 0;
        pointer-events: none;
        transition: opacity .3s;
        white-space: nowrap;
        z-index: 999;
    }
    .toast.show {
        opacity: 1;
    }

    /* ── Info banner ── */
    .info-banner {
        background: var(--teal-light);
        border: 1px solid #A8DECE;
        border-radius: var(--radius-sm);
        padding: 10px 14px;
        font-size: 12px;
        color: #085041;
        margin-bottom: 14px;
    }
</style>
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading text-gray-900 fw-bold fs-3 my-0">
                        Register a new model
                    </h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="<?php echo $site_path; ?>/pages/dashboard" class="text-muted text-hover-primary">
                                Home
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">
                            Model
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                <form id="regForm" novalidate>
                    <input type="hidden" id="availability" name="availability" value="pan_india"/>
                    <input type="hidden" id="measurementUnit" name="measurementUnit" value="inches"/>
                    <input type="hidden" id="hidden_id" name="hidden_id" value="<?php echo $id; ?>"/>
                    <input type="hidden" id="measurement_id" name="measurement_id" value="<?php echo $mes_row['id']; ?>"/>
                    <div class="card" id="sec1">
                        <div class="card-header">
                            <div class="card-header-left">
                                <div class="section-badge">1</div>
                                <span class="card-title">Basic Information</span>
                            </div>
                            <span class="badge-req">Required</span>
                        </div>
                        <div class="card-body">
                            <div class="row col-2">
                                <div class="field mb-4" id="f-first-name">
                                    <label>First Name <span class="req">*</span></label>
                                    <input type="text" id="firstName" name="firstName" placeholder="e.g. Valentina" required value="<?php echo $first_name; ?>"/>
                                    <span class="field-error">First name is required</span>
                                </div>
                                <div class="field mb-4" id="f-last-name">
                                    <label>Last Name <span class="req">*</span></label>
                                    <input type="text" id="lastName" name="lastName" placeholder="e.g. Cruz" required value="<?php echo $last_name; ?>"/>
                                    <span class="field-error">Last name is required</span>
                                </div>
                            </div>
                            <div class="row col-3">
                                <div class="field mb-4">
                                    <label>Date of Birth</label>
                                    <input type="date" id="dob" name="dob" onchange="calcAge()" value="<?php echo $dob; ?>"/>
                                </div>
                                <div class="field mb-4">
                                    <label>Age</label>
                                    <input type="text" id="age" name="age" placeholder="Auto-calculated" readonly value="<?php echo $age; ?>"/>
                                </div>
                                <div class="field mb-4" id="f-gender">
                                    <label>Gender</label>
                                    <select id="gender" name="gender">
                                        <option value=""></option>
                                        <option>Female</option>
                                        <option>Male</option>
                                        <option>Non-binary</option>
                                        <option>Prefer not to say</option>
                                    </select>
                                    <span class="field-error">Gender is required</span>
                                </div>
                            </div>
                            <div class="row col-2">
                                <div class="field mb-4">
                                    <label>Agency / Division</label>
                                    <input type="text" id="agency" name="agency" value="<?php echo $agency_division; ?>"/>
                                </div>
                                <div class="field mb-4">
                                    <label>Model ID <span>(AUTO IF BLANK)</span></label>
                                    <input type="text" id="modelId" name="modelId" placeholder="Model ID" value="<?php echo $model_id; ?>" readonly/>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card" id="sec2">
                        <div class="card-header">
                            <div class="card-header-left">
                                <div class="section-badge">2</div>
                                <span class="card-title">Contact Details</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row col-2">
                                <div class="field mb-4">
                                    <label>Mobile (Primary)</label>
                                    <input type="tel" id="mobile1" name="mobile1" placeholder="+91 98765 43210" value="<?php echo $mobile_primary; ?>"/>
                                </div>
                                <div class="field mb-4">
                                    <label>Mobile (Alternate)</label>
                                    <input type="tel" id="mobile2" name="mobile2" placeholder="+91 98765 00000" value="<?php echo $mobile_alternate; ?>"/>
                                </div>
                            </div>
                            <div class="row col-3">
                                <div class="field mb-3" id="f-email">
                                    <label>Email</label>
                                    <input type="email" id="email" name="email" placeholder="model@email.com" value="<?php echo $email; ?>"/>
                                    <span class="field-error">Enter a valid email address</span>
                                </div>
                                <div class="field mb-3">
                                    <label>Instagram</label>
                                    <input type="text" id="instagram" name="instagram" placeholder="@handle" value="<?php echo $instagram; ?>"/>
                                </div>
                                <div class="field mb-3">
                                    <label>Youtube Channel ID</label>
                                    <input type="text" id="youtube" name="youtube" placeholder="" value="<?php echo $youtube_channel_id; ?>"/>
                                </div>
                            </div>
                            <div class="row col-full">
                                <div class="field mb-4">
                                    <label>Residential Address</label>
                                    <textarea id="address" name="address" placeholder="Flat / House No., Building, Street, Area…" rows="2"><?php echo $address; ?></textarea>
                                </div>
                            </div>
                            <div class="row col-3">
                                <div class="field mb-4">
                                    <label>City</label>
                                    <input type="text" id="city" name="city" placeholder="Mumbai" value="<?php echo $city; ?>"/>
                                </div>
                                <div class="field mb-4">
                                    <label>State</label>
                                    <input type="text" id="state" name="state" placeholder="Maharashtra" value="<?php echo $row['state']; ?>" />
                                </div>
                                <div class="field mb-4">
                                    <label>PIN Code</label>
                                    <input type="text" id="pin" name="pin" placeholder="400050" maxlength="6" value="<?php echo $row['pincode']; ?>"/>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card" id="sec3">
                        <div class="card-header">
                            <div class="card-header-left">
                                <div class="section-badge">3</div>
                                <span class="card-title">Identity Documents</span>
                            </div>
                            <span class="badge-opt">Optional</span>
                        </div>
                        <div class="card-body">
                            <div class="info-banner">📋 Upload government-issued ID for KYC verification. Supported: Aadhaar, PAN, Passport, Voter ID, Driving Licence.</div>
                            <div class="row col-2">
                                <div class="field mb-4">
                                    <label>PAN Number</label>
                                    <input type="text" id="panNumber" name="panNumber" placeholder="ABCDE1234F" maxlength="10" value="<?php echo $row['pan_number']; ?>"/>
                                </div>
                                <div class="field mb-4">
                                    <label>Aadhaar Card Number</label>
                                    <input type="text" id="aadhaarNumber" name="aadhaarNumber" placeholder="1234 5678 9012" maxlength="14" value="<?php echo $row['aadhaar_number']; ?>"/>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card" id="sec4">
                        <div class="card-header">
                            <div class="card-header-left">
                                <div class="section-badge">4</div>
                                <span class="card-title">Bank Details</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="info-banner">🔒 Bank account number is encrypted and stored securely. Never shared externally.</div>
                            <div class="row col-3">
                                <div class="field mb-4">
                                    <label>Bank Name</label>
                                    <input type="text" id="bankName" name="bankName" placeholder="e.g. HDFC Bank" value="<?php echo $row['bank_name']; ?>"/>
                                </div>
                                <div class="field mb-4">
                                    <label>Account Holder Name</label>
                                    <input type="text" id="acHolder" name="acHolder" placeholder="Full name as on account" value="<?php echo $row['account_holder_name']; ?>"/>
                                </div>
                                <div class="field mb-4" id="f-acNo">
                                    <label>Account Number</label>
                                    <input type="password" id="acNo" name="acNo" placeholder="Enter account number"  value="<?php echo $row['account_number']; ?>"/>
                                    <span class="field-error">Account numbers do not match</span>
                                </div>
                            </div>
                                
                            <div class="row col-3">
                                
                                <div class="field mb-4">
                                    <label>IFSC Code</label>
                                    <input type="text" id="ifsc" name="ifsc" placeholder="HDFC0001234" maxlength="11" value="<?php echo $row['ifsc_code']; ?>"/>
                                </div>
                                <div class="field mb-4">
                                    <label>Branch</label>
                                    <input type="text" id="branch" name="branch" placeholder="Bandra West" value="<?php echo $row['branch']; ?>"/>
                                </div>
                                <div class="field mb-4">
                                    <label>State</label>
                                    <input type="text" id="bankState" name="bankState" placeholder="Maharashtra" value="<?php echo $row['bank_state']; ?>"/>
                                </div>
                            </div>
                            <div class="row col-3">
                                <div class="field mb-4">
                                    <label>UPI ID</label>
                                    <input type="text" id="upi" name="upi" placeholder="name@upi" value="<?php echo $row['upi_id']; ?>"/>
                                </div>
                                <div class="field mb-4">
                                    <label>UPI QR Code <span style="font-weight:400;font-size:10px;color:var(--text-3)">(PNG / JPG · max 2 MB)</span></label>
                                    <label class="upload-zone" style="padding:14px;cursor:pointer;">
                                        <input type="file" id="qrFile" name="qrFile" accept=".png,.jpg,.jpeg" style="position:absolute;opacity:0;width:100%;height:100%"/>
                                        <div class="upload-icon">📷</div>
                                        <div class="upload-title">Upload QR image</div>
                                        <div class="upload-hint">PNG, JPG · max 2 MB</div>
                                    </label>
                                    <div id="qr-preview">
                                        <?php if(!empty($row['qr_code_image'])){ ?>
                                                <img
                                                    src="<?php $define_company_website; ?>/uploads/model/<?php echo $row['qr_code_image']; ?>"
                                                    alt="UPI QR"
                                                    style="max-width:200px;
                                                           border:1px solid #ddd;
                                                           border-radius:8px;
                                                           padding:5px;">
                                            <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card" id="sec5">
                        <div class="card-header">
                            <div class="card-header-left">
                                <div class="section-badge">5</div>
                                <span class="card-title">Commercials</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="radio-cards" style="margin-bottom:16px;">
                                <label class="radio-card" onclick="setRateType('per_day')">
                                    <div style="display:flex;align-items:center;">
                                        <input type="radio" name="rateType" value="per_day" <?php if($row['rate_type'] == 'per_day'){ echo "checked";} ?> id="rtDay"/>
                                        <span class="radio-card-label">Per Day Rate</span>
                                    </div>
                                    <div class="radio-card-sub" id="per-day-body">
                                        <div class="field" style="margin-top:8px;">
                                            <label>Amount per day (₹)</label>
                                            <input type="number" id="amtDay" name="amtDay" placeholder="e.g. 15000" min="0" value="<?php echo $row['amount_per_day']; ?>"/>
                                        </div>
                                    </div>
                                </label>
                                <label class="radio-card" onclick="setRateType('per_outfit')">
                                    <div style="display:flex;align-items:center;">
                                        <input type="radio" name="rateType" value="per_outfit" <?php if($row['rate_type'] == 'per_outfit'){ echo "checked";} ?> id="rtOutfit"/>
                                        <span class="radio-card-label">Per Outfit Rate</span>
                                    </div>
                                    <div class="radio-card-sub" id="per-outfit-body" style="display:none;">
                                        <div class="row col-2" style="margin-top:8px;">
                                            <div class="field">
                                                <label>Amount per outfit (₹)</label>
                                                <input type="number" id="amtOutfit" name="amtOutfit" placeholder="e.g. 3000" min="0" value="<?php echo $row['amount_per_outfit']; ?>"/>
                                            </div>
                                            <div class="field">
                                                <label>Max Outfits per Day</label>
                                                <input type="number" id="maxOutfitsDay" name="maxOutfitsDay" placeholder="e.g. 5" min="0" value="<?php echo $row['max_outfit_per_day']; ?>"/>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="card" id="sec6">
                        <div class="card-header">
                            <div class="card-header-left">
                                <div class="section-badge">6</div>
                                <span class="card-title">Location Flexibility</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div style="margin-bottom:16px;">
                                <label style="margin-bottom:8px;display:block;">Availability</label>
                                <div class="avail-pills">
                                    <button type="button" class="avail-pill active" data-avail="pan_india" onclick="setAvail(this)">Pan-India (Flexible)</button>
                                    <button type="button" class="avail-pill" data-avail="hometown_only" onclick="setAvail(this)">Hometown Only</button>
                                    <button type="button" class="avail-pill" data-avail="both" onclick="setAvail(this)">Both</button>
                                </div>
                            </div>
                            <div class="row col-2">
                                <div class="field">
                                    <label>Hometown / Base City</label>
                                    <input type="text" id="hometown" name="hometown" placeholder="e.g. Mumbai, Maharashtra" value="<?php echo $row['availability']; ?>"/>
                                </div>
                                <div class="field">
                                    <label>Travel Notes</label>
                                    <input type="text" id="travelNotes" name="travelNotes" placeholder="e.g. Available internationally with 7-day notice" value="<?php echo $row['travel_notes']; ?>"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card" id="sec7">
                        <div class="card-header">
                            <div class="card-header-left">
                                <div class="section-badge teal">7</div>
                                <span class="card-title">Body Measurements</span>
                            </div>
                            <a href="#" class="mt-5" style="font-size:12px;color:var(--brand);text-decoration:none;font-weight:600;">See diagram →</a>
                        </div>
                        <div class="card-body">
                            <div class="info-banner">Use the reference diagram to locate each measurement point accurately.</div>
                            <div class="unit-toggle">
                                <button type="button" class="active" onclick="setUnit('inches', this)">Inches</button>
                                <button type="button" onclick="setUnit('cm', this)">CM</button>
                            </div>

                            <div class="meas-section-label">Circumferences</div>
                            <div class="row col-3">
                                <div class="field"><label>A · Neck</label><input type="number" step="0.1" id="neck" name="neck" placeholder='14.5"' value="<?php echo $mes_row['neck']; ?>"/></div>
                                <div class="field"><label>1 · Bust / Chest</label><input type="number" step="0.1" id="bust" name="bust" placeholder='36"' value="<?php echo $mes_row['bust_chest']; ?>"/></div>
                                <div class="field"><label>C · Under Bust</label><input type="number" step="0.1" id="underbust" name="underbust" placeholder='32"' value="<?php echo $mes_row['under_bust']; ?>"/></div>
                            </div>
                            <div class="row col-3">
                                <div class="field"><label>2 · Natural Waist</label><input type="number" step="0.1" id="waist" name="waist" placeholder='28"' value="<?php echo $mes_row['natural_waist']; ?>"/></div>
                                <div class="field"><label>D · High Hip</label><input type="number" step="0.1" id="highHip" name="highHip" placeholder='36"' value="<?php echo $mes_row['high_hip']; ?>"/></div>
                                <div class="field"><label>3 · Full Hip</label><input type="number" step="0.1" id="fullHip" name="fullHip" placeholder='38"' value="<?php echo $mes_row['full_hip']; ?>"/></div>
                            </div>
                            <div class="row col-3">
                                <div class="field"><label>E · Bicep</label><input type="number" step="0.1" id="bicep" name="bicep" placeholder='12"' value="<?php echo $mes_row['bicep']; ?>"/></div>
                                <div class="field"><label>F · Wrist</label><input type="number" step="0.1" id="wrist" name="wrist" placeholder='6.5"' value="<?php echo $mes_row['wrist']; ?>"/></div>
                                <div class="field"><label>G · Thigh</label><input type="number" step="0.1" id="thigh" name="thigh" placeholder='22"' value="<?php echo $mes_row['thigh']; ?>"/></div>
                            </div>
                            <div class="row col-2">
                                <div class="field"><label>H · Knee</label><input type="number" step="0.1" id="knee" name="knee" placeholder='14"' value="<?php echo $mes_row['knee']; ?>"/></div>
                                <div class="field"><label>I · Calf</label><input type="number" step="0.1" id="calf" name="calf" placeholder='13"' value="<?php echo $mes_row['calf']; ?>"/></div>
                            </div>

                            <div class="meas-section-label">Shoulder</div>
                            <div class="row col-3">
                                <div class="field"><label>B · Shoulder Width</label><input type="number" step="0.1" id="shoulderW" name="shoulderW" placeholder='15.5"' value="<?php echo $mes_row['shoulder_width']; ?>"/></div>
                                <div class="field"><label>5 · Shoulder End-End</label><input type="number" step="0.1" id="shoulderE" name="shoulderE" placeholder='16"' value="<?php echo $mes_row['shoulder_end']; ?>"/></div>
                                <div class="field"><label>J · Across Back</label><input type="number" step="0.1" id="acrossBack" name="acrossBack" placeholder='14"' value="<?php echo $mes_row['across_back']; ?>"/></div>
                            </div>

                            <div class="meas-section-label">Lengths — Front</div>
                            <div class="row col-3">
                                <div class="field"><label>4 · Bust Apex Height</label><input type="number" step="0.1" id="bustApex" name="bustApex" placeholder='10.5"' value="<?php echo $mes_row['bust_apex_height']; ?>"/></div>
                                <div class="field"><label>6 · Neck-Waist (Front)</label><input type="number" step="0.1" id="nwFront" name="nwFront" placeholder='15.5"' value="<?php echo $mes_row['neck_waist_front']; ?>"/></div>
                                <div class="field"><label>K · Neck-Waist (Back)</label><input type="number" step="0.1" id="nwBack" name="nwBack" placeholder='16"' value="<?php echo $mes_row['neck_waist_back']; ?>"/></div>
                            </div>
                            <div class="row col-3">
                                <div class="field"><label>7 · Back Waist Length</label><input type="number" step="0.1" id="backWaist" name="backWaist" placeholder='16.5"' value="<?php echo $mes_row['back_waist_length']; ?>"/></div>
                                <div class="field"><label>8 · Waist to Length</label><input type="number" step="0.1" id="waistLen" name="waistLen" placeholder='24"' value="<?php echo $mes_row['waist_to_length']; ?>"/></div>
                                <div class="field"><label>L · Back Hip (Seat)</label><input type="number" step="0.1" id="backHip" name="backHip" placeholder='40"' value="<?php echo $mes_row['back_hip_seat']; ?>"/></div>
                            </div>

                            <div class="meas-section-label">Lengths — Lower Body</div>
                            <div class="row col-3">
                                <div class="field"><label>9 · Crotch Length</label><input type="number" step="0.1" id="crotch" name="crotch" placeholder='11"' value="<?php echo $mes_row['back_hip_seat']; ?>"/></div>
                                <div class="field"><label>M · Inseam</label><input type="number" step="0.1" id="inseam" name="inseam" placeholder='30"' value="<?php echo $mes_row['inseam']; ?>"/></div>
                                <div class="field"><label>10 · Arm / Sleeve</label><input type="number" step="0.1" id="sleeve" name="sleeve" placeholder='24.5"' value="<?php echo $mes_row['sleeve_length']; ?>"/></div>
                            </div>
                            <div class="row col-2">
                                <div class="field"><label>P · Total Height</label><input type="number" step="0.1" id="height" name="height" placeholder="5'7&quot;" value="<?php echo $row['height']; ?>"/></div>
                                <div class="field"><label>Weight <span style="font-weight:400;text-transform:none;font-size:10px;color:var(--text-3)">(optional)</span></label><input type="number" step="0.1" id="weight" name="weight" placeholder="60 kg" value="<?php echo $row['weight']; ?>"/></div>
                            </div>
                            <div class="row col-2">
                                <div class="field">
                                    <label>Dress / Size Label</label>
                                    <input type="text" id="dressSize" name="dressSize" placeholder="e.g. S / 36"/>
                                </div>
                                <div class="field">
                                    <label>Posture Type</label>
                                    <select id="posture" name="posture">
                                        <option value="">Select</option>
                                        <option <?php if($row['posture_type'] == 'Upright'){ echo 'selected';} ?>>Upright</option>
                                        <option <?php if($row['posture_type'] == 'Slightly Forward'){ echo 'selected';} ?>>Slightly Forward</option>
                                        <option <?php if($row['posture_type'] == 'Scoliosis'){ echo 'selected';} ?>>Scoliosis</option>
                                        <option <?php if($row['posture_type'] == 'Other'){ echo 'selected';} ?>>Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card" id="sec8">
                        <div class="card-header">
                            <div class="card-header-left">
                                <div class="section-badge teal">8</div>
                                <span class="card-title">Outfit Style Tags</span>
                            </div>
                        </div>

                        <div class="style-tags-wrapper">
                            <p class="tag-desc">
                                Select all styles that suit this model's look
                            </p>

                            <div id="style-tags-list" class="style-tags">
                                <?php foreach($outfit_style_tag as $tag){ ?>
                                    <button type="button" class="style-tag" data-id="<?= $tag['id']; ?>"> <?= htmlspecialchars($tag['name']); ?></button>
                                <?php } ?>

                            </div>

                            <input type="hidden" name="selected_tags" id="selected_tags">
                            <div class="tag-add-row">
                                <input type="text" id="new_tag" class="form-control" placeholder="Add custom style tag...">
                                <button type="button" id="addTagBtn" class="btn btn-primary"> + Add</button>
                            </div>
                        </div>
                    </div>

                    <div class="card" id="sec9">
                        <div class="card-header">
                            <div class="card-header-left">
                                <div class="section-badge">9</div>
                                <span class="card-title">Profile Media</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="upload-grid">
                                <div>
                                    <label style="display:block;margin-bottom:8px;">Profile Photos</label>
                                    <label class="upload-zone">
                                        <input type="file" id="photoFiles" accept=".jpg,.jpeg,.png" multiple onchange="handleMedia(this, 'photo')"/>
                                        <div class="upload-icon">🖼️</div>
                                        <div class="upload-title">Upload photos</div>
                                        <div class="upload-hint">JPG, PNG · max 10 · 5 MB each</div>
                                    </label>
                                    <div class="file-list" id="photo-list">
                                        <?php while($photo = $photos->fetch_assoc()) { ?>
                                            <div class="media-preview-item"
                                                 id="photo_<?php echo $photo['id']; ?>">
                                                <img src="../../uploads/model/<?php echo $photo['image_path']; ?>" style="max-width:200px;
                                                           border:1px solid #ddd;
                                                           border-radius:8px;
                                                           padding:5px;">
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div>
                                    <label style="display:block;margin-bottom:8px;">Profile Videos / Reels</label>
                                    <label class="upload-zone">
                                        <input type="file" id="videoFiles" accept=".mp4,.mov" multiple onchange="handleMedia(this, 'video')"/>
                                        <div class="upload-icon">🎬</div>
                                        <div class="upload-title">Upload videos</div>
                                        <div class="upload-hint">MP4, MOV · max 3 · 50 MB each</div>
                                    </label>
                                    <div class="file-list" id="video-list"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card" id="sec10">
                        <div class="card-header">
                            <div class="card-header-left">
                                <div class="section-badge">10</div>
                                <span class="card-title">Remarks / Notes</span>
                            </div>
                            <span class="badge-opt">Optional</span>
                        </div>
                        <div class="card-body">
                            <div class="field">
                                <label>Internal Notes</label>
                                <textarea id="notes" name="notes" rows="4" placeholder="Special notes, preferences, restrictions, or agency remarks about this model…"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="action-bar">
                        <div class="action-bar-left">
                            <button type="button" class="btn" onclick="discardForm()">Discard</button>
                        </div>
                        <div class="action-bar-right">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form><!-- /regForm -->

                <div class="toast" id="formToast"></div>
            </div>
        </div>
        <?php include("includes/footer.php"); ?>
    </div>
</div>

<script>var hostUrl = "<?php echo $site_path; ?>/";</script>
<script src="<?php echo $site_path; ?>/assets/plugins/global/plugins.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/scripts.bundle.js"></script>
<script src="<?php echo $site_path; ?>/assets/js/custom/create-task.js?v=<?php echo time(); ?>"></script>
<script>
    function calcAge() {
        let dob = document.getElementById('dob').value;

        if (dob) {
            let birthDate = new Date(dob);
            let today = new Date();

            let age = today.getFullYear() - birthDate.getFullYear();

            let monthDiff = today.getMonth() - birthDate.getMonth();

            if (
                monthDiff < 0 ||
                (monthDiff === 0 && today.getDate() < birthDate.getDate())
            ) {
                age--;
            }

            document.getElementById('age').value = age;
        }
    }
    function showToast(message) {
        var toast = document.getElementById('formToast');
        if (!toast)
            return;
        toast.textContent = message;
        toast.classList.add('show');
        window.clearTimeout(window.__modelFormToastTimer);
        window.__modelFormToastTimer = window.setTimeout(function () {
            toast.classList.remove('show');
        }, 2200);
    }

    function setRateType(type) {
        var dayRadio = document.getElementById('rtDay');
        var outfitRadio = document.getElementById('rtOutfit');
        var dayBody = document.getElementById('per-day-body');
        var outfitBody = document.getElementById('per-outfit-body');
        var cards = document.querySelectorAll('#sec5 .radio-card');

        if (dayRadio)
            dayRadio.checked = type === 'per_day';
        if (outfitRadio)
            outfitRadio.checked = type === 'per_outfit';
        if (dayBody)
            dayBody.style.display = type === 'per_day' ? 'block' : 'none';
        if (outfitBody)
            outfitBody.style.display = type === 'per_outfit' ? 'block' : 'none';

        cards.forEach(function (card) {
            var input = card.querySelector('input[name="rateType"]');
            card.classList.toggle('active', !!input && input.value === type);
        });
    }

    function setAvail(button) {
        var value = button && button.getAttribute('data-avail') ? button.getAttribute('data-avail') : 'pan_india';
        document.querySelectorAll('.avail-pill').forEach(function (pill) {
            pill.classList.toggle('active', pill === button);
        });
        var availability = document.getElementById('availability');
        if (availability)
            availability.value = value;
    }

    function setUnit(unit, button) {
        document.querySelectorAll('.unit-toggle button').forEach(function (unitButton) {
            unitButton.classList.toggle('active', unitButton === button);
        });
        var measurementUnit = document.getElementById('measurementUnit');
        if (measurementUnit)
            measurementUnit.value = unit;
    }

    function discardForm() {
        var form = document.getElementById('regForm');
        if (!form)
            return;
        form.reset();
        setRateType('per_day');
        setAvail(document.querySelector('.avail-pill[data-avail="pan_india"]'));
        setUnit('inches', document.querySelector('.unit-toggle button'));
        showToast('Form cleared');
    }

    function validateModelForm() {
        var isValid = true;
        $(".field").removeClass("has-error");
        if ($("#firstName").val().trim() == "") {
            $("#f-first-name").addClass("has-error");
            isValid = false;
        }

        // Last Name
        if ($("#lastName").val().trim() == "") {
            $("#f-last-name").addClass("has-error");
            isValid = false;
        }

        // Email validation
        let email = $("#email").val().trim();

        if (email != "") {
            let regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!regex.test(email)) {
                $("#f-email").addClass("has-error");
                isValid = false;
            }
        }
        
        if($("#gender").val().trim == ""){
            $("#f-gender").addClass("has-error");
            isValid = false;
        }

        if (!isValid) {
            showToast("Please correct validation errors.");
        }

        return isValid;
    }

    document.addEventListener('DOMContentLoaded', function () {
        var checkedRate = document.querySelector('input[name="rateType"]:checked');
        var selectedUnit = document.getElementById('measurementUnit');

        setRateType(checkedRate ? checkedRate.value : 'per_day');
        setAvail(document.querySelector('.avail-pill.active') || document.querySelector('.avail-pill[data-avail="pan_india"]'));
        setUnit(selectedUnit ? selectedUnit.value : 'inches', document.querySelector('.unit-toggle button.active') || document.querySelector('.unit-toggle button'));

        var form = document.getElementById('regForm');
        if (form) {
            $("#regForm").on("submit", function(e) {
                e.preventDefault();
                if (!validateModelForm()) {
                    return false;
                }
                let formData = new FormData(this);
                $.ajax({
                    url: "<?php echo $site_path; ?>/ajax/ajax-update-model-data",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: "json",

                    beforeSend: function() {
                        $(".btn-primary")
                            .prop("disabled", true)
                            .text("Submitting...");
                    },

                    success: function(response) {
                        $(".btn-primary").prop("disabled", false).text("Submit");
                        if (response.success) {
                            showToast(response.message);
                            setTimeout(function() {
                                window.location.href = "<?php echo $site_path; ?>/model-registration";
                            }, 1000);
                        } else {
                            showToast(response.message);
                        }
                    },

                    error: function(xhr) {
                        $(".btn-primary").prop("disabled", false).text("Submit");
                        showToast("Something went wrong.");
                        console.log(xhr.responseText);
                    }
                });

            });
        }
    });

    $(document).ready(function () {
        $("#due_date").flatpickr({
            altInput: true,
            altFormat: "Y-m-d H:i",
            dateFormat: "Y-m-d H:i",
            enableTime: true,
            time_24hr: true       // remove this line if you want 12hr AM/PM
        });
    });
    /* TAG SELECTION */
    let selectedTags = [];
    $(document).on('click', '.style-tag', function () {
        let id = $(this).data('id');
        $(this).toggleClass('active');
        if ($(this).hasClass('active')) {
            if (!selectedTags.includes(id)) {
                selectedTags.push(id);
            }
        } else {
            selectedTags = selectedTags.filter(tagId => tagId != id);
        }
        $('#selected_tags').val(selectedTags.join(','));
    });
    
    
    $('#addTagBtn').click(function () {
        let tag_name = $('#new_tag').val().trim();
        if (tag_name == '') {
            alert('Please enter style tag name');
            $('#new_tag').focus();
            return;
        }

        $.ajax({
        url: '<?php echo $site_path ?>/ajax/add-update-tag',
        type: 'POST',
        dataType: 'json',
        data: {
            style_name: tag_name,
            action : 'add-style'
        },
        success: function (response) {
            if (response.status == 'success') {
                $('#style-tags-list').append(`
                    <button
                        type="button"
                        class="style-tag active"
                        data-id="${response.id}">
                        ${response.tag_name}
                    </button>
                `);

                selectedTags.push(response.id);
                $('#selected_tags').val(selectedTags.join(','));

                $('#new_tag').val('');
            } else {
                alert(response.message);
            }
        }
    });
    });
    document.getElementById('qrFile').addEventListener('change', function () {

    const file = this.files[0];
    const preview = document.getElementById('qr-preview');

    preview.innerHTML = '';

    if (!file) return;

    // Validate size (2 MB)
    if (file.size > 2 * 1024 * 1024) {
        alert('File size must be less than 2 MB');
        this.value = '';
        return;
    }

    const reader = new FileReader();

    reader.onload = function (e) {
        preview.innerHTML = `
            <img src="${e.target.result}"
                 alt="QR Preview"
                 style="max-width:200px;
                        max-height:200px;
                        border:1px solid #ddd;
                        border-radius:8px;
                        padding:5px;">
        `;
    };

    reader.readAsDataURL(file);
});
</script>
