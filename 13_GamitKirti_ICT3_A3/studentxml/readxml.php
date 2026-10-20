<?php
$conn = mysqli_connect("localhost", "root", "root", "college");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$xml = simplexml_load_file("students.xml");

if ($xml === false) {
    die("Error reading XML file.");
}

foreach ($xml->student as $student) {
    $rollno = (int)$student->rollno;
    $name = mysqli_real_escape_string($conn, (string)$student->name);
    $course = mysqli_real_escape_string($conn, (string)$student->course);
    $marks = (int)$student->marks;

    $sql = "INSERT INTO students (rollno, name, course, marks)
            VALUES ($rollno, '$name', '$course', $marks)";

    if (mysqli_query($conn, $sql)) {
        echo "Student $name inserted successfully.<br>";
    } else {
        echo "Error: " . mysqli_error($conn) . "<br>";
    }
}

mysqli_close($conn);
?>