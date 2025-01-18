<?php
include '../config/db.php'; // Include the database connection file
session_start(); // Start session to manage login state

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Hardcoded credentials for testing
    $valid_username = 'awaistahir01234@gmail.com';
    $valid_password = '12345678';

    // Validate credentials
    if ($username === $valid_username && $password === $valid_password) {
        // Set the session and redirect to dashboard if credentials match
        $_SESSION['admin'] = $username; // You can store any value in the session
        header("Location: dashboard.php"); // Redirect to dashboard on success
        exit;
    } else {
        // Error message for invalid login
        $error = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Family Tree</title>
    <style>
        /* Global Reset */
        body, h1, h2, p, ul, li, input, button {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: #f0f8ff;
            color: #333;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .container {
            display: flex;
            flex-wrap: wrap;
            width: 90%;
            max-width: 1200px;
            background: #fff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            overflow: hidden;
        }

        .left {
            flex: 1;
            background: #e3f2fd;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            padding: 40px;
            text-align: left;
        }

        .left img {
            max-width: 100%;
            height: auto;
            margin-bottom: 20px;
        }

        .left h1 {
            font-size: 2rem;
            color: #2c3e50;
            margin-bottom: 15px;
        }

        .left p {
            font-size: 1rem;
            line-height: 1.6;
            color: #555;
        }

        .right {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
            background: #f9f9f9;
        }

        .login-form {
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .login-form h2 {
            margin-bottom: 20px;
            font-size: 1.5rem;
            color: #2c3e50;
        }

        .login-form input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            font-size: 1rem;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .login-form button {
            width: 100%;
            padding: 10px;
            background: #28a745;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            margin-top: 10px;
        }

        .login-form button:hover {
            background: #218838;
        }

        .login-form p {
            margin-top: 15px;
            font-size: 0.9rem;
        }

        .login-form p a {
            color: #007bff;
            text-decoration: none;
        }

        .login-form p a:hover {
            text-decoration: underline;
        }

        .error {
            color: #dc3545;
            font-size: 0.9rem;
            margin-top: 10px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }

            .left, .right {
                padding: 20px;
            }

            .left h1 {
                font-size: 1.8rem;
            }

            .left p {
                font-size: 0.95rem;
            }

            .login-form h2 {
                font-size: 1.3rem;
            }

            .login-form button {
                font-size: 0.95rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Left Section -->
        <div class="left">
            <img src="https://cdn.creazilla.com/cliparts/7538/multi-generational-family-clipart-md.png" alt="Family Tree Illustration">
            <h1>Building Family Tree.</h1>
            <p>
                Make your family tree live with Puerto Family Tree and do not leave it
                just a memory hanging. Build it with the participation of everyone and
                make it stretch to infinity.
            </p>
        </div>

        <!-- Right Section -->
        <div class="right">
            <div class="login-form">
                <h2>Admin Login</h2>
                <form action="" method="POST">
                    <input type="text" name="username" placeholder="Enter your email" required>
                    <input type="password" name="password" placeholder="Enter your password" required>
                    <button type="submit">Sign In</button>
                </form>
                <?php if (isset($error)): ?>
                    <p class="error"><?= htmlspecialchars($error) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
