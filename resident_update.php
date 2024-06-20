<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the official ID is set
    if (isset($_POST["official_id"])) {
        // Database connection
        include 'connection.php';
        
        // Get resident ID from the form
        $resident_id = $_POST["official_id"];

        // Initialize an empty array to store the update fields and values
        $update_fields = array();
        $update_values = array();

        // Check if photo is set and add it to the update array
        if (isset($_FILES["photo"]) && $_FILES["photo"]["error"] == UPLOAD_ERR_OK) {
            // Upload photo
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["photo"]["name"]);
            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                $update_fields[] = "photo=?";
                $update_values[] = $target_file;
            } else {
                echo "Error uploading photo.";
            }
        }

        // Add other fields to the update array
        $fields = array(
            "residentFullName" => "full_name",
            "residentBirthDate" => "birth_date",
            "residentBirthPlace" => "birth_place",
            "residentAge" => "age",
            "residentTotalHouseholds" => "total_households",
            "residentContact" => "contact",
            "residentBloodType" => "blood_type",
            "residentCivilStatus" => "civil_status",
            "residentOccupation" => "occupation",
            "residentMonthlyIncome" => "monthly_income",
            "residentHousehold" => "household",
            "residentLengthOfStay" => "length_of_stay",
            "residentReligion" => "religion",
            "residentNationality" => "nationality",
            "residentGender" => "gender",
            "residentEducation" => "education"
        );

        foreach ($fields as $form_field => $db_field) {
            if (isset($_POST[$form_field])) {
                $update_fields[] = "$db_field=?";
                $update_values[] = $_POST[$form_field];
            }
        }

        // Check if there are fields to update
        if (!empty($update_fields)) {
            // Construct the SQL UPDATE statement
            $sql = "UPDATE residents SET " . implode(", ", $update_fields) . " WHERE id=?";
            $stmt = $conn->prepare($sql);

            // Create parameter types string for bind_param
            $types = str_repeat("s", count($update_values));
            $types .= "i"; // Add "i" for the resident_id

            // Merge parameter types with resident_id
            $stmt_params = array_merge(array($types), $update_values, array($resident_id));

            // Bind the values to the prepared statement parameters
            $stmt->bind_param(...$stmt_params);

            // Execute the prepared statement
            if ($stmt->execute()) {
                header("Location: Resident.php");
                exit();
            } else {
                echo "Error updating record: " . $stmt->error;
            }

            // Close prepared statement
            $stmt->close();
        } else {
            echo "No fields to update";
        }

        // Close the database connection
        $conn->close();
    } else {
        echo "Official ID is required";
    }
} else {
    echo "Invalid request";
}
?>
