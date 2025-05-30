<?php
// API base URL
$apiUrl = "https://busmanagementapi.onrender.com/GetAllRoutes";

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
    :root {
        --primary-color: #1a237e;
        --secondary-color: #0d47a1;
        --accent-color: #1976d2;
        --light-bg: #e3f2fd;
        --dark-text: #e3f2fd;
        --light-text: #bbdefb;
        --border-radius: 8px;
        --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        --transition: all 0.3s ease;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #0d47a1;
        color: var(--dark-text);
    }

    /* Header and Navigation */
    .navbar {
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        background-color: var(--primary-color) !important;
    }

    /* Main Content */
    .main-container {
        margin-top: 80px;
        padding: 15px;
    }

    .card {
        border: none;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        transition: var(--transition);
        margin-bottom: 24px;
        background-color: var(--secondary-color);
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
    }

    .card-header {
        background-color: var(--primary-color);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        font-weight: 600;
        padding: 16px 20px;
        color: var(--light-text);
        border-radius: var(--border-radius) var(--border-radius) 0 0 !important;
    }

    /* Table Styling */
    .table {
        margin-bottom: 0;
        color: var(--dark-text);
    }

    .table thead th {
        background-color: var(--primary-color);
        color: var(--light-text);
        font-weight: 500;
        border: none;
        padding: 12px 16px;
    }

    .table tbody tr {
        transition: var(--transition);
        background-color: rgba(255, 255, 255, 0.05);
    }

    .table tbody tr:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }

    .table td {
        padding: 14px 16px;
        vertical-align: middle;
        border-top: 1px solid rgba(255, 255, 255, 0.05);
    }

    /* Action Buttons */
    .action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        transition: var(--transition);
    }

    .action-btn:hover {
        background-color: rgba(255, 255, 255, 0.1);
        transform: scale(1.1);
    }

    .view-btn { color: #bbdefb; }
    .edit-btn { color: #ffd54f; }
    .delete-btn { color: #ff8a65; }

    /* Filter Sidebar */
    .filter-container {
        background-color: var(--secondary-color);
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        padding: 20px;
        margin-bottom: 24px;
    }

    .filter-title {
        color: var(--light-text);
        font-weight: 600;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .filter-title i {
        font-size: 1.2rem;
    }

    .form-label {
        font-weight: 500;
        color: var(--light-text);
        margin-bottom: 8px;
    }

    .form-control {
        background-color: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: var(--dark-text);
        border-radius: var(--border-radius);
        padding: 10px 14px;
        transition: var(--transition);
    }

    .form-control:focus {
        background-color: rgba(255, 255, 255, 0.15);
        border-color: var(--accent-color);
        box-shadow: 0 0 0 3px rgba(25, 118, 210, 0.3);
        color: white;
    }

    .form-control::placeholder {
        color: rgba(255, 255, 255, 0.5);
    }

    /* Buttons */
    .btn-primary {
        background-color: var(--accent-color);
        border: none;
        border-radius: var(--border-radius);
        padding: 10px 20px;
        font-weight: 500;
        transition: var(--transition);
    }

    .btn-primary:hover {
        background-color: #1565c0;
        transform: translateY(-1px);
    }

    .btn-success {
        background-color: #00acc1;
        border: none;
        border-radius: var(--border-radius);
        padding: 10px 20px;
        font-weight: 500;
        transition: var(--transition);
        box-shadow: var(--box-shadow);
    }

    .btn-success:hover {
        background-color: #00838f;
        transform: translateY(-1px);
    }

    .btn-outline-secondary {
        border-color: rgba(255, 255, 255, 0.2);
        color: var(--light-text);
    }

    .btn-outline-secondary:hover {
        background-color: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.3);
    }

    /* Pagination */
    .pagination .page-item .page-link {
        background-color: rgba(255, 255, 255, 0.05);
        color: var(--light-text);
        border: none;
        margin: 0 4px;
        border-radius: var(--border-radius) !important;
        transition: var(--transition);
    }

    .pagination .page-item.active .page-link {
        background-color: var(--accent-color);
        color: white;
    }

    .pagination .page-item:not(.active) .page-link:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }

    /* Empty State */
    .empty-state {
        padding: 40px 0;
        text-align: center;
        color: var(--light-text);
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 16px;
        color: rgba(255, 255, 255, 0.2);
    }

    /* Responsive Adjustments */
    @media (max-width: 992px) {
        .main-container {
            margin-top: 70px;
            padding: 10px;
        }
        
        .filter-container {
            position: static !important;
            margin-bottom: 20px;
        }
        
        .card-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }
        
        .card-header .btn-success {
            width: 100%;
        }
    }

    @media (max-width: 768px) {
        .main-container {
            margin-top: 60px;
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
        }

        .table thead {
            display: none;
        }

        .table tr {
            display: block;
            margin-bottom: 16px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            background-color: var(--secondary-color);
            padding: 10px;
        }

        .table td {
            display: block;
            padding: 8px 10px;
            text-align: right;
            border: none;
            position: relative;
            padding-left: 50%;
        }

        .table td:before {
            content: attr(data-label);
            position: absolute;
            left: 10px;
            width: 45%;
            padding-right: 10px;
            font-weight: 600;
            color: var(--light-text);
            text-align: left;
        }

        .table td:last-child {
            display: flex;
            justify-content: flex-end;
            padding-top: 10px;
            padding-bottom: 5px;
            padding-left: 10px;
        }
        
        .table td:last-child:before {
            display: none;
        }

        .action-btn {
            width: 36px;
            height: 36px;
            margin: 0 5px;
        }
        
        .pagination .page-item .page-link {
            padding: 8px 12px;
            margin: 0 2px;
            font-size: 0.9rem;
        }
    }

    @media (max-width: 576px) {
        .main-container {
            margin-top: 60px;
            padding: 8px;
        }
        
        .card-header h5 {
            font-size: 1.2rem;
        }
        
        .filter-title {
            font-size: 1.1rem;
            margin-bottom: 15px;
        }
        
        .form-control {
            padding: 8px 12px;
            font-size: 0.9rem;
        }
        
        .btn {
            padding: 8px 16px;
            font-size: 0.9rem;
        }
        
        .table td {
            padding-left: 40%;
            font-size: 0.9rem;
        }
        
        .table td:before {
            width: 40%;
            font-size: 0.85rem;
        }
        
        .empty-state h5 {
            font-size: 1.1rem;
        }
        
        .empty-state p {
            font-size: 0.9rem;
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
                                        <tr>
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