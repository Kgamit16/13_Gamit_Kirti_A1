
<?php
require_once "../db.php";

if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 350px;
            margin: 100px auto;
            padding: 25px;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            text-align: center;
        }

        h2 {
            color: #333;
            margin-bottom: 25px;
        }

        .menu a {
            display: block;
            padding: 12px;
            margin: 12px 0;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }

        .menu a:hover {
            background-color: #45a049;
        }

        .menu .logout {
            background-color: #e53935;
        }

        .menu .logout:hover {
            background-color: #c62828;
        }
    </style>
</head>

<body>

    <div class="container">

        <h2>Shopping Cart Admin</h2>

        <div class="menu">
            <a href="category.php">Manage Categories</a>

            <a href="product.php">Manage Products</a>

            <a href="logout.php" class="logout">Logout</a>
        </div>

    </div>

</body>
</html>
