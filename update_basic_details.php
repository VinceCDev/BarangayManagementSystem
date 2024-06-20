<?php
    include 'connection.php';

    // Assuming you have received the data from the form
    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'];
    $lastname = $_POST['lastname'];
    $birthdate = $_POST['birthdate'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $gender = $_POST['gender'];
    $religion = $_POST['religion'];
    $status = $_POST['status'];

    // Update the basic details in the database
    $sql = "UPDATE ProfileData SET 
            firstname = '$firstname',
            middlename = '$middlename',
            lastname = '$lastname',
            birthdate = '$birthdate',
            email = '$email',
            contact = '$contact',
            gender = '$gender',
            religion = '$religion',
            status = '$status'
            WHERE id = 2"; // Assuming you are updating the record with ID 1
    if ($conn->query($sql) === TRUE) {
        echo json_encode(array("success" => true, "message" => "Basic details updated successfully"));
    } else {
        echo json_encode(array("success" => false, "message" => "Error updating basic details: " . $conn->error));
    }

    $conn->close();
?>
