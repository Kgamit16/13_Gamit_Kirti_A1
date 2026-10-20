
<?php
$dir = "C:/xampp/htdocs/studentxml/";

if (is_dir($dir)) {
    echo "<h2>Files in Directory</h2>";

    $files = scandir($dir);

    foreach ($files as $file) {
        if ($file != "." && $file != "..") {
            echo $file . "<br>";
        }
    }
} else {
    echo "Directory does not exist.";
}
?>
