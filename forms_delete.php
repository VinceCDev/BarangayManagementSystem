<?php
// Establish database connection
include 'connection.php';

// Establish a separate database connection for the file management system
$fileManagementConn = new mysqli('127.0.0.1:3307', 'root', 'Allen_122', 'file_management_system');

// Check the connection for the file management system
if ($fileManagementConn->connect_error) {
    die("Connection failed: " . $fileManagementConn->connect_error);
}

// Check if 'delete_id' parameter is set in the POST data and is numeric
if (isset($_POST['delete_id']) && is_numeric($_POST['delete_id'])) {
    // Get the form ID from the POST data
    $formId = $_POST['delete_id'];

    // Begin a transaction for the primary database connection
    $conn->begin_transaction();
    // Begin a transaction for the file management system database connection
    $fileManagementConn->begin_transaction();

    try {
        // Prepare SQL statement to delete from form_file by form_id
        $stmtFormFile = $conn->prepare("DELETE FROM form_file WHERE form_id = ?");
        $stmtFormFile->bind_param("i", $formId);

        // Execute the prepared statement for form_file deletion in the primary database
        $stmtFormFile->execute();

        // Prepare SQL statement to delete from form_file by form_id in the file management system database
        $stmtFormFileDMS = $fileManagementConn->prepare("DELETE FROM form_file WHERE form_id = ?");
        $stmtFormFileDMS->bind_param("i", $formId);

        // Execute the prepared statement for form_file deletion in the file management system database
        $stmtFormFileDMS->execute();

        // Prepare SQL statement to delete from certificates by ID
        $stmtCertificates = $conn->prepare("DELETE FROM certificates WHERE id = ?");
        $stmtCertificates->bind_param("i", $formId);

        // Execute the prepared statement for certificates deletion
        $stmtCertificates->execute();

        // Commit transactions if all queries succeed
        $conn->commit();
        $fileManagementConn->commit();

        // If deletion is successful, redirect to Forms.php
        header("Location: Forms.php");
        exit(); // Terminate script execution
    } catch (Exception $e) {
        // Rollback transactions if any query fails
        $conn->rollback();
        $fileManagementConn->rollback();

        // Display error message
        echo "Error deleting form: " . $e->getMessage();
    } finally {
        // Close the prepared statements for the primary database connection
        $stmtFormFile->close();
        $stmtCertificates->close();

        // Close the prepared statement for the file management system database connection
        $stmtFormFileDMS->close();
    }
} else {
    // If 'delete_id' parameter is missing or invalid, display error message
    echo "Invalid form ID.";
}

// Close database connections
$conn->close();
$fileManagementConn->close();
?>
