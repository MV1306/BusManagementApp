<?php
require_once 'AdminAuth.php';

checkAuth();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Find Routes Between Stages</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            /* Modern color palette */
            --primary-color: #4361ee;
            --primary-light: #4895ef;
            --secondary-color: #3f37c9;
            --accent-color: #f72585;
            --success-color: #4cc9f0;
            --warning-color: #f8961e;
            --danger-color: #f94144;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --gray-600: #6c757d;
            --gray-300: #dee2e6;
            
            --border-radius: 12px;
            --border-radius-sm: 8px;
            --box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            --box-shadow-lg: 0 8px 30px rgba(0, 0, 0, 0.12);
            --transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background-color: #f5f7ff;
            color: var(--dark-color);
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
            font-size: 2rem;
            background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            letter-spacing: -0.5px;
        }

        .search-card {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 2.5rem;
            margin-bottom: 3rem;
            border: none;
            transition: var(--transition);
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .search-card:hover {
            box-shadow: var(--box-shadow-lg);
            transform: translateY(-2px);
            background: rgba(255, 255, 255, 0.95);
        }

        .form-label {
            font-weight: 600;
            margin-bottom: 0.75rem;
            color: var(--dark-color);
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-control {
            border-radius: var(--border-radius-sm);
            padding: 0.75rem 1.25rem;
            border: 1px solid var(--gray-300);
            transition: var(--transition);
            font-size: 1rem;
            background-color: rgba(255, 255, 255, 0.8);
        }

        .form-control:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.15);
            background-color: white;
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
            border: 1px solid var(--primary-light);
            border-top: none;
            border-radius: 0 0 var(--border-radius-sm) var(--border-radius-sm);
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.15);
            margin-top: -1px;
            display: none;
        }

        .suggestions-list.show {
            display: block;
        }

        .suggestion-item {
            padding: 0.75rem 1.25rem;
            cursor: pointer;
            transition: var(--transition);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            color: var(--dark-color);
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .suggestion-item:hover,
        .suggestion-item.active {
            background-color: rgba(67, 97, 238, 0.08);
            color: var(--primary-color);
        }

        .suggestion-item .highlight {
            font-weight: 600;
            color: var(--primary-color);
            background-color: rgba(67, 97, 238, 0.1);
        }

        .loading-suggestion {
            padding: 1rem;
            text-align: center;
            color: var(--gray-600);
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .loading-spinner {
            width: 1rem;
            height: 1rem;
            border: 2px solid var(--gray-300);
            border-top: 2px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .btn-action {
            padding: 0.875rem 1.75rem;
            border-radius: var(--border-radius-sm);
            font-weight: 600;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-size: 1rem;
            letter-spacing: 0.5px;
            border: none;
        }

        .btn-submit {
            background-color: var(--primary-color);
            color: white;
            box-shadow: 0 4px 14px rgba(67, 97, 238, 0.3);
        }

        .btn-submit:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(67, 97, 238, 0.4);
        }

        .btn-submit.loading {
            position: relative;
            color: transparent;
        }

        .btn-submit.loading:after {
            content: '';
            position: absolute;
            width: 1.25rem;
            height: 1.25rem;
            border: 2px solid white;
            border-top: 2px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        .btn-clear {
            background-color: white;
            color: var(--gray-600);
            border: 1px solid var(--gray-300);
        }

        .btn-clear:hover {
            background-color: var(--light-color);
            color: var(--dark-color);
            transform: translateY(-2px);
            border-color: var(--gray-600);
        }

        .route-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 1.75rem;
            margin-bottom: 1.5rem;
            border: none;
            transition: var(--transition);
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        .route-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(to bottom, var(--primary-color), var(--accent-color));
        }

        .route-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--box-shadow-lg);
        }

        .route-header {
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .route-info {
            margin-bottom: 1.25rem;
        }

        .info-label {
            font-weight: 600;
            color: var(--gray-600);
            display: inline-block;
            min-width: 50px;
            font-size: 0.9rem;
        }

        .stages-list {
            list-style: none;
            padding-left: 0;
            margin-top: 1.25rem;
        }

        .stages-list li {
            padding: 0.75rem 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            position: relative;
            padding-left: 1.5rem;
        }

        .stages-list li:last-child {
            border-bottom: none;
        }

        .stages-list li::before {
            content: '';
            position: absolute;
            left: 0;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: var(--primary-light);
        }

        .stage-icon {
            color: var(--primary-color);
            font-size: 1.1rem;
        }

        .error-message {
            background: #fff0f3;
            color: var(--danger-color);
            padding: 1.25rem;
            border-radius: var(--border-radius);
            margin: 2rem auto;
            max-width: 600px;
            text-align: center;
            font-weight: 600;
            border: 1px solid #ffccd5;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
        }

        .no-results {
            text-align: center;
            padding: 2.5rem;
            color: var(--gray-600);
            font-size: 1.1rem;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
        }

        .no-results i {
            font-size: 2rem;
            color: var(--primary-light);
        }

        .loading-results {
            text-align: center;
            padding: 3rem;
            color: var(--gray-600);
        }

        .loading-results .spinner {
            width: 3rem;
            height: 3rem;
            border: 4px solid var(--gray-300);
            border-top: 4px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 1.5rem;
        }

        /* Modern scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary-light);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-color);
        }

        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .route-card {
            animation: fadeIn 0.4s ease-out forwards;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .search-card {
                padding: 1.75rem;
            }
            
            .btn-action {
                width: 100%;
                margin-bottom: 0.75rem;
            }
            
            .route-header {
                font-size: 1.2rem;
            }
            
            .page-header h2 {
                font-size: 1.75rem;
            }
        }

        /* Gradient background */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #f5f7ff 0%, #f8f9fa 100%);
            z-index: -1;
        }
    </style>
