
<?php

require_once "db.php";

// Check whether the XML file exists
if (!file_exists("students.xml")) {
    die("Error: students.xml file not found.");
}

// Read the XML file
libxml_use_internal_errors(true);

$xml = simplexml_load_file("students.xml");

if ($xml === false) {
    die("Error: Unable to read XML file.");
}

// Prepare the SQL statement
$stmt = $conn->prepare(
    "INSERT INTO students (name, age, course)
     VALUES (?, ?, ?)"
);

if (!$stmt) {
    die("Error preparing SQL statement.");
}

// Bind parameters
$stmt->bind_param("sis", $name, $age, $course);

$count = 0;

// Read and insert each student
foreach ($xml->student as $student) {

    $name = trim((string) $student->name);
    $age = (int) $student->age;
    $course = trim((string) $student->course);

    if ($name === "" || $age <= 0 || $course === "") {
        continue;
    }

    if ($stmt->execute()) {
        $count++;
    }
}

echo $count . " students imported successfully.";

$stmt->close();
$conn->close();

?>

