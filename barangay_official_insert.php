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

// Check if the form is submitted using POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data from POST request
    $position = $_POST['position'];
    $full_Name = $_POST['barangayFullName'];
    $contacts = $_POST['barangayContact'];
    $barangayaddress = $_POST['barangayResident'];
    $start_Of_Term = $_POST['StartofTerm'];
    $end_Of_Term = $_POST['EndofTerm'];
    $photo_name = $_FILES['barangayPhoto']['name'];
    $tmp_name = $_FILES['barangayPhoto']['tmp_name'];

    // Directory for photos (relative to current directory)
    $photosDir = "photos/";
    $targetFilePath = $photosDir . basename($photo_name);

    // Directory for another folder within DMS (outside current directory)
    $anotherFolderDir = "../DMS/photos/";
    $anotherFilePath = $anotherFolderDir . basename($photo_name);

    // Ensure directories exist, create if they don't
    if (!file_exists($photosDir)) {
        mkdir($photosDir, 0777, true);
    }
    if (!file_exists($anotherFolderDir)) {
        mkdir($anotherFolderDir, 0777, true);
    }

    // Move uploaded photo to 'photos/' directory
    if (move_uploaded_file($tmp_name, $targetFilePath)) {
        // Copy the uploaded photo to another directory within DMS
        if (copy($targetFilePath, $anotherFilePath)) {
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

                // Insert into barangay_officials table
                $sqlBarangayOfficials = "INSERT INTO barangay_officials (position, photo, fullName, contact, address, startOfTerm, endOfTerm) 
                                        VALUES (?,?,?,?,?,?,?)";
                $stmtBarangayOfficials = $conn->prepare($sqlBarangayOfficials);
                $stmtBarangayOfficials->bind_param("sssssss", $position, $photo_name, $full_Name, $contacts, $barangayaddress, $start_Of_Term, $end_Of_Term);

                if ($stmtBarangayOfficials->execute()) {
                    // Get the last inserted ID from barangay_officials
                    $official_id = $conn->insert_id;

                    // Insert into official_file table in the file management system database
                    $sqlOfficialFile = "INSERT INTO official_file (offcial_name, photos, official_id, user_id, user_email) 
                                        VALUES (?,?,?,?,?)";
                    $stmtOfficialFile = $fileManagementConn->prepare($sqlOfficialFile);
                    $stmtOfficialFile->bind_param("ssiss", $full_Name, $photo_name, $official_id, $user_id, $user_email);

                    if ($stmtOfficialFile->execute()) {
                        // If both insertions are successful, redirect to BarangayOfficial.php
                        header("Location: BarangayOfficial.php");
                        exit(); // Terminate script execution
                    } else {
                        // If an error occurs during insertion into official_file, display error message
                        echo "Error inserting into official_file: " . $stmtOfficialFile->error;
                    }

                    // Close the prepared statement for official_file
                    $stmtOfficialFile->close();
                } else {
                    // If an error occurs during insertion into barangay_officials, display error message
                    echo "Error inserting into barangay_officials: " . $stmtBarangayOfficials->error;
                }

                // Close the prepared statement for barangay_officials
                $stmtBarangayOfficials->close();
            } else {
                // Handle case where user data is not found (should not happen if username is valid)
                echo "Error: User data not found for username $username";
            }

            // Close the prepared statement for user data retrieval
            $stmtUserData->close();
        } else {
            echo "Failed to copy file to another directory within DMS.";
        }
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}

// Close database connections
$conn->close();
$fileManagementConn->close();
?>