</head>
<body>
<?php include 'AdminNavbar.php'; ?>

<div class="container py-5">
    <div class="page-header">
        <h2>Find Bus Routes Between Stages</h2>
        <p class="text-muted mt-2">Discover the best routes connecting your locations</p>
    </div>

    <div class="search-card">
        <form id="routeForm" method="GET" action="">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="suggestions-container">
                        <label for="fromStage" class="form-label">
                            <i class="bi bi-geo-alt"></i> From Stage
                        </label>
                        <input type="text" class="form-control" id="fromStage" name="fromStage" autocomplete="off" required
                            value="<?= htmlspecialchars($_GET['fromStage'] ?? '') ?>" placeholder="Enter starting point">
                        <div id="fromStageList" class="suggestions-list"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="suggestions-container">
                        <label for="toStage" class="form-label">
                            <i class="bi bi-geo-alt-fill"></i> To Stage
                        </label>
                        <input type="text" class="form-control" id="toStage" name="toStage" autocomplete="off" required
                            value="<?= htmlspecialchars($_GET['toStage'] ?? '') ?>" placeholder="Enter destination">
                        <div id="toStageList" class="suggestions-list"></div>
                    </div>
                </div>
            </div>
            <div class="d-flex flex-column flex-sm-row justify-content-center gap-3 mt-4 pt-2">
                <button type="submit" id="submitBtn" class="btn btn-action btn-submit">
                    <i class="bi bi-search"></i> Find Routes
                </button>
                <button type="button" id="clearBtn" class="btn btn-action btn-clear">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset
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
            echo "<div class='error-message'><i class='bi bi-exclamation-triangle-fill'></i> cURL error: " . htmlspecialchars(curl_error($curl)) . "</div>";
        } else {
            $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if ($http_code != 200) {
                echo "<div class='error-message'><i class='bi bi-exclamation-triangle-fill'></i> API returned HTTP code $http_code.</div>";
            } else {
                $data = json_decode($response, true);
                if (is_null($data)) {
                    echo "<div class='error-message'><i class='bi bi-exclamation-triangle-fill'></i> No routes found between the given stages.</div>";
                } elseif (empty($data)) {
                    echo "<div class='no-results'>
                            <i class='bi bi-map'></i>
                            <div>No routes found between the given stages</div>
                            <small class='text-muted'>Try different locations or check your spelling</small>
                          </div>";
                } else {
                    echo '<div class="row g-4">';
                    foreach ($data as $index => $route) {
                        // Add delay to animation
                        $animationDelay = $index * 0.1;
                        echo '<div class="col-lg-4 col-md-6">';
                        echo '<div class="route-card" style="animation-delay: '.$animationDelay.'s">';
                        echo '<div class="route-header"><i class="bi bi-bus-front"></i> Route: ' . htmlspecialchars($route['code']) . '</div>';
                        echo '<div class="route-info">';
                        echo '<p><span class="info-label">From:</span> ' . htmlspecialchars($route['from']) . '</p>';
                        echo '<p><span class="info-label">To:</span> ' . htmlspecialchars($route['to']) . '</p>';
                        echo '</div>';
                        echo '<hr>';
                        echo '<h6 class="fw-semibold"><i class="bi bi-signpost-split"></i> Stages</h6>';
                        echo '<ul class="stages-list">';
                        foreach ($route['stages'] as $stage) {
                            echo '<li><i class="bi bi-dot"></i>' . htmlspecialchars($stage['stageName']) . '</li>';
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

    function highlightMatch(text, query) {
        if (!query) return text;
        const escapedQuery = escapeRegExp(query);
        const regex = new RegExp(`(${escapedQuery})`, 'gi');
        return text.replace(regex, '<span class="highlight">$1</span>');
    }

    function escapeRegExp(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '');
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
            
            // Show loading state
            list.innerHTML = '<div class="loading-suggestion"><div class="loading-spinner"></div> Searching...</div>';
            list.classList.add('show');
            
            const results = await fetchStages(query);
            if (!Array.isArray(results) || results.length === 0) {
                list.innerHTML = '<div class="loading-suggestion">No results found</div>';
                return;
            }
            
            list.innerHTML = results.map(stage => {
                const name = typeof stage === 'string' ? stage : stage.stageName || '';
                return `<div class="suggestion-item">${highlightMatch(name, query)}</div>`;
            }).join('');
            
            activeIndex = -1;
            
            const items = list.querySelectorAll('.suggestion-item');
            items.forEach((item, index) => {
                item.addEventListener('click', () => {
                    input.value = item.textContent;
                    list.classList.remove('show');
                    input.focus();
                });
                
                item.addEventListener('mouseenter', () => {
                    items.forEach(i => i.classList.remove('active'));
                    item.classList.add('active');
                    activeIndex = index;
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
        
        // Show suggestions when input is focused
        input.addEventListener('focus', async () => {
            if (input.value.trim() && !list.classList.contains('show')) {
                const query = input.value.trim();
                list.innerHTML = '<div class="loading-suggestion"><div class="loading-spinner"></div> Searching...</div>';
                list.classList.add('show');
                
                const results = await fetchStages(query);
                if (results.length > 0) {
                    list.innerHTML = results.map(stage => {
                        const name = typeof stage === 'string' ? stage : stage.stageName || '';
                        return `<div class="suggestion-item">${highlightMatch(name, query)}</div>`;
                    }).join('');
                } else {
                    list.innerHTML = '<div class="loading-suggestion">No results found</div>';
                }
            }
        });
    }

    document.getElementById('clearBtn').addEventListener('click', () => {
        document.getElementById('fromStage').value = '';
        document.getElementById('toStage').value = '';
        document.getElementById('fromStageList').innerHTML = '';
        document.getElementById('toStageList').classList.remove('show');
        document.getElementById('routeResults').innerHTML = '';
        window.location.href = window.location.pathname;
    });

    // Form submission with loading state
    document.getElementById('routeForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const fromStage = document.getElementById('fromStage').value.trim();
        const toStage = document.getElementById('toStage').value.trim();
        
        if (!fromStage || !toStage) {
            return;
        }
        
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.innerHTML = '';
        submitBtn.classList.add('loading');
        
        // Show loading state in results area
        document.getElementById('routeResults').innerHTML = `
            <div class="loading-results">
                <div class="spinner"></div>
                <p>Finding routes between ${fromStage} and ${toStage}...</p>
            </div>
        `;
        
        // Submit the form after a small delay to allow the UI to update
        setTimeout(() => {
            e.target.submit();
        }, 100);
    });

    // Initialize autocomplete for both inputs
    setupAutocomplete('fromStage', 'fromStageList');
    setupAutocomplete('toStage', 'toStageList');
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>