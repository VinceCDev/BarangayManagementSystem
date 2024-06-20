<?php
// Start session
session_start();

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection
    $fileManagementConn = new mysqli('127.0.0.1:3307', 'root', 'Allen_122', 'file_management_system');
    include 'connection.php';

    // Function to upload file and return the file path
    function uploadFile($file, $folder)
    {
        $target_dir = $folder . "/";
        $target_file = $target_dir . basename($file["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if file already exists
        if (file_exists($target_file)) {
            $uploadOk = 0;
        }

        // Check file size (adjust as needed)
        if ($file["size"] > 500000) {
            $uploadOk = 0;
        }

        // Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            return "";
        } else {
            if (move_uploaded_file($file["tmp_name"], $target_file)) {
                return $target_file;
            } else {
                return "";
            }
        }
    }

    // Upload 2x2 picture
    $picture = uploadFile($_FILES["file1"], "profile_pic");

    // Upload valid ID
    $valid_id = uploadFile($_FILES["file2"], "valid_id");

    // Check if both files are uploaded successfully
    if ($picture != "" && $valid_id != "") {
        // Begin a transaction
        $fileManagementConn->begin_transaction();

        try {
            // Insert data into proof_of_identity table
            $sqlProofOfIdentity = "INSERT INTO proof_of_identity (picture, valid_id) VALUES (?, ?)";
            $stmtProofOfIdentity = $conn->prepare($sqlProofOfIdentity);
            $stmtProofOfIdentity->bind_param("ss", $picture, $valid_id);
            $stmtProofOfIdentity->execute();

            // Get the last inserted ID from proof_of_identity
            $profile_id = $conn->insert_id;

            // Get the current logged-in user's username from session
            $username = $_SESSION['username'];

            // Query to fetch user_id and userName from users table
            $sqlUserData = "SELECT id, userName FROM users WHERE userName = ?";
            $stmtUserData = $conn->prepare($sqlUserData);
            $stmtUserData->bind_param("s", $username);
            $stmtUserData->execute();
            $resultUserData = $stmtUserData->get_result();

            if ($resultUserData->num_rows == 1) {
                $rowUserData = $resultUserData->fetch_assoc();
                $user_id = $rowUserData['id']; // Fetch user_id
                $user_email = $rowUserData['userName']; // Fetch userName as user_email

                // Insert data into profile_file table
                $sqlProfileFile = "INSERT INTO file_management_system.profile_file (photos, profile_id, user_id, user_email) VALUES (?, ?, ?, ?)";
                $stmtProfileFile = $fileManagementConn->prepare($sqlProfileFile);
                $stmtProfileFile->bind_param("siis", $picture, $profile_id, $user_id, $user_email);
                $stmtProfileFile->execute();

                // Commit the transaction
                $fileManagementConn->commit();

                // Success
                echo "Records inserted successfully.";
            } else {
                // Handle case where user data is not found (should not happen if username is valid)
                echo "Error: User data not found for username $username";
            }

        } catch (Exception $e) {
            // Rollback the transaction on error
            $fileManagementConn->rollback();

            // Display error message
            echo "Error: " . $e->getMessage();
        } finally {
            // Close the prepared statements
            $stmtProofOfIdentity->close();
            $stmtProfileFile->close();
            $stmtUserData->close();
        }
    } else {
        // Handle file upload error
        echo "Sorry, there was an error uploading your files.";
    }

    // Close the database connections
    $fileManagementConn->close();
    $conn->close();
}
?>
