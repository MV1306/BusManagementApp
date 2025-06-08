<!-- navbar.php -->
<style>
    :root {
        --primary-bg: #0f172a;
        --primary-text: #f8fafc;
        --accent-color: #3b82f6;
        --hover-accent: #60a5fa;
        --transition-speed: 0.3s;
        --nav-height: 70px;
    }

    body {
        margin: 0;
        padding: 0;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    .navbar {
        background-color: var(--primary-bg);
        color: var(--primary-text);
        padding: 0 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: sticky;
        top: 0;
        z-index: 1000;
        height: var(--nav-height);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .navbar .brand {
        font-size: 1.5rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .brand-icon {
        width: 24px;
        height: 24px;
        fill: var(--accent-color);
    }

    .navbar .links {
        display: flex;
        gap: 1.5rem;
    }

    .navbar .links a {
        color: var(--primary-text);
        text-decoration: none;
        font-size: 1rem;
        font-weight: 500;
        position: relative;
        padding: 0.5rem 0;
        transition: color var(--transition-speed) ease;
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }

    .navbar .links a:hover {
        color: var(--hover-accent);
    }

    .navbar .links a::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        height: 2px;
        width: 0;
        background: var(--accent-color);
        transition: width var(--transition-speed) ease;
    }

    .navbar .links a:hover::after {
        width: 100%;
    }

    .navbar .links a.active {
        color: var(--accent-color);
    }

    .navbar .hamburger {
        display: none;
        flex-direction: column;
        cursor: pointer;
        padding: 0.5rem;
        border-radius: 0.25rem;
        transition: background-color var(--transition-speed) ease;
    }

    .navbar .hamburger:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }

    .navbar .hamburger div {
        width: 25px;
        height: 2px;
        background: white;
        margin: 4px 0;
        transition: all var(--transition-speed) ease;
    }

    .navbar .hamburger.active div:nth-child(1) {
        transform: translateY(6px) rotate(45deg);
    }

    .navbar .hamburger.active div:nth-child(2) {
        opacity: 0;
    }

    .navbar .hamburger.active div:nth-child(3) {
        transform: translateY(-6px) rotate(-45deg);
    }

    @media (max-width: 768px) {
        .navbar {
            padding: 0 1rem;
        }

        .navbar .links {
            display: none;
            flex-direction: column;
            background: var(--primary-bg);
            width: 100%;
            position: absolute;
            top: var(--nav-height);
            left: 0;
            padding: 1rem 2rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .navbar .links.active {
            display: flex;
        }

        .navbar .links a {
            padding: 0.75rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .navbar .links a:last-child {
            border-bottom: none;
        }

        .navbar .hamburger {
            display: flex;
        }
    }
</style>

<script>
    function toggleMenu() {
        const hamburger = document.querySelector('.navbar .hamburger');
        const links = document.querySelector('.navbar .links');
        hamburger.classList.toggle('active');
        links.classList.toggle('active');
    }

    // Highlight current page link
    document.addEventListener('DOMContentLoaded', function() {
        const currentPage = window.location.pathname.split('/').pop();
        const links = document.querySelectorAll('.navbar .links a');
        
        links.forEach(link => {
            const linkHref = link.getAttribute('href');
            if (currentPage === linkHref) {
                link.classList.add('active');
            }
        });
    });
</script>

<div class="navbar">
    <div class="brand">
        <svg class="brand-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M18 10H6V5H18M16 2H8L4 6V19C4 20.1 4.9 21 6 21H18C19.1 21 20 20.1 20 19V6L16 2M11 17H8V14H11V17M16 17H13V14H16V17Z"/>
        </svg>
        <span>Bus Manager</span>
    </div>
    <div class="hamburger" onclick="toggleMenu()">
        <div></div>
        <div></div>
        <div></div>
    </div>
    <div class="links">
        <a href="Index.php">
            <svg style="width:18px;height:18px" viewBox="0 0 24 24">
                <path fill="currentColor" d="M10,20V14H14V20H19V12H22L12,3L2,12H5V20H10Z" />
            </svg>
            Home
        </a>
        <a href="ViewRoutes.php">
            <svg style="width:18px;height:18px" viewBox="0 0 24 24">
                <path fill="currentColor" d="M5,6H23V18H5V6M14,9A3,3 0 0,1 17,12A3,3 0 0,1 14,15A3,3 0 0,1 11,12A3,3 0 0,1 14,9M9,8A2,2 0 0,1 7,10V14A2,2 0 0,1 9,16H19A2,2 0 0,1 21,14V10A2,2 0 0,1 19,8H9M1,10H3V20H19V22H1V10Z" />
            </svg>
            View Routes
        </a>
        <a href="FindRoutes.php">
            <svg style="width:18px;height:18px" viewBox="0 0 24 24">
                <path fill="currentColor" d="M9,2A7,7 0 0,1 16,9C16,10.57 15.5,12 14.61,13.19L15.41,14H16L22,20L20,22L14,16V15.41L13.19,14.61C12,15.5 10.57,16 9,16A7,7 0 0,1 2,9A7,7 0 0,1 9,2M8,5V8H5V10H8V13H10V10H13V8H10V5H8Z" />
            </svg>
            Find Routes
        </a>
        <a href="CalculateFare.php">
            <svg style="width:18px;height:18px" viewBox="0 0 24 24">
                <path fill="currentColor" d="M5,6H23V18H5V6M14,9A3,3 0 0,1 17,12A3,3 0 0,1 14,15A3,3 0 0,1 11,12A3,3 0 0,1 14,9M9,8A2,2 0 0,1 7,10V14A2,2 0 0,1 9,16H19A2,2 0 0,1 21,14V10A2,2 0 0,1 19,8H9M1,10H3V20H19V22H1V10Z" />
            </svg>
            Calculate Fare
        </a>
        <a href="AdminLogin.php">
            <svg style="width:18px;height:18px" viewBox="0 0 24 24">
                <path fill="currentColor" d="M12,3A4,4 0 0,1 16,7A4,4 0 0,1 12,11A4,4 0 0,1 8,7A4,4 0 0,1 12,3M12,13C16.42,13 20,14.79 20,17V20H4V17C4,14.79 7.58,13 12,13Z" />
            </svg>
            Admin
        </a>
    </div>
</div>