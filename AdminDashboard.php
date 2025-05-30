<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Bus Management System - Home</title>
    <style>
        /* Reset some default */
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            margin: 0;
            padding: 0;
            color: #fff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Navbar placeholder styling */
        /* You can customize navbar.php to match */
        nav {
            background-color: #0b2e6a;
            padding: 12px 24px;
            font-weight: 600;
            letter-spacing: 0.05em;
        }

        header {
            background-color: #145da0;
            padding: 40px 20px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        header h1 {
            margin: 0;
            font-size: 2.8rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            text-shadow: 1px 1px 4px rgba(0,0,0,0.5);
        }

        .container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 24px;
            max-width: 900px;
            width: 90%;
            margin: 40px auto 60px;
            padding: 0 10px;
        }

        .btn {
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255 255 255 / 0.15);
            border: 2px solid rgba(255 255 255 / 0.3);
            color: white;
            font-weight: 700;
            font-size: 1.1rem;
            padding: 18px 24px;
            border-radius: 12px;
            text-decoration: none;
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
            transition:
                background-color 0.3s ease,
                border-color 0.3s ease,
                box-shadow 0.3s ease,
                transform 0.2s ease;
            cursor: pointer;
            user-select: none;
            gap: 8px;
            /* for emoji and text spacing */
        }

        .btn:hover,
        .btn:focus {
            background: rgba(255 255 255 / 0.35);
            border-color: white;
            box-shadow: 0 12px 20px rgba(255 255 255, 0.4);
            transform: translateY(-4px);
            outline: none;
        }

        .btn:active {
            transform: translateY(-1px);
            box-shadow: 0 8px 10px rgba(255 255 255, 0.3);
        }

        footer {
            margin-top: auto;
            padding: 20px 10px;
            color: rgba(255 255 255 / 0.6);
            font-size: 0.9rem;
            text-align: center;
            background-color: #0b2e6a;
            box-shadow: inset 0 1px 0 rgba(255 255 255 / 0.1);
            user-select: none;
        }

        /* Responsive font sizes */
        @media (max-width: 600px) {
            header h1 {
                font-size: 2rem;
            }
            .btn {
                font-size: 1rem;
                padding: 14px 20px;
            }
        }
    </style>
</head>
<body>
<?php include 'AdminNavbar.php'; ?>

<header>
    <h1>Welcome to Bus Management System</h1>
</header>

<div class="container">
    <a class="btn" href="AddRoute.php">➕ Add New Route</a>
    <a class="btn" href="AdminViewRoutes.php">📋 View Routes</a>
    <a class="btn" href="FindRoutes.php">🔍 Find Routes</a>
    <a class="btn" href="CalculateFare.php">🔢 Calculate Fare</a>
    <a class="btn" href="Reports.php">📊 Reports</a>
    <a class="btn" href="FareChart.php">💰 Fare Chart</a>
</div>

<footer>
    &copy; <?php echo date("Y"); ?> Bus Management System
</footer>

</body>
</html>
