
<?php

require_once "db.php";

// Read student records from MySQL
$sql = "SELECT name, age, course FROM students";

$result = $conn->query($sql);

if ($result === false) {
    die("Error retrieving student records.");
}

// Create the XML root element
$xml = new SimpleXMLElement("<students></students>");

// Convert each database record into XML
while ($row = $result->fetch_assoc()) {

    $student = $xml->addChild("student");

    $student->addChild(
        "name",
        htmlspecialchars($row["name"], ENT_XML1, "UTF-8")
    );

    $student->addChild(
        "age",
        (string) $row["age"]
    );

    $student->addChild(
        "course",
        htmlspecialchars($row["course"], ENT_XML1, "UTF-8")
    );
}

// Save the XML file in the current folder
$filename = __DIR__ . "/students.xml";

if ($xml->asXML($filename) === false) {
    die("Error saving students.xml. Check folder permissions.");
}

// Display the success message
echo "Student data exported to students.xml successfully.";

$result->free();
$conn->close();

?>

