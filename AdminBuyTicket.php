<?php
    require_once 'AdminAuth.php';
    checkAuth();
    $config = include('config.php');
    $apiBaseUrl = $config['api_base_url'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Bus Ticket Booking</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            overflow: visible;
            margin-bottom: 2rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
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
            overflow: visible;
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
       
        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 1em;
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
       
        .fare-result {
            background: linear-gradient(135deg, rgba(76, 201, 240, 0.1) 0%, rgba(67, 97, 238, 0.1) 100%);
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1.5rem 0;
            border-left: 5px solid var(--accent);
            animation: fadeIn 0.5s ease;
        }
       
        .fare-result h3 {
            color: var(--primary);
            margin-bottom: 0.5rem;
            font-size: 1.2rem;
        }
       
        .fare-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }
       
        .fare-total {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--secondary);
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px dashed #dee2e6;
        }
       
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
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
       
        .route-info {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
       
        .route-info-item {
            flex: 1;
            background: rgba(67, 97, 238, 0.1);
            padding: 1rem;
            border-radius: 10px;
            text-align: center;
        }
       
        .route-info-item i {
            color: var(--primary);
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
       
        .route-info-item span {
            display: block;
            font-weight: 600;
            color: var(--secondary);
        }
       
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
       
        .modal.show {
            display: flex;
            opacity: 1;
        }
       
        .modal-content {
            background-color: white;
            border-radius: 15px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            transform: translateY(-20px);
            transition: transform 0.3s ease;
            overflow: hidden;
        }
       
        .modal.show .modal-content {
            transform: translateY(0);
        }
       
        .modal-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 1.5rem;
            text-align: center;
        }
       
        .modal-header h3 {
            margin: 0;
            font-size: 1.5rem;
        }
       
        .modal-body {
            padding: 2rem;
        }
       
        .modal-footer {
            padding: 1rem 2rem;
            background-color: #f8f9fa;
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
        }
       
        .error-message {
            color: var(--danger);
            font-size: 0.9rem;
            margin-top: 0.25rem;
            display: none;
        }
       
        /* Route Search Results Styles */
        .search-container {
            position: relative;
            z-index: 100;
        }
       
        .route-search-results {
            position: absolute;
            width: calc(100% - 2px);
            max-height: 300px;
            overflow-y: auto;
            background: white;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            display: none;
            top: 100%;
            left: 0;
            margin-top: -1px;
        }
       
        .route-item {
            padding: 10px 15px;
            cursor: pointer;
            transition: background-color 0.2s;
            border-bottom: 1px solid #eee;
        }
       
        .route-item:hover {
            background-color: #f5f5f5;
        }
       
        .route-item:last-child {
            border-bottom: none;
        }
       
        .route-item .route-code {
            font-weight: bold;
            color: var(--primary);
        }
       
        .route-item .route-name {
            font-size: 0.9em;
            color: #666;
        }
       
        .search-loading {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary);
            display: none;
            z-index: 10;
        }
       
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
           
            .route-info {
                flex-direction: column;
                gap: 0.5rem;
            }
           
            .modal-content {
                width: 95%;
            }
           
            .modal-body {
                padding: 1.5rem;
            }
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<?php include 'AdminNavbar.php'; ?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-bus"></i> Book Your Bus Ticket</h2>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label for="routeSearch">Search Route</label>
                <div class="search-container">
                    <div class="icon-input">
                        <i class="fas fa-route"></i>
                        <input type="text" id="routeSearch" class="form-control" placeholder="Enter route code or name...">
                    </div>
                    <div class="search-loading" id="searchLoading">
                        <i class="fas fa-spinner fa-spin"></i>
                    </div>
                    <div class="route-search-results" id="routeSearchResults"></div>
                </div>
                <input type="hidden" id="routeCode">
            </div>
           
            <div id="routeDetails" style="display: none;">
                <div class="route-info">
                    <div class="route-info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span id="fromStageLabel">From</span>
                    </div>
                    <div class="route-info-item">
                        <i class="fas fa-arrow-right"></i>
                        <span>Journey</span>
                    </div>
                    <div class="route-info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span id="toStageLabel">To</span>
                    </div>
                </div>
               
                <div class="form-group">
                    <label for="fromStage">Departure Point</label>
                    <div class="icon-input">
                        <i class="fas fa-location-arrow"></i>
                        <select id="fromStage" class="form-control" disabled>
                            <option value="">Select departure stage...</option>
                        </select>
                    </div>
                </div>
               
                <div class="form-group">
                    <label for="toStage">Destination Point</label>
                    <div class="icon-input">
                        <i class="fas fa-map-pin"></i>
                        <select id="toStage" class="form-control" disabled>
                            <option value="">Select destination stage...</option>
                        </select>
                    </div>
                </div>
               
                <div class="form-group">
                    <label for="busType">Bus Type</label>
                    <div class="icon-input">
                        <i class="fas fa-bus"></i>
                        <select id="busType" class="form-control">
                            <option value="">Select bus type...</option>
                            <option value="AC">AC</option>
                            <option value="Express">Express</option>
                            <option value="Deluxe">Deluxe</option>
                            <option value="Night">Night Service</option>
                            <option value="Ordinary">Ordinary</option>
                        </select>
                    </div>
                </div>
               
                <div class="form-group">
                    <label for="passengerCount">Number of Passengers</label>
                    <div class="icon-input">
                        <i class="fas fa-users"></i>
                        <input type="number" id="passengerCount" class="form-control" min="1" value="1">
                    </div>
                </div>
               
                <button id="calculateBtn" class="btn btn-primary">
                    <i class="fas fa-calculator"></i> Calculate Fare
                </button>
               
                <div id="fareResult" class="fare-result" style="display: none;">
                    <h3><i class="fas fa-receipt"></i> Fare Details</h3>
                    <div class="fare-details">
                        <span>Route:</span>
                        <span id="routeDisplay"></span>
                    </div>
                    <div class="fare-details">
                        <span>From:</span>
                        <span id="fromDisplay"></span>
                    </div>
                    <div class="fare-details">
                        <span>To:</span>
                        <span id="toDisplay"></span>
                    </div>
                    <div class="fare-details">
                        <span>Bus Type:</span>
                        <span id="busTypeDisplay"></span>
                    </div>
                    <div class="fare-details">
                        <span>Base Fare:</span>
                        <span id="baseFare">₹0</span>
                    </div>
                    <div class="fare-details">
                        <span>Passengers:</span>
                        <span id="passengerCountDisplay">1</span>
                    </div>
                    <div class="fare-total">
                        Total: <span id="totalFare">₹0</span>
                    </div>
                </div>
               
                <button id="buyBtn" class="btn btn-primary" disabled>
                    <i class="fas fa-ticket-alt"></i> Confirm Booking
                </button>
            </div>
        </div>
    </div>
