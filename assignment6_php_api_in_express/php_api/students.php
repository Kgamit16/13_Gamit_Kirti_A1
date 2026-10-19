<?php

header("Content-Type: application/json; charset=UTF-8");

$students = [
    [
        "id" => 1,
        "name" => "Rahul",
        "course" => "BCA"
    ],
    [
        "id" => 2,
        "name" => "Priya",
        "course" => "MSc ICT"
    ],
    [
        "id" => 3,
        "name" => "Amit",
        "course" => "BSc IT"
    ]
];

echo json_encode($students);

?>