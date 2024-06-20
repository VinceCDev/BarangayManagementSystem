<?php
// Include the database connection file
include 'connection.php';

// Check if the 'delete_id' parameter is set in the POST request and is not empty
if(isset($_POST['delete_id']) && !empty($_POST['delete_id'])) {
    // Retrieve the FAQ ID to be deleted from the POST request
    $faqId = $_POST['delete_id'];
    
    // Construct the SQL query to delete the FAQ record with the specified ID
    $sql = "DELETE FROM faq WHERE id = $faqId";

    // Execute the SQL query
    if ($conn->query($sql) === TRUE) {
        // If deletion is successful, redirect to BarangayFAQ.php
        header("Location: BarangayFAQ.php");
        exit(); // Terminate script execution
    } else {
        // If an error occurs during deletion, display error message
        echo "Error deleting record: " . $conn->error;
    }
} else {
    // If the 'delete_id' parameter is not set or empty, display "Invalid FAQ ID"
    echo "Invalid FAQ ID";
}

// Close the database connection
$conn->close();
?>