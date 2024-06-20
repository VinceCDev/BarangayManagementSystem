<?php
// Include connection.php to establish database connection
include 'connection.php';

// Get the user ID from the URL parameter
$userId = $_GET['id'];

// Fetch user data from the database based on the user ID
$sql = "SELECT * FROM users WHERE id = $userId";
$result = $conn->query($sql);

// Check if user data is found
if ($result->num_rows > 0) {
    // Fetch user details
    $row = $result->fetch_assoc();
    $userData = [
        'id' => $row['id'],
        'fullName' => $row['fullName'],
        'userName' => $row['userName'],
        'password' => $row['password'],
        'userType' => $row['userType']
    ];
    // Return the user data as JSON
    echo json_encode($userData);
} else {
    // Handle the case where user data is not found
    echo "User data not found";
}

// Close database connection
$conn->close();
?>
