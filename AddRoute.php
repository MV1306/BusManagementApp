<!DOCTYPE html>
<html>
<head>
    <title>Add New Route with Stages</title>
    <style>
        body { font-family: Arial, sans-serif; }
        form { margin: 40px auto; padding: 20px; border: 1px solid #ccc; border-radius: 8px; }
        label { display: block; margin-bottom: 10px; }
        input[type=text], input[type=number] { width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box; }
        button { background-color: #28a745; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; margin-top: 15px; }
        button:hover { background-color: #218838; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; }
        .btn-remove-stage { background-color: #dc3545; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 4px; }
        .btn-remove-stage:hover { background-color: #c82333; }
        .message { text-align: center; margin: 20px 0; }
        .add-stage-btn { background-color: #007bff; margin-top: 10px; }
        .add-stage-btn:hover { background-color: #0069d9; }
        .back-btn {
            background-color: #6c757d; 
            margin-top: 15px; 
            margin-right: 10px;
        }
        .back-btn:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    
<?php include 'navbar.php'; ?>


<h2 style="text-align:center;">Add New Route with Stages</h2>

<form id="addRouteForm" onsubmit="event.preventDefault(); saveRoute();">

    <label>Route Code:
        <input type="text" id="code" name="code" required>
    </label>

    <label>From:
        <input type="text" id="from" name="from" required>
    </label>

    <label>To:
        <input type="text" id="to" name="to" required>
    </label>

    <h3>Stages</h3>
    <table id="stagesTable">
        <thead>
            <tr>
                <th>Stage Name</th>
                <th>Stage Order</th>
                <th>Distance From Start</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <!-- Stage rows go here -->
        </tbody>
    </table>

    <button type="button" class="add-stage-btn" onclick="addStageRow()">+ Add Stage</button>
    
    <br>
    
    <button type="submit">Add Route</button>
	    
    <button type="button" class="back-btn" onclick="window.location.href='ViewRoutes.php'">Back</button>
</form>

<div class="message" id="message"></div>

<script>
    function addStageRow(stage = {}) {
        const tbody = document.querySelector('#stagesTable tbody');
        const stageName = stage.stageName || '';
        const stageOrder = stage.stageOrder !== undefined ? stage.stageOrder : '';
        const distance = stage.distanceFromStart !== undefined ? stage.distanceFromStart : '';

        const row = document.createElement('tr');
        row.innerHTML = `
            <td><input type="text" class="stageName" placeholder="Stage Name" value="${stageName}" required></td>
            <td><input type="number" class="stageOrder" placeholder="Stage Order" value="${stageOrder}" min="0" required></td>
            <td><input type="number" class="distanceFromStart" placeholder="Distance From Start" step="0.01" value="${distance}" min="0" required></td>
            <td><button type="button" class="btn-remove-stage" onclick="removeStageRow(this)">Remove</button></td>
        `;
        tbody.appendChild(row);
    }

    function removeStageRow(button) {
        button.closest('tr').remove();
    }

    function saveRoute() {
        const code = document.getElementById('code').value.trim();
        const from = document.getElementById('from').value.trim();
        const to = document.getElementById('to').value.trim();

        if (!code || !from || !to) {
            alert('Please fill all route fields.');
            return;
        }

        const stagesRows = document.querySelectorAll('#stagesTable tbody tr');
        if (stagesRows.length === 0) {
            if (!confirm('No stages added. Are you sure you want to continue?')) {
                return;
            }
        }

        const busStages = [];
        for (const row of stagesRows) {
            const stageName = row.querySelector('.stageName').value.trim();
            const stageOrder = row.querySelector('.stageOrder').value.trim();
            const distanceFromStart = row.querySelector('.distanceFromStart').value.trim();

            if (!stageName || !stageOrder || !distanceFromStart) {
                alert('Please fill all fields for each stage.');
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

        fetch('https://172.20.10.2/BusManagementAPI/CreateRoute', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(res => res.text())   // Read response as plain text
        .then(text => {
            alert(text);  // Show alert with the API response

            if (text.toLowerCase().includes('success')) {
                // Redirect to ViewRoute.php after alert
                window.location.href = 'ViewRoutes.php';
            } else {
                // Show error message in message div as well
                const msg = document.getElementById('message');
                msg.style.color = 'red';
                msg.textContent = text || 'Failed to add route.';
            }
        })
        .catch(err => {
            const msg = document.getElementById('message');
            msg.style.color = 'red';
            msg.textContent = 'Error: ' + err.message;
        });
    }

    // Add one empty stage row by default
    addStageRow();
</script>

</body>
</html>
