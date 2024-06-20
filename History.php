<!DOCTYPE html>
<html lang="en"> 
<head> 
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History</title>
    <link rel="icon" href="images/logo1.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/History.css">
</head> 
<body>
    <header class="header"> 
        <a href="#" class="logo"> 
            <img src="images/logo1.png" alt="Error Image" height="60px" width="60px"/>
            <h2>Barangay Paule 1</h2> 
        </a>
        <button class="hamburger" onclick="toggleMenu()"> 
            <div class="bar"></div>
            <div class="bar"></div>
            <div class="bar"></div>
        </button>
        <nav class="navigation">
            <a href="index.php" style="--i:1">Home</a>
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false" style="--i:2">
                    Our Barangay</button>
                <div class="dropdown-content">
                  <a href="GeneralInformation.php">General Information</a>
                  <a href="History.php" style="background-color: #F4B400; color: #000; padding: 12px; border-radius: 30px;">History</a>
                  <a href="Maps.php">Maps</a>
                  <a href="Photos.php">Photo Album</a>
                </div>
            </div>
            <a href="Certificate.php" style="--i:3">Services</a>
            <a href="FAQ.php" style="--i:4">FAQ</a>
            <a href="Contact.php" style="--i:5">Contacts</a>
        </nav> 
    </header>
    <section id="history" class="history">
        <div class="picture1"> 
            <img src="images/history.jpg" alt="Your Picture"> 
        </div> 
        <div class="hc1">
            <?php
                include 'connection.php';

                $sql = "SELECT context FROM history WHERE id = 1"; 
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $currentContext = $row["context"];
                    $currentContext = nl2br($currentContext);
                } else {
                    $currentContext = "History context not found.";
                }

                $conn->close();
            ?>
            <h3>History</h3><br>
            <p><?php echo $currentContext; ?></p>
        </div>
    </section>
    <footer> 
        <p>&copy; 2024 Barangay Paule 1. All rights reserved.</p> 
    </footer>
    <script src="js/index.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleMenu() {
            var navigation = document.querySelector('.navigation');
            var hamburger = document.querySelector('.hamburger');

            navigation.classList.toggle('active');

            hamburger.classList.toggle('active');
        }
    </script>
</body> 
</html>