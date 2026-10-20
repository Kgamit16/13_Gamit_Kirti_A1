
<?php
// Connect to MySQL database
$conn = mysqli_connect("localhost", "root", "root", "college");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch student records
$sql = "SELECT * FROM students";
$result = mysqli_query($conn, $sql);

// Create XML document
$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><students></students>');

// Store records in XML
while ($row = mysqli_fetch_assoc($result)) {
    $student = $xml->addChild("student");

    $student->addChild("id", $row["id"]);
    $student->addChild("rollno", $row["rollno"]);
    $student->addChild("name", htmlspecialchars($row["name"], ENT_XML1, "UTF-8"));
    $student->addChild("course", htmlspecialchars($row["course"], ENT_XML1, "UTF-8"));
    $student->addChild("marks", $row["marks"]);
}

// Save XML data to file
if ($xml->asXML("students.xml")) {
    echo "Student data successfully exported to students.xml";
} else {
    echo "Error creating XML file.";
}

// Close connection
mysqli_close($conn);
?>
