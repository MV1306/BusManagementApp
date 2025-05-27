<!-- navbar.php -->
<style>
    body {
        margin: 0;
        padding: 0;
    }

    .navbar {
        /* width: 100%; */
        background-color: #1a1a2e;
        color: white;
        font-family: 'Segoe UI', sans-serif;
        padding: 10px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: sticky;
        top: 0;
        z-index: 1000;
    }

    .navbar .brand {
        font-size: 22px;
        font-weight: bold;
    }

    .navbar .links {
        display: flex;
        gap: 20px;
    }

    .navbar .links a {
        color: white;
        text-decoration: none;
        font-size: 16px;
        position: relative;
        padding: 6px 0;
    }

    .navbar .links a::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        height: 2px;
        width: 0;
        background: #e94560;
        transition: width 0.3s ease;
    }

    .navbar .links a:hover::after {
        width: 100%;
    }

    .navbar .hamburger {
        display: none;
        flex-direction: column;
        cursor: pointer;
    }

    .navbar .hamburger div {
        width: 25px;
        height: 3px;
        background: white;
        margin: 4px 0;
    }

    @media (max-width: 768px) {
        .navbar .links {
            display: none;
            flex-direction: column;
            background: #1a1a2e;
            width: 100%;
            position: absolute;
            top: 60px;
            left: 0;
            padding: 10px 20px;
        }

        .navbar .links a {
            margin: 10px 0;
        }

        .navbar .links.active {
            display: flex;
        }

        .navbar .hamburger {
            display: flex;
        }
    }
</style>

<script>
    function toggleMenu() {
        const links = document.querySelector('.navbar .links');
        links.classList.toggle('active');
    }
</script>

<div class="navbar">
    <div class="brand">Bus Management</div>
    <div class="hamburger" onclick="toggleMenu()">
        <div></div>
        <div></div>
        <div></div>
    </div>
    <div class="links">
        <a href="Index.php">Home</a>
        <a href="ViewRoutes.php">View Route</a>
        <a href="FindRoutes.php">Find Routes</a>
        <a href="CalculateFare.php">Calculate Fare</a>
        <a href="Admin.php">Admin</a>
    </div>
</div>
