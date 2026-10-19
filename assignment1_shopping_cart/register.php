<?php

session_start();
require_once "config/db.php";

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $captcha = trim($_POST["captcha"] ?? "");

    $expectedCaptcha = (string)($_SESSION["captcha_code"] ?? "");

    unset($_SESSION["captcha_code"]);

    if ($name === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Enter a valid name and email address.";

    } elseif (strlen($password) < 8) {

        $error = "Password must contain at least 8 characters.";

    } elseif (
        $expectedCaptcha === "" ||
        !hash_equals($expectedCaptcha, $captcha)
    ) {

        $error = "Incorrect CAPTCHA. Please try again.";

    } else {

        try {

            $stmt = $conn->prepare(
                "INSERT INTO users (name, email, password)
                 VALUES (?, ?, ?)"
            );

            $stmt->execute([
                $name,
                $email,
                password_hash($password, PASSWORD_DEFAULT)
            ]);

            $success = "Registration successful. You can now log in.";

        } catch (PDOException $e) {

            $error = "Registration failed. This email may already be registered.";

        }
    }
}

?>

<!DOCTYPE html>

<html>
<head>
    <title>User Registration</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="form-container">

```
<h2>User Registration</h2>

<?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<?php if ($success): ?>
    <p class="success"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<form method="POST">

    <label>Name</label>
    <input type="text" name="name" maxlength="100" required>

    <label>Email</label>
    <input type="email" name="email" maxlength="150" required>

    <label>Password (minimum 8 characters)</label>
    <input type="password" name="password" minlength="8" required>

    <label>Enter CAPTCHA</label>

    <img
        src="captcha.php"
        alt="CAPTCHA"
        width="150"
        height="50"
    >

    <p>
        <a href="register.php">Refresh CAPTCHA</a>
    </p>

    <input type="text" name="captcha" required>

    <button type="submit">Register</button>

</form>

<p><a href="login.php">Already registered? Login</a></p>
```

</div>

</body>
</html>
