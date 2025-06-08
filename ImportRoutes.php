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
            --border-radius: 0.75rem;
            --box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.08);
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            --card-bg: #ffffff;
            --card-border: rgba(0, 0, 0, 0.05);
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: #f5f7fa;
            color: var(--dark-text);
            padding-top: 1rem;
            line-height: 1.6;
        }

        .form-container {
            max-width: 800px;
            margin: 2rem auto;
            background-color: var(--card-bg);
            padding: 2.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            transition: var(--transition);
            border: 1px solid var(--card-border);
        }

        @media (max-width: 768px) {
            .form-container {
                padding: 1.75rem;
                margin: 1rem;
            }
        }

        h2 {
            text-align: center;
            margin-bottom: 2rem;
            color: var(--primary-color);
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            font-size: 1.75rem;
        }

        .form-label {
            font-weight: 600;
            margin-bottom: 0.75rem;
            color: var(--dark-text);
            font-size: 0.95rem;
        }

        .form-control, .form-select {
            padding: 0.85rem 1.25rem;
            border-radius: var(--border-radius);
            border: 1px solid #e2e8f0;
            transition: var(--transition);
            font-size: 0.95rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.15);
        }

        .btn {
            padding: 0.85rem 1.75rem;
            border-radius: var(--border-radius);
            font-weight: 600;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            font-size: 0.95rem;
            letter-spacing: 0.02rem;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.2);
        }

        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-outline-primary:hover {
            background-color: rgba(67, 97, 238, 0.08);
            border-color: var(--primary-hover);
            color: var(--primary-hover);
        }

        .result-message {
            margin-top: 1.75rem;
            padding: 1.25rem;
            border-radius: var(--border-radius);
            font-weight: 500;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            line-height: 1.5;
        }

        .result-message.success {
            background-color: rgba(43, 147, 72, 0.08);
            color: var(--success-color);
            border-left: 4px solid var(--success-color);
        }

        .result-message.error {
            background-color: rgba(239, 35, 60, 0.08);
            color: var(--danger-color);
            border-left: 4px solid var(--danger-color);
        }

        .file-input-container {
            position: relative;
            overflow: hidden;
            border-radius: var(--border-radius);
        }

        .file-input-label {
            display: block;
            width: 100%;
            padding: 2rem 1rem;
            background-color: #f8fafc;
            border: 2px dashed #e2e8f0;
            border-radius: var(--border-radius);
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
        }

        .file-input-label:hover {
            background-color: #f1f5f9;
            border-color: var(--primary-color);
        }

        .file-input-label i {
            font-size: 2rem;
            color: var(--muted-text);
            margin-bottom: 0.75rem;
        }

        .file-name {
            margin-top: 0.75rem;
            font-size: 0.95rem;
            color: var(--muted-text);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-weight: 500;
        }

        .button-group {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 1.5rem;
        }

        @media (max-width: 576px) {
            .button-group {
                flex-direction: column;
            }
            
            .button-group .btn {
                width: 100%;
            }
        }

        .template-card {
            background-color: #f8fafc;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            border: 1px solid #e2e8f0;
            transition: var(--transition);
            margin-bottom: 1.5rem;
        }

        .template-card:hover {
            border-color: var(--primary-color);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .template-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .template-icon {
            width: 48px;
            height: 48px;
            background-color: rgba(67, 97, 238, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
            font-size: 1.5rem;
        }

        .template-title {
            font-weight: 600;
            margin-bottom: 0.25rem;
            color: var(--dark-text);
        }

        .template-description {
            color: var(--muted-text);
            font-size: 0.9rem;
        }

        .progress-container {
            margin-top: 1.5rem;
            display: none;
        }

        .progress-bar {
            height: 8px;
            border-radius: 4px;
            background-color: #e2e8f0;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            width: 0;
            background-color: var(--primary-color);
            transition: width 0.3s ease;
        }

        .progress-text {
            font-size: 0.85rem;
            color: var(--muted-text);
            margin-top: 0.5rem;
            text-align: right;
        }

        .import-steps {
            margin: 2rem 0;
            padding: 0;
            list-style: none;
            counter-reset: step-counter;
        }

        .import-step {
            position: relative;
            padding-left: 3rem;
            margin-bottom: 1.5rem;
            counter-increment: step-counter;
        }

        .import-step:before {
            content: counter(step-counter);
            position: absolute;
            left: 0;
            top: 0;
            width: 2rem;
            height: 2rem;
            background-color: var(--primary-color);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .import-step-title {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--dark-text);
        }

        .import-step-desc {
            color: var(--muted-text);
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<?php include 'AdminNavbar.php'; ?>

<div class="container">
    <div class="form-container">
        <h2><i class="bi bi-cloud-arrow-up-fill"></i> Import Bus Data</h2>

        <div class="mb-4">
            <ul class="import-steps">
                <li class="import-step">
                    <div class="import-step-title">Select Data Type</div>
                    <div class="import-step-desc">Choose what type of bus data you want to import</div>
                </li>
                <li class="import-step">
                    <div class="import-step-title">Upload Excel File</div>
                    <div class="import-step-desc">Use our template or your own properly formatted file</div>
                </li>
                <li class="import-step">
                    <div class="import-step-title">Review & Submit</div>
                    <div class="import-step-desc">Validate your data and complete the import process</div>
                </li>
            </ul>
        </div>

        <form id="importForm" enctype="multipart/form-data">
            <div class="mb-4">
                <label for="importType" class="form-label">Import Type</label>
                <select id="importType" name="importType" class="form-select" required>
                    <option value="" disabled selected>Select import type</option>
                    <option value="routes">Bus Routes</option>
                    <option value="stageCoordinates">Stage Coordinates</option>
                    <option value="stageTranslations">Stage Translations</option>
                </select>
                <div class="form-text mt-2">Select the type of data you want to import</div>
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
                <div class="form-text mt-2">Supported formats: .xlsx, .xls (Max size: 10MB)</div>
            </div>

            <!-- Progress bar -->
            <div class="progress-container" id="progressContainer">
                <div class="progress-bar">
                    <div class="progress-fill" id="progressFill"></div>
                </div>
                <div class="progress-text" id="progressText">0% uploaded</div>
            </div>

            <!-- Template download section -->
            <div class="template-card">
                <div class="template-header">
                    <div class="template-icon">
                        <i class="bi bi-file-earmark-spreadsheet"></i>
                    </div>
                    <div>
                        <div class="template-title">Download Template</div>
                        <div class="template-description">Get the properly formatted Excel template for your data type</div>
                    </div>
                </div>
                <button type="button" id="downloadTemplateBtn" class="btn btn-outline-primary">
                    <i class="bi bi-download"></i> Download Template
                </button>
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
    const progressContainer = document.getElementById('progressContainer');
    const progressFill = document.getElementById('progressFill');
    const progressText = document.getElementById('progressText');

    fileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            fileNameElement.textContent = this.files[0].name;
            fileNameElement.style.color = 'var(--dark-text)';
        } else {
            fileNameElement.textContent = 'No file selected';
            fileNameElement.style.color = 'var(--muted-text)';
        }
    });

    // Drag and drop functionality with improved UI
    const fileInputLabel = document.querySelector('.file-input-label');

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        fileInputLabel.addEventListener(eventName, preventDefaults, false);
        document.body.addEventListener(eventName, preventDefaults, false);
    });

    ['dragenter', 'dragover'].forEach(eventName => {
        fileInputLabel.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        fileInputLabel.addEventListener(eventName, unhighlight, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    function highlight() {
        fileInputLabel.style.backgroundColor = '#f1f5f9';
        fileInputLabel.style.borderColor = 'var(--primary-color)';
        fileInputLabel.style.boxShadow = '0 0 0 4px rgba(67, 97, 238, 0.1)';
    }

    function unhighlight() {
        fileInputLabel.style.backgroundColor = '#f8fafc';
        fileInputLabel.style.borderColor = '#e2e8f0';
        fileInputLabel.style.boxShadow = 'none';
    }

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        fileInput.files = files;
        fileInput.dispatchEvent(new Event('change'));
    }

    fileInputLabel.addEventListener('drop', handleDrop, false);

    // Form submission with progress tracking
    document.getElementById('importForm').addEventListener('submit', async function (e) {
        e.preventDefault();

        const fileInput = document.getElementById('excelFile');
        const importType = document.getElementById('importType').value;
        const resultContainer = document.getElementById('resultContainer');
        const submitBtn = document.querySelector('button[type="submit"]');

        // Validate form
        if (!importType) {
            showResult('error', 'Please select an import type.');
            return;
        }

        if (fileInput.files.length === 0) {
            showResult('error', 'Please select a file to import.');
            return;
        }

        if (fileInput.files[0].size > 10 * 1024 * 1024) {
            showResult('error', 'File size exceeds 10MB limit.');
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
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...`;
        submitBtn.disabled = true;
        progressContainer.style.display = 'block';

        const formData = new FormData();
        formData.append('file', fileInput.files[0]);

        try {
            const xhr = new XMLHttpRequest();
            
            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    const percentComplete = Math.round((e.loaded / e.total) * 100);
                    progressFill.style.width = percentComplete + '%';
                    progressText.textContent = `${percentComplete}% uploaded`;
                }
            });

            xhr.open('POST', apiUrl, true);
            
            xhr.onload = function() {
                if (xhr.status === 200) {
                    let response;
                    try {
                        response = JSON.parse(xhr.responseText);
                    } catch (e) {
                        response = xhr.responseText;
                    }
                    
                    if (typeof response === 'object' && response.message) {
                        showResult('success', response.message);
                    } else {
                        showResult('success', 'File imported successfully!');
                    }
                } else {
                    let errorResponse;
                    try {
                        errorResponse = JSON.parse(xhr.responseText);
                    } catch (e) {
                        errorResponse = xhr.responseText || 'Import failed. Please try again.';
                    }
                    
                    showResult('error', typeof errorResponse === 'object' ? errorResponse.message || errorResponse.error : errorResponse);
                }
            };
            
            xhr.onerror = function() {
                showResult('error', 'Network error occurred. Please check your connection.');
            };
            
            xhr.send(formData);
        } catch (error) {
            console.error('Error:', error);
            showResult('error', `An error occurred: ${error.message}`);
        } finally {
            submitBtn.innerHTML = originalBtnText;
            submitBtn.disabled = false;
            setTimeout(() => {
                progressContainer.style.display = 'none';
                progressFill.style.width = '0%';
                progressText.textContent = '0% uploaded';
            }, 1000);
        }
    });

    // Clear form
    document.getElementById('clearBtn').addEventListener('click', function () {
        document.getElementById('importForm').reset();
        document.getElementById('resultContainer').innerHTML = '';
        fileNameElement.textContent = 'No file selected';
        fileNameElement.style.color = 'var(--muted-text)';
        document.getElementById('importType').selectedIndex = 0;
    });

    // Template download functionality
    document.getElementById('downloadTemplateBtn').addEventListener('click', function() {
        const importType = document.getElementById('importType').value;
        
        if (!importType) {
            showResult('error', 'Please select an import type first.');
            return;
        }
        
        const templateFile = `${importType}_template.xlsx`;
        const templatePath = `templates/${templateFile}`;
        
        // Show loading state
        const originalBtnText = this.innerHTML;
        this.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Preparing...`;
        this.disabled = true;
        
        // Check if template exists first
        fetch(templatePath, { method: 'HEAD' })
            .then(response => {
                if (response.ok) {
                    // Create a temporary anchor element to trigger download
                    const a = document.createElement('a');
                    a.href = templatePath;
                    a.download = templateFile;
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    
                    showResult('success', 'Template download started. Check your downloads folder.');
                } else {
                    showResult('error', 'Template file not found. Please contact support.');
                }
            })
            .catch(() => {
                showResult('error', 'Failed to download template. Please try again.');
            })
            .finally(() => {
                this.innerHTML = originalBtnText;
                this.disabled = false;
            });
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
        
        // Scroll to result smoothly
        setTimeout(() => {
            resultContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }, 100);
    }

    // Add animation to form container on load
    document.addEventListener('DOMContentLoaded', () => {
        const formContainer = document.querySelector('.form-container');
        formContainer.style.opacity = '0';
        formContainer.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            formContainer.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            formContainer.style.opacity = '1';
            formContainer.style.transform = 'translateY(0)';
        }, 100);
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>