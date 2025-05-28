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
    
    <!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

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

        .stage-start {
            color: green;
            font-weight: 600;
        }

        .stage-end {
            color: red;
            font-weight: 600;
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

        /* Leaflet map height for modal */
        #routeMap {
            height: 350px;
            width: 100%;
            margin-top: 15px;
            border-radius: 6px;
            border: 1px solid #ddd;
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
          <h4 id="modalRouteCode"></h4>
          <p><strong>From:</strong> <span id="modalFrom"></span></p>
          <p><strong>To:</strong> <span id="modalTo"></span></p>

          <h6>Stages:</h6>
          <div class="stages-container" id="modalStages"></div>

          <h6>Route Map:</h6>
          <div id="routeMap"></div> <!-- Leaflet Map -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const filterSidebar = document.getElementById('filterSidebar');
    const toggleFilterBtn = document.getElementById('toggleFilter');
    const mainContent = document.getElementById('mainContent');
    let sidebarVisible = false;

    toggleFilterBtn.addEventListener('click', () => {
        sidebarVisible = !sidebarVisible;
        filterSidebar.classList.toggle('active', sidebarVisible);
        mainContent.classList.toggle('shifted', sidebarVisible);
        toggleFilterBtn.innerHTML = sidebarVisible ? '<i class="bi bi-chevron-left"></i>' : '<i class="bi bi-chevron-right"></i>';
    });

    let map; // Leaflet map instance
    let markersLayer; // Layer group for markers

    function viewRoute(route) {
    // Set modal content
    document.getElementById('modalRouteCode').textContent = "Route Code: " + route.code;
    document.getElementById('modalFrom').textContent = route.from;
    document.getElementById('modalTo').textContent = route.to;

    // Populate stages
    const stagesContainer = document.getElementById('modalStages');
    stagesContainer.innerHTML = '';
    let coordinates = [];

    if (route.stages && route.stages.length > 0) {
        route.stages.forEach((stage, index) => {
            const div = document.createElement('div');
            div.classList.add('stage-item');
            if (index === 0) div.classList.add('stage-start');
            else if (index === route.stages.length - 1) div.classList.add('stage-end');
            div.innerHTML = stage.stageName + ' <i class="bi bi-geo-alt-fill"></i>';
            stagesContainer.appendChild(div);

            // Collect coordinates
            if (stage.latitude && stage.longitude) {
                coordinates.push([parseFloat(stage.latitude), parseFloat(stage.longitude)]);
            }
        });
    }

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('viewModal'));
    modal.show();

    setTimeout(() => {
        if (!map) {
            map = L.map('routeMap');
        } else {
            map.eachLayer(layer => map.removeLayer(layer)); // Clear old layers
        }

        // Add tile layer again
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18
        }).addTo(map);

        // Add markers and polyline
        if (coordinates.length > 0) {
            // Add markers
            coordinates.forEach((latlng, index) => {
                L.marker(latlng).addTo(map)
                    .bindPopup(route.stages[index].stageName);
            });

            // Draw polyline in blue
            const polyline = L.polyline(coordinates, { color: 'blue' }).addTo(map);

            // Fit bounds to polyline
            map.fitBounds(polyline.getBounds(), { padding: [30, 30] });
        } else {
            map.setView([20.5937, 78.9629], 5); // Fallback to center of India
        }
    }, 500); // Delay to ensure modal is visible before initializing map
}

    function initMap(stages) {
        // If map already exists, remove it to reset
        if (map) {
            map.off();
            map.remove();
        }

        // Create map
        map = L.map('routeMap');

        // Add OpenStreetMap tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        markersLayer = L.layerGroup().addTo(map);

        // Add markers for stages and collect latlngs for bounds
        const latLngs = [];

        stages.forEach((stage, index) => {
            // Validate latitude and longitude
            let lat = parseFloat(stage.latitude);
            let lng = parseFloat(stage.longitude);

            if (isNaN(lat) || isNaN(lng)) {
                return; // Skip invalid coordinates
            }

            latLngs.push([lat, lng]);

            const markerOptions = {
                title: stage.stageName,
                riseOnHover: true
            };

            // Different marker color for start/end
            if (index === 0) {
                markerOptions.icon = L.icon({
                    iconUrl: 'https://cdn-icons-png.flaticon.com/512/252/252025.png', // green marker icon
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
                    shadowSize: [41, 41]
                });
            } else if (index === stages.length - 1) {
                markerOptions.icon = L.icon({
                    iconUrl: 'https://cdn-icons-png.flaticon.com/512/252/252025.png', // red marker icon
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
                    shadowSize: [41, 41]
                });
            }

            const marker = L.marker([lat, lng], markerOptions).addTo(markersLayer);
            marker.bindPopup(stage.stageName);
        });

        // Fit map to markers or set default view
        if (latLngs.length > 0) {
            const bounds = L.latLngBounds(latLngs);
            map.fitBounds(bounds.pad(0.2));
        } else {
            // Default location if no stages
            map.setView([20.5937, 78.9629], 5); // Center of India
        }
    }
</script>

</body>
</html>
