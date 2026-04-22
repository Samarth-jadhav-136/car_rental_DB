<?php
include '../partials/_dbconnect.php';  

$message = "";
$messageType = "";

if (isset($_POST['create'])) {
    $car_name = $_POST['car_name'];
    $brand = $_POST['brand'];
    $price_per_day = $_POST['price_per_day'];
    if (mysqli_query($conn, "INSERT INTO cars (car_name, brand, price_per_day) VALUES ('$car_name','$brand','$price_per_day')")) {
        $message = "Car added successfully!";
        $messageType = "success";
    } else {
        $message = "Error adding car: " . mysqli_error($conn);
        $messageType = "danger";
    }
}

if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $price_per_day = $_POST['price_per_day'];
    if (mysqli_query($conn, "UPDATE cars SET price_per_day='$price_per_day' WHERE id=$id")) {
        $message = "Car updated successfully!";
        $messageType = "warning";
    } else {
        $message = "Error updating car: " . mysqli_error($conn);
        $messageType = "danger";
    }
}

if (isset($_POST['delete'])) {
    $id = $_POST['id'];
    if (mysqli_query($conn, "DELETE FROM cars WHERE id=$id")) {
        $message = "Car deleted successfully!";
        $messageType = "danger";
    } else {
        $message = "Error deleting car: " . mysqli_error($conn);
        $messageType = "danger";
    }
}

$result = mysqli_query($conn, "SELECT * FROM cars");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Cars</title>
    <style>
        body {
            background-color: #f4f4f9;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        h2 {
            text-align: center;
            color: #2c7a7b;
            margin-bottom: 20px;
        }
        .alert {
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        .success { background: #d4edda; color: #155724; }
        .warning { background: #fff3cd; color: #856404; }
        .danger { background: #f8d7da; color: #721c24; }
        form {
            margin-bottom: 20px;
            text-align: center;
        }
        form input {
            padding: 8px;
            margin: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        form button {
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
        }
        .btn-success { background: #28a745; color: #fff; }
        .btn-warning { background: #ffc107; color: #000; }
        .btn-danger { background: #dc3545; color: #fff; }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
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
    </style>
</head>
<body>

<h2>Manage Cars</h2>

<?php if (!empty($message)): ?>
    <div class="alert <?= $messageType; ?>">
        <?= $message; ?>
    </div>
<?php endif; ?>

<form method="post">
    <input type="text" name="car_name" placeholder="Car Name" required>
    <input type="text" name="brand" placeholder="Brand" required>
    <input type="number" step="0.01" name="price_per_day" placeholder="Price per Day" required>
    <button type="submit" name="create" class="btn-success">Add Car</button>
</form>

<table>
    <tr>
        <th>ID</th><th>Name</th><th>Brand</th><th>Price per Day</th><th>Actions</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['car_name'] ?></td>
            <td><?= $row['brand'] ?></td>
            <td><?= $row['price_per_day'] ?></td>
            <td>
                <form method="post" style="display:inline-block;">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <input type="number" step="0.01" name="price_per_day" placeholder="New Price" required>
                    <button type="submit" name="update" class="btn-warning">Update</button>
                </form>

                <form method="post" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this car?');">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <button type="submit" name="delete" class="btn-danger">Delete</button>
                </form>
            </td>
        </tr>
    <?php } ?>
</table>

</body>
</html>