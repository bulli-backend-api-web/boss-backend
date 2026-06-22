<?php
include("config/database.php");
include("config/auth_check.php");
include("includes/sidemenu.php");

$emp_id = generate_staff_code();
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
    
    #regForm > #contractDates {
        display: none !important;
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
        grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
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
    .file-list{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(180px,1fr));
    gap:15px;
    margin-top:15px;
}

.media-preview-item{
    background:#fff;
    border:1px solid #e5e7eb;
    border-radius:12px;
    overflow:hidden;
    box-shadow:0 2px 8px rgba(0,0,0,.08);
}

.media-preview-item img,
.media-preview-item video{
    width:100%;
    height:180px;
    object-fit:cover;
    display:block;
}

.media-preview-info{
    padding:10px;
}

.media-preview-name{
    font-size:13px;
    font-weight:500;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
}

.media-preview-size{
    font-size:11px;
    color:#777;
    margin-top:4px;
}
</style>
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading text-gray-900 fw-bold fs-3 my-0">
                        Register a new Staff
                    </h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="<?php echo $site_path; ?>/dashboard" class="text-muted text-hover-primary">
                                Home
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">
                            Staff List
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                <form id="regForm" class="form" action="<?php echo $site_path; ?>/ajax/add-update-staff-details">
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
                                <div class="fv-row field mb-4">
                                    <label>First Name <span class="req">*</span></label>
                                    <input type="text" id="firstName" name="firstName" placeholder="e.g. Valentina" required/>
                                    <span class="field-error">First name is required</span>
                                </div>
                                <div class="fv-row field mb-4" id="f-last-name">
                                    <label>Last Name <span class="req">*</span></label>
                                    <input type="text" id="lastName" name="lastName" placeholder="e.g. Cruz" required/>
                                    <span class="field-error">Last name is required</span>
                                </div>
                            </div>
                            <div class="row col-3">
                                <div class="fav-row field mb-4">
                                    <label>Date of Birth</label>
                                    <input type="date" id="dob" name="dob"/>
                                </div>
                                <div class="field fv-row mb-4">
                                    <label>Date of Join</label>
                                    <input type="date" id="doj" name="doj"/>
                                </div>
                                <div class="field fv-row mb-4" id="f-gender">
                                    <label>Gender</label>
                                    <select id="gender" name="gender">
                                        <option value="">Select Gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                    <span class="field-error">Gender is required</span>
                                </div>
                            </div>
                            <div class="row col-2">
                                <div class="field fv-row mb-4">
                                    <label> Employment Type</label>
                                    <select id="employment_type" name="employment_type">
                                        <option value="">Select Employment Type</option>
                                        <option value="monthly">Monthly</option>
                                        <option value="contract">Contract</option>
                                    </select>
                                </div>
                                <div class="field mb-4">
                                    <label>Employer ID</label>
                                    <input type="text" id="employ_id" name="employ_id" placeholder="Employer ID" value="<?php echo $emp_id; ?>" readonly/>
                                </div>
                            </div>
                            <div class="row col-2" id="contractDates">
                                <div class="field mb-4">
                                    <label>Start Date</label>
                                    <input type="date" id="contract_start" name="contract_start"/>
                                </div>
                                <div class="field mb-4">
                                    <label>End Date</label>
                                    <input type="date" id="contract_end" name="contract_end"/>
                                </div>
                            </div>
                            <div class="row col-2" id="contractDates">
                                <div class="field mb-4">
                                    <label>Salary</label>
                                    <input type="text" id="salary" name="salary" placeholder="e.g 10000"/>
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
                                <div class="field fv-row mb-4" id="f-mobile1">
                                    <label>Mobile (Primary)</label>
                                    <input type="tel" id="mobile1" name="mobile1" placeholder="+91 98765 43210"/>
                                    <span class="field-error">Mobile Number is required</span>
                                </div>
                                <div class="field fv-row mb-4"  id="f-email">
                                     <label>Email</label>
                                    <input type="email" id="email" name="email" placeholder="model@email.com"/>
                                    <span class="field-error">Enter a valid email address</span>
                                </div>
                            </div>
                            <div class="row col-full">
                                <div class="field fv-row mb-4">
                                    <label>Residential Address</label>
                                    <textarea id="address" name="address" placeholder="Flat / House No., Building, Street, Area…" rows="2"></textarea>
                                </div>
                            </div>
                            <div class="row col-3">
                                <div class="field mb-4">
                                    <label>City</label>
                                    <input type="text" id="city" name="city" placeholder="Mumbai"/>
                                </div>
                                <div class="field mb-4">
                                    <label>State</label>
                                    <input type="text" id="state" name="state" placeholder="Maharashtra"/>
                                </div>
                                <div class="field mb-4">
                                    <label>PIN Code</label>
                                    <input type="text" id="pin" name="pin" placeholder="400050" maxlength="6"/>
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
                            <div class="row col-2">
                                <div class="field mb-4">
                                    <label>ID Proof</label>
                                    <input type="file" id="id_proof" name="id_proof" accept=".png,.jpg,.jpeg"/>
                                </div>
                                <div class="field mb-4">
                                    <label>ID Proof No</label>
                                    <input type="text" id="id_proof_no" name="id_proof_no" placeholder="1234 5678 9012" maxlength="14"/>
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
                            <div class="row col-3">
                                <div class="field mb-4">
                                    <label>Bank Name</label>
                                    <input type="text" id="bankName" name="bankName" placeholder="e.g. HDFC Bank"/>
                                </div>
                                <div class="field mb-4">
                                    <label>Account Holder Name</label>
                                    <input type="text" id="acHolder" name="acHolder" placeholder="Full name as on account"/>
                                </div>
                                <div class="field mb-4" id="f-acNo">
                                    <label>Account Number</label>
                                    <input type="text" id="acNo" name="acNo" placeholder="Enter account number"/>
                                    <span class="field-error">Account numbers do not match</span>
                                </div>
                            </div>
                                
                            <div class="row col-3">
                                
                                <div class="field mb-4">
                                    <label>IFSC Code</label>
                                    <input type="text" id="ifsc" name="ifsc" placeholder="HDFC0001234" maxlength="11"/>
                                </div>
                                <div class="field mb-4">
                                    <label>Branch</label>
                                    <input type="text" id="branch" name="branch" placeholder="Bandra West"/>
                                </div>
                                <div class="field mb-4">
                                    <label>Cancelled Cheque <span style="font-weight:400;font-size:10px;color:var(--text-3)">(PNG / JPG · max 2 MB)</span></label>
                                    <label class="upload-zone" style="padding:14px;cursor:pointer;">
                                        <input type="file" id="cencelled_cheque" name="cencelled_cheque" accept=".png,.jpg,.jpeg" style="position:absolute;opacity:0;width:100%;height:100%"/>
                                        <div class="upload-icon">📷</div>
                                        <div class="upload-title">Upload Cancel Cheque</div>
                                        <div class="upload-hint">PNG, JPG · max 2 MB</div>
                                    </label>
                                    <div id="qr-preview"></div>
                                </div>
                            </div>
                            <div class="row col-3">
                                
                            </div>
                        </div>
                    </div>
                    <div class="card" id="sec10">
                        <div class="card-header">
                            <div class="card-header-left">
                                <div class="section-badge">5</div>
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
                            <button type="submit" class="btn btn-primary" id="kt_create_staff_details_submit">Save Changes</button>
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
<script src="<?php echo $site_path; ?>/assets/js/custom/create-staff.js?v=<?php echo time(); ?>"></script>
<script>
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
    
    document.getElementById('cencelled_cheque').addEventListener('change', function () {
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
                <img src="${e.target.result}" alt="Cheque Preview" style="max-width:200px; max-height:200px; border:1px solid #ddd; border-radius:8px; padding:5px;">`;
        };
        reader.readAsDataURL(file);
    });
</script>
