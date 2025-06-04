<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Find Routes Between Stages</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />

    <style>
        :root {
            /* RCB Colors */
            --rcb-red: #CE1126;
            --rcb-gold: #F7D100;
            --rcb-black: #2F2F2F;
            --rcb-dark-grey: #4A4A4A;
            --rcb-light-grey: #E0E0E0;
            --rcb-white: #FFFFFF;

            --primary-color: var(--rcb-red);
            --secondary-color: #A90E20; /* A darker shade of RCB red */
            --success-color: #4CAF50; /* Standard green for success, or pick a relevant RCB shade */
            --danger-color: var(--rcb-gold); /* Using gold for danger/attention, as it contrasts well with red */
            --light-color: var(--rcb-light-grey);
            --dark-color: var(--rcb-black);
            --border-radius: 12px;
            --box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background-color: var(--rcb-light-grey);
            color: var(--rcb-dark-grey);
            line-height: 1.6;
        }

        .page-header {
            text-align: center;
            margin: 2rem 0 3rem;
            position: relative;
        }

        .page-header h2 {
            font-weight: 700;
            color: var(--dark-color);
            position: relative;
            display: inline-block;
            padding-bottom: 0.5rem;
        }

        .page-header h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: var(--primary-color);
            border-radius: 2px;
        }

        .search-card {
            background-color: var(--rcb-white);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 2rem;
            margin-bottom: 3rem;
            border: none;
            transition: var(--transition);
        }

        .search-card:hover {
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.1);
        }

        .form-label {
            font-weight: 600;
            margin-bottom: 0.75rem;
            color: var(--dark-color);
        }

        .form-control {
            border-radius: var(--border-radius);
            padding: 0.75rem 1rem;
            border: 1px solid #e0e0e0;
            transition: var(--transition);
            color: var(--rcb-black); /* Text color for inputs */
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(var(--rcb-red-rgb), 0.25); /* Using RGB for rgba() */
        }
        /* Define RGB values for RCB colors */
        :root {
            --rcb-red-rgb: 206, 17, 38;
            --rcb-gold-rgb: 247, 209, 0;
            --rcb-black-rgb: 47, 47, 47;
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
            background: var(--rcb-white);
            border: 1px solid #e0e0e0;
            border-top: none;
            border-radius: 0 0 var(--border-radius) var(--border-radius);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-top: -1px;
            display: none;
        }

        .suggestions-list.show {
            display: block;
        }

        .suggestion-item {
            padding: 0.75rem 1rem;
            cursor: pointer;
            transition: var(--transition);
            border-bottom: 1px solid #f0f0f0;
            color: var(--rcb-dark-grey);
        }

        .suggestion-item:last-child {
            border-bottom: none;
        }

        .suggestion-item:hover,
        .suggestion-item.active {
            background-color: var(--primary-color);
            color: var(--rcb-white);
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

        .btn-submit {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: var(--rcb-white);
        }

        .btn-submit:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            transform: translateY(-2px);
        }

        .btn-clear {
            background-color: var(--rcb-gold);
            border: 1px solid var(--rcb-gold);
            color: var(--rcb-black);
        }

        .btn-clear:hover {
            background-color: #D8B700; /* Slightly darker gold on hover */
            border-color: #D8B700;
            transform: translateY(-2px);
        }

        .route-card {
            background: var(--rcb-white);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: none;
            transition: var(--transition);
            height: 100%;
        }

        .route-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.12);
        }

        .route-header {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary-color);
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
            color: var(--rcb-dark-grey); /* Changed from generic grey */
            display: inline-block;
            min-width: 50px;
        }

        .stages-list {
            list-style: none;
            padding-left: 0;
            margin-top: 1rem;
        }

        .stages-list li {
            padding: 0.5rem 0;
            border-bottom: 1px solid var(--rcb-light-grey);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .stages-list li:last-child {
            border-bottom: none;
        }

        .stage-icon {
            color: var(--primary-color);
            font-size: 1.1rem;
        }

        .error-message {
            background: #FCE4EC; /* Light pink from a standard color palette that blends well */
            color: var(--rcb-red);
            padding: 1rem;
            border-radius: var(--border-radius);
            margin: 2rem auto;
            max-width: 600px;
            text-align: center;
            font-weight: 600;
            border: 1px solid var(--rcb-red);
        }

        .no-results {
            text-align: center;
            padding: 2rem;
            color: var(--rcb-dark-grey);
            font-size: 1.1rem;
            background: var(--rcb-white);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }

        @media (max-width: 768px) {
            .search-card {
                padding: 1.5rem;
            }
            
            .btn-action {
                width: 100%;
                margin-bottom: 0.5rem;
            }
            
            .route-header {
                font-size: 1.1rem;
            }
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container py-4">
    <div class="page-header">
        <h2>Find Bus Routes Between Stages</h2>
    </div>

    <div class="search-card">
        <form id="routeForm" method="GET" action="">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="suggestions-container">
                        <label for="fromStage" class="form-label">From Stage</label>
                        <input type="text" class="form-control" id="fromStage" name="fromStage" autocomplete="off" required
                            value="<?= htmlspecialchars($_GET['fromStage'] ?? '') ?>">
                        <div id="fromStageList" class="suggestions-list"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="suggestions-container">
                        <label for="toStage" class="form-label">To Stage</label>
                        <input type="text" class="form-control" id="toStage" name="toStage" autocomplete="off" required
                            value="<?= htmlspecialchars($_GET['toStage'] ?? '') ?>">
                        <div id="toStageList" class="suggestions-list"></div>
                    </div>
                </div>
            </div>
            <div class="d-flex flex-column flex-sm-row justify-content-center gap-3 mt-4">
                <button type="submit" class="btn btn-primary btn-action btn-submit">
                    <i class="bi bi-search"></i> Find Routes
                </button>
                <button type="button" id="clearBtn" class="btn btn-action btn-clear">
                    <i class="bi bi-x-circle"></i> Clear
                </button>
            </div>
        </form>
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
            echo "<div class='error-message'><i class='bi bi-exclamation-triangle-fill me-2'></i> cURL error: " . htmlspecialchars(curl_error($curl)) . "</div>";
        } else {
            $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if ($http_code != 200) {
                echo "<div class='error-message'><i class='bi bi-exclamation-triangle-fill me-2'></i> API returned HTTP code $http_code.</div>";
            } else {
                $data = json_decode($response, true);
                if (is_null($data)) {
                    echo "<div class='error-message'><i class='bi bi-exclamation-triangle-fill me-2'></i> No routes found between the given stages.</div>";
                } elseif (empty($data)) {
                    echo "<div class='no-results'><i class='bi bi-info-circle-fill me-2'></i> No routes found between the given stages.</div>";
                } else {
                    echo '<div class="row g-4">';
                    foreach ($data as $route) {
                        echo '<div class="col-lg-4 col-md-6">';
                        echo '<div class="route-card">';
                        echo '<div class="route-header"><i class="bi bi-bus-front"></i> Route: ' . htmlspecialchars($route['code']) . '</div>';
                        echo '<div class="route-info">';
                        echo '<p><span class="info-label">From:</span> ' . htmlspecialchars($route['from']) . '</p>';
                        echo '<p><span class="info-label">To:</span> ' . htmlspecialchars($route['to']) . '</p>';
                        echo '</div>';
                        echo '<hr>';
                        echo '<h6 class="fw-semibold"><i class="bi bi-signpost-split"></i> Stages</h6>';
                        echo '<ul class="stages-list">';
                        foreach ($route['stages'] as $stage) {
                            echo '<li><i class="bi bi-geo-alt-fill stage-icon"></i>' . htmlspecialchars($stage['stageName']) . '</li>';
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
</div>

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