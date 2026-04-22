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
        * { box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f4f4f4 0%, #e0e0e0 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-box {
            background: #fff;
            padding: 40px 30px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            width: 350px;
            animation: fadeIn 0.6s cubic-bezier(0.25, 0.8, 0.25, 1);
        }
        .login-box h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #2c7a7b;
            font-weight: 600;
        }
        
        .input-group {
            position: relative;
            margin-bottom: 25px;
        }
        .input-group input {
            width: 100%;
            padding: 12px 60px 12px 15px; 
            border: 2px solid #ddd;
            border-radius: 6px;
            outline: none;
            background: transparent;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        .input-group label {
            position: absolute;
            top: 50%;
            left: 15px;
            transform: translateY(-50%);
            color: #999;
            font-size: 16px;
            pointer-events: none;
            transition: 0.3s ease;
            background: #fff;
            padding: 0 5px;
        }
        .input-group input:focus, 
        .input-group input:not(:placeholder-shown) {
            border-color: #2c7a7b;
        }
        .input-group input:focus ~ label, 
        .input-group input:not(:placeholder-shown) ~ label {
            top: 0;
            font-size: 12px;
            color: #2c7a7b;
        }
        .input-group input:valid:not(:placeholder-shown) {
            border-color: #4CAF50;
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 13px;
            font-weight: bold;
            user-select: none;
            color: #666;
            transition: color 0.2s;
            background: #fff;
        }
        .toggle-password:hover { color: #2c7a7b; }

        .strength-container { margin-top: -10px; margin-bottom: 20px; }
        #strengthBar {
            height: 6px;
            width: 0%;
            background: #ddd;
            border-radius: 3px;
            transition: width 0.4s ease, background 0.4s ease;
        }
        #strengthText {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
            display: block;
            text-align: right;
            min-height: 15px;
        }

        .remember {
            display: flex;
            align-items: center;
            font-size: 14px;
            margin-bottom: 20px;
            color: #555;
            cursor: pointer;
        }
        .remember input { margin-right: 8px; cursor: pointer; }
        .login-box button {
            width: 100%;
            padding: 12px;
            background: #2c7a7b;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.3s, transform 0.1s;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .login-box button:hover { background: #225e5f; }
        .login-box button:active { transform: scale(0.98); }
        .login-box button:disabled { background: #71b1b2; cursor: not-allowed; }
        
        .loader {
            display: none;
            border: 3px solid rgba(255,255,255,0.3);
            border-top: 3px solid white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
            margin-left: 10px;
        }

        .error {
            color: #e53e3e;
            background: #fed7d7;
            padding: 10px;
            border-radius: 6px;
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
            animation: shake 0.4s;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-8px); }
            50% { transform: translateX(8px); }
            75% { transform: translateX(-8px); }
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Welcome Back</h2>
        <form method="POST" onsubmit="handleFormSubmit()">
            
            <div class="input-group">
                <input type="email" id="email" name="email" placeholder=" " required>
                <label for="email">Email Address</label>
            </div>

            <div class="input-group">
                <input type="password" id="password" name="password" placeholder=" " required>
                <label for="password">Password</label>
                <span class="toggle-password" onclick="togglePassword()" id="toggleText">Show</span>
            </div>
            
            <div class="strength-container">
                <div id="strengthBar"></div>
                <span id="strengthText"></span>
            </div>

            <label class="remember">
                <input type="checkbox" id="rememberMe"> Remember Me
            </label>

            <button type="submit" id="loginBtn">
                <span id="btnText">Login</span>
                <div class="loader" id="btnLoader"></div>
            </button>
        </form>
        
        <?php if (isset($error)) { echo "<div class='error'>$error</div>"; } ?>
    </div>

    <script>
        function togglePassword() {
            const passField = document.getElementById('password');
            const toggleText = document.getElementById('toggleText');
            if (passField.type === "password") {
                passField.type = "text";
                toggleText.innerText = "Hide"; 
            } else {
                passField.type = "password";
                toggleText.innerText = "Show"; 
            }
        }

        function handleFormSubmit() {
            const emailField = document.getElementById('email');
            const remember = document.getElementById('rememberMe').checked;
            const btn = document.getElementById("loginBtn");
            const btnText = document.getElementById("btnText");
            const loader = document.getElementById("btnLoader");

            if (remember) {
                localStorage.setItem('savedEmail', emailField.value);
            } else {
                localStorage.removeItem('savedEmail');
            }

        
            btnText.innerText = "Authenticating...";
            loader.style.display = "block";
            btn.disabled = true;
        }

    
        window.onload = function() {
            const savedEmail = localStorage.getItem('savedEmail');
            if (savedEmail) {
                document.getElementById('email').value = savedEmail;
                document.getElementById('rememberMe').checked = true;
            }
        }

        document.getElementById("password").addEventListener("input", function() {
            const bar = document.getElementById("strengthBar");
            const text = document.getElementById("strengthText");
            const val = this.value;
            let strength = 0;
            
            if (val.length > 0) {
                if (val.length > 5) strength++;
                if (/[A-Z]/.test(val)) strength++;
                if (/[0-9]/.test(val)) strength++;
                if (/[^A-Za-z0-9]/.test(val)) strength++;
            }

            const colors = ["#ddd", "#ff4d4d", "#ffa64d", "#c6da00", "#4CAF50"];
            const labels = ["", "Very Weak", "Weak", "Good", "Strong"];
            const widths = ["0%", "25%", "50%", "75%", "100%"];

            bar.style.width = widths[strength];
            bar.style.background = colors[strength];
            text.innerText = labels[strength];
            text.style.color = colors[strength];
        });
    </script>
</body>
</html>
