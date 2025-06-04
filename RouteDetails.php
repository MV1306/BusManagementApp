<?php
$config = include('config.php');

// API base URL from config
$apiBaseUrl = $config['api_base_url'];

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Invalid Route ID.";
    exit;
}

$routeId = $_GET['id'];
$apiUrl = $apiBaseUrl . "GetRouteByID/" . urlencode($routeId);

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        :root {
            /* RCB Colors */
            --rcb-red: #CE1126;
            --rcb-gold: #F7D100;
            --rcb-black: #2F2F2F;
            --rcb-dark-grey: #4A4A4A;
            --rcb-light-grey: #E0E0E0;
            --rcb-white: #FFFFFF;

            --primary-gradient: linear-gradient(135deg, var(--rcb-red) 0%, #A90E20 100%); /* Deeper red for gradient */
            --secondary-gradient: linear-gradient(135deg, var(--rcb-gold) 0%, #D8B700 100%); /* Deeper gold for gradient */
        }
        
        body {
            background-color: var(--rcb-light-grey);
            color: var(--rcb-dark-grey);
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
        
        .route-header-card {
            background: var(--primary-gradient);
            color: var(--rcb-white);
            border-radius: 12px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .route-badge {
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: 1px;
            padding: 0.5rem 1rem;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255,255,255,0.3);
            color: var(--rcb-white);
        }
        
        .location-card {
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(5px);
            border-radius: 10px;
            padding: 1.25rem;
            transition: all 0.3s ease;
            border: 1px solid rgba(255,255,255,0.2);
        }
        
        .location-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
            background: rgba(255,255,255,0.25);
        }
        
        .location-icon {
            font-size: 1.75rem;
            color: var(--rcb-white);
            background: rgba(255,255,255,0.2);
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .card {
            border-radius: 12px;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }

        .card-title {
            color: var(--rcb-red);
            font-weight: bold;
        }

        .stage-item {
            background: var(--rcb-white);
            color: var(--rcb-black);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            margin: 0.5rem 0.5rem 0.5rem 0;
            display: inline-flex;
            align-items: center;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            border: 1px solid var(--rcb-light-grey);
        }
        
        .stage-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            background: var(--rcb-light-grey);
        }
        
        .stage-item i {
            margin-right: 0.5rem;
            color: var(--rcb-dark-grey);
        }
        
        .stage-start {
            background: var(--rcb-red);
            color: var(--rcb-white);
        }
        
        .stage-end {
            background: var(--rcb-gold);
            color: var(--rcb-black); /* Changed to black for better contrast on gold */
        }
        
        .stage-order-badge {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: var(--rcb-black);
            color: var(--rcb-gold);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.5rem;
            font-size: 0.75rem;
            font-weight: bold;
        }
        
        .stage-start .stage-order-badge {
            background: var(--rcb-gold);
            color: var(--rcb-red);
        }
        
        .stage-end .stage-order-badge {
            background: var(--rcb-red);
            color: var(--rcb-gold);
        }
        
        #routeMap {
            height: 500px;
            width: 100%;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border: 1px solid rgba(0,0,0,0.1);
            margin: 1.5rem 0;
        }
        
        .back-btn {
            transition: all 0.3s ease;
            font-weight: 500;
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            background-color: var(--rcb-gold);
            color: var(--rcb-black);
            border-color: var(--rcb-gold);
        }
        
        .back-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            background-color: #D8B700; /* Slightly darker gold on hover */
            border-color: #D8B700;
        }

        .text-primary {
            color: var(--rcb-red) !important;
        }
        
        @media (max-width: 768px) {
            .route-header-card {
                padding: 1.5rem;
            }
            
            .location-card {
                margin-bottom: 1rem;
                width: 100%;
            }
            
            #routeMap {
                height: 350px;
            }
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container py-4">
        <div class="route-header-card p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
                <h1 class="h3 mb-3 mb-md-0 fw-bold">
                    Route Details
                    <span class="route-badge rounded-pill ms-2">
                        <?= htmlspecialchars($route['code']) ?>
                    </span>
                </h1>
                <a href="ViewRoutes.php" class="btn back-btn">
                    <i class="bi bi-arrow-left me-2"></i>Back to Routes
                </a>
            </div>

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="location-card h-100 d-flex align-items-center">
                        <div class="location-icon me-3">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>
                        <div>
                            <div class="small text-white-80 fw-semibold">Departure</div>
                            <div class="h5 mb-0 fw-bold"><?= htmlspecialchars($route['from']) ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="location-card h-100 d-flex align-items-center">
                        <div class="location-icon me-3">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>
                        <div>
                            <div class="small text-white-80 fw-semibold">Destination</div>
                            <div class="h5 mb-0 fw-bold"><?= htmlspecialchars($route['to']) ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3 fw-semibold">
                    <i class="bi bi-signpost-2-fill text-primary me-2"></i>
                    Route Stages
                </h5>
                
                <div class="stages-container">
                    <?php if (!empty($route['stages'])): ?>
                        <?php foreach ($route['stages'] as $i => $stage): ?>
                            <?php 
                                $stageNumber = $i + 1;
                                $isFirst = ($i === 0);
                                $isLast = ($i === count($route['stages']) - 1);
                            ?>
                            <div class="stage-item <?= $isFirst ? 'stage-start' : ($isLast ? 'stage-end' : '') ?>">
                                <span class="stage-order-badge"><?= $stageNumber ?></span>
                                <?php if ($isFirst): ?>
                                    <i class="bi bi-flag-fill" title="Start Stage"></i>
                                <?php elseif ($isLast): ?>
                                    <i class="bi bi-flag-fill" title="End Stage"></i>
                                <?php else: ?>
                                    <i class="bi bi-geo-alt"></i>
                                <?php endif; ?>
                                <?= htmlspecialchars($stage['stageName']) ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="alert alert-info mb-0">No stages available for this route.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-3 fw-semibold">
                    <i class="bi bi-map-fill text-primary me-2"></i>
                    Route Map
                </h5>
                <div id="routeMap"></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const stages = <?= json_encode($route['stages'] ?? []) ?>;
        const map = L.map('routeMap').setView([20.5937, 78.9629], 5);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        if (stages.length > 0) {
            let bounds = [];
            let pathCoordinates = [];
            let markers = [];

            stages.forEach((stage, i) => {
                if (stage.latitude && stage.longitude) {
                    const stageNumber = i + 1;
                    const latLng = [stage.latitude, stage.longitude];
                    bounds.push(latLng);
                    pathCoordinates.push(latLng);

                    let marker;
                    if (i === 0) {
                        // Start marker (RCB Red)
                        marker = L.marker(latLng, {
                            icon: L.divIcon({
                                html: `<div style="background: var(--rcb-red); border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; color: var(--rcb-white); font-weight: bold;">${stageNumber}</div>`,
                                className: 'custom-div-icon',
                                iconSize: [30, 30],
                                iconAnchor: [15, 15]
                            })
                        }).bindPopup(`<b>Start (Stage ${stageNumber}):</b> ${stage.stageName}`);
                    } else if (i === stages.length - 1) {
                        // End marker (RCB Gold)
                        marker = L.marker(latLng, {
                            icon: L.divIcon({
                                html: `<div style="background: var(--rcb-gold); border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; color: var(--rcb-black); font-weight: bold;">${stageNumber}</div>`,
                                className: 'custom-div-icon',
                                iconSize: [30, 30],
                                iconAnchor: [15, 15]
                            })
                        }).bindPopup(`<b>End (Stage ${stageNumber}):</b> ${stage.stageName}`);
                    } else {
                        // Intermediate markers (RCB Black/Dark Grey)
                        marker = L.marker(latLng, {
                            icon: L.divIcon({
                                html: `<div style="background: var(--rcb-black); border-radius: 50%; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; color: var(--rcb-gold); font-weight: bold;">${stageNumber}</div>`,
                                className: 'custom-div-icon',
                                iconSize: [24, 24],
                                iconAnchor: [12, 12]
                            })
                        }).bindPopup(`<b>Stage ${stageNumber}:</b> ${stage.stageName}`);
                    }
                    
                    marker.addTo(map);
                    markers.push(marker);
                }
            });

            if (bounds.length > 0) {
                // Add route path (RCB Black with Gold dashes)
                L.polyline(pathCoordinates, {
                    color: 'var(--rcb-black)',
                    weight: 4,
                    opacity: 0.8,
                    dashArray: '8, 8', /* Slightly longer dashes for a distinct look */
                    lineJoin: 'round'
                }).addTo(map);
                
                // Fit bounds with some padding
                map.fitBounds(bounds, { padding: [50, 50] });
            }
        } else {
            // No stages with coordinates - show info
            const infoDiv = document.createElement('div');
            infoDiv.className = 'alert alert-info text-center m-3';
            infoDiv.innerHTML = 'No map data available for this route';
            document.getElementById('routeMap').appendChild(infoDiv);
        }
    </script>
</body>
</html>