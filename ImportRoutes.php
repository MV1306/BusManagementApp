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
        :root {
            --primary-color: #4361ee;
            --primary-hover: #3a56d4;
            --danger-color: #ef233c;
            --danger-hover: #d90429;
            --success-color: #2b9348;
            --warning-color: #f77f00;
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

        .form-container {
            max-width: 700px;
            margin: auto;
            background-color: white;
            padding: 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            transition: var(--transition);
        }

        @media (max-width: 768px) {
            .form-container {
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

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: var(--border-radius);
            font-weight: 500;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
        }

        .btn-danger {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
        }

        .btn-danger:hover {
            background-color: var(--danger-hover);
            border-color: var(--danger-hover);
        }

        .result-message {
            margin-top: 1.5rem;
            padding: 1rem;
            border-radius: var(--border-radius);
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .result-message.success {
            background-color: rgba(43, 147, 72, 0.15);
            color: var(--success-color);
            border-left: 4px solid var(--success-color);
        }

        .result-message.error {
            background-color: rgba(239, 35, 60, 0.15);
            color: var(--danger-color);
            border-left: 4px solid var(--danger-color);
        }

        .file-input-container {
            position: relative;
            overflow: hidden;
        }

        .file-input-label {
            display: block;
            width: 100%;
            padding: 0.75rem;
            background-color: #f8f9fa;
            border: 1px dashed #ced4da;
            border-radius: var(--border-radius);
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
        }

        .file-input-label:hover {
            background-color: #e9ecef;
            border-color: var(--primary-color);
        }

        .file-input-label i {
            font-size: 1.5rem;
            color: var(--muted-text);
            margin-bottom: 0.5rem;
        }

        .file-name {
            margin-top: 0.5rem;
            font-size: 0.875rem;
            color: var(--muted-text);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .button-group {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        @media (max-width: 576px) {
            .button-group {
                flex-direction: column;
            }
            
            .button-group .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<?php include 'AdminNavbar.php'; ?>

<div class="container">
    <div class="form-container">
        <h2><i class="bi bi-cloud-arrow-up-fill"></i> Import Bus Data</h2>

        <form id="importForm" enctype="multipart/form-data">
            <div class="mb-4">
                <label for="importType" class="form-label">Import Type</label>
                <select id="importType" name="importType" class="form-select" required>
                    <option value="routes" selected>Bus Routes</option>
                    <option value="stageCoordinates">Stage Coordinates</option>
                    <option value="stageTranslations">Stage Translations</option>
                </select>
                <div class="form-text">Select the type of data you want to import</div>
            </div>

            <div class="mb-4">
                <label for="excelFile" class="form-label">Excel File</label>
                <div class="file-input-container">
                    <label for="excelFile" class="file-input-label">
                        <i class="bi bi-file-earmark-excel"></i>
                        <div>Click to browse or drag & drop your file</div>
                        <div class="file-name" id="fileName">No file selected</div>
                    </label>
                    <input type="file" id="excelFile" name="excelFile" class="d-none" accept=".xlsx,.xls" required />
                </div>
                <div class="form-text">Supported formats: .xlsx, .xls (Max size: 5MB)</div>
            </div>

            <!-- Template download section -->
            <div class="mb-4">
                <label class="form-label">Download Template</label>
                <div class="button-group">
                    <button type="button" id="downloadTemplateBtn" class="btn btn-outline-primary">
                        <i class="bi bi-file-earmark-excel"></i> Download Template
                    </button>
                </div>
                <div class="form-text">Download the template for the selected import type</div>
            </div>

            <div class="button-group">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-upload"></i> Import Data
                </button>
                <button type="button" id="clearBtn" class="btn btn-outline-secondary">
                    <i class="bi bi-x-lg"></i> Clear
                </button>
            </div>
        </form>

        <div id="resultContainer"></div>
    </div>
</div>

<script>
    const API_BASE_URL = "<?php echo $apiBaseUrl; ?>";

    // File input handling
    const fileInput = document.getElementById('excelFile');
    const fileNameElement = document.getElementById('fileName');

    fileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            fileNameElement.textContent = this.files[0].name;
            fileNameElement.style.color = 'var(--dark-text)';
        } else {
            fileNameElement.textContent = 'No file selected';
            fileNameElement.style.color = 'var(--muted-text)';
        }
    });

    // Drag and drop functionality
    const fileInputLabel = document.querySelector('.file-input-label');

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        fileInputLabel.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        fileInputLabel.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        fileInputLabel.addEventListener(eventName, unhighlight, false);
    });

    function highlight() {
        fileInputLabel.style.backgroundColor = '#e2e8f0';
        fileInputLabel.style.borderColor = 'var(--primary-color)';
    }

    function unhighlight() {
        fileInputLabel.style.backgroundColor = '#f8f9fa';
        fileInputLabel.style.borderColor = '#ced4da';
    }

    fileInputLabel.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        fileInput.files = files;
        fileInput.dispatchEvent(new Event('change'));
    }

    // Form submission
    document.getElementById('importForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const fileInput = document.getElementById('excelFile');
        const importType = document.getElementById('importType').value;
        const resultContainer = document.getElementById('resultContainer');

        // Validate file
        if (fileInput.files.length === 0) {
            showResult('error', 'Please select a file to import.');
            return;
        }

        // Validate file size (5MB max)
        if (fileInput.files[0].size > 5 * 1024 * 1024) {
            showResult('error', 'File size exceeds 5MB limit.');
            return;
        }

        // Determine API endpoint
        let apiUrl = '';
        switch (importType) {
            case 'routes':
                apiUrl = `${API_BASE_URL}ImportBusRoutes`;
                break;
            case 'stageTranslations':
                apiUrl = `${API_BASE_URL}ImportStageTranslations`;
                break;
            case 'stageCoordinates':
                apiUrl = `${API_BASE_URL}ImportStageCoordinates`;
                break;
            default:
                showResult('error', 'Invalid import type selected.');
                return;
        }

        // Show loading state
        const submitBtn = document.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...`;
        submitBtn.disabled = true;

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
                showResult('success', data || 'File imported successfully.');
            } else {
                showResult('error', data || 'Import failed. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showResult('error', `An error occurred: ${error.message}`);
        })
        .finally(() => {
            submitBtn.innerHTML = originalBtnText;
            submitBtn.disabled = false;
        });
    });

    // Clear form
    document.getElementById('clearBtn').addEventListener('click', function () {
        document.getElementById('importForm').reset();
        document.getElementById('resultContainer').innerHTML = '';
        fileNameElement.textContent = 'No file selected';
        fileNameElement.style.color = 'var(--muted-text)';
    });

    // Template download functionality
    document.getElementById('downloadTemplateBtn').addEventListener('click', function() {
        const importType = document.getElementById('importType').value;
        const templateFile = `${importType}_template.xlsx`;
        const templatePath = `templates/${templateFile}`;
        
        // Show loading state
        const originalBtnText = this.innerHTML;
        this.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Preparing...`;
        this.disabled = true;
        
        // Create a temporary anchor element to trigger download
        const a = document.createElement('a');
        a.href = templatePath;
        a.download = templateFile;
        document.body.appendChild(a);
        a.click();
        
        // Clean up
        setTimeout(() => {
            document.body.removeChild(a);
            this.innerHTML = originalBtnText;
            this.disabled = false;
            
            // Check if download was successful
            fetch(templatePath, { method: 'HEAD' })
                .then(response => {
                    if (!response.ok) {
                        showResult('error', 'Template file not found. Please contact support.');
                    }
                })
                .catch(() => {
                    showResult('error', 'Failed to download template. Please try again.');
                });
        }, 100);
    });

    // Helper function to show result messages
    function showResult(type, message) {
        const resultContainer = document.getElementById('resultContainer');
        const iconClass = type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-circle-fill';
        
        resultContainer.innerHTML = `
            <div class='result-message ${type}'>
                <i class="bi ${iconClass}"></i>
                <div>${message}</div>
            </div>
        `;
        
        // Scroll to result
        resultContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>