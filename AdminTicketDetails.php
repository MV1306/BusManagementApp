<?php
    require_once 'AdminAuth.php';
    checkAuth();
    $config = include('config.php');
    $apiBaseUrl = $config['api_base_url'];

    $ticketId = $_GET['id'] ?? ''; // Get the ticket ID from the URL
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Details - Bus Ticket Booking</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #4361ee;
            --primary-light: #4cc9f0;
            --secondary: #3f37c9;
            --accent: #4cc9f0;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --light-gray: #e9ecef;
            
            /* Consistent Category Colors with SearchTickets.php */
            --active-color: #28a745; /* Green */
            --active-color-dark: #228b22;
            --redeemed-color: #6f42c1; /* Muted Purple */
            --redeemed-color-dark: #5b369c;
            --cancelled-color: #dc3545; /* Red */
            --cancelled-color-dark: #b02a37;

            --card-bg: rgba(255, 255, 255, 0.98);
            --ticket-bg: linear-gradient(135deg, rgba(76, 201, 240, 0.03) 0%, rgba(67, 97, 238, 0.03) 100%);
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
            padding-bottom: 2rem;
        }
        
        .container {
            max-width: 900px;
            margin: 2rem auto;
            padding: 0 1.5rem;
        }
        
        .card {
            background: var(--card-bg);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            margin-bottom: 2rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 1.5rem;
            text-align: center;
            position: relative;
            overflow: hidden;
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
        
        .card-header h2 {
            font-weight: 700;
            font-size: 1.8rem;
            margin: 0;
            position: relative;
        }
        
        .card-body {
            padding: 2rem;
        }
        
        /* Dynamic ticket card styling based on status */
        .ticket-card { 
            background: var(--ticket-bg);
            border-radius: 14px;
            padding: 0;
            margin-bottom: 1.5rem;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.03);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .ticket-card .ticket-header { /* Base style for all ticket headers */
            color: white;
            padding: 1.2rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            /* Default background, will be overridden by specific status classes */
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%); 
        }

        .ticket-card.active .ticket-header {
            background: linear-gradient(135deg, var(--active-color) 0%, var(--active-color-dark) 100%);
        }
        .ticket-card.redeemed .ticket-header {
            background: linear-gradient(135deg, var(--redeemed-color) 0%, var(--redeemed-color-dark) 100%);
        }
        .ticket-card.cancelled .ticket-header {
            background: linear-gradient(135deg, var(--cancelled-color) 0%, var(--cancelled-color-dark) 100%);
        }
        
        .ticket-header h3 {
            margin: 0;
            font-size: 1.2rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .ticket-header .badge {
            background-color: rgba(255, 255, 255, 0.2);
            padding: 0.3rem 0.8rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .ticket-details-grid { 
            padding: 1.5rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); 
            gap: 1.5rem;
        }
        
        .detail-group {
            margin-bottom: 0; 
        }
        
        .detail-label {
            font-size: 0.85rem;
            color: var(--gray);
            margin-bottom: 0.2rem;
            display: block;
            font-weight: 500;
        }
        
        .detail-value {
            font-size: 1rem;
            font-weight: 600;
            color: var(--dark);
        }
        
        .detail-value.primary {
            color: var(--primary);
        }
        
        .detail-value.large {
            font-size: 1.2rem;
        }
        
        .ticket-footer {
            background-color: rgba(0, 0, 0, 0.02);
            padding: 1rem 1.5rem;
            border-top: 1px dashed var(--light-gray);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap; 
        }
        
        .ticket-status {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            /* Default color, will be overridden */
            color: var(--dark); 
        }
        
        .ticket-status .status-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            /* Default color, will be overridden */
            background-color: var(--gray); 
        }
        
        /* Status dot colors based on classes applied to .ticket-status */
        .ticket-status.active {
            color: var(--active-color);
        }
        .ticket-status.active .status-dot {
            background-color: var(--active-color);
        }
        
        .ticket-status.redeemed {
            color: var(--redeemed-color);
        }
        .ticket-status.redeemed .status-dot {
            background-color: var(--redeemed-color);
        }

        .ticket-status.cancelled {
            color: var(--cancelled-color);
        }
        .ticket-status.cancelled .status-dot {
            background-color: var(--cancelled-color);
        }
        
        .ticket-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap; 
        }
        
        .ticket-btn {
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .ticket-btn.print {
            background-color: var(--primary);
            color: white;
        }
        
        .ticket-btn.cancel {
            background-color: var(--cancelled-color); /* Use cancelled-color for consistency */
            color: white; /* White text for better contrast on red */
        }

        .ticket-btn.cancel:hover {
            background-color: var(--cancelled-color-dark); /* Darker red on hover */
        }
        
        .ticket-btn.back {
            background-color: var(--gray);
            color: white;
        }
        
        .ticket-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }
        
        /* Info message styles */
        .info-message {
            text-align: center;
            padding: 2.5rem;
            background-color: #f8f9fa;
            border-radius: 12px;
            border: 2px dashed var(--light-gray);
        }
        
        .info-message i {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 1.5rem;
            opacity: 0.8;
        }
        
        .info-message p {
            font-size: 1.1rem;
            color: var(--gray);
            margin-bottom: 0.5rem;
        }
        
        .info-message.error {
            border-color: var(--cancelled-color); /* Use consistent cancelled color for errors */
            background-color: rgba(220, 53, 69, 0.03); /* Red background tint */
        }

        .info-message.error i {
            color: var(--cancelled-color); /* Red icon for errors */
        }
        
        /* Loading spinner */
        .spinner {
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .container {
                padding: 0 1rem;
            }
            
            .card-header h2 {
                font-size: 1.5rem;
            }
            
            .card-body {
                padding: 1.5rem;
            }
            
            .ticket-details-grid {
                grid-template-columns: 1fr;
            }
            
            .ticket-footer {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }
            
            .ticket-actions {
                width: 100%;
                justify-content: flex-start; 
            }
            
            .ticket-btn {
                flex: 1;
                padding: 0.6rem;
            }
        }

        @media (max-width: 480px) {
            .card-header h2 {
                font-size: 1.3rem;
            }
            
            .ticket-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
            
            .ticket-header h3 {
                font-size: 1.1rem;
            }
        }
    </style>
