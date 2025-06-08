<?php
require_once 'AdminAuth.php';

checkAuth();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Find Routes</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #4f46e5;
            --primary-light: #6366f1;
            --primary-dark: #4338ca;
            --secondary: #10b981;
            --dark: #1e293b;
            --light: #f8fafc;
            --gray: #64748b;
            --light-gray: #e2e8f0;
            --card-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
            --card-shadow-hover: 0 14px 28px rgba(0,0,0,0.12), 0 10px 10px rgba(0,0,0,0.10);
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #f1f5f9;
            color: var(--dark);
            line-height: 1.6;
        }

        .navbar {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .page-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            padding: 3rem 1rem;
            margin-bottom: 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 20% 50%, rgba(255,255,255,0.1) 0%, transparent 70%);
        }

        .page-header h1 {
            font-weight: 700;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            position: relative;
        }

        .page-header p {
            font-weight: 300;
            opacity: 0.9;
            font-size: 1.1rem;
            position: relative;
        }

        .search-container {
            max-width: 800px;
            margin: 0 auto 3rem;
        }

        .search-card {
            background: white;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            padding: 2rem;
            transition: var(--transition);
            border: none;
        }

        .search-card:hover {
            box-shadow: var(--card-shadow-hover);
        }

        .form-label {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.75rem;
        }

        .form-control {
            border-radius: 10px;
            padding: 0.75rem 1rem;
            border: 1px solid var(--light-gray);
            transition: var(--transition);
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
        }

        .suggestions-container {
            position: relative;
        }

        .suggestions-list {
            position: absolute;
            width: 100%;
            max-height: 300px;
            overflow-y: auto;
            z-index: 1000;
            background: white;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-top: -1px;
            display: none;
            border: 1px solid var(--light-gray);
            border-top: none;
        }

        .suggestions-list.show {
            display: block;
        }

        .suggestion-item {
            padding: 0.75rem 1rem;
            cursor: pointer;
            transition: var(--transition);
            border-bottom: 1px solid var(--light-gray);
        }

        .suggestion-item:last-child {
            border-bottom: none;
        }

        .suggestion-item:hover,
        .suggestion-item.active {
            background-color: var(--primary);
            color: white;
        }

        .btn-action {
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-2px);
        }

        .btn-outline {
            background-color: white;
            border: 1px solid var(--light-gray);
            color: var(--dark);
        }

        .btn-outline:hover {
            background-color: #f8fafc;
            border-color: var(--gray);
        }

        .route-card {
            background: white;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: var(--transition);
            height: 100%;
            border: none;
            position: relative;
            overflow: hidden;
        }

        .route-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--card-shadow-hover);
        }

        .route-badge {
            position: absolute;
            top: 0;
            right: 0;
            background: var(--primary);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0 16px 0 16px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .route-header {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .route-info {
            margin-bottom: 1rem;
        }

        .info-label {
            font-weight: 600;
            color: var(--gray);
            display: inline-block;
            min-width: 50px;
        }

        .stages-list {
            list-style: none;
            padding-left: 0;
            margin-top: 1rem;
        }

        .stages-list li {
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--light-gray);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .stages-list li:last-child {
            border-bottom: none;
        }

        .stage-icon {
            color: var(--primary);
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .stage-name {
            flex-grow: 1;
        }

        .error-message {
            background: #fee2e2;
            color: #b91c1c;
            padding: 1.5rem;
            border-radius: 16px;
            margin: 2rem auto;
            max-width: 800px;
            text-align: center;
            font-weight: 600;
            box-shadow: var(--card-shadow);
        }

        .no-results {
            background: white;
            padding: 2rem;
            border-radius: 16px;
            text-align: center;
            color: var(--gray);
            font-size: 1.1rem;
            box-shadow: var(--card-shadow);
            max-width: 800px;
            margin: 0 auto;
        }

        .no-results i {
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: 1rem;
        }

        .footer {
            background: white;
            padding: 1.5rem;
            margin-top: 3rem;
            text-align: center;
            color: var(--gray);
            font-size: 0.9rem;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
        }

        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 2rem;
            }
            
            .search-card {
                padding: 1.5rem;
            }
            
            .btn-action {
                width: 100%;
                margin-bottom: 0.75rem;
            }
        }

        @media (max-width: 576px) {
            .page-header {
                padding: 2rem 1rem;
            }
            
            .page-header h1 {
                font-size: 1.75rem;
            }
            
            .route-header {
                font-size: 1.1rem;
            }
        }
    </style>
</head>
<body>
<?php include 'AdminNavbar.php'; ?>

<header class="page-header">
    <div class="container">
        <h1>Find Routes Between Stages</h1>
        <p>Discover available bus routes connecting your desired locations</p>
    </div>
</header>

<main class="container">
    <div class="search-container">
        <div class="search-card">
            <form id="routeForm" method="GET" action="">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="suggestions-container">
                            <label for="fromStage" class="form-label">Departure Stage</label>
                            <input type="text" class="form-control" id="fromStage" name="fromStage" autocomplete="off" required
                                value="<?= htmlspecialchars($_GET['fromStage'] ?? '') ?>"
                                placeholder="Enter starting point">
                            <div id="fromStageList" class="suggestions-list"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="suggestions-container">
                            <label for="toStage" class="form-label">Destination Stage</label>
                            <input type="text" class="form-control" id="toStage" name="toStage" autocomplete="off" required
                                value="<?= htmlspecialchars($_GET['toStage'] ?? '') ?>"
                                placeholder="Enter destination">
                            <div id="toStageList" class="suggestions-list"></div>
                        </div>
                    </div>
                </div>
                <div class="d-flex flex-column flex-sm-row justify-content-center gap-3 mt-4">
                    <button type="submit" class="btn btn-primary btn-action">
                        <i class="bi bi-search"></i> Find Routes
                    </button>
                    <button type="button" id="clearBtn" class="btn btn-outline btn-action">
                        <i class="bi bi-x-circle"></i> Clear
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="routeResults">
    <?php
    $config = include('config.php');
    $apiBaseUrl = $config['api_base_url'];

    if (!empty($_GET['fromStage']) && !empty($_GET['toStage'])) {
        $fromStage = rawurlencode($_GET['fromStage']);
        $toStage = rawurlencode($_GET['toStage']);
        $apiUrl = $apiBaseUrl . "FindRoutesBetweenStages/$fromStage/$toStage";

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $apiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPGET => true,
            CURLOPT_HTTPHEADER => [
                'User-Agent: PHP-cURL/1.0'
            ],
        ]);

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            echo "<div class='error-message'><i class='bi bi-exclamation-triangle-fill me-2'></i> Error connecting to server: " . htmlspecialchars(curl_error($curl)) . "</div>";
        } else {
            $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if ($http_code != 200) {
                echo "<div class='error-message'><i class='bi bi-exclamation-triangle-fill me-2'></i> Server returned an error (HTTP $http_code). Please try again.</div>";
            } else {
                $data = json_decode($response, true);
                if (is_null($data)) {
                    echo "<div class='no-results'><i class='bi bi-info-circle-fill'></i><p class='mt-3'>No routes found between the specified stages</p></div>";
                } elseif (empty($data)) {
                    echo "<div class='no-results'><i class='bi bi-info-circle-fill'></i><p class='mt-3'>No routes found between the specified stages</p></div>";
                } else {
                    echo '<div class="row g-4">';
                    foreach ($data as $route) {
                        echo '<div class="col-lg-4 col-md-6">';
                        echo '<div class="route-card">';
                        echo '<div class="route-badge">Route</div>';
                        echo '<div class="route-header"><i class="bi bi-bus-front"></i> ' . htmlspecialchars($route['code']) . '</div>';
                        echo '<div class="route-info">';
                        echo '<p><span class="info-label">From:</span> ' . htmlspecialchars($route['from']) . '</p>';
                        echo '<p><span class="info-label">To:</span> ' . htmlspecialchars($route['to']) . '</p>';
                        echo '</div>';
                        echo '<hr>';
                        echo '<h6 class="fw-semibold"><i class="bi bi-signpost-split"></i> Route Stages</h6>';
                        echo '<ul class="stages-list">';
                        foreach ($route['stages'] as $stage) {
                            echo '<li><i class="bi bi-geo-alt-fill stage-icon"></i><span class="stage-name">' . htmlspecialchars($stage['stageName']) . '</span></li>';
                        }
                        echo '</ul>';
                        echo '</div>';
                        echo '</div>';
                    }
                    echo '</div>';
                }
            }
        }
        curl_close($curl);
    }
    ?>
    </div>
