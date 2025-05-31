<?php
$routeId = $_GET['id'] ?? null;
if (!$routeId) {
    die("Route ID not specified");
}

$config = include('config.php');

$apiBaseUrl = $config['api_base_url'];

$routeApiUrl = $apiBaseUrl . "GetRouteById/" . urlencode($routeId);

$ch = curl_init($routeApiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
$response = curl_exec($ch);
curl_close($ch);

$routeData = json_decode($response, true);

if (!$routeData) {
    die("Failed to fetch route data");
}

$route = $routeData;
$stages = $routeData['stages'] ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Route and Stages</title>
    <style>
        /* Reset and basics */
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background: #f9f9f9;
            color: #333;
            line-height: 1.6;
        }
        h2, h3 {
            text-align: center;
            margin-top: 1.5rem;
            margin-bottom: 1rem;
            color: #222;
        }

        /* Navbar placeholder styling */
        /* You can customize your navbar.php to be responsive */
        /* Assuming navbar.php has responsive nav */

        /* Container */
        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 15px 2rem;
        }

        /* Route Form styling */
        #routeForm {
            background: white;
            padding: 20px 25px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgb(0 0 0 / 0.1);
            max-width: 600px;
            margin: 0 auto 2rem;
            display: flex;
            flex-wrap: wrap;
            gap: 1rem 1.5rem;
            align-items: center;
        }
        #routeForm label {
            flex: 1 1 45%;
            display: flex;
            flex-direction: column;
            font-weight: 600;
            font-size: 0.95rem;
            color: #444;
        }
        #routeForm input[type="text"] {
            padding: 8px 10px;
            margin-top: 6px;
            border: 1.5px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        #routeForm input[type="text"]:focus {
            border-color: #4CAF50;
            outline: none;
        }
        #routeForm button {
            flex: 1 1 100%;
            background-color: #4CAF50;
            border: none;
            color: white;
            padding: 12px 0;
            font-size: 1.1rem;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 10px;
        }
        #routeForm button:hover {
            background-color: #45a049;
        }

        /* Table container for responsiveness */
        .table-responsive {
            overflow-x: auto;
            margin: 0 auto 2rem;
            max-width: 1100px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgb(0 0 0 / 0.1);
            padding: 15px;
        }

        /* Table styling */
        table {
            border-collapse: collapse;
            width: 100%;
            min-width: 600px;
        }
        th, td {
            text-align: left;
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
            vertical-align: middle;
        }
        th {
            background-color: #4CAF50;
            color: white;
            font-weight: 600;
            position: sticky;
            top: 0;
            z-index: 1;
        }
        tr:hover {
            background-color: #f1f7f1;
        }
        td input[type="text"],
        td input[type="number"] {
            width: 100%;
            padding: 6px 8px;
            border: 1.2px solid #ccc;
            border-radius: 4px;
            font-size: 0.9rem;
        }
        td input[type="text"]:focus,
        td input[type="number"]:focus {
            border-color: #4CAF50;
            outline: none;
        }

        /* Buttons inside table */
        button.btn-edit, button.btn-delete {
            border: none;
            color: white;
            padding: 7px 12px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s ease;
            margin-right: 6px;
        }
        button.btn-edit {
            background-color: #4CAF50;
        }
        button.btn-edit:hover {
            background-color: #3e8e41;
        }
        button.btn-delete {
            background-color: #f44336;
        }
        button.btn-delete:hover {
            background-color: #c0392b;
        }
        button.btn-cancel {
            background-color: #777;
            color: white;
            border: none;
            padding: 7px 12px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s ease;
            margin-left: 6px;
        }
        button.btn-cancel:hover {
            background-color: #555;
        }

        /* Add Stage Form */
        #addStageForm {
            max-width: 1100px;
            margin: 0 auto 2rem;
            background: white;
            padding: 20px 25px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgb(0 0 0 / 0.1);
            display: flex;
            flex-wrap: wrap;
            gap: 12px 16px;
            justify-content: center;
        }
        #addStageForm input[type="text"],
        #addStageForm input[type="number"] {
            flex: 1 1 180px;
            padding: 10px 12px;
            font-size: 1rem;
            border: 1.5px solid #ccc;
            border-radius: 6px;
            transition: border-color 0.3s ease;
        }
        #addStageForm input[type="text"]:focus,
        #addStageForm input[type="number"]:focus {
            border-color: #4CAF50;
            outline: none;
        }
        #addStageForm button {
            flex: 1 1 150px;
            background-color: #4CAF50;
            border: none;
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        #addStageForm button:hover {
            background-color: #45a049;
        }

        /* Back button */
        .back-btn {
            display: block;
            max-width: 1100px;
            margin: 0 auto 40px;
            padding: 12px 25px;
            font-size: 1.1rem;
            font-weight: 600;
            background-color: #777;
            border: none;
            border-radius: 6px;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
            text-align: center;
            text-decoration: none;
        }
        .back-btn:hover {
            background-color: #555;
        }

        /* Responsive tweaks */
        @media (max-width: 720px) {
            #routeForm label {
                flex: 1 1 100%;
            }
            #addStageForm input[type="text"],
            #addStageForm input[type="number"],
            #addStageForm button {
                flex: 1 1 100%;
            }
            table {
                min-width: unset;
            }
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <h2>Edit Route</h2>

    <form id="routeForm">
        <input type="hidden" name="id" value="<?= htmlspecialchars($route['id']) ?>">
        <label>Route Code:
            <input type="text" name="code" value="<?= htmlspecialchars($route['code']) ?>" required>
        </label>
        <label>From:
            <input type="text" name="from" value="<?= htmlspecialchars($route['from']) ?>" required>
        </label>
        <label>To:
            <input type="text" name="to" value="<?= htmlspecialchars($route['to']) ?>" required>
        </label>
        <button type="button" onclick="saveRoute()">Save Route</button>
    </form>

    <h3>Stages for this Route</h3>
    <div class="table-responsive">
        <table id="stagesTable" aria-label="Stages Table">
            <thead>
                <tr>
                    <th scope="col">Stage Name</th>
                    <th scope="col">Stage Order</th>
                    <th scope="col">Distance From Start</th>
                    <th scope="col">Latitude</th>
                    <th scope="col">Longitude</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($stages as $index => $stage): ?>
                <tr data-index="<?= $index ?>">
                    <td class="stageName"><?= htmlspecialchars($stage['stageName']) ?></td>
                    <td class="stageOrder"><?= htmlspecialchars($stage['stageOrder']) ?></td>
                    <td class="distanceFromStart"><?= htmlspecialchars($stage['distanceFromStart']) ?></td>
                    <td class="latitude"><?= htmlspecialchars($stage['latitude'] ?? '') ?></td>
                    <td class="longitude"><?= htmlspecialchars($stage['longitude'] ?? '') ?></td>
                    <td>
                        <button class="btn-edit" onclick="editStage(this)" aria-label="Edit stage <?= htmlspecialchars($stage['stageName']) ?>">Edit</button>
                        <button class="btn-delete" onclick="deleteStage(<?= $index ?>)" aria-label="Delete stage <?= htmlspecialchars($stage['stageName']) ?>">Delete</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <h3>Add New Stage</h3>
    <form id="addStageForm" onsubmit="event.preventDefault(); addStage();" aria-label="Add new stage form">
        <input type="text" id="newStageName" placeholder="Stage Name" required aria-required="true" />
        <input type="number" id="newStageOrder" placeholder="Stage Order" required aria-required="true" />
        <input type="number" step="0.01" id="newDistanceFromStart" placeholder="Distance From Start" required aria-required="true" />
        <input type="number" step="0.000001" id="newLatitude" placeholder="Latitude" required aria-required="true" />
        <input type="number" step="0.000001" id="newLongitude" placeholder="Longitude" required aria-required="true" />
        <button type="submit">Add Stage</button>
    </form>

    <button type="button" class="back-btn" onclick="window.location.href='ViewRoutes.php'">Back</button>
