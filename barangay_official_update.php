<?php
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the official ID is set
    if (isset($_POST["official_id"])) {
        $official_id = $_POST["official_id"];

        // Initialize an empty array to store the update fields and values
        $update_fields = array();
        $update_values = array();

        // Check if position is set and add it to the update array
        if (isset($_POST["position"])) {
            $update_fields[] = "position=?";
            $update_values[] = $_POST["position"];
        }

        // Check if fullName is set and add it to the update array
        if (isset($_POST["barangayFullName"])) {
            $update_fields[] = "fullName=?";
            $update_values[] = $_POST["barangayFullName"];
        }

        // Check if contact is set and add it to the update array
        if (isset($_POST["barangayContact"])) {
            $update_fields[] = "contact=?";
            $update_values[] = $_POST["barangayContact"];
        }

        // Check if address is set and add it to the update array
        if (isset($_POST["barangayResident"])) {
            $update_fields[] = "address=?";
            $update_values[] = $_POST["barangayResident"];
        }

        // Check if startOfTerm is set and add it to the update array
        if (isset($_POST["StartofTerm"])) {
            $update_fields[] = "startOfTerm=?";
            $update_values[] = $_POST["StartofTerm"];
        }

        // Check if endOfTerm is set and add it to the update array
        if (isset($_POST["EndofTerm"])) {
            $update_fields[] = "endOfTerm=?";
            $update_values[] = $_POST["EndofTerm"];
        }

        if (isset($_FILES["barangayPhoto"]) && $_FILES["barangayPhoto"]["error"] == UPLOAD_ERR_OK) {
            // Upload photo
            $target_dir = "photos/";
            $target_file = $target_dir . basename($_FILES["barangayPhoto"]["name"]);
            if (move_uploaded_file($_FILES["barangayPhoto"]["tmp_name"], $target_file)) {
                $update_fields[] = "photo=?";
                $update_values[] = $target_file;
            } else {
                echo "Error uploading photo.";
            }
        }

        // Check if there are fields to update
        if (!empty($update_fields)) {
            // Construct the SQL UPDATE statement
            $sql = "UPDATE barangay_officials SET " . implode(", ", $update_fields) . " WHERE id=?";
            $stmt = $conn->prepare($sql);

            // Bind the values to the prepared statement parameters
            $stmt_params = array_merge($update_values, array($official_id));
            $stmt->bind_param(str_repeat("s", count($update_values)) . "i", ...$stmt_params);

            // Execute the prepared statement
            if ($stmt->execute()) {
                header("Location: BarangayOfficial.php");
                exit();
            } else {
                echo "Error updating record: " . $conn->error;
            }
            
            // Close prepared statement
            $stmt->close();
        } else {
            echo "No fields to update";
        }
    } else {
        echo "Official ID is required";
    }
} else {
    echo "Invalid request";
}

// Close the database connection
$conn->close();
?>