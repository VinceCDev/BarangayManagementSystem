<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the FAQ ID and other fields are set
    if (isset($_POST["faq_id"]) && isset($_POST["question"]) && isset($_POST["answer"]) && isset($_POST["date"])) {
        // Retrieve form data
        $faq_id = $_POST["faq_id"];
        $question = $_POST["question"];
        $answer = $_POST["answer"];
        $date = $_POST["date"];
        
        // Database connection
        include 'connection.php';

        // Prepare SQL statement to update the record
        $sql = "UPDATE faq SET question=?, answer=?, date=? WHERE id=?";
        $stmt = $conn->prepare($sql);

        // Bind parameters to the prepared statement
        $stmt->bind_param("sssi", $question, $answer, $date, $faq_id);

        // Execute the prepared statement
        if ($stmt->execute()) {
            header("Location: BarangayFAQ.php");
            exit();
        } else {
            echo "Error updating record: " . $stmt->error;
        }

        // Close the prepared statement
        $stmt->close();

        // Close the database connection
        $conn->close();
    } else {
        echo "Required fields are not set";
    }
} else {
    echo "Invalid request";
}
?>
