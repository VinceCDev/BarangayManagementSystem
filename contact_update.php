<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include database connection
    include 'connection.php';

    // Get form data
    $contact_id = $_POST['contact_id'];
    $label = $_POST['label'];
    $description = $_POST['description'];
    $contact = $_POST['contact'];

    // Prepare SQL statement to update contact
    $sql = "UPDATE contacts SET label='$label', description='$description', contacts='$contact' WHERE id='$contact_id'";

    // Execute SQL statement
    if ($conn->query($sql) === TRUE) {
        // Redirect to BarangayContact&Message.php
        header("Location: BarangayContact&Message.php");
        exit(); // Make sure to exit after redirection
    } else {
        echo "Error updating contact: " . $conn->error;
    }

    // Close database connection
    $conn->close();
} else {
    echo "Invalid request method";
}
?>
