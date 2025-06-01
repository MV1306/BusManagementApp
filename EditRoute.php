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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --primary-dark: #3a56d4;
            --secondary: #3f37c9;
            --success: #4cc9f0;
            --danger: #f72585;
            --warning: #f8961e;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --light-gray: #e9ecef;
            --border-radius: 12px;
            --box-shadow: 0 8px 20px rgba(0,0,0,0.08);
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7ff;
            color: var(--dark);
            line-height: 1.6;
            min-height: 100vh;
            padding: 0;
            margin: 0;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .page-header {
            text-align: center;
            margin: 30px 0 40px;
        }

        .page-header h2 {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 15px;
            position: relative;
            display: inline-block;
        }

        .page-header h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--success));
            border-radius: 2px;
        }

        .page-header p {
            color: var(--gray);
            font-size: 1rem;
            max-width: 700px;
            margin: 0 auto;
        }

        .card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 30px;
            margin-bottom: 30px;
            transition: var(--transition);
        }

        .card:hover {
            box-shadow: 0 10px 25px rgba(0,0,0,0.12);
        }

        .card-title {
            font-size: 1.4rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--light-gray);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-weight: 500;
            margin-bottom: 8px;
            color: var(--dark);
            font-size: 0.95rem;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            font-size: 1rem;
            border: 1px solid var(--light-gray);
            border-radius: var(--border-radius);
            transition: var(--transition);
            background-color: var(--light);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
            background-color: white;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 24px;
            font-size: 1rem;
            font-weight: 500;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: var(--transition);
            border: none;
            text-decoration: none;
        }

        .btn i {
            margin-right: 8px;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
        }

        .btn-success {
            background-color: var(--success);
            color: white;
        }

        .btn-success:hover {
            background-color: #3ab5db;
            transform: translateY(-2px);
        }

        .btn-danger {
            background-color: var(--danger);
            color: white;
        }

        .btn-danger:hover {
            background-color: #e5177a;
            transform: translateY(-2px);
        }

        .btn-warning {
            background-color: var(--warning);
            color: white;
        }

        .btn-warning:hover {
            background-color: #e68a19;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background-color: var(--gray);
            color: white;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
        }

        .btn-sm {
            padding: 8px 16px;
            font-size: 0.85rem;
        }

        .btn-xs {
            padding: 6px 12px;
            font-size: 0.8rem;
        }

        .table-responsive {
            overflow-x: auto;
            margin: 25px 0;
            border-radius: var(--border-radius);
            box-shadow: 0 0 0 1px var(--light-gray);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
        }

        .table th {
            background-color: var(--primary);
            color: white;
            font-weight: 500;
            padding: 15px;
            text-align: left;
            position: sticky;
            top: 0;
        }

        .table td {
            padding: 12px 15px;
            border-bottom: 1px solid var(--light-gray);
            vertical-align: middle;
        }

        .table tr:last-child td {
            border-bottom: none;
        }

        .table tr:nth-child(even) {
            background-color: rgba(248, 249, 250, 0.5);
        }

        .table tr:hover {
            background-color: rgba(67, 97, 238, 0.05);
        }

        .table input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid var(--light-gray);
            border-radius: 6px;
            transition: var(--transition);
        }

        .table input:focus {
            outline: none;
            border-color: var(--primary);
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }

        .action-buttons .btn {
            flex: 1;
        }

        .form-row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-col {
            flex: 1;
            min-width: 250px;
        }

        .message {
            padding: 15px;
            border-radius: var(--border-radius);
            margin: 20px 0;
            text-align: center;
            font-weight: 500;
        }

        .message.error {
            background-color: #ffebee;
            color: var(--danger);
            border: 1px solid #ffcdd2;
        }

        .message.success {
            background-color: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: var(--gray);
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 15px;
            color: var(--light-gray);
        }

        .empty-state p {
            margin-bottom: 20px;
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-in {
            animation: fadeIn 0.4s ease-out forwards;
        }

        /* Responsive adjustments */
        @media (max-width: 992px) {
            .container {
                padding: 15px;
            }
            
            .page-header h2 {
                font-size: 1.8rem;
            }
            
            .card {
                padding: 25px;
            }
        }

        @media (max-width: 768px) {
            .page-header h2 {
                font-size: 1.6rem;
            }
            
            .card {
                padding: 20px;
            }
            
            .form-control {
                padding: 10px 12px;
            }
            
            .btn {
                padding: 10px 18px;
            }
            
            .action-buttons {
                flex-direction: column;
                gap: 10px;
            }
        }

        @media (max-width: 576px) {
            .page-header h2 {
                font-size: 1.4rem;
            }
            
            .page-header p {
                font-size: 0.9rem;
            }
            
            .card-title {
                font-size: 1.2rem;
            }
            
            .form-label {
                font-size: 0.9rem;
            }
            
            .form-control {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>

<?php include 'AdminNavbar.php'; ?>

<div class="container">
    <div class="page-header animate-in">
        <h2>Edit Route</h2>
        <p>Update route details and manage its stages</p>
    </div>

    <div class="card animate-in" style="animation-delay: 0.1s;">
        <h3 class="card-title">Route Information</h3>
        
        <form id="routeForm">
            <input type="hidden" name="id" value="<?= htmlspecialchars($route['id']) ?>">
            
            <div class="form-row">
                <div class="form-col">
                    <div class="form-group">
                        <label for="code" class="form-label">Route Code</label>
                        <input type="text" id="code" name="code" class="form-control" 
                               value="<?= htmlspecialchars($route['code']) ?>" required 
                               placeholder="E.g., RT-101">
                    </div>
                </div>
                
                <div class="form-col">
                    <div class="form-group">
                        <label for="from" class="form-label">Starting Point</label>
                        <input type="text" id="from" name="from" class="form-control" 
                               value="<?= htmlspecialchars($route['from']) ?>" required 
                               placeholder="Where the route begins">
                    </div>
                </div>
                
                <div class="form-col">
                    <div class="form-group">
                        <label for="to" class="form-label">Destination</label>
                        <input type="text" id="to" name="to" class="form-control" 
                               value="<?= htmlspecialchars($route['to']) ?>" required 
                               placeholder="Where the route ends">
                    </div>
                </div>
            </div>
            
            <button type="button" class="btn btn-success" onclick="saveRoute()">
                <i class="fas fa-save"></i> Save Route
            </button>
        </form>
    </div>

    <div class="card animate-in" style="animation-delay: 0.2s;">
        <h3 class="card-title">Route Stages</h3>
        
        <div class="table-responsive">
            <table class="table" id="stagesTable" aria-label="Stages Table">
                <thead>
                    <tr>
                        <th scope="col">Stage Name</th>
                        <th scope="col" style="width: 120px;">Order</th>
                        <th scope="col" style="width: 150px;">Distance (km)</th>
                        <th scope="col" style="width: 180px;">Actions</th>
                    </tr>
                </thead>
                <tbody id="stagesTableBody">
                    <?php foreach ($stages as $index => $stage): ?>
                    <tr data-index="<?= $index ?>" class="animate-in" style="animation-delay: <?= ($index * 0.05) ?>s">
                        <td class="stageName"><?= htmlspecialchars($stage['stageName']) ?></td>
                        <td class="stageOrder"><?= htmlspecialchars($stage['stageOrder']) ?></td>
                        <td class="distanceFromStart"><?= htmlspecialchars($stage['distanceFromStart']) ?></td>
                        <td>
                            <button class="btn btn-warning btn-xs" onclick="editStage(this)" aria-label="Edit stage <?= htmlspecialchars($stage['stageName']) ?>">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-danger btn-xs" onclick="deleteStage(<?= $index ?>)" aria-label="Delete stage <?= htmlspecialchars($stage['stageName']) ?>">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <?php if (empty($stages)): ?>
        <div class="empty-state">
            <i class="fas fa-map-marked-alt"></i>
            <p>No stages added to this route yet</p>
        </div>
        <?php endif; ?>
    </div>

    <div class="card animate-in" style="animation-delay: 0.3s;">
        <h3 class="card-title">Add New Stage</h3>
        
        <form id="addStageForm" onsubmit="event.preventDefault(); addStage();" aria-label="Add new stage form">
            <div class="form-row">
                <div class="form-col">
                    <div class="form-group">
                        <label for="newStageName" class="form-label">Stage Name</label>
                        <input type="text" id="newStageName" class="form-control" 
                               placeholder="Enter stage name" required aria-required="true">
                    </div>
                </div>
                
                <div class="form-col">
                    <div class="form-group">
                        <label for="newStageOrder" class="form-label">Stage Order</label>
                        <input type="number" id="newStageOrder" class="form-control" 
                               placeholder="Enter order number" required aria-required="true">
                    </div>
                </div>
                
                <div class="form-col">
                    <div class="form-group">
                        <label for="newDistanceFromStart" class="form-label">Distance From Start (km)</label>
                        <input type="number" step="1" id="newDistanceFromStart" class="form-control" 
                               placeholder="0" required aria-required="true">
                    </div>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Stage
            </button>
        </form>
    </div>
    
    <div class="action-buttons">
        <button type="button" class="btn btn-secondary" onclick="window.location.href='AdminViewRoutes.php'">
            <i class="fas fa-arrow-left"></i> Back to Routes
        </button>
    </div>
    
    <div id="message" class="message" style="display: none;"></div>
</div>

<script>
let stages = <?php echo json_encode($stages); ?>;
const routeId = '<?= $route['id'] ?>';

function saveRoute() {
    const code = document.getElementById('code').value.trim();
    const from = document.getElementById('from').value.trim();
    const to = document.getElementById('to').value.trim();

    if (!code || !from || !to) {
        showMessage('Please fill all route fields.', 'error');
        return;
    }

    const data = {
        routeCode: code,
        startPoint: from,
        endPoint: to,
        busStages: stages.map(stage => ({
            stageName: stage.stageName,
            stageOrder: parseInt(stage.stageOrder),
            distanceFromStart: parseFloat(stage.distanceFromStart)
        }))
    };

    showMessage('Saving route...', 'info');

    fetch(`SaveRoute.php?id=${encodeURIComponent(routeId)}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(response.text());
        }
        return response.json();
    })
    .then(res => {
        if (res.success) {
            showMessage(res.message || 'Route saved successfully!', 'success');
            setTimeout(() => {
                window.location.href = 'AdminViewRoutes.php';
            }, 1500);
        } else {
            showMessage(res.message || 'Failed to save route.', 'error');
        }
    })
    .catch(err => {
        showMessage('Error saving route: ' + err.message, 'error');
        console.error('Error:', err);
    });
}

function editStage(button) {
    const tr = button.closest('tr');
    const index = tr.getAttribute('data-index');

    if (button.innerHTML.includes('Edit')) {
        // Convert cells to input fields
        tr.querySelectorAll('td').forEach((td, idx) => {
            if (idx < 3) {
                const val = td.textContent;
                const inputType = (idx === 1 || idx === 2) ? 'number' : 'text';
                const step = (idx === 2) ? '0.01' : '';
                td.innerHTML = `<input type="${inputType}" class="form-control" step="${step}" value="${val}" aria-label="${td.previousElementSibling ? td.previousElementSibling.textContent : ''} input" />`;
            }
        });
        button.innerHTML = '<i class="fas fa-save"></i> Save';

        // Add cancel button
        const cancelBtn = document.createElement('button');
        cancelBtn.className = 'btn btn-secondary btn-xs';
        cancelBtn.innerHTML = '<i class="fas fa-times"></i> Cancel';
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
            distanceFromStart: parseFloat(inputs[2].value)
        };

        // Simple validation
        if (!updatedStage.stageName) {
            showMessage("Stage Name cannot be empty.", 'error');
            valid = false;
        }
        if (isNaN(updatedStage.stageOrder) || updatedStage.stageOrder < 0) {
            showMessage("Invalid Stage Order.", 'error');
            valid = false;
        }
        if (isNaN(updatedStage.distanceFromStart) || updatedStage.distanceFromStart < 0) {
            showMessage("Invalid Distance From Start.", 'error');
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
        <td>
            <button class="btn btn-warning btn-xs" onclick="editStage(this)">
                <i class="fas fa-edit"></i> Edit
            </button>
            <button class="btn btn-danger btn-xs" onclick="deleteStage(${index})">
                <i class="fas fa-trash"></i> Delete
            </button>
        </td>
    `;
}

function deleteStage(index) {
    if (confirm("Are you sure you want to delete this stage?")) {
        stages.splice(index, 1);
        updateStagesTable();
        showMessage('Stage deleted successfully.', 'success');
    }
}

function addStage() {
    const name = document.getElementById('newStageName').value.trim();
    const order = parseInt(document.getElementById('newStageOrder').value);
    const distance = parseFloat(document.getElementById('newDistanceFromStart').value);

    if (!name) { 
        showMessage('Stage Name is required.', 'error'); 
        return; 
    }
    if (isNaN(order) || order < 0) { 
        showMessage('Valid Stage Order is required.', 'error'); 
        return; 
    }
    if (isNaN(distance) || distance < 0) { 
        showMessage('Valid Distance From Start is required.', 'error'); 
        return; 
    }

    stages.push({
        stageName: name,
        stageOrder: order,
        distanceFromStart: distance
    });

    // Clear inputs
    document.getElementById('addStageForm').reset();
    updateStagesTable();
    showMessage('Stage added successfully!', 'success');
}

function updateStagesTable() {
    const tbody = document.getElementById('stagesTableBody');
    tbody.innerHTML = '';

    if (stages.length === 0) {
        tbody.insertAdjacentHTML('afterend', `
            <div class="empty-state">
                <i class="fas fa-map-marked-alt"></i>
                <p>No stages added to this route yet</p>
            </div>
        `);
        return;
    }

    stages.forEach((stage, index) => {
        const tr = document.createElement('tr');
        tr.setAttribute('data-index', index);
        tr.className = 'animate-in';
        tr.style.animationDelay = `${index * 0.05}s`;
        tr.innerHTML = `
            <td class="stageName">${stage.stageName}</td>
            <td class="stageOrder">${stage.stageOrder}</td>
            <td class="distanceFromStart">${stage.distanceFromStart}</td>
            <td>
                <button class="btn btn-warning btn-xs" onclick="editStage(this)">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-danger btn-xs" onclick="deleteStage(${index})">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function showMessage(text, type) {
    const messageEl = document.getElementById('message');
    messageEl.textContent = text;
    messageEl.className = `message ${type}`;
    messageEl.style.display = 'block';
    
    // Auto-hide success messages after 3 seconds
    if (type === 'success') {
        setTimeout(() => {
            messageEl.style.display = 'none';
        }, 3000);
    }
}

// Set default order for new stage
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('newStageOrder').value = stages.length + 1;
});
</script>

</body>
</html>