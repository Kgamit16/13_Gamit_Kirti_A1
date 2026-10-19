```php
<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

require_once "../config/db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: categories.php");
    exit;
}

$action = $_POST["action"] ?? "";
$message = "";

try {

    // ADD CATEGORY
    if ($action === "add") {

        $name = trim($_POST["name"] ?? "");

        if ($name === "" || strlen($name) > 100) {
            throw new Exception("Please enter a valid category name.");
        }

        $stmt = $conn->prepare(
            "INSERT INTO categories (name) VALUES (?)"
        );

        $stmt->execute([$name]);

        $message = "Category added successfully.";

    }

    // UPDATE CATEGORY
    elseif ($action === "update") {

        $id = filter_input(
            INPUT_POST,
            "id",
            FILTER_VALIDATE_INT
        );

        $name = trim($_POST["name"] ?? "");

        if (!$id || $name === "" || strlen($name) > 100) {
            throw new Exception("Please enter valid category details.");
        }

        $stmt = $conn->prepare(
            "UPDATE categories SET name = ? WHERE id = ?"
        );

        $stmt->execute([$name, $id]);

        if ($stmt->rowCount() === 0) {
            $check = $conn->prepare(
                "SELECT id FROM categories WHERE id = ? AND name = ?"
            );

            $check->execute([$id, $name]);

            if (!$check->fetch()) {
                throw new Exception("Category not found.");
            }
        }

        $message = "Category updated successfully.";

    }

    // DELETE CATEGORY
    elseif ($action === "delete") {

        $id = filter_input(
            INPUT_POST,
            "id",
            FILTER_VALIDATE_INT
        );

        if (!$id) {
            throw new Exception("Invalid category ID.");
        }

        $check = $conn->prepare(
            "SELECT id FROM categories WHERE id = ?"
        );

        $check->execute([$id]);

        if (!$check->fetch()) {
            throw new Exception("Category not found.");
        }

        $stmt = $conn->prepare(
            "DELETE FROM categories WHERE id = ?"
        );

        $stmt->execute([$id]);

        $message = "Category deleted successfully.";

    }

    else {
        throw new Exception("Invalid category action.");
    }

} catch (PDOException $e) {

    if ($e->getCode() === "23000") {
        $message = "This category already exists or cannot be deleted.";
    } else {
        $message = "Database error. Please check your database settings.";
    }

} catch (Exception $e) {

    $message = $e->getMessage();

}

header(
    "Location: categories.php?message=" . urlencode($message)
);

exit;
?>
```
