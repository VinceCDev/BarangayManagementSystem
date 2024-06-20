<?php
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["message"]) && !empty($_POST["message"])) {
        $paragraph = $_POST["message"];
        $sql = "UPDATE introduction SET paragraph = '$paragraph'";
        if ($conn->query($sql) === TRUE) {
            header("Location: Information.php");
            exit();
        } else {
            echo "Error updating introduction paragraph: " . $conn->error . "<br>";
        }
    }

    if (isset($_POST["messageMission"]) && !empty($_POST["messageMission"])) {
        $new_mission_content = $_POST["messageMission"];
        $sql = "UPDATE mission SET paragraph = '$new_mission_content'";
        if ($conn->query($sql) === TRUE) {
            header("Location: Information.php");
            exit();
        } else {
            echo "Error updating mission content: " . $conn->error . "<br>";
        }
    }

    if (isset($_POST["messageVision"]) && !empty($_POST["messageVision"])) {
        $new_vision_content = $_POST["messageVision"];
        $sql = "UPDATE vision SET paragraph = '$new_vision_content'";
        if ($conn->query($sql) === TRUE) {
            header("Location: Information.php");
            exit();
        } else {
            echo "Error updating vision content: " . $conn->error . "<br>";
        }
    }

    if (isset($_POST["totalLandArea"]) && !empty($_POST["totalLandArea"]) && isset($_POST["landUsed"]) && !empty($_POST["landUsed"])) {
        $total_land_area = $_POST["totalLandArea"];
        $land_used = $_POST["landUsed"];
        $sql = "UPDATE map_statics SET total_land_area = '$total_land_area', land_used = '$land_used' WHERE id = 1";
        if ($conn->query($sql) === TRUE) {
            header("Location: Information.php");
            exit();
        } else {
            echo "Error updating map statistics: " . $conn->error . "<br>";
        }
    }

    if (isset($_POST["founding_years"]) && !empty($_POST["founding_years"]) && isset($_POST["environmental_health_status"]) && !empty($_POST["environmental_health_status"]) && isset($_POST["partnerships_organization"]) && !empty($_POST["partnerships_organization"]) && isset($_POST["projects_made"]) && !empty($_POST["projects_made"])) {
        $foundingyears = $_POST["founding_years"];
        $environmentalhealthstatus = $_POST["environmental_health_status"];
        $partnershipsorganization = $_POST["partnerships_organization"];
        $projectsmade = $_POST["projects_made"];
        $sql = "UPDATE statistics SET founding_years = '$foundingyears', environmental_health_status = '$environmentalhealthstatus', partnerships_organization = '$partnershipsorganization', projects_made = '$projectsmade'";
        if ($conn->query($sql) === TRUE) {
            header("Location: Information.php");
            exit();
        } else {
            echo "Error updating statistics: " . $conn->error . "<br>";
        }
    }

    if (isset($_POST["context"]) && !empty($_POST["context"])) {
        $context = $_POST["context"];
        $sql = "UPDATE history SET context = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $context);
        if ($stmt->execute()) {
            header("Location: Information.php");
            exit();
        } else {
            echo "Error updating history paragraph: " . $stmt->error . "<br>";
        }
    }
    

    if (isset($_POST["number_of_population"]) && !empty($_POST["number_of_population"]) && isset($_POST["average_household_size"]) && !empty($_POST["average_household_size"])) {
        $numberofpopulation = $_POST["number_of_population"];
        $averagehouseholdsize = $_POST["average_household_size"];
        $sql = "UPDATE population SET number_of_population = '$numberofpopulation', average_household_size = '$averagehouseholdsize'";
        if ($conn->query($sql) === TRUE) {
            header("Location: Information.php");
            exit();
        } else {
            echo "Error updating population: " . $conn->error . "<br>";
        }
    }

    if (isset($_POST['id']) && isset($_POST['messages'])) {
        $ids = $_POST['id'];
        $messages = explode("\n", $_POST['messages']);

        // Update each message corresponding to its ID
        foreach ($ids as $key => $id) {
            $message = $conn->real_escape_string($messages[$key]); // Escape the message to prevent SQL injection
            $sql = "UPDATE economics SET message='$message' WHERE id=$id";
            if ($conn->query($sql) === TRUE) {
                header("Location: Information.php");
                exit();
            } else {
                echo "Error updating record with ID $id: " . $conn->error . "<br>";
            }
        }
    } else {
        echo "No messages to update.";
    }
    
    if (isset($_POST['id']) && isset($_POST['business_text'])) {
        $ids = $_POST['id'];
        $messages = explode("\n", $_POST['business_text']);

        // Update each message corresponding to its ID
        foreach ($ids as $key => $id) {
            $message = $conn->real_escape_string($messages[$key]); // Escape the message to prevent SQL injection
            $sql = "UPDATE major_business SET business_text='$message' WHERE id=$id";
            if ($conn->query($sql) === TRUE) {
                header("Location: Information.php");
                exit();
            } else {
                echo "Error updating record with ID $id: " . $conn->error . "<br>";
            }
        }
    } else {
        echo "No messages to update.";
    }
    
    if (isset($_POST['id']) && isset($_POST['income_text'])) {
        $ids = $_POST['id'];
        $incomes = explode("\n", $_POST['income_text']);

        // Update each income corresponding to its ID
        foreach ($ids as $key => $id) {
            $income_text = $conn->real_escape_string($incomes[$key]); // Escape the income to prevent SQL injection
            $sql = "UPDATE major_income SET income_text='$income_text' WHERE id=$id";
            if ($conn->query($sql) === TRUE) {
                header("Location: Information.php");
                exit();
            } else {
                echo "Error updating record with ID $id: " . $conn->error . "<br>";
            }
        }
    } else {
        echo "No incomes to update.";
    }
    
}

$conn->close();
?>
