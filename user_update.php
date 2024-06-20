<?php
// Include database connection
include 'connection.php';

// Check if form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $userId = $_POST['users_id'];
    $fullName = $_POST['userFullName'];
    $userName = $_POST['userName'];
    $newPassword = $_POST['newpassword'];
    $confirmPassword = $_POST['confirmpassword'];
    $userType = isset($_POST['usertype']) ? substr($_POST['usertype'], 0, 50) : ''; // Limiting the length to 50 characters

    // Sanitize form data to prevent SQL injection
    $userId = mysqli_real_escape_string($conn, $userId);
    $fullName = mysqli_real_escape_string($conn, $fullName);
    $userName = mysqli_real_escape_string($conn, $userName);
    $userType = mysqli_real_escape_string($conn, $userType);

    // Check if new password and confirm password are provided and match
    if (!empty($newPassword) && $newPassword === $confirmPassword) {
        $password = mysqli_real_escape_string($conn, $newPassword);

        // Update user record with new password in the database
        $sql = "UPDATE users SET fullName='$fullName', userName='$userName', password='$password', userType='$userType' WHERE id='$userId'";
    } else {
        // Update user record without changing password in the database
        $sql = "UPDATE users SET fullName='$fullName', userName='$userName', userType='$userType' WHERE id='$userId'";
    }

    // Execute the SQL query
    if ($conn->query($sql) === TRUE) {
        // Redirect back to Users.php after successful update
        header("Location: Users.php");
        exit();
    } else {
        // If update fails, display error message
        echo "Error updating user: " . $conn->error;
    }
}

// Close database connection
$conn->close();
?>