</head>
<body>

<?php include 'AdminNavbar.php'; ?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-ticket-alt"></i> Ticket Details</h2>
        </div>
        <div class="card-body" id="ticketDetailsContent">
            <?php if (empty($ticketId)): ?>
                <div class="info-message error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>No ticket ID provided.</p>
                    <small class="text-muted">Please go back to the <a href="SearchTickets.php">View Tickets</a> page and select a ticket.</small>
                </div>
            <?php else: ?>
                <div class="info-message">
                    <i class="fas fa-spinner spinner"></i>
                    <p>Loading ticket details...</p>
                </div>
            <?php endif; ?>
        </div>
        <div class="card-footer" style="text-align: center; padding-bottom: 2rem;">
            <button class="ticket-btn back" onclick="window.location.href='SearchTickets.php'">
                <i class="fas fa-arrow-left"></i> Back to Search Tickets
            </button>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        const apiBase = "<?php echo rtrim($apiBaseUrl, '/'); ?>";
        const ticketId = "<?php echo $ticketId; ?>"; 
        const ticketDetailsContent = $('#ticketDetailsContent');

        // Function to fetch and display ticket details
        function fetchTicketDetails() {
            if (!ticketId) {
                // If ticketId is not provided from PHP, the initial error message is already shown.
                return;
            }

            $.ajax({
                url: `${apiBase}/GetTicketById/${ticketId}`, 
                method: 'GET',
                success: function(ticket) {
                    if (ticket) {
                        const bookingDate = new Date(ticket.bookingDate);
                        const formattedDate = bookingDate.toLocaleDateString('en-IN', {
                            year: 'numeric', month: 'long', day: 'numeric'
                        });
                        const formattedTime = bookingDate.toLocaleTimeString('en-IN', {
                            hour: '2-digit', minute: '2-digit'
                        });
                        
                        // Determine status based on API flags, giving precedence to cancelled
                        let statusClass;
                        let statusText;

                        if (ticket.isCancelled) {
                            statusClass = 'cancelled';
                            statusText = 'Cancelled';
                        } else if (ticket.isRedeemed) {
                            statusClass = 'redeemed';
                            statusText = 'Used';
                        } else {
                            statusClass = 'active';
                            statusText = 'Active';
                        }

                        // Debugging log
                        console.log(`Ticket ID: ${ticket.ticketID}, isRedeemed: ${ticket.isRedeemed}, isCancelled: ${ticket.isCancelled}, Final Status Class: ${statusClass}`);

                        const ticketHtml = `
                            <div class="ticket-card ${statusClass}">
                                <div class="ticket-header">
                                    <h3>
                                        <i class="fas fa-bus"></i> ${ticket.routeCode || 'N/A'} • ${ticket.busType || 'N/A'}
                                    </h3>
                                    <span class="badge"><strong>Ref.ID: ${ticket.bookingRefId || 'N/A'}</strong></span>
                                </div>
                                
                                <div class="ticket-details-grid">
                                    <div class="detail-group">
                                        <span class="detail-label">Passenger Name</span>
                                        <span class="detail-value">${ticket.userName || 'N/A'}</span>
                                    </div>
                                    
                                    <div class="detail-group">
                                        <span class="detail-label">From</span>
                                        <span class="detail-value">${ticket.fromStage || 'N/A'}</span>
                                    </div>
                                    
                                    <div class="detail-group">
                                        <span class="detail-label">To</span>
                                        <span class="detail-value">${ticket.toStage || 'N/A'}</span>
                                    </div>
                                    
                                    <div class="detail-group">
                                        <span class="detail-label">Booking Date & Time</span>
                                        <span class="detail-value">${formattedDate} at ${formattedTime}</span>
                                    </div>
                                    
                                    <div class="detail-group">
                                        <span class="detail-label">Passengers</span>
                                        <span class="detail-value">${ticket.passengers || 'N/A'}</span>
                                    </div>
                                    
                                    <div class="detail-group">
                                        <span class="detail-label">Fare per Passenger</span>
                                        <span class="detail-value">₹${(ticket.fare || 0).toFixed(2)}</span>
                                    </div>
                                    
                                    <div class="detail-group">
                                        <span class="detail-label">Total Fare</span>
                                        <span class="detail-value primary large">₹${(ticket.totalFare || 0).toFixed(2)}</span>
                                    </div>
                                    
                                    <div class="detail-group">
                                        <span class="detail-label">Contact Mobile</span>
                                        <span class="detail-value">${ticket.mobileNo || 'N/A'}</span>
                                    </div>
                                    
                                    <div class="detail-group">
                                        <span class="detail-label">Contact Email</span>
                                        <span class="detail-value">${ticket.email || 'N/A'}</span>
                                    </div>
                                </div>
                                
                                <div class="ticket-footer">
                                    <div class="ticket-status ${statusClass}">
                                        <span class="status-dot"></span>
                                        <span>${statusText}</span>
                                    </div>
                                    
                                    <div class="ticket-actions">
                                        <button class="ticket-btn print" id="printTicketBtn">
                                            <i class="fas fa-print"></i> Print
                                        </button>
                                        
                                        ${!ticket.isRedeemed && !ticket.isCancelled ? `
                                        <button class="ticket-btn cancel" id="cancelTicketBtn" data-ref-id="${ticket.bookingRefId}">
                                            <i class="fas fa-times"></i> Cancel Ticket
                                        </button>
                                        ` : ''}
                                    </div>
                                </div>
                            </div>
                        `;
                        ticketDetailsContent.html(ticketHtml);

                        // Attach event listener for the cancel button
                        if (!ticket.isRedeemed && !ticket.isCancelled) {
                            $('#cancelTicketBtn').on('click', function() {
                                const bookingRefId = $(this).data('ref-id');
                                if (confirm(`Are you sure you want to cancel ticket ${bookingRefId}? This action cannot be undone.`)) {
                                    cancelTicket(bookingRefId);
                                }
                            });
                        }
                        
                        // Attach event listener for the print button (if needed, this can open a print-friendly view)
                        $('#printTicketBtn').on('click', function() {
                            // Example: open a new window with a simplified print view
                            const printWindow = window.open('', '_blank');
                            printWindow.document.write('<html><head><title>Print Ticket</title>');
                            // Include minimal CSS for printing
                            printWindow.document.write('<style>body{font-family: Arial, sans-serif; margin: 20px;} .ticket-print-area{border: 1px solid #ccc; padding: 20px; max-width: 600px; margin: 0 auto;} h1{color: #333;} p{margin: 5px 0;} strong{font-weight: bold;} table{width: 100%; border-collapse: collapse; margin-top: 20px;} th, td{border: 1px solid #eee; padding: 8px; text-align: left;}.status{font-weight: bold; padding: 5px 10px; border-radius: 5px; display: inline-block;}.active-status{background-color: #d4edda; color: #155724;}.redeemed-status{background-color: #e2e3e5; color: #495057;}.cancelled-status{background-color: #f8d7da; color: #721c24;}</style>');
                            printWindow.document.write('</head><body>');
                            printWindow.document.write('<div class="ticket-print-area">');
                            printWindow.document.write(`<h1>Bus Ticket Details</h1>`);
                            printWindow.document.write(`<p><strong>Ref. ID:</strong> ${ticket.bookingRefId || 'N/A'}</p>`);
                            printWindow.document.write(`<p><strong>Route:</strong> ${ticket.routeCode || 'N/A'} - ${ticket.busType || 'N/A'}</p>`);
                            printWindow.document.write(`<p><strong>Passenger:</strong> ${ticket.userName || 'N/A'}</p>`);
                            printWindow.document.write(`<p><strong>From:</strong> ${ticket.fromStage || 'N/A'}</p>`);
                            printWindow.document.write(`<p><strong>To:</strong> ${ticket.toStage || 'N/A'}</p>`);
                            printWindow.document.write(`<p><strong>Date & Time:</strong> ${formattedDate} at ${formattedTime}</p>`);
                            printWindow.document.write(`<p><strong>Passengers:</strong> ${ticket.passengers || 'N/A'}</p>`);
                            printWindow.document.write(`<p><strong>Fare per Passenger:</strong> ₹${(ticket.fare || 0).toFixed(2)}</p>`);
                            printWindow.document.write(`<p><strong>Total Fare:</strong> ₹${(ticket.totalFare || 0).toFixed(2)}</p>`);
                            printWindow.document.write(`<p><strong>Contact Mobile:</strong> ${ticket.mobileNo || 'N/A'}</p>`);
                            printWindow.document.write(`<p><strong>Contact Email:</strong> ${ticket.email || 'N/A'}</p>`);
                            printWindow.document.write(`<p><strong>Status:</strong> <span class="status ${statusClass}-status">${statusText}</span></p>`);
                            printWindow.document.write('</div>');
                            printWindow.document.write('</body></html>');
                            printWindow.document.close();
                            printWindow.print();
                        });


                    } else {
                        ticketDetailsContent.html(`
                            <div class="info-message error">
                                <i class="fas fa-exclamation-circle"></i>
                                <p>Ticket not found.</p>
                                <small class="text-muted">The ticket ID may be incorrect or the ticket does not exist.</small>
                            </div>`);
                    }
                },
                error: function(xhr) {
                    console.error("API Error:", xhr);
                    ticketDetailsContent.html(`
                        <div class="info-message error">
                            <i class="fas fa-exclamation-circle"></i>
                            <p>Error loading ticket details.</p>
                            <small class="text-muted">Please try again later or contact support. (Status: ${xhr.status})</small>
                        </div>`);
                }
            });
        }

        // Function to handle ticket cancellation
        function cancelTicket(bookingRefId) {
            // Show loading or confirmation UI
            if (!confirm("Are you sure you want to cancel this ticket? This action cannot be undone.")) {
                return; // User cancelled
            }

            // Display a temporary loading message or spinner
            const originalContent = ticketDetailsContent.html();
            ticketDetailsContent.html(`
                <div class="info-message">
                    <i class="fas fa-spinner spinner"></i>
                    <p>Attempting to cancel ticket ${bookingRefId}...</p>
                    <small>Please wait, this may take a moment.</small>
                </div>
            `);

            $.ajax({
                url: `${apiBase}/CancelTicket/${bookingRefId}`,
                method: 'PUT', // Assuming POST for state-changing operations
                success: function(response) {
                    if (response.success) { // Assuming API returns { success: true, message: "Ticket cancelled successfully" }
                        ticketDetailsContent.html(`
                            <div class="info-message">
                                <i class="fas fa-check-circle" style="color: var(--active-color);"></i>
                                <p>Ticket ${bookingRefId} has been successfully cancelled!</p>
                                <small>The details will now reflect the cancelled status.</small>
                            </div>
                        `);
                        // Re-fetch ticket details to reflect the updated status
                        setTimeout(fetchTicketDetails, 2000); // Fetch after 2 seconds to show message
                    } else {
                        // Assuming API returns { success: false, message: "Reason for failure" }
                        ticketDetailsContent.html(`
                            <div class="info-message error">
                                <i class="fas fa-exclamation-triangle"></i>
                                <p>Failed to cancel ticket ${bookingRefId}.</p>
                                <small>${response.message || 'An unknown error occurred.'}</small>
                            </div>
                        `);
                        setTimeout(() => { ticketDetailsContent.html(originalContent); }, 3000); // Restore original after delay
                    }
                },
                error: function(xhr) {
                    console.error("Cancellation API Error:", xhr);
                    ticketDetailsContent.html(`
                        <div class="info-message error">
                            <i class="fas fa-exclamation-circle"></i>
                            <p>Error during cancellation attempt for ${bookingRefId}.</p>
                            <small>Server responded with status: ${xhr.status}. Please try again.</small>
                        </div>
                    `);
                    setTimeout(() => { ticketDetailsContent.html(originalContent); }, 3000); // Restore original after delay
                }
            });
        }

        // Initial fetch when page loads
        fetchTicketDetails();
    });
</script>

</body>
</html>