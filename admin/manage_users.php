<?php
session_start();
include '../partials/_dbconnect.php';

$result = mysqli_query($conn, "SELECT * FROM users");
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Users</title>
  <style>
    body {
      background-color: #f4f4f9;
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
    }
    nav {
      background: #333;
      color: #fff;
      padding: 15px;
    }
    .container {
      max-width: 1000px;
      margin: 40px auto;
      padding: 0 20px;
    }
    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #2c7a7b;
    }
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
    tr:nth-child(even) {
      background: #f9f9f9;
    }
    .btn {
      display: inline-block;
      padding: 6px 12px;
      border-radius: 4px;
      text-decoration: none;
      font-size: 14px;
      font-weight: bold;
      cursor: pointer;
      margin: 2px;
    }
    .btn-edit { background: #ffc107; color: #000; }
    .btn-edit:hover { background: #e0a800; }
    .btn-delete { background: #dc3545; color: #fff; }
    .btn-delete:hover { background: #b52a37; }
  </style>
</head>
<body>
  <?php require '../partials/_nav.php'; ?>

  <div class="container">
    <h2>Registered Users</h2>

    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Username</th>
          <th>Email</th>
          <th>Created At</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while($user = mysqli_fetch_assoc($result)) { ?>
          <tr>
            <td><?= $user['id'] ?></td>
            <td><?= htmlspecialchars($user['username']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td><?= $user['created_at'] ?></td>
            <td>
              <a href="edit_user.php?id=<?= $user['id'] ?>" class="btn btn-edit">Edit</a>
              <a href="delete_user.php?id=<?= $user['id'] ?>" 
                 class="btn btn-delete" 
                 onclick="return confirm('Are you sure you want to delete this user?');">
                 Delete
              </a>
            </td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</body>
</html>