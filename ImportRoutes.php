<!DOCTYPE html>
<html>
<head>
    <title>Import Bus Data</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            padding: 30px 20px;
            width: 97%;
            margin: 40px auto;
            background: #f9f9f9;
            color: #333;
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #2c3e50;
            font-weight: 600;
        }
        form#importForm {
            background: white;
            padding: 25px 30px;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgb(0 0 0 / 0.1);
        }
        .input-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #34495e;
        }
        input[type="file"], select {
            width: 100%;
            padding: 10px 14px;
            border: 2px solid #ddd;
            border-radius: 8px;
            background: white;
            font-size: 1rem;
        }
        .buttons {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        .buttons button {
            flex: 1;
            padding: 12px 0;
            font-size: 16px;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            color: white;
            user-select: none;
        }
        .buttons button[type="submit"] {
            background-color: #007bff;
        }
        .buttons button[type="submit"]:hover {
            background-color: #0056b3;
        }
        .buttons button#clearBtn {
            background-color: #dc3545;
        }
        .buttons button#clearBtn:hover {
            background-color: #c82333;
        }
        .result-message {
            margin-top: 30px;
            padding: 15px;
            border-radius: 8px;
            font-weight: 600;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<h2>Import Bus Routes</h2>

<form id="importForm" enctype="multipart/form-data">
    <div class="input-group mb-3">
        <label for="importType">Select Import Type:</label>
        <select id="importType" name="importType" required>
            <option value="routes" selected>Routes</option>
            <option value="stageTranslations">Stage Translations</option>
        </select>
    </div>

    <div class="input-group mb-3">
        <label for="excelFile">Choose Excel File:</label>
        <input type="file" id="excelFile" name="excelFile" accept=".xlsx,.xls" required />
    </div>

    <div class="buttons">
        <button type="submit">Import</button>
        <button type="button" id="clearBtn">Clear</button>
    </div>
</form>

<div id="resultContainer"></div>

<script>
    document.getElementById('importForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const fileInput = document.getElementById('excelFile');
        const importType = document.getElementById('importType').value;
        const resultContainer = document.getElementById('resultContainer');

        if (fileInput.files.length === 0) {
            resultContainer.innerHTML = `<div class='result-message error'>Please select a file to import.</div>`;
            return;
        }

        // Decide API URL based on import type
        let apiUrl = '';
        if (importType === 'routes') {
            apiUrl = 'http://192.168.29.141/BusManagementAPI/ImportBusRoutes';
        } else if (importType === 'stageTranslations') {
            apiUrl = 'http://192.168.29.141/BusManagementAPI/ImportStageTranslations';
        } else {
            resultContainer.innerHTML = `<div class='result-message error'>Invalid import type selected.</div>`;
            return;
        }

        const formData = new FormData();
        formData.append('file', fileInput.files[0]);

        fetch(apiUrl, {
            method: 'POST',
            body: formData
        })
        .then(async response => {
            const contentType = response.headers.get("content-type");
            let data;
            if (contentType && contentType.includes("application/json")) {
                data = await response.json();
            } else {
                data = await response.text();
            }

            if (response.ok) {
                resultContainer.innerHTML = `<div class='result-message success'>${data || 'File imported successfully.'}</div>`;
            } else {
                resultContainer.innerHTML = `<div class='result-message error'>${data || 'Import failed. Please try again.'}</div>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            resultContainer.innerHTML = `<div class='result-message error'>An error occurred: ${error.message}</div>`;
        });
    });

    document.getElementById('clearBtn').addEventListener('click', function () {
        document.getElementById('excelFile').value = '';
        document.getElementById('resultContainer').innerHTML = '';
    });
</script>

</body>
</html>
