<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Navbar</title>
  <style>
    nav {
      background: #333;
      padding: 12px 20px;
      position: relative;
    }
    nav .navbar-brand {
      color: #fff;
      font-size: 20px;
      font-weight: bold;
      text-decoration: none;
    }
    nav ul {
      list-style: none;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: flex-end;
      transition: max-height 0.4s ease, opacity 0.4s ease;
    }
    nav ul li {
      margin-left: 15px;
    }
    nav ul li a {
      color: #fff;
      text-decoration: none;
      font-size: 16px;
      padding: 6px 10px;
      border-radius: 4px;
      transition: background 0.3s;
    }
    nav ul li a:hover {
      background: #555;
    }

    .menu-toggle {
      display: none;
      font-size: 22px;
      color: #fff;
      background: none;
      border: none;
      cursor: pointer;
      position: absolute;
      right: 20px;
      top: 12px;
    }

    @media (max-width: 768px) {
      nav ul {
        flex-direction: column;
        align-items: flex-start;
        overflow: hidden;
        max-height: 0;
        opacity: 0;
        width: 100%;
        background: #333;
      }
      nav ul.show {
        max-height: 500px;
        opacity: 1;
      }
      .menu-toggle {
        display: block;
      }
    }
  </style>
</head>
<body>
  <nav>
    <a class="navbar-brand" href="/carrental/index.php">Car Rental</a>
    <button class="menu-toggle" onclick="toggleMenu()">☰</button>
    <ul id="nav-links">
      <li><a href="/carrental/index.php">Home</a></li>

      <?php if (!isset($_SESSION['user_id'])): ?>
        <li><a href="/carrental/signup.php">Signup</a></li>
        <li><a href="/carrental/login.php">Login</a></li>
      <?php else: ?>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
       
          <li><a href="/carrental/admin/dashboard.php">Admin Dashboard</a></li>
          <li><a href="/carrental/admin/manage_cars.php">Manage Cars</a></li>
          <li><a href="/carrental/admin/manage_bookings.php">Manage Bookings</a></li>
          <li><a href="/carrental/logout.php">Logout</a></li>
        <?php else: ?>
       
          <li><a href="/carrental/my_bookings.php">My Bookings</a></li>
          <li><a href="/carrental/logout.php">Logout</a></li>
        <?php endif; ?>
      <?php endif; ?>
    </ul>
  </nav>

  <script>
    function toggleMenu() {
      document.getElementById('nav-links').classList.toggle('show');
    }
  </script>
</body>
</html>