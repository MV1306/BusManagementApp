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
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f4f4; padding: 20px; }
        .container { max-width: 600px; margin: auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { color: #1a1a2e; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { font-weight: bold; }
        select, input[type="number"] {
            width: 100%; padding: 8px; margin-top: 5px; border-radius: 5px; border: 1px solid #ccc;
        }
        button {
            padding: 10px 20px; border: none; border-radius: 5px; background-color: #e94560; color: white; cursor: pointer;
        }
        button:disabled { background-color: #ccc; cursor: not-allowed; }
        #fareResult { margin-top: 20px; font-weight: bold; }
    </style>
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
