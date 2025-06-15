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
            position: absolute; /* positioned relative to the body */
            background: white;
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid var(--gray-300);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            z-index: 10000;
            display: none;
            /* Added for smooth keyboard navigation */
            scroll-behavior: smooth;
        }

        .autocomplete-item {
            padding: 0.75rem 1rem;
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.875rem;
        }

        .autocomplete-item:hover,
        .autocomplete-item.highlighted { /* Added highlighted class */
            background-color: var(--primary-light);
            color: var(--primary-dark);
        }

        .search-highlight {
            font-weight: 600;
            color: var(--primary);
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

        /* Shortcut help text */
        .shortcut-help {
            font-size: 0.75rem;
            color: var(--gray-500);
            margin-left: 0.5rem;
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
                        </tbody>
                </table>
            </div>
            
            <div id="emptyState" class="empty-state" style="display: none;">
                <i class="fas fa-map-marked-alt"></i>
                <p>No stages added yet</p>
                <button type="button" class="btn btn-primary" onclick="addStageRow()" data-shortcut="F6">
                    <i class="fas fa-plus"></i> Add First Stage <small class="shortcut-help">(F6)</small>
                </button>
            </div>
            
            <button type="button" class="btn btn-outline" id="addAnotherStageBtn" onclick="addStageRow()" style="margin-top: 1rem;" data-shortcut="F6">
                <i class="fas fa-plus"></i> Add Another Stage <small class="shortcut-help">(F6)</small>
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
    // Map to hold dropdowns for each input, keyed by input element
    const dropdownMap = new WeakMap();

    // Initialize with empty state
    document.addEventListener('DOMContentLoaded', function() {
        toggleEmptyState();
        // Add one empty stage row by default
        setTimeout(() => {
            addStageRow();
        }, 300);
        // Setup autocomplete for initial inputs (from, to)
        setupTypeahead(document.getElementById('from'));
        setupTypeahead(document.getElementById('to'));
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
        const distance = stage.distanceFromStart !== undefined ? stage.distanceFromStart : 0;

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
        
        const newStageInput = row.querySelector('.stageName');
        setupTypeahead(newStageInput); // Setup autocomplete for the new stage input

        // Set focus to the newly added stage name input after a short delay
        setTimeout(() => {
            newStageInput.focus();
        }, 100);
    }

    function removeStageRow(button) {
        const row = button.closest('tr');
        const inputElement = row.querySelector('.stageName');
        const dropdown = dropdownMap.get(inputElement);
        if (dropdown) {
            dropdown.remove(); // Remove the associated dropdown from the body
            dropdownMap.delete(inputElement); // Clean up the map
        }

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
            const resText = await res.text();
            console.log(resText);
            if (!res.ok || resText.includes("No stages found")) {                
                console.error(`API Error: ${res.status} - ${resText}`);
                return [];
            }
            return await JSON.parse(resText);
        } catch(error) {
            console.error('Fetch error:', error);
            return [];
        }
    }

    function highlightMatch(text, query) {
        if (!query) return text;
        
        const regex = new RegExp(`(${query})`, 'gi');
        return text.replace(regex, '<span class="search-highlight">$1</span>');
    }

    function setupTypeahead(inputElement) {
        let timeout;
        let dropdown;
        let activeItem = -1; // To keep track of highlighted item for keyboard navigation

        // Use WeakMap to store and retrieve the dropdown associated with this input
        if (dropdownMap.has(inputElement)) {
            dropdown = dropdownMap.get(inputElement);
        } else {
            dropdown = document.createElement('div');
            dropdown.className = 'autocomplete-dropdown';
            document.body.appendChild(dropdown); // Append to body
            dropdownMap.set(inputElement, dropdown);
        }
        
        const positionDropdown = () => {
            const rect = inputElement.getBoundingClientRect();
            dropdown.style.left = rect.left + window.scrollX + 'px';
            dropdown.style.top = rect.bottom + window.scrollY + 'px';
            dropdown.style.width = rect.width + 'px';
        };

        const highlightItem = (index) => {
            const items = dropdown.querySelectorAll('.autocomplete-item');
            items.forEach((item, i) => {
                if (i === index) {
                    item.classList.add('highlighted');
                    scrollIntoViewIfNeeded(item, dropdown); // Ensure highlighted item is visible
                } else {
                    item.classList.remove('highlighted');
                }
            });
        };

        const scrollIntoViewIfNeeded = (element, parent) => {
            if (!element || !parent) return;

            const parentRect = parent.getBoundingClientRect();
            const elementRect = element.getBoundingClientRect();

            if (elementRect.top < parentRect.top) {
                parent.scrollTop -= (parentRect.top - elementRect.top);
            } else if (elementRect.bottom > parentRect.bottom) {
                parent.scrollTop += (elementRect.bottom - parentRect.bottom);
            }
        };

        const selectActiveItem = () => {
            const items = dropdown.querySelectorAll('.autocomplete-item');
            if (activeItem >= 0 && activeItem < items.length) {
                inputElement.value = items[activeItem].dataset.value; // Use dataset value for selection
                dropdown.style.display = 'none';
                activeItem = -1; // Reset active item
                inputElement.focus(); // Keep focus on the input
            } else if (items.length === 1) { // If only one item and no active selection, select it
                    inputElement.value = items[0].dataset.value;
                    dropdown.style.display = 'none';
                    activeItem = -1;
                    inputElement.focus();
            }
        };


        inputElement.addEventListener('input', async () => {
            clearTimeout(timeout);
            dropdown.innerHTML = '';
            dropdown.style.display = 'none'; // Hide immediately on new input
            activeItem = -1; // Reset active item on new input

            const query = inputElement.value.trim();
            if (query.length < 2) {
                return;
            }
            
            timeout = setTimeout(async () => {
                const stages = await fetchStages(query);
                if (stages.length === 0) {
                    return;
                }
                
                stages.forEach(stage => {
                    const item = document.createElement('div');
                    item.className = 'autocomplete-item';
                    item.innerHTML = highlightMatch(stage, query);
                    item.dataset.value = stage; // Store the full stage name
                    item.addEventListener('click', (event) => {
                        // Prevent blur from hiding dropdown immediately
                        event.preventDefault(); 
                        inputElement.value = stage;
                        dropdown.style.display = 'none';
                        activeItem = -1;
                        inputElement.focus(); // Keep focus on the input
                    });
                    dropdown.appendChild(item);
                });
                
                positionDropdown(); // Position before displaying
                dropdown.style.display = 'block';
            }, 300);
        });
        
        inputElement.addEventListener('focus', () => {
            // Reposition and show if there are existing results or if query is ready
            const query = inputElement.value.trim();
            if (query.length >= 2 && dropdown.children.length > 0) {
                positionDropdown();
                dropdown.style.display = 'block';
            }
        });

        inputElement.addEventListener('blur', () => {
            // Use a small delay to allow click on dropdown item to register
            // and to check if focus moves to the dropdown itself
            setTimeout(() => {
                // Check if the focus is NOT on the input AND NOT within the dropdown
                if (document.activeElement !== inputElement && !dropdown.contains(document.activeElement)) {
                    dropdown.style.display = 'none';
                    activeItem = -1; // Reset active item when dropdown is hidden
                }
            }, 150); // Increased timeout slightly
        });

        // Keyboard navigation for dropdown
        inputElement.addEventListener('keydown', (event) => {
            const items = dropdown.querySelectorAll('.autocomplete-item');
            if (items.length === 0 || dropdown.style.display === 'none') {
                return;
            }

            switch (event.key) {
                case 'ArrowDown':
                    event.preventDefault(); // Prevent cursor movement in input
                    activeItem = (activeItem + 1) % items.length;
                    highlightItem(activeItem);
                    break;
                case 'ArrowUp':
                    event.preventDefault(); // Prevent cursor movement in input
                    activeItem = (activeItem - 1 + items.length) % items.length;
                    highlightItem(activeItem);
                    break;
                case 'Enter':
                    event.preventDefault(); // Prevent form submission
                    selectActiveItem();
                    break;
                case 'Escape':
                    event.preventDefault();
                    dropdown.style.display = 'none';
                    activeItem = -1;
                    break;
            }
        });

        // Reposition dropdown on scroll/resize
        window.addEventListener('scroll', () => {
            if (dropdown.style.display === 'block') {
                positionDropdown();
            }
        });
        window.addEventListener('resize', () => {
            if (dropdown.style.display === 'block') {
                positionDropdown();
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
                // Read response as text to get potential error message from API
                return response.text().then(text => {
                    throw new Error(text || 'Failed to add route.');
                });
            }
            return response.text();
        })
        .then(text => {
            if (text.toLowerCase().includes('success')) {
                showMessage(text, 'success');
                // Cleanup all active dropdowns before redirecting
                // dropdownMap.forEach(dropdown => dropdown.remove());
                setTimeout(() => {
                    window.location.href = 'AdminViewRoutes.php';
                }, 1500);
            } else {
                showMessage(text || 'Failed to add route. Unexpected response.', 'error');
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

    // Keyboard shortcut listener for F6
    document.addEventListener('keydown', (event) => {
        if (event.key === 'F6') {
            event.preventDefault(); // Prevent default browser behavior (e.g., F6 moving cursor to address bar)
            const addStageButton = document.getElementById('addAnotherStageBtn');
            const addFirstStageButton = document.querySelector('#emptyState .btn-primary[data-shortcut="F6"]');
            
            if (addStageButton && addStageButton.style.display !== 'none') {
                addStageButton.click();
            } else if (addFirstStageButton && addFirstStageButton.style.display !== 'none') {
                addFirstStageButton.click();
            }
        }
    });
</script>

</body>
</html>