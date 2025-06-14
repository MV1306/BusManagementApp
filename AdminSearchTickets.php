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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Booked Tickets - Bus Ticket Booking</title>
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
            
            /* Category Colors */
            --active-color: #28a745; /* Green */
            --active-color-dark: #228b22; /* Darker green for gradient */
            --redeemed-color: #6f42c1; /* Muted Purple */
            --redeemed-color-dark: #5b369c; /* Darker purple for gradient */
            --cancelled-color: #dc3545; /* Red */
            --cancelled-color-dark: #b02a37; /* Darker red for gradient */

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
            cursor: pointer;
        }
        
        .ticket-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        }

        /* Dynamic header color based on status */
        .ticket-card .ticket-header {
            color: white;
            padding: 1.2rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%); 
        }

        .ticket-card.Active .ticket-header {
            background: linear-gradient(135deg, var(--active-color) 0%, var(--active-color-dark) 100%);
        }
        .ticket-card.Redeemed .ticket-header {
            background: linear-gradient(135deg, var(--redeemed-color) 0%, var(--redeemed-color-dark) 100%);
        }
        .ticket-card.Cancelled .ticket-header {
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
        
        .ticket-body {
            padding: 1.5rem;
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
        }
        
        .ticket-status.Active .status-dot {
            background-color: var(--active-color);
        }
        
        .ticket-status.Redeemed .status-dot {
            background-color: var(--redeemed-color);
        }

        .ticket-status.Cancelled .status-dot {
            background-color: var(--cancelled-color);
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
            border-color: var(--cancelled-color);
            background-color: rgba(220, 53, 69, 0.03);
        }

        .info-message.error i {
            color: var(--cancelled-color);
        }
        
        /* Loading spinner */
        .spinner {
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Tab Styles */
        .tabs {
            display: flex;
            margin-bottom: 1.5rem;
            border-bottom: 2px solid var(--light-gray);
            gap: 5px;
        }

        .tab-button {
            padding: 0.8rem 1.2rem;
            cursor: pointer;
            font-weight: 600;
            color: var(--gray);
            transition: all 0.3s ease;
            border-bottom: 3px solid transparent;
            position: relative;
            top: 2px;
            background-color: transparent;
            border-radius: 8px 8px 0 0;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .tab-button:hover:not(.active) {
            color: var(--primary);
            background-color: var(--light-gray);
        }

        .tab-button[data-tab="activeTickets"].active {
            color: var(--active-color);
            border-bottom-color: var(--active-color);
        }
        .tab-button[data-tab="redeemedTickets"].active {
            color: var(--redeemed-color);
            border-bottom-color: var(--redeemed-color);
        }
        .tab-button[data-tab="cancelledTickets"].active {
            color: var(--cancelled-color);
            border-bottom-color: var(--cancelled-color);
        }

        .tab-button i {
            font-size: 1rem;
        }

        .tab-content {
            display: none;
            animation: fadeIn 0.5s ease;
        }

        .tab-content.active {
            display: block;
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

            .tabs {
                flex-wrap: wrap;
                justify-content: center;
            }
            .tab-button {
                flex-basis: 45%;
                margin-bottom: 5px;
                justify-content: center;
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

            .tab-button {
                flex-basis: 100%;
            }
        }
    </style>
</head>
<body>

<?php include 'AdminNavbar.php'; ?>

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
                    <input type="tel" id="searchInput" autocomplete="off" class="form-control" placeholder="Enter 10-digit Mobile Number" required>
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

        // Helper function to format date
        function formatBookingDate(isoDateString) {
            try {
                const date = new Date(isoDateString);
                if (isNaN(date.getTime())) {
                    return "Invalid Date";
                }
                return date.toLocaleDateString('en-IN', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
            } catch (e) {
                console.error("Error formatting date:", isoDateString, e);
                return "Date N/A";
            }
        }

        // Helper function to generate a ticket card HTML
        function generateTicketCard(ticket) {
            const formattedDate = formatBookingDate(ticket.bookingDate);
            
            // Determine the status class and text based on ticket properties
            let statusClass = ticket.status;
            let statusText = ticket.status;

            // if (ticket.isCancelled) {
            //     statusClass = 'cancelled';
            //     statusText = 'Cancelled';
            // } else if (ticket.isRedeemed) {
            //     statusClass = 'redeemed';
            //     statusText = 'Used';
            // }
            
            return `
                <div class="ticket-card ${statusClass}" data-ticket-id="${ticket.ticketID}">
                    <div class="ticket-header">
                        <h3>
                            <i class="fas fa-bus"></i> ${ticket.routeCode || 'N/A'}
                        </h3>
                        <span class="badge"><strong>${ticket.bookingRefId || 'N/A'}</strong></span>
                    </div>
                    
                    <div class="ticket-body">
                        <div class="detail-group">
                            <span class="detail-label">From</span>
                            <span class="detail-value">${ticket.fromStage || 'N/A'}</span>
                        </div>
                        
                        <div class="detail-group">
                            <span class="detail-label">To</span>
                            <span class="detail-value">${ticket.toStage || 'N/A'}</span>
                        </div>
                        
                        <div class="detail-group">
                            <span class="detail-label">Booking Date</span>
                            <span class="detail-value">${formattedDate}</span>
                        </div>
                        
                        <div class="detail-group">
                            <span class="detail-label">Passengers</span>
                            <span class="detail-value">${ticket.passengers || 'N/A'}</span>
                        </div>
                    </div>
                    
                    <div class="ticket-footer">
                        <div class="ticket-status ${statusClass}">
                            <span class="status-dot"></span>
                            <span>${statusText}</span>
                        </div>
                        <div>
                            <small class="text-muted">Click for full details</small>
                        </div>
                    </div>
                </div>
            `;
        }

        // Function to render tickets into a specific tab content
        function renderTicketsToTab(tabId, ticketsArray) {
            const tabContentDiv = $(`#${tabId}`);
            tabContentDiv.empty();

            if (ticketsArray && ticketsArray.length > 0) {
                ticketsArray.forEach(ticket => {
                    tabContentDiv.append(generateTicketCard(ticket));
                });
            } else {
                tabContentDiv.html(`
                    <div class="info-message">
                        <i class="fas fa-box-open"></i>
                        <p>No tickets found in this category.</p>
                    </div>
                `);
            }
        }

        // Handle switching search types (Mobile, Booking ID, Date)
        $('.search-option').on('click', function() {
            const searchType = $(this).data('type');
            currentSearchType = searchType;

            $('.search-option').removeClass('active');
            $(this).addClass('active');

            const searchInput = $('#searchInput');
            const inputIcon = $('#inputIcon');
            
            searchInput.val('');

            $('#ticketResult').html(`
                <div class="info-message">
                    <i class="fas fa-ticket-alt"></i>
                    <p>Enter your search details to view tickets</p>
                    <small class="text-muted">You can search by mobile number, booking ID, or date</small>
                </div>
            `);

            switch(searchType) {
                case 'mobile':
                    searchInput.attr('type', 'tel')
                               .attr('placeholder', 'Enter 10-digit Mobile Number')
                               .attr('pattern', '[0-9]{10}')
                               .prop('required', true);
                    inputIcon.attr('class', 'fas fa-mobile-alt');
                    break;
                case 'bookingId':
                    searchInput.attr('type', 'text')
                               .attr('placeholder', 'Enter Booking Reference ID')
                               .removeAttr('pattern')
                               .prop('required', true);
                    inputIcon.attr('class', 'fas fa-ticket-alt');
                    break;
                case 'bookingDate':
                    searchInput.attr('type', 'date')
                               .attr('placeholder', '')
                               .removeAttr('pattern')
                               .prop('required', true);
                    inputIcon.attr('class', 'fas fa-calendar-alt');
                    break;
            }
        });

        // Handle form submission
        $('#viewTicketForm').on('submit', function(e) {
            e.preventDefault();
            const searchValue = $('#searchInput').val().trim();
            const ticketResultDiv = $('#ticketResult');

            if (!searchValue) {
                ticketResultDiv.html(`
                    <div class="info-message error">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p>Please enter a value to search</p>
                    </div>`);
                return;
            }
            
            ticketResultDiv.html(`
                <div class="info-message">
                    <i class="fas fa-spinner spinner"></i>
                    <p>Searching for your tickets...</p>
                </div>
            `);

            let apiUrl = '';
            let validationPassed = true;

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
                if (searchValue === '') {
                    validationPassed = false;
                    ticketResultDiv.html(`
                        <div class="info-message error">
                           <i class="fas fa-exclamation-triangle"></i>
                           <p>Please enter a booking reference ID</p>
                        </div>`);
                } else {
                    ticketResultDiv.html(`
                        <div class="info-message error">
                            <i class="fas fa-times-circle"></i>
                            <p>Searching by Booking ID is not yet implemented.</p>
                            <small class="text-muted">Please use mobile number for now.</small>
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
                    ticketResultDiv.html(`
                           <div class="info-message error">
                                <i class="fas fa-times-circle"></i>
                                <p>Searching by Booking Date is not yet implemented.</p>
                                <small class="text-muted">Please use mobile number for now.</small>
                            </div>`);
                    return;
                }
            }

            if (!validationPassed) return;

            // Make API call
            $.ajax({
                url: apiUrl,
                method: 'GET',
                success: function(response) {
                    let allTicketsFound = false;

                    if (!response || (!response.active && !response.redeemed && !response.cancelled)) {
                        ticketResultDiv.html(`
                            <div class="info-message error">
                                <i class="fas fa-search"></i>
                                <p>No tickets found for the provided details.</p>
                                <small class="text-muted">Please check your search criteria and try again.</small>
                            </div>
                        `);
                        return;
                    }
                    
                    // Build the tab structure and content areas
                    let tabsHtml = `
                        <div class="tabs">
                            <div class="tab-button active" data-tab="activeTickets"><i class="fas fa-check-circle"></i> Active</div>
                            <div class="tab-button" data-tab="redeemedTickets"><i class="fas fa-history"></i> Redeemed</div>
                            <div class="tab-button" data-tab="cancelledTickets"><i class="fas fa-times-circle"></i> Cancelled</div>
                        </div>
                        <div id="activeTickets" class="tab-content active"></div>
                        <div id="redeemedTickets" class="tab-content"></div>
                        <div id="cancelledTickets" class="tab-content"></div>
                    `;
                    ticketResultDiv.html(tabsHtml);

                    // Render tickets into their respective tabs
                    if (response.active && response.active.length > 0) {
                        renderTicketsToTab('activeTickets', response.active);
                        allTicketsFound = true;
                    } else {
                        $('#activeTickets').html(`
                            <div class="info-message">
                                <i class="fas fa-box-open"></i>
                                <p>No active tickets found.</p>
                            </div>
                        `);
                    }

                    if (response.redeemed && response.redeemed.length > 0) {
                        renderTicketsToTab('redeemedTickets', response.redeemed);
                        allTicketsFound = true;
                    } else {
                        $('#redeemedTickets').html(`
                            <div class="info-message">
                                <i class="fas fa-box-open"></i>
                                <p>No redeemed tickets found.</p>
                            </div>
                        `);
                    }

                    if (response.cancelled && response.cancelled.length > 0) {
                        renderTicketsToTab('cancelledTickets', response.cancelled);
                        allTicketsFound = true;
                    } else {
                         $('#cancelledTickets').html(`
                            <div class="info-message">
                                <i class="fas fa-box-open"></i>
                                <p>No cancelled tickets found.</p>
                            </div>
                        `);
                    }
                    
                    if (!allTicketsFound) {
                        ticketResultDiv.html(`
                            <div class="info-message">
                                <i class="fas fa-search"></i>
                                <p>No tickets found for the provided details.</p>
                                <small class="text-muted">Please check your search criteria and try again.</small>
                            </div>
                        `);
                    }

                    // Attach click event to ticket cards
                    $('#ticketResult').off('click', '.ticket-card').on('click', '.ticket-card', function() {
                        const ticketID = $(this).data('ticket-id');
                        if (ticketID) {
                            window.location.href = `AdminTicketDetails.php?id=${ticketID}`;
                        } else {
                            console.warn("Ticket card clicked, but no ticket ID found.");
                        }
                    });

                    // Tab switching logic
                    $('#ticketResult').off('click', '.tab-button').on('click', '.tab-button', function() {
                        const targetTab = $(this).data('tab');

                        $('#ticketResult .tab-button').removeClass('active');
                        $('#ticketResult .tab-content').removeClass('active');

                        $(this).addClass('active');
                        $(`#${targetTab}`).addClass('active');
                    });

                    // Set default active tab
                    if (response.active && response.active.length > 0) {
                        $('.tab-button[data-tab="activeTickets"]').click();
                    } else if (response.redeemed && response.redeemed.length > 0) {
                         $('.tab-button[data-tab="redeemedTickets"]').click();
                    } else if (response.cancelled && response.cancelled.length > 0) {
                         $('.tab-button[data-tab="cancelledTickets"]').click();
                    } else {
                         $('.tab-button[data-tab="activeTickets"]').click();
                    }

                },
                error: function(xhr, status, error) {
                    console.error("API Error:", status, error, xhr);
                       ticketResultDiv.html(`
                            <div class="info-message error">
                                <i class="fas fa-exclamation-circle"></i>
                                <p>Error retrieving tickets.</p>
                                <small class="text-muted">Please ensure the mobile number is correct and try again later. (Status: ${xhr.status})</small>
                            </div>`);
                }
            });
        });
    });
</script>

</body>
</html>