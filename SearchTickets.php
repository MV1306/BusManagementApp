<?php
    if (session_status() === PHP_SESSION_NONE) session_start();
    $config = include('config.php');
    $apiBaseUrl = $config['api_base_url'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Booked Tickets - Bus Ticket Booking</title>
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
        
        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }
        
        .form-control {
            width: 100%;
            padding: 0.9rem 1rem 0.9rem 3rem;
            border: 2px solid var(--light-gray);
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background-color: white;
            color: var(--dark);
            font-family: 'Poppins', sans-serif;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(76, 201, 240, 0.2);
        }
        
        .btn {
            display: inline-block;
            padding: 0.9rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-family: 'Poppins', sans-serif;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            width: 100%;
            padding: 1rem;
            font-size: 1.1rem;
            position: relative;
            overflow: hidden;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(67, 97, 238, 0.3);
        }
        
        .btn-primary::after {
            content: "";
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0) 70%);
            transform: rotate(30deg);
            transition: all 0.5s ease;
            opacity: 0;
        }
        
        .btn-primary:hover::after {
            opacity: 1;
            transform: rotate(30deg) translate(20%, 20%);
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
            transition: color 0.3s ease;
            font-size: 1.1rem;
        }
        
        .icon-input .form-control:focus + i {
            color: var(--secondary);
        }
        
        /* Search options styles */
        .search-options {
            display: flex;
            background-color: var(--light-gray);
            border-radius: 12px;
            padding: 6px;
            margin-bottom: 1.5rem;
            gap: 6px;
        }

        .search-option {
            flex: 1;
            padding: 0.8rem;
            text-align: center;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
            color: var(--dark);
            border: 2px solid transparent;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .search-option.active {
            background-color: white;
            color: var(--primary);
            font-weight: 600;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
            border-color: var(--primary-light);
        }
        
        .search-option i {
            font-size: 1rem;
        }
        
        /* Ticket results container */
        .ticket-result-container {
            margin-top: 2rem;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Modern ticket card design */
        .ticket-card {
            background: var(--ticket-bg);
            border-radius: 14px;
            padding: 0;
            margin-bottom: 1.5rem;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.03);
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .ticket-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
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
        
        .ticket-body {
            padding: 1.5rem;
        }
        
        .ticket-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.2rem;
        }
        
        .detail-group {
            margin-bottom: 0.5rem;
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
        }
        
        .ticket-btn {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.85rem;
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
            
            .search-options {
                flex-direction: column;
            }
            
            .ticket-details {
                grid-template-columns: 1fr;
            }
            
            .ticket-footer {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }
            
            .ticket-actions {
                width: 100%;
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
            <h2><i class="fas fa-ticket-alt"></i> View Your Tickets</h2>
        </div>
        <div class="card-body">
            <form id="viewTicketForm">
                <div class="form-group">
                    <div class="search-options">
                        <div class="search-option active" data-type="mobile">
                            <i class="fas fa-mobile-alt"></i> Mobile No
                        </div>
                        <div class="search-option" data-type="bookingId">
                            <i class="fas fa-ticket-alt"></i> Booking ID
                        </div>
                        <div class="search-option" data-type="bookingDate">
                            <i class="fas fa-calendar-alt"></i> Booking Date
                        </div>
                    </div>
                </div>

                <div class="form-group icon-input">
                    <input type="tel" id="searchInput" class="form-control" placeholder="Enter 10-digit Mobile Number" required>
                    <i id="inputIcon" class="fas fa-mobile-alt"></i>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Search Tickets
                </button>
            </form>

            <div id="ticketResult" class="ticket-result-container">
                <div class="info-message">
                    <i class="fas fa-ticket-alt"></i>
                    <p>Enter your search details to view tickets</p>
                    <small class="text-muted">You can search by mobile number, booking ID, or date</small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        const apiBase = "<?php echo rtrim($apiBaseUrl, '/'); ?>";
        let currentSearchType = 'mobile';

        // Handle switching search types
        $('.search-option').on('click', function() {
            const searchType = $(this).data('type');
            currentSearchType = searchType;

            $('.search-option').removeClass('active');
            $(this).addClass('active');

            const searchInput = $('#searchInput');
            const inputIcon = $('#inputIcon');
            
            searchInput.val('');

            switch(searchType) {
                case 'mobile':
                    searchInput.attr('type', 'tel')
                              .attr('placeholder', 'Enter 10-digit Mobile Number')
                              .attr('pattern', '[0-9]{10}');
                    inputIcon.attr('class', 'fas fa-mobile-alt');
                    break;
                case 'bookingId':
                    searchInput.attr('type', 'text')
                              .attr('placeholder', 'Enter Booking Reference ID')
                              .removeAttr('pattern');
                    inputIcon.attr('class', 'fas fa-ticket-alt');
                    break;
                case 'bookingDate':
                    searchInput.attr('type', 'date')
                              .attr('placeholder', '')
                              .removeAttr('pattern');
                    inputIcon.attr('class', 'fas fa-calendar-alt');
                    break;
            }
        });

        // Handle form submission
        $('#viewTicketForm').on('submit', function(e) {
            e.preventDefault();
            const searchValue = $('#searchInput').val();
            const ticketResultDiv = $('#ticketResult');

            if (!searchValue) {
                ticketResultDiv.html(`
                    <div class="info-message error">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p>Please enter a value to search</p>
                    </div>`);
                return;
            }
            
            // Show loading message
            ticketResultDiv.html(`
                <div class="info-message">
                    <i class="fas fa-spinner spinner"></i>
                    <p>Searching for your tickets...</p>
                </div>
            `);

            let apiUrl = '';
            let validationPassed = true;

            // Validate input based on search type
            if (currentSearchType === 'mobile') {
                if (!/^\d{10}$/.test(searchValue)) {
                    validationPassed = false;
                    ticketResultDiv.html(`
                        <div class="info-message error">
                           <i class="fas fa-exclamation-triangle"></i>
                           <p>Please enter a valid 10-digit mobile number</p>
                        </div>`);
                } else {
                    apiUrl = `${apiBase}/GetTicketByMobileNo/${searchValue}`;
                }
            } else if (currentSearchType === 'bookingId') {
                if (searchValue.trim() === '') {
                    validationPassed = false;
                    ticketResultDiv.html(`
                        <div class="info-message error">
                           <i class="fas fa-exclamation-triangle"></i>
                           <p>Please enter a booking reference ID</p>
                        </div>`);
                } else {
                    // This would be your actual API endpoint for booking ID search
                    // apiUrl = `${apiBase}/GetTicketById/${searchValue}`;
                    ticketResultDiv.html(`
                        <div class="info-message error">
                            <i class="fas fa-times-circle"></i>
                            <p>Searching by Booking ID is not yet implemented</p>
                        </div>`);
                    return;
                }
            } else if (currentSearchType === 'bookingDate') {
                if (!searchValue) {
                    validationPassed = false;
                    ticketResultDiv.html(`
                        <div class="info-message error">
                           <i class="fas fa-exclamation-triangle"></i>
                           <p>Please select a valid date</p>
                        </div>`);
                } else {
                    // This would be your actual API endpoint for date search
                    // apiUrl = `${apiBase}/GetTicketsByDate/${searchValue}`;
                    ticketResultDiv.html(`
                         <div class="info-message error">
                            <i class="fas fa-times-circle"></i>
                            <p>Searching by Booking Date is not yet implemented</p>
                        </div>`);
                    return;
                }
            }

            if (!validationPassed) return;

            // Make API call only for mobile search (as other methods are not implemented)
            if (currentSearchType === 'mobile') {
                $.ajax({
                    url: apiUrl,
                    method: 'GET',
                    success: function(tickets) {
                        if (tickets && tickets.length > 0) {
                            let ticketsHtml = '';
                            tickets.forEach(ticket => {
                                const bookingDate = new Date(ticket.bookingDate);
                                const formattedDate = bookingDate.toLocaleDateString('en-IN', {
                                    year: 'numeric', month: 'long', day: 'numeric'
                                });
                                const formattedTime = bookingDate.toLocaleTimeString('en-IN', {
                                    hour: '2-digit', minute: '2-digit'
                                });
                                
                                const statusClass = ticket.isRedeemed ? 'redeemed' : 'active';
                                const statusText = ticket.isRedeemed ? 'Used' : 'Active';

                                ticketsHtml += `
                                    <div class="ticket-card">
                                        <div class="ticket-header">
                                            <h3>
                                                <i class="fas fa-bus"></i> ${ticket.routeCode} • ${ticket.busType}
                                            </h3>
                                            <span class="badge"><strong>${ticket.bookingRefId}</strong></span>
                                        </div>
                                        
                                        <div class="ticket-body">
                                            <div class="ticket-details">
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
                                                    <span class="detail-label">Booking Date</span>
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
                                                    <span class="detail-label">Contact</span>
                                                    <span class="detail-value">${ticket.mobileNo}<br>${ticket.email || 'N/A'}</span>
                                                </div>
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
                                                    <i class="fas fa-times"></i> Cancel
                                                </button>
                                                ` : ''}
                                            </div>
                                        </div>
                                    </div>
                                `;
                            });
                            ticketResultDiv.html(ticketsHtml);
                        } else {
                            ticketResultDiv.html(`
                                 <div class="info-message">
                                    <i class="fas fa-search"></i>
                                    <p>No tickets found for the provided details</p>
                                    <small class="text-muted">Please check your search criteria and try again</small>
                                </div>`);
                        }
                    },
                    error: function(xhr) {
                        console.error("API Error:", xhr);
                         ticketResultDiv.html(`
                            <div class="info-message error">
                                <i class="fas fa-exclamation-circle"></i>
                                <p>Error connecting to server</p>
                                <small class="text-muted">Please try again later</small>
                            </div>`);
                    }
                });
            }
        });
    });
</script>

</body>
</html>