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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <style>
        :root {
            --primary: #4361ee;
            --primary-dark: #3a0ca3;
            --secondary: #4cc9f0;
            --dark: #1a1a2e;
            --darker: #16213e;
            --light: #f8f9fa;
            --text: #e2e2e2;
            --text-muted: #b8b8b8;
            --success: #4ade80;
            --warning: #fbbf24;
            --danger: #f87171;
            
            --radius-lg: 12px;
            --radius-md: 8px;
            --radius-sm: 4px;
            
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.1);
            --glow: 0 0 12px rgba(67, 97, 238, 0.3);
            
            --transition: all 0.2s ease;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: #f5f7fa;
            color: #333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Navbar - Dark Theme */
        .navbar {
            background: var(--darker) !important;
            box-shadow: var(--shadow);
            padding: 0.75rem 1.5rem;
        }
        
        .navbar-brand,
        .navbar-nav .nav-link {
            color: var(--text) !important;
        }
        
        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link:focus {
            color: var(--secondary) !important;
        }
        
        .navbar-toggler {
            border-color: rgba(255,255,255,0.1);
        }
        
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.75%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        /* Main Container */
        .main-container {
            margin-top: 90px;
            padding: 1.5rem;
        }

        /* Cards */
        .card {
            background: white;
            border: none;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            transition: var(--transition);
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        }

        .card-header {
            background: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1.25rem 1.5rem;
            font-weight: 600;
            color: var(--primary-dark);
            border-radius: var(--radius-lg) var(--radius-lg) 0 0 !important;
        }

        /* Table */
        .table {
            --bs-table-bg: transparent;
            --bs-table-color: #333;
            --bs-table-striped-bg: rgba(0, 0, 0, 0.02);
            --bs-table-hover-bg: rgba(67, 97, 238, 0.05);
            margin-bottom: 0;
        }

        .table thead th {
            background: white;
            color: var(--primary);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .table tbody tr {
            transition: var(--transition);
            border-bottom: 1px solid rgba(0, 0, 0, 0.03);
        }

        .table tbody tr:last-child {
            border-bottom: none;
        }

        .table tbody tr:hover {
            background: rgba(67, 97, 238, 0.03) !important;
        }

        .table td {
            padding: 1.25rem 1.5rem;
            vertical-align: middle;
            color: #555;
        }

        .table td .route-code {
            font-weight: 600;
            color: var(--primary);
            letter-spacing: 0.5px;
        }

        /* Filter Section */
        .filter-container {
            background: white;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .filter-title {
            color: var(--primary-dark);
            font-weight: 600;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.25rem;
        }

        /* Form Elements */
        .form-control {
            background: white;
            border: 1px solid rgba(0, 0, 0, 0.1);
            color: #333;
            border-radius: var(--radius-md);
            padding: 0.75rem 1rem;
            transition: var(--transition);
            box-shadow: none;
        }

        .form-control:focus {
            background: white;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
            color: #333;
        }

        .form-control::placeholder {
            color: rgba(0, 0, 0, 0.4);
        }

        /* Buttons */
        .btn {
            border-radius: var(--radius-md);
            padding: 0.75rem 1.25rem;
            font-weight: 600;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            border: none;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.2);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(67, 97, 238, 0.3);
        }

        .btn-outline-secondary {
            border: 1px solid rgba(0, 0, 0, 0.1);
            color: #666;
            background: white;
        }

        .btn-outline-secondary:hover {
            background: rgba(0, 0, 0, 0.02);
            border-color: var(--primary);
            color: var(--primary);
        }

        /* Action Buttons */
        .action-btn {
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: var(--transition);
            background: rgba(0, 0, 0, 0.05);
            color: #666;
            margin: 0 0.25rem;
        }

        .action-btn:hover {
            background: rgba(67, 97, 238, 0.1);
            color: var(--primary);
            transform: scale(1.1);
        }

        /* Pagination */
        .pagination .page-item .page-link {
            background: white;
            color: #666;
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: var(--radius-md) !important;
            margin: 0 0.25rem;
            padding: 0.5rem 1rem;
            transition: var(--transition);
        }

        .pagination .page-item.active .page-link {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
            box-shadow: var(--glow);
        }

        .pagination .page-item:not(.active) .page-link:hover {
            background: rgba(0, 0, 0, 0.02);
            border-color: var(--primary);
            color: var(--primary);
        }

        /* Empty State */
        .empty-state {
            padding: 3rem 1.5rem;
            text-align: center;
            background: white;
            border-radius: var(--radius-lg);
        }

        .empty-state i {
            font-size: 3.5rem;
            color: rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
        }

        .empty-state h5 {
            color: var(--primary);
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: #666;
            margin-bottom: 0;
        }

        /* Clickable Rows */
        tr[data-href] {
            cursor: pointer;
        }

        /* Animations */
        .animate-fade-in {
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Responsive Adjustments */
        @media (max-width: 992px) {
            .main-container {
                margin-top: 80px;
                padding: 1rem;
            }
        }

        @media (max-width: 768px) {
            .main-container {
                margin-top: 70px;
                padding: 0.75rem;
            }
            
            .table-responsive {
                border-radius: var(--radius-md);
                overflow: hidden;
            }

            .table thead {
                display: none;
            }

            .table tr {
                display: block;
                margin-bottom: 1rem;
                border-radius: var(--radius-md);
                box-shadow: var(--shadow-sm);
                padding: 1rem;
                background: white;
            }

            .table td {
                display: block;
                text-align: right;
                padding: 0.75rem 1rem;
                padding-left: 50%;
                position: relative;
                border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            }

            .table td:before {
                content: attr(data-label);
                position: absolute;
                left: 1rem;
                width: 45%;
                padding-right: 1rem;
                font-weight: 600;
                color: var(--primary);
                text-align: left;
            }

            .table td:last-child {
                display: flex;
                justify-content: flex-end;
                padding-left: 1rem;
                border-bottom: none;
            }
            
            .table td:last-child:before {
                display: none;
            }
        }

        @media (max-width: 576px) {
            .main-container {
                margin-top: 70px;
                padding: 0.5rem;
            }
            
            .card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.75rem;
                padding: 1rem;
            }
            
            .table td {
                padding-left: 45%;
                font-size: 0.9rem;
            }
            
            .table td:before {
                width: 40%;
                font-size: 0.85rem;
            }
            
            .empty-state {
                padding: 2rem 1rem;
            }
            
            .empty-state i {
                font-size: 2.5rem;
            }
        }

        /* Modern filter layout */
        .filter-row {
            background: white;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .filter-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            align-items: end;
        }

        @media (max-width: 768px) {
            .filter-form {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<!-- <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container">
        <a class="navbar-brand" href="#">
            <i class="bi bi-bus-front me-2"></i> Bus Route Manager
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="ViewRoutes.php">
                        <i class="bi bi-view-list me-1"></i> View Routes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-plus-circle me-1"></i> Add Route
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-gear me-1"></i> Settings
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav> -->

<?php include 'navbar.php'; ?>

<div class="container main-container animate__animated animate__fadeIn">
    <!-- Modern Filter Section at Top -->
    <div class="filter-row animate__animated animate__fadeIn">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0"><i class="bi bi-funnel me-2"></i> Filter Routes</h5>
            <span class="badge bg-primary"><?= $totalRoutes ?> routes found</span>
        </div>
        <form method="GET" autocomplete="off" class="filter-form">
            <div>
                <label for="filterCode" class="form-label">Route Code</label>
                <input id="filterCode" type="text" name="code" class="form-control" 
                       value="<?= isset($_GET['code']) ? htmlspecialchars($_GET['code']) : '' ?>" 
                       placeholder="e.g., RCB-101" />
            </div>
            <div>
                <label for="filterFrom" class="form-label">From</label>
                <input id="filterFrom" type="text" name="from" class="form-control" 
                       value="<?= isset($_GET['from']) ? htmlspecialchars($_GET['from']) : '' ?>" 
                       placeholder="Starting point" />
            </div>
            <div>
                <label for="filterTo" class="form-label">To</label>
                <input id="filterTo" type="text" name="to" class="form-control" 
                       value="<?= isset($_GET['to']) ? htmlspecialchars($_GET['to']) : '' ?>" 
                       placeholder="Destination" />
            </div>
            <div class="d-flex gap-2 align-items-end">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="bi bi-funnel me-1"></i> Apply Filters
                </button>
                <a href="ViewRoutes.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Routes List -->
    <div class="card animate__animated animate__fadeInUp">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-bus-front me-2"></i> Bus Routes</h5>
            <div class="d-flex align-items-center">
                <span class="text-muted small">Page <?= $page ?> of <?= $totalPages ?></span>
            </div>
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
                            <th style="width: 100px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($routesPage) === 0): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="bi bi-bus-front"></i>
                                        <h5>No routes found</h5>
                                        <p>Try adjusting your search filters</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($routesPage as $route): ?>
                                <tr data-href="RouteDetails.php?id=<?= $route['id'] ?>" class="animate__animated animate__fadeIn">
                                    <td class="d-none" data-label="ID"><?= htmlspecialchars($route['id']) ?></td>
                                    <td data-label="Route Code">
                                        <span class="route-code"><?= htmlspecialchars($route['code']) ?></span>
                                    </td>
                                    <td data-label="From"><?= htmlspecialchars($route['from']) ?></td>
                                    <td data-label="To"><?= htmlspecialchars($route['to']) ?></td>
                                    <td data-label="Actions" class="text-nowrap">
                                        <a href="RouteDetails.php?id=<?= $route['id'] ?>" class="action-btn" title="View details">
                                            <i class="bi bi-eye-fill"></i>
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
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>

                <?php
                // Show max 5 page links around current page
                $startPage = max(1, $page - 2);
                $endPage = min($totalPages, $page + 2);
                
                // Always show first page
                if ($startPage > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?<?php
                            $params = $_GET;
                            $params['page'] = 1;
                            echo http_build_query($params);
                        ?>">1</a>
                    </li>
                    <?php if ($startPage > 2): ?>
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    <?php endif;
                endif;
                
                for ($i = $startPage; $i <= $endPage; $i++): ?>
                    <li class="page-item<?= $i === $page ? ' active' : '' ?>">
                        <a class="page-link" href="?<?php
                            $params = $_GET;
                            $params['page'] = $i;
                            echo http_build_query($params);
                        ?>"><?= $i ?></a>
                    </li>
                <?php endfor;
                
                // Always show last page
                if ($endPage < $totalPages): ?>
                    <?php if ($endPage < $totalPages - 1): ?>
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    <?php endif; ?>
                    <li class="page-item">
                        <a class="page-link" href="?<?php
                            $params = $_GET;
                            $params['page'] = $totalPages;
                            echo http_build_query($params);
                        ?>"><?= $totalPages ?></a>
                    </li>
                <?php endif; ?>

                <li class="page-item<?= $page >= $totalPages ? ' disabled' : '' ?>">
                    <a class="page-link" href="?<?php
                        $params = $_GET;
                        $params['page'] = $page + 1;
                        echo http_build_query($params);
                    ?>" aria-label="Next">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Make table rows clickable
        document.querySelectorAll('tbody tr[data-href]').forEach(row => {
            row.addEventListener('click', (e) => {
                // Don't navigate if clicking on an action button
                if (!e.target.closest('.action-btn')) {
                    window.location.href = row.dataset.href;
                }
            });
        });
        
        // Add hover effects to cards
        const cards = document.querySelectorAll('.card');
        cards.forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.style.transform = 'translateY(-2px)';
                card.style.boxShadow = '0 8px 24px rgba(0, 0, 0, 0.15)';
            });
            
            card.addEventListener('mouseleave', () => {
                card.style.transform = '';
                card.style.boxShadow = '';
            });
        });
    });
</script>
</body>
</html>