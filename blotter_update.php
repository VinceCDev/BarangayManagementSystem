<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $blotterId = $_POST['blotter_id'];
    $status = isset($_POST['status']) ? $_POST['status'] : ''; // Set a default value if 'status' is not set
    $complainant = $_POST['complainant'];
    $age1 = $_POST['age1'];
    $address1 = $_POST['address1'];
    $contact1 = $_POST['contact1'];
    $personToComplaint = $_POST['personToComplaint'];
    $age2 = $_POST['age2'];
    $address2 = $_POST['address2'];
    $contact2 = $_POST['contact2'];
    $actionTaken = $_POST['actionTaken'];

    // Prepare update query
    $sql = "UPDATE blotterrecords SET 
            status='$status', 
            complainant='$complainant', 
            age1='$age1', 
            address1='$address1', 
            contact1='$contact1', 
            personToComplaint='$personToComplaint', 
            age2='$age2', 
            address2='$address2', 
            contact2='$contact2', 
            actionTaken='$actionTaken' 
            WHERE id='$blotterId'";

    // Include database connection
    include 'connection.php';

    // Execute update query
    if ($conn->query($sql) === TRUE) {
        // Redirect to the previous page after successful update
        header("Location: Blotter.php");
        exit;
    } else {
        echo "Error updating record: " . $conn->error;
    }

    // Close database connection
    $conn->close();
} else {
    // Redirect to the previous page if accessed directly without form submission
    header("Location: Blotter.php");
    exit;
}
?>
