<?php
// Start session
session_start();

// Establish database connection for the primary database
include 'connection.php';

// Establish a separate database connection for the file management system
$fileManagementConn = new mysqli('127.0.0.1:3307', 'root', 'Allen_122', 'file_management_system');

// Check the connection
if ($fileManagementConn->connect_error) {
    die("Connection failed: " . $fileManagementConn->connect_error);
}

// Check if the form is submitted using POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if all necessary form fields are set
    if (isset($_FILES['photo'], $_POST['date'], $_POST['activity'], $_POST['description'])) {
        // Get the name and temporary name of the uploaded photo file
        $photos = $_FILES['photo']['name'];
        $tmp_name = $_FILES['photo']['tmp_name'];

        // Directory for activity photos (relative to current directory)
        $activityPhotosDir = "activity_photos/";
        $targetFilePath = $activityPhotosDir . basename($photos);

        // Directory for another folder within DMS (outside current directory)
        $anotherFolderDir = "../DMS/activity_photos/";
        $anotherFilePath = $anotherFolderDir . basename($photos);

        // Ensure directories exist, create if they don't
        if (!file_exists($activityPhotosDir)) {
            mkdir($activityPhotosDir, 0777, true);
        }
        if (!file_exists($anotherFolderDir)) {
            mkdir($anotherFolderDir, 0777, true);
        }

        // Move the uploaded photo to the 'activity_photos' directory
        if (move_uploaded_file($tmp_name, $targetFilePath)) {
            // Copy the uploaded photo to another directory within DMS
            if (copy($targetFilePath, $anotherFilePath)) {
                // Retrieve data from the form
                $date = mysqli_real_escape_string($conn, $_POST['date']);
                $activity = mysqli_real_escape_string($conn, $_POST['activity']);
                $description = mysqli_real_escape_string($conn, $_POST['description']);

                // Get the current logged-in user's username from session
                $username = $_SESSION['username'];

                // Query to fetch user_id and userName from users table
                $sqlUserData = "SELECT id, userName FROM users WHERE userName = '$username'";
                $resultUserData = $conn->query($sqlUserData);

                if ($resultUserData->num_rows == 1) {
                    $rowUserData = $resultUserData->fetch_assoc();
                    $user_id = $rowUserData['id']; // Fetch user_id
                    $user_email = $rowUserData['userName']; // Fetch userName as user_email

                    // SQL query to insert data into the 'activity' table with user_id
                    $sqlActivity = "INSERT INTO activity (photos, date, activity, description) 
                                   VALUES ('$photos', '$date', '$activity', '$description')";

                    // Execute the SQL query for the activity table
                    if ($conn->query($sqlActivity) === TRUE) {
                        // Get the last inserted activity ID
                        $activityId = $conn->insert_id;

                        // SQL query to insert data into the 'document_folder' table in file management system
                        $sqlDocumentFolder = "INSERT INTO document_folder (activity_id, activity_name, photos, user_id, user_email) 
                                             VALUES ('$activityId', '$activity', '$photos', '$user_id', '$user_email')";

                        // Execute the SQL query for the document folder table
                        if ($fileManagementConn->query($sqlDocumentFolder) === TRUE) {
                            // If both insertions are successful, redirect to Activity.php
                            header("Location: Activity.php");
                            exit(); // Terminate script execution after redirection
                        } else {
                            // If an error occurs during insertion into the document_folder table, display error message
                            echo "Error: " . $sqlDocumentFolder . "<br>" . $fileManagementConn->error;
                        }
                    } else {
                        // If an error occurs during insertion into the activity table, display error message
                        echo "Error: " . $sqlActivity . "<br>" . $conn->error;
                    }
                } else {
                    // Handle case where user data is not found (should not happen if username is valid)
                    echo "Error: User data not found for username $username";
                }
            } else {
                echo "Failed to copy file to another directory within DMS.";
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    } else {
        echo "Missing required form fields.";
    }
}

// Close database connections
$conn->close();
$fileManagementConn->close();
?>
