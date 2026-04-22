<?php
session_start();
include 'partials/_dbconnect.php';

if (!isset($_GET['car_id'])) {
    die("Car not selected.");
}
$car_id = (int)$_GET['car_id'];

$sql = "SELECT * FROM cars WHERE id='$car_id'";
$result = mysqli_query($conn, $sql);
if (!$result || mysqli_num_rows($result) == 0) {
    die("Car not found.");
}
$car = mysqli_fetch_assoc($result);

$bookedDates = [];
$bookedSql = "SELECT start_date, end_date 
              FROM bookings 
              WHERE car_id = '$car_id' 
                AND status IN ('confirmed','pending')";
$bookedResult = mysqli_query($conn, $bookedSql);
while ($row = mysqli_fetch_assoc($bookedResult)) {
    $bookedDates[] = [
        'start' => $row['start_date'],
        'end'   => $row['end_date']
    ];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['user_id'])) {
        die("You must be logged in to book a car.");
    }

    $user_id = $_SESSION['user_id'];
    $start = $_POST['start_date'];
    $end = $_POST['end_date'];

    $days = (strtotime($end) - strtotime($start)) / (60*60*24);
    if ($days <= 0) {
        echo "<div class='alert danger'>Invalid date range!</div>";
    } else {
        $total_price = $days * $car['price_per_day'];

        $checkSql = "SELECT * FROM bookings 
                     WHERE car_id = '$car_id' 
                       AND status IN ('confirmed','pending')
                       AND (start_date <= '$end' AND end_date >= '$start')";
        $checkResult = mysqli_query($conn, $checkSql);

        if (mysqli_num_rows($checkResult) > 0) {
            echo "<div class='alert danger'>Sorry, this car is already booked for the selected dates.</div>";
        } else {
            $sql = "INSERT INTO bookings (user_id, car_id, start_date, end_date, total_price, status) 
                    VALUES ('$user_id', '$car_id', '$start', '$end', '$total_price', 'confirmed')";
            if (mysqli_query($conn, $sql)) {
                mysqli_query($conn, "UPDATE cars SET status='booked' WHERE id='$car_id'");
                echo "<div class='alert success'>Booking successful! Total Price: ₹$total_price</div>";
            } else {
                echo "<div class='alert danger'>Error: " . mysqli_error($conn) . "</div>";
            }
        }
    }
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Book Car</title>
  <style>
    body {
      background-color: #f4f4f9;
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
    }
    .container {
      max-width: 600px;
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
    p {
      font-size: 16px;
      margin: 8px 0;
    }
    label {
      display: block;
      margin-bottom: 6px;
      font-weight: bold;
    }
    input {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 5px;
      font-size: 14px;
    }
    button {
      width: 100%;
      padding: 12px;
      background-color: #2c7a7b;
      color: #fff;
      border: none;
      border-radius: 5px;
      font-size: 16px;
      font-weight: bold;
      cursor: pointer;
    }
    button:hover {
      background-color: #225e5f;
    }
    .alert {
      padding: 12px;
      border-radius: 5px;
      margin-bottom: 20px;
      text-align: center;
      font-weight: bold;
    }
    .alert.success { background: #d4edda; color: #155724; }
    .alert.danger { background: #f8d7da; color: #721c24; }
    #price-preview {
      font-size: 16px;
      font-weight: bold;
      color: #2c7a7b;
      margin-top: 10px;
      text-align: center;
    }
  </style>
</head>
<body>
  <?php require 'partials/_nav.php'; ?>

  <div class="container">
    <h2>Book Car: <?= htmlspecialchars($car['car_name']); ?></h2>
    <p><strong>Brand:</strong> <?= htmlspecialchars($car['brand']); ?></p>
    <p><strong>Price per day:</strong> ₹<?= $car['price_per_day']; ?></p>

    <form method="post">
      <label for="start_date">Start Date</label>
      <input type="date" id="start_date" name="start_date" required>

      <label for="end_date">End Date</label>
      <input type="date" id="end_date" name="end_date" required>

      <div id="price-preview"></div>

      <button type="submit">Confirm Booking</button>
    </form>
  </div>

  <script>
    const startInput = document.getElementById('start_date');
    const endInput = document.getElementById('end_date');
    const preview = document.getElementById('price-preview');
    const pricePerDay = <?= $car['price_per_day']; ?>;
    const bookedRanges = <?= json_encode($bookedDates); ?>;

    function isDateBlocked(dateStr) {
      const date = new Date(dateStr);
      for (const range of bookedRanges) {
        const start = new Date(range.start);
        const end = new Date(range.end);
        if (date >= start && date <= end) {
          return true;
        }
      }
      return false;
    }

    function updatePrice() {
      const start = new Date(startInput.value);
      const end = new Date(endInput.value);
      if (start && end && end > start) {
        const days = (end - start) / (1000 * 60 * 60 * 24);
        const total = days * pricePerDay;
        preview.textContent = `Estimated Total Price: ₹${total}`;
      } else {
        preview.textContent = "";
      }
    }

    startInput.addEventListener('change', function() {
      if (isDateBlocked(this.value)) {
        alert("This start date is already booked. Please choose another.");
        this.value = "";
      }
      updatePrice();
    });

    endInput.addEventListener('change', function() {
      if (isDateBlocked(this.value)) {
        alert("This end date is already booked. Please choose another.");
        this.value = "";
      }
      updatePrice();
    });
  </script>
</body>
</html>