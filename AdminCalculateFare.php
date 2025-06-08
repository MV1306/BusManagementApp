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
    <title>Admin - Calculate Fare</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            /* Modern Color Palette */
            --primary: #4361ee;
            --primary-hover: #3a56d4;
            --secondary: #3f37c9;
            --accent: #4895ef;
            --success: #4cc9f0;
            --warning: #f72585;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --light-gray: #e9ecef;
            
            --border-radius: 12px;
            --box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #f5f7ff;
            color: var(--dark);
            line-height: 1.6;
            padding: 0;
            margin: 0;
        }

        .app-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            background: white;
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 1.5rem;
            border-bottom: none;
        }

        .card-title {
            font-weight: 700;
            font-size: 1.5rem;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .card-body {
            padding: 2rem;
        }

        @media (max-width: 768px) {
            .card-body {
                padding: 1.5rem;
            }
        }

        .form-label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--dark);
            font-size: 0.95rem;
        }

        .form-control, .form-select {
            padding: 0.85rem 1rem;
            border-radius: 8px;
            border: 1px solid var(--light-gray);
            transition: var(--transition);
            font-size: 0.95rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.2);
        }

        .btn-primary {
            background-color: var(--primary);
            border: none;
            padding: 1rem;
            font-weight: 600;
            border-radius: 8px;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            width: 100%;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
        }

        .result-card {
            margin-top: 2rem;
            border-radius: var(--border-radius);
            overflow: hidden;
            display: none;
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .result-header {
            background: linear-gradient(135deg, var(--success), #3a86ff);
            color: white;
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .result-error .result-header {
            background: linear-gradient(135deg, var(--warning), #f72585);
        }

        .result-title {
            font-weight: 600;
            font-size: 1.1rem;
            margin: 0;
        }

        .result-body {
            padding: 1.5rem;
            background-color: white;
        }

        .result-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
        }

        .result-item {
            margin-bottom: 1rem;
        }

        .result-label {
            font-weight: 500;
            color: var(--gray);
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
        }

        .result-value {
            font-weight: 600;
            color: var(--dark);
            font-size: 1.05rem;
        }

        .total-fare {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--light-gray);
            text-align: center;
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--primary);
        }

        .loading-spinner {
            display: inline-block;
            width: 1.25rem;
            height: 1.25rem;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 0.8s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Search dropdown styles */
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
            border-radius: 0 0 8px 8px;
            box-shadow: 0 6px 12px rgba(0,0,0,0.1);
            display: none;
            border: 1px solid var(--light-gray);
            border-top: none;
        }

        .search-item {
            padding: 0.75rem 1rem;
            cursor: pointer;
            transition: background-color 0.2s;
            border-bottom: 1px solid var(--light-gray);
        }

        .search-item:last-child {
            border-bottom: none;
        }

        .search-item:hover {
            background-color: var(--light);
        }

        .search-highlight {
            font-weight: 600;
            color: var(--primary);
        }

        /* Floating labels effect */
        .form-floating > label {
            transition: all 0.2s;
        }

        /* Responsive adjustments */
        @media (max-width: 576px) {
            .result-grid {
                grid-template-columns: 1fr;
            }
            
            .card-title {
                font-size: 1.25rem;
            }
        }
    </style>
</head>
<body>

<?php include 'AdminNavbar.php'; ?>

<div class="app-container">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">
                <i class="bi bi-calculator"></i>
                Bus Fare Calculator
            </h1>
        </div>
        
        <div class="card-body">
            <form id="fareForm">
                <div class="mb-4 search-container">
                    <label for="routeCode" class="form-label">Search Route</label>
                    <input type="text" id="routeCode" name="routeCode" class="form-control" 
                           placeholder="Type route code or name..." required
                           autocomplete="off">
                    <div id="routeCodeResults"></div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="busType" class="form-label">Bus Type</label>
                            <select id="busType" name="busType" class="form-select" required>
                                <option value="" disabled selected>Select bus type</option>
                                <option value="Ordinary">Ordinary</option>
                                <option value="Express">Express</option>
                                <option value="Deluxe">Deluxe</option>
                                <option value="AC">AC</option>
                                <option value="Night">Night</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="passengers" class="form-label">Passengers</label>
                            <select id="passengers" name="passengers" class="form-select" required>
                                <option value="" disabled selected>Select passengers</option>
                                <?php for ($i = 1; $i <= 10; $i++): ?>
                                    <option value="<?= $i ?>"><?= $i ?> <?= $i === 1 ? 'passenger' : 'passengers' ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="startStage" class="form-label">Start Stage</label>
                            <select id="startStage" name="startStage" class="form-select" disabled required>
                                <option value="" disabled selected>Select route first</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label for="endStage" class="form-label">End Stage</label>
                            <select id="endStage" name="endStage" class="form-select" disabled required>
                                <option value="" disabled selected>Select route first</option>
                            </select>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" id="calculateBtn">
                    <i class="bi bi-calculator"></i> Calculate Fare
                </button>
            </form>

            <div id="fareResult" class="result-card">
                <div class="result-header">
                    <i class="bi bi-check-circle-fill"></i>
                    <h3 class="result-title">Fare Calculation Result</h3>
                </div>
                <div class="result-body">
                    <div class="result-grid" id="resultDetails"></div>
                    <div class="total-fare" id="totalFare"></div>
                </div>
            </div>

            <div id="errorResult" class="result-card result-error">
                <div class="result-header">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <h3 class="result-title" id="errorMessage">Error</h3>
                </div>
                <div class="result-body">
                    <p>Please check your inputs and try again.</p>
                </div>
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

    // Enhanced route search with debounce
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

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!routeCodeInput.contains(e.target) && !routeCodeResults.contains(e.target)) {
            routeCodeResults.style.display = 'none';
        }
    });

    function highlightMatch(text, search) {
        const regex = new RegExp(`(${search})`, 'gi');
        return text.replace(regex, '<span class="search-highlight">$1</span>');
    }

    function searchRoutes(searchText) {
        fetch(`${API_BASE_URL}SearchRoutes/${encodeURIComponent(searchText)}`)
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                routeCodeResults.innerHTML = '';
                
                if (data.length === 0) {
                    const noResults = document.createElement('div');
                    noResults.className = 'search-item';
                    noResults.textContent = 'No routes found';
                    routeCodeResults.appendChild(noResults);
                } else {
                    data.forEach(route => {
                        const item = document.createElement('div');
                        item.className = 'search-item';
                        item.innerHTML = highlightMatch(route, searchText);
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
                routeCodeResults.innerHTML = '<div class="search-item">Error loading routes</div>';
                routeCodeResults.style.display = 'block';
            });
    }

    function loadStagesForRoute(routeCode) {
        // Reset and disable stage selects
        startStageSelect.innerHTML = '<option value="" disabled selected>Loading stages...</option>';
        endStageSelect.innerHTML = '<option value="" disabled selected>Loading stages...</option>';
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
                
                startStageSelect.innerHTML = '<option value="" disabled selected>Select start stage</option>';
                endStageSelect.innerHTML = '<option value="" disabled selected>Select end stage</option>';
                
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
                startStageSelect.innerHTML = '<option value="" disabled selected>Error loading stages</option>';
                endStageSelect.innerHTML = '<option value="" disabled selected>Error loading stages</option>';
                startStageSelect.disabled = false;
                endStageSelect.disabled = false;
            });
    }

    // Form submission handler
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
            showError('Please fill in all required fields');
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
                if (!response.ok) throw new Error(`Error: ${response.status}`);
                return response.json();
            })
            .then(data => {
                const totalFare = parseInt(passengers) * data.fare;
                
                // Format results
                resultDetailsDiv.innerHTML = `
                    <div class="result-item">
                        <div class="result-label">Route Code</div>
                        <div class="result-value">${data.routeCode}</div>
                    </div>
                    <div class="result-item">
                        <div class="result-label">Bus Type</div>
                        <div class="result-value">${busType}</div>
                    </div>
                    <div class="result-item">
                        <div class="result-label">From</div>
                        <div class="result-value">${data.from} ${data.fromTranslated ? `(${data.fromTranslated})` : ''}</div>
                    </div>
                    <div class="result-item">
                        <div class="result-label">To</div>
                        <div class="result-value">${data.to} ${data.toTranslated ? `(${data.toTranslated})` : ''}</div>
                    </div>
                    <div class="result-item">
                        <div class="result-label">Stages</div>
                        <div class="result-value">${data.stagesTravelled}</div>
                    </div>
                    <div class="result-item">
                        <div class="result-label">Passengers</div>
                        <div class="result-value">${passengers}</div>
                    </div>
                    <div class="result-item">
                        <div class="result-label">Fare per Passenger</div>
                        <div class="result-value">₹${data.fare}</div>
                    </div>
                `;
                
                totalFareDiv.innerHTML = `Total Fare: <strong>₹${totalFare}</strong>`;
                fareResultDiv.style.display = 'block';
                
                // Smooth scroll to result
                setTimeout(() => {
                    fareResultDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }, 100);
            })
            .catch(err => {
                showError(err.message || 'Failed to calculate fare. Please try again.');
            })
            .finally(() => {
                calculateBtn.innerHTML = originalBtnText;
                calculateBtn.disabled = false;
            });
    });

    function showError(message) {
        errorMessageSpan.textContent = message;
        errorResultDiv.style.display = 'block';
        setTimeout(() => {
            errorResultDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }, 100);
    }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>