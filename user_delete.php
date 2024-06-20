<?php
    // Include database connection
    include 'connection.php';
    
    // Check if 'id' parameter is set in the URL and is not empty
    if(isset($_GET['id']) && !empty($_GET['id'])) {
        // Get the user ID from the URL
        $userId = $_GET['id'];
        
        // SQL query to delete user with the specified ID
        $sql = "DELETE FROM users WHERE id = $userId";
        
        // Execute the SQL query
        if ($conn->query($sql) === TRUE) {
            // Redirect to Users.php after successful deletion
            header("Location: Users.php");
            exit();
        } else {
            // If deletion fails, display error message
            echo "Error deleting user: " . $conn->error;
        }
    } else {
        // If 'id' parameter is not set or empty, display invalid ID message
        echo "Invalid user ID";
    }
    
    // Close database connection
    $conn->close();
?>