<?php
session_start();
include 'partials/_dbconnect.php';

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to view your bookings.");
}
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    die("Access denied. This page is for users only.");
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT b.id AS booking_id, b.start_date, b.end_date, b.total_price, b.status,
               c.car_name, c.brand
        FROM bookings b
        JOIN cars c ON b.car_id = c.id
        WHERE b.user_id = '$user_id'
        ORDER BY b.start_date DESC";
$result = mysqli_query($conn, $sql);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Bookings</title>
  <style>
    body {
      background-color: #f4f4f9;
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
    }
    .container {
      max-width: 1000px;
      margin: 40px auto;
      padding: 20px;
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #2c7a7b;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    th, td {
      padding: 12px;
      border: 1px solid #ddd;
      text-align: center;
    }
    th {
      background: #333;
      color: #fff;
    }
    tr:nth-child(even) {
      background: #f9f9f9;
    }
    .alert {
      padding: 12px;
      border-radius: 5px;
      margin-bottom: 20px;
      text-align: center;
      font-weight: bold;
    }
    .alert-success { background: #d4edda; color: #155724; }
    .alert-warning { background: #fff3cd; color: #856404; }
    .badge {
      padding: 6px 10px;
      border-radius: 4px;
      font-size: 13px;
      font-weight: bold;
      color: #fff;
    }
    .bg-success { background: #28a745; }
    .bg-danger { background: #dc3545; }
    .bg-secondary { background: #6c757d; }
    .btn {
      display: inline-block;
      padding: 6px 12px;
      border-radius: 4px;
      text-decoration: none;
      font-size: 14px;
      font-weight: bold;
      cursor: pointer;
    }
    .btn-danger { background: #dc3545; color: #fff; }
    .btn-danger:hover { background: #b52a37; }
    .text-muted { color: #6c757d; }
  </style>
</head>
<body>
  <?php require 'partials/_nav.php'; ?>

  <div class="container">
    <h2>My Bookings</h2>

    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'cancelled') { ?>
      <div class="alert alert-success">Booking cancelled successfully!</div>
    <?php } ?>

    <?php if (mysqli_num_rows($result) > 0) { ?>
      <table>
        <thead>
          <tr>
            <th>Car</th>
            <th>Brand</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Total Price</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php while($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
              <td><?= htmlspecialchars($row['car_name']) ?></td>
              <td><?= htmlspecialchars($row['brand']) ?></td>
              <td><?= htmlspecialchars($row['start_date']) ?></td>
              <td><?= htmlspecialchars($row['end_date']) ?></td>
              <td>₹<?= $row['total_price'] ?></td>
              <td>
                <span class="badge <?= $row['status']=='confirmed'?'bg-success':($row['status']=='cancelled'?'bg-danger':'bg-secondary') ?>">
                  <?= $row['status'] ?>
                </span>
              </td>
              <td>
                <?php if ($row['status'] == 'confirmed') { ?>
                  <a href="cancel.php?booking_id=<?= $row['booking_id'] ?>" 
                     class="btn btn-danger btn-sm"
                     onclick="return confirmCancel();">
                     Cancel
                  </a>
                <?php } else { ?>
                  <span class="text-muted">N/A</span>
                <?php } ?>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    <?php } else { ?>
      <div class="alert alert-warning">You have no bookings yet.</div>
    <?php } ?>
  </div>

  <script>
    function confirmCancel() {
      return confirm("Are you sure you want to cancel this booking?");
    }
  </script>
</body>
</html>