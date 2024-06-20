<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve password and email from the form
    $password = $_POST['password'];
    $email = $_POST['email'];

    // Validate password (optional)
    if (!isPasswordValid($password)) {
        // Password validation failed
        echo "error: Password must be at least 8 characters long and contain symbols, numbers, uppercase and lowercase letters.";
        exit();
    }

    // Database connection
    include 'connection.php';

    // Prepare SQL statement to update password in the users table
    $update_password_sql = "UPDATE users SET password=? WHERE userName=?";

    // Prepare and bind parameters for password update
    $stmt = $conn->prepare($update_password_sql);
    $stmt->bind_param("ss", $password, $email);

    // Execute the password update statement
    if ($stmt->execute()) {
        header("Location: Login.php");
        exit();
    } else {
        // Error updating password
        echo "error: " . $stmt->error;
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
} else {
    // Redirect back to the profile page if accessed directly
    header("Location: ResetPassword.php");
    exit();
}

// Function to validate password
function isPasswordValid($password) {
    // Modified regex pattern to allow passwords with the format provided
    $passwordRegex = '/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';
    return preg_match($passwordRegex, $password);
}
?>