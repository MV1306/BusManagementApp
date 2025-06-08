<?php

require_once 'AdminAuth.php';

checkAuth();

$config = include('config.php');

$apiBaseUrl = $config['api_base_url'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Calculate Fare</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Your existing CSS remains unchanged */
        :root {
            --primary-color: #4361ee;
            --primary-hover: #3a56d4;
            --success-color: #2b9348;
            --error-color: #ef233c;
            --light-bg: #f8f9fa;
            --dark-text: #212529;
            --muted-text: #6c757d;
            --border-radius: 0.5rem;
            --box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-bg);
            color: var(--dark-text);
            padding-top: 2rem;
            padding-bottom: 2rem;
        }

        .fare-container {
            max-width: 700px;
            margin: auto;
            background-color: white;
            padding: 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }

        @media (max-width: 768px) {
            .fare-container {
                padding: 1.5rem;
                margin: 0 1rem;
            }
        }

        h2 {
            text-align: center;
            margin-bottom: 1.5rem;
            color: var(--primary-color);
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: var(--dark-text);
        }

        .form-control, .form-select {
            padding: 0.75rem 1rem;
            border-radius: var(--border-radius);
            border: 1px solid #ced4da;
            transition: var(--transition);
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
        }

        .btn-calculate {
            background-color: var(--primary-color);
            border: none;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
        }

        .btn-calculate:hover {
            background-color: var(--primary-hover);
        }

        .result-card {
            margin-top: 1.5rem;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            display: none;
        }

        .result-success {
            background-color: rgba(43, 147, 72, 0.1);
            border-left: 4px solid var(--success-color);
        }

        .result-error {
            background-color: rgba(239, 35, 60, 0.1);
            border-left: 4px solid var(--error-color);
        }

        .result-title {
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--dark-text);
        }

        .result-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .result-item {
            margin-bottom: 0.5rem;
        }

        .result-label {
            font-weight: 500;
            color: var(--muted-text);
        }

        .result-value {
            font-weight: 600;
            color: var(--dark-text);
        }

        .total-fare {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px dashed #ccc;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .loading-spinner {
            display: inline-block;
            width: 1.25rem;
            height: 1.25rem;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        select[disabled] {
            background-color: #e9ecef;
            opacity: 1;
        }
        
        /* Add these new styles for search functionality */
        .search-container {
            position: relative;
        }
        
        #routeCodeResults {
            position: absolute;
            z-index: 1000;
            width: 100%;
            max-height: 300px;
            overflow-y: auto;
            background: white;
            border: 1px solid #ced4da;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            display: none;
        }
        
        .route-option {
            padding: 0.75rem 1rem;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .route-option:hover {
            background-color: var(--light-bg);
        }
    </style>
</head>
<body>

<?php include 'AdminNavbar.php'; ?>

<div class="container">
    <div class="fare-container">
        <h2><i class="bi bi-calculator-fill"></i> Calculate Bus Fare</h2>

        <form id="fareForm">
            <div class="mb-3 search-container">
                <label for="routeCode" class="form-label">Route Code</label>
                <input type="text" id="routeCode" name="routeCode" class="form-control" 
                       placeholder="Start typing to search routes..." required
                       autocomplete="off">
                <div id="routeCodeResults" class="mt-1"></div>
            </div>

            <div class="mb-3">
                <label for="busType" class="form-label">Bus Type</label>
                <select id="busType" name="busType" class="form-select" required>
                    <option value="">Select Bus Type</option>
                    <option value="Ordinary">Ordinary</option>
                    <option value="Express">Express</option>
                    <option value="Deluxe">Deluxe</option>
                    <option value="AC">AC</option>
                    <option value="Night">Night</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="startStage" class="form-label">Start Stage</label>
                <select id="startStage" name="startStage" class="form-select" disabled required>
                    <option value="">Select a route first</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="endStage" class="form-label">End Stage</label>
                <select id="endStage" name="endStage" class="form-select" disabled required>
                    <option value="">Select a route first</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="passengers" class="form-label">Passengers</label>
                <select id="passengers" name="passengers" class="form-select" required>
                    <option value="">Select Passengers</option>
                    <?php for ($i = 1; $i <= 10; $i++): ?>
                        <option value="<?= $i ?>"><?= $i ?> <?= $i === 1 ? 'passenger' : 'passengers' ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary btn-calculate" id="calculateBtn">
                <i class="bi bi-calculator"></i> Calculate Fare
            </button>
        </form>

        <div id="fareResult" class="result-card result-success">
            <div class="result-title">
                <i class="bi bi-check-circle-fill"></i>
                <span>Fare Calculation Result</span>
            </div>
            <div class="result-details" id="resultDetails"></div>
            <div class="total-fare" id="totalFare"></div>
        </div>

        <div id="errorResult" class="result-card result-error">
            <div class="result-title">
                <i class="bi bi-exclamation-circle-fill"></i>
                <span id="errorMessage"></span>
            </div>
        </div>
    </div>
</div>

<script>
const API_BASE_URL = "<?php echo $apiBaseUrl; ?>";

document.addEventListener('DOMContentLoaded', function () {
    const routeCodeInput = document.getElementById('routeCode');
    const routeCodeResults = document.getElementById('routeCodeResults');
    const busTypeSelect = document.getElementById('busType');
    const startStageSelect = document.getElementById('startStage');
    const endStageSelect = document.getElementById('endStage');
    const passengersSelect = document.getElementById('passengers');
    const fareResultDiv = document.getElementById('fareResult');
    const errorResultDiv = document.getElementById('errorResult');
    const resultDetailsDiv = document.getElementById('resultDetails');
    const totalFareDiv = document.getElementById('totalFare');
    const errorMessageSpan = document.getElementById('errorMessage');
    const calculateBtn = document.getElementById('calculateBtn');
    let selectedRouteCode = '';
    let debounceTimer;

    // Handle route code input with debounce
    routeCodeInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const searchText = this.value.trim();
        
        if (searchText.length < 2) {
            routeCodeResults.style.display = 'none';
            return;
        }
        
        debounceTimer = setTimeout(() => {
            searchRoutes(searchText);
        }, 300);
    });

    // Handle clicks outside to close dropdown
    document.addEventListener('click', function(e) {
        if (!routeCodeInput.contains(e.target) && !routeCodeResults.contains(e.target)) {
            routeCodeResults.style.display = 'none';
        }
    });

    function searchRoutes(searchText) {
        fetch(`${API_BASE_URL}SearchRoutes/${encodeURIComponent(searchText)}`)
            .then(response => {
                if (!response.ok) throw new Error('Failed to search routes');
                return response.json();
            })
            .then(data => {
                routeCodeResults.innerHTML = '';
                
                if (data.length === 0) {
                    const noResults = document.createElement('div');
                    noResults.className = 'route-option';
                    noResults.textContent = 'No routes found';
                    routeCodeResults.appendChild(noResults);
                } else {
                    data.forEach(route => {
                        const item = document.createElement('div');
                        item.className = 'route-option';
                        item.textContent = route;
                        item.addEventListener('click', function() {
                            routeCodeInput.value = route;
                            selectedRouteCode = route;
                            routeCodeResults.style.display = 'none';
                            loadStagesForRoute(route);
                        });
                        routeCodeResults.appendChild(item);
                    });
                }
                
                routeCodeResults.style.display = 'block';
            })
            .catch(error => {
                console.error('Error searching routes:', error);
                routeCodeResults.innerHTML = '<div class="route-option">No Routes Found</div>';
                routeCodeResults.style.display = 'block';
            });
    }

    function loadStagesForRoute(routeCode) {
        // Reset and disable stage selects
        startStageSelect.innerHTML = '<option value="">Loading stages...</option>';
        endStageSelect.innerHTML = '<option value="">Loading stages...</option>';
        startStageSelect.disabled = true;
        endStageSelect.disabled = true;

        // Fetch stages for selected route
        fetch(`${API_BASE_URL}GetRouteStagesByCode/${encodeURIComponent(routeCode)}`)
            .then(response => {
                if (!response.ok) throw new Error('Failed to load stages');
                return response.json();
            })
            .then(data => {
                const stages = data.stages || [];
                
                startStageSelect.innerHTML = '<option value="">Select Start Stage</option>';
                endStageSelect.innerHTML = '<option value="">Select End Stage</option>';
                
                stages.forEach(stage => {
                    const optStart = document.createElement('option');
                    optStart.value = stage.stageName;
                    optStart.textContent = stage.stageName;
                    startStageSelect.appendChild(optStart);

                    const optEnd = document.createElement('option');
                    optEnd.value = stage.stageName;
                    optEnd.textContent = stage.stageName;
                    endStageSelect.appendChild(optEnd);
                });
                
                startStageSelect.disabled = false;
                endStageSelect.disabled = false;
            })
            .catch(error => {
                console.error('Error fetching stages:', error);
                startStageSelect.innerHTML = '<option value="">Error loading stages</option>';
                endStageSelect.innerHTML = '<option value="">Error loading stages</option>';
                startStageSelect.disabled = false;
                endStageSelect.disabled = false;
            });
    }

    // Handle form submission
    document.getElementById('fareForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const routeCode = selectedRouteCode;
        const busType = busTypeSelect.value;
        const startStage = startStageSelect.value;
        const endStage = endStageSelect.value;
        const passengers = passengersSelect.value;

        // Hide previous results
        fareResultDiv.style.display = 'none';
        errorResultDiv.style.display = 'none';

        // Validate form
        if (!routeCode || !busType || !startStage || !endStage || !passengers) {
            showError('Please fill in all fields');
            return;
        }

        // Show loading state
        const originalBtnText = calculateBtn.innerHTML;
        calculateBtn.innerHTML = `<span class="loading-spinner"></span> Calculating...`;
        calculateBtn.disabled = true;

        // Make API call
        const apiUrl = `${API_BASE_URL}CalculateFare/${encodeURIComponent(routeCode)}/${encodeURIComponent(busType)}/${encodeURIComponent(startStage)}/${encodeURIComponent(endStage)}`;

        fetch(apiUrl, {
            method: 'GET',
            headers: { 'Accept': 'application/json' }
        })
            .then(response => {
                if (!response.ok) throw new Error(`Server error: ${response.status}`);
                return response.json();
            })
            .then(data => {
                const totalFare = parseInt(passengers) * data.fare;
                
                // Display results
                resultDetailsDiv.innerHTML = `
                    <div class="result-item">
                        <div class="result-label">Route:</div>
                        <div class="result-value">${data.routeCode}</div>
                    </div>
                    <div class="result-item">
                        <div class="result-label">Start Point:</div>
                        <div class="result-value">${data.from} / ${data.fromTranslated || '-'}</div>
                    </div>
                    <div class="result-item">
                        <div class="result-label">End Point:</div>
                        <div class="result-value">${data.to} / ${data.toTranslated || '-'}</div>
                    </div>
                    <div class="result-item">
                        <div class="result-label">Stages Travelled:</div>
                        <div class="result-value">${data.stagesTravelled}</div>
                    </div>
                    <div class="result-item">
                        <div class="result-label">Passengers:</div>
                        <div class="result-value">${passengers}</div>
                    </div>
                    <div class="result-item">
                        <div class="result-label">Fare (Per Passenger):</div>
                        <div class="result-value">₹${data.fare}</div>
                    </div>
                `;
                
                totalFareDiv.innerHTML = `Total Fare: ₹${totalFare}`;
                fareResultDiv.style.display = 'block';
                
                // Scroll to result
                fareResultDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            })
            .catch(err => {
                showError(err.message || 'Failed to calculate fare');
            })
            .finally(() => {
                calculateBtn.innerHTML = originalBtnText;
                calculateBtn.disabled = false;
            });
    });

    function showError(message) {
        errorMessageSpan.textContent = message;
        errorResultDiv.style.display = 'block';
        errorResultDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>