</div>

<div id="passengerModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-user-edit"></i> Passenger Details</h3>
        </div>
        <div class="modal-body">
            <form id="passengerForm">
                <div class="form-group">
                    <label for="passengerName">Full Name</label>
                    <div class="icon-input">
                        <i class="fas fa-user"></i>
                        <input type="text" id="passengerName" class="form-control" placeholder="Enter your full name" required>
                    </div>
                    <div id="nameError" class="error-message">Please enter your name</div>
                </div>
               
                <div class="form-group">
                    <label for="passengerMobile">Mobile Number</label>
                    <div class="icon-input">
                        <i class="fas fa-mobile-alt"></i>
                        <input type="tel" id="passengerMobile" class="form-control" placeholder="Enter 10-digit mobile number" required>
                    </div>
                    <div id="mobileError" class="error-message">Please enter a valid 10-digit mobile number</div>
                </div>
               
                <div class="form-group">
                    <label for="passengerEmail">Email Address</label>
                    <div class="icon-input">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="passengerEmail" class="form-control" placeholder="Enter your email address">
                    </div>
                    <div id="emailError" class="error-message">Please enter a valid email address</div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button id="cancelBooking" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </button>
            <button id="confirmBooking" class="btn btn-primary">
                <i class="fas fa-check"></i> Confirm Booking
            </button>
        </div>
    </div>
