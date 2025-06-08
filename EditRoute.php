<?php
require_once 'AdminAuth.php';

checkAuth();
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
    <title>Admin - Edit Route</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-light: #6366f1;
            --primary-dark: #4338ca;
            --secondary: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
            --light: #f9fafb;
            --dark: #111827;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-500: #6b7280;
            --gray-700: #374151;
            --border-radius: 0.5rem;
            --border-radius-lg: 0.75rem;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--gray-100);
            color: var(--dark);
            line-height: 1.5;
            min-height: 100vh;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 1.5rem;
        }

        .page-header {
            margin-bottom: 2.5rem;
        }

        .page-header h2 {
            font-size: 1.875rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.5rem;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            display: inline-block;
        }

        .page-header p {
            color: var(--gray-500);
            font-size: 1rem;
        }

        .card {
            background: white;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-sm);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: var(--transition);
            border: 1px solid var(--gray-200);
        }

        .card:hover {
            box-shadow: var(--shadow-md);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid var(--gray-200);
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--dark);
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: var(--gray-700);
            font-size: 0.875rem;
        }

        .form-control {
            width: 100%;
            padding: 0.625rem 0.75rem;
            font-size: 0.875rem;
            border: 1px solid var(--gray-300);
            border-radius: var(--border-radius);
            transition: var(--transition);
            background-color: white;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.625rem 1.25rem;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: var(--transition);
            border: none;
            text-decoration: none;
            gap: 0.5rem;
        }

        .btn i {
            font-size: 1rem;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
        }

        .btn-success {
            background-color: var(--secondary);
            color: white;
        }

        .btn-success:hover {
            background-color: #0d9f6e;
        }

        .btn-danger {
            background-color: var(--danger);
            color: white;
        }

        .btn-danger:hover {
            background-color: #dc2626;
        }

        .btn-warning {
            background-color: var(--warning);
            color: white;
        }

        .btn-warning:hover {
            background-color: #d97706;
        }

        .btn-secondary {
            background-color: var(--gray-500);
            color: white;
        }

        .btn-secondary:hover {
            background-color: var(--gray-700);
        }

        .btn-outline {
            background-color: transparent;
            border: 1px solid var(--gray-300);
            color: var(--gray-700);
        }

        .btn-outline:hover {
            background-color: var(--gray-100);
        }

        .btn-sm {
            padding: 0.5rem 0.75rem;
            font-size: 0.75rem;
        }

        .btn-xs {
            padding: 0.375rem 0.625rem;
            font-size: 0.75rem;
        }

        .grid {
            display: grid;
            gap: 1rem;
        }

        .grid-cols-3 {
            grid-template-columns: repeat(3, 1fr);
        }

        @media (max-width: 768px) {
            .grid-cols-3 {
                grid-template-columns: 1fr;
            }
        }

        .table-container {
            overflow-x: auto;
            margin: 1.5rem 0;
            border-radius: var(--border-radius);
            border: 1px solid var(--gray-200);
            background-color: white;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
        }

        .table th {
            background-color: var(--gray-100);
            color: var(--gray-700);
            font-weight: 500;
            padding: 0.75rem 1rem;
            text-align: left;
            position: sticky;
            top: 0;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .table td {
            padding: 1rem;
            border-bottom: 1px solid var(--gray-200);
            vertical-align: middle;
            font-size: 0.875rem;
        }

        .table tr:last-child td {
            border-bottom: none;
        }

        .table tr:hover {
            background-color: var(--gray-50);
        }

        .table input {
            width: 100%;
            padding: 0.5rem 0.75rem;
            border: 1px solid var(--gray-300);
            border-radius: var(--border-radius);
            transition: var(--transition);
            font-size: 0.875rem;
        }

        .table input:focus {
            outline: none;
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .empty-state {
            text-align: center;
            padding: 2rem;
            color: var(--gray-500);
        }

        .empty-state i {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: var(--gray-300);
        }

        .empty-state p {
            margin-bottom: 1rem;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .badge-primary {
            background-color: var(--primary-light);
            color: white;
        }

        .badge-secondary {
            background-color: var(--gray-200);
            color: var(--gray-700);
        }

        .toast {
            position: fixed;
            top: 1.5rem;
            right: 1.5rem;
            padding: 1rem 1.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-lg);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            z-index: 50;
            transform: translateY(-20px);
            opacity: 0;
            transition: var(--transition);
            pointer-events: none;
        }

        .toast.show {
            transform: translateY(0);
            opacity: 1;
            pointer-events: auto;
        }

        .toast-success {
            background-color: white;
            border-left: 4px solid var(--secondary);
            color: var(--dark);
        }

        .toast-error {
            background-color: white;
            border-left: 4px solid var(--danger);
            color: var(--dark);
        }

        .toast-info {
            background-color: white;
            border-left: 4px solid var(--info);
            color: var(--dark);
        }

        .toast i {
            font-size: 1.25rem;
        }

        .toast-success i {
            color: var(--secondary);
        }

        .toast-error i {
            color: var(--danger);
        }

        .toast-info i {
            color: var(--info);
        }

        .animate-in {
            animation: fadeIn 0.3s ease-out forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Responsive adjustments */
        @media (max-width: 1024px) {
            .container {
                padding: 1.25rem;
            }
        }

        @media (max-width: 768px) {
            .page-header h2 {
                font-size: 1.5rem;
            }
            
            .card {
                padding: 1.25rem;
            }
        }

        @media (max-width: 640px) {
            .container {
                padding: 1rem;
            }
            
            .page-header h2 {
                font-size: 1.25rem;
            }
            
            .card-title {
                font-size: 1.125rem;
            }
            
            .action-buttons {
                flex-direction: column;
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
        <div class="card-header">
            <h3 class="card-title">Route Information</h3>
            <span class="badge badge-primary">
                <i class="fas fa-route mr-1"></i>&nbsp; <p>Route: <?= htmlspecialchars($route['code']) ?></p>
            </span>
        </div>
        
        <form id="routeForm">
            <input type="hidden" name="id" value="<?= htmlspecialchars($route['id']) ?>">
            
            <div class="grid grid-cols-3 gap-4">
                <div class="form-group">
                    <label for="code" class="form-label">Route Code</label>
                    <input type="text" id="code" name="code" class="form-control" 
                           value="<?= htmlspecialchars($route['code']) ?>" required 
                           placeholder="E.g., RT-101">
                </div>
                
                <div class="form-group">
                    <label for="from" class="form-label">Starting Point</label>
                    <input type="text" id="from" name="from" class="form-control" 
                           value="<?= htmlspecialchars($route['from']) ?>" required 
                           placeholder="Where the route begins">
                </div>
                
                <div class="form-group">
                    <label for="to" class="form-label">Destination</label>
                    <input type="text" id="to" name="to" class="form-control" 
                           value="<?= htmlspecialchars($route['to']) ?>" required 
                           placeholder="Where the route ends">
                </div>
            </div>
            
            <div class="flex justify-end mt-4">
                <button type="button" class="btn btn-success" onclick="saveRoute()">
                    <i class="fas fa-save"></i> Save Route
                </button>
            </div>
        </form>
    </div>

    <div class="card animate-in" style="animation-delay: 0.2s;">
        <div class="card-header">
            <h3 class="card-title">Route Stages</h3>
            <span class="badge badge-secondary">
                <i class="fas fa-layer-group mr-1"></i> <?= count($stages) ?> stages
            </span>
        </div>
        
        <div class="table-container">
            <table class="table" id="stagesTable">
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
                            <button class="btn btn-outline btn-xs" onclick="editStage(this)">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-outline btn-xs" onclick="deleteStage(<?= $index ?>)">
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
            <button class="btn btn-primary btn-sm" onclick="document.getElementById('newStageName').focus()">
                <i class="fas fa-plus"></i> Add First Stage
            </button>
        </div>
        <?php endif; ?>
    </div>

    <div class="card animate-in" style="animation-delay: 0.3s;">
        <div class="card-header">
            <h3 class="card-title">Add New Stage</h3>
        </div>
        
        <form id="addStageForm" onsubmit="event.preventDefault(); addStage();">
            <div class="grid grid-cols-3 gap-4">
                <div class="form-group">
                    <label for="newStageName" class="form-label">Stage Name</label>
                    <input type="text" id="newStageName" class="form-control" 
                           placeholder="Enter stage name" required>
                </div>
                
                <div class="form-group">
                    <label for="newStageOrder" class="form-label">Stage Order</label>
                    <input type="number" id="newStageOrder" class="form-control" 
                           placeholder="Enter order number" required>
                </div>
                
                <div class="form-group">
                    <label for="newDistanceFromStart" class="form-label">Distance From Start (km)</label>
                    <input type="number" step="0.01" id="newDistanceFromStart" class="form-control" 
                           placeholder="0" required>
                </div>
            </div>
            
            <div class="flex justify-end mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Stage
                </button>
            </div>
        </form>
    </div>
    
    <div class="action-buttons">
        <button type="button" class="btn btn-secondary" onclick="window.location.href='AdminViewRoutes.php'">
            <i class="fas fa-arrow-left"></i> Back to Routes
        </button>
    </div>
</div>

<div id="toast" class="toast">
    <i class="fas fa-info-circle"></i>
    <span id="toast-message"></span>
</div>

<script>
let stages = <?php echo json_encode($stages); ?>;
const routeId = '<?= $route['id'] ?>';

function showToast(message, type) {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toast-message');
    
    toast.className = `toast toast-${type}`;
    toastMessage.textContent = message;
    
    toast.classList.add('show');
    
    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);
}

function saveRoute() {
    const code = document.getElementById('code').value.trim();
    const from = document.getElementById('from').value.trim();
    const to = document.getElementById('to').value.trim();

    if (!code || !from || !to) {
        showToast('Please fill all route fields.', 'error');
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

    showToast('Saving route...', 'info');

    fetch(`SaveRoute.php?id=${encodeURIComponent(routeId)}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showToast('Route saved successfully!', 'success');
            setTimeout(() => {
                window.location.href = 'AdminViewRoutes.php';
            }, 1500);
        } else {
            showToast(data.message || 'Failed to save route.', 'error');
        }
    })
    .catch(error => {
        showToast('Error saving route: ' + error.message, 'error');
        console.error('Error:', error);
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
                td.innerHTML = `<input type="${inputType}" class="form-control" step="${step}" value="${val}" />`;
            }
        });
        button.innerHTML = '<i class="fas fa-save"></i> Save';
        button.className = 'btn btn-success btn-xs';

        // Add cancel button
        const cancelBtn = document.createElement('button');
        cancelBtn.className = 'btn btn-outline btn-xs';
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
            showToast("Stage Name cannot be empty.", 'error');
            valid = false;
        }
        if (isNaN(updatedStage.stageOrder)) {
            showToast("Invalid Stage Order.", 'error');
            valid = false;
        }
        if (isNaN(updatedStage.distanceFromStart)) {
            showToast("Invalid Distance From Start.", 'error');
            valid = false;
        }
        if (!valid) return;

        stages[index] = updatedStage;
        updateStagesTable();
        showToast('Stage updated successfully!', 'success');
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
            <button class="btn btn-outline btn-xs" onclick="editStage(this)">
                <i class="fas fa-edit"></i> Edit
            </button>
            <button class="btn btn-outline btn-xs" onclick="deleteStage(${index})">
                <i class="fas fa-trash"></i> Delete
            </button>
        </td>
    `;
}

function deleteStage(index) {
    if (confirm("Are you sure you want to delete this stage?")) {
        stages.splice(index, 1);
        updateStagesTable();
        showToast('Stage deleted successfully.', 'success');
    }
}

function addStage() {
    const name = document.getElementById('newStageName').value.trim();
    const order = parseInt(document.getElementById('newStageOrder').value);
    const distance = parseFloat(document.getElementById('newDistanceFromStart').value);

    if (!name) { 
        showToast('Stage Name is required.', 'error'); 
        return; 
    }
    if (isNaN(order)) { 
        showToast('Valid Stage Order is required.', 'error'); 
        return; 
    }
    if (isNaN(distance)) { 
        showToast('Valid Distance From Start is required.', 'error'); 
        return; 
    }

    // Check if stage with this order already exists
    const existingStage = stages.find(s => s.stageOrder === order);
    if (existingStage) {
        showToast(`Stage with order ${order} already exists.`, 'error');
        return;
    }

    stages.push({
        stageName: name,
        stageOrder: order,
        distanceFromStart: distance
    });

    // Sort stages by order
    stages.sort((a, b) => a.stageOrder - b.stageOrder);

    // Clear inputs and set next order number
    document.getElementById('newStageName').value = '';
    document.getElementById('newDistanceFromStart').value = '';
    document.getElementById('newStageOrder').value = stages.length > 0 
        ? Math.max(...stages.map(s => s.stageOrder)) + 1 
        : 1;
    
    updateStagesTable();
    showToast('Stage added successfully!', 'success');
}

function updateStagesTable() {
    const tbody = document.getElementById('stagesTableBody');
    tbody.innerHTML = '';

    if (stages.length === 0) {
        tbody.insertAdjacentHTML('afterend', `
            <div class="empty-state">
                <i class="fas fa-map-marked-alt"></i>
                <p>No stages added to this route yet</p>
                <button class="btn btn-primary btn-sm" onclick="document.getElementById('newStageName').focus()">
                    <i class="fas fa-plus"></i> Add First Stage
                </button>
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
                <button class="btn btn-outline btn-xs" onclick="editStage(this)">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-outline btn-xs" onclick="deleteStage(${index})">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

// Set default order for new stage
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('newStageOrder').value = stages.length > 0 
        ? Math.max(...stages.map(s => s.stageOrder)) + 1 
        : 1;

    document.getElementById('newDistanceFromStart').value = 0;
});
</script>

</body>
</html>