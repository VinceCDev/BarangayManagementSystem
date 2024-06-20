<?php
// Include the database connection file
include 'connection.php';

// Retrieve form data from POST request
$status = $_POST['status'];
$complainant = $_POST['complainant'];
$age1 = $_POST['age1'];
$address1 = $_POST['address1'];
$contact1 = $_POST['contact1'];
$personToComplaint = $_POST['personToComplaint'];
$age2 = $_POST['age2'];
$address2 = $_POST['address2'];
$contact2 = $_POST['contact2'];
$actionTaken = $_POST['actionTaken'];

// SQL query to insert new blotter record into the database
$sql = "INSERT INTO blotterrecords (status, complainant, age1, address1, contact1, personToComplaint, age2, address2, contact2, actionTaken) 
        VALUES ('$status', '$complainant', '$age1', '$address1', '$contact1', '$personToComplaint', '$age2', '$address2', '$contact2', '$actionTaken')";

// Execute the SQL query
if ($conn->query($sql) === TRUE) {
    // If insertion is successful, redirect to Blotter.php
    header("Location: Blotter.php");
    exit(); // Terminate script execution
} else {
    // If an error occurs during insertion, display error message
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close database connection
$conn->close();
?>

