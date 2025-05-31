<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Find Routes Between Stages</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />

    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f5f7fa;
            padding: 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 40px;
            font-weight: 600;
            color: #2c3e50;
        }

        form#routeForm {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
            max-width: 900px;
            margin: 0 auto 40px auto;
        }

        label {
            font-weight: 600;
            margin-bottom: 6px;
            color: #2d3436;
        }

        input[type="text"] {
            border-radius: 8px;
            padding: 10px 14px;
        }

        input[type="text"]:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }

        ul.suggestions {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            z-index: 999;
            background: #fff;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 8px 8px;
            max-height: 200px;
            overflow-y: auto;
        }

        ul.suggestions li {
            padding: 10px;
            cursor: pointer;
            transition: background 0.3s;
        }

        ul.suggestions li:hover,
        ul.suggestions li.active {
            background-color: #007bff;
            color: #fff;
        }

        .btn-submit {
            background-color: #28a745;
            color: #fff;
        }

        .btn-submit:hover {
            background-color: #218838;
        }

        .btn-clear {
            background-color: #dc3545;
            color: #fff;
        }

        .btn-clear:hover {
            background-color: #c82333;
        }

        .route {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.05);
        }

        .route-header {
            font-size: 1.2rem;
            font-weight: 600;
            color: #007bff;
            margin-bottom: 10px;
        }

        .route-info p {
            margin: 5px 0;
            color: #333;
        }

        .label {
            font-weight: 600;
            color: #6c757d;
        }

        ul.stages-list {
            padding-left: 20px;
            list-style: disc;
        }

        ul.stages-list li {
            margin-bottom: 5px;
        }

        p.error {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 8px;
            margin-top: 20px;
            font-weight: 600;
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<h2>Find Bus Routes Between Stages</h2>

<form id="routeForm" method="GET" action="">
    <div class="row g-3">
        <div class="col-md-6 position-relative">
            <label for="fromStage" class="form-label">From Stage:</label>
            <input type="text" class="form-control" id="fromStage" name="fromStage" autocomplete="off" required
                value="<?= htmlspecialchars($_GET['fromStage'] ?? '') ?>">
            <ul id="fromStageList" class="suggestions"></ul>
        </div>
        <div class="col-md-6 position-relative">
            <label for="toStage" class="form-label">To Stage:</label>
            <input type="text" class="form-control" id="toStage" name="toStage" autocomplete="off" required
                value="<?= htmlspecialchars($_GET['toStage'] ?? '') ?>">
            <ul id="toStageList" class="suggestions"></ul>
        </div>
    </div>
    <div class="d-flex justify-content-center gap-3 mt-4">
        <button type="submit" class="btn btn-submit px-4">Find Routes</button>
        <button type="button" id="clearBtn" class="btn btn-clear px-4">Clear</button>
    </div>
</form>

<div class="container" id="routeResults">
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
        echo "<p class='error'>cURL error: " . htmlspecialchars(curl_error($curl)) . "</p>";
    } else {
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($http_code != 200) {
            echo "<p class='error'>API returned HTTP code $http_code.</p>";
        } else {
            $data = json_decode($response, true);
            if (is_null($data)) {
                echo "<p class='error'>Failed to decode JSON response.</p>";
            } elseif (empty($data)) {
                echo "<p>No routes found between the given stages.</p>";
            } else {
                echo '<div class="row">';
                foreach ($data as $route) {
                    echo '<div class="col-lg-4 col-md-6">';
                    echo '<div class="route">';
                    echo '<div class="route-header">Route Code: ' . htmlspecialchars($route['code']) . '</div>';
                    echo '<div class="route-info">';
                    echo '<p><span class="label">From:</span> ' . htmlspecialchars($route['from']) . '</p>';
                    echo '<p><span class="label">To:</span> ' . htmlspecialchars($route['to']) . '</p>';
                    echo '</div>';
                    echo '<hr>';
                    echo '<h6>Stages:</h6>';
                    echo '<ul class="stages-list">';
                    foreach ($route['stages'] as $stage) {
                        echo '<li><i class="bi bi-geo-alt-fill text-primary me-1"></i>' . htmlspecialchars($stage['stageName']) . '</li>';
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

        input.addEventListener('input', debounce(async () => {
            const query = input.value.trim();
            if (!query) return list.innerHTML = '';
            const results = await fetchStages(query);
            if (!Array.isArray(results) || results.length === 0) return list.innerHTML = '';
            list.innerHTML = results.map(stage => {
                const name = typeof stage === 'string' ? stage : stage.stageName || '';
                return `<li tabindex="0">${name}</li>`;
            }).join('');
            list.querySelectorAll('li').forEach(li => {
                li.addEventListener('click', () => {
                    input.value = li.textContent;
                    list.innerHTML = '';
                });
                li.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        input.value = li.textContent;
                        list.innerHTML = '';
                        input.focus();
                    }
                });
            });
        }, 300));

        input.addEventListener('blur', () => {
            setTimeout(() => list.innerHTML = '', 200);
        });
    }

    setupAutocomplete('fromStage', 'fromStageList');
    setupAutocomplete('toStage', 'toStageList');

    document.getElementById('clearBtn').addEventListener('click', () => {
        document.getElementById('fromStage').value = '';
        document.getElementById('toStage').value = '';
        document.getElementById('fromStageList').innerHTML = '';
        document.getElementById('toStageList').innerHTML = '';
        document.getElementById('routeResults').innerHTML = '';
        window.location.href = window.location.pathname;
    });
</script>
</body>
</html>
