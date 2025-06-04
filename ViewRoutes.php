<?php

$config = include('config.php');

$apiBaseUrl = $config['api_base_url'];

$apiUrl = $apiBaseUrl . "GetAllRoutes";

// Fetch all routes via cURL
$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
$response = curl_exec($ch);
curl_close($ch);

$routes = json_decode($response, true);
if (!is_array($routes)) {
    $routes = [];
}

// Apply filters
$filteredRoutes = array_filter($routes, function($route) {
    $codeFilter = isset($_GET['code']) ? strtolower(trim($_GET['code'])) : '';
    $fromFilter = isset($_GET['from']) ? strtolower(trim($_GET['from'])) : '';
    $toFilter   = isset($_GET['to']) ? strtolower(trim($_GET['to'])) : '';

    $codeMatch = empty($codeFilter) || strpos(strtolower($route['code']), $codeFilter) !== false;
    $fromMatch = empty($fromFilter) || strpos(strtolower($route['from']), $fromFilter) !== false;
    $toMatch   = empty($toFilter)   || strpos(strtolower($route['to']), $toFilter) !== false;

    return $codeMatch && $fromMatch && $toMatch;
});

// Pagination
$limit = 10;
$totalRoutes = count($filteredRoutes);
$totalPages = max(1, ceil($totalRoutes / $limit));
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
if ($page > $totalPages) $page = $totalPages;
$start = ($page - 1) * $limit;
$routesPage = array_slice($filteredRoutes, $start, $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Bus Routes</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        /* RCB Color Palette */
:root {
    --rcb-red: #E21B4C;
    --rcb-dark-red: #56042C;
    --rcb-black: #000000;
    --rcb-gold: #FFD700; /* Gold */
    --rcb-light-gold: rgba(255, 215, 0, 0.7); /* Lighter, semi-transparent gold */
    --rcb-text-light: #FFFFFF; /* Pure white for maximum visibility */
    --rcb-text-dark: #333333; /* Dark text for contrast on light elements */

    /* UI Variables based on RCB palette */
    --primary-color: var(--rcb-dark-red); /* Darker red for main elements */
    --secondary-color: var(--rcb-black); /* Black for cards/backgrounds */
    --accent-color: var(--rcb-red); /* Bright red for accents/buttons */
    --light-bg: #2a2a2a; /* Slightly lighter black for subtle contrast */
    --dark-text: var(--rcb-text-light); /* Main text color (now white) */
    --light-text: var(--rcb-gold); /* Highlight text color */
    --border-color: rgba(255, 215, 0, 0.2); /* Subtle gold border */

    --border-radius: 12px;
    --box-shadow: 0 6px 18px rgba(0, 0, 0, 0.3); /* Enhanced shadow */
    --transition: all 0.3s ease;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, var(--rcb-dark-red), var(--rcb-black)); /* RCB gradient */
    color: var(--dark-text); /* Now explicitly white */
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

/* Header and Navigation */
.navbar {
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4); /* Stronger shadow */
    background-color: var(--secondary-color) !important; /* Black navbar */
    color: var(--light-text); /* Gold text */
}

.navbar .nav-link, .navbar .navbar-brand {
    color: var(--light-text) !important; /* Ensure nav links are gold */
}

/* Main Content */
.main-container {
    margin-top: 80px;
    padding: 20px; /* Slightly more padding */
}

.card {
    border: 1px solid var(--border-color); /* Subtle gold border */
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    transition: var(--transition);
    margin-bottom: 28px; /* More spacing between cards */
    background-color: var(--secondary-color); /* Black card background */
    overflow: hidden; /* Ensures rounded corners apply to children */
}

.card:hover {
    transform: translateY(-4px); /* More pronounced lift */
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4); /* Stronger shadow on hover */
}

.card-header {
    background: linear-gradient(90deg, var(--rcb-red), var(--rcb-dark-red)); /* Red to dark red gradient */
    border-bottom: 1px solid var(--rcb-dark-red); /* Dark red border */
    font-weight: 600;
    padding: 18px 25px; /* More padding */
    color: var(--rcb-text-light); /* Light text (now white) */
    border-radius: var(--border-radius) var(--border-radius) 0 0 !important;
    font-size: 1.3rem; /* Larger header text */
}

/* Table Styling - Updated for visibility */
.table {
    margin-bottom: 0;
    color: white !important; /* Force white text for all table content */
    background-color: var(--secondary-color); /* Ensure table background matches card */
}

.table thead th {
    background-color: var(--primary-color); /* Dark red header */
    color: var(--rcb-text-light) !important; /* Light text (now white) */
    font-weight: 500;
    border: none;
    padding: 14px 20px; /* More padding */
    text-transform: uppercase; /* Uppercase headers */
    font-size: 0.95rem;
}

