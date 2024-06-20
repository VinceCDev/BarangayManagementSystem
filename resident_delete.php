<?php
// Establish database connection
$fileManagementConn = new mysqli('127.0.0.1:3307', 'root', 'Allen_122', 'file_management_system');
include 'connection.php';

// Check if 'id' parameter is set in the URL and is numeric
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    // Get the resident ID from the URL
    $residentId = $_GET['id'];

    // Begin a transaction
    $fileManagementConn->begin_transaction();

    try {
        // Prepare SQL statement to delete from resident_file by resident_id
        $stmtResidentFile = $fileManagementConn->prepare("DELETE FROM file_management_system.resident_file WHERE resident_id = ?");
        $stmtResidentFile->bind_param("i", $residentId);

        // Execute the prepared statement for resident_file deletion
        $stmtResidentFile->execute();

        // Prepare SQL statement to delete from residents by ID
        $stmtResident = $conn->prepare("DELETE FROM residents WHERE id = ?");
        $stmtResident->bind_param("i", $residentId);

        // Execute the prepared statement for resident deletion
        $stmtResident->execute();

        // Commit the transaction
        $fileManagementConn->commit();

        // If deletion is successful, redirect to Resident.php
        header("Location: Resident.php");
        exit(); // Terminate script execution
    } catch (Exception $e) {
        // Rollback the transaction on error
        $fileManagementConn->rollback();

        // Display error message
        echo "Error deleting resident: " . $e->getMessage();
    } finally {
        // Close the prepared statements
        $stmtResidentFile->close();
        $stmtResident->close();
    }
} else {
    // If 'id' parameter is missing or invalid, display error message
    echo "Invalid resident ID.";
}

// Close database connections
$fileManagementConn->close();
$conn->close();
?>
