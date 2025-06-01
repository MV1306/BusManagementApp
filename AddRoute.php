<?php
$config = include('config.php');
$apiBaseUrl = $config['api_base_url'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Add New Route with Stages</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            position: relative;
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
            background-color: #4cc9f0;
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

        .table-responsive {
            overflow-x: auto;
            margin: 25px 0;
            border-radius: var(--border-radius);
            box-shadow: 0 0 0 1px var(--light-gray);
            position: relative;
            overflow: visible;
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
        }

        .table td {
            padding: 12px 15px;
            border-bottom: 1px solid var(--light-gray);
            vertical-align: middle;
            position: relative;
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

        /* Autocomplete styles */
        .autocomplete-dropdown {
            position: absolute;
            background: white;
            width: calc(100% - 2px);
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid var(--light-gray);
            border-top: none;
            border-radius: 0 0 var(--border-radius) var(--border-radius);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            z-index: 1000;
            display: none;
            left: 0;
            top: 100%;
        }

        .autocomplete-item {
            padding: 10px 15px;
            cursor: pointer;
            transition: var(--transition);
        }

        .autocomplete-item:hover {
            background-color: var(--light);
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<?php include 'AdminNavbar.php'; ?>

<div class="container">
    <div class="page-header animate-in">
        <h2>Create New Route</h2>
        <p>Add a new bus route with all its intermediate stages and distances</p>
    </div>

    <div class="card animate-in" style="animation-delay: 0.1s;">
        <h3 class="card-title">Route Information</h3>
        
        <form id="addRouteForm" onsubmit="event.preventDefault(); saveRoute();">
            <div class="form-group">
                <label for="code" class="form-label">Route Code</label>
                <input type="text" id="code" name="code" class="form-control" required placeholder="E.g., RT-101" />
            </div>
            
            <div class="form-group">
                <label for="from" class="form-label">Starting Point</label>
                <input type="text" id="from" name="from" class="form-control" required placeholder="Where the route begins" autocomplete="off" />
            </div>
            
            <div class="form-group">
                <label for="to" class="form-label">Destination</label>
                <input type="text" id="to" name="to" class="form-control" required placeholder="Where the route ends" autocomplete="off" />
            </div>
            
            <h3 class="card-title" style="margin-top: 30px;">Route Stages</h3>
            
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
            
            <button type="button" class="btn btn-primary" onclick="addStageRow()" style="margin-top: 15px;">
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
            <td><input type="number" class="form-control distanceFromStart" placeholder="0" step="1" value="${distance}" min="0" required></td>
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
            showMessage('Please fill all route fields.', 'error');
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
        messageEl.style.display = 'block';
        
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