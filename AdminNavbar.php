<!-- navbar.php -->
<style>
    :root {
        --navbar-bg: #0f172a;
        --navbar-text: #f8fafc;
        --navbar-hover: #334155;
        --navbar-active: #6366f1;
        --navbar-accent: #818cf8;
        --navbar-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    body {
        margin: 0;
        padding: 0;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    .navbar {
        background-color: var(--navbar-bg);
        color: var(--navbar-text);
        padding: 0.75rem 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: sticky;
        top: 0;
        z-index: 1000;
        box-shadow: var(--navbar-shadow);
    }

    .navbar-container {
        width: 100%;
        max-width: 1400px;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .brand {
        font-size: 1.25rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: white;
        text-decoration: none;
    }

    .brand-icon {
        color: var(--navbar-accent);
        font-size: 1.5rem;
    }

    .nav-links {
        display: flex;
        gap: 1.5rem;
        align-items: center;
    }

    .nav-links a {
        color: var(--navbar-text);
        text-decoration: none;
        font-size: 0.95rem;
        font-weight: 500;
        padding: 0.75rem 0;
        position: relative;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        opacity: 0.9;
        transition: var(--transition);
    }

    .nav-links a:hover {
        opacity: 1;
        color: var(--navbar-accent);
    }

    .nav-links a::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 0;
        height: 2px;
        background-color: var(--navbar-active);
        transition: var(--transition);
    }

    .nav-links a:hover::after,
    .nav-links a.active::after {
        width: 100%;
    }

    .nav-links a.active {
        color: var(--navbar-accent);
        opacity: 1;
    }

    .nav-links a i {
        font-size: 1.1rem;
    }

    .hamburger {
        display: none;
        flex-direction: column;
        cursor: pointer;
        padding: 0.5rem;
        border-radius: 0.375rem;
        transition: var(--transition);
    }

    .hamburger:hover {
        background-color: var(--navbar-hover);
    }

    .hamburger div {
        width: 24px;
        height: 2px;
        background: var(--navbar-text);
        margin: 0.25rem 0;
        transition: var(--transition);
        border-radius: 2px;
    }

    .hamburger.active div:nth-child(1) {
        transform: translateY(6px) rotate(45deg);
    }

    .hamburger.active div:nth-child(2) {
        opacity: 0;
    }

    .hamburger.active div:nth-child(3) {
        transform: translateY(-6px) rotate(-45deg);
    }

    @media (max-width: 768px) {
        .navbar {
            padding: 0.75rem 1.5rem;
        }
        
        .nav-links {
            position: fixed;
            top: 60px;
            left: 0;
            right: 0;
            background-color: var(--navbar-bg);
            flex-direction: column;
            gap: 0;
            padding: 0;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
            box-shadow: var(--navbar-shadow);
        }

        .nav-links.active {
            max-height: 500px;
            padding: 1rem 0;
        }

        .nav-links a {
            width: 100%;
            padding: 1rem 2rem;
            justify-content: flex-start;
        }

        .nav-links a:hover {
            background-color: var(--navbar-hover);
        }

        .nav-links a::after {
            display: none;
        }

        .hamburger {
            display: flex;
        }
    }
</style>

<script>
    function toggleMenu() {
        const hamburger = document.querySelector('.hamburger');
        const navLinks = document.querySelector('.nav-links');
        hamburger.classList.toggle('active');
        navLinks.classList.toggle('active');
    }

    // Close menu when clicking on a link (for mobile)
    document.querySelectorAll('.nav-links a').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 768) {
                toggleMenu();
            }
        });
    });

    // Highlight active page
    document.addEventListener('DOMContentLoaded', () => {
        const currentPage = location.pathname.split('/').pop();
        document.querySelectorAll('.nav-links a').forEach(link => {
            if (link.getAttribute('href') === currentPage) {
                link.classList.add('active');
            }
        });
    });
</script>

<div class="navbar">
    <div class="navbar-container">
        <a href="AdminDashboard.php" class="brand">
            <i class="bi bi-bus-front brand-icon"></i>
            <span>Bus Manager</span>
        </a>
        
        <div class="hamburger" onclick="toggleMenu()">
            <div></div>
            <div></div>
            <div></div>
        </div>
        
        <div class="nav-links">
            <a href="AdminDashboard.php">
                <i class="bi bi-house-door"></i>
                <span>Home</span>
            </a>
            <a href="AdminTicketsHome.php">
                <i class="bi bi-ticket"></i>
                <span>Tickets</span>
            </a>
            <a href="AdminViewRoutes.php">
                <i class="bi bi-map"></i>
                <span>View Routes</span>
            </a>
            <a href="AdminFindRoutes.php">
                <i class="bi bi-search"></i>
                <span>Find Routes</span>
            </a>
            <a href="ImportRoutes.php">
                <i class="bi bi-upload"></i>
                <span>Import Routes</span>
            </a>
            <a href="AdminCalculateFare.php">
                <i class="bi bi-calculator"></i>
                <span>Calculate Fare</span>
            </a>
            <a href="AdminLogout.php">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>
</div>