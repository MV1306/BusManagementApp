<!DOCTYPE html>
<html>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <title>Find Routes Between Stages</title>
    <style>
        /* Reset some default */
        * {
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            padding: 30px 20px;
            width: 97%;
            margin: 40px auto;
            background: #f9f9f9;
            color: #333;
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #2c3e50;
            font-weight: 600;
        }
        form#routeForm {
            background: white;
            padding: 25px 30px;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgb(0 0 0 / 0.1);
        }
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        .input-group {
            flex: 1;
            position: relative;
        }
        label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #34495e;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px 14px;
            font-size: 16px;
            border: 2px solid #ddd;
            border-radius: 8px;
            transition: border-color 0.25s ease;
        }
        input[type="text"]:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 6px rgba(0,123,255,0.3);
        }
        ul.suggestions {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            max-height: 160px;
            overflow-y: auto;
            background: white;
            border: 1px solid #ccc;
            border-top: none;
            border-radius: 0 0 8px 8px;
            z-index: 1000;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        ul.suggestions li {
            padding: 10px 15px;
            cursor: pointer;
            font-size: 15px;
            color: #555;
            user-select: none;
        }
        ul.suggestions li:hover,
        ul.suggestions li.active {
            background-color: #007bff;
            color: white;
        }
        .buttons {
            display: flex;
            gap: 15px;
        }
        .buttons button {
            flex: 1;
            padding: 12px 0;
            font-size: 16px;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            color: white;
            user-select: none;
        }
        .buttons button[type="submit"] {
            background-color: #28a745;
        }
        .buttons button[type="submit"]:hover {
            background-color: #218838;
        }
        .buttons button#clearBtn {
            background-color: #dc3545;
        }
        .buttons button#clearBtn:hover {
            background-color: #c82333;
        }
        #routeResults {
            margin-top: 30px;
        }
        /* Card styles */
        .route {
            background: white;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px 25px;
            margin-bottom: 20px;
            box-shadow: 0 3px 10px rgb(0 0 0 / 0.05);
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .route strong {
            color: #2c3e50;
        }
        .route-header {
            margin-bottom: 10px;
            font-weight: 600;
            font-size: 1.2rem;
            color: #007bff;
        }
        .route-info p {
            margin: 5px 0;
            font-size: 1rem;
            color: #34495e;
        }
        .label {
            font-weight: 600;
            color: #6c757d;
            margin-right: 5px;
        }
        hr {
            margin: 15px 0;
            border-color: #eee;
        }
        h6 {
            margin-bottom: 10px;
            font-weight: 600;
            color: #495057;
        }
        ul.stages-list {
            list-style-type: disc;
            padding-left: 20px;
            margin: 0;
            flex-grow: 1;
            overflow-y: auto;
        }
        ul.stages-list li {
            font-size: 0.95rem;
            color: #555;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
        }
        ul.stages-list li i {
            color: #007bff;
            margin-right: 8px;
            font-size: 1rem;
        }
        p.error {
            color: #dc3545;
            font-weight: 600;
            background: #fddede;
            padding: 12px 15px;
            border-radius: 8px;
            margin-top: 25px;
        }
    </style>
    <!-- Bootstrap Icons CDN for geo icon -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
</head>
<body>
<?php include 'navbar.php'; ?>

<h2>Find Bus Routes Between Stages</h2>

<form id="routeForm" method="GET" action="">
    <div class="form-row">
        <div class="input-group">
            <label for="fromStage">From Stage:</label>
            <input id="fromStage" name="fromStage" type="text" autocomplete="off" required
                value="<?= htmlspecialchars($_GET['fromStage'] ?? '') ?>" />
            <ul id="fromStageList" class="suggestions"></ul>
        </div>

        <div class="input-group">
            <label for="toStage">To Stage:</label>
            <input id="toStage" name="toStage" type="text" autocomplete="off" required
                value="<?= htmlspecialchars($_GET['toStage'] ?? '') ?>" />
            <ul id="toStageList" class="suggestions"></ul>
        </div>
    </div>

    <div class="buttons">
        <button type="submit">Find Routes</button>
        <button type="button" id="clearBtn">Clear</button>
    </div>
</form>

<div id="routeResults">
<?php
if (!empty($_GET['fromStage']) && !empty($_GET['toStage'])) {
    $fromStage = rawurlencode($_GET['fromStage']);
    $toStage = rawurlencode($_GET['toStage']);
    $apiUrl = "http://172.20.10.2/BusManagementAPI/FindRoutesBetweenStages/$fromStage/$toStage";

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
                echo '<div class="row">'; // Bootstrap row

                foreach ($data as $route) {
                    echo '<div class="col-md-4 mb-4">';  // 3 columns per row on md+
                    echo "<div class='route'>";
                    echo "<div class='route-header'>";
                    echo "Route Code: " . htmlspecialchars($route['code']);
                    echo "</div>";
                    echo "<div class='route-info'>";
                    echo "<p><span class='label'>From:</span> " . htmlspecialchars($route['from']) . "</p>";
                    echo "<p><span class='label'>To:</span> " . htmlspecialchars($route['to']) . "</p>";
                    echo "</div>";
                    echo "<hr>";
                    echo "<h6>Stages:</h6>";
                    echo "<ul class='stages-list'>";
                    foreach ($route['stages'] as $stage) {
                        echo "<li>";
                        echo "<i class='bi bi-geo-alt-fill'></i>";
                        echo htmlspecialchars($stage['stageName']);
                        echo "</li>";
                    }
                    echo "</ul>";
                    echo "</div>"; // .route
                    echo "</div>"; // .col-md-4
                }

                echo '</div>'; // end .row
            }
        }
    }
    curl_close($curl);
}
?>
</div>

<script>
     // Helper: debounce function to limit API calls while typingAdd commentMore actions
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

    // Clear button functionality
    document.getElementById('clearBtn').addEventListener('click', function() {
        document.getElementById('fromStage').value = '';
        document.getElementById('toStage').value = '';
        document.getElementById('routeResults').innerHTML = '';
    });

    // You can add your autocomplete JS here if needed for fromStageList and toStageList
</script>
</body>
</html>
