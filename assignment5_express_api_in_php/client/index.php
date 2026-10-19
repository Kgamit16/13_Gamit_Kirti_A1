<?php

$url = "http://localhost:3000/api/students";

$response = file_get_contents($url);

if ($response === false) {
    die("Unable to connect to Express API.");
}

$students = json_decode($response, true);

if (!is_array($students)) {
    die("Invalid API response.");
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>PHP Calling Express API</title>
</head>
<body>

<h2>Student Details from Express API</h2>

<table border="1" cellpadding="10">

<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Course</th>
</tr>

<?php foreach ($students as $student): ?>

<tr>

    <td>
        <?= htmlspecialchars((string) $student["id"]) ?>
    </td>

    <td>
        <?= htmlspecialchars($student["name"]) ?>
    </td>

    <td>
        <?= htmlspecialchars($student["course"]) ?>
    </td>

</tr>

<?php endforeach; ?>

</table>

</body>
</html>