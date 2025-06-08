<?php
require_once 'AdminAuth.php';

checkAuth();
$config = include('config.php');
$apiBaseUrl = $config['api_base_url'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin - Add Route</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        :root {
            --primary: #6366f1;
            --primary-light: #a5b4fc;
            --primary-dark: #4f46e5;
            --secondary: #8b5cf6;
            --success: #10b981;
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
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-md: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            --transition: all 0.2s ease-in-out;
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
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .page-header h2 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.75rem;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: inline-block;
        }

        .page-header p {
            color: var(--gray-500);
            font-size: 1rem;
            max-width: 700px;
            margin: 0 auto;
        }

        .card {
            background: white;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow);
            padding: 2rem;
            margin-bottom: 2rem;
            transition: var(--transition);
            border: 1px solid var(--gray-200);
        }

        .card:hover {
            box-shadow: var(--shadow-md);
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .card-title i {
            color: var(--primary);
        }

        .form-group {
            margin-bottom: 1.25rem;
            position: relative;
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
            padding: 0.75rem 1rem;
            font-size: 0.9375rem;
            border: 1px solid var(--gray-300);
            border-radius: var(--border-radius);
            transition: var(--transition);
            background-color: white;
            color: var(--dark);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
            background-color: white;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 1.5rem;
            font-size: 0.9375rem;
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
            transform: translateY(-1px);
            box-shadow: var(--shadow);
        }

        .btn-success {
            background-color: var(--success);
            color: white;
        }

        .btn-success:hover {
            background-color: #0d9f6e;
            transform: translateY(-1px);
            box-shadow: var(--shadow);
        }

        .btn-danger {
            background-color: var(--danger);
            color: white;
        }

        .btn-danger:hover {
            background-color: #dc2626;
            transform: translateY(-1px);
            box-shadow: var(--shadow);
        }

        .btn-secondary {
            background-color: var(--gray-500);
            color: white;
        }

        .btn-secondary:hover {
            background-color: var(--gray-700);
            transform: translateY(-1px);
            box-shadow: var(--shadow);
        }

        .btn-outline {
            background-color: transparent;
            border: 1px solid var(--gray-300);
            color: var(--gray-700);
        }

        .btn-outline:hover {
            background-color: var(--gray-100);
            border-color: var(--gray-400);
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.8125rem;
        }

        .table-responsive {
            overflow-x: auto;
            margin: 1.5rem 0;
            border-radius: var(--border-radius);
            position: relative;
        }

        .table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            min-width: 600px;
        }

        .table th {
            background-color: var(--primary);
            color: white;
            font-weight: 500;
            padding: 0.75rem 1rem;
            text-align: left;
            position: sticky;
            top: 0;
        }

        .table th:first-child {
            border-top-left-radius: var(--border-radius);
        }

        .table th:last-child {
            border-top-right-radius: var(--border-radius);
        }

        .table td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--gray-200);
            vertical-align: middle;
            position: relative;
            background-color: white;
        }

        .table tr:first-child td {
            border-top: 1px solid var(--gray-200);
        }

        .table tr:hover td {
            background-color: var(--gray-100);
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
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.15);
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .action-buttons .btn {
            flex: 1;
        }

        .message {
            padding: 1rem;
            border-radius: var(--border-radius);
            margin: 1.5rem 0;
            text-align: center;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
        }

        .message.error {
            background-color: #fee2e2;
            color: var(--danger);
            border: 1px solid #fecaca;
        }

        .message.success {
            background-color: #d1fae5;
            color: var(--success);
            border: 1px solid #a7f3d0;
        }

        .message.info {
            background-color: #dbeafe;
            color: var(--info);
            border: 1px solid #bfdbfe;
        }

        .empty-state {
            text-align: center;
            padding: 2.5rem 1.5rem;
            color: var(--gray-500);
            border: 1px dashed var(--gray-300);
            border-radius: var(--border-radius);
            margin: 1.5rem 0;
            background-color: var(--gray-100);
        }

        .empty-state i {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: var(--gray-300);
        }

        .empty-state p {
            margin-bottom: 1.5rem;
            color: var(--gray-500);
        }

        /* Autocomplete styles */
        .autocomplete-dropdown {
            position: absolute;
            background: white;
            width: calc(100% - 2px);
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid var(--gray-300);
            border-top: none;
            border-radius: 0 0 var(--border-radius) var(--border-radius);
            box-shadow: var(--shadow-sm);
            z-index: 1000;
            display: none;
            left: 0;
            top: 100%;
        }

        .autocomplete-item {
            padding: 0.75rem 1rem;
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.875rem;
        }

        .autocomplete-item:hover {
            background-color: var(--gray-100);
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeInScale {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        .animate-in {
            animation: fadeIn 0.3s ease-out forwards;
        }

        .animate-pop {
            animation: fadeInScale 0.2s ease-out forwards;
        }

        /* Badge */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 500;
            border-radius: 9999px;
            background-color: var(--primary-light);
            color: var(--primary-dark);
        }

        /* Responsive adjustments */
        @media (max-width: 992px) {
            .container {
                padding: 1.25rem;
            }
            
            .page-header h2 {
                font-size: 1.75rem;
            }
            
            .card {
                padding: 1.5rem;
            }
        }

        @media (max-width: 768px) {
            .page-header h2 {
                font-size: 1.5rem;
            }
            
            .card {
                padding: 1.25rem;
            }
            
            .action-buttons {
                flex-direction: column;
            }
        }

        @media (max-width: 576px) {
            .page-header h2 {
                font-size: 1.375rem;
            }
            
            .page-header p {
                font-size: 0.9375rem;
            }
            
            .card-title {
                font-size: 1.125rem;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<?php include 'AdminNavbar.php'; ?>

<div class="container">
    <div class="page-header animate-pop">
        <h2>Create New Route</h2>
        <p>Define a new bus route with all its intermediate stops and distances</p>
    </div>

    <div class="card animate-pop" style="animation-delay: 0.1s;">
        <h3 class="card-title">
            <i class="fas fa-route"></i>
            Route Information
        </h3>
        
        <form id="addRouteForm" onsubmit="event.preventDefault(); saveRoute();">
            <div class="grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
                <div class="form-group">
                    <label for="code" class="form-label">Route Code <span class="badge">Required</span></label>
                    <input type="text" id="code" autocomplete="off" name="code" class="form-control" required placeholder="E.g., RT-101" />
                </div>
                
                <div class="form-group">
                    <label for="from" class="form-label">Starting Point <span class="badge">Required</span></label>
                    <input type="text" id="from" name="from" class="form-control" required placeholder="Where the route begins" autocomplete="off" />
                </div>
                
                <div class="form-group">
                    <label for="to" class="form-label">Ending Point <span class="badge">Required</span></label>
                    <input type="text" id="to" name="to" class="form-control" required placeholder="Where the route ends" autocomplete="off" />
                </div>
            </div>
            
            <h3 class="card-title" style="margin-top: 2rem;">
                <i class="fas fa-map-marker-alt"></i>
                Route Stages
            </h3>
            
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Stage Name</th>
                            <th style="width: 120px;">Order</th>
                            <th style="width: 180px;">Distance (km)</th>
                            <th style="width: 100px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="stagesTableBody">
                        <!-- Stage rows will be added here -->
                    </tbody>
                </table>
            </div>
            
            <div id="emptyState" class="empty-state" style="display: none;">
                <i class="fas fa-map-marked-alt"></i>
                <p>No stages added yet</p>
                <button type="button" class="btn btn-primary" onclick="addStageRow()">
                    <i class="fas fa-plus"></i> Add First Stage
                </button>
            </div>
            
            <button type="button" class="btn btn-outline" onclick="addStageRow()" style="margin-top: 1rem;">
                <i class="fas fa-plus"></i> Add Another Stage
            </button>
            
            <div class="action-buttons">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Save Route
                </button>
                <button type="button" class="btn btn-secondary" onclick="window.location.href='AdminViewRoutes.php'">
                    <i class="fas fa-arrow-left"></i> Back to Routes
                </button>
            </div>
        </form>
    </div>
    
    <div id="message" class="message" style="display: none;"></div>
</div>

<script>
    const API_BASE_URL = "<?php echo $apiBaseUrl; ?>";
    let stageCounter = 0;

    // Initialize with empty state
    document.addEventListener('DOMContentLoaded', function() {
        toggleEmptyState();
        setupAutocomplete();
        // Add one empty stage row by default
        setTimeout(() => {
            addStageRow();
        }, 300);
    });

    function toggleEmptyState() {
        const tbody = document.getElementById('stagesTableBody');
        const emptyState = document.getElementById('emptyState');
        
        if (tbody.children.length === 0) {
            emptyState.style.display = 'block';
        } else {
            emptyState.style.display = 'none';
        }
    }

    function addStageRow(stage = {}) {
        const tbody = document.getElementById('stagesTableBody');
        const stageName = stage.stageName || '';
        const stageOrder = stage.stageOrder !== undefined ? stage.stageOrder : (tbody.children.length + 1);
        const distance = stage.distanceFromStart !== undefined ? stage.distanceFromStart : '';

        const row = document.createElement('tr');
        row.className = 'animate-in';
        row.style.animationDelay = `${stageCounter * 0.05}s`;
        row.innerHTML = `
            <td><input type="text" class="form-control stageName" placeholder="Enter stage name" value="${stageName}" required autocomplete="off"></td>
            <td><input type="number" class="form-control stageOrder" placeholder="Order" value="${stageOrder}" min="1" required></td>
            <td><input type="number" class="form-control distanceFromStart" placeholder="0" step="0.1" value="${distance}" min="0" required></td>
            <td><button type="button" class="btn btn-danger btn-sm" onclick="removeStageRow(this)"><i class="fas fa-trash"></i> Remove</button></td>
        `;
        tbody.appendChild(row);
        stageCounter++;
        toggleEmptyState();
        
        // Setup autocomplete for the new stage input
        setupTypeahead(row.querySelector('.stageName'));
    }

    function removeStageRow(button) {
        const row = button.closest('tr');
        row.classList.add('animate-in');
        row.style.animation = 'fadeIn 0.3s reverse forwards';
        
        setTimeout(() => {
            row.remove();
            toggleEmptyState();
            renumberStages();
        }, 300);
    }

    function renumberStages() {
        const rows = document.querySelectorAll('#stagesTableBody tr');
        rows.forEach((row, index) => {
            row.querySelector('.stageOrder').value = index + 1;
        });
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

    function setupAutocomplete() {
        // Setup autocomplete for starting point
        const fromInput = document.getElementById('from');
        setupTypeahead(fromInput);
        
        // Setup autocomplete for destination
        const toInput = document.getElementById('to');
        setupTypeahead(toInput);
        
        // Setup autocomplete for existing stage name inputs
        document.querySelectorAll('.stageName').forEach(input => {
            setupTypeahead(input);
        });
    }

    function setupTypeahead(inputElement) {
        let timeout;
        let dropdown;
        
        // Create dropdown element if it doesn't exist
        if (!inputElement.nextElementSibling || !inputElement.nextElementSibling.classList.contains('autocomplete-dropdown')) {
            dropdown = document.createElement('div');
            dropdown.className = 'autocomplete-dropdown';
            
            // For table cells, append to the cell instead of parent
            if (inputElement.closest('td')) {
                inputElement.closest('td').appendChild(dropdown);
            } else {
                inputElement.parentNode.appendChild(dropdown);
            }
        } else {
            dropdown = inputElement.nextElementSibling;
        }
        
        inputElement.addEventListener('input', async () => {
            clearTimeout(timeout);
            dropdown.innerHTML = '';
            
            const query = inputElement.value.trim();
            if (query.length < 2) {
                dropdown.style.display = 'none';
                return;
            }
            
            timeout = setTimeout(async () => {
                const stages = await fetchStages(query);
                if (stages.length === 0) {
                    dropdown.style.display = 'none';
                    return;
                }
                
                dropdown.innerHTML = '';
                stages.forEach(stage => {
                    const item = document.createElement('div');
                    item.className = 'autocomplete-item';
                    item.textContent = stage;
                    item.addEventListener('click', () => {
                        inputElement.value = stage;
                        dropdown.style.display = 'none';
                    });
                    dropdown.appendChild(item);
                });
                
                dropdown.style.display = 'block';
            }, 300);
        });
        
        // Hide dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (e.target !== inputElement && !dropdown.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });
        
        inputElement.addEventListener('focus', () => {
            if (dropdown.children.length > 0) {
                dropdown.style.display = 'block';
            }
        });
    }

    function saveRoute() {
        const code = document.getElementById('code').value.trim();
        const from = document.getElementById('from').value.trim();
        const to = document.getElementById('to').value.trim();

        if (!code || !from || !to) {
            showMessage('Please fill all required route fields.', 'error');
            return;
        }

        const stagesRows = document.querySelectorAll('#stagesTableBody tr');
        if (stagesRows.length === 0) {
            if (!confirm('No stages added. Are you sure you want to create a route without stages?')) {
                return;
            }
        }

        const busStages = [];
        for (const row of stagesRows) {
            const stageName = row.querySelector('.stageName').value.trim();
            const stageOrder = row.querySelector('.stageOrder').value.trim();
            const distanceFromStart = row.querySelector('.distanceFromStart').value.trim();

            if (!stageName || !stageOrder || !distanceFromStart) {
                showMessage('Please fill all fields for each stage.', 'error');
                return;
            }

            busStages.push({
                stageName: stageName,
                stageOrder: parseInt(stageOrder),
                distanceFromStart: parseFloat(distanceFromStart)
            });
        }

        const data = {
            routeCode: code,
            startPoint: from,
            endPoint: to,
            busStages: busStages
        };

        showMessage('Saving route...', 'info');

        fetch(`${API_BASE_URL}CreateRoute`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(response.text());
            }
            return response.text();
        })
        .then(text => {
            if (text.toLowerCase().includes('success')) {
                showMessage(text, 'success');
                setTimeout(() => {
                    window.location.href = 'AdminViewRoutes.php';
                }, 1500);
            } else {
                showMessage(text || 'Failed to add route.', 'error');
            }
        })
        .catch(err => {
            showMessage('Error: ' + err.message, 'error');
            console.error('Error:', err);
        });
    }

    function showMessage(text, type) {
        const messageEl = document.getElementById('message');
        messageEl.textContent = text;
        messageEl.className = `message ${type}`;
        messageEl.style.display = 'flex';
        
        // Auto-hide success messages after 3 seconds
        if (type === 'success') {
            setTimeout(() => {
                messageEl.style.display = 'none';
            }, 3000);
        }
    }
</script>

</body>
</html>