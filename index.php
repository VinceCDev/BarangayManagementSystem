<!DOCTYPE html>
<html lang="en">
<head> 
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Website of Barangay Paule 1</title>
    <link rel="icon" href="images/logo1.png" type="image/x-icon">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/index.css">
</head> 
<body>
    <header class="header"> 
        <a href="#" class="logo"> 
            <img src="images/logo1.png" alt="Error Image" height="60px" width="60px"/>
            <h2>Barangay Paule 1</h2> 
        </a>
        <button class="hamburger" title="hamburger" onclick="toggleMenu()"> 
            <div class="bar"></div>
            <div class="bar"></div>
            <div class="bar"></div>
        </button>
        <nav class="navigation">
            <a id="index" href="index.php" style="--i:1; color: white; background-color: #00aaff;
            padding: 12px; border-radius: 30px;">Home</a>
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" title="dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                    Our Barangay</button>
                <div class="dropdown-content">
                  <a href="GeneralInformation.php">General Information</a>
                  <a href="History.php">History</a>
                  <a href="Maps.php">Maps</a>
                  <a href="Photos.php">Photo Album</a>
                </div>
            </div>
            <a href="Certificate.php" style="--i:3">Services</a>
            <a href="FAQ.php" style="--i:4">FAQ</a>
            <a href="Contact.php" style="--i:5">Contacts</a>
        </nav> 
    </header> 
    <section id="home" class="home"> 
        <div class="hc"> 
            <p class="before-line">Welcome and Discover</p><br> 
            <h1>Barangay Paule of <br>Rizal, Laguna</h1> 
            <br>
            <br>
            <p>
                Located in the vibrant city of Rizal, Laguna, our barangay invites you 
                to join us in embracing progress and unity as we move forward together towards 
                a brighter future.
            </p><br><br>
            <a href="GeneralInformation.php" class="button_box"><p>Learn More</p><i class="bi bi-arrow-right"></i></a>
        </div> 
        <div class="picture"> 
            <img src="images/logo1.png" alt="Your Picture"> 
        </div> 
    </section> 
    <section id="about" class="about">
        <div class="picture1"> 
            <img src="images/assembly.jpeg" alt="Your Picture"> 
        </div> 
        <div class="hc1"> 
            <p class="before-line">About Us</p> 
            <h2>By reshaping our city, we transform the world.</h2><br>
            <h4 style="color: #000;">Barangay Paule 1 is committed to overcoming every obstacle on its path to excellence.</h4>
        </div> 
    </section>
    <section id="statistics" class="statistics">
        <h3>Foundational Statistics</h3>
        <p class=".p1">Statistics in Barangay Paule 1.</p>
        <hr>
        <div class="container">
          <div class="row">
            <div class="col-md-6">
              <div class="row">
                <div class="col-md-6">
                  <div class="card">
                    <div class="card-body">
                      <i class="bi bi-calendar icon"></i>
                      <div class="text-right">
                        <?php
                            include 'connection.php';
        
                            $sql = "SELECT * FROM statistics";
                            $result = $conn->query($sql);
        
                            if ($result->num_rows > 0) {
                                $row = $result->fetch_assoc();
                                $foundingyears  = $row["founding_years"];
                            } else {
                                $foundingyears  = "";
                            }
        
                            $conn->close();
                        ?>
                        <p class="value"><?php echo $foundingyears; ?></p>
                        <h5 class="label">Foundation Years</h5>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="card">
                    <div class="card-body">
                      <i class="bi bi-geo-alt icon"></i>
                      <div class="text-right">
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
                        <p class="value"><?php echo $total_land_area; ?></p>
                        <h5 class="label"> Land Measurement</h5>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="row">
                <div class="col-md-6">
                  <div class="card">
                    <div class="card-body">
                      <i class="bi bi-people icon"></i>
                      <div class="text-right">
                        <?php
                            include 'connection.php';
                        
                            $sql = "SELECT * FROM population";
                            $result = $conn->query($sql);
                        
                            if ($result->num_rows > 0) {
                                $row = $result->fetch_assoc();
                                $number_of_population  = $row["number_of_population"];
                            } else {
                                $number_of_population  = "";
                            }
                        
                            $conn->close();
                        ?>
                        <p class="value"><?php echo $number_of_population; ?></p>
                        <h5 class="label">Barangay Population</h5>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="card">
                    <div class="card-body">
                      <i class="bi bi-list-task icon"></i>
                      <div class="text-right">
                        <?php
                            include 'connection.php';
                                    
                            $sql = "SELECT COUNT(*) AS totalActivity FROM activity";
                            $result = $conn->query($sql);
                                    
                            if ($result->num_rows > 0) {
                            $row = $result->fetch_assoc();
                            $countActivity = $row['totalActivity'];
                            } else {
                            $countActivity = 0;
                            }
                                    
                            $conn->close();
                        ?>
                        <p class="value"><?php echo $countActivity; ?></p>
                        <h5 class="label">Projects Made</h5>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    <?php include 'connection.php'; ?>
    <section id="officials" class="officials">
        <h3 style="text-align: center; width: 100%; left:0;">Meet Our Barangay Officials</h3>
        <p class="p1">Here are the barangay captain and councilors of barangay.</p>
        <hr style=" right:0; left:0;">
        <div class="selector" style="margin-top: 110px;">
            <span><label style="font-weight: 500; font-size:20px;"><em>Period Term</em></label></span>
            <select style="margin: 0 auto; margin-top: 10px;" name="period" id="period" class="form-select form-select-lg mb-3" aria-label=".form-select-lg example">
                <?php
                $sql = "SELECT DISTINCT SUBSTRING(startOfTerm, 1, 4) AS startYear, SUBSTRING(endOfTerm, 1, 4) AS endYear FROM barangay_officials WHERE (SUBSTRING(startOfTerm, 1, 4) != '2024' AND SUBSTRING(endOfTerm, 1, 4) != '2024') OR (SUBSTRING(startOfTerm, 1, 4) = '2025' AND SUBSTRING(endOfTerm, 1, 4) = '2027') OR (SUBSTRING(startOfTerm, 1, 4) <= '2027' AND SUBSTRING(endOfTerm, 1, 4) >= '2027')";
                $result = $conn->query($sql);
    
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $startYear = $row["startYear"];
                        $endYear = $row["endYear"];
                        echo "<option value=\"$startYear-$endYear\">$startYear - $endYear</option>";
                    }
                } else {
                    echo "<option value='' disabled>No officials found</option>";
                }
    
                echo "<option value='2019-2023'>2019 - 2023</option>";
                echo "<option value='2025-2026'>2025 - 2026</option>";
                ?>
            </select>
        </div>
        <section class="gallery">
            <div class="container">
                <div class="row">
                    <?php
                    $sql = "SELECT * FROM barangay_officials";
                    $result = $conn->query($sql);
                
                    if ($result->num_rows > 0) {
                        $firstOfficialDisplayed = false;
                
                        while ($row = $result->fetch_assoc()) {
                            $photo = $row["photo"];
                            $fullName = $row["fullName"];
                            $position = $row["position"];
                            $startOfTerm = substr($row["startOfTerm"], 0, 4);
                            $endOfTerm = substr($row["endOfTerm"], 0, 4);
                
                            // Display the first official separately
                            if (!$firstOfficialDisplayed) {
                                ?>
                                <div class="col-md-12 d-flex justify-content-center">
                                    <div class="gallery-column" data-start="<?php echo $startOfTerm; ?>" data-end="<?php echo $endOfTerm; ?>">
                                        <div class="card">
                                            <img src="photos/<?php echo $photo; ?>" class="card-img-top" alt="Photo">
                                            <div class="card-body">
                                                <h5 class="card-title"><?php echo $fullName; ?></h5>
                                                <p class="card-text"><?php echo $position; ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                $firstOfficialDisplayed = true;
                            } else {
                                ?>
                                <div class="col-md-4 gallery-column" data-start="<?php echo $startOfTerm; ?>" data-end="<?php echo $endOfTerm; ?>">
                                    <div class="card">
                                        <img src="photos/<?php echo $photo; ?>" class="card-img-top" alt="Photo">
                                        <div class="card-body">
                                            <h5 class="card-title"><?php echo $fullName; ?></h5>
                                            <p class="card-text"><?php echo $position; ?></p>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                    } else {
                        echo "No officials found";
                    }
                    ?>
                </div>
            </div>
        </section>
    </section>
    <footer style="width: 100%;"> 
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

        document.addEventListener("DOMContentLoaded", function () {
        const periodSelect = document.getElementById("period");
    
        periodSelect.addEventListener("change", function () {
            const selectedPeriod = periodSelect.value;
    
            const officials = document.querySelectorAll('.gallery-column');
    
            officials.forEach(function (official) {
                const startYear = official.dataset.start;
                const endYear = official.dataset.end;
    
                if ((startYear <= selectedPeriod.split('-')[0] && endYear >= selectedPeriod.split('-')[1]) || official.classList.contains('first-official')) {
                    official.style.display = 'block';
                } else {
                    official.style.display = 'none';
                }
            });
        });
    
        const selectedPeriod = periodSelect.value;
        const officials = document.querySelectorAll('.gallery-column, .d-flex');
        const firstOfficial = document.querySelector('.first-official');
        const startYear = firstOfficial.dataset.start;
        const endYear = firstOfficial.dataset.end;
    
        officials.forEach(function (official) {
            const startYear = official.dataset.start;
            const endYear = official.dataset.end;
    
            if (!(startYear <= selectedPeriod.split('-')[0] && endYear >= selectedPeriod.split('-')[1])) {
                official.style.display = 'none';
            }
        });
    });
    </script>
</body>
</html>