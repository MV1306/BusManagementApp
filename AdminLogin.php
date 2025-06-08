<?php
session_start();

// Define valid credentials
$valid_username = 'admin';
$valid_password = 'Welcome@123';

// Initialize variables
$username = '';
$password = '';
$error = '';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Validate credentials
    if ($username === $valid_username && $password === $valid_password) {
        // Authentication successful - set session and redirect
        $_SESSION['authenticated'] = true;
        $_SESSION['username'] = $username;
        header('Location: AdminDashboard.php');
        exit;
    } else {
        $error = 'Invalid username or password';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #6366f1;
            --primary-hover: #4f46e5;
            --dark-color: #1e293b;
            --light-color: #f8fafc;
            --gray-color: #94a3b8;
            --error-color: #ef4444;
            --border-radius: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #f1f5f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--dark-color);
        }

        .login-container {
            width: 100%;
            max-width: 420px;
            padding: 1rem;
        }

        .login-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border: none;
        }

        .login-header {
            padding: 2rem 2rem 1rem;
            text-align: center;
        }

        .login-header h2 {
            font-weight: 700;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            color: var(--dark-color);
        }

        .login-header p {
            color: var(--gray-color);
            font-size: 0.875rem;
        }

        .login-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background-color: rgba(99, 102, 241, 0.1);
            margin-bottom: 1rem;
        }

        .login-icon i {
            font-size: 1.5rem;
            color: var(--primary-color);
        }

        .login-body {
            padding: 1.5rem 2rem 2rem;
        }

        .form-control {
            height: 48px;
            border-radius: var(--border-radius);
            border: 1px solid #e2e8f0;
            padding: 0.5rem 1rem;
            transition: var(--transition);
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        }

        .input-group-text {
            background-color: transparent;
            border-right: none;
            color: var(--gray-color);
        }

        .input-group .form-control {
            border-left: none;
        }

        .btn-login {
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            height: 48px;
            font-weight: 600;
            width: 100%;
            transition: var(--transition);
        }

        .btn-login:hover {
            background-color: var(--primary-hover);
            transform: translateY(-1px);
        }

        .btn-back {
            background-color: white;
            color: var(--dark-color);
            border: 1px solid #e2e8f0;
            border-radius: var(--border-radius);
            height: 48px;
            font-weight: 600;
            width: 100%;
            transition: var(--transition);
        }

        .btn-back:hover {
            background-color: #f8fafc;
            border-color: #cbd5e1;
        }

        .alert {
            border-radius: var(--border-radius);
        }

        .form-label {
            font-weight: 500;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
            display: block;
            color: var(--dark-color);
        }

        .button-group {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .floating-input {
            position: relative;
        }

        .floating-input label {
            position: absolute;
            top: 15px;
            left: 15px;
            transition: var(--transition);
            pointer-events: none;
            color: var(--gray-color);
            font-size: 0.875rem;
        }

        .floating-input input:focus + label,
        .floating-input input:not(:placeholder-shown) + label {
            transform: translateY(-24px) scale(0.85);
            background: white;
            padding: 0 4px;
            left: 11px;
            color: var(--primary-color);
        }

        @media (max-width: 576px) {
            .login-container {
                padding: 1rem;
            }
            
            .login-header,
            .login-body {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-icon">
                    <i class="bi bi-shield-lock"></i>
                </div>
                <h2>Admin Portal</h2>
                <p>Enter your credentials to access the dashboard</p>
            </div>
            <div class="login-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-exclamation-circle-fill me-2"></i>
                            <div><?php echo htmlspecialchars($error); ?></div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <div class="mb-3 floating-input">
                        <input type="text" class="form-control" id="username" name="username" 
                               value="<?php echo htmlspecialchars($username); ?>" 
                               placeholder=" " required autofocus>
                        <label for="username">Username</label>
                    </div>
                    
                    <div class="mb-3 floating-input">
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder=" " required>
                        <label for="password">Password</label>
                    </div>
                    
                    <div class="button-group">
                        <button type="submit" class="btn btn-login">
                            <i class="bi bi-box-arrow-in-right me-2"></i> Sign In
                        </button>
                        <a href="Index.php" class="btn btn-back">
                            <i class="bi bi-arrow-left me-2"></i> Back to Home
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Simple client-side validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();
            
            if (!username || !password) {
                e.preventDefault();
                alert('Please fill in both username and password');
            }
        });
        
        // Add floating label functionality
        document.querySelectorAll('.floating-input input').forEach(input => {
            // Trigger the check on page load if there's a value
            if (input.value) {
                input.dispatchEvent(new Event('input'));
            }
        });
    </script>
</body>
</html>