</div>

<script>
    const apiBase = "<?php echo $apiBaseUrl; ?>"; // This will be your http://192.168.29.141/BusManagementAPI/api/
    let searchTimeout;

    // Route search functionality
    $('#routeSearch').on('input', function() {
        const searchText = $(this).val().trim();
        
        // Clear previous timeout if exists
        if (searchTimeout) {
            clearTimeout(searchTimeout);
        }
        
        // Hide results if search is empty
        if (searchText.length === 0) {
            $('#routeSearchResults').hide().empty();
            return;
        }
        
        // Show loading indicator
        $('#searchLoading').show();
        
        // Set a new timeout to delay the API call
        searchTimeout = setTimeout(() => {
            $.get(`${apiBase}SearchRoutes/${encodeURIComponent(searchText)}`, function(routes) {
                $('#searchLoading').hide();
                
                const resultsContainer = $('#routeSearchResults');
                resultsContainer.empty();
                
                if (routes && routes.length > 0) {
                    routes.forEach(route => {
                        resultsContainer.append(`
                            <div class="route-item" data-code="${route}">
                                <div class="route-code">${route}</div>
                            </div>
                        `);
                    });
                    resultsContainer.show();
                } else {
                    resultsContainer.append('<div class="route-item">No routes found</div>');
                    resultsContainer.show();
                }
            }).fail(function() {
                $('#searchLoading').hide();
                $('#routeSearchResults').html('<div class="route-item">Error loading routes</div>').show();
            });
        }, 500); // 500ms delay after typing stops
    });
    
    // Handle route selection from search results
    $(document).on('click', '.route-item', function() {
        const routeCode = $(this).data('code');
        const routeName = $(this).find('.route-code').text();
        
        // Set the selected route
        $('#routeSearch').val(routeName);
        $('#routeCode').val(routeCode);
        $('#routeSearchResults').hide();
        
        // Load stages for the selected route
        loadRouteStages(routeCode);
    });
    
    // Hide search results when clicking elsewhere
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.search-container').length) {
            $('#routeSearchResults').hide();
        }
    });
    
    // Function to load route stages
    function loadRouteStages(routeCode) {
        $('#fromStage, #toStage').empty().append('<option value="">Select...</option>').prop('disabled', true);
        $('#fareResult').hide();
        $('#buyBtn').prop('disabled', true);
        $('#routeDetails').hide();
        
        if (routeCode) {
            $.get(`${apiBase}GetRouteStagesByCode/${routeCode}`, function(stages) {
                $('#routeDetails').show();
                
                stages.stages.forEach(s => {
                    $('#fromStage, #toStage').append(`<option value="${s.stageName}">${s.stageName}</option>`);
                });
                
                $('#fromStage, #toStage').prop('disabled', false);
            }).fail(function() {
                console.error("Failed to load route stages.");
                alert("Could not load stages for the selected route.");
            });
        }
    }

    // Update route info labels when stages change
    $('#fromStage, #toStage').on('change', function() {
        $('#fromStageLabel').text($('#fromStage').val() || 'From');
        $('#toStageLabel').text($('#toStage').val() || 'To');
        // Reset fare result and buy button if stages change after calculation
        $('#fareResult').hide();
        $('#buyBtn').prop('disabled', true);
    });

    // Calculate fare
    $('#calculateBtn').on('click', function() {
        const routeCode = $('#routeCode').val();
        const from = $('#fromStage').val();
        const to = $('#toStage').val();
        const busType = $('#busType').val();
        const passengers = parseInt($('#passengerCount').val());

        if (!routeCode || !from || !to || !busType || !passengers || passengers < 1) {
            alert("Please fill all fields correctly before calculating fare.");
            return;
        }

        // Check if 'from' and 'to' stages are different
        if (from === to) {
            alert("Departure and Destination points cannot be the same.");
            return;
        }

        $.get(`${apiBase}CalculateFare/${routeCode}/${busType}/${from}/${to}`, function(fare) {
            if (fare && fare.fare !== undefined) {
                const totalFare = fare.fare * passengers;
               
                // Update all fare details displays
                $('#routeDisplay').text($('#routeSearch').val());
                $('#fromDisplay').text(from);
                $('#toDisplay').text(to);
                $('#busTypeDisplay').text(busType);
                $('#baseFare').text(`₹${fare.fare.toFixed(2)}`);
                $('#passengerCountDisplay').text(passengers);
                $('#totalFare').text(`₹${totalFare.toFixed(2)}`);
               
                $('#fareResult').show();
                $('#buyBtn').prop('disabled', false);
               
                // Scroll to fare result for better UX
                $('html, body').animate({
                    scrollTop: $('#fareResult').offset().top - 100
                }, 500);
            } else {
                alert("Invalid fare data received. Please try again.");
                $('#fareResult').hide();
                $('#buyBtn').prop('disabled', true);
            }
        }).fail(function(xhr) {
            let errorMsg = "Could not calculate fare. Please check your inputs and try again.";
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            } else if (xhr.responseText) {
                errorMsg = xhr.responseText;
            }
            alert(`Error: ${errorMsg}`);
            $('#fareResult').hide();
            $('#buyBtn').prop('disabled', true);
        });
    });

    // Show passenger details modal when clicking Buy button
    $('#buyBtn').on('click', function() {
        $('#passengerModal').addClass('show');
        // Pre-fill passenger details if available from session or local storage (optional)
        $('#passengerName').val('<?php echo $_SESSION["username"] ?? ''; ?>'); // Example pre-fill from session
    });

    // Close modal when clicking cancel button inside the modal
    $('#cancelBooking').on('click', function() {
        $('#passengerModal').removeClass('show');
        // Clear any validation errors
        $('.error-message').hide();
        $('#passengerForm')[0].reset(); // Reset form fields
    });

    // Validate 10-digit mobile number
    function validateMobile(mobile) {
        return /^\d{10}$/.test(mobile);
    }

    // Validate email (optional, can be empty but if present must be valid)
    function validateEmail(email) {
        if (!email) return true; // Email is optional, so empty is valid
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    // Process booking when confirming in modal
    $('#confirmBooking').on('click', function() {
        // Validate form inputs
        const name = $('#passengerName').val().trim();
        const mobile = $('#passengerMobile').val().trim();
        const email = $('#passengerEmail').val().trim();
       
        let isValid = true;
       
        // Validate name
        if (!name) {
            $('#nameError').show();
            isValid = false;
        } else {
            $('#nameError').hide();
        }
       
        // Validate mobile
        if (!validateMobile(mobile)) {
            $('#mobileError').show();
            isValid = false;
        } else {
            $('#mobileError').hide();
        }
       
        // Validate email
        if (!validateEmail(email)) {
            $('#emailError').show();
            isValid = false;
        } else {
            $('#emailError').hide();
        }
       
        if (!isValid) {
            return; // Stop if any validation fails
        }
       
        // Get booking details from the main form
        const routeCode = $('#routeCode').val();
        const from = $('#fromStage').val();
        const to = $('#toStage').val();
        const busType = $('#busType').val();
        const passengers = parseInt($('#passengerCount').val());
        const totalFare = parseFloat($('#totalFare').text().replace('₹', '')); // Get the already calculated total fare

        let stagesTravelled = 0;
        let baseFarePerPerson = parseFloat($('#baseFare').text().replace('₹', '')); // Use the base fare already displayed

        // Synchronously fetch route stages to determine stagesTravelled
        $.ajax({
            url: `${apiBase}GetRouteStagesByCode/${routeCode}`,
            method: "GET",
            async: false, // Important: makes the call blocking
            success: function(data) {
                const stages = data.stages;
                let fromIndex = -1;
                let toIndex = -1;
               
                // Find indices of from and to stages in the route array
                for (let i = 0; i < stages.length; i++) {
                    if (stages[i].stageName === from) {
                        fromIndex = i;
                    }
                    if (stages[i].stageName === to) {
                        toIndex = i;
                    }
                }

                if (fromIndex !== -1 && toIndex !== -1) {
                    stagesTravelled = Math.abs(toIndex - fromIndex);
                } else {
                    console.error("Could not find 'from' or 'to' stages in route data.");
                    alert("A problem occurred with route stages. Please try again.");
                    isValid = false; // Mark as invalid to stop booking
                }
            },
            error: function() {
                console.error("Failed to fetch route stages for stagesTravelled calculation.");
                alert("Failed to confirm route details. Please try again.");
                isValid = false; // Mark as invalid to stop booking
            }
        });

        if (!isValid) {
            $('#confirmBooking').html('<i class="fas fa-check"></i> Confirm Booking').prop('disabled', false);
            return; // Stop the booking process if stages could not be determined
        }

        const payload = {
            routeCode: routeCode,
            fromStage: from,
            toStage: to,
            busType: busType,
            passengers: passengers,
            userName: name,
            mobileNo: mobile,
            email: email
        };

        // Show loading state on button
        $('#confirmBooking').html('<i class="fas fa-spinner fa-spin"></i> Processing...').prop('disabled', true);
       
        // Make the API call to book the ticket
        $.ajax({
            url: `${apiBase}BuyTicket`, // Corrected API endpoint for booking
            method: "POST",
            contentType: "application/json", // Specify content type as JSON
            data: JSON.stringify(payload), // Send the payload as a JSON string
            success: function(response) {
                // Hide modal
                $('#passengerModal').removeClass('show');
               
                // Extract Booking ID from the response string
                const bookingIdMatch = response.match(/Booking Reference ID - ([A-Z0-9]+)/);
                const bookingId = bookingIdMatch ? bookingIdMatch[1] : 'N/A';

                // Update the fareResult section to show success message
                $('#fareResult').html(`
                    <div style="text-align: center; padding: 2rem;">
                        <i class="fas fa-check-circle" style="font-size: 3rem; color: var(--success); margin-bottom: 1rem;"></i>
                        <h3 style="color: var(--success);">Ticket Booked Successfully!</h3>
                        <p>Booking Reference: <strong>${bookingId}</strong></p>
                        <p>Passenger: <strong>${name}</strong></p>
                        <p>Mobile: <strong>${mobile}</strong></p>
                        ${email ? `<p>Email: <strong>${email}</strong></p>` : ''}
                        <p>Total Paid: <strong>₹${totalFare.toFixed(2)}</strong></p>
                        <button onclick="window.location.reload()" class="btn btn-secondary" style="margin-top: 1rem;">
                            <i class="fas fa-plus"></i> Book Another Ticket
                        </button>
                    </div>
                `).show(); // Ensure it's visible

                // Hide the original buy button after successful booking
                $('#buyBtn').hide();
               
                // Reset passenger form and button state
                $('#passengerForm')[0].reset();
                $('#confirmBooking').html('<i class="fas fa-check"></i> Confirm Booking').prop('disabled', false);

                // Scroll to the success message for the user
                $('html, body').animate({
                    scrollTop: $('#fareResult').offset().top - 100
                }, 500);

            },
            error: function(xhr) {
                // Handle API error response
                let errorMsg = "Booking failed. An unknown error occurred.";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    errorMsg = xhr.responseText;
                }
               
                alert(`Error: ${errorMsg}`); // Display error to the user
                $('#confirmBooking').html('<i class="fas fa-check"></i> Confirm Booking').prop('disabled', false); // Reset button
            }
        });
    });

    // Close modal when clicking outside the modal content
    $(document).on('click', function(e) {
        if ($(e.target).hasClass('modal')) {
            $('#passengerModal').removeClass('show');
            // Optionally clear form and errors when closing modal from outside click
            $('.error-message').hide();
            $('#passengerForm')[0].reset();
        }
    });
</script>

</body>
</html>