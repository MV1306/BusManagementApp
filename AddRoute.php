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
    <style>
        /* Reset and base */
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            background: #f9f9f9;
            color: #333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }
        h2 {
            margin-bottom: 20px;
            color: #2c3e50;
            text-align: center;
            font-weight: 600;
            font-size: 1.8rem;
        }
        form {
            background: #fff;
            max-width: 700px;
            width: 100%;
            padding: 25px 30px 30px 30px;
            border-radius: 10px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
        }
        label {
            font-weight: 600;
            margin-bottom: 6px;
            display: block;
            color: #444;
            font-size: 1rem;
        }
        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 10px 14px;
            margin-bottom: 18px;
            border: 1.8px solid #ccc;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.25s ease;
        }
        input[type="text"]:focus,
        input[type="number"]:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 6px rgba(0, 123, 255, 0.4);
        }
        h3 {
            margin-top: 0;
            margin-bottom: 10px;
            color: #2c3e50;
            font-weight: 700;
            border-bottom: 2px solid #007bff;
            padding-bottom: 6px;
        }

        /* Table styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            font-size: 0.95rem;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        thead {
            background: #007bff;
            color: #fff;
            font-weight: 700;
        }
        th, td {
            padding: 12px 15px;
            border-bottom: 1px solid #e1e1e1;
            text-align: left;
        }
        tbody tr:nth-child(even) {
            background: #f9f9f9;
        }
        tbody tr:last-child td {
            border-bottom: none;
        }
        /* Buttons */
        button {
            font-size: 1rem;
            padding: 12px 20px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
            user-select: none;
        }
        .add-stage-btn {
            background-color: #007bff;
            color: #fff;
            margin-top: 15px;
            align-self: flex-start;
        }
        .add-stage-btn:hover {
            background-color: #0056b3;
        }
        .btn-remove-stage {
            background-color: #dc3545;
            color: white;
            padding: 6px 12px;
            border-radius: 5px;
        }
        .btn-remove-stage:hover {
            background-color: #b02a37;
        }
        button[type="submit"] {
            background-color: #28a745;
            color: white;
            margin-top: 25px;
            width: 100%;
            font-weight: 700;
        }
        button[type="submit"]:hover {
            background-color: #218838;
        }
        .back-btn {
            background-color: #6c757d;
            color: #fff;
            margin-top: 15px;
            width: 100%;
            font-weight: 600;
        }
        .back-btn:hover {
            background-color: #5a6268;
        }

        .message {
            margin: 25px auto 0;
            font-size: 1.1rem;
            font-weight: 600;
            color: #e74c3c;
            max-width: 700px;
            text-align: center;
        }

        /* Responsive */
        @media (max-width: 768px) {
            form {
                padding: 20px;
            }
            th, td {
                padding: 10px 8px;
                font-size: 0.9rem;
            }
            button[type="submit"],
            .back-btn {
                font-size: 1.1rem;
                padding: 14px;
            }
        }
        @media (max-width: 480px) {
            /* Stack labels and inputs */
            label {
                font-size: 0.9rem;
            }
            input[type="text"],
            input[type="number"] {
                font-size: 0.9rem;
                padding: 9px 12px;
            }
            button {
                font-size: 0.95rem;
                padding: 10px 14px;
            }
            /* Table scroll on small screens */
            table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<h2>Add New Route with Stages</h2>

<form id="addRouteForm" onsubmit="event.preventDefault(); saveRoute();">
    <label for="code">Route Code:</label>
    <input type="text" id="code" name="code" required placeholder="Enter Route Code" />

    <label for="from">From:</label>
    <input type="text" id="from" name="from" required placeholder="Starting Point" />

    <label for="to">To:</label>
    <input type="text" id="to" name="to" required placeholder="Destination" />

    <h3>Stages</h3>
    <table id="stagesTable" aria-label="Stages Table">
        <thead>
            <tr>
                <th scope="col">Stage Name</th>
                <th scope="col">Stage Order</th>
                <th scope="col">Distance From Start (km)</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            <!-- Stage rows go here -->
        </tbody>
    </table>

    <button type="button" class="add-stage-btn" onclick="addStageRow()">+ Add Stage</button>

    <button type="submit">Add Route</button>
    <button type="button" class="back-btn" onclick="window.location.href='ViewRoutes.php'">Back</button>
</form>

<div class="message" id="message"></div>

<script>

    const API_BASE_URL = "<?php echo $apiBaseUrl; ?>";

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

        fetch(`${API_BASE_URL}CreateRoute`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(res => res.text())
        .then(text => {
            alert(text);

            if (text.toLowerCase().includes('success')) {
                window.location.href = 'ViewRoutes.php';
            } else {
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
