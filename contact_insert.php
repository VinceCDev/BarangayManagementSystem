<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer files
require 'PHPmailer/src/Exception.php';
require 'PHPmailer/src/PHPMailer.php';
require 'PHPmailer/src/SMTP.php';

include 'connection.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = $_POST['name'];
    $age = $_POST['age'];
    $email = $_POST['email'];
    $contact = $_POST['contact']; // Added line to retrieve contact number
    $message = $_POST['message'];

    // Prepare SQL statement
    $sql = "INSERT INTO receivemessages (name, age, email, contact, message) VALUES (?, ?, ?, ?, ?)";
    
    // Prepare and bind parameters
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sisss", $name, $age, $email, $contact, $message); // Modified line to bind contact number

    // Execute the statement
    if ($stmt->execute()) {
        // Create a new PHPMailer instance
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP(); // Set mailer to use SMTP
            $mail->Host = 'smtp.gmail.com'; // Your SMTP server
            $mail->SMTPAuth = true; // Enable SMTP authentication
            $mail->Username = 'allencristal23@gmail.com'; // SMTP username
            $mail->Password = 'phvzoeaybkfvftzl'; // SMTP password
            $mail->SMTPSecure = 'ssl'; // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 465; // TCP port to connect to

            // Sender information
            $mail->setFrom($_POST["email"]); // Sender's email address and name
            $mail->addAddress('allencristal23@gmail.com'); // Recipient's email address
            $mail->addReplyTo($email, $name); // Reply-to address

            // Content
            $mail->isHTML(true); // Set email format to HTML
            $mail->Subject = 'Message from Contact Form';
            $mail->Body = "Name: $name<br>Age: $age<br>Email: $email<br>Contact: $contact<br>Message: $message"; // Modified line to include contact number

            // Send email
            $mail->send();
            echo 'success'; // Email sent successfully
        } catch (Exception $e) {
            echo "error: {$mail->ErrorInfo}"; // Error sending email
        }
        
        // Redirect to Contact1.php
        header("Location: Contact.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}

?>
