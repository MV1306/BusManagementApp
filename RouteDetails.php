<?php
$config = include('config.php');
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-light: #93c5fd;
            --secondary: #f59e0b;
            --secondary-dark: #d97706;
            --dark: #1e293b;
            --light: #f8fafc;
            --gray: #64748b;
            --gray-light: #e2e8f0;
            --success: #10b981;
            --danger: #ef4444;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-md: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            --rounded: 0.5rem;
            --rounded-lg: 1rem;
            --rounded-xl: 1.5rem;
        }

        body {
            background-color: var(--light);
            color: var(--dark);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            line-height: 1.6;
        }

        .route-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border-radius: var(--rounded-xl);
            box-shadow: var(--shadow-lg);
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
            height: 120px;
            background: linear-gradient(135deg, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0.05) 100%);
            z-index: -1;
        }

        .route-badge {
            font-size: 0.875rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            padding: 0.5rem 1rem;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.3);
            color: white;
            border-radius: 50px;
        }

        .location-card {
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(10px);
            border-radius: var(--rounded-lg);
            padding: 1.5rem;
            transition: all 0.3s ease;
            border: 1px solid rgba(255,255,255,0.2);
            height: 100%;
        }

        .location-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
            background: rgba(255,255,255,0.25);
        }

        .location-icon {
            font-size: 1.75rem;
            color: white;
            background: rgba(255,255,255,0.2);
            width: 56px;
            height: 56px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .card {
            border-radius: var(--rounded-xl);
            border: none;
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .card-header {
            background-color: white;
            border-bottom: 1px solid var(--gray-light);
            padding: 1.25rem 1.5rem;
            font-weight: 600;
        }

        .card-title {
            color: var(--primary);
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .stage-item {
            background: white;
            color: var(--dark);
            padding: 0.75rem 1.25rem;
            border-radius: 50px;
            margin: 0.5rem 0.5rem 0.5rem 0;
            display: inline-flex;
            align-items: center;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gray-light);
            position: relative;
            overflow: hidden;
        }

        .stage-item::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.1) 0%, rgba(147, 197, 253, 0.1) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .stage-item:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow);
            color: var(--primary);
        }

        .stage-item:hover::after {
            opacity: 1;
        }

        .stage-item i {
            margin-right: 0.75rem;
            color: var(--gray);
        }

        .stage-start {
            background: var(--primary);
            color: white;
        }

        .stage-start i {
            color: white;
        }

        .stage-end {
            background: var(--secondary);
            color: var(--dark);
        }

        .stage-end i {
            color: var(--dark);
        }

        .stage-order-badge {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: var(--dark);
            color: white;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.75rem;
            font-size: 0.875rem;
            font-weight: 700;
            flex-shrink: 0;
        }

        .stage-start .stage-order-badge {
            background: white;
            color: var(--primary);
        }

        .stage-end .stage-order-badge {
            background: var(--dark);
            color: var(--secondary);
        }

        #routeMap {
            height: 500px;
            width: 100%;
            border-radius: var(--rounded-lg);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gray-light);
            margin: 1rem 0;
        }

        .back-btn {
            transition: all 0.3s ease;
            font-weight: 600;
            padding: 0.625rem 1.5rem;
            border-radius: 50px;
            background-color: white;
            color: var(--primary);
            border: 1px solid white;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .back-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
            background-color: rgba(255,255,255,0.9);
            color: var(--primary-dark);
        }

        .animate-delay-1 {
            animation-delay: 0.1s;
        }

        .animate-delay-2 {
            animation-delay: 0.2s;
        }

        .section-divider {
            border: 0;
            height: 1px;
            background: linear-gradient(to right, transparent, var(--gray-light), transparent);
            margin: 2rem 0;
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
                width: 100%;
                display: flex;
                margin: 0.5rem 0;
            }
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container py-4">
        <div class="route-header p-4 p-lg-5 animate__animated animate__fadeIn">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
                <div>
                    <h1 class="h3 mb-2 fw-bold">
                        Route Details
                        <span class="route-badge animate__animated animate__fadeIn animate__delay-1s ms-2">
                            <?= htmlspecialchars($route['code']) ?>
                        </span>
                    </h1>
                    <p class="text-white-80 mb-0">Detailed information about this bus route</p>
                </div>
                <a href="ViewRoutes.php" class="btn back-btn animate__animated animate__fadeIn animate__delay-2s mt-3 mt-md-0">
                    <i class="bi bi-arrow-left"></i>
                    <span>Back to Routes</span>
                </a>
            </div>

            <div class="row g-4 mt-3">
                <div class="col-md-6 animate__animated animate__fadeInLeft">
                    <div class="location-card">
                        <div class="d-flex align-items-center">
                            <div class="location-icon me-3">
                                <i class="bi bi-geo-alt-fill"></i>
                            </div>
                            <div>
                                <div class="small text-white-80 fw-semibold">Departure Point</div>
                                <div class="h4 mb-0 fw-bold"><?= htmlspecialchars($route['from']) ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 animate__animated animate__fadeInRight">
                    <div class="location-card">
                        <div class="d-flex align-items-center">
                            <div class="location-icon me-3">
                                <i class="bi bi-geo-alt-fill"></i>
                            </div>
                            <div>
                                <div class="small text-white-80 fw-semibold">Destination</div>
                                <div class="h4 mb-0 fw-bold"><?= htmlspecialchars($route['to']) ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card animate__animated animate__fadeIn animate__delay-1s">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-signpost-2-fill"></i>
                    Route Stages
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($route['stages'])): ?>
                    <div class="d-flex flex-wrap">
                        <?php foreach ($route['stages'] as $i => $stage): ?>
                            <?php 
                                $stageNumber = $i + 1;
                                $isFirst = ($i === 0);
                                $isLast = ($i === count($route['stages']) - 1);
                                $animationClass = $isFirst ? 'animate__fadeInLeft' : ($isLast ? 'animate__fadeInRight' : 'animate__fadeInUp');
                            ?>
                            <div class="stage-item <?= $isFirst ? 'stage-start' : ($isLast ? 'stage-end' : '') ?> animate__animated <?= $animationClass ?> animate__delay-<?= ($i % 3) + 1 ?>s">
                                <span class="stage-order-badge"><?= $stageNumber ?></span>
                                <?php if ($isFirst): ?>
                                    <i class="bi bi-flag-fill"></i>
                                <?php elseif ($isLast): ?>
                                    <i class="bi bi-flag-fill"></i>
                                <?php else: ?>
                                    <i class="bi bi-geo-alt"></i>
                                <?php endif; ?>
                                <?= htmlspecialchars($stage['stageName']) ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info mb-0">No stages available for this route.</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card animate__animated animate__fadeIn animate__delay-2s">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-map-fill"></i>
                    Route Map
                </h5>
            </div>
            <div class="card-body p-0">
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

            // Custom icons
            const startIcon = L.divIcon({
                html: `<div style="background: var(--primary); color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; border: 2px solid white; box-shadow: var(--shadow-md);">S</div>`,
                className: 'custom-div-icon',
                iconSize: [32, 32],
                iconAnchor: [16, 16]
            });

            const endIcon = L.divIcon({
                html: `<div style="background: var(--secondary); color: var(--dark); width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; border: 2px solid white; box-shadow: var(--shadow-md);">E</div>`,
                className: 'custom-div-icon',
                iconSize: [32, 32],
                iconAnchor: [16, 16]
            });            

            stages.forEach((stage, i) => {
                if (stage.latitude && stage.longitude) {
                    const latLng = [stage.latitude, stage.longitude];
                    bounds.push(latLng);
                    pathCoordinates.push(latLng);

                    const waypointIcon = L.divIcon({
                html: `<div style="background: white; color: var(--primary); width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; border: 2px solid var(--primary); box-shadow: var(--shadow-sm);">${i+1}</div>`,
                className: 'custom-div-icon',
                iconSize: [28, 28],
                iconAnchor: [14, 14]
            });

                    let marker;
                    if (i === 0) {
                        marker = L.marker(latLng, { icon: startIcon })
                            .bindPopup(`<b>Start:</b> ${stage.stageName}`);
                    } else if (i === stages.length - 1) {
                        marker = L.marker(latLng, { icon: endIcon })
                            .bindPopup(`<b>End:</b> ${stage.stageName}`);
                    } else {
                        const icon = waypointIcon;
                        icon.options.html = `<div style="background: white; color: var(--primary); width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; border: 2px solid var(--primary); box-shadow: var(--shadow-sm);">${i+1}</div>`;
                        marker = L.marker(latLng, { icon })
                            .bindPopup(`<b>Stage ${i+1}:</b> ${stage.stageName}`);
                    }
                    
                    marker.addTo(map);
                    markers.push(marker);
                }
            });

            if (bounds.length > 0) {
                // Add route path
                L.polyline(pathCoordinates, {
                    color: 'var(--primary)',
                    weight: 4,
                    opacity: 0.8,
                    lineJoin: 'round',
                    dashArray: '8, 6'
                }).addTo(map);
                
                // Fit bounds with padding
                map.fitBounds(bounds, { padding: [50, 50] });
            }
        } else {
            // No stages with coordinates
            const infoDiv = document.createElement('div');
            infoDiv.className = 'alert alert-info text-center m-3';
            infoDiv.innerHTML = '<i class="bi bi-map me-2"></i> No map data available for this route';
            document.getElementById('routeMap').appendChild(infoDiv);
        }
    </script>
</body>
</html>