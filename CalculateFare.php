<!DOCTYPE html>
<html>
<head>
    <title>Calculate Fare</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; margin: auto; }
        label { display: block; margin-top: 10px; }
        select, input { width: 100%; padding: 8px; margin-top: 4px; }
        button { margin-top: 15px; padding: 10px; width: 100%; background-color: #007bff; color: white; border: none; cursor: pointer; }
        .result, .error { margin-top: 20px; padding: 10px; border-radius: 6px; display: none; }
        .result { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }

    </style>
</head>
<body>

    <?php include 'navbar.php'; ?>
<h2>Calculate Bus Fare</h2>

<form id="fareForm">
    <label for="routeCode">Route Code:</label>
    <select id="routeCode" name="routeCode" required>
        <option value="">Loading route codes...</option>
    </select>

    <label for="busType">Bus Type:</label>
    <select id="busType" name="busType" required>
        <option value="">Select Bus Type</option>
        <option value="Ordinary">Ordinary</option>
        <option value="Express">Express</option>
        <option value="Deluxe">Deluxe</option>
        <option value="AC">AC</option>
        <option value="Night">Night</option>
    </select>

    <label for="startStage">Start Stage:</label>
    <select id="startStage" name="startStage" required>
        <option value="">Select Start Stage</option>
    </select>

    <label for="endStage">End Stage:</label>
    <select id="endStage" name="endStage" required>
        <option value="">Select End Stage</option>
    </select>
	
	<label>Passengers:</label>
        <select id="passengers" name="passengers" required>
        <option value="">Select Passengers</option>
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5">5</option>
        <option value="6">6</option>
        <option value="7">7</option>
        <option value="8">8</option>
        <option value="9">9</option>
        <option value="10">10</option>
    </select>

    <button type="submit">Calculate Fare</button>
</form>

<div id="fareResult" class="result"></div>
<div id="errorResult" class="error"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const routeCodeSelect = document.getElementById('routeCode');
    const startStageSelect = document.getElementById('startStage');
    const endStageSelect = document.getElementById('endStage');
    const passengersSelect = document.getElementById('passengers');

    // Fetch route codes
    fetch('https://busmanagementapi.onrender.com/GetRouteCodes')
        .then(response => response.json())
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

    // Load stages when route code changes
    routeCodeSelect.addEventListener('change', function() {
        const selectedRouteCode = this.value;
        startStageSelect.innerHTML = '<option value="">Loading stages...</option>';
        endStageSelect.innerHTML = '<option value="">Loading stages...</option>';

        if (!selectedRouteCode) return;

        fetch(`https://busmanagementapi.onrender.com/GetRouteStagesByCode/${selectedRouteCode}`)
            .then(response => response.json())
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
            })
            .catch(error => {
                console.error('Error fetching stages:', error);
                startStageSelect.innerHTML = '<option value="">Error loading stages</option>';
                endStageSelect.innerHTML = '<option value="">Error loading stages</option>';
            });
    });

    // Handle fare calculation via JavaScript
    document.getElementById('fareForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const routeCode = routeCodeSelect.value;
        const busType = document.getElementById('busType').value;
        const startStage = startStageSelect.value;
        const endStage = endStageSelect.value;
        const fareResultDiv = document.getElementById('fareResult');
        const errorResultDiv = document.getElementById('errorResult');

        fareResultDiv.style.display = 'none';
        errorResultDiv.style.display = 'none';

        if (!routeCode || !busType || !startStage || !endStage) {
            errorResultDiv.textContent = 'Please select all fields.';
            errorResultDiv.style.display = 'block';
            return;
        }

        const apiUrl = `https://busmanagementapi.onrender.com/CalculateFare/${encodeURIComponent(routeCode)}/${encodeURIComponent(busType)}/${encodeURIComponent(startStage)}/${encodeURIComponent(endStage)}`;

        fetch(apiUrl, {
            method: 'GET',
            headers: { 'Accept': 'application/json' }
        })
        .then(res => {
            if (!res.ok) throw new Error(`Server error: ${res.status}`);
            return res.json();
        })
        .then(data => {
            const passengers = parseInt(passengersSelect.value);
const totalFare = passengers * data.fare;

fareResultDiv.innerHTML = `
    <strong>Route :</strong> ${data.routeCode}<br>
    <strong>Start Point:</strong> ${data.from} / ${data.fromTranslated}<br>
    <strong>End Point:</strong> ${data.to} / ${data.toTranslated}<br>
    <strong>Stages Travelled:</strong> ${data.stagesTravelled}<br>
    <strong>Passengers:</strong> ${passengers}<br>
    <strong>Fare:</strong> ₹${data.fare}<br>
    <strong>Total Fare:</strong> ₹${totalFare}
`;
            fareResultDiv.style.display = 'block';
        })
        .catch(err => {
            errorResultDiv.textContent = `Error: ${err.message}`;
            errorResultDiv.style.display = 'block';
        });
    });
});
</script>

</body>
</html>
