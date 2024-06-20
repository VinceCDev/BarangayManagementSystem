<!DOCTYPE html> 
<html lang="en"> 
<head> 
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maps</title>
    <link rel="icon" href="images/logo1.png" type="image/x-icon">
    <link rel="stylesheet" href="css/Maps.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://unpkg.com/typed.js@2.1.0/dist/typed.umd.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
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
                <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                    Our Barangay</button>
                <div class="dropdown-content">
                  <a href="GeneralInformation.php">General Information</a>
                  <a href="History.php">History</a>
                  <a href="Maps.php" style="background-color: #F4B400; color: #000; padding: 12px; border-radius: 30px;">Maps</a>
                  <a href="Photos.php">Photo Album</a>
                </div>
            </div>
            <a href="Certificate.php" style="--i:3">Services</a>
            <a href="FAQ.php" style="--i:4">FAQ</a>
            <a href="Contact.php" style="--i:5">Contacts</a>
        </nav> 
    </header>
    <section id="map">
        <div id="location" class="location">
            <div class="map">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d7738.967087619569!2d121.40030895000001!3d14.107637050000001!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33bd5a4d36636399%3A0x4f52190306e4b869!2sPaule%201%2C%20Rizal%2C%20Laguna!5e0!3m2!1sen!2sph!4v1713785201525!5m2!1sen!2sph" 
                    width="790" height="550" style="border-radius:10px; border: 5px solid black;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>
        <div class="land">
            <div id="land-area" class="land-area">
                <h3>Land Area</h3>
                <p class="p2">Here is the total land area of Barangay Paule 1.</p>
                <hr>
                <table>
                  <thead>
                    <tr>
                        <th>Total Land Area</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                            include 'connection.php';
                            
                            $sql = "SELECT * FROM map_statics";
                            $result = $conn->query($sql);
                            
                            if ($result->num_rows > 0) {
                                $row = $result->fetch_assoc();
                                $total_land_area  = $row["total_land_area"];
                            } else {
                                $total_land_area  = "";
                            }
                            
                            $conn->close();
                    ?>
                    <tr>
                        <td><?php echo $total_land_area . " " . "hectares"; ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div id="land-use" class="land-use">
                <h3>Land Used</h3>
                <p class="p2">Here is the total land used of Barangay Paule 1.</p>
                <hr>
                <table>
                  <thead>
                    <tr>
                        <th style="background-color: #00aaff;">Total Land Used</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                        <?php
                            include 'connection.php';
                            
                            $sql = "SELECT * FROM map_statics";
                            $result = $conn->query($sql);
                            
                            if ($result->num_rows > 0) {
                                $row = $result->fetch_assoc();
                                $land_used  = $row["land_used"];
                            } else {
                                $land_used  = "";
                            }
                            
                            $conn->close();
                        ?>
                        <td>
                            <?php echo $land_used . " " . "hectares"; ?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
    <footer> 
      <p>&copy; 2024 Barangay Paule 1. All rights reserved.</p> 
    </footer>    
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