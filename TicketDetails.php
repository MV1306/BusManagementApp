<?php
    if (session_status() === PHP_SESSION_NONE) session_start();
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
            --success: #4bb543;
            --warning: #f8961e;
            --danger: #f72585;
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
        
        .ticket-card { /* Using similar styling for the single ticket view */
            background: var(--ticket-bg);
            border-radius: 14px;
            padding: 0;
            margin-bottom: 1.5rem;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.03);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .ticket-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 1.2rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
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
        
        .ticket-details-grid { /* Renamed for clarity in this file */
            padding: 1.5rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); /* Adjusted for more details */
            gap: 1.5rem;
        }
        
        .detail-group {
            margin-bottom: 0; /* No margin-bottom if using grid gap */
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
            flex-wrap: wrap; /* Allow wrapping on smaller screens */
        }
        
        .ticket-status {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .ticket-status .status-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: var(--success);
        }
        
        .ticket-status.active .status-dot {
            background-color: var(--success);
        }
        
        .ticket-status.redeemed .status-dot {
            background-color: var(--gray);
        }
        
        .ticket-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap; /* Allow buttons to wrap */
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
            background-color: var(--light-gray);
            color: var(--dark);
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
            border-color: var(--danger);
            background-color: rgba(247, 37, 133, 0.03);
        }

        .info-message.error i {
            color: var(--danger);
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
                justify-content: flex-start; /* Align buttons to start */
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

<?php include 'navbar.php'; ?>

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
        const ticketId = "<?php echo $ticketId; ?>"; // Now correctly named ticketId
        const ticketDetailsContent = $('#ticketDetailsContent');

        if (ticketId) {
            $.ajax({
                url: `${apiBase}/GetTicketById/${ticketId}`, // Assumed API endpoint to fetch by TicketId
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
                        
                        const statusClass = ticket.isRedeemed ? 'redeemed' : 'active';
                        const statusText = ticket.isRedeemed ? 'Used' : 'Active';

                        const ticketHtml = `
                            <div class="ticket-card">
                                <div class="ticket-header">
                                    <h3>
                                        <i class="fas fa-bus"></i> ${ticket.routeCode} • ${ticket.busType}
                                    </h3>
                                    <span class="badge"><strong>ID: ${ticket.ticketId}</strong> | Ref: ${ticket.bookingRefId}</span>
                                </div>
                                
                                <div class="ticket-details-grid">
                                    <div class="detail-group">
                                        <span class="detail-label">Passenger Name</span>
                                        <span class="detail-value">${ticket.userName}</span>
                                    </div>
                                    
                                    <div class="detail-group">
                                        <span class="detail-label">From</span>
                                        <span class="detail-value">${ticket.fromStage}</span>
                                    </div>
                                    
                                    <div class="detail-group">
                                        <span class="detail-label">To</span>
                                        <span class="detail-value">${ticket.toStage}</span>
                                    </div>
                                    
                                    <div class="detail-group">
                                        <span class="detail-label">Booking Date & Time</span>
                                        <span class="detail-value">${formattedDate} at ${formattedTime}</span>
                                    </div>
                                    
                                    <div class="detail-group">
                                        <span class="detail-label">Passengers</span>
                                        <span class="detail-value">${ticket.passengers}</span>
                                    </div>
                                    
                                    <div class="detail-group">
                                        <span class="detail-label">Fare per Passenger</span>
                                        <span class="detail-value">₹${ticket.fare.toFixed(2)}</span>
                                    </div>
                                    
                                    <div class="detail-group">
                                        <span class="detail-label">Total Fare</span>
                                        <span class="detail-value primary large">₹${ticket.totalFare.toFixed(2)}</span>
                                    </div>
                                    
                                    <div class="detail-group">
                                        <span class="detail-label">Contact Mobile</span>
                                        <span class="detail-value">${ticket.mobileNo}</span>
                                    </div>
                                    
                                    <div class="detail-group">
                                        <span class="detail-label">Contact Email</span>
                                        <span class="detail-value">${ticket.email || 'N/A'}</span>
                                    </div>
                                    
                                    <div class="detail-group">
                                        <span class="detail-label">Seat Number(s)</span>
                                        <span class="detail-value">${ticket.seatNumbers || 'N/A'}</span>
                                    </div>
                                </div>
                                
                                <div class="ticket-footer">
                                    <div class="ticket-status ${statusClass}">
                                        <span class="status-dot"></span>
                                        <span>${statusText}</span>
                                    </div>
                                    
                                    <div class="ticket-actions">
                                        <button class="ticket-btn print">
                                            <i class="fas fa-print"></i> Print
                                        </button>
                                        ${!ticket.isRedeemed ? `
                                        <button class="ticket-btn cancel">
                                            <i class="fas fa-times"></i> Cancel Ticket
                                        </button>
                                        ` : ''}
                                    </div>
                                </div>
                            </div>
                        `;
                        ticketDetailsContent.html(ticketHtml);
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
                            <small class="text-muted">Please try again later or contact support.</small>
                        </div>`);
                }
            });
        }
    });
</script>

</body>
</html>