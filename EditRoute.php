<?php
$routeId = $_GET['id'] ?? null;
if (!$routeId) {
    die("Route ID not specified");
}

$routeApiUrl = "https://busmanagementapi.onrender.com/GetRouteById/" . urlencode($routeId);

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
<html>
<head>
    <title>Edit Route and Stages</title>
    <style>
        table { border-collapse: collapse; width: 90%; margin: 20px auto; }
        th, td { padding: 10px; border: 1px solid #ccc; }
        th { background-color: #f2f2f2; }
        input[type=text], input[type=number] { width: 100%; padding: 5px; }
        button, a.button { padding: 5px 10px; margin: 2px; cursor: pointer; }
        .btn-edit { background-color: #4CAF50; color: white; border: none; }
        .btn-delete { background-color: #f44336; color: white; border: none; }
        .btn-cancel { background-color: #777; color: white; border: none; }
        #addStageForm { margin: 20px auto; width: 90%; }
        #addStageForm input { width: 30%; margin-right: 10px; }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<h2 style="text-align:center;">Edit Route</h2>

<form id="routeForm">
    <input type="hidden" name="id" value="<?= htmlspecialchars($route['id']) ?>">
    <label>Route Code:
        <input type="text" name="code" value="<?= htmlspecialchars($route['code']) ?>" required>
    </label>
    <br>
    <label>From:
        <input type="text" name="from" value="<?= htmlspecialchars($route['from']) ?>" required>
    </label>
    <br>
    <label>To:
        <input type="text" name="to" value="<?= htmlspecialchars($route['to']) ?>" required>
    </label>
    <br>
    <button type="button" onclick="saveRoute()">Save Route</button>
</form>

<h3 style="text-align:center;">Stages for this Route</h3>

<table id="stagesTable">
    <thead>
        <tr>
            <th>Stage Name</th>
            <th>Stage Order</th>
            <th>Distance From Start</th>
            <th>Latitude</th>
            <th>Longitude</th>
            <th>Actions</th>
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
                <button class="btn-edit" onclick="editStage(this)">Edit</button>
                <button class="btn-delete" onclick="deleteStage(<?= $index ?>)">Delete</button>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<h3 style="text-align:center;">Add New Stage</h3>
<form id="addStageForm" onsubmit="event.preventDefault(); addStage();">
    <input type="text" id="newStageName" placeholder="Stage Name" required>
    <input type="number" id="newStageOrder" placeholder="Stage Order" required>
    <input type="number" step="0.01" id="newDistanceFromStart" placeholder="Distance From Start" required>
    <input type="number" step="0.000001" id="newLatitude" placeholder="Latitude" required>
    <input type="number" step="0.000001" id="newLongitude" placeholder="Longitude" required>
    <button type="submit">Add Stage</button>
</form>

    <button type="button" class="back-btn" onclick="window.location.href='ViewRoutes.php'">Back</button>

<script>
// Hold stages data locally for this example (initial load)
let stages = <?php echo json_encode($stages); ?>;
const routeId = '<?= $route['id'] ?>';

// Save route info (call your API)
function saveRoute() {
    const form = document.getElementById('routeForm');
    const formData = new FormData(form);

    // Prepare data as per your API format
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
        method: 'POST', // PHP will call PUT internally
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


// Edit stage inline
function editStage(button) {
    const tr = button.closest('tr');
    const index = tr.getAttribute('data-index');

    // Get current values
    const name = tr.querySelector('.stageName').textContent;
    const order = tr.querySelector('.stageOrder').textContent;
    const distance = tr.querySelector('.distanceFromStart').textContent;
    const lat = tr.querySelector('.latitude')?.textContent || '';
    const lng = tr.querySelector('.longitude')?.textContent || '';

    // Replace cells with inputs
    tr.innerHTML = `
        <td><input type="text" value="${name}" id="editName_${index}"></td>
        <td><input type="number" value="${order}" id="editOrder_${index}"></td>
        <td><input type="number" step="0.01" value="${distance}" id="editDistance_${index}"></td>
        <td><input type="number" step="0.000001" value="${lat}" id="editLat_${index}"></td>
        <td><input type="number" step="0.000001" value="${lng}" id="editLng_${index}"></td>
        <td>
            <button onclick="saveStage(${index})">Save</button>
            <button onclick="cancelEdit(${index})">Cancel</button>
        </td>
    `;
}

// Cancel editing: restore original row
function cancelEdit(index) {
    const tr = document.querySelector(`tr[data-index='${index}']`);
    const stage = stages[index];
    tr.innerHTML = `
        <td class="stageName">${stage.stageName}</td>
        <td class="stageOrder">${stage.stageOrder}</td>
        <td class="distanceFromStart">${stage.distanceFromStart}</td>
        <td class="Latitude">${stage.latitude}</td>
        <td class="Longitude">${stage.longitude}</td>
        <td>
            <button class="btn-edit" onclick="editStage(this)">Edit</button>
            <button class="btn-delete" onclick="deleteStage(${index})">Delete</button>
        </td>
    `;
}

// Save edited stage (call your API)
function saveStage(index) {
    const name = document.getElementById(`editName_${index}`).value.trim();
    const order = document.getElementById(`editOrder_${index}`).value.trim();
    const distance = document.getElementById(`editDistance_${index}`).value.trim();
    const lat = document.getElementById(`editLat_${index}`).value.trim();
    const lng = document.getElementById(`editLng_${index}`).value.trim();

    if (!name || !order || !distance || !lat || !lng) {
        alert('Please fill all fields.');
        return;
    }

    // Prepare stage data
    const stageData = {
        routeId: routeId,
        stageIndex: index,
        stageName: name,
        stageOrder: parseInt(order),
        distanceFromStart: parseFloat(distance),
        latitude: parseFloat(lat),
        longitude: parseFloat(lng)
    };
	        stages[index] = stageData;
            cancelEdit(index); // refresh row
    
}

// Delete stage (call your API)
function deleteStage(index) {
    if (!confirm('Are you sure you want to delete this stage?')) return;

    const stageData = {routeId: routeId, stageIndex: index};

            stages.splice(index, 1);
            renderStages();
}

// Add new stage
function addStage() {
    const name = document.getElementById('newStageName').value.trim();
    const order = document.getElementById('newStageOrder').value.trim();
    const distance = document.getElementById('newDistanceFromStart').value.trim();
    const latitude = document.getElementById('newLatitude').value.trim();
    const longitude = document.getElementById('newLongitude').value.trim();

    if (!name || !order || !distance || !latitude || !longitude) {
        alert('Please fill all fields.');
        return;
    }

    const newStage = {
        routeId: routeId,
        stageName: name,
        stageOrder: parseInt(order),
        distanceFromStart: parseFloat(distance),
        latitude: parseFloat(latitude),
        longitude: parseFloat(longitude)
    };

            stages.push(newStage);
            renderStages();
            document.getElementById('addStageForm').reset();
}

// Re-render stages table after add/delete
function renderStages() {
    const tbody = document.querySelector('#stagesTable tbody');
    tbody.innerHTML = '';
    stages.forEach((stage, i) => {
        tbody.innerHTML += `
        <tr data-index="${i}">
            <td class="stageName">${stage.stageName}</td>
            <td class="stageOrder">${stage.stageOrder}</td>
            <td class="distanceFromStart">${stage.distanceFromStart}</td>
            <td class="latitude">${stage.latitude ?? ''}</td>
            <td class="longitude">${stage.longitude ?? ''}</td>
            <td>
                <button class="btn-edit" onclick="editStage(this)">Edit</button>
                <button class="btn-delete" onclick="deleteStage(${i})">Delete</button>
            </td>
        </tr>`;
    });
}
</script>

</body>
</html>
