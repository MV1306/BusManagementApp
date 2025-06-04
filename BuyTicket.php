<?php 
    if (session_status() === PHP_SESSION_NONE) session_start(); 

    $config = include('config.php');
    $apiBaseUrl = $config['api_base_url'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Buy Ticket</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        :root {
            --rcb-primary: #000000;
            --rcb-secondary: #FF005C;
            --rcb-accent: #FFFFFF;
            --rcb-highlight: #575757;
        }
        
        body { 
            font-family: 'Poppins', 'Segoe UI', sans-serif; 
            background: linear-gradient(135deg, var(--rcb-primary) 0%, var(--rcb-highlight) 100%);
            padding: 20px;
            min-height: 100vh;
            color: var(--rcb-accent);
        }
        
        .container { 
            max-width: 600px; 
            margin: 20px auto; 
            background: rgba(0, 0, 0, 0.8); 
            padding: 30px; 
            border-radius: 15px; 
            box-shadow: 0 5px 15px rgba(255, 0, 92, 0.2);
            border: 1px solid var(--rcb-secondary);
        }
        
        h2 { 
            color: var(--rcb-secondary); 
            margin-bottom: 25px;
            text-align: center;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .form-group { 
            margin-bottom: 20px; 
        }
        
        label { 
            font-weight: 600;
            display: block;
            margin-bottom: 8px;
            color: var(--rcb-accent);
        }
        
        select, input[type="number"] {
            width: 100%; 
            padding: 12px 15px; 
            margin-top: 5px; 
            border-radius: 8px; 
            border: 1px solid var(--rcb-secondary);
            background-color: var(--rcb-primary);
            color: var(--rcb-accent);
            font-size: 16px;
            transition: all 0.3s;
        }
        
        select:focus, input[type="number"]:focus {
            outline: none;
            border-color: var(--rcb-accent);
            box-shadow: 0 0 0 2px rgba(255, 0, 92, 0.3);
        }
        
        button {
            padding: 12px 25px; 
            border: none; 
            border-radius: 8px; 
            background-color: var(--rcb-secondary); 
            color: var(--rcb-accent); 
            cursor: pointer;
            font-weight: 600;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s;
            width: 100%;
            margin-top: 10px;
        }
        
        button:hover {
            background-color: #d4004f;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 0, 92, 0.4);
        }
        
        button:disabled { 
            background-color: var(--rcb-highlight); 
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        
        #fareResult { 
            margin-top: 25px; 
            font-weight: bold;
            padding: 15px;
            background-color: rgba(255, 0, 92, 0.1);
            border-left: 4px solid var(--rcb-secondary);
            border-radius: 0 8px 8px 0;
            color: var(--rcb-accent);
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 20px;
                margin: 10px;
            }
            
            h2 {
                font-size: 22px;
            }
            
            select, input[type="number"] {
                padding: 10px 12px;
            }
            
            button {
                padding: 10px 20px;
            }
        }
        
        /* RCB logo inspired decoration */
        .container::before {
            content: "";
            position: absolute;
            top: -10px;
            left: 20px;
            width: 40px;
            height: 40px;
            background: var(--rcb-secondary);
            border-radius: 50%;
            z-index: -1;
        }
        
        .container::after {
            content: "";
            position: absolute;
            bottom: -10px;
            right: 20px;
            width: 40px;
            height: 40px;
            background: var(--rcb-secondary);
            border-radius: 50%;
            z-index: -1;
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container">
    <h2>Buy Bus Ticket</h2>

    <div class="form-group">
        <label for="routeCode">Route Code</label>
        <select id="routeCode">
            <option value="">Select Route</option>
        </select>
    </div>

    <div class="form-group">
        <label for="fromStage">From Stage</label>
        <select id="fromStage" disabled>
            <option value="">Select From</option>
        </select>
    </div>

    <div class="form-group">
        <label for="toStage">To Stage</label>
        <select id="toStage" disabled>
            <option value="">Select To</option>
        </select>
    </div>

    <div class="form-group">
        <label for="busType">Bus Type</label>
        <select id="busType">
            <option value="">Select Bus Type</option>
            <option value="Regular">Regular</option>
            <option value="AC">AC</option>
            <option value="Deluxe">Deluxe</option>
        </select>
    </div>

    <div class="form-group">
        <label for="passengerCount">Passengers</label>
        <input type="number" id="passengerCount" min="1" value="1">
    </div>

    <button id="calculateBtn">Calculate Fare</button>

    <div id="fareResult"></div>

    <button id="buyBtn" disabled>Buy Ticket</button>
</div>

<script>
    const apiBase = "<?php echo $apiBaseUrl; ?>";

    // Populate route codes
    $(document).ready(function () {
        $.get(`${apiBase}GetAllRoutes`, function (routes) {
            routes.forEach(r => {
                $('#routeCode').append(`<option value="${r.code}">${r.code}</option>`);
            });
        });
    });

    // Load stages when route changes
    $('#routeCode').on('change', function () {
        const code = $(this).val();
        $('#fromStage, #toStage').empty().append('<option value="">Select</option>').prop('disabled', true);
        $('#fareResult').html('');
        $('#buyBtn').prop('disabled', true);

        if (code) {
            $.get(`${apiBase}GetRouteStagesByCode/${code}`, function (stages) {
                stages.stages.forEach(s => {
                    $('#fromStage, #toStage').append(`<option value="${s.stageName}">${s.stageName}</option>`);
                });
                $('#fromStage, #toStage').prop('disabled', false);
            });
        }
    });

    // Calculate fare
    $('#calculateBtn').on('click', function () {
        const routeCode = $('#routeCode').val();
        const from = $('#fromStage').val();
        const to = $('#toStage').val();
        const busType = $('#busType').val();
        const passengers = parseInt($('#passengerCount').val());

        if (!routeCode || !from || !to || !busType || !passengers || passengers < 1) {
            alert("Please fill all fields correctly.");
            return;
        }

        $.get(`${apiBase}CalculateFare/${routeCode}/${busType}/${from}/${to}`, function (fare) {
            const totalFare = fare.fare * passengers;
            $('#fareResult').html(`Fare per person: ₹${fare.fare}<br>Total Fare for ${passengers}: ₹${totalFare}`);
            $('#buyBtn').prop('disabled', false);
        }).fail(function () {
            alert("Could not calculate fare. Please check your inputs.");
        });
    });

    // Buy Ticket
    $('#buyBtn').on('click', function () {
        const routeCode = $('#routeCode').val();
        const from = $('#fromStage').val();
        const to = $('#toStage').val();
        const busType = $('#busType').val();
        const passengers = parseInt($('#passengerCount').val());

        const payload = {
            RouteCode: routeCode,
            FromStage: from,
            ToStage: to,
            BusType: busType,
            PassengerCount: passengers
        };

        $.ajax({
            url: `${apiBase}/BuyTicket`, // Adjust as per your actual backend endpoint
            method: "POST",
            contentType: "application/json",
            data: JSON.stringify(payload),
            success: function (response) {
                alert("Ticket booked successfully!");
                // Reset form
                $('#routeCode').val('');
                $('#fromStage').empty().append('<option value="">Select</option>').prop('disabled', true);
                $('#toStage').empty().append('<option value="">Select</option>').prop('disabled', true);
                $('#busType').val('');
                $('#passengerCount').val(1);
                $('#fareResult').html('');
                $('#buyBtn').prop('disabled', true);
            },
            error: function () {
                alert("Failed to book ticket. Please try again.");
            }
        });
    });
</script>

</body>
</html>