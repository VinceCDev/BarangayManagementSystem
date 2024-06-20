<?php
// Start session
session_start();

// Establish database connection
include 'connection.php';

// Establish a separate database connection for the file management system
$fileManagementConn = new mysqli('127.0.0.1:3307', 'root', 'Allen_122', 'file_management_system');

// Check the connection for the file management system
if ($fileManagementConn->connect_error) {
    die("Connection failed: " . $fileManagementConn->connect_error);
}

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data from POST request
    $certificate_name = $_POST["certificate"];
    $requirements = $_POST["requirements"];
    $file_name = $_FILES["file"]["name"];
    $tmp_file = $_FILES["file"]["tmp_name"];

    // Directories for file storage
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($file_name);

    // DMS directories for file storage
    $dms_target_dir = "../DMS/uploads/";
    $dms_target_file = $dms_target_dir . basename($file_name);

    // Ensure local directory exists or create it
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Ensure DMS directory exists or create it
    if (!file_exists($dms_target_dir)) {
        mkdir($dms_target_dir, 0777, true);
    }

    // Move uploaded file to local directory
    if (move_uploaded_file($tmp_file, $target_file)) {
        // Copy uploaded file to DMS directory
        if (!copy($target_file, $dms_target_file)) {
            echo "Failed to copy file to DMS directory.";
            exit(); // Terminate script execution
        }

        // Retrieve user_id and user_email based on current session username
        $username = $_SESSION['username'];
        $sqlUserData = "SELECT id, userName FROM users WHERE userName = ?";
        $stmtUserData = $conn->prepare($sqlUserData);
        $stmtUserData->bind_param("s", $username);
        $stmtUserData->execute();
        $resultUserData = $stmtUserData->get_result();

        if ($resultUserData->num_rows == 1) {
            $rowUserData = $resultUserData->fetch_assoc();
            $user_id = $rowUserData['id']; // Fetch user_id
            $user_email = $rowUserData['userName']; // Fetch userName as user_email

            // Start a transaction for the primary database connection
            $conn->begin_transaction();
            // Start a transaction for the file management system database connection
            $fileManagementConn->begin_transaction();

            try {
                // Insert into certificates table
                $sqlCertificates = "INSERT INTO certificates (certificate_name, requirements, file) VALUES (?, ?, ?)";
                $stmtCertificates = $conn->prepare($sqlCertificates);
                $stmtCertificates->bind_param("sss", $certificate_name, $requirements, $file_name);
                $stmtCertificates->execute();

                // Get the last inserted ID from certificates
                $certificate_id = $conn->insert_id;
                
                // Insert into form_file table in the file management system database
                $sqlFormFileDMS = "INSERT INTO form_file (form_name, form, form_id, user_id, user_email) VALUES (?, ?, ?, ?, ?)";
                $stmtFormFileDMS = $fileManagementConn->prepare($sqlFormFileDMS);
                $stmtFormFileDMS->bind_param("sssis", $certificate_name, $file_name, $certificate_id, $user_id, $user_email);
                $stmtFormFileDMS->execute();

                // Commit transactions if all queries succeed
                $conn->commit();
                $fileManagementConn->commit();

                // If all insertions are successful, redirect to Forms.php
                header("Location: Forms.php");
                exit();
            } catch (Exception $e) {
                // Rollback transactions if any query fails
                $conn->rollback();
                $fileManagementConn->rollback();

                // Display error message
                echo "Error: " . $e->getMessage();
            }

            // Close statements for the primary database
            $stmtCertificates->close();
            $stmtFormFile->close();

            // Close statements for the file management system database
            $stmtFormFileDMS->close();
        } else {
            // Handle case where user data is not found (should not happen if username is valid)
            echo "Error: User data not found for username $username";
        }

        // Close statement for user data retrieval
        $stmtUserData->close();
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}

// Close database connections
$conn->close();
$fileManagementConn->close();
?>
