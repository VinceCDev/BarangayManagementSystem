<?php

// Check if the form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection
    include 'connection.php';

    // Get data from POST request
    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'];
    $lastname = $_POST['lastname'];
    $birthdate = $_POST['birthdate'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $gender = $_POST['gender'];
    $religion = $_POST['religion'];
    $status = $_POST['status'];
    $occupation = $_POST['occupation'];
    $monthly_income = $_POST['monthly_income'];
    $allergies_conditions = $_POST['allergies_conditions'];
    $education = $_POST['education'];
    $emergency_person = $_POST['emergency_person'];
    $emergency_contact = $_POST['emergency_contact'];

    // Get id from ProfileData table based on email
    $stmt = $conn->prepare("SELECT id FROM profiledata WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id = $row['id'];

        // Update ProfileData table
        $sql = "UPDATE profiledata SET firstname=?, middlename=?, lastname=?, birthdate=?, email=?, contact=?, gender=?, religion=?, status=?, emergency_person=?, emergency_contact=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssssssi", $firstname, $middlename, $lastname, $birthdate, $email, $contact, $gender, $religion, $status, $emergency_person, $emergency_contact, $id);
        $stmt->execute();
        $stmt->close();

        // Update ImportantInfo table
        $sql = "UPDATE importantinfo SET occupation=?, monthly_income=?, allergies_conditions=?, education=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $occupation, $monthly_income, $allergies_conditions, $education, $id);
        $stmt->execute();
        $stmt->close();

        echo "Profile updated successfully!";
    } else {
        echo "Error: User not found!";
    }

    // Close connection
    $conn->close();
} else {
    // If the request is not POST, redirect to the appropriate page
    header("Location: UserProfile.php");
    exit;
}
?>