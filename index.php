<?php
include __DIR__ . '/partials/_dbconnect.php';

$filter_brand  = isset($_GET['brand']) ? trim($_GET['brand']) : '';
$search_name   = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? trim($_GET['status']) : '';

$sql = "SELECT * FROM cars WHERE 1=1";

if ($filter_brand !== '') {
    $sql .= " AND brand = '" . mysqli_real_escape_string($conn, $filter_brand) . "'";
}
if ($search_name !== '') {
    $sql .= " AND car_name LIKE '%" . mysqli_real_escape_string($conn, $search_name) . "%'";
}
if ($status_filter !== '') {
    $sql .= " AND status = '" . mysqli_real_escape_string($conn, $status_filter) . "'";
}

$result = mysqli_query($conn, $sql);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Car Rental System</title>
  <style>
    body {
      background-color: #f4f4f9;
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
    }
    .container {
      max-width: 1200px;
      margin: 40px auto;
      padding: 0 20px;
    }
    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #2c7a7b;
    }
    form {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 20px;
      margin-bottom: 30px;
    }
    label {
      font-weight: bold;
      margin-right: 5px;
    }
    select, input[type="text"] {
      padding: 8px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }
    button {
      padding: 8px 15px;
      border: none;
      border-radius: 4px;
      background: #2c7a7b;
      color: #fff;
      font-weight: bold;
      cursor: pointer;
    }
    button:hover {
      background: #225e5f;
    }
    .row {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      justify-content: center;
    }
    .card {
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      overflow: hidden;
      width: 300px;
      text-align: center;
    }
    .card img {
      width: 100%;
      height: 180px;
      object-fit: cover;
    }
    .card-body {
      padding: 15px;
    }
    .card h5 {
      margin: 10px 0;
      font-size: 18px;
      color: #333;
    }
    .badge {
      display: inline-block;
      padding: 5px 10px;
      border-radius: 4px;
      font-size: 13px;
      font-weight: bold;
    }
    .bg-success { background: #28a745; color: #fff; }
    .bg-danger { background: #dc3545; color: #fff; }
    .btn {
      display: inline-block;
      margin-top: 10px;
      padding: 8px 12px;
      border-radius: 4px;
      text-decoration: none;
      font-weight: bold;
      background: #2c7a7b;
      color: #fff;
    }
    .btn:hover {
      background: #225e5f;
    }
    .alert {
      padding: 12px;
      border-radius: 5px;
      margin: 20px auto;
      text-align: center;
      max-width: 600px;
    }
    .alert-warning { background: #fff3cd; color: #856404; }
  </style>
</head>
<body>
  <?php require __DIR__ . '/partials/_nav.php'; ?>

  <div class="container">
    <h2>Available Cars</h2>

    <?php $brands_result = mysqli_query($conn, "SELECT DISTINCT brand FROM cars"); ?>
    <form method="get" action="">
      <div>
        <label for="brand">Brand:</label>
        <select name="brand" id="brand">
          <option value="">All</option>
          <?php while($b = mysqli_fetch_assoc($brands_result)) { ?>
            <option value="<?= htmlspecialchars($b['brand']) ?>" 
              <?= strcasecmp($filter_brand, $b['brand'])==0 ? 'selected' : '' ?>>
              <?= htmlspecialchars($b['brand']) ?>
            </option>
          <?php } ?>
        </select>
      </div>

      <div>
        <label for="search">Car Name:</label>
        <input type="text" name="search" id="search"
               value="<?= htmlspecialchars($search_name) ?>" placeholder="Enter car name">
      </div>

      <div>
        <label for="status">Status:</label>
        <select name="status" id="status">
          <option value="">All</option>
          <option value="available" <?= $status_filter=='available'?'selected':'' ?>>Available</option>
          <option value="booked" <?= $status_filter=='booked'?'selected':'' ?>>Booked</option>
        </select>
      </div>

      <div>
        <button type="submit">Apply</button>
      </div>
    </form>

    <div class="row">
      <?php if (mysqli_num_rows($result) > 0) { ?>
        <?php while($row = mysqli_fetch_assoc($result)) { ?>
          <div class="card">
            <img src="images/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['car_name']) ?>">
            <div class="card-body">
              <h5><?= htmlspecialchars($row['car_name']) ?></h5>
              <p><?= htmlspecialchars($row['brand']) ?></p>
              <p>₹<?= $row['price_per_day'] ?> per day</p>
              <p>
                <span class="badge <?= $row['status']=='available'?'bg-success':'bg-danger' ?>">
                  <?= $row['status'] ?>
                </span>
              </p>
              <a href="book.php?car_id=<?= $row['id'] ?>" class="btn">Book Now</a>
            </div>
          </div>
        <?php } ?>
      <?php } else { ?>
        <div class="alert alert-warning">
          <?php if ($status_filter == 'available') { ?>
            No cars are currently <strong>available</strong>.
          <?php } elseif ($status_filter == 'booked') { ?>
            No cars are currently <strong>booked</strong>.
          <?php } else { ?>
            No cars found for your filter.
          <?php } ?>
        </div>
      <?php } ?>
    </div>
  </div>
</body>
</html>