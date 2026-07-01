<?php
// Auto-generated module: reference-library

include("config/database.php");
include("config/auth_check.php");
include("includes/sidemenu.php");

$jobwork_type_list = get_jobwork_type_list();
$ref_code = generate_ref_library_code();
$collection_list = get_collection_list();

$total_ref_sql = "SELECT COUNT(id) as total FROM reference_library";
$res = $con->query($total_ref_sql);
$total_count = $res ? ($res->fetch_assoc()['total'] ?? 0) : 0;

$total_col_sql = "SELECT COUNT(id) as total FROM collections";
$res = $con->query($total_col_sql);
$total_collection_count = $res ? ($res->fetch_assoc()['total'] ?? 0) : 0;

$fav_count = $auto_fetch_count = 0;

$garment = ['General', 'Lehenga', 'Kurta Set', 'Gown', 'Suit Set', 'Dupatta'];
$ocassion = ['Bridal', 'Festive', 'Navratri', 'Sangeet', 'Party wear', 'Wedding guest', 'Casual', 'General'];
$reference_type  = get_reference_type_list();

$sql = "SELECT reference_library.reference_type,reference_type.name, COUNT(*) AS total FROM reference_library JOIN reference_type ON reference_type.id =reference_library.reference_type  GROUP BY reference_type";
$result = $con->query($sql);
$filter_by_reference_output  = [];
$total_reference_type = 0;
while($row = $result->fetch_assoc()){
    $filter_by_reference_output[] = $row;
    $total_reference_type+= (int)$row['total'];
}
?>

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_toolbar" class="app-toolbar pt-7 pt-lg-10">
            <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
                <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                    <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                        <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">Reference Library</h1>
                    </div>
                </div>
            </div>
        </div>

        <div id="kt_app_content" class="app-content">
            <div id="kt_app_content_container" class="app-container container-fluid">
                <!-- Header actions -->
                <div class="d-flex justify-content-between align-items-start mb-5 flex-wrap gap-3">
                    <div>

                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-light-primary"><i class="bi bi-arrow-left-right me-1"></i> Compare</button>
                        <button class="btn btn-light-primary"  data-bs-toggle="modal" data-bs-target="#newCollectionModal"><i class="bi bi-collection me-1"></i> Collection</button>
                        <button class="btn btn-warning text-dark fw-bold" data-bs-toggle="modal" data-bs-target="#addReferenceModal"><i class="bi bi-plus-lg me-1"></i> Add</button>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-body d-flex flex-wrap align-items-center gap-3 py-3">
                        <span class="text-muted fw-bold small">YEAR</span>
                        <div class="btn-group" id="yearFilterGroup">
                            <button class="btn btn-sm btn-warning fw-bold filter-btn" data-group="year" data-value="">All years</button>
                            <button class="btn btn-sm btn-light filter-btn" data-group="year" data-value="2025">2025</button>
                            <button class="btn btn-sm btn-light filter-btn" data-group="year" data-value="2024">2024</button>
                            <button class="btn btn-sm btn-light filter-btn" data-group="year" data-value="2023">2023</button>
                            <button class="btn btn-sm btn-light filter-btn" data-group="year" data-value="2022">2022</button>
                        </div>
                        <span class="text-muted fw-bold small ms-3">SEASON</span>
                        <div class="btn-group ms-3">
                            <button class="btn btn-sm btn-light filter-btn" data-group="season" data-value="Navratri">Navratri all</button>
                            <button class="btn btn-sm btn-light filter-btn" data-group="season" data-value="Bridal">Bridal all</button>
                            <button class="btn btn-sm btn-light filter-btn" data-group="season" data-value="Diwali">Diwali all</button>
                        </div>
                        <span class="ms-auto text-muted small fw-bold">SHOWING 5</span>
                    </div>
                </div>

                <!-- Stat cards -->
                <div class="row g-4 mb-5">
                    <div class="col">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="text-muted small fw-bold mb-1">TOTAL</div>
                                <div class="fs-2 fw-bold"><?php echo $total_count; ?></div>
                                <div class="text-muted small">All refs</div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="text-muted small fw-bold mb-1">COLLECTIONS</div>
                                <div class="fs-2 fw-bold text-warning"><?php echo $total_collection_count; ?></div>
                                <div class="text-muted small">Active</div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="text-muted small fw-bold mb-1">USED</div>
                                <div class="fs-2 fw-bold text-success">312</div>
                                <div class="text-muted small">In designs</div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="text-muted small fw-bold mb-1">ORIGINALS</div>
                                <div class="fs-2 fw-bold text-primary"><?php echo $total_count; ?></div>
                                <div class="text-muted small">BK created</div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="text-muted small fw-bold mb-1">FAVOURITES</div>
                                <div class="fs-2 fw-bold text-danger"><?php echo $fav_count; ?></div>
                                <div class="text-muted small">Starred</div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="text-muted small fw-bold mb-1">PENDING</div>
                                <div class="fs-2 fw-bold"><?php echo $auto_fetch_count; ?></div>
                                <div class="text-muted small">Auto-fetch</div>
                            </div>
                        </div>
                    </div>
                </div>
                <ul class="nav nav-tabs mb-4" id="refLibTabs">
                    <li class="nav-item"><a class="nav-link active fw-bold" href="#"><i class="bi bi-grid me-1"></i> Browse</a></li>
                    <li class="nav-item"><a class="nav-link" href="#" data-tab="timeline"><i class="bi bi-calendar3 me-1"></i> Timeline</a></li>
                    <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-folder me-1"></i> Collections</a></li>
                    <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-graph-up-arrow me-1"></i> Trending</a></li>
                    <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-globe me-1"></i> Auto-fetch</a></li>
                    <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-star me-1"></i> Favourites</a></li>
                    <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-person me-1"></i> Own creation</a></li>
                    <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-layers me-1"></i> Compare</a></li>
                </ul>
                <div id="timelinePane" style="display:none;">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <span class="text-muted">View:</span>
                        <button class="btn btn-sm btn-success rounded-pill viewBtn active" data-view="quarter">Quarter</button>
                        <button class="btn btn-sm btn-outline-secondary rounded-pill viewBtn" data-view="vertical">Vertical</button>
                        <span class="text-muted ms-3">Year:</span>
                        <div id="yearTabs"></div>
                    </div>
                    <div id="quarterGrid" class="row g-3"></div>
                    <div id="detailPanel" class="card mt-4 p-3" style="display:none;"></div>
                </div>

                <div class="d-flex flex-wrap gap-3 mb-4">
                    <button class="btn btn-outline-dark btn-active-light-primary active text-start filter-btn" data-group="reference_type" data-value="">
                        <div class="fw-bold">All types</div>
                        <div class="text-muted small"><?php echo $total_reference_type; ?></div>
                    </button>
                            
                    <?php if($filter_by_reference_output){
                            foreach($filter_by_reference_output as $single_val){?>
                    <button class="btn btn-outline-secondary text-start filter-btn" data-group="reference_type" data-value="<?php echo $single_val['reference_type']; ?>">
                        <div class="fw-bold"><?php echo $single_val['name']; ?></div>
                        <div class="text-muted small"><?php echo $single_val['total']; ?></div>
                    </button>
                    <?php }
                    } ?>
                </div>

                <!-- Detailed filter card -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row mb-3 align-items-center">
                            <div class="col-md-1 text-muted fw-bold small">GARMENT</div>
                            <div class="col-md-11 d-flex flex-wrap gap-2" id="garmentFilterGroup">
                                <button class="btn btn-sm btn-warning filter-btn" data-group="garment" data-value="">All</button>
                                <?php foreach ($garment as $single_g) { ?> 
                                    <button class="btn btn-sm btn btn-sm btn-light filter-btn" data-group="garment" data-value="<?php echo $single_g; ?>"><?php echo $single_g; ?></button>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="row mb-3 align-items-center">
                            <div class="col-md-1 text-muted fw-bold small">WORK</div>
                            <div class="col-md-11 d-flex flex-wrap gap-2">
                                <button class="btn btn-sm btn-warning filter-btn" data-group="work" data-value="">All</button>
                                <?php if ($jobwork_type_list) {
                                    foreach ($jobwork_type_list as $single_work) {
                                        ?>
                                        <button class="btn btn-sm btn-light filter-btn" data-group="work" data-value="<?php echo $single_work['id']; ?>"><?php echo $single_work['name']; ?></button>
    <?php }
} ?>
                            </div>
                        </div>
                        <div class="row mb-3 align-items-center">
                            <div class="col-md-1 text-muted fw-bold small">OCCASION</div>
                            <div class="col-md-11 d-flex flex-wrap gap-2">
                                <button class="btn btn-sm btn-warning filter-btn fw-bold" data-group="occasion" data-value="">All</button>
                                <?php foreach ($ocassion as $single_o) { ?>
                                    <button class="btn btn-sm btn-light filter-btn" data-group="occasion" data-value="<?php echo $single_o; ?>"><?php echo $single_o; ?></button>
<?php } ?>
                            </div>
                        </div>
                        <div class="row mb-3 align-items-center">
                            <div class="col-md-1 text-muted fw-bold small">STYLE</div>
                            <div class="col-md-11 d-flex flex-wrap gap-2 align-items-center">
                                <button class="btn btn-sm btn-warning fw-bold">All</button>
                                <button class="btn btn-sm btn-light">Flared</button>
                                <button class="btn btn-sm btn-light">A-line</button>
                                <button class="btn btn-sm btn-light">Straight</button>
                                <button class="btn btn-sm btn-light">Mermaid</button>
                                <button class="btn btn-sm btn-light">Layered</button>
                                <span class="text-muted fw-bold small ms-3">SOURCE</span>
                                <button class="btn btn-sm btn-warning fw-bold">All</button>
                                <button class="btn btn-sm btn-light">BK Original</button>
                                <button class="btn btn-sm btn-light">Instagram</button>
                                <button class="btn btn-sm btn-light">Pinterest</button>
                                <button class="btn btn-sm btn-light">Market survey</button>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3 pt-2 border-top">
                            <span class="text-muted fw-bold small">LATEST</span>
                            <button class="btn btn-sm btn-light">Last 7 days</button>
                            <button class="btn btn-sm btn-light">Last 30 days</button>
                            <button class="btn btn-sm btn-light">Last 3 months</button>
                            <button class="btn btn-sm btn-light">All time</button>
                            <span class="text-muted fw-bold small ms-3">SORT</span>
                            <select class="form-select form-select-sm w-auto">
                                <option>Newest first</option>
                                <option>Oldest first</option>
                            </select>
                            <button class="btn btn-sm btn-outline-danger ms-auto">Clear all</button>
                        </div>
                    </div>
                </div>

                <!-- Results header -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="fw-semibold" id="resultsSummary">Showing 5 of 15 references (1 filter active)</span>
                    <span class="badge bg-light-warning text-warning fw-bold px-3 py-2">
                        <div id="activeFilterBadges" class="d-flex gap-2 flex-wrap"></div>
                    </span>
                </div>

                <!-- Reference cards grid -->
                <div class="row g-4" id="refCardsGrid">
                    <?php
                    // Replace this with your actual DB result loop
                    $refs = fetch_ref_library();
                    foreach ($refs as $ref) {
                        ?>
                        <div class="col-md-2">
                            <div class="card h-100 ref-card">
                                <div class="ref-thumb d-flex align-items-center justify-content-center">
                                    <img src="<?= $ref['image'] ?>" alt="<?= htmlspecialchars($ref['title']) ?>">
                                </div>
                                <div class="card-body">
                                    <div class="text-muted small fw-bold mb-1"><?= $ref['code'] ?></div>
                                    <div class="fw-bold mb-2"><?= $ref['title'] ?></div>
                                    <div class="d-flex flex-wrap gap-1 mb-2">
                                        <?php foreach ($ref['tags'] as $i => $tag) { ?>
                                            <span class="badge <?= $i == 0 ? 'bg-light-warning text-warning' : 'bg-light-primary text-primary' ?>"><?= $tag ?></span>
    <?php } ?>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="small text-muted">
                                            <i class="bi bi-circle-fill text-danger small"></i>
    <?php if ($ref['count'] > 1) echo '×' . $ref['count']; ?>
                                        </span>
                                        <span class="text-warning small"><?= str_repeat('★', $ref['rating']) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
<?php } ?>

                    <!-- Add reference card -->
                    <div class="col-md-2">
                        <div class="card h-100 d-flex align-items-center justify-content-center add-ref-card" style="min-height:260px; cursor:pointer;" data-bs-toggle = "modal" data-bs-target="#addReferenceModal">
                            <div class="text-center text-muted">
                                <i class="bi bi-plus-circle fs-1 d-block mb-2"></i>
                                Add reference
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- Add Reference Modal -->
            <div class="modal fade" id="addReferenceModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header border-0 pb-0">
                            <h4 class="modal-title fw-bold">
                                <i class="bi bi-upload text-warning me-2"></i> Add Reference
                            </h4>
                            <button type="button" class="btn btn-icon btn-sm btn-light" data-bs-dismiss="modal">
                                <i class="bi bi-x fs-2"></i>
                            </button>
                        </div>
                        <div class="modal-body pt-3">
                            <form id="addReferenceForm" enctype="multipart/form-data">

                                <!-- Upload area -->
                                <div class="upload-dropzone text-center mb-5" id="refUploadDropzone">
                                    <div id="refUploadPlaceholder">
                                        <i class="bi bi-image fs-2x text-muted d-block mb-2"></i>
                                        <span class="text-muted">Upload image · paste URL · drag and drop</span>
                                    </div>
                                    <div id="refUploadPreview" class="d-none">
                                        <img id="refPreviewImg" src="" style="max-height:180px; border-radius:8px;">
                                    </div>
                                    <input type="file" id="refImageInput" name="ref_image" accept="image/*" class="d-none">
                                </div>

                                <!-- Name + Code -->
                                <div class="row mb-5">
                                    <div class="col-md-7">
                                        <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="name" placeholder="e.g. Heavy Zardozi Bridal Lehenga" required>
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label fw-semibold">Code (auto)</label>
                                        <input type="text" name="ref_code" class="form-control bg-light-warning text-warning fw-bold" value="<?php echo $ref_code; ?>">
                                    </div>
                                </div>

                                <!-- Reference type -->
                                <div class="mb-5">
                                    <label class="form-label fw-semibold mb-2">REFERENCE TYPE <span class="text-danger">*</span></label>
                                    <div class="d-flex flex-wrap gap-2 ref-type-group">
                                        <button type="button" class="btn btn-sm btn-outline-secondary ref-type-btn" data-value="1">Colour</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary ref-type-btn" data-value="2">Work</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary ref-type-btn" data-value="3">Style</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary ref-type-btn" data-value="4">Stitching</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary ref-type-btn" data-value="5">Festive</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary ref-type-btn" data-value="6">Celebrity</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary ref-type-btn" data-value="7">Fabric</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary ref-type-btn" data-value="8">General</button>
                                    </div>
                                    <input type="hidden" name="reference_type" id="reference_type_input">
                                </div>

                                <!-- Garment / Work type / Occasion -->
                                <div class="row mb-5">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Garment</label>
                                        <select class="form-select" name="garment">
                                            <?php foreach ($garment as $single_val) { ?>
                                                <option value="<?php echo $single_val; ?>"><?php echo $single_val; ?></option>
<?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Work type</label>
                                        <select class="form-select" name="work_type">
                                            <option>Select Work Type</option>
                                            <?php foreach ($jobwork_type_list as $single_val) { ?>
                                                <option value="<?= $single_val['id']; ?>"><?= $single_val['name']; ?></option>
<?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Occasion</label>
                                        <select class="form-select" name="occasion">
                                            <?php foreach ($ocassion as $single_val) { ?>
                                                <option value="<?php echo $single_val; ?>"><?php echo $single_val; ?></option>
<?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- Collections multi-select -->
                                <div class="mb-5">
                                    <label class="form-label fw-semibold mb-2">ASSIGN TO COLLECTIONS (MULTI-SELECT)</label>
                                    <div class="d-flex flex-wrap gap-2 collection-group">
                                        <?php if ($collection_list) {
                                            foreach ($collection_list as $single_collection) {
                                                ?>
                                                <button type="button" class="btn btn-sm btn-outline-secondary collection-btn" data-value="<?php echo $single_collection['id']; ?>"><?php echo $single_collection['name']; ?></button>
    <?php }
}
?>
                                    </div>
                                    <input type="hidden" name="collections" id="collections_input">
                                </div>

                                <!-- Primary / Secondary colours -->
                                <div class="row mb-5">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Primary colour</label>
                                        <input type="color" class="form-control form-control-color w-100" name="primary_colour" id="primary_colour" value="#C0392B" style="height:38px;">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Secondary colours</label>
                                        <input type="color" class="form-control form-control-color w-100" name="secondary_colours" id="secondary_colours" value="#C0392B" style="height:38px;">
                                    </div>
                                </div>

                                <!-- Notes -->
                                <div class="mb-5">
                                    <label class="form-label fw-semibold">Notes</label>
                                    <textarea class="form-control" name="notes" rows="3" placeholder="What this ref is for, key design points..."></textarea>
                                </div>

                                <!-- Tags -->
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Tags</label>
                                    <input type="text" class="form-control" name="tags" placeholder="e.g. heavy work, bridal, red gold (comma separated)">
                                </div>

                            </form>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-light" id="saveDraftBtn">
                                <i class="bi bi-file-earmark me-1"></i> Draft
                            </button>
                            <button type="button" class="btn btn-warning text-dark fw-bold" id="addReferenceSubmitBtn">
                                <i class="bi bi-check-lg me-1"></i> Add
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- New Collection Modal -->
            <div class="modal fade" id="newCollectionModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header border-0 pb-0">
                            <h4 class="modal-title fw-bold">
                                <i class="bi bi-folder-plus text-warning me-2"></i> New Collection
                            </h4>
                            <button type="button" class="btn btn-icon btn-sm btn-light" data-bs-dismiss="modal">
                                <i class="bi bi-x fs-2"></i>
                            </button>
                        </div>
                        <div class="modal-body pt-3">
                            <form id="newCollectionForm" novalidate>

                                <!-- Name + ID -->
                                <div class="row mb-5">
                                    <div class="col-md-7">
                                        <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="name" id="collection_name" placeholder="e.g. Navratri 2026">
                                        <div class="invalid-feedback" id="name_error">Name is required.</div>
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label fw-semibold">ID (auto)</label>
                                        <input type="text" class="form-control bg-light-warning text-warning fw-bold" id="collection_id_display" value="NAV26" readonly>
                                        <input type="hidden" name="collection_code" id="collection_code">
                                    </div>
                                </div>

                                <!-- Year / Month / Target refs -->
                                <div class="row mb-5">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Year</label>
                                        <input type="number" class="form-control" name="year" id="collection_year" value="<?= date('Y') + 1 ?>" min="2000" max="2100">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Month</label>
                                        <select class="form-select" name="month" id="collection_month">
                                            <option value="Jan">Jan</option>
                                            <option value="Feb">Feb</option>
                                            <option value="Mar">Mar</option>
                                            <option value="Apr">Apr</option>
                                            <option value="May">May</option>
                                            <option value="Jun">Jun</option>
                                            <option value="Jul">Jul</option>
                                            <option value="Aug">Aug</option>
                                            <option value="Sep">Sep</option>
                                            <option value="Oct" selected>Oct</option>
                                            <option value="Nov">Nov</option>
                                            <option value="Dec">Dec</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Target refs</label>
                                        <input type="number" class="form-control" name="target_refs" min="0" placeholder="30">
                                    </div>
                                </div>

                                <!-- Occasion / Colour -->
                                <div class="row mb-5">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Occasion</label>
                                        <select class="form-select" name="occasion">
                                            <option>Navratri / Garba</option>
                                            <option>Bridal</option>
                                            <option>Diwali</option>
                                            <option>Eid</option>
                                            <option>Wedding Season</option>
                                            <option>Summer</option>
                                            <option>General</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Colour</label>
                                        <input type="color" class="form-control form-control-color w-100" name="colour" id="collection_colour" value="#C0392B" style="height:38px;">
                                    </div>
                                </div>

                                <!-- Brief -->
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Brief</label>
                                    <textarea class="form-control" name="brief" rows="3" placeholder="Focus areas, work types, colour direction..."></textarea>
                                </div>

                            </form>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-warning text-dark fw-bold" id="createCollectionBtn">
                                <i class="bi bi-check-lg me-1"></i> Create
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <style>
                #name_error {
                    display: none;
                }
                .is-invalid ~ #name_error,
                .is-invalid + #name_error {
                    display: block;
                }
                .ref-type-btn {
                    border: 1px solid #989292 !important;
                    border-radius: 5%;
                }
                .collection-btn{
                    border: 1px solid #989292 !important;
                    border-radius: 5%;
                }
                .ref-type-btn.selected,
                .collection-btn.selected {
                    background: #B47A1A;
                    border-color: #B47A1A;
                    color: #fff;
                    font-weight: 500;
                }
                .ref-card .ref-thumb {
                    height: 180px;
                    background: linear-gradient(135deg, #b03a2e, #d4a017);
                    border-radius: 0.475rem 0.475rem 0 0;
                }
                .ref-card .ref-icon {
                    font-size: 2.5rem;
                }
                .add-ref-card {
                    border: 2px dashed #d9d9d9;
                    background: #fafafa;
                }
                .add-ref-card:hover {
                    border-color: #f1bb47;
                    background: #fff9ef;
                }
            </style>
        </div>
    </div>

<?php include("includes/footer.php"); ?>
</div>

<script src="<?= $site_path ?>/assets/plugins/global/plugins.bundle.js"></script>
<script src="<?= $site_path ?>/assets/js/scripts.bundle.js"></script>
<script src="<?= $site_path ?>/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<script>
    $(document).ready(function () {
        $(document).on('click', '[data-tab="timeline"]', function (e) {
            e.preventDefault();
            $('.nav-link').removeClass('active');
            $(this).addClass('active');
            $('#browsePane, #timelinePane, #collectionsPane').hide();
            $('#timelinePane').show();
            loadTimeline(2025);
        });
        let filters = {}; // { year: '2025', garment: 'Lehenga', ... }

        // Single-select group click (year, season, garment, work, occasion, style, source)
        $(document).on('click', '.filter-btn', function () {
            let group = $(this).data('group');
            let value = $(this).data('value');

            // Toggle active styling within this group only
            $(`.filter-btn[data-group="${group}"]`).removeClass('btn-warning fw-bold').addClass('btn-light');
            $(this).removeClass('btn-light').addClass('btn-warning fw-bold');

            if (value === '') {
                delete filters[group];
            } else {
                filters[group] = value;
            }

            loadReferences();
        });

        // Sort dropdown
        $(document).on('change', '.filter-select', function () {
            let group = $(this).data('group');
            filters[group] = $(this).val();
            loadReferences();
        });

        // Clear all
        $('#clearAllFiltersBtn').on('click', function () {
            filters = {};
            $('.filter-btn').removeClass('btn-warning fw-bold').addClass('btn-light');
            $('.filter-btn[data-value=""]').removeClass('btn-light').addClass('btn-warning fw-bold');
            loadReferences();
        });

        // Remove a single badge
        $(document).on('click', '.remove-filter-badge', function () {
            let group = $(this).data('group');
            delete filters[group];

            $(`.filter-btn[data-group="${group}"]`).removeClass('btn-warning fw-bold').addClass('btn-light');
            $(`.filter-btn[data-group="${group}"][data-value=""]`).removeClass('btn-light').addClass('btn-warning fw-bold');

            loadReferences();
        });

        function loadReferences() {
            $.ajax({
                url: '<?php echo $site_path; ?>/ajax/ajax-fetch_ref_library',
                method: 'GET',
                data: filters,
                dataType: 'json',
                beforeSend: function () {
                    $('#refCardsGrid').html('<div class="col-12 text-center py-5 text-muted">Loading...</div>');
                },
                success: function (res) {
                    renderCards(res.refs);
                    renderBadges();
                    $('#resultsSummary').text(`Showing ${res.refs.length} of ${res.total} references`);
                },
                error: function () {
                    $('#refCardsGrid').html('<div class="col-12 text-center py-5 text-danger">Failed to load references</div>');
                }
            });
        }

        function renderCards(refs) {
            let html = '';
            refs.forEach(function (ref) {
                let tagsHtml = '';
                let tagsArr = Array.isArray(ref.tags) ? ref.tags : (ref.tags ? ref.tags.split(',') : []);

                if (tagsArr) {
                    tagsArr.forEach(function (tag, i) {
                        let cls = i === 0 ? 'bg-light-warning text-warning' : 'bg-light-primary text-primary';
                        tagsHtml += `<span class="badge ${cls}">${tag}</span>`;
                    });
                }


                html += `
            <div class="col-md-2">
                <div class="card h-100 ref-card">
                    <div class="ref-thumb d-flex align-items-center justify-content-center">
                        <img src="${ref.image}" alt="${ref.title}">
                    </div>
                    <div class="card-body">
                        <div class="text-muted small fw-bold mb-1">${ref.code}</div>
                        <div class="fw-bold mb-2">${ref.title}</div>
                        <div class="d-flex flex-wrap gap-1 mb-2">${tagsHtml}</div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small text-muted">
                                <i class="bi bi-circle-fill text-danger small"></i>
                                ${ref.count > 1 ? '×' + ref.count : ''}
                            </span>
                            <span class="text-warning small">${'★'.repeat(ref.rating)}</span>
                        </div>
                    </div>
                </div>
            </div>`;
            });

            // Add reference card always at the end
            html += `
        <div class="col-md-2">
            <div class="card h-100 d-flex align-items-center justify-content-center add-ref-card" style="min-height:260px; cursor:pointer;" data-bs-toggle="modal" data-bs-target="#addReferenceModal">
                <div class="text-center text-muted">
                    <i class="bi bi-plus-circle fs-1 d-block mb-2"></i>
                    Add reference
                </div>
            </div>
        </div>`;

            $('#refCardsGrid').html(html);
        }

        function renderBadges() {
            let html = '';
            $.each(filters, function (group, value) {
                let label = group.charAt(0).toUpperCase() + group.slice(1);
                html += `
            <span class="badge bg-light-warning text-warning fw-bold px-3 py-2">
                ${label}: ${value}
                <i class="bi bi-x ms-1 remove-filter-badge" data-group="${group}" style="cursor:pointer;"></i>
            </span>`;
            });
            $('#activeFilterBadges').html(html);
        }

        // Initial load on page render
        loadReferences();
        $('.ref-type-btn').on('click', function () {
            $('.ref-type-btn').removeClass('selected');
            $(this).addClass('selected');
            $('#reference_type_input').val($(this).data('value'));
        });

        // Multi select — Collections  
        $('.collection-btn').on('click', function () {
            $(this).toggleClass('selected');
            const vals = $('.collection-btn.selected').map((_, b) => $(b).data('value')).get();
            $('#collections_input').val(vals.join(','));
            //updateCollectionsInput();
        });

        function updateCollectionsInput() {
            let selected = [];
            $('.collection-btn.btn-warning').each(function () {
                selected.push($(this).data('value'));
            });
            $('#collections_input').val(selected.join(','));
        }

        // Upload dropzone click to trigger file input
        $('#refUploadDropzone').on('click', function (e) {
            if ($(e.target).is('#refImageInput'))
                return;
            $('#refImageInput').click();
        });

        $('#refImageInput').on('change', function () {
            let file = this.files[0];
            if (file) {
                let reader = new FileReader();
                reader.onload = function (e) {
                    $('#refPreviewImg').attr('src', e.target.result);
                    $('#refUploadPlaceholder').addClass('d-none');
                    $('#refUploadPreview').removeClass('d-none');
                };
                reader.readAsDataURL(file);
            }
        });

        // Submit handler
        $('#addReferenceSubmitBtn').on('click', function () {
            if (!$('#addReferenceForm')[0].checkValidity()) {
                $('#addReferenceForm')[0].reportValidity();
                return;
            }
            if (!$('#reference_type_input').val()) {
                alert('Please select a reference type');
                return;
            }

            let formData = new FormData($('#addReferenceForm')[0]);
            formData.append('status', 'active');
            formData.append('action', 'add-ref-library');

            $.ajax({
                url: '<?php echo $site_path; ?>/ajax/ajax-save-reference', // your backend endpoint
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (res) {
                    let data = typeof res === 'string' ? JSON.parse(res) : res;
                    if (data.success) {
                        $('#addReferenceModal').modal('hide');
                        location.reload(); // or dynamically prepend the new card
                    } else {
                        alert(data.message || 'Something went wrong');
                    }
                },
                error: function () {
                    alert('Server error while saving reference');
                }
            });
        });

        // Draft handler
        $('#saveDraftBtn').on('click', function () {
            let formData = new FormData($('#addReferenceForm')[0]);
            formData.append('status', 'draft');

            $.ajax({
                url: 'save_reference.php',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (res) {
                    $('#addReferenceModal').modal('hide');
                    alert('Saved as draft');
                }
            });
        });
        // Auto-generate ID preview as user types name + selects year
        function updateCollectionCode() {
            let name = $('#collection_name').val().trim();
            let year = $('#collection_year').val().trim();

            if (!name) {
                $('#collection_id_display').val('—');
                $('#collection_code').val('');
                return;
            }

            // Take first letters of significant words, e.g. "Navratri 2026" -> "NAV"
            let words = name.replace(/[0-9]/g, '').trim().split(/\s+/);
            let prefix = words[0] ? words[0].substring(0, 3).toUpperCase() : 'COL';
            let yearShort = year ? year.toString().slice(-2) : '';

            let code = prefix + yearShort;
            $('#collection_id_display').val(code);
            $('#collection_code').val(code);
        }

        $('#collection_name, #collection_year').on('input', updateCollectionCode);

        // Reset form & defaults when modal opens
        $('#newCollectionModal').on('show.bs.modal', function () {
            $('#newCollectionForm')[0].reset();
            $('.is-invalid').removeClass('is-invalid');
            $('#collection_year').val(<?= date('Y') + 1 ?>);
            updateCollectionCode();
        });

        // Validation + Submit
        $('#createCollectionBtn').on('click', function () {
            let isValid = true;
            let name = $('#collection_name').val().trim();

            if (!name) {
                $('#collection_name').addClass('is-invalid');
                $('#name_error').show();
                isValid = false;
            } else {
                $('#collection_name').removeClass('is-invalid');
            }

            if (!isValid)
                return;

            let payload = {
                name: name,
                code: $('#collection_code').val(),
                year: $('#collection_year').val(),
                month: $('#collection_month').val(),
                target_refs: $('input[name="target_refs"]').val(),
                occasion: $('select[name="occasion"]').val(),
                colour: $('#collection_colour').val(),
                brief: $('textarea[name="brief"]').val(),
                action: 'add-collection'
            };

            $('#createCollectionBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Saving...');

            $.ajax({
                url: '<?php echo $site_path; ?>/ajax/ajax-save-reference',
                method: 'POST',
                data: payload,
                dataType: 'json',
                success: function (res) {
                    if (res.success) {
                        $('#newCollectionModal').modal('hide');
                        location.reload(); // or dynamically update collections list/count
                    } else {
                        alert(res.message || 'Something went wrong');
                    }
                },
                error: function () {
                    alert('Server error while saving collection');
                },
                complete: function () {
                    $('#createCollectionBtn').prop('disabled', false).html('<i class="bi bi-check-lg me-1"></i> Create');
                }
            });
        });
    });
    
    function loadTimeline(year) {
        $.ajax({
            url: '<?php echo $site_path; ?>/ajax/get-timeline-data',
            method: 'GET',
            data: { year: year },
            dataType: 'json',
            success: function (response) {
                renderQuarters(response);
            },
            error: function (xhr) {
                console.error('Failed to load timeline:', xhr.responseText);
            }
        });
    }

function renderQuarters(quarters) {
    let grid = document.getElementById('quarterGrid');
    grid.innerHTML = '';
    document.getElementById('detailPanel').style.display = 'none';

    quarters.forEach(q => {
        let total = q.months.reduce((s, m) => s + m.count, 0);
        let col = document.createElement('div');
        col.className = 'col-md-3';
        col.innerHTML = `
            <div class="card h-100 border-top-3" style="border-top:3px solid ${q.color};">
                <div class="card-body">
                    <div class="text-muted small">${q.label} · ${q.year}</div>
                    <div class="fw-bold">${q.range}</div>
                    <div class="text-muted small mb-2">${q.themes}</div>
                    <div class="fw-bold mb-2">${total} reference${total === 1 ? '' : 's'}</div>
                    <div class="progress mb-2" style="height:4px;">
                        <div class="progress-bar" style="width:${total ? 100 : 0}%; background:${q.color};"></div>
                    </div>
                    ${q.campaign ? `<span class="badge bg-light-primary text-primary mb-2">${q.campaign.name} (${q.campaign.done}/${q.campaign.of})</span>` : ''}
                    <div class="months-list mt-2"></div>
                </div>
            </div>`;
        let monthsList = col.querySelector('.months-list');
        q.months.forEach(m => {
            let row = document.createElement('div');
            row.className = 'd-flex justify-content-between border-top py-2';
            row.style.cursor = m.refs ? 'pointer' : 'default';
            row.innerHTML = `<span class="text-muted small">${m.name}</span><span>${m.count || '—'}</span>`;
            if (m.refs) row.addEventListener('click', () => showDetail(q, m));
            monthsList.appendChild(row);
        });
        grid.appendChild(col);
    });
}
</script>