<?php

$directory = __DIR__ . "/files";

if (!is_dir($directory)) {
    die("Directory does not exist.");
}

$items = scandir($directory);

?>

<!DOCTYPE html>
<html>
<head>
    <title>List Files</title>
</head>
<body>

<h2>Files in Directory</h2>

<ul>

<?php

foreach ($items as $item) {

    if ($item === "." || $item === "..") {
        continue;
    }

    echo "<li>" .
        htmlspecialchars($item, ENT_QUOTES, "UTF-8") .
        "</li>";
}

?>

</ul>

</body>
</html>