<?php
    include 'connection.php';

    // Assuming you have received the data from the form
    $occupation = $_POST['occupation'];
    $monthly_income = $_POST['monthly_income'];
    $allergies_conditions = $_POST['allergies_conditions'];
    $education = $_POST['education'];
    $emergency_person = $_POST['emergency_person'];
    $emergency_contact = $_POST['emergency_contact'];

    // Update the other information in the database
    $sql = "UPDATE ImportantInfo SET 
            occupation = '$occupation',
            monthly_income = '$monthly_income',
            allergies_conditions = '$allergies_conditions',
            education = '$education',
            emergency_person = '$emergency_person',
            emergency_contact = '$emergency_contact'
            WHERE id = 4"; // Assuming you are updating the record with ID 1
    if ($conn->query($sql) === TRUE) {
        echo json_encode(array("success" => true, "message" => "Other information updated successfully"));
    } else {
        echo json_encode(array("success" => false, "message" => "Error updating other information: " . $conn->error));
    }

    $conn->close();
?>