</main>

<footer class="footer">
    <div class="container">
        &copy; <?php echo date("Y"); ?> Bus Management System. All rights reserved.
    </div>
</footer>

<script>
    const API_BASE_URL = "<?php echo $apiBaseUrl; ?>";

    function debounce(func, delay) {
        let timeout;
        return (...args) => {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), delay);
        };
    }

    async function fetchStages(query) {
        if (!query.trim()) return [];
        const url = `${API_BASE_URL}SearchStages/${encodeURIComponent(query)}`;
        try {
            const res = await fetch(url);
            if (!res.ok) return [];
            return await res.json();
        } catch {
            return [];
        }
    }

    function setupAutocomplete(inputId, listId) {
        const input = document.getElementById(inputId);
        const list = document.getElementById(listId);
        let activeIndex = -1;

        input.addEventListener('input', debounce(async () => {
            const query = input.value.trim();
            if (!query) {
                list.classList.remove('show');
                return;
            }
            
            const results = await fetchStages(query);
            if (!Array.isArray(results) || results.length === 0) {
                list.classList.remove('show');
                return;
            }
            
            list.innerHTML = results.map(stage => {
                const name = typeof stage === 'string' ? stage : stage.stageName || '';
                return `<div class="suggestion-item">${name}</div>`;
            }).join('');
            list.classList.add('show');
            activeIndex = -1;
            
            const items = list.querySelectorAll('.suggestion-item');
            items.forEach((item, index) => {
                item.addEventListener('click', () => {
                    input.value = item.textContent;
                    list.classList.remove('show');
                });
            });
        }, 300));

        input.addEventListener('keydown', (e) => {
            const items = list.querySelectorAll('.suggestion-item');
            
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                activeIndex = (activeIndex < items.length - 1) ? activeIndex + 1 : activeIndex;
                updateActiveItem(items);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                activeIndex = (activeIndex > 0) ? activeIndex - 1 : 0;
                updateActiveItem(items);
            } else if (e.key === 'Enter' && activeIndex >= 0) {
                e.preventDefault();
                input.value = items[activeIndex].textContent;
                list.classList.remove('show');
            }
        });

        function updateActiveItem(items) {
            items.forEach(item => item.classList.remove('active'));
            if (activeIndex >= 0) {
                items[activeIndex].classList.add('active');
                items[activeIndex].scrollIntoView({ block: 'nearest' });
            }
        }

        document.addEventListener('click', (e) => {
            if (!input.contains(e.target) && !list.contains(e.target)) {
                list.classList.remove('show');
            }
        });
    }

    setupAutocomplete('fromStage', 'fromStageList');
    setupAutocomplete('toStage', 'toStageList');

    document.getElementById('clearBtn').addEventListener('click', () => {
        document.getElementById('fromStage').value = '';
        document.getElementById('toStage').value = '';
        document.getElementById('fromStageList').innerHTML = '';
        document.getElementById('toStageList').classList.remove('show');
        document.getElementById('routeResults').innerHTML = '';
        window.location.href = window.location.pathname;
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>