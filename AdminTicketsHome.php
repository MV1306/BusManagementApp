<?php
    require_once 'AdminAuth.php';
    checkAuth();
    $config = include('config.php');
    $apiBaseUrl = $config['api_base_url'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Bus Management System - Tickets</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
:root {
    --primary: #4361ee;
    --primary-dark: #3a56d4;
    --secondary: #3f37c9;
    --accent: #4895ef;
    --light: #f8f9fa;
    --dark: #212529;
    --gray: #6c757d;
    --light-gray: #e9ecef;
    --success: #4cc9f0;
    --warning: #f72585;
    --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    --transition: all 0.3s ease;
    --gradient: linear-gradient(135deg, var(--primary), var(--secondary));
}

* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

body {
    background-color: #f5f7ff;
    color: var(--dark);
    line-height: 1.6;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

/* Header Styles */
header {
    background: var(--gradient);
    color: white;
    padding: 3rem 1rem;
    text-align: center;
    position: relative;
    overflow: hidden;
}

header::before {
    content: '';
    position: absolute;
    top: -50px;
    right: -50px;
    width: 200px;
    height: 200px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
}

header::after {
    content: '';
    position: absolute;
    bottom: -80px;
    left: -80px;
    width: 300px;
    height: 300px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 50%;
}

header h1 {
    font-size: 2.5rem;
    margin-bottom: 0.5rem;
    font-weight: 700;
    position: relative;
    z-index: 1;
}

header p {
    font-size: 1.1rem;
    opacity: 0.9;
    max-width: 800px;
    margin: 0 auto;
    position: relative;
    z-index: 1;
}

/* Main Content */
.main-content {
    flex: 1;
    padding: 3rem 1rem;
    max-width: 1200px;
    margin: 0 auto;
    width: 100%;
}

.section-title {
    font-size: 1.75rem;
    color: var(--primary);
    margin-bottom: 2rem;
    position: relative;
    padding-bottom: 0.75rem;
    font-weight: 600;
}

.section-title::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 80px;
    height: 4px;
    background: var(--gradient);
    border-radius: 2px;
}

/* Card Grid */
.card-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.ticket-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: var(--card-shadow);
    transition: var(--transition);
    border: none;
    display: flex;
    flex-direction: column;
    position: relative;
    overflow: hidden;
}

.ticket-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 8px;
    background: var(--gradient);
}

.ticket-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
}

.card-icon-container {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: rgba(67, 97, 238, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 2rem auto 1.5rem;
}

.card-icon {
    font-size: 2.5rem;
    color: var(--primary);
}

.card-body {
    padding: 0 2rem 2rem;
    flex: 1;
    display: flex;
    flex-direction: column;
    text-align: center;
}

.card-title {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: var(--dark);
}

.card-text {
    color: var(--gray);
    margin-bottom: 2rem;
    flex: 1;
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: var(--gradient);
    color: white;
    padding: 0.75rem 1.75rem;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    transition: var(--transition);
    border: none;
    cursor: pointer;
    width: fit-content;
    margin: 0 auto;
    position: relative;
    overflow: hidden;
}

.btn::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.1);
    transform: translateX(-100%);
    transition: transform 0.3s ease;
}

.btn:hover {
    color: white;
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
}

.btn:hover::after {
    transform: translateX(0);
}

.btn i {
    margin-right: 0.5rem;
}

/* Footer */
footer {
    background-color: var(--dark);
    color: white;
    text-align: center;
    padding: 2rem;
    margin-top: auto;
}

.footer-content {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.social-links {
    display: flex;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.social-links a {
    color: white;
    font-size: 1.5rem;
    transition: var(--transition);
}

.social-links a:hover {
    color: var(--accent);
    transform: translateY(-3px);
}

.copyright {
    font-size: 0.9rem;
    opacity: 0.8;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    header h1 {
        font-size: 2rem;
    }
    
    .section-title {
        font-size: 1.5rem;
    }
    
    .card-grid {
        grid-template-columns: 1fr;
    }
    
    .card-icon-container {
        width: 70px;
        height: 70px;
    }
    
    .card-icon {
        font-size: 2rem;
    }
}

@media (max-width: 480px) {
    header h1 {
        font-size: 1.75rem;
    }
    
    header p {
        font-size: 1rem;
    }
    
    .card-title {
        font-size: 1.3rem;
    }
    
    .card-text {
        font-size: 0.9rem;
    }
    
    .btn {
        padding: 0.6rem 1.2rem;
        font-size: 0.9rem;
    }
}
    </style>
</head>
<body>
    <?php include 'AdminNavbar.php'; ?>

    <header>
        <h1>Ticket Management</h1>
        <p>Your hub for issuing new tickets and managing bookings</p>
    </header>

    <main class="main-content">
        <section>
            <h2 class="section-title">Ticket Actions</h2>
            <div class="card-grid">
                <div class="ticket-card">
                    <div class="card-icon-container">
                        <i class="fas fa-ticket-alt card-icon"></i>
                    </div>
                    <div class="card-body">
                        <h3 class="card-title">Issue New Ticket</h3>
                        <p class="card-text">Purchase bus tickets for your desired routes with our secure payment system and instant confirmation.</p>
                        <a href="AdminBuyTicket.php" class="btn">
                            <i class="fas fa-shopping-cart"></i> Buy Ticket
                        </a>
                    </div>
                </div>
                
                <div class="ticket-card">
                    <div class="card-icon-container">
                        <i class="fas fa-receipt card-icon"></i>
                    </div>
                    <div class="card-body">
                        <h3 class="card-title">View My Tickets</h3>
                        <p class="card-text">Access your booking history, view upcoming trips, and manage your existing reservations.</p>
                        <a href="AdminSearchTickets.php" class="btn">
                            <i class="fas fa-eye"></i> View Tickets
                        </a>
                    </div>
                </div>

                <div class="ticket-card">
                    <div class="card-icon-container">
                        <i class="fas fa-receipt card-icon"></i>
                    </div>
                    <div class="card-body">
                        <h3 class="card-title">Redeem Tickets</h3>
                        <p class="card-text">Access your booking history, view upcoming trips, and manage your existing reservations.</p>
                        <a href="RedeemTicket.php" class="btn">
                            <i class="fas fa-times"></i> Redeem Tickets
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="footer-content">
            <div class="social-links">
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-linkedin"></i></a>
            </div>
            <p class="copyright">&copy; <?php echo date("Y"); ?> Bus Management System. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>