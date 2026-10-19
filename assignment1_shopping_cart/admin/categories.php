<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

require_once "../config/db.php";

$categories = $conn->query(
    "SELECT * FROM categories ORDER BY id DESC"
)->fetchAll();

?>

<!DOCTYPE html>

<html>
<head>
    <title>Manage Categories</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="container">

```
<h1>Manage Categories</h1>

<p><a href="dashboard.php">Back to Dashboard</a></p>

<?php if (isset($_GET["message"])): ?>
    <p class="success">
        <?= htmlspecialchars($_GET["message"]) ?>
    </p>
<?php endif; ?>

<h2>Add Category</h2>

<form action="category_action.php" method="POST">

    <input type="hidden" name="action" value="add">

    <label>Category Name</label>

    <input
        type="text"
        name="name"
        maxlength="100"
        required
    >

    <button type="submit">Add Category</button>

</form>

<h2>Category List</h2>

<table>

    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Update</th>
        <th>Delete</th>
    </tr>

    <?php foreach ($categories as $category): ?>

    <tr>

        <td><?= $category["id"] ?></td>

        <td>
            <?= htmlspecialchars($category["name"]) ?>
        </td>

        <td>

            <form action="category_action.php" method="POST">

                <input
                    type="hidden"
                    name="action"
                    value="update"
                >

                <input
                    type="hidden"
                    name="id"
                    value="<?= $category["id"] ?>"
                >

                <input
                    type="text"
                    name="name"
                    value="<?= htmlspecialchars($category["name"]) ?>"
                    maxlength="100"
                    required
                >

                <button type="submit">Update</button>

            </form>

        </td>

        <td>

            <form
                action="category_action.php"
                method="POST"
                onsubmit="return confirm('Delete this category and its products?')"
            >

                <input
                    type="hidden"
                    name="action"
                    value="delete"
                >

                <input
                    type="hidden"
                    name="id"
                    value="<?= $category["id"] ?>"
                >

                <button class="danger" type="submit">
                    Delete
                </button>

            </form>

        </td>

    </tr>

    <?php endforeach; ?>

</table>
```

</div>

</body>
</html>