.table tbody tr {
    transition: var(--transition);
    background-color: var(--secondary-color); /* Black rows */
    border-bottom: 1px solid rgba(255, 215, 0, 0.1); /* Subtle gold line between rows */
}

.table tbody tr:last-child {
    border-bottom: none; /* No border for the last row */
}

.table tbody tr:hover {
    background-color: rgba(255, 215, 0, 0.05); /* Subtle gold highlight on hover */
    cursor: pointer; /* Indicate clickability */
}

.table td {
    padding: 16px 20px; /* More padding */
    vertical-align: middle;
    border-top: none; /* Remove default bootstrap border */
    color: white !important; /* Force white text */
}

/* Specific column styling */
.table td .fw-semibold {
    color: var(--rcb-gold) !important; /* Gold for route code */
}

.table td:nth-child(3), /* From column */
.table td:nth-child(4) { /* To column */
    color: var(--rcb-light-gold) !important; /* Lighter gold for better visibility */
}

/* Mobile view data labels */
.table td:before {
    color: var(--rcb-gold) !important; /* Gold labels */
}

/* Action Buttons */
.action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 38px; /* Larger buttons */
    height: 38px;
    border-radius: 50%;
    transition: var(--transition);
    font-size: 1.2rem; /* Larger icons */
    margin: 0 4px;
}

.action-btn:hover {
    background-color: rgba(255, 215, 0, 0.15); /* Gold highlight on hover */
    transform: scale(1.15); /* More pronounced scale */
}

