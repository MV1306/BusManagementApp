<!DOCTYPE html>
<html>
<head>
    <title>Find Routes Between Stages</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; max-width: 800px; margin: auto; }
        input, button { padding: 8px; margin: 5px 0; width: 100%; box-sizing: border-box; }
        button { background-color: #28a745; color: white; border: none; cursor: pointer; width: auto; padding: 8px 16px; }
        .route { margin-top: 20px; padding: 10px; border: 1px solid #ccc; border-radius: 6px; }
        .stage { margin-left: 20px; }
        p.error { color: red; }
        .input-group { position: relative; }
        ul.suggestions {
            list-style: none;
            margin: 0;
            padding: 0;
            max-height: 150px;
            overflow-y: auto;
            border: 1px solid #ccc;
            border-top: none;
            position: absolute;
            width: 100%;
            background: white;
            z-index: 1000;
        }
        ul.suggestions li {
            padding: 6px;
            cursor: pointer;
        }
        ul.suggestions li:hover {
            background-color: #ddd;
        }
        .form-row {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .form-row > * {
            flex: 1;
        }
        .buttons {
            margin-top: 10px;
            display: flex;
            gap: 10px;
        }
        .buttons button {
            flex: 1;
        }
    </style>
</head>
<body>

<h2>Find Bus Routes Between Stages</h2>

<form id="routeForm" method="GET" action="">
    <div class="form-row">
        <div class="input-group">
            <label for="fromStage">From Stage:</label>
            <input id="fromStage" name="fromStage" autocomplete="off" required
                value="<?= htmlspecialchars($_GET['fromStage'] ?? '') ?>" />
            <ul id="fromStageList" class="suggestions"></ul>
        </div>

        <div class="input-group">
            <label for="toStage">To Stage:</label>
            <input id="toStage" name="toStage" autocomplete="off" required
                value="<?= htmlspecialchars($_GET['toStage'] ?? '') ?>" />
            <ul id="toStageList" class="suggestions"></ul>
        </div>
    </div>

    <div class="buttons">
        <button type="submit">Find Routes</button>
        <button type="button" id="clearBtn" style="flex: 1; background-color: #dc3545;">Clear</button>
    </div>

    </div>
</form>

<div id="routeResults">
<?php
if (!empty($_GET['fromStage']) && !empty($_GET['toStage'])) {
    // Encode parameters for URL
    $fromStage = rawurlencode($_GET['fromStage']);
    $toStage = rawurlencode($_GET['toStage']);

    // API endpoint with route parameters
    $apiUrl = "https://192.168.29.141/BusManagementAPI/FindRoutesBetweenStages/$fromStage/$toStage";

    // Initialize cURL
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

    // Execute cURL request
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
                // Display routes
                foreach ($data as $route) {
                    echo "<div class='route'>";
                    echo "<strong>Route Code:</strong> " . htmlspecialchars($route['code']) . "<br>";
                    echo "<strong>From:</strong> " . htmlspecialchars($route['from']) . "<br>";
                    echo "<strong>To:</strong> " . htmlspecialchars($route['to']) . "<br>";
                    echo "<strong>Stages:</strong><ul>";
                    foreach ($route['stages'] as $stage) {
                        echo "<li class='stage'>" . htmlspecialchars($stage['stageName']) . "</li>";
                    }
                    echo "</ul></div>";
                }
            }
        }
    }
    curl_close($curl);
}
?>
</div>

<script>
    // Helper: debounce function to limit API calls while typing
    function debounce(func, wait) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }

    // Fetch suggestions from SearchStages API
    async function fetchStages(query) {
        if (!query.trim()) return [];
        const url = `http://192.168.29.141/BusManagementAPI/SearchStages/${encodeURIComponent(query)}`;
        try {
            const response = await fetch(url);
            if (!response.ok) return [];
            const data = await response.json();
            return data; // assuming data is an array of stage names or objects with stageName
        } catch (e) {
            return [];
        }
    }

    function setupAutocomplete(inputId, listId) {
        const input = document.getElementById(inputId);
        const list = document.getElementById(listId);

        input.addEventListener('input', debounce(async () => {
            const query = input.value;
            if (!query) {
                list.innerHTML = '';
                return;
            }
            const stages = await fetchStages(query);
            if (!Array.isArray(stages) || stages.length === 0) {
                list.innerHTML = '';
                return;
            }

            list.innerHTML = stages.map(stage => {
                // stage could be string or object; adjust if needed
                let name = typeof stage === 'string' ? stage : stage.stageName || '';
                return `<li tabindex="0">${name}</li>`;
            }).join('');

            // Add click handlers for list items
            list.querySelectorAll('li').forEach(li => {
                li.addEventListener('click', () => {
                    input.value = li.textContent;
                    list.innerHTML = '';
                });
                // Also allow keyboard selection
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

        // Hide suggestions when input loses focus (delay to allow click)
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
		history.replaceState(null, '', window.location.pathname);
        document.getElementById('fromStage').focus();
    });
</script>

</body>
</html>
