<!DOCTYPE html> 
<html lang="en"> 
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Photos</title>
    <link rel="icon" href="images/logo1.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://unpkg.com/typed.js@2.1.0/dist/typed.umd.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/Photos.css">
    <style>
    .gallery {
        justify-content: center;
        align-items: center;
        padding: 40px 0;
        margin-top: -270px
    }
    </style>
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
                  <a href="Maps.php">Maps</a>
                  <a href="Photos.php" style="background-color: #F4B400; color: #000; padding: 12px; border-radius: 30px;">Photo Album</a>
                </div>
            </div>
            <a href="Certificate.php" style="--i:3">Services</a>
            <a href="FAQ.php" style="--i:4">FAQ</a>
            <a href="Contact.php" style="--i:5">Contacts</a>
        </nav> 
    </header> 
    <section id="photos" class="photos">
        <h3>Photo Albums</h3> 
        <p class="p1">Here are the photos of barangay.</p>
        <hr>
        <section class="gallery">
            <div class="container">
              <div class="row">
                <?php
                  include 'connection.php';

                  $sql = "SELECT * FROM activity"; 
                  $result = $conn->query($sql);

                  if ($result->num_rows > 0) {
                      $count = 0; 
                      while ($row = $result->fetch_assoc()) {
                          $photo  = $row["photos"];
                          $date  = $row["date"];
                          $activity  = $row["activity"];
                          ?>
                          <?php if ($count % 3 == 0 && $count != 0) { ?>
                              </div><div class="row">
                          <?php } ?>
                          <div class="col-md-4 gallery-column">
                              <div class="card">
                                  <img src="activity_photos/<?php echo $photo; ?>" class="card-img-top" alt="Photo">
                                  <div class="card-body">
                                      <h5 class="card-title"><?php echo $activity; ?></h5>
                                      <p class="card-text"><?php echo $date; ?></p>
                                  </div>
                              </div>
                          </div>
                          <?php
                          $count++;
                      }
                  } else {
                      echo "No activity found";
                  }
                  $conn->close();
                ?>
              </div>     
            </div>
          </section>
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