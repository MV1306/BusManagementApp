<?php
require_once 'AdminAuth.php';

checkAuth();

$config = include('config.php');

// API base URL from config
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
    <title>Admin - View Routes</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
    :root {
        --primary-color: #4361ee;
        --secondary-color: #3f37c9;
        --accent-color: #4895ef;
        --success-color: #4cc9f0;
        --danger-color: #f72585;
        --warning-color: #f8961e;
        --info-color: #43aa8b;
        --light-bg: #f8f9fa;
        --dark-bg: #212529;
        --text-light: #f8f9fa;
        --text-dark: #212529;
        --border-radius: 12px;
        --box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        background-color: #f5f7fa;
        color: var(--text-dark);
        line-height: 1.6;
        padding-top: 80px;
    }

    /* Navbar Styling */
    .navbar {
        background-color: var(--primary-color) !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        padding: 0.75rem 1rem;
    }

    .navbar-brand {
        font-weight: 700;
        color: white !important;
        font-size: 1.25rem;
    }

    .navbar-nav .nav-link {
        color: rgba(255, 255, 255, 0.9) !important;
        font-weight: 500;
        padding: 0.5rem 1rem;
        transition: var(--transition);
    }

    .navbar-nav .nav-link:hover,
    .navbar-nav .nav-link.active {
        color: white !important;
        transform: translateY(-2px);
    }

    .navbar-toggler {
        border-color: rgba(255, 255, 255, 0.1);
    }

    .navbar-toggler-icon {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.9%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
    }

    /* Main Content */
    .main-container {
        padding: 20px;
    }

    .card {
        border: none;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        transition: var(--transition);
        margin-bottom: 24px;
        background-color: white;
        overflow: hidden;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.15);
    }

    .card-header {
        background-color: white;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        font-weight: 600;
        padding: 20px;
        color: var(--primary-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Filter Section */
    .filter-section {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        padding: 20px;
        margin-bottom: 30px;
        color: white;
    }

    .filter-title {
        font-weight: 700;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        color: white;
    }

    .filter-title i {
        font-size: 1.4rem;
    }

    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
    }

    .form-label {
        font-weight: 600;
        margin-bottom: 8px;
        color: rgba(255, 255, 255, 0.9);
    }

    .form-control {
        background-color: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: white;
        border-radius: 8px;
        padding: 10px 15px;
        transition: var(--transition);
    }

    .form-control:focus {
        background-color: rgba(255, 255, 255, 0.25);
        border-color: white;
        box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.2);
        color: white;
    }

    .form-control::placeholder {
        color: rgba(255, 255, 255, 0.6);
    }

    /* Buttons */
    .btn-primary {
        background-color: white;
        color: var(--primary-color);
        border: none;
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 600;
        transition: var(--transition);
    }

    .btn-primary:hover {
        background-color: rgba(255, 255, 255, 0.9);
        color: var(--primary-color);
        transform: translateY(-2px);
    }

    .btn-outline-light {
        border-color: rgba(255, 255, 255, 0.5);
        color: white;
    }

    .btn-outline-light:hover {
        background-color: rgba(255, 255, 255, 0.1);
        border-color: white;
    }

    .btn-success {
        background-color: var(--success-color);
        border: none;
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 600;
        transition: var(--transition);
    }

    .btn-success:hover {
        background-color: #3ab7d8;
        transform: translateY(-2px);
    }

    /* Table Styling */
    .table {
        margin-bottom: 0;
        color: var(--text-dark);
    }

    .table thead th {
        background-color: #f8f9fa;
        color: var(--primary-color);
        font-weight: 600;
        border: none;
        padding: 15px 20px;
        border-bottom: 2px solid rgba(0, 0, 0, 0.05);
    }

    .table tbody tr {
        transition: var(--transition);
    }

    .table tbody tr:hover {
        background-color: rgba(67, 97, 238, 0.05);
    }

    .table td {
        padding: 15px 20px;
        vertical-align: middle;
        border-top: 1px solid rgba(0, 0, 0, 0.03);
    }

    /* Action Buttons */
    .action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        transition: var(--transition);
        margin: 0 3px;
    }

    .action-btn:hover {
        transform: scale(1.1);
    }

    .view-btn { 
        color: var(--info-color);
        background-color: rgba(67, 170, 139, 0.1);
    }
    .view-btn:hover { background-color: rgba(67, 170, 139, 0.2); }

    .edit-btn { 
        color: var(--warning-color);
        background-color: rgba(248, 150, 30, 0.1);
    }
    .edit-btn:hover { background-color: rgba(248, 150, 30, 0.2); }

    .delete-btn { 
        color: var(--danger-color);
        background-color: rgba(247, 37, 133, 0.1);
    }
    .delete-btn:hover { background-color: rgba(247, 37, 133, 0.2); }

    /* Pagination */
    .pagination .page-item .page-link {
        background-color: white;
        color: var(--primary-color);
        border: 1px solid rgba(0, 0, 0, 0.1);
        margin: 0 5px;
        border-radius: 8px !important;
        transition: var(--transition);
        font-weight: 600;
    }

    .pagination .page-item.active .page-link {
        background-color: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }

    .pagination .page-item:not(.active) .page-link:hover {
        background-color: rgba(67, 97, 238, 0.1);
        border-color: rgba(67, 97, 238, 0.2);
    }

    /* Empty State */
    .empty-state {
        padding: 50px 0;
        text-align: center;
        color: #6c757d;
    }

    .empty-state i {
        font-size: 3.5rem;
        margin-bottom: 20px;
        color: #dee2e6;
    }

    .empty-state h5 {
        color: #495057;
        font-weight: 600;
    }

    /* Responsive Adjustments */
    @media (max-width: 992px) {
        body {
            padding-top: 70px;
        }
        
        .main-container {
            padding: 15px;
        }
        
        .filter-grid {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 768px) {
        body {
            padding-top: 60px;
        }
        
        .filter-grid {
            grid-template-columns: 1fr;
        }
        
        .card-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }
        
        .card-header .btn-success {
            width: 100%;
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
            margin-bottom: 15px;
            border-radius: var(--border-radius);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            background-color: white;
            padding: 10px;
        }

        .table td {
            display: block;
            padding: 10px 15px;
            text-align: right;
            border: none;
            position: relative;
            padding-left: 50%;
        }

        .table td:before {
            content: attr(data-label);
            position: absolute;
            left: 15px;
            width: 45%;
            padding-right: 10px;
            font-weight: 600;
            color: var(--primary-color);
            text-align: left;
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
            width: 40px;
            height: 40px;
            margin: 0 5px;
        }
    }

    @media (max-width: 576px) {
        body {
            padding-top: 60px;
        }
        
        .main-container {
            padding: 10px;
        }
        
        .filter-section {
            padding: 15px;
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
<?php include 'AdminNavbar.php'; ?>

<div class="container main-container">
    <!-- Filter Section at Top -->
    <div class="filter-section">
        <h5 class="filter-title">
            <i class="bi bi-funnel"></i> Filter Routes
        </h5>
        <form method="GET" autocomplete="off">
            <div class="filter-grid">
                <div>
                    <label for="filterCode" class="form-label">Route Code</label>
                    <input id="filterCode" type="text" name="code" class="form-control" 
                           value="<?= isset($_GET['code']) ? htmlspecialchars($_GET['code']) : '' ?>" 
                           placeholder="Enter route code" />
                </div>
                <div>
                    <label for="filterFrom" class="form-label">From</label>
                    <input id="filterFrom" type="text" name="from" class="form-control" 
                           value="<?= isset($_GET['from']) ? htmlspecialchars($_GET['from']) : '' ?>" 
                           placeholder="Starting location" />
                </div>
                <div>
                    <label for="filterTo" class="form-label">To</label>
                    <input id="filterTo" type="text" name="to" class="form-control" 
                           value="<?= isset($_GET['to']) ? htmlspecialchars($_GET['to']) : '' ?>" 
                           placeholder="Destination" />
                </div>
                <div class="d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="bi bi-filter me-1"></i> Apply Filters
                    </button>
                    <a href="AdminViewRoutes.php" class="btn btn-outline-light">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Main Content -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Bus Routes</h5>
            <a href="AddRoute.php" class="btn btn-success">
                <i class="bi bi-plus-lg me-1"></i> Add New Route
            </a>
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
                                        <a href="AdminRouteDetails.php?id=<?= $route['id'] ?>" class="action-btn view-btn" title="View details">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                        <a href="EditRoute.php?id=<?= $route['id'] ?>" class="action-btn edit-btn" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <a href="DeleteRoute.php?id=<?= $route['id'] ?>" class="action-btn delete-btn" title="Delete" 
                                           onclick="return confirm('Are you sure you want to delete this route?');">
                                            <i class="bi bi-trash-fill"></i>
                                        </a>
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