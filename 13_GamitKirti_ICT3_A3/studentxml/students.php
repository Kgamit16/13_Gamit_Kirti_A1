
<?php
header("Content-Type: application/json");

$students = array(
    array("id" => 1, "name" => "Rahul", "course" => "BCA"),
    array("id" => 2, "name" => "Priya", "course" => "MCA"),
    array("id" => 3, "name" => "Amit", "course" => "BSc IT")
);

echo json_encode($students);
?>