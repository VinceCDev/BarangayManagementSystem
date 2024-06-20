<?php
// Check if the request method is GET and if 'id' parameter is set
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    // Include the database connection file
    include 'connection.php';

    // Establish a separate database connection for the file management system
    $fileManagementConn = new mysqli('127.0.0.1:3307', 'root', 'Allen_122', 'file_management_system');

    // Check the connection for the file management system
    if ($fileManagementConn->connect_error) {
        die("Connection failed: " . $fileManagementConn->connect_error);
    }

    // Get the ID of the barangay official to be deleted
    $officialId = $_GET['id'];

    // Begin a transaction on the primary database
    $conn->begin_transaction();

    // Begin a transaction on the file management system database
    $fileManagementConn->begin_transaction();

    try {
        // Prepare SQL statement to delete from official_file by official_id in the file management system
        $stmtOfficialFile = $fileManagementConn->prepare("DELETE FROM official_file WHERE official_id = ?");
        $stmtOfficialFile->bind_param("i", $officialId);

        // Execute the prepared statement for official_file deletion
        $stmtOfficialFile->execute();

        // Prepare SQL statement to delete from barangay_officials by ID in the primary database
        $stmtBarangayOfficials = $conn->prepare("DELETE FROM barangay_officials WHERE id = ?");
        $stmtBarangayOfficials->bind_param("i", $officialId);

        // Execute the prepared statement for barangay_officials deletion
        $stmtBarangayOfficials->execute();

        // Commit the transaction on the primary database
        $conn->commit();

        // Commit the transaction on the file management system database
        $fileManagementConn->commit();

        // If deletion is successful, redirect to BarangayOfficial.php
        header("Location: BarangayOfficial.php");
        exit(); // Terminate script execution
    } catch (Exception $e) {
        // Rollback the transaction on the primary database
        $conn->rollback();

        // Rollback the transaction on the file management system database
        $fileManagementConn->rollback();

        // Display error message
        echo "Error deleting record: " . $e->getMessage();
    } finally {
        // Close the prepared statements
        $stmtOfficialFile->close();
        $stmtBarangayOfficials->close();
    }

    // Close database connections
    $conn->close();
    $fileManagementConn->close();
}
?>
