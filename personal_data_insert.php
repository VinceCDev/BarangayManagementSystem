<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'connection.php';

    // Prepare SQL statement to insert the record
    $sql = "INSERT INTO ProfileData (firstname, middlename, lastname, gender, birthdate, email, contact, religion, status, emergency_person, emergency_contact) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    // Bind parameters
    $stmt->bind_param("sssssssssss", $firstname, $middlename, $lastname, $gender, $birthdate, $email, $contact, $religion, $status, $emergency_person, $emergency_contact);

    // Set parameters from the form data
    $firstname = $_POST["firstname"];
    $middlename = $_POST["middlename"];
    $lastname = $_POST["lastname"];
    $gender = $_POST["gender"];
    $birthdate = $_POST["birthdate"];
    $email = $_POST["email"];
    $contact = $_POST["contact"];
    $religion = $_POST["religion"];
    $status = $_POST["status"];
    $emergency_person = $_POST["emergency_name"]; // Corrected variable name
    $emergency_contact = $_POST["emergency_contact"]; // Corrected variable name

    // Execute the prepared statement
    if ($stmt->execute()) {
        echo "success"; // Optional: return success message
    } else {
        error_log("Error inserting record: " . $stmt->error); // Log error message
        echo "Error inserting record"; // Return generic error message
    }

    // Close the prepared statement
    $stmt->close();

    // Close the database connection
    $conn->close();
} else {
    echo "Invalid request"; // Return error message for invalid request
}
?>
