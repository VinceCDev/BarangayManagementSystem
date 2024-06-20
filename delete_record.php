<?php
// Check if the request method is POST and if the ID parameter is set
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    // Connect to the database
    include 'connection.php';

    // Get the user ID from the POST data
    $userId = $_POST['id'];

    // Prepare a DELETE statement
    $sql = "DELETE FROM BlotterRecords WHERE id=?";

    // Prepare and bind parameters
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);

    // Execute the statement
    if ($stmt->execute()) {
        // If the deletion was successful, return a success message
        echo json_encode(array("status" => "success", "message" => "Record deleted successfully"));
    } else {
        // If the deletion failed, return an error message
        echo json_encode(array("status" => "error", "message" => "Failed to delete record"));
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
} else {
    // If the request method is not POST or ID parameter is not set, return an error message
    echo json_encode(array("status" => "error", "message" => "Invalid request"));
}
?>
