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
<html>
<head>
    <title>Bus Routes</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

    <style>
        .filter-sidebar {
            position: fixed;
            top: 56px;
            left: 0;
            width: 250px;
            height: 100%;
            background: #f8f9fa;
            border-right: 1px solid #ddd;
            padding: 20px;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
            z-index: 1050;
        }

        .filter-sidebar.active {
            transform: translateX(0);
        }

        .filter-toggle {
            position: fixed;
            top: 70px;
            left: 10px;
            z-index: 1100;
            background: #0d6efd;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 50%;
            font-size: 18px;
            cursor: pointer;
        }

        .content-area {
            margin-left: 0;
            transition: margin-left 0.3s ease;
        }

        .content-area.shifted {
            margin-left: 250px;
        }

        .stage-start {
            color: green;
            font-weight: 600;
        }

        .stage-end {
            color: red;
            font-weight: 600;
        }

        .stages-container {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 10px;
            max-width: 100%;
        }

        .stage-item {
            background: #e9ecef;
            padding: 8px 12px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            font-weight: 500;
            font-size: 0.9rem;
            white-space: normal;
            word-break: break-word;
            min-width: 120px;
            max-width: 250px;
            box-sizing: border-box;
        }

        .stage-item .bi-geo-alt-fill {
            margin-left: 6px;
            color: #0d6efd;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        #routeMap {
            height: 600px;
            width: 100%;
            margin-top: 15px;
            border-radius: 6px;
            border: 1px solid #ddd;
        }

        @media (max-width: 767px) {
            .filter-sidebar {
                width: 100%;
                height: auto;
                position: fixed;
                top: 0;
                left: 0;
                z-index: 1055;
                padding-top: 60px;
                transform: translateY(-100%);
                transition: transform 0.3s ease;
            }

            .filter-sidebar.active {
                transform: translateY(0);
            }

            .content-area.shifted {
                margin-left: 0;
            }

            .filter-toggle {
                top: 10px;
            }

            .filter-toggle i {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<!-- Toggle Button -->
<button class="filter-toggle" id="toggleFilter"><i class="bi bi-funnel-fill"></i></button>

<!-- Sidebar Filter -->
<div class="filter-sidebar" id="filterSidebar">
    <br/>
    <br/>
    <h5 class="mb-3">Filter Routes</h5>
    <form method="GET">
        <div class="mb-3">
            <label class="form-label">Route Code</label>
            <input type="text" name="code" class="form-control" value="<?= isset($_GET['code']) ? htmlspecialchars($_GET['code']) : '' ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">From</label>
            <input type="text" name="from" class="form-control" value="<?= isset($_GET['from']) ? htmlspecialchars($_GET['from']) : '' ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">To</label>
            <input type="text" name="to" class="form-control" value="<?= isset($_GET['to']) ? htmlspecialchars($_GET['to']) : '' ?>">
        </div>
        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary">Apply</button>
            <a href="ViewRoutes.php" class="btn btn-secondary">Reset</a>
        </div>
    </form>
</div>

<!-- Main Content -->
<div class="container mt-4 content-area" id="mainContent">
    <h2 class="text-center mb-4">Bus Routes</h2>

    <div class="mb-3 text-end">
        <a href="AddRoute.php" class="btn btn-success">Add New Route</a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead class="table-light">
                <tr>
                    <th class="d-none">ID</th>
                    <th>Route Code</th>
                    <th>From</th>
                    <th>To</th>
                    <th style="width: 140px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($routesPage) === 0): ?>
                    <tr><td colspan="5" class="text-center">No routes found.</td></tr>
                <?php else: ?>
                    <?php foreach ($routesPage as $route): ?>
                        <tr>
                            <td class="d-none"><?= htmlspecialchars($route['id']) ?></td>
                            <td><?= htmlspecialchars($route['code']) ?></td>
                            <td><?= htmlspecialchars($route['from']) ?></td>
                            <td><?= htmlspecialchars($route['to']) ?></td>
                            <td>
                                <a href="RouteDetails.php?id=<?= $route['id'] ?>" class="text-primary me-2" title="View">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                                <a href="EditRoute.php?id=<?= $route['id'] ?>" class="text-warning me-2" title="Edit">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                <a href="delete_route.php?id=<?= $route['id'] ?>" class="text-danger" title="Delete" onclick="return confirm('Are you sure?')">
                                    <i class="bi bi-trash-fill"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <nav>
        <ul class="pagination justify-content-center flex-wrap">
            <?php
            $queryParams = $_GET;
            if ($page > 1):
                $queryParams['page'] = $page - 1;
            ?>
                <li class="page-item">
                    <a class="page-link" href="?<?= http_build_query($queryParams) ?>">&laquo; Prev</a>
                </li>
            <?php endif; ?>

            <?php for ($p = 1; $p <= $totalPages; $p++):
                $queryParams['page'] = $p;
                $active = ($p == $page) ? 'active' : '';
            ?>
                <li class="page-item <?= $active ?>"><a class="page-link" href="?<?= http_build_query($queryParams) ?>"><?= $p ?></a></li>
            <?php endfor; ?>

            <?php
            if ($page < $totalPages):
                $queryParams['page'] = $page + 1;
            ?>
                <li class="page-item">
                    <a class="page-link" href="?<?= http_build_query($queryParams) ?>">Next &raquo;</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const filterToggleBtn = document.getElementById('toggleFilter');
    const filterSidebar = document.getElementById('filterSidebar');
    const mainContent = document.getElementById('mainContent');
    let sidebarOpen = false;

    filterToggleBtn.addEventListener('click', () => {
        sidebarOpen = !sidebarOpen;
        filterSidebar.classList.toggle('active', sidebarOpen);
        mainContent.classList.toggle('shifted', sidebarOpen);
        filterToggleBtn.innerHTML = sidebarOpen
            ? '<i class="bi bi-x-lg"></i>'
            : '<i class="bi bi-funnel-fill"></i>';
    });
</script>
</body>
</html>
