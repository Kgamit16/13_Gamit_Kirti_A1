<?php

session_start();
require_once "config/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

$productId = filter_input(
    INPUT_POST,
    "product_id",
    FILTER_VALIDATE_INT
);

if (!$productId) {
    header("Location: index.php");
    exit;
}

$stmt = $conn->prepare(
    "SELECT id FROM products WHERE id = ?"
);

$stmt->execute([$productId]);

if (!$stmt->fetch()) {
    header("Location: index.php");
    exit;
}

if (!isset($_SESSION["cart"])) {
    $_SESSION["cart"] = [];
}

if (!isset($_SESSION["cart"][$productId])) {
    $_SESSION["cart"][$productId] = 0;
}

$_SESSION["cart"][$productId]++;

header("Location: cart.php");
exit;

?>
