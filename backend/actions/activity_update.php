<?php
// Include database connection
require __DIR__ . '/../connection.php';

// Check if form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $activityId = $_POST['activity_id'];
    $date = $_POST['date'];
    $activity = $_POST['activity'];
    $description = $_POST['description'];

    // Initialize photo variable
    $photo = "";

    // Check if a new photo is provided
    if ($_FILES['photo']['name'] != "") {
        // Handle photo upload if a new photo is provided
        $photo = $_FILES['photo']['name'];
        $targetDir = UPLOAD_PATH . "/activity_photos/";
        $targetFilePath = $targetDir . basename($photo);
        move_uploaded_file($_FILES['photo']['tmp_name'], $targetFilePath);
    } else {
        // If no new photo is provided, use the existing photo if available
        $photo = isset($_POST['existing_photo']) ? $_POST['existing_photo'] : "";
    }

    // Prepare and execute SQL query to update activity record
    $sql = "UPDATE activity SET date = '$date', activity = '$activity', description = '$description'";
    
    // Conditionally update the photo field if a new photo is provided
    if ($photo !== "") {
        $sql .= ", photos = '$photo'";
    }

    $sql .= " WHERE id = '$activityId'";

    if ($conn->query($sql) === TRUE) {
        // Redirect back to Activity.php after successful update
        header("Location: /BarangayManagementSystem-main/frontend/pages/Activity.php");
        exit();
    } else {
        // Handle update error
        echo "Error updating record: " . $conn->error;
    }
}

// Close database connection
$conn->close();
?>
