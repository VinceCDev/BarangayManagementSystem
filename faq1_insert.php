<?php
// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include the database connection file
    include 'connection.php';

    // Prepare an SQL statement to insert data into the faq table
    $stmt = $conn->prepare("INSERT INTO faq (question, answer, date) VALUES (?, ?, ?)");

    // Bind parameters to the SQL statement
    $stmt->bind_param("sss", $question, $answer, $date);

    // Retrieve form data from POST request
    $question = $_POST["Question"];
    $answer = $_POST["StartofTerm"];
    $date = $_POST["date"];

    // Execute the prepared statement
    if ($stmt->execute()) {
        // If insertion is successful, redirect to BarangayFAQ.php
        header("Location: BarangayFAQ.php");
        exit(); // Terminate script execution
    } else {
        // If an error occurs during execution, display error message
        echo "Error: " . $stmt->error;
    }

    // Close the prepared statement
    $stmt->close();
    
    // Close the database connection
    $conn->close();
}
?>
