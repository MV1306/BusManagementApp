<?php
    require_once 'AdminAuth.php';
    checkAuth();
    $config = include('config.php');
    $apiBaseUrl = $config['api_base_url'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Redeem Bus Ticket</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --accent: #4cc9f0;
            --light: #f8f9fa;
            --dark: #212529;
            --success: #4bb543;
            --warning: #f8961e;
            --danger: #f72585;
            --card-bg: rgba(255, 255, 255, 0.95);
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body { 
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif; 
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            color: var(--dark);
            line-height: 1.6;
            padding: 0;
            margin: 0;
        }
        
        .container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .card {
            background: var(--card-bg);
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 2rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 1.5rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .card-header h2 {
            font-weight: 700;
            font-size: 1.8rem;
            margin: 0;
            position: relative;
            z-index: 1;
        }
        
        .card-header::before {
            content: "";
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
            transform: rotate(30deg);
        }
        
        .card-body {
            padding: 2rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--dark);
            font-size: 0.95rem;
        }
        
        .form-control {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background-color: white;
            color: var(--dark);
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(76, 201, 240, 0.2);
        }
        
        .btn {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            width: 100%;
            padding: 1rem;
            font-size: 1.1rem;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.4);
        }
        
        .btn-primary:disabled {
            background: #adb5bd;
            transform: none;
            box-shadow: none;
            cursor: not-allowed;
        }
        
        .btn-secondary {
            background: var(--light);
            color: var(--dark);
            border: 1px solid #dee2e6;
        }
        
        .btn-secondary:hover {
            background: #e9ecef;
        }

        .icon-input {
            position: relative;
        }
        
        .icon-input i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary);
        }
        
        .icon-input .form-control {
            padding-left: 3rem;
        }

        .message-box {
            background: rgba(var(--primary-rgb, 67, 97, 238), 0.1);
            border-left: 5px solid var(--primary);
            padding: 1.5rem;
            border-radius: 10px;
            margin-top: 1.5rem;
            display: none;
            animation: fadeIn 0.5s ease;
        }

        .message-box.success {
            border-color: var(--success);
            background: rgba(75, 181, 67, 0.1);
        }

        .message-box.error {
            border-color: var(--danger);
            background: rgba(247, 37, 133, 0.1);
        }

        .message-box h3 {
            color: var(--primary);
            margin-bottom: 0.5rem;
        }
        .message-box.success h3 { color: var(--success); }
        .message-box.error h3 { color: var(--danger); }

        .message-box p {
            margin-bottom: 0.5rem;
        }

        .message-box .detail {
            font-weight: 600;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* QR Scanner Styles */
        .qr-scanner-container {
            margin-bottom: 1.5rem;
            display: none;
        }
        
        #qr-reader {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
            border: 2px solid var(--primary);
            border-radius: 10px;
            overflow: hidden;
        }
        
        #qr-reader-results {
            text-align: center;
            margin-top: 0.5rem;
            font-size: 0.9rem;
            color: var(--dark);
        }
        
        .toggle-method {
            text-align: center;
            margin: 1rem 0;
        }
        
        .toggle-method-btn {
            background: none;
            border: none;
            color: var(--primary);
            text-decoration: underline;
            cursor: pointer;
            font-size: 0.9rem;
        }
        
        .toggle-method-btn:hover {
            color: var(--secondary);
        }
        
        .error-message {
            color: var(--danger);
            font-size: 0.8rem;
            margin-top: 0.3rem;
            display: none;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .container {
                padding: 0 0.5rem;
            }
            .card-header h2 {
                font-size: 1.5rem;
            }
            .card-body {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>

<?php include 'AdminNavbar.php'; ?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-ticket-alt"></i> Redeem Your Bus Ticket</h2>
        </div>
        <div class="card-body">
            <div class="toggle-method">
                <button id="toggleMethodBtn" class="toggle-method-btn">
                    <i class="fas fa-qrcode"></i> Scan QR Code Instead
                </button>
            </div>
            
            <!-- Manual Input Section -->
            <div id="manualInputSection">
                <div class="form-group">
                    <label for="bookingId">Booking ID</label>
                    <div class="icon-input">
                        <i class="fas fa-barcode"></i>
                        <input type="text" id="bookingId" class="form-control" placeholder="Enter your Booking ID" required>
                    </div>
                    <div id="bookingIdError" class="error-message">Please enter a valid booking ID.</div>
                </div>
            </div>
            
            <!-- QR Scanner Section -->
            <div id="qrScannerSection" class="qr-scanner-container">
                <div id="qr-reader"></div>
                <div id="qr-reader-results"></div>
            </div>
            
            <button id="redeemBtn" class="btn btn-primary">
                <i class="fas fa-check-circle"></i> Redeem Ticket
            </button>
            
            <div id="responseMessage" class="message-box" style="display: none;"></div>
        </div>
    </div>
</div>

<script>
    const apiBase = "<?php echo $apiBaseUrl; ?>";  
    let html5QrCode;
    let isScannerActive = false;

    // Function to validate booking ID
    function validateBookingId(bookingId) {
        return bookingId.length === 10;
    }


    // Toggle between manual input and QR scanner
    $('#toggleMethodBtn').on('click', function() {
        const $manualSection = $('#manualInputSection');
        const $qrSection = $('#qrScannerSection');
        
        if ($manualSection.is(':visible')) {
            // Switch to QR scanner
            $manualSection.hide();
            $qrSection.show();
            $(this).html('<i class="fas fa-keyboard"></i> Enter Manually Instead');
            
            // Initialize and start QR scanner
            if (!html5QrCode) {
                html5QrCode = new Html5Qrcode("qr-reader");
            }
            
            const qrScannerConfig = { 
                fps: 10,
                qrbox: { width: 250, height: 250 }
            };
            
            html5QrCode.start(
                { facingMode: "environment" },
                qrScannerConfig,
                onQrScanSuccess,
                onQrScanError
            ).then(() => {
                isScannerActive = true;
            }).catch(err => {
                console.error("Unable to start QR scanner:", err);
                $('#qr-reader-results').text("Unable to access camera. Please check permissions.");
            });
        } else {
            // Switch to manual input
            $manualSection.show();
            $qrSection.hide();
            $(this).html('<i class="fas fa-qrcode"></i> Scan QR Code Instead');
            
            // Stop QR scanner if active
            if (html5QrCode && isScannerActive) {
                html5QrCode.stop().then(() => {
                    isScannerActive = false;
                }).catch(err => {
                    console.error("Unable to stop QR scanner:", err);
                });
            }
        }
    });

    // Handle successful QR scan
    function onQrScanSuccess(decodedText, decodedResult) {
        // Validate the scanned data
        if (validateBookingId(decodedText)) {
            $('#bookingId').val(decodedText);
            $('#qr-reader-results').html('<i class="fas fa-check-circle" style="color: var(--success);"></i> Valid QR code scanned');
            
            // Automatically attempt redemption after 1 second
            setTimeout(() => {
                $('#redeemBtn').click();
            }, 1000);
        } else {
            $('#qr-reader-results').html('<i class="fas fa-exclamation-circle" style="color: var(--danger);"></i> Invalid QR code format');
        }
    }

    // Handle QR scan errors
    function onQrScanError(errorMessage) {
        console.log("QR Scan Error:", errorMessage);
    }

    // Redeem ticket function
    $('#redeemBtn').on('click', function() {
        const bookingId = $('#bookingId').val().trim();
        const $responseMessage = $('#responseMessage');
        const $bookingIdError = $('#bookingIdError');

        // Reset previous messages
        $responseMessage.hide().removeClass('success error').html('');
        $bookingIdError.hide();

        if (!bookingId) {
            $bookingIdError.text("Booking ID cannot be empty.").show();
            return;
        }

        if (!validateBookingId(bookingId)) {
            $bookingIdError.text("Please enter a valid booking ID format.").show();
            return;
        }

        // Show loading state
        $(this).html('<i class="fas fa-spinner fa-spin"></i> Processing...').prop('disabled', true);

        // API call to redeem the ticket
        $.ajax({
            url: `${apiBase}RedeemTicket/${bookingId}`,
            method: "PUT",
            contentType: "application/json",
            success: function(response) {
                let statusMsg = '';
                let messageClass = 'success';
                let iconClass = 'fas fa-check-circle';

                if (response && response.message) {
                    statusMsg = response.message;
                } else {
                    statusMsg = "Ticket redeemed successfully!";
                }

                $responseMessage.addClass(messageClass).html(`
                    <h3><i class="${iconClass}"></i> ${statusMsg}</h3>
                    <p>Booking ID: <span class="detail">${bookingId}</span></p>
                    <p>This ticket is now marked as used.</p>
                `).show();
                
                // Clear the input field after successful redemption
                $('#bookingId').val('');
                
                // If scanner is active, stop it after successful redemption
                if (html5QrCode && isScannerActive) {
                    html5QrCode.stop().then(() => {
                        isScannerActive = false;
                    });
                }
            },
            error: function(xhr) {
                let errorMsg = "Failed to redeem ticket. Please try again.";
                let iconClass = 'fas fa-exclamation-circle';
                let messageClass = 'error';

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    try {
                        const errorObj = JSON.parse(xhr.responseText);
                        if (errorObj.message) errorMsg = errorObj.message;
                    } catch (e) {
                        errorMsg = xhr.responseText;
                    }
                }
                
                $responseMessage.addClass(messageClass).html(`
                    <h3><i class="${iconClass}"></i> Redemption Failed!</h3>
                    <p>${errorMsg}</p>
                `).show();
            },
            complete: function() {
                // Reset button state
                $('#redeemBtn').html('<i class="fas fa-check-circle"></i> Redeem Ticket').prop('disabled', false);
                // Scroll to the message
                $('html, body').animate({
                    scrollTop: $responseMessage.offset().top - 100
                }, 500);
            }
        });
    });

    // Clean up scanner when page is unloaded
    $(window).on('beforeunload', function() {
        if (html5QrCode && isScannerActive) {
            html5QrCode.stop().then(() => {
                isScannerActive = false;
            });
        }
    });
</script>

</body>
</html>