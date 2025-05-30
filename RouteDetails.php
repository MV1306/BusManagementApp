<?php
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Invalid Route ID.";
    exit;
}

$routeId = $_GET['id'];
$apiUrl = "https://busmanagementapi.onrender.com/GetRouteByID/" . urlencode($routeId);

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
$response = curl_exec($ch);
curl_close($ch);

$route = json_decode($response, true);
if (!$route || !isset($route['id'])) {
    echo "Route not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Route Details - <?= htmlspecialchars($route['code']) ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        .stage-item {
            background: #e9ecef;
            padding: 8px 12px;
            border-radius: 20px;
            margin: 6px;
            display: inline-block;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }
        #routeMap {
            height: 500px;
            margin-top: 15px;
            border-radius: 10px;
            box-shadow: 0 0 8px rgb(0 0 0 / 0.15);
        }
        .bg-gradient {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <!-- Beautiful Route Details Card -->
        <div class="card shadow-lg p-4 mb-5 border-0 rounded-4" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
            <h2 class="card-title mb-4 fw-bold text-white">
                Route <span class="badge bg-warning text-dark fs-5 align-middle" style="font-weight:700; letter-spacing:1.2px;">
                    <?= htmlspecialchars($route['code']) ?>
                </span>
            </h2>

            <div class="d-flex flex-column flex-md-row gap-5 justify-content-start text-white">
                <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-geo-alt-fill fs-4 text-white"></i>
                    <div>
                        <div class="small text-white-50 fw-semibold">From</div>
                        <div class="fs-5 fw-bold"><?= htmlspecialchars($route['from']) ?></div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-geo-alt-fill fs-4 text-white"></i>
                    <div>
                        <div class="small text-white-50 fw-semibold">To</div>
                        <div class="fs-5 fw-bold"><?= htmlspecialchars($route['to']) ?></div>
                    </div>
                </div>
            </div>
        </div>

        <h5>Stages:</h5>
        <div>
            <?php if (!empty($route['stages'])): ?>
                <?php foreach ($route['stages'] as $i => $stage): ?>
                    <?php 
                        $isFirst = ($i === 0);
                        $isLast = ($i === count($route['stages']) - 1);
                    ?>
                    <div class="stage-item <?= $isFirst ? 'bg-success text-white' : ($isLast ? 'bg-danger text-white' : '') ?>">
                        <?php if ($isFirst): ?>
                            <i class="bi bi-geo-alt-fill" title="Start Stage"></i> 
                            <strong>Start:</strong>
                        <?php elseif ($isLast): ?>
                            <i class="bi bi-geo-alt-fill" title="End Stage"></i> 
                            <strong>End:</strong>
                        <?php else: ?>
                            <i class="bi bi-geo-alt-fill"></i>
                        <?php endif; ?>
                        <?= htmlspecialchars($stage['stageName']) ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No stages available.</p>
            <?php endif; ?>
        </div>

        <div id="routeMap"></div>

        <a href="ViewRoutes.php" class="btn btn-secondary mt-3">Back to Routes</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const stages = <?= json_encode($route['stages'] ?? []) ?>;
        const map = L.map('routeMap').setView([20.5937, 78.9629], 5); // India default center

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        if (stages.length > 0) {
            let bounds = [];
            let pathCoordinates = [];

            stages.forEach((stage, i) => {
                if (stage.latitude && stage.longitude) {
                    const latLng = [stage.latitude, stage.longitude];
                    bounds.push(latLng);
                    pathCoordinates.push(latLng);

                    let markerOptions = {};
                    if (i === 0) {
                        markerOptions = {
                            title: 'Start: ' + stage.stageName,
                            icon: L.icon({
                                iconUrl: 'https://cdn-icons-png.flaticon.com/512/190/190411.png',
                                iconSize: [25, 41],
                                iconAnchor: [12, 41],
                                popupAnchor: [1, -34],
                            })
                        };
                    } else if (i === stages.length - 1) {
                        markerOptions = {
                            title: 'End: ' + stage.stageName,
                            icon: L.icon({
                                iconUrl: 'https://cdn-icons-png.flaticon.com/512/190/190406.png',
                                iconSize: [25, 41],
                                iconAnchor: [12, 41],
                                popupAnchor: [1, -34],
                            })
                        };
                    } else {
                        markerOptions = { title: stage.stageName };
                    }

                    L.marker(latLng, markerOptions).addTo(map).bindPopup(markerOptions.title);
                }
            });

            if (bounds.length > 0) {
                map.fitBounds(bounds);
                L.polyline(pathCoordinates, { color: 'blue', weight: 4 }).addTo(map);
            }
        }
    </script>
</body>
</html>
