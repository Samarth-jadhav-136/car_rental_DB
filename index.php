<?php
include __DIR__ . '/partials/_dbconnect.php';

$filter_brand  = isset($_GET['brand']) ? trim($_GET['brand']) : '';
$status_filter = isset($_GET['status']) ? trim($_GET['status']) : '';

$sql = "SELECT * FROM cars WHERE 1=1";

if ($filter_brand !== '') {
    $sql .= " AND brand = '" . mysqli_real_escape_string($conn, $filter_brand) . "'";
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
      background-color: #f4f7f6;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 0;
      color: #333;
    }
    .container {
      max-width: 1200px;
      margin: 40px auto;
      padding: 0 20px;
    }
    h2 {
      text-align: center;
      margin-bottom: 30px;
      color: #2c7a7b;
      font-size: 2.2rem;
      font-weight: 700;
    }

    .filter-container {
      background: #fff;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.05);
      margin-bottom: 40px;
    }
    form {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      align-items: flex-end;
      gap: 20px;
    }
    .form-group {
      display: flex;
      flex-direction: column;
    }
    label {
      font-weight: 600;
      margin-bottom: 8px;
      font-size: 14px;
      color: #555;
    }
    select, input[type="text"] {
      padding: 10px 15px;
      border: 1px solid #ccc;
      border-radius: 6px;
      min-width: 200px;
      font-size: 15px;
      transition: border-color 0.3s, box-shadow 0.3s;
    }
    select:focus, input[type="text"]:focus {
      outline: none;
      border-color: #2c7a7b;
      box-shadow: 0 0 0 3px rgba(44, 122, 123, 0.2);
    }
    form button {
      padding: 12px 25px;
      border: none;
      border-radius: 6px;
      background: #2c7a7b;
      color: #fff;
      font-weight: bold;
      font-size: 15px;
      cursor: pointer;
      transition: background 0.3s, transform 0.1s;
    }
    form button:hover { background: #225e5f; }
    form button:active { transform: scale(0.97); }
    .row {
      display: flex;
      flex-wrap: wrap;
      gap: 25px;
      justify-content: center;
    }
    .card {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.08);
      overflow: hidden;
      width: 320px;
      text-align: center;
      opacity: 0;
      transform: translateY(20px);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .card:hover {
      transform: translateY(-8px) !important;
      box-shadow: 0 15px 30px rgba(0,0,0,0.15);
    }
    .card-img-wrapper {
      width: 100%;
      height: 200px;
      overflow: hidden;
    }
    .card img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.5s ease;
    }
    .card:hover img {
      transform: scale(1.08);
    }
    
    .card.is-booked img {
      filter: grayscale(80%) opacity(0.8);
    }
    .card.is-booked {
      background: #f9f9f9;
    }

    .card-body {
      padding: 20px;
    }
    .card h5 {
      margin: 0 0 10px 0;
      font-size: 20px;
      color: #2c3e50;
    }
    .card .brand { color: #7f8c8d; font-size: 14px; margin-bottom: 15px; text-transform: uppercase; letter-spacing: 1px;}
    .card .price { font-size: 18px; font-weight: bold; color: #e67e22; margin-bottom: 15px; }
    
    .badge {
      display: inline-block;
      padding: 6px 12px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: bold;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 15px;
    }
    .bg-success { background: #e8f5e9; color: #2e7d32; }
    .bg-danger { background: #ffebee; color: #c62828; }
    
    .btn {
      display: block;
      width: calc(100% - 24px);
      margin: 0 auto;
      padding: 12px;
      border-radius: 6px;
      text-decoration: none;
      font-weight: bold;
      background: #2c7a7b;
      color: #fff;
      transition: background 0.3s ease, box-shadow 0.3s ease;
    }
    .btn:hover:not(.disabled) {
      background: #225e5f;
      box-shadow: 0 4px 10px rgba(44, 122, 123, 0.4);
    }
    .btn.disabled {
      background: #cbd5e0;
      color: #718096;
      cursor: not-allowed;
      pointer-events: none;
      box-shadow: none;
    }

    .alert {
      padding: 15px 20px;
      border-radius: 8px;
      margin: 20px auto;
      text-align: center;
      max-width: 600px;
      width: 100%;
      font-size: 16px;
    }
    .alert-warning { background: #fff3cd; color: #856404; border: 1px solid #ffeeba; }

    @keyframes slideUpFade {
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>
  <?php require __DIR__ . '/partials/_nav.php'; ?>

  <div class="container">
    <h2>Explore Our Fleet</h2>

    <div class="filter-container">
      <?php $brands_result = mysqli_query($conn, "SELECT DISTINCT brand FROM cars"); ?>
      <form method="get" action="" id="filterForm">
        <div class="form-group">
          <label for="brand">Brand</label>
          <select name="brand" id="brand">
            <option value="">All Brands</option>
            <?php while($b = mysqli_fetch_assoc($brands_result)) { ?>
              <option value="<?= htmlspecialchars($b['brand']) ?>" 
                <?= strcasecmp($filter_brand, $b['brand'])==0 ? 'selected' : '' ?>>
                <?= htmlspecialchars($b['brand']) ?>
              </option>
            <?php } ?>
          </select>
        </div>

        <div class="form-group">
          <label for="status">Availability</label>
          <select name="status" id="status">
            <option value="">Any Status</option>
            <option value="available" <?= $status_filter=='available'?'selected':'' ?>>Available Now</option>
            <option value="booked" <?= $status_filter=='booked'?'selected':'' ?>>Currently Booked</option>
          </select>
        </div>

        <button type="submit" id="submitBtn">Apply Filters</button>
      </form>
    </div>

    <div class="row" id="carGrid">
      <?php if (mysqli_num_rows($result) > 0) { ?>
        <?php while($row = mysqli_fetch_assoc($result)) { ?>
          <div class="card">
            <div class="card-img-wrapper">
              <img src="images/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['car_name']) ?>">
            </div>
            <div class="card-body">
              <h5><?= htmlspecialchars($row['car_name']) ?></h5>
              <p class="brand"><?= htmlspecialchars($row['brand']) ?></p>
              <p class="price">₹<?= $row['price_per_day'] ?> <span style="font-size:12px; color:#999;">/ day</span></p>
              
              <span class="badge <?= $row['status']=='available'?'bg-success':'bg-danger' ?> status-badge">
                <?= $row['status'] ?>
              </span>
              
              <a href="book.php?car_id=<?= $row['id'] ?>" class="btn book-btn">Book Now</a>
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
            No cars found matching your criteria. Try adjusting your filters.
          <?php } ?>
        </div>
      <?php } ?>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      
        const cards = document.querySelectorAll('.card');
        cards.forEach((card, index) => {
        
            card.style.animation = `slideUpFade 0.6s ease forwards ${index * 0.1}s`;
            
            const badge = card.querySelector('.status-badge');
            const btn = card.querySelector('.book-btn');
            
            if (badge && badge.textContent.trim().toLowerCase() === 'booked') {
           
                card.classList.add('is-booked');
         
                btn.classList.add('disabled');
                btn.textContent = 'Currently Unavailable';
                btn.href = 'javascript:void(0)'; 
            }
        });

    
        const form = document.getElementById('filterForm');
        const submitBtn = document.getElementById('submitBtn');
        
        form.addEventListener('submit', () => {
            submitBtn.innerHTML = 'Applying... ';
            submitBtn.style.opacity = '0.8';
            submitBtn.style.pointerEvents = 'none'; 
        });
    });
  </script>
</body>
</html>
