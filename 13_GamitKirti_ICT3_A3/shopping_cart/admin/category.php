
<?php
require_once "../db.php";

if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit;
}

// Add category
if (isset($_POST["add"])) {
    $name = trim($_POST["name"]);

    if ($name != "") {
        $stmt = $conn->prepare(
            "INSERT INTO categories (name) VALUES (?)"
        );

        $stmt->bind_param("s", $name);
        $stmt->execute();
    }

    header("Location: category.php");
    exit;
}

// Update category
if (isset($_POST["update"])) {
    $id = (int) $_POST["id"];
    $name = trim($_POST["name"]);

    if ($name != "") {
        $stmt = $conn->prepare(
            "UPDATE categories SET name = ? WHERE id = ?"
        );

        $stmt->bind_param("si", $name, $id);
        $stmt->execute();
    }

    header("Location: category.php");
    exit;
}

// Delete category
$message = "";

if (isset($_GET["delete"])) {
    $id = (int) $_GET["delete"];

    $stmt = $conn->prepare(
        "DELETE FROM categories WHERE id = ?"
    );

    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: category.php");
        exit;
    } else {
        $message = "Delete products in this category first.";
    }
}

// Get all categories
$result = $conn->query("SELECT * FROM categories");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Categories</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 850px;
            margin: 30px auto;
            padding: 25px;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        .add-form {
            margin: 25px 0;
            text-align: center;
        }

        input {
            padding: 9px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            padding: 9px 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
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
            background-color: #4CAF50;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .update-form {
            display: flex;
            gap: 8px;
            justify-content: center;
        }

        .update-form input[name="name"] {
            width: 55%;
        }

        .delete {
            color: white;
            background-color: #e53935;
            padding: 8px 10px;
            text-decoration: none;
            border-radius: 4px;
        }

        .delete:hover {
            background-color: #c62828;
        }

        .back {
            display: inline-block;
            margin-top: 25px;
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }

        .back:hover {
            background-color: #45a049;
        }

        .message {
            color: red;
            text-align: center;
        }
    </style>
</head>

<body>

<div class="container">

    <h2>Manage Categories</h2>

    <?php if ($message != "") { ?>
        <p class="message">
            <?php echo htmlspecialchars($message); ?>
        </p>
    <?php } ?>

    <!-- Add Category -->
    <form method="post" class="add-form">
        <input
            type="text"
            name="name"
            placeholder="Category name"
            required
        >

        <button type="submit" name="add">Add Category</button>
    </form>

    <!-- Category Table -->
    <table>
        <tr>
            <th>ID</th>
            <th>Category Name</th>
            <th>Action</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td>
                    <?php echo (int) $row["id"]; ?>
                </td>

                <td>
                    <form method="post" class="update-form">
                        <input
                            type="hidden"
                            name="id"
                            value="<?php echo (int) $row["id"]; ?>"
                        >

                        <input
                            type="text"
                            name="name"
                            value="<?php
                                echo htmlspecialchars(
                                    $row["name"],
                                    ENT_QUOTES,
                                    "UTF-8"
                                );
                            ?>"
                            required
                        >

                        <button type="submit" name="update">
                            Update
                        </button>
                    </form>
                </td>

                <td>
                    <a
                        class="delete"
                        href="category.php?delete=<?php
                            echo (int) $row["id"];
                        ?>"
                        onclick="return confirm('Delete category?')"
                    >
                        Delete
                    </a>
                </td>
            </tr>
        <?php } ?>

    </table>

    <a class="back" href="dashboard.php">Back to Dashboard</a>

</div>

</body>
</html>
