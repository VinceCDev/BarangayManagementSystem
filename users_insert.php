<?php
    include 'connection.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $fullName = $_POST['userFullName'];
        $userName = $_POST['userName'];
        $password = $_POST['password'];
        $userType = $_POST['usertype'];
    
        $sql = "INSERT INTO users (fullName, userName, password, userType) VALUES (?, ?, ?, ?)";
    
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $fullName, $userName, $password, $userType);
    
        if ($stmt->execute()) {
            header("Location: Users.php");
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    
        $stmt->close();
        $conn->close();
    }
?>
