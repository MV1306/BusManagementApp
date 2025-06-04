<?php
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
        :root {
            /* RCB Colors */
            --rcb-red: #CE1126;
            --rcb-gold: #F7D100;
            --rcb-black: #2F2F2F;
            --rcb-dark-grey: #4A4A4A;
            --rcb-light-grey: #E0E0E0;
            --rcb-white: #FFFFFF;

            --primary-color: var(--rcb-red);
            --primary-hover: #A90E20; /* A darker shade of RCB red */
            --success-color: #4CAF50; /* Standard green for success */
            --error-color: var(--rcb-gold); /* Using gold for attention/error */
            --light-bg: var(--rcb-light-grey);
            --dark-text: var(--rcb-black);
            --muted-text: var(--rcb-dark-grey);
            --border-radius: 0.75rem; /* Slightly larger border radius for a softer look */
            --box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.12); /* More prominent shadow */
            --transition: all 0.3s ease;
        }
        /* Define RGB values for RCB colors */
        :root {
            --rcb-red-rgb: 206, 17, 38;
            --rcb-gold-rgb: 247, 209, 0;
            --rcb-black-rgb: 47, 47, 47;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background-color: var(--light-bg);
            color: var(--dark-text);
            padding-top: 2rem;
            padding-bottom: 2rem;
        }

        .fare-container {
            max-width: 700px;
            margin: auto;
            background-color: var(--rcb-white);
            padding: 2.5rem; /* Increased padding */
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            border: 1px solid rgba(var(--rcb-red-rgb), 0.1); /* Subtle border for definition */
        }

        @media (max-width: 768px) {
            .fare-container {
                padding: 1.5rem;
                margin: 0 1rem;
            }
        }

        h2 {
            text-align: center;
            margin-bottom: 2rem; /* Increased margin-bottom */
            color: var(--primary-color);
            font-weight: 700; /* Bolder font weight */
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem; /* Increased gap */
        }

        .form-label {
            font-weight: 600; /* Bolder label font weight */
            margin-bottom: 0.6rem;
            color: var(--dark-text);
        }

        .form-control, .form-select {
            padding: 0.85rem 1.15rem; /* Slightly larger padding */
            border-radius: var(--border-radius);
            border: 1px solid #ced4da;
            transition: var(--transition);
            color: var(--dark-text); /* Ensure input text is readable */
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(var(--rcb-red-rgb), 0.25);
        }

        .btn-calculate {
            background-color: var(--primary-color);
            border: none;
            padding: 0.85rem 1.8rem; /* Larger button padding */
            font-weight: 600; /* Bolder button text */
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            width: 100%;
            border-radius: 50px; /* Pill-shaped button */
            color: var(--rcb-white);
        }

        .btn-calculate:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px); /* Slight lift effect */
            box-shadow: 0 4px 10px rgba(var(--rcb-red-rgb), 0.3);
        }

        .result-card {
            margin-top: 2rem; /* Increased margin-top */
            padding: 2rem; /* Increased padding */
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            display: none;
        }

        .result-success {
            background-color: rgba(76, 175, 80, 0.1); /* Standard success green with transparency */
            border-left: 5px solid var(--success-color); /* Thicker border */
        }

        .result-error {
            background-color: rgba(var(--rcb-gold-rgb), 0.15); /* RCB Gold with transparency */
            border-left: 5px solid var(--error-color); /* Thicker border with RCB Gold */
        }

        .result-title {
            font-weight: 700; /* Bolder title font weight */
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--dark-text);
            font-size: 1.2rem;
        }
        .result-title .bi {
            font-size: 1.5rem;
            color: inherit; /* Inherit color from parent */
        }

        .result-success .result-title .bi {
            color: var(--success-color);
        }

        .result-error .result-title .bi {
            color: var(--error-color);
        }

        .result-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); /* Adjusted minwidth for items */
            gap: 1.5rem; /* Increased gap between items */
        }

        .result-item {
            margin-bottom: 0; /* Remove default margin */
            padding-bottom: 0.5rem; /* Add some internal padding */
            border-bottom: 1px dashed var(--rcb-light-grey); /* Dotted separator */
        }
        .result-item:last-child {
            border-bottom: none;
        }

        .result-label {
            font-weight: 600; /* Bolder label */
            color: var(--muted-text);
            font-size: 0.95rem;
        }

        .result-value {
            font-weight: 700; /* Even bolder value */
            color: var(--dark-text);
            font-size: 1.1rem;
        }

        .total-fare {
            margin-top: 1.5rem; /* Increased margin */
            padding-top: 1.5rem;
            border-top: 2px solid var(--rcb-light-grey); /* Solid border for emphasis */
            font-size: 1.5rem; /* Larger font size */
            font-weight: 800; /* Extra bold */
            color: var(--primary-color);
            text-align: center;
        }

        .loading-spinner {
            display: inline-block;
            width: 1.5rem; /* Larger spinner */
            height: 1.5rem;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: var(--rcb-white); /* Spinner color matches button text */
            animation: spin 0.8s ease-in-out infinite; /* Faster spin */
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        select[disabled] {
            background-color: #e9ecef;
            opacity: 0.8; /* Slightly less opaque */
        }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container">
    <div class="fare-container">
        <h2><i class="bi bi-calculator-fill"></i> Calculate Bus Fare</h2>

        <form id="fareForm">
            <div class="mb-3">
                <label for="routeCode" class="form-label">Route Code</label>
                <select id="routeCode" name="routeCode" class="form-select" required>
                    <option value="">Loading route codes...</option>
                </select>
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
                    <option value="">Select Route Code first</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="endStage" class="form-label">End Stage</label>
                <select id="endStage" name="endStage" class="form-select" disabled required>
                    <option value="">Select Route Code first</option>
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
    const routeCodeSelect = document.getElementById('routeCode');
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

    // Load route codes
    fetch(`${API_BASE_URL}GetRouteCodes`)
        .then(response => {
            if (!response.ok) throw new Error('Failed to load route codes');
            return response.json();
        })
        .then(data => {
            routeCodeSelect.innerHTML = '<option value="">Select Route Code</option>';
            data.forEach(code => {
                const option = document.createElement('option');
                option.value = code;
                option.textContent = code;
                routeCodeSelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error fetching route codes:', error);
            routeCodeSelect.innerHTML = '<option value="">Error loading route codes</option>';
        });

    // Handle route code change
    routeCodeSelect.addEventListener('change', function () {
        const selectedRouteCode = this.value;
        
        // Reset and disable stage selects
        startStageSelect.innerHTML = '<option value="">Loading stages...</option>';
        endStageSelect.innerHTML = '<option value="">Loading stages...</option>';
        startStageSelect.disabled = true;
        endStageSelect.disabled = true;

        if (!selectedRouteCode) return;

        // Fetch stages for selected route
        fetch(`${API_BASE_URL}GetRouteStagesByCode/${encodeURIComponent(selectedRouteCode)}`)
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
    });

    // Handle form submission
    document.getElementById('fareForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const routeCode = routeCodeSelect.value;
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