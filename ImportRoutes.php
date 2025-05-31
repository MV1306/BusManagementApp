<?php
$config = include('config.php');

$apiBaseUrl = $config['api_base_url'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Import Bus Data</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f6f9;
            padding-top: 40px;
            padding-bottom: 40px;
        }
        .form-container {
            max-width: 600px;
            margin: auto;
            background-color: #fff;
            padding: 30px 25px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #2c3e50;
        }
        .form-label {
            font-weight: 600;
            color: #34495e;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .btn-danger {
            background-color: #dc3545;
            border: none;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }
        .result-message {
            margin-top: 25px;
            padding: 15px;
            border-radius: 8px;
            font-weight: 500;
            display: flex;
            align-items: center;
        }
        .result-message.success {
            background-color: #d1e7dd;
            color: #0f5132;
        }
        .result-message.error {
            background-color: #f8d7da;
            color: #842029;
        }
        .result-message i {
            margin-right: 10px;
        }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container">
    <div class="form-container">
        <h2><i class="bi bi-upload"></i> Import Bus Routes</h2>

        <form id="importForm" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="importType" class="form-label">Select Import Type:</label>
                <select id="importType" name="importType" class="form-select" required>
                    <option value="routes" selected>Routes</option>
                    <option value="stageTranslations">Stage Translations</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="excelFile" class="form-label">Choose Excel File:</label>
                <input type="file" id="excelFile" name="excelFile" class="form-control" accept=".xlsx,.xls" required />
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-between">
                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-circle"></i> Import</button>
                <button type="button" id="clearBtn" class="btn btn-danger px-4"><i class="bi bi-x-circle"></i> Clear</button>
            </div>
        </form>

        <div id="resultContainer"></div>
    </div>
</div>

<script>

    const API_BASE_URL = "<?php echo $apiBaseUrl; ?>";

    document.getElementById('importForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const fileInput = document.getElementById('excelFile');
        const importType = document.getElementById('importType').value;
        const resultContainer = document.getElementById('resultContainer');

        if (fileInput.files.length === 0) {
            resultContainer.innerHTML = `<div class='result-message error'><i class="bi bi-exclamation-circle"></i> Please select a file to import.</div>`;
            return;
        }

        let apiUrl = '';
        if (importType === 'routes') {
            apiUrl = `${API_BASE_URL}ImportBusRoutes`;
        } else if (importType === 'stageTranslations') {
            apiUrl = `${API_BASE_URL}ImportStageTranslations`;
        } else {
            resultContainer.innerHTML = `<div class='result-message error'><i class="bi bi-x-circle"></i> Invalid import type selected.</div>`;
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
                resultContainer.innerHTML = `<div class='result-message success'><i class="bi bi-check-circle-fill"></i> ${data || 'File imported successfully.'}</div>`;
            } else {
                resultContainer.innerHTML = `<div class='result-message error'><i class="bi bi-x-circle-fill"></i> ${data || 'Import failed. Please try again.'}</div>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            resultContainer.innerHTML = `<div class='result-message error'><i class="bi bi-x-circle-fill"></i> An error occurred: ${error.message}</div>`;
        });
    });

    document.getElementById('clearBtn').addEventListener('click', function () {
        document.getElementById('importForm').reset();
        document.getElementById('resultContainer').innerHTML = '';
    });
</script>

</body>
</html>
