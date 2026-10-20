
<?php
// Express REST API URL
$url = "http://localhost:3000/students";

// Call API using file_get_contents
$response = file_get_contents($url);

if ($response === false) {
    die("Unable to call API.");
}

// Convert JSON response into PHP array
$students = json_decode($response, true);

// Display student data
echo "<h2>Student Details</h2>";

if (is_array($students)) {
    foreach ($students as $student) {
        echo "ID: " . htmlspecialchars($student['id']) . "<br>";
        echo "Name: " . htmlspecialchars($student['name']) . "<br>";
        echo "Course: " . htmlspecialchars($student['course']) . "<br>";
        echo "<hr>";
    }
} else {
    echo "Invalid API response.";
}
?>

