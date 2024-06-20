<?php
// Start session
session_start();

// Include the database connection file
include 'connection.php';

// Establish connection to the file management system database
$fileManagementConn = new mysqli('127.0.0.1:3307', 'root', 'Allen_122', 'file_management_system');

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

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the name and temporary name of the uploaded photo file
    $photo = $_FILES['photo']['name'];
    $tmp_name = $_FILES['photo']['tmp_name'];

    // Directories for file storage
    $target_dir = "resident_photo/";
    $target_file = $target_dir . basename($photo);

    // DMS directories for file storage
    $dms_target_dir = "../DMS/resident_photo/";
    $dms_target_file = $dms_target_dir . basename($photo);

    // Ensure local directory exists or create it
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Ensure DMS directory exists or create it
    if (!file_exists($dms_target_dir)) {
        mkdir($dms_target_dir, 0777, true);
    }

    // Move uploaded file to local directory
    if (move_uploaded_file($tmp_name, $target_file)) {
        // Copy uploaded file to DMS directory
        if (!copy($target_file, $dms_target_file)) {
            echo "Failed to copy file to DMS directory.";
            exit(); // Terminate script execution
        }

        // Retrieve other resident information from the POST data
        $residentFullName = $_POST['residentFullName'];
        $residentBirthDate = $_POST['residentBirthDate'];
        $residentBirthPlace = $_POST['residentBirthPlace'];
        $residentAge = $_POST['residentAge'];
        $residentTotalHouseholds = $_POST['residentTotalHouseholds'];
        $residentContact = $_POST['residentContact'];
        $residentBloodType = $_POST['residentBloodType'];
        $residentCivilStatus = $_POST['residentCivilStatus'];
        $residentOccupation = $_POST['residentOccupation'];
        $residentMonthlyIncome = $_POST['residentMonthlyIncome'];
        $residentHousehold = $_POST['residentHousehold'];
        $residentLengthOfStay = $_POST['residentLengthOfStay'];
        $residentReligion = $_POST['residentReligion'];
        $residentNationality = $_POST['residentNationality'];
        $residentGender = $_POST['residentGender'];
        $residentEducation = $_POST['residentEducation'];

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

            // Start a transaction
            $conn->begin_transaction();

                        // Start a transaction
            $conn->begin_transaction();

            try {
                // SQL query to insert resident data into the database
                $sqlResidents = "INSERT INTO residents (photo, full_name, birth_date, birth_place, age, total_households, contact, blood_type, civil_status, occupation, monthly_income, household, length_of_stay, religion, nationality, gender, education) 
                                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmtResidents = $conn->prepare($sqlResidents);
                $stmtResidents->bind_param("ssssissssssssssss", $photo, $residentFullName, $residentBirthDate, $residentBirthPlace, $residentAge, $residentTotalHouseholds, $residentContact, $residentBloodType, $residentCivilStatus, $residentOccupation, $residentMonthlyIncome, $residentHousehold, $residentLengthOfStay, $residentReligion, $residentNationality, $residentGender, $residentEducation);
                $stmtResidents->execute();

                // Get the last inserted ID from residents
                $resident_id = $conn->insert_id;

                // Insert into resident_file table
                $sqlResidentFile = "INSERT INTO file_management_system.resident_file (resident_name, photos, resident_id, user_id, user_email) VALUES (?, ?, ?, ?, ?)";
                $stmtResidentFile = $fileManagementConn->prepare($sqlResidentFile);
                $stmtResidentFile->bind_param("ssiss", $residentFullName, $photo, $resident_id, $user_id, $user_email);
                $stmtResidentFile->execute();

                // Commit transaction if all queries succeed
                $conn->commit();

                // If both insertions are successful, redirect to Resident.php
                header("Location: Resident.php");
                exit();
            } catch (Exception $e) {
                // Rollback transaction if any query fails
                $conn->rollback();

                // Display error message
                echo "Error: " . $e->getMessage();
            }

            // Close statements
            $stmtResidents->close();
            $stmtResidentFile->close();
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

// Close the database connection
$conn->close();
?>
