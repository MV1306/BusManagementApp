<!DOCTYPE html>
<html>
<head>
    <title>Bus Management System - Home</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            text-align: center;
        }
        header {
            background-color: #007bff;
            color: white;
            padding: 20px;
        }
        h1 {
            margin: 0;
        }
        .container {
            margin-top: 50px;
        }
        .btn {
            display: inline-block;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            margin: 10px;
            border-radius: 6px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        .btn:hover {
            background-color: #218838;
        }
        footer {
            margin-top: 80px;
            padding: 20px;
            color: #888;
        }
    </style>
</head>
<body>

<header>
    <h1>Welcome to Bus Management System</h1>
</header>

<div class="container">
    <a class="btn" href="AddRoute.php">➕ Add New Route</a>
    <a class="btn" href="ViewRoutes.php">📋 View Routes</a>
    <a class="btn" href="FindRoutes.php">📋 Find Routes</a>
    <a class="btn" href="CalculateFare.php">🔢 Calculate Fare</a>
    <a class="btn" href="Reports.php">📊 Reports</a>
    <a class="btn" href="FareChart.php">💰 Fare Chart</a>
</div>

<footer>
    &copy; <?php echo date("Y"); ?> Bus Management System
</footer>

</body>
</html>