.view-btn { color: var(--rcb-gold); } /* Gold for view */
.edit-btn { color: #f8c000; } /* Slightly different gold for edit */
.delete-btn { color: var(--rcb-red); } /* Red for delete */

/* Filter Sidebar */
.filter-container {
    background-color: var(--secondary-color); /* Black background */
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    padding: 25px; /* More padding */
    margin-bottom: 28px;
    border: 1px solid var(--border-color); /* Subtle gold border */
}

.filter-title {
    color: var(--rcb-gold); /* Gold title */
    font-weight: 600;
    margin-bottom: 25px; /* More spacing */
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 1.4rem; /* Larger title */
}

.filter-title i {
    font-size: 1.5rem;
    color: var(--rcb-red); /* Red icon */
}

.form-label {
    font-weight: 500;
    color: white; /* Explicitly white for labels */
    margin-bottom: 10px; /* More spacing */
    font-size: 1.05rem;
}

.form-control {
    background-color: rgba(255, 215, 0, 0.05); /* Very light transparent gold */
    border: 1px solid rgba(255, 215, 0, 0.2); /* Transparent gold border */
    color: white; /* Explicitly white for input text */
    border-radius: var(--border-radius);
    padding: 12px 16px; /* More padding */
    transition: var(--transition);
    font-size: 1rem;
}

.form-control:focus {
    background-color: rgba(255, 215, 0, 0.1); /* Slightly more opaque on focus */
    border-color: var(--rcb-gold); /* Solid gold border on focus */
    box-shadow: 0 0 0 4px rgba(255, 215, 0, 0.3); /* Gold glow on focus */
    color: white; /* Ensure text stays white */
}

.form-control::placeholder {
    color: var(--rcb-gold); /* Solid gold placeholder */
}

/* Buttons */
.btn-primary {
    background-color: var(--rcb-red); /* RCB Red */
    border: none;
    border-radius: var(--border-radius);
    padding: 12px 25px; /* More padding */
    font-weight: 600;
    transition: var(--transition);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); /* Subtle shadow */
    color: white; /* White text */
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.btn-primary:hover {
    background-color: var(--rcb-dark-red); /* Darker red on hover */
    transform: translateY(-2px); /* Lift effect */
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
}

.btn-primary:active {
    transform: translateY(0);
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

.btn-success { /* Add New Route button - if uncommented */
    background-color: var(--rcb-gold); /* RCB Gold */
    border: none;
    border-radius: var(--border-radius);
    padding: 12px 25px;
    font-weight: 600;
    transition: var(--transition);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    color: var(--rcb-black); /* Black text for contrast */
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.btn-success:hover {
    background-color: var(--rcb-light-gold); /* Lighter gold on hover */
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
}

.btn-outline-secondary {
    border-color: rgba(255, 215, 0, 0.3); /* Transparent gold border */
    color: var(--rcb-gold); /* Gold text */
    background-color: transparent;
    border-radius: var(--border-radius);
    padding: 12px 25px;
    font-weight: 600;
    transition: var(--transition);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.btn-outline-secondary:hover {
    background-color: rgba(255, 215, 0, 0.1); /* Light transparent gold background */
    border-color: var(--rcb-gold); /* Solid gold border */
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
}

.btn-outline-secondary:active {
    transform: translateY(0);
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

/* Pagination */
.pagination .page-item .page-link {
    background-color: var(--secondary-color); /* Black background */
    color: var(--rcb-gold); /* Gold text */
    border: 1px solid rgba(255, 215, 0, 0.2); /* Subtle gold border */
    margin: 0 6px; /* More spacing */
    border-radius: var(--border-radius) !important;
    transition: var(--transition);
    padding: 10px 16px; /* More padding */
    font-weight: 500;
}

.pagination .page-item.active .page-link {
    background-color: var(--rcb-red); /* RCB Red for active page */
    color: white; /* White text */
    border-color: var(--rcb-red);
    box-shadow: 0 2px 8px rgba(226, 27, 76, 0.4); /* Red glow */
}

.pagination .page-item:not(.active) .page-link:hover {
    background-color: rgba(255, 215, 0, 0.1); /* Light transparent gold on hover */
    border-color: var(--rcb-gold);
}

/* Empty State */
.empty-state {
    padding: 50px 0; /* More padding */
    text-align: center;
    color: var(--rcb-text-light); /* Light text (now white) */
    background-color: var(--secondary-color); /* Black background */
    border-radius: var(--border-radius);
    box-shadow: inset 0 0 15px rgba(255, 215, 0, 0.05); /* Inner gold shadow */
}

.empty-state i {
    font-size: 4rem; /* Larger icon */
    margin-bottom: 20px;
    color: rgba(255, 215, 0, 0.3); /* Transparent gold icon */
}

.empty-state h5 {
    font-size: 1.5rem;
    color: var(--rcb-gold); /* Gold heading */
}

.empty-state p {
    font-size: 1.1rem;
    color: white; /* Explicitly white */
}

/* Responsive Adjustments */
@media (max-width: 992px) {
    .main-container {
        margin-top: 70px;
        padding: 15px;
    }
    
    .filter-container {
        position: static !important;
        margin-bottom: 25px;
        padding: 20px;
    }
    
    .card-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
        padding: 15px 20px;
        font-size: 1.2rem;
    }
    
    .card-header .btn-success {
        width: 100%;
        padding: 10px 20px;
    }
}

@media (max-width: 768px) {
    .main-container {
        margin-top: 60px;
        padding: 10px;
    }
    
    .row {
        flex-direction: column;
    }
    
    .col-lg-3, .col-lg-9 {
        width: 100%;
        max-width: 100%;
        padding-left: 0;
        padding-right: 0;
    }
    
    .filter-container {
        padding: 15px;
    }
    
    .table-responsive {
        border-radius: var(--border-radius);
        overflow: hidden;
        box-shadow: var(--box-shadow); /* Apply shadow to responsive table container */
    }

    .table thead {
        display: none;
    }

    .table tr {
        display: block;
        margin-bottom: 20px; /* More spacing */
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        background-color: var(--secondary-color);
        padding: 15px; /* More padding */
        border: 1px solid var(--border-color); /* Border for each row card */
    }

    .table td {
        display: block;
        padding: 10px 15px; /* Adjust padding */
        text-align: right;
        border: none;
        position: relative;
        padding-left: 55%; /* Adjust for label width */
        font-size: 1rem;
        color: white !important; /* Force white text */
    }

    .table td:before {
        content: attr(data-label);
        position: absolute;
        left: 15px; /* Align label to the left */
        width: 45%; /* Label width */
        padding-right: 10px;
        font-weight: 600;
        color: var(--rcb-gold) !important; /* Gold labels */
        text-align: left;
        font-size: 0.95rem;
    }

    .table td:last-child {
        display: flex;
        justify-content: flex-end;
        padding-top: 15px;
        padding-bottom: 10px;
        padding-left: 15px;
    }
    
    .table td:last-child:before {
        display: none;
    }

    .action-btn {
        width: 40px; /* Slightly larger for touch */
        height: 40px;
        margin: 0 6px;
    }
    
    .pagination .page-item .page-link {
        padding: 10px 14px;
        margin: 0 4px;
        font-size: 1rem;
    }
}

@media (max-width: 576px) {
    .main-container {
        margin-top: 60px;
        padding: 10px;
    }
    
    .card-header h5 {
        font-size: 1.1rem;
    }
    
    .filter-title {
        font-size: 1.2rem;
        margin-bottom: 20px;
    }
    
    .form-control {
        padding: 10px 14px;
        font-size: 0.95rem;
    }
    
    .btn {
        padding: 10px 20px;
        font-size: 0.95rem;
    }
    
    .table td {
        padding-left: 45%;
        font-size: 0.95rem;
    }
    
    .table td:before {
        width: 40%;
        font-size: 0.9rem;
    }
    
    .empty-state h5 {
        font-size: 1.3rem;
    }
    
    .empty-state p {
        font-size: 1.0rem; /* Adjusted for better visibility */
    }
}
</style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container main-container">
    <div class="row">
        <!-- Filter Sidebar - Left Column -->
        <div class="col-lg-3 mb-4">
            <div class="filter-container sticky-top" style="top: 90px;">
                <h5 class="filter-title">
                    <i class="bi bi-funnel"></i> Filter Routes
                </h5>
                <form method="GET" autocomplete="off">
                    <div class="mb-3">
                        <label for="filterCode" class="form-label">Route Code</label>
                        <input id="filterCode" type="text" name="code" class="form-control" 
                               value="<?= isset($_GET['code']) ? htmlspecialchars($_GET['code']) : '' ?>" 
                               placeholder="Enter route code" />
                    </div>
                    <div class="mb-3">
                        <label for="filterFrom" class="form-label">From</label>
                        <input id="filterFrom" type="text" name="from" class="form-control" 
                               value="<?= isset($_GET['from']) ? htmlspecialchars($_GET['from']) : '' ?>" 
                               placeholder="Starting location" />
                    </div>
                    <div class="mb-3">
                        <label for="filterTo" class="form-label">To</label>
                        <input id="filterTo" type="text" name="to" class="form-control" 
                               value="<?= isset($_GET['to']) ? htmlspecialchars($_GET['to']) : '' ?>" 
                               placeholder="Destination" />
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-filter me-1"></i> Apply Filters
                        </button>
                        <a href="ViewRoutes.php" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Main Content - Right Column -->
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Bus Routes</h5>
                    <!-- <a href="AddRoute.php" class="btn btn-success">
                        <i class="bi bi-plus-lg me-1"></i> Add New Route
                    </a> -->
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="d-none">ID</th>
                                    <th>Route Code</th>
                                    <th>From</th>
                                    <th>To</th>
                                    <th style="width: 120px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($routesPage) === 0): ?>
                                    <tr>
                                        <td colspan="5" class="empty-state">
                                            <i class="bi bi-exclamation-circle"></i>
                                            <h5>No routes found</h5>
                                            <p class="mb-0">Try adjusting your filters or add a new route</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($routesPage as $route): ?>
                                        <tr data-href="RouteDetails.php?id=<?= $route['id'] ?>">
                                            <td class="d-none" data-label="ID"><?= htmlspecialchars($route['id']) ?></td>
                                            <td data-label="Route Code">
                                                <span class="fw-semibold"><?= htmlspecialchars($route['code']) ?></span>
                                            </td>
                                            <td data-label="From"><?= htmlspecialchars($route['from']) ?></td>
                                            <td data-label="To"><?= htmlspecialchars($route['to']) ?></td>
                                            <td data-label="Actions">
                                                <a href="RouteDetails.php?id=<?= $route['id'] ?>" class="action-btn view-btn" title="View details">
                                                    <i class="bi bi-eye-fill"></i>
                                                </a>
                                                <!-- <a href="EditRoute.php?id=<?= $route['id'] ?>" class="action-btn edit-btn" title="Edit">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <a href="DeleteRoute.php?id=<?= $route['id'] ?>" class="action-btn delete-btn" title="Delete" 
                                                   onclick="return confirm('Are you sure you want to delete this route?');">
                                                    <i class="bi bi-trash-fill"></i>
                                                </a> -->
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <nav aria-label="Route pagination" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item<?= $page <= 1 ? ' disabled' : '' ?>">
                            <a class="page-link" href="?<?php
                                $params = $_GET;
                                $params['page'] = $page - 1;
                                echo http_build_query($params);
                            ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>

                        <?php
                        // Show max 7 page links around current page
                        $startPage = max(1, $page - 3);
                        $endPage = min($totalPages, $page + 3);
                        for ($i = $startPage; $i <= $endPage; $i++): 
                        ?>
                            <li class="page-item<?= $i === $page ? ' active' : '' ?>">
                                <a class="page-link" href="?<?php
                                    $params = $_GET;
                                    $params['page'] = $i;
                                    echo http_build_query($params);
                                ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <li class="page-item<?= $page >= $totalPages ? ' disabled' : '' ?>">
                            <a class="page-link" href="?<?php
                                $params = $_GET;
                                $params['page'] = $page + 1;
                                echo http_build_query($params);
                            ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Add active class to current nav item
    document.addEventListener('DOMContentLoaded', function() {
        // Make table rows clickable
        document.querySelectorAll('tbody tr[data-href]').forEach(row => {
            row.addEventListener('click', () => {
                window.location.href = row.dataset.href;
            });
        });
    });
</script>
</body>
</html>
