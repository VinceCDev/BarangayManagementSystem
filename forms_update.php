<?php
ob_start();
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the form fields are set
    if (isset($_POST["certificate"]) && isset($_POST["requirements"]) && isset($_POST["forms_id"])) {
        // Database connection
        include 'connection.php';

        // Prepare SQL statement to update the record
        $sql = "UPDATE certificates SET certificate_name=?, requirements=? WHERE id=?";
        $stmt = $conn->prepare($sql);

        $stmt->bind_param("ssi", $certificate_name, $requirements, $formsid);
        
        $certificate_name = $_POST["certificate"];
        $requirements = $_POST["requirements"];
        $formsid = $_POST["forms_id"];

        // Execute the prepared statement
        if ($stmt->execute()) {
            // Check if a file is uploaded
            if ($_FILES["file"]["error"] == UPLOAD_ERR_OK) {
                $target_dir = "uploads/";
                $target_file = $target_dir . basename($_FILES["file"]["name"]);
                $file_name = basename($_FILES["file"]["name"]);

                // Create the uploads directory if it doesn't exist
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true); 
                }

                // Move the uploaded file to the target directory
                if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                    echo "The file ". htmlspecialchars(basename($_FILES["file"]["name"])). " has been uploaded.";
                } else {
                    echo "Sorry, there was an error uploading your file.";
                }

                // Update the file field in the database
                $sql_update_file = "UPDATE certificates SET file=? WHERE id=?";
                $stmt_update_file = $conn->prepare($sql_update_file);
                $stmt_update_file->bind_param("si", $file_name, $formsid);
                $stmt_update_file->execute();
                $stmt_update_file->close();
            }

            header("Location: Forms.php");
            exit();
        } else {
            echo "Error updating record: " . $stmt->error;
        }

        // Close the prepared statement
        $stmt->close();

        // Close the database connection
        $conn->close();
    } else {
        echo "Form fields are not set";
    }
} else {
    echo "Invalid request";
}
ob_end_flush();
?>
