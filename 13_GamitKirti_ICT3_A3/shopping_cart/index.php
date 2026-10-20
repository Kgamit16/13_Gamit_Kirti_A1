
<?php
require_once "db.php";

$result = $conn->query("SELECT * FROM categories");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Online Shopping</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            max-width: 700px;
            margin: 50px auto;
            padding: 25px;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        h2 {
            color: #4CAF50;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            background-color: #f9f9f9;
            margin: 10px 0;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        li a {
            color: #333;
            text-decoration: none;
            display: block;
        }

        li:hover {
            background-color: #e8f5e9;
        }

        .links {
            text-align: center;
            margin-top: 25px;
        }

        .links a {
            display: inline-block;
            margin: 5px;
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }

        .links a:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>

    <div class="container">

        <h1>Online Shopping</h1>

        <h2>Categories</h2>

        <ul>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <li>
                    <a href="category.php?id=<?= $row["id"] ?>">
                        <?= htmlspecialchars($row["name"]) ?>
                    </a>
                </li>
            <?php } ?>
        </ul>

        <div class="links">
            <a href="cart.php">View Cart</a>
            <a href="register.php">Register</a>
            <a href="login.php">Login</a>
        </div>

    </div>

</body>
</html>
