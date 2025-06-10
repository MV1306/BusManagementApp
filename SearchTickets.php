<?php
    if (session_status() === PHP_SESSION_NONE) session_start();
    // Assume config.php is in the same directory and returns an array with 'api_base_url'
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
        /* Copied styles from your booking page for consistency */
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
        }
        
        .card-header h2 {
            font-weight: 700;
            font-size: 1.8rem;
            margin: 0;
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
        }
        
        .icon-input .form-control:focus + i {
            color: var(--secondary);
        }
        
        .icon-input .form-control {
            padding-left: 3rem;
        }
        
        /* New styles for search options and results */
        .search-options {
            display: flex;
            background-color: #e9ecef;
            border-radius: 10px;
            padding: 5px;
            margin-bottom: 1.5rem;
        }

        .search-option {
            flex: 1;
            padding: 0.75rem;
            text-align: center;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
            color: var(--dark);
            border: 2px solid transparent;
        }

        .search-option.active {
            background-color: white;
            color: var(--primary);
            font-weight: 600;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            border-color: var(--accent);
        }
        
        .ticket-result-container {
            margin-top: 2rem;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .ticket-card {
            background: linear-gradient(135deg, rgba(76, 201, 240, 0.05) 0%, rgba(67, 97, 238, 0.05) 100%);
            border: 1px solid #e9ecef;
            border-left: 5px solid var(--accent);
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }

        .ticket-card h3 {
            color: var(--primary);
            margin-bottom: 1rem;
            font-size: 1.3rem;
            border-bottom: 1px dashed #dee2e6;
            padding-bottom: 0.75rem;
        }
        
        .ticket-details p {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }
        
        .ticket-details p span:first-child {
            font-weight: 500;
            color: #495057;
        }

        .ticket-details p span:last-child {
            font-weight: 600;
            color: var(--secondary);
        }

        .info-message {
            text-align: center;
            padding: 2rem;
            background-color: #f8f9fa;
            border-radius: 10px;
            border: 2px dashed #dee2e6;
        }
        
        .info-message i {
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: 1rem;
        }
        
        .info-message.error {
            border-color: var(--danger);
            background-color: rgba(247, 37, 133, 0.05);
        }

        .info-message.error i {
            color: var(--danger);
        }

        @media (max-width: 768px) {
            .card-header h2 { font-size: 1.5rem; }
            .card-body { padding: 1.5rem; }
        }

    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-search"></i> View Your Booking</h2>
        </div>
        <div class="card-body">
            <form id="viewTicketForm">
                <div class="form-group">
                    <label>Search By</label>
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

                <div class="form-group">
                    <div class="icon-input">
                        <input type="tel" id="searchInput" class="form-control" placeholder="Enter 10-digit Mobile Number" required>
                        <i id="inputIcon" class="fas fa-mobile-alt"></i>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Search Ticket
                </button>
            </form>

            <div id="ticketResult" class="ticket-result-container">
                 <div class="info-message">
                    <i class="fas fa-info-circle"></i>
                    <p>Your ticket details will appear here.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        const apiBase = "<?php echo rtrim($apiBaseUrl, '/'); ?>"; // Ensure no trailing slash
        let currentSearchType = 'mobile'; // Default search type

        // Handle switching search types
        $('.search-option').on('click', function() {
            const searchType = $(this).data('type');
            currentSearchType = searchType;

            // Update active class
            $('.search-option').removeClass('active');
            $(this).addClass('active');

            // Update input placeholder and type
            const searchInput = $('#searchInput');
            const inputIcon = $('#inputIcon');
            
            searchInput.val(''); // Clear previous input

            switch(searchType) {
                case 'mobile':
                    searchInput.attr('type', 'tel').attr('placeholder', 'Enter 10-digit Mobile Number');
                    inputIcon.attr('class', 'fas fa-mobile-alt');
                    break;
                case 'bookingId':
                    searchInput.attr('type', 'text').attr('placeholder', 'Enter Booking ID (e.g., UUID)');
                    inputIcon.attr('class', 'fas fa-ticket-alt');
                    break;
                case 'bookingDate':
                    searchInput.attr('type', 'date').attr('placeholder', '');
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
                alert('Please enter a value to search.');
                return;
            }
            
            // Show loading message
            ticketResultDiv.html(`
                <div class="info-message">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Searching for your booking...</p>
                </div>
            `);

            let apiUrl = '';

            // Construct API URL based on search type
            // --- ONLY MOBILE IS IMPLEMENTED AS PER YOUR API INFO ---
            if (currentSearchType === 'mobile') {
                 if (!/^\d{10}$/.test(searchValue)) {
                    ticketResultDiv.html(`
                        <div class="info-message error">
                           <i class="fas fa-exclamation-triangle"></i>
                           <p>Please enter a valid 10-digit mobile number.</p>
                        </div>`);
                    return;
                }
                apiUrl = `${apiBase}/GetTicketByMobileNo/${searchValue}`;            
            } else if (currentSearchType === 'bookingId') {
                // Example for when you have the Booking ID API
                // apiUrl = `${apiBase}/Tickets/id/${searchValue}`; 
                ticketResultDiv.html(`
                    <div class="info-message error">
                        <i class="fas fa-times-circle"></i>
                        <p>Searching by Booking ID is not yet implemented.</p>
                    </div>`);
                return; // Stop execution for unimplemented features
            } else if (currentSearchType === 'bookingDate') {
                // Example for when you have the Booking Date API
                // apiUrl = `${apiBase}/Tickets/date/${searchValue}`;
                ticketResultDiv.html(`
                     <div class="info-message error">
                        <i class="fas fa-times-circle"></i>
                        <p>Searching by Booking Date is not yet implemented.</p>
                    </div>`);
                return; // Stop execution for unimplemented features
            }

            $.ajax({
                url: apiUrl,
                method: 'GET',
                success: function(tickets) {
                    if (tickets && tickets.length > 0) {
                        let ticketsHtml = '';
                        tickets.forEach(ticket => {
                             // Assuming date is in a format that needs formatting, e.g., "2024-05-20T00:00:00"
                            const formattedDate = new Date(ticket.bookingDate).toLocaleDateString('en-IN', {
                                year: 'numeric', month: 'long', day: 'numeric'
                            });

                            ticketsHtml += `
                                <div class="ticket-card">
                                    <h3>
                                        <i class="fas fa-bus-alt"></i> Route: ${ticket.routeCode || 'N/A'}
                                    </h3>
                                    <div class="ticket-details">
                                        <p><span>Booking ID:</span> <span>${ticket.ticketID || 'N/A'}</span></p>
                                        <p><span>Passenger Name:</span> <span>${ticket.userName || 'N/A'}</span></p>
                                        <p><span>Journey Date:</span> <span>${formattedDate}</span></p>
                                        <p><span>From:</span> <span>${ticket.fromStage || 'N/A'}</span></p>
                                        <p><span>To:</span> <span>${ticket.toStage || 'N/A'}</span></p>
                                        <p><span>Total Fare:</span> <span>₹${ticket.fare ? ticket.fare.toFixed(2) : '0.00'}</span></p>
                                    </div>
                                </div>
                            `;
                        });
                        ticketResultDiv.html(ticketsHtml);
                    } else {
                        ticketResultDiv.html(`
                             <div class="info-message">
                                <i class="fas fa-search"></i>
                                <p>No bookings found for the provided details.</p>
                            </div>`);
                    }
                },
                error: function(xhr) {
                    console.error("API Error:", xhr);
                     ticketResultDiv.html(`
                        <div class="info-message error">
                            <i class="fas fa-exclamation-circle"></i>
                            <p>Could not connect to the server. Please check your connection and try again.</p>
                        </div>`);
                }
            });
        });
    });
</script>

</body>
</html>
