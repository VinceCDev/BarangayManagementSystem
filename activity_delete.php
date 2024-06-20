<?php
// Establish database connection for the primary database
include 'connection.php';

// Establish a separate database connection for the file management system
$fileManagementConn = new mysqli('127.0.0.1:3307', 'root', 'Allen_122', 'file_management_system');

// Check the connection for the file management system
if ($fileManagementConn->connect_error) {
    die("Connection failed: " . $fileManagementConn->connect_error);
}

// Check if 'id' parameter is set in the URL and is numeric
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    // Get the activity ID from the URL
    $activityId = $_GET['id'];

    // Begin a transaction on the primary database
    $conn->begin_transaction();

    // Begin a transaction on the file management system database
    $fileManagementConn->begin_transaction();

    try {
        // Prepare SQL statement to delete from document_folder by activity_id in file management system
        $stmtDocument = $fileManagementConn->prepare("DELETE FROM document_folder WHERE activity_id = ?");
        $stmtDocument->bind_param("i", $activityId);

        // Execute the prepared statement for document_folder deletion
        $stmtDocument->execute();

        // Prepare SQL statement to delete from activity by ID in the primary database
        $stmtActivity = $conn->prepare("DELETE FROM activity WHERE id = ?");
        $stmtActivity->bind_param("i", $activityId);

        // Execute the prepared statement for activity deletion
        $stmtActivity->execute();

        // Commit the transaction on the primary database
        $conn->commit();

        // Commit the transaction on the file management system database
        $fileManagementConn->commit();

        // If deletion is successful, redirect to Activity.php
        header("Location: Activity.php");
        exit(); // Terminate script execution
    } catch (Exception $e) {
        // Rollback the transaction on the primary database
        $conn->rollback();

        // Rollback the transaction on the file management system database
        $fileManagementConn->rollback();

        // Display error message
        echo "Error deleting activity: " . $e->getMessage();
    } finally {
        // Close the prepared statements
        $stmtDocument->close();
        $stmtActivity->close();
    }
} else {
    // If 'id' parameter is missing or invalid, display error message
    echo "Invalid activity ID.";
}

// Close database connections
$conn->close();
$fileManagementConn->close();
?>
