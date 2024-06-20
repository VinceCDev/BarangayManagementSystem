<?php
// Start session
session_start();

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if both username and password are provided
    if (isset($_POST["username"]) && isset($_POST["password"])) {
        // Database connection
        include 'connection.php';

        // Sanitize user input to prevent SQL injection
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);

        // Prepare SQL statement to retrieve the user with the provided username and password
        $sql = "SELECT * FROM users WHERE userName='$username' AND password='$password'";
        $result = $conn->query($sql);

        if ($result->num_rows == 1) {
            // Login successful
            // Set session variables
            $_SESSION['username'] = $username;

            // Check if "Remember Me" checkbox is checked
            if (isset($_POST['remember']) && $_POST['remember'] == 'on') {
                // Store username in a cookie that expires in 30 days
                setcookie('remembered_username', $username, time() + (30 * 24 * 60 * 60), '/');
            } else {
                // Delete the remembered username cookie if "Remember Me" is unchecked
                setcookie('remembered_username', '', time() - 3600, '/');
            }

            // Close the database connection
            $conn->close();

            // Return JSON response indicating success
            echo json_encode(array('success' => true));
            exit(); // Ensure that script execution stops after returning the response
        } else {
            // Close the database connection
            $conn->close();

            // Return JSON response indicating login failure
            echo json_encode(array('success' => false));
            exit(); // Ensure that script execution stops after returning the response
        }
    } else {
        // Return JSON response indicating missing username or password
        echo json_encode(array('error' => 'Username and password are required'));
        exit(); // Ensure that script execution stops after returning the response
    }
} else {
    // Return JSON response indicating invalid request method
    echo json_encode(array('error' => 'Invalid request'));
    exit(); // Ensure that script execution stops after returning the response
}
?>
