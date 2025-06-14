<?php
require_once 'AdminAuth.php';

checkAuth();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Bus Management System Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4361ee;
            --primary-dark: #3a56d4;
            --secondary: #3f37c9;
            --dark: #1a1a2e;
            --light: #f8f9fa;
            --gray: #6c757d;
            --success: #4cc9f0;
            --card-bg: rgba(255, 255, 255, 0.05);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0f2f5;
            color: #1a1a2e;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 16px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 2rem 1rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 20% 50%, rgba(255,255,255,0.1) 0%, transparent 70%);
        }

        header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            position: relative;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        header p {
            font-weight: 300;
            opacity: 0.9;
            position: relative;
        }

        .container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            max-width: 1200px;
            width: 90%;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: white;
            color: var(--primary);
            font-weight: 600;
            font-size: 1rem;
            padding: 1.5rem 1rem;
            border-radius: 12px;
            text-decoration: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            cursor: pointer;
            user-select: none;
            gap: 0.75rem;
            border: none;
            text-align: center;
            height: 100%;
        }

        .btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(67, 97, 238, 0.2);
            background: var(--primary);
            color: white;
        }

        .btn i {
            font-size: 1.75rem;
        }

        footer {
            margin-top: auto;
            padding: 1.5rem;
            background: white;
            color: var(--gray);
            font-size: 0.875rem;
            text-align: center;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.03);
        }

        /* Modern navbar styles */
        nav {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 0.75rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-brand {
            font-weight: 700;
            color: var(--primary);
            font-size: 1.25rem;
            text-decoration: none;
        }

        .nav-user {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            header h1 {
                font-size: 2rem;
            }
            
            .container {
                grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
                gap: 1rem;
            }
            
            .btn {
                padding: 1.25rem 0.75rem;
                font-size: 0.875rem;
            }
            
            .btn i {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 480px) {
            nav {
                padding: 0.75rem 1rem;
            }
            
            header h1 {
                font-size: 1.75rem;
            }
            
            .container {
                grid-template-columns: 1fr 1fr;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body>
<?php include 'AdminNavbar.php'; ?>

<header>
    <h1>Bus Management Dashboard</h1>
    <p>Manage your transportation network efficiently</p>
</header>

<div class="container">
    <a class="btn glass-card" href="AddRoute.php">
        <i class="fas fa-route"></i>
        Add New Route
    </a>
    <a class="btn glass-card" href="AdminViewRoutes.php">
        <i class="fas fa-list-alt"></i>
        View All Routes
    </a>
    <a class="btn glass-card" href="AdminFindRoutes.php">
        <i class="fas fa-search"></i>
        Find Routes
    </a>
    <a class="btn glass-card" href="AdminTicketsHome.php">
        <i class="fas fa-ticket"></i>
        Tickets
    </a>
    <a class="btn glass-card" href="AdminCalculateFare.php">
        <i class="fas fa-calculator"></i>
        Calculate Fare
    </a>
    <a class="btn glass-card" href="ImportRoutes.php">
        <i class="fas fa-file-import"></i>
        Import Data
    </a>    
    <a class="btn glass-card" href="Reports.php">
        <i class="fas fa-chart-bar"></i>
        Analytics Reports
    </a>
    <a class="btn glass-card" href="AdminFareChart.php">
        <i class="fas fa-money-bill-wave"></i>
        Fare Chart
    </a>
</div>

<footer>
    &copy; <?php echo date("Y"); ?> Bus Management System. All rights reserved.
</footer>

</body>
</html>