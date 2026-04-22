<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'partials/_dbconnect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT id, username, email, password, role 
            FROM users 
            WHERE email='$email' 
            LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);

        if ($password === $row['password']) { 
            $_SESSION['user_id']  = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role']     = $row['role'];
            if ($row['role'] === 'admin') {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "No user found with that email!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-box {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
            width: 320px;
        }
        .login-box h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #2c7a7b;
        }
        .login-box input {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .login-box button {
            width: 100%;
            padding: 10px;
            background: #2c7a7b;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        .login-box button:hover {
            background: #225e5f;
        }
        .error {
            color: red;
            text-align: center;
            margin-top: 10px;
        }
        .toggle-password {
            font-size: 12px;
            color: #007BFF;
            cursor: pointer;
            text-align: right;
            margin-top: -5px;
            margin-bottom: 10px;
        }
        .remember {
            display: flex;
            align-items: center;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .remember input {
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Login</h2>
        <form method="POST" onsubmit="handleRememberMe()">
            <input type="email" id="email" name="email" placeholder="Email" required>
            <input type="password" id="password" name="password" placeholder="Password" required>
            <div class="toggle-password" onclick="togglePassword()">Show/Hide Password</div>
            <div class="remember">
                <input type="checkbox" id="rememberMe"> <label for="rememberMe">Remember Me</label>
            </div>
            <button type="submit">Login</button>
        </form>
        <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
    </div>

    <script>
        // Toggle password visibility
        function togglePassword() {
            const passField = document.getElementById('password');
            passField.type = passField.type === "password" ? "text" : "password";
        }

        // Handle Remember Me
        function handleRememberMe() {
            const emailField = document.getElementById('email');
            const remember = document.getElementById('rememberMe').checked;
            if (remember) {
                localStorage.setItem('savedEmail', emailField.value);
            } else {
                localStorage.removeItem('savedEmail');
            }
        }

        // Pre-fill email if saved
        window.onload = function() {
            const savedEmail = localStorage.getItem('savedEmail');
            if (savedEmail) {
                document.getElementById('email').value = savedEmail;
                document.getElementById('rememberMe').checked = true;
            }
        }
    </script>
</body>
</html>