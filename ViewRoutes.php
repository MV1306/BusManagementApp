<?php
// API base URL
$apiUrl = "https://192.168.29.141/BusManagementAPI/GetAllRoutes";

// Fetch all routes via cURL
$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
$response = curl_exec($ch);
curl_close($ch);

$routes = json_decode($response, true);

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
$totalPages = ceil($totalRoutes / $limit);
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
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
        }

        .content-area {
            margin-left: 0;
            transition: margin-left 0.3s ease;
        }

        .content-area.shifted {
            margin-left: 250px;
        }

        /* Updated styles for stages in modal */
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
            white-space: normal;       /* Allow wrapping */
            word-break: break-word;    /* Break long words if needed */
            min-width: 120px;          /* Minimum width for consistency */
            max-width: 250px;          /* Max width to keep layout neat */
            box-sizing: border-box;
        }

        .stage-item .bi-geo-alt-fill {
            margin-left: 6px;
            color: #0d6efd;
            font-size: 1.1rem;
            flex-shrink: 0;
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<!-- Toggle Button -->
<button class="filter-toggle" id="toggleFilter"><i class="bi bi-chevron-right"></i></button>

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

    <!-- Routes Table -->
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
                                <a href="#" class="text-primary me-2" title="View" onclick='viewRoute(<?= json_encode($route) ?>)'>
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
        <ul class="pagination justify-content-center">
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
                <li class="page-item <?= $active ?>">
                    <a class="page-link" href="?<?= http_build_query($queryParams) ?>"><?= $p ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($page < $totalPages):
                $queryParams['page'] = $page + 1;
            ?>
                <li class="page-item">
                    <a class="page-link" href="?<?= http_build_query($queryParams) ?>">Next &raquo;</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
</div>

<!-- View Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg"> <!-- Make modal wider -->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="viewModalLabel">Route Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p><strong>Route Code:</strong> <span id="viewCode"></span></p>
        <p><strong>From:</strong> <span id="viewFrom"></span></p>
        <p><strong>To:</strong> <span id="viewTo"></span></p>
        <p><strong>Stages:</strong></p>
        <div class="stages-container" id="viewStages"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
    const toggleBtn = document.getElementById('toggleFilter');
    const sidebar = document.getElementById('filterSidebar');
    const content = document.getElementById('mainContent');

    toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('active');
        content.classList.toggle('shifted');

        const icon = toggleBtn.querySelector('i');
        if (sidebar.classList.contains('active')) {
            icon.classList.remove('bi-chevron-right');
            icon.classList.add('bi-chevron-left');
        } else {
            icon.classList.remove('bi-chevron-left');
            icon.classList.add('bi-chevron-right');
        }
    });

    function viewRoute(route) {
        document.getElementById('viewCode').textContent = route.code;
        document.getElementById('viewFrom').textContent = route.from;
        document.getElementById('viewTo').textContent = route.to;

        const stagesContainer = document.getElementById('viewStages');
        stagesContainer.innerHTML = '';

        if (route.stages && route.stages.length) {
            route.stages.forEach((stage, index) => {
    const stageDiv = document.createElement('div');
    stageDiv.classList.add('stage-item');

    // Create icon element
    const icon = document.createElement('i');
    icon.className = 'bi bi-geo-alt-fill me-2';  // Add some margin-right
    stageDiv.appendChild(icon);

    // Create the text node with order and stage name
    const text = document.createTextNode(`${index + 1}. ${stage.stageName}`);
    stageDiv.appendChild(text);

    stagesContainer.appendChild(stageDiv);
});

        } else {
            stagesContainer.textContent = 'No stages available.';
        }

        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('viewModal'));
        modal.show();
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
