<?php
    if (session_status() === PHP_SESSION_NONE) session_start();
    $config = include('config.php');
    $apiBaseUrl = $config['api_base_url'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Redeem Bus Ticket</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Reusing your existing CSS variables and general styles */
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
            display: none; /* Hidden by default */
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

<?php include 'navbar.php'; // Assuming you have a navbar.php file for consistent navigation ?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-ticket-alt"></i> Redeem Your Bus Ticket</h2>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label for="bookingId">Booking ID</label>
                <div class="icon-input">
                    <i class="fas fa-barcode"></i>
                    <input type="text" id="bookingId" class="form-control" placeholder="Enter your 10-digit Booking ID" required>
                </div>
                <div id="bookingIdError" class="error-message">Please enter a valid 10-digit booking ID.</div>
            </div>
            
            <button id="redeemBtn" class="btn btn-primary">
                <i class="fas fa-check-circle"></i> Redeem Ticket
            </button>
            
            <div id="responseMessage" class="message-box" style="display: none;">
                </div>
        </div>
    </div>
</div>

<script>
    const apiBase = "<?php echo $apiBaseUrl; ?>"; 

    // Function to validate booking ID (example: 10 digits/characters, adjust as per your actual ID format)
    function validateBookingId(bookingId) {
        // Assuming your booking ID is a 10-character alphanumeric string,
        // or a specific UUID format if that's what your API returns.
        // For the UUID you are returning in BuyTicket success message, it's 36 characters including hyphens.
        // Adjust this regex based on your actual booking ID format.
        return /^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/.test(bookingId);
    }

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
            url: `${apiBase}RedeemTicket/${bookingId}`, // Example API endpoint for redemption
            method: "POST", // Typically redemption would be a POST or PUT
            contentType: "application/json", // If your API expects JSON even for simple POSTs
            success: function(response) {
                let statusMsg = '';
                let messageClass = 'success';
                let iconClass = 'fas fa-check-circle';

                // Assuming your API returns a clear success message or object
                // You might need to adjust this based on the actual API response
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
                
                // Optionally clear the input field after successful redemption
                $('#bookingId').val('');
            },
            error: function(xhr) {
                let errorMsg = "Failed to redeem ticket. Please try again.";
                let iconClass = 'fas fa-exclamation-circle';
                let messageClass = 'error';

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    // Try to parse if responseText is JSON but not set as application/json
                    try {
                        const errorObj = JSON.parse(xhr.responseText);
                        if (errorObj.message) errorMsg = errorObj.message;
                    } catch (e) {
                        // Fallback to plain text if parsing fails
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
</script>

</body>
</html>