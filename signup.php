<?php
session_start();
include '../partials/_dbconnect.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $email    = $_POST["email"];
    $password = $_POST["password"];  

    $sql = "INSERT INTO users (username, email, password) 
            VALUES ('$username', '$email', '$password')";
    if (mysqli_query($conn, $sql)) {
        echo "<div class='alert success'>Signup successful! You can login now.</div>";
    } else {
        echo "<div class='alert danger'>Error: " . mysqli_error($conn) . "</div>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>User Signup</title>
  <style>
    body {
      background-color: #f4f4f9;
      font-family: Arial, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }
    .container {
      background: #fff;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      width: 350px;
    }
    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #2c7a7b;
    }
    input {
      width: 100%;
      padding: 10px;
      margin: 8px 0;
      border: 1px solid #ccc;
      border-radius: 4px;
    }
    button {
      width: 100%;
      padding: 10px;
      background: #2c7a7b;
      border: none;
      color: #fff;
      font-size: 16px;
      border-radius: 4px;
      cursor: pointer;
      font-weight: bold;
    }
    button:hover {
      background: #225e5f;
    }
    .alert {
      padding: 12px;
      border-radius: 5px;
      margin-bottom: 15px;
      text-align: center;
      font-weight: bold;
    }
    .alert.success { background: #d4edda; color: #155724; }
    .alert.danger { background: #f8d7da; color: #721c24; }
    .toggle-password {
      font-size: 12px;
      color: #007BFF;
      cursor: pointer;
      text-align: right;
      margin-top: -5px;
      margin-bottom: 10px;
    }
    .login-link {
      text-align: center;
      margin-top: 15px;
    }
    .login-link a {
      color: #007BFF;
      text-decoration: none;
    }
    .login-link a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Signup</h2>
    <form method="post">
      <input type="text" name="username" placeholder="Username" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" id="password" name="password" placeholder="Password" required>
      <div class="toggle-password" onclick="togglePassword()">Show/Hide Password</div>
      <button type="submit">Signup</button>
    </form>
    <div class="login-link">
      <a href="login.php">Already have an account? Login here</a>
    </div>
  </div>

  <script>
    function togglePassword() {
      const passField = document.getElementById('password');
      passField.type = passField.type === "password" ? "text" : "password";
    }
  </script>
</body>
</html>