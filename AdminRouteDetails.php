<?php
require_once 'AdminAuth.php';

checkAuth();
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
    <title>Admin - Route Details - <?= htmlspecialchars($route['code']) ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4895ef;
            --success-color: #4cc9f0;
            --danger-color: #f72585;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            --card-hover-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }
        
        body {
            background-color: #f8f9fb;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
        }
        
        .glass-card:hover {
            box-shadow: var(--card-hover-shadow);
            transform: translateY(-2px);
        }
        
        .route-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border-radius: 16px;
            box-shadow: var(--card-hover-shadow);
            overflow: hidden;
            margin-bottom: 2rem;
            position: relative;
            z-index: 1;
        }
        
        .route-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="none"><path d="M0,0 L100,0 L100,100 Q50,80 0,100 Z" fill="rgba(255,255,255,0.1)" /></svg>');
            background-size: 100% 100%;
            opacity: 0.5;
            z-index: -1;
        }
        
        .route-badge {
            font-size: 0.9rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            padding: 0.4rem 1rem;
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(5px);
            border: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .location-card {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            padding: 1.5rem;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.25);
            height: 100%;
        }
        
        .location-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            background: rgba(255, 255, 255, 0.3);
        }
        
        .location-icon {
            font-size: 1.5rem;
            color: white;
            background: rgba(255, 255, 255, 0.3);
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .stage-item {
            background: white;
            padding: 0.75rem 1.25rem;
            border-radius: 12px;
            margin: 0 0.75rem 0.75rem 0;
            display: inline-flex;
            align-items: center;
            font-weight: 500;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 0, 0, 0.03);
        }
        
        .stage-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.12);
        }
        
        .stage-item i {
            margin-right: 0.75rem;
            font-size: 1.1rem;
        }
        
        .stage-start {
            background: linear-gradient(135deg, #38b000 0%, #008000 100%);
            color: white;
        }
        
        .stage-end {
            background: linear-gradient(135deg, #ef233c 0%, #d90429 100%);
            color: white;
        }
        
        .stage-order-badge {
            width: 26px;
            height: 26px;
            border-radius: 8px;
            background: var(--accent-color);
            color: white;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.75rem;
            font-size: 0.8rem;
            font-weight: bold;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .stage-start .stage-order-badge {
            background: white;
            color: #008000;
        }
        
        .stage-end .stage-order-badge {
            background: white;
            color: #d90429;
        }
        
        #routeMap {
            height: 500px;
            width: 100%;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            border: none;
            margin: 1.5rem 0;
        }
        
        .back-btn {
            transition: all 0.3s ease;
            font-weight: 500;
            padding: 0.6rem 1.5rem;
            border-radius: 12px;
            background: white;
            color: var(--primary-color);
            border: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .back-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
            background: white;
            color: var(--secondary-color);
        }
        
        .card-title {
            font-weight: 600;
            color: var(--dark-color);
            display: flex;
            align-items: center;
        }
        
        .card-title i {
            margin-right: 0.75rem;
            font-size: 1.25rem;
        }
        
        @media (max-width: 768px) {
            .route-header {
                padding: 1.5rem;
            }
            
            .location-card {
                margin-bottom: 1rem;
            }
            
            #routeMap {
                height: 350px;
            }
            
            .stage-item {
                padding: 0.6rem 1rem;
                font-size: 0.9rem;
            }
        }
        
        /* Floating animation for location cards */
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
            100% { transform: translateY(0px); }
        }
        
        .location-card {
            animation: float 6s ease-in-out infinite;
        }
        
        .location-card:nth-child(2n) {
            animation-delay: 0.5s;
        }
        
        /* Modern scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--secondary-color);
        }
    </style>
</head>
<body>
    <?php include 'AdminNavbar.php'; ?>

    <div class="container py-5">
        <!-- Route Header Card -->
        <div class="route-header p-4 animate__animated animate__fadeIn">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
                <div>
                    <h1 class="h3 mb-2 fw-bold">
                        Route Details
                    </h1>
                    <span class="route-badge rounded-pill">
                        <?= htmlspecialchars($route['code']) ?>
                    </span>
                </div>
                <a href="AdminViewRoutes.php" class="btn back-btn mt-3 mt-md-0 animate__animated animate__pulse animate__infinite animate__slower">
                    <i class="bi bi-arrow-left me-2"></i>Back to Routes
                </a>
            </div>

            <div class="row g-4 mt-3">
                <div class="col-md-6">
                    <div class="location-card d-flex align-items-center animate__animated animate__fadeInLeft">
                        <div class="location-icon me-3">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>
                        <div>
                            <div class="small text-white-80 fw-semibold">Departure Point</div>
                            <div class="h4 mb-0 fw-bold"><?= htmlspecialchars($route['from']) ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="location-card d-flex align-items-center animate__animated animate__fadeInRight">
                        <div class="location-icon me-3">
                            <i class="bi bi-pin-map-fill"></i>
                        </div>
                        <div>
                            <div class="small text-white-80 fw-semibold">Destination Point</div>
                            <div class="h4 mb-0 fw-bold"><?= htmlspecialchars($route['to']) ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stages Section -->
        <div class="glass-card mb-4 animate__animated animate__fadeInUp">
            <div class="card-body p-4">
                <h5 class="card-title mb-4">
                    <i class="bi bi-signpost-split text-primary"></i>
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
                            <div class="stage-item <?= $isFirst ? 'stage-start' : ($isLast ? 'stage-end' : '') ?> animate__animated animate__fadeIn" style="animation-delay: <?= $i * 0.1 ?>s;">
                                <span class="stage-order-badge"><?= $stageNumber ?></span>
                                <?php if ($isFirst): ?>
                                    <i class="bi bi-signpost-start-fill" title="Start Stage"></i>
                                <?php elseif ($isLast): ?>
                                    <i class="bi bi-signpost-end-fill" title="End Stage"></i>
                                <?php else: ?>
                                    <i class="bi bi-signpost"></i>
                                <?php endif; ?>
                                <?= htmlspecialchars($stage['stageName']) ?>
                                <!-- <?php if ($stage['latitude'] && $stage['longitude']): ?>
                                    <span class="ms-2 small opacity-75"><i class="bi bi-geo-alt"></i> <?= round($stage['latitude'], 4) ?>, <?= round($stage['longitude'], 4) ?></span>
                                <?php endif; ?> -->
                                <!-- <?php if ($stage['distanceFromStart']) : ?>
                                    <span class="ms-2 small opacity-75"><i class="bi bi-geo-alt"></i> <?= round($stage['distanceFromStart'], 0) ?> Kms</span>
                                <?php endif; ?> -->
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="alert alert-info mb-0">No stages available for this route.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Map Section -->
        <div class="glass-card animate__animated animate__fadeInUp">
            <div class="card-body p-4">
                <h5 class="card-title mb-4">
                    <i class="bi bi-map text-primary"></i>
                    Route Visualization
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
                        // Start marker (green)
                        marker = L.marker(latLng, {
                            icon: L.divIcon({
                                html: `<div style="background: #38b000; border-radius: 12px; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; box-shadow: 0 4px 8px rgba(0,0,0,0.2);">${stageNumber}</div>`,
                                className: 'custom-div-icon',
                                iconSize: [36, 36],
                                iconAnchor: [18, 18]
                            })
                        }).bindPopup(`<b>Start (Stage ${stageNumber}):</b> ${stage.stageName}`);
                    } else if (i === stages.length - 1) {
                        // End marker (red)
                        marker = L.marker(latLng, {
                            icon: L.divIcon({
                                html: `<div style="background: #ef233c; border-radius: 12px; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; box-shadow: 0 4px 8px rgba(0,0,0,0.2);">${stageNumber}</div>`,
                                className: 'custom-div-icon',
                                iconSize: [36, 36],
                                iconAnchor: [18, 18]
                            })
                        }).bindPopup(`<b>End (Stage ${stageNumber}):</b> ${stage.stageName}`);
                    } else {
                        // Intermediate markers (blue)
                        marker = L.marker(latLng, {
                            icon: L.divIcon({
                                html: `<div style="background: #4361ee; border-radius: 10px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; box-shadow: 0 4px 8px rgba(0,0,0,0.2);">${stageNumber}</div>`,
                                className: 'custom-div-icon',
                                iconSize: [32, 32],
                                iconAnchor: [16, 16]
                            })
                        }).bindPopup(`<b>Stage ${stageNumber}:</b> ${stage.stageName}`);
                    }
                    
                    marker.addTo(map);
                    markers.push(marker);
                }
            });

            if (bounds.length > 0) {
                // Add route path
                L.polyline(pathCoordinates, {
                    color: '#4361ee',
                    weight: 5,
                    opacity: 0.8,
                    lineJoin: 'round',
                    dashArray: '8, 8'
                }).addTo(map);
                
                // Fit bounds with some padding
                map.fitBounds(bounds, { padding: [50, 50] });
            }
        } else {
            // No stages with coordinates - show info
            const infoDiv = document.createElement('div');
            infoDiv.className = 'alert alert-info text-center m-3';
            infoDiv.innerHTML = '<i class="bi bi-map me-2"></i>No map data available for this route';
            document.getElementById('routeMap').appendChild(infoDiv);
        }
    </script>
</body>
</html>