</div>

<script>
let stages = <?php echo json_encode($stages); ?>;
const routeId = '<?= $route['id'] ?>';

function saveRoute() {
    const form = document.getElementById('routeForm');
    const formData = new FormData(form);

    const data = {
        routeCode: formData.get('code').trim(),
        startPoint: formData.get('from').trim(),
        endPoint: formData.get('to').trim(),
        busStages: stages.map(stage => ({
            stageName: stage.stageName,
            stageOrder: parseInt(stage.stageOrder),
            distanceFromStart: parseFloat(stage.distanceFromStart),
            latitude: parseFloat(stage.latitude),
            longitude: parseFloat(stage.longitude)
        }))
    };

    fetch(`SaveRoute.php?id=${encodeURIComponent(routeId)}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(res => {
        alert(res.message || 'Route saved successfully');
        if(res.success) {
            window.location.href = 'ViewRoutes.php';
        }
    })
    .catch(err => alert('Error saving route: ' + err));
}

function editStage(button) {
    const tr = button.closest('tr');
    const index = tr.getAttribute('data-index');

    if (button.textContent === 'Edit') {
        // Convert cells to input fields
        tr.querySelectorAll('td').forEach((td, idx) => {
            if (idx < 5) {
                const val = td.textContent;
                const inputType = (idx === 1 || idx === 2 || idx === 3 || idx === 4) ? 'number' : 'text';
                const step = (idx === 2) ? '0.01' : ((idx === 3 || idx === 4) ? '0.000001' : '');
                td.innerHTML = `<input type="${inputType}" step="${step}" value="${val}" aria-label="${td.previousElementSibling ? td.previousElementSibling.textContent : ''} input" />`;
            }
        });
        button.textContent = 'Save';

        // Add cancel button
        const cancelBtn = document.createElement('button');
        cancelBtn.textContent = 'Cancel';
        cancelBtn.className = 'btn-cancel';
        cancelBtn.type = 'button';
        cancelBtn.onclick = () => cancelEditStage(tr, index);
        button.insertAdjacentElement('afterend', cancelBtn);
    } else {
        // Save edited stage
        const inputs = tr.querySelectorAll('input');
        let valid = true;

        const updatedStage = {
            stageName: inputs[0].value.trim(),
            stageOrder: parseInt(inputs[1].value),
            distanceFromStart: parseFloat(inputs[2].value),
            latitude: parseFloat(inputs[3].value),
            longitude: parseFloat(inputs[4].value),
        };

        // Simple validation
        if (!updatedStage.stageName) {
            alert("Stage Name cannot be empty.");
            valid = false;
        }
        if (isNaN(updatedStage.stageOrder) || updatedStage.stageOrder < 0) {
            alert("Invalid Stage Order.");
            valid = false;
        }
        if (isNaN(updatedStage.distanceFromStart) || updatedStage.distanceFromStart < 0) {
            alert("Invalid Distance From Start.");
            valid = false;
        }
        if (isNaN(updatedStage.latitude)) {
            alert("Invalid Latitude.");
            valid = false;
        }
        if (isNaN(updatedStage.longitude)) {
            alert("Invalid Longitude.");
            valid = false;
        }
        if (!valid) return;

        stages[index] = updatedStage;
        updateStagesTable();
    }
}

function cancelEditStage(tr, index) {
    // Restore row data from stages array
    const stage = stages[index];
    tr.innerHTML = `
        <td class="stageName">${stage.stageName}</td>
        <td class="stageOrder">${stage.stageOrder}</td>
        <td class="distanceFromStart">${stage.distanceFromStart}</td>
        <td class="latitude">${stage.latitude}</td>
        <td class="longitude">${stage.longitude}</td>
        <td>
            <button class="btn-edit" onclick="editStage(this)">Edit</button>
            <button class="btn-delete" onclick="deleteStage(${index})">Delete</button>
        </td>
    `;
}

function deleteStage(index) {
    if (confirm("Are you sure you want to delete this stage?")) {
        stages.splice(index, 1);
        updateStagesTable();
    }
}

function addStage() {
    const name = document.getElementById('newStageName').value.trim();
    const order = parseInt(document.getElementById('newStageOrder').value);
    const distance = parseFloat(document.getElementById('newDistanceFromStart').value);
    const lat = parseFloat(document.getElementById('newLatitude').value);
    const lng = parseFloat(document.getElementById('newLongitude').value);

    if (!name) { alert('Stage Name is required.'); return; }
    if (isNaN(order) || order < 0) { alert('Valid Stage Order is required.'); return; }
    if (isNaN(distance) || distance < 0) { alert('Valid Distance From Start is required.'); return; }
    if (isNaN(lat)) { alert('Valid Latitude is required.'); return; }
    if (isNaN(lng)) { alert('Valid Longitude is required.'); return; }

    stages.push({
        stageName: name,
        stageOrder: order,
        distanceFromStart: distance,
        latitude: lat,
        longitude: lng
    });

    // Clear inputs
    document.getElementById('addStageForm').reset();
    updateStagesTable();
}

function updateStagesTable() {
    const tbody = document.querySelector('#stagesTable tbody');
    tbody.innerHTML = '';

    stages.forEach((stage, index) => {
        const tr = document.createElement('tr');
        tr.setAttribute('data-index', index);
        tr.innerHTML = `
            <td class="stageName">${stage.stageName}</td>
            <td class="stageOrder">${stage.stageOrder}</td>
            <td class="distanceFromStart">${stage.distanceFromStart}</td>
            <td class="latitude">${stage.latitude}</td>
            <td class="longitude">${stage.longitude}</td>
            <td>
                <button class="btn-edit" onclick="editStage(this)">Edit</button>
                <button class="btn-delete" onclick="deleteStage(${index})">Delete</button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}
</script>

</body>
</html>
