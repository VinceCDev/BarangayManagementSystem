<?php
// Include the database connection file
include 'connection.php';

// Check if the 'id' parameter is set in the URL
if(isset($_GET['id'])) {
    // Get the ID of the blotter record to be deleted
    $blotterId = $_GET['id'];

    // SQL query to delete the blotter record with the specified ID
    $sql = "DELETE FROM blotterrecords WHERE id = $blotterId";

    // Execute the SQL query
    if ($conn->query($sql) === TRUE) {
        // If deletion is successful, redirect to Blotter.php
        header("Location: Blotter.php");
        exit(); // Terminate script execution
    } else {
        // If an error occurs during deletion, display error message
        echo "Error deleting user: " . $conn->error;
    }
}

// Close database connection
$conn->close();
?>
