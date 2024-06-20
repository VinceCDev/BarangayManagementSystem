<?php
  session_start();

  if (!isset($_SESSION['username'])) {
      header("Location: Login.php");
      exit;
  }

  if (isset($_GET['logout'])) {
      session_destroy();

      header("Location: Login.php");
      exit;
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Barangay Information</title>
  <link rel="icon" href="images/logo1.png" type="image/x-icon">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.1.7/sweetalert2.min.css">
  <link rel="stylesheet" href="css/Information.css">
</head>
<body>
  <div>
    <div class="header">
      <i class="fas fa-bars hamburger" onclick="toggleNavigation()" style="display: none;"></i>
      <div class="picfetch">
        <?php
            include 'connection.php';

              $sql = "SELECT * FROM proof_of_identity WHERE id = (SELECT id FROM profiledata WHERE email = ?)";
              $stmt = $conn->prepare($sql);
              $stmt->bind_param("s", $_SESSION['username']);
              $stmt->execute();
              $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $picture = $row["picture"];
            } else {
                $picture = "";
            }
                
              $sql = "SELECT * FROM profiledata WHERE email = ?";
              $stmt = $conn->prepare($sql);
              $stmt->bind_param("s", $_SESSION['username']);
              $stmt->execute();
              $result = $stmt->get_result();

            if ($result->num_rows > 0) {
              $row = $result->fetch_assoc();
              $firstname = $row["firstname"];
              $middlename = $row["middlename"];
              $lastname = $row["lastname"];
            } else {
              $firstname = "";
              $middlename = "";
              $lastname = "";
            }

            $stmt->close();
            $conn->close();
          ?>

          <img src="<?php echo $picture; ?>" width="80px" height="80px" onerror="this.style.display='none';">
          <p class="p"><?php echo $firstname . " " . $middlename . " " . $lastname; ?></p>
      </div>
      <div class="profile-icon" onclick="toggleProfileDetails()">
          <i class="fas fa-user"></i>
          <div class="profile-details-container" id="profileDetailsContainer">
              <div class="profile">
              <?php
                include 'connection.php';

                $sql = "SELECT * FROM proof_of_identity WHERE id = (SELECT id FROM profiledata WHERE email = ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $_SESSION['username']);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $picture = $row["picture"];
                } else {
                    $picture = "";
                }

                $sql = "SELECT * FROM profiledata WHERE email = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $_SESSION['username']);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $firstname = $row["firstname"];
                    $middlename = $row["middlename"];
                    $lastname = $row["lastname"];
                } else {
                    $firstname = "";
                    $middlename = "";
                    $lastname = "";
                }

                $sql = "SELECT * FROM users WHERE userName = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $_SESSION['username']);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $userType = $row["userType"];
                } else {
                    $userType = "";
                }

                $stmt->close();
                $conn->close();
                ?>
                <img src="<?php echo $picture; ?>" alt="Barangay Hall of Paule 1" width="80px" height="80px">
                <div class="adminname">
                  <p class="p1"><?php echo $firstname . " " . $middlename . " " . $lastname; ?></p>
                  <p class="p2"><?php echo $userType; ?></p>
                </div>
              </div>
              <hr>
              <a href="Profile.php"><i class="bi bi-person"></i> Profile</a>
              <a href="Password.php"><i class="bi bi-key"></i> Reset Password</a>
              <hr>
              <a href="#" onclick="confirmLogout()"><i class="bi bi-box-arrow-right"></i> Log Out</a>
          </div>
      </div>
    </div>
    <div class="navigation" id="navigation">
      <div class="logo">
        <img src="images/logo1.png" alt="Barangay Logo" height="40px" width="40px">
        <p>Barangay Records</p>
      </div>
      <div class="administrators">
        <p><em> Administrator</em></p>
      </div>
      <a href="AdminDashboard.php" class="a1"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
      <a href="BarangayOfficial.php" class="a1"><i class="fas fa-users"></i> Barangay Officials</a>
      <a href="Blotter.php" class="a1"><i class="fas fa-book"></i> Blotter</a>
      <a href="Resident.php" class="a1"><i class="fas fa-users"></i> Residents</a>
      <a href="DocumentRequest.php" class="a1" id="requests"><i class="fas fa-file"></i> Document Requests</a>
      <a href="Users.php" class="a1"><i class="fas fa-users-cog"></i> Users</a>
      <a href="Activity.php" class="a1"><i class="bi bi-activity"></i> Activity</a>
      <div class="dropdown" onclick="toggleDropdown()">
        <button class="btn btn-primary plus-toggle" type="button" id="dropdownMenuButton" >
          <i class="fas fa-cog"></i>Page <i class="bi bi-plus"></i>
        </button>
        <div class="dropdown-content" id="dropdownContent">
          <a href="Information.php" id="info" onclick="keepDropdownOpen()"><i class="fas fa-chevron-right"></i>Information</a>
          <a href="Forms.php"><i class="fas fa-chevron-right"></i>Forms</a>
          <a href="BarangayFAQ.php"><i class="fas fa-chevron-right"></i>FAQ</a>
          <a href="BarangayContact&Message.php" class="contact1"><i class="fas fa-chevron-right"></i>Contact</a>
        </div>
      </div>            
      <a href="#" onclick="confirmLogout()"><i class="bi bi-box-arrow-right"></i> Log Out</a>
    </div>
  </div>
  
  <div class="title-with-icon">
    <a href="AdminDashboard.php" title="Dashboard"><i class="bi bi-house"></i></a>
    <p>Barangay Information</p>
  </div>

  <div class="overlay" id="overlay" onclick="hideIntroForm()"></div>
  <div class="overlay" id="overlay" onclick="hideMisForm()"></div>
  <div class="overlay" id="overlay" onclick="hideVisForm()"></div>
  <div class="overlay" id="overlay" onclick="hideGoalForm()"></div>
  <div class="overlay" id="overlay" onclick="hideStaForm()"></div>
  <div class="overlay" id="overlay" onclick="hidePopForm()"></div>
  <div class="overlay" id="overlay" onclick="hidePSForm()"></div>
  <div class="overlay" id="overlay" onclick="hidePEAForm()"></div>
  <div class="overlay" id="overlay" onclick="hideBEForm()"></div>
  <div class="overlay" id="overlay" onclick="hideIncomeForm()"></div>
  <div class="overlay" id="overlay" onclick="hideHisForm()"></div>
  <div class="overlay" id="overlay" onclick="hideMapForm()"></div>

  <div class="dashboard-box-container1">
    <div class="dashboard-box1" style="margin-right: 20px;" id="db1">
        <div class="box-title7"><h3>Introduction</h3></div><hr>
        <div class="textcon" style="max-height: 57%; overflow-y: auto; text-align: justify !important;
        padding-left: 10px; padding-right: 7px;">
          <?php
            include 'connection.php';

            $sql = "SELECT paragraph FROM introduction";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $currentParagraph = $row["paragraph"];
                echo "<p>$currentParagraph</p>";
            } else {
                echo "0 results";
            }
            $conn->close();
          ?>
        </div>
        <div class="info">
          <button type="button" class="btn btn-primary" onclick="showIntroForm()">Edit</button>
        </div>
  </div>
    <div class="dashboard-box1" style="margin-right: 20px;" id="db2">
        <div class="box-title7"><h3>Mission</h3></div><hr>
        <div class="textcon" style="max-height: 57%; overflow-y: auto; text-align: justify !important;
        padding-left: 10px; padding-right: 7px;">
          <?php
            include 'connection.php';

            $sql = "SELECT paragraph FROM mission";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $currentParagraph = $row["paragraph"];
                echo "<p>$currentParagraph</p>";
            } else {
                echo "0 results";
            }

            $conn->close();
          ?>
        </div>
        <div class="info"><button type="button" class="btn btn-primary" onclick="showMisForm()">Edit</button></div>
    </div>
    <div class="dashboard-box1" id="db2">
        <div class="box-title7"><h3>Vision</h3></div><hr>
        <div class="textcon" style="max-height: 57%; overflow-y: auto; text-align: justify !important;
        padding-left: 10px; padding-right: 7px;">
          <?php
            include 'connection.php';

            $sql = "SELECT paragraph FROM vision"; 
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $currentVision = $row["paragraph"];
                echo "<p>$currentVision</p>";
            } else {
                echo "<p>Vision content not available.</p>";
            }

            $conn->close();
        ?>
        </div>
        <div class="info"><button type="button" class="btn btn-primary" onclick="showVisForm()">Edit</button></div>
    </div>
  </div>
  <div class="dashboard-box-container2">
    <div class="dashboard-box2" style="margin-right: 20px;" id="db4">
        <div class="box-title7"><h3>Maps Statistics</h3></div><hr>
        <div class="textcon" style="max-height: 200px; overflow-y: auto; text-align: center !important;
        padding-left: 10px; padding-right: 7px;">
        <?php
        include 'connection.php';

        $sql = "SELECT total_land_area, land_used FROM map_statics"; 
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<dl>";
                echo "<dt>Total Land Area </dt>";
                echo "<dd>" . $row["total_land_area"] . "</dd>";
                echo "<dt>Land Area Used </dt>";
                echo "<dd>" . $row["land_used"] . "</dd>";
                echo "</dl>";
            }
        } else {
            echo "No data found";
        }

        $conn->close();
        ?>
        </div>
        <div class="info"><button type="button" class="btn btn-primary" onclick="showMapForm()">Edit</button></div>
    </div>
    <div class="dashboard-box2" style="margin-right: 20px;" id="db5">
        <div class="box-title7"><h3>Statistics</h3></div><hr>
        <div class="textcon" style="max-height: 57%; overflow-y: auto; text-align: center !important;
        padding-left: 10px; padding-right: 7px;">
        <?php
          include 'connection.php';

          $sql = "SELECT founding_years, environmental_health_status, partnerships_organization, projects_made FROM statistics"; 
          $result = $conn->query($sql);

          if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                  echo "<dl>";
                  echo "<dt>Founding Years </dt>";
                  echo "<dd>" . $row["founding_years"] . "</dd>";
                  echo "<dt>Environmental Health Status </dt>";
                  echo "<dd>" . $row["environmental_health_status"] . "</dd>";
                  echo "<dt>Partnerships Organization </dt>";
                  echo "<dd>" . $row["partnerships_organization"] . "</dd>";
                  echo "<dt>Projects Made </dt>";
                  echo "<dd>" . $row["projects_made"] . "</dd>";
                  echo "</dl>";
              }
          } else {
              echo "No data found";
          }

          $conn->close();
        ?>
        </div>
        <div class="info"><button type="button" class="btn btn-primary" onclick="showStaForm()">Edit</button></div>
    </div>
    <div class="dashboard-box2" id="db5">
        <div class="box-title7"><h3>History</h3></div><hr>
        <div class="textcon" style="max-height: 57%; overflow-y: auto; text-align: justify !important;
        padding-left: 10px; padding-right: 7px;">
          <?php
            include 'connection.php';

            $sql = "SELECT context FROM history";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $currentContext = $row["context"];
                echo "<p>$currentContext</p>";
            } else {
                echo "0 results";
            }

            $conn->close();
          ?>
        </div>
        <div class="info"><button type="button" class="btn btn-primary" onclick="showHisForm()">Edit</button></div>
    </div>
  </div>
  <div class="dashboard-box-container3">
    <div class="dashboard-box3" style="margin-right: 20px;" id="db4">
        <div class="box-title7"><h3>Population Statistics</h3></div><hr>
        <div class="textcon" style="max-height: 57%; overflow-y: auto; text-align: center !important;
        padding-left: 10px; padding-right: 7px;">
          <?php
            include 'connection.php';

            $sql = "SELECT number_of_population, average_household_size FROM population"; 
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<dl>";
                    echo "<dt>Number of Population </dt>";
                    echo "<dd>" . $row["number_of_population"] . "</dd>";
                    echo "<dt>Average Household Size </dt>";
                    echo "<dd>" . $row["average_household_size"] . "</dd>";
                    echo "</dl>";
                }
            } else {
                echo "No data found";
            }

            $conn->close();
          ?>
        </div>
        <div class="info"><button type="button" class="btn btn-primary" onclick="showPSForm()">Edit</button></div>
    </div>
    <div class="dashboard-box3" style="margin-right: 20px;" id="db5">
        <div class="box-title7"><h3>Predominant Economic Activities</h3></div><hr>
        <div class="textcon" style="max-height: 57%; overflow-y: auto; text-align: justify !important;
        padding-left: 10px; padding-right: 7px;">
        <?php
          include 'connection.php';

          $sql = "SELECT * FROM economics";
          $result = $conn->query($sql);

          $currentContent = ""; 

          if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                echo "<ul>";
                echo "<li>" . $row["message"] . "</li>";
                echo "</ul>"; 
              }
          } else {
              $currentContent = "<p>No data available</p>"; 
          }
        ?>
        </div>
        <div class="info"><button type="button" class="btn btn-primary" onclick="showPEAForm()">Edit</button></div>
    </div>
    <div class="dashboard-box3" id="db5">
        <div class="box-title7"><h3>Major Business Establishments</h3></div><hr>
        <div class="textcon" style="max-height: 57%; overflow-y: auto; text-align: justify !important;
        padding-left: 10px; padding-right: 7px;">
        <?php
          include 'connection.php';

          $sql = "SELECT * FROM major_business";
          $result = $conn->query($sql);

          $currentContent = ""; 

          if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                echo "<ul>";
                echo "<li>" . $row["business_text"] . "</li>";
                echo "</ul>"; 
              }
          } else {
              $currentContent = "<p>No data available</p>"; 
          }
        ?>
        </div>
        <div class="info"><button type="button" class="btn btn-primary" onclick="showBEForm()">Edit</button></div>
    </div>
  </div>
  <div class="dashboard-box-container4">
    <div class="dashboard-box4" id="db4">
        <div class="box-title7"><h3>Major Source of Household Income</h3></div><hr>
        <div class="textcon" style="max-height: 57%; overflow-y: auto; text-align: justify !important;
        padding-left: 10px; padding-right: 7px;">
            <?php
          include 'connection.php';

          $sql = "SELECT * FROM major_income";
          $result = $conn->query($sql);

          $currentContent = ""; 

          if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                echo "<ul>";
                echo "<li>" . $row["income_text"] . "</li>";
                echo "</ul>"; 
              }
          } else {
              $currentContent = "<p>No data available</p>"; 
          }
        ?>
        </div>
        <div class="info"><button type="button" class="btn btn-primary" onclick="showIncomeForm()">Edit</button></div>
    </div>
  </div>
  <footer class="footer">
    <div class="container">
        <p>&copy; 2024 Barangay Paule 1. All rights reserved.</p>
    </div>
  </footer>

  <div class="form-container" id="infoContainer">
    <div class="title">
      <i class="fas fa-handshake"></i><h2>Edit Introduction</h2>
      <button type="button" class="btn-close" aria-label="Close" onclick="hideIntroForm()"></button>
    </div>
    <form action="information_update.php" method="POST" onsubmit="return validateForm()">
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <div class="input-group">
              <?php
                include 'connection.php';

                $sql = "SELECT paragraph FROM introduction";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $currentParagraph = $row["paragraph"];
                    echo '<textarea id="message" name="message" rows="5" placeholder="Type any message" required>' . $currentParagraph . '</textarea>';
                } else {
                    echo "0 results";
                }

                $conn->close();
              ?>
            </div>
          </div>
        </div>
        <div class="form-group text-center">
          <div class="col">
            <button type="button" class="btn btn-secondary btn3" onclick="hideIntroForm()">Close</button>
            <button type="submit" class="btn btn-primary btn4">Update</button>
          </div>
        </div>
      </div>
    </form>
  </div>

  <div class="form-container" id="MisContainer">
    <div class="title">
      <i class="fas fa-bullseye"></i><h2>Edit Mission</h2>
      <button type="button" class="btn-close" aria-label="Close" onclick="hideMisForm()"></button>
    </div>
    <form action="information_update.php" method="POST" onsubmit="return validateMission()">
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <div class="input-group">
              <?php
                include 'connection.php';

                $sql = "SELECT paragraph FROM mission";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $currentParagraph = $row["paragraph"];
                } else {
                    $currentParagraph = ""; 
                }

                $conn->close();
              ?>
              <textarea id="messageMission" name="messageMission" rows="5" placeholder="Type any message" required><?php echo $currentParagraph; ?></textarea>            
            </div>
          </div>
        </div>
        <div class="form-group text-center">
          <div class="col">
            <button type="button" class="btn btn-secondary btn3" onclick="hideMisForm()">Close</button>
            <button type="submit" class="btn btn-primary btn4">Update</button>
          </div>
        </div>
      </div>
    </form>
  </div>

  <div class="form-container" id="visContainer">
    <div class="title">
      <i class="fas fa-eye"></i><h2>Edit Vision</h2>
      <button type="button" class="btn-close" aria-label="Close" onclick="hideVisForm()"></button>
    </div>
    <form action="information_update.php" method="POST" onsubmit="return validateVision()">
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <div class="input-group">
            <?php
              include 'connection.php';

              $sql = "SELECT paragraph FROM vision"; 
              $result = $conn->query($sql);

              if ($result->num_rows > 0) {
                  $row = $result->fetch_assoc();
                  $currentVision = $row["paragraph"];
              } else {
                  $currentVision = "Vision content not found.";
              }

              $conn->close();
              ?>
            <textarea id="messageVision" name="messageVision" rows="5" placeholder="Type any message" required><?php echo $currentVision; ?></textarea>
            </div>
            </div>
          </div>
        </div>
        <div class="form-group text-center">
          <div class="col">
            <button type="button" class="btn btn-secondary btn3" onclick="hideVisForm()">Close</button>
            <button type="submit" class="btn btn-primary btn4">Update</button>
          </div>
        </div>
      </div>
    </form>
  </div>

  <div class="form-container" id="mapContainer">
    <div class="title">
      <i class="fas fa-map-marker-alt"></i><h2>Edit Map Statistics</h2>
      <button type="button" class="btn-close" aria-label="Close" onclick="hideMapForm()"></button>
    </div>
    <form action="information_update.php" method="POST">
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label for="message" class="lab">Total Land Area</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                <?php
                  include 'connection.php';

                  $sql = "SELECT total_land_area FROM map_statics"; 
                  $result = $conn->query($sql);

                  if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $currentTotalLandArea = $row["total_land_area"];
                  } else {
                    $currentTotalLandArea = "Not available";
                  }

                  $conn->close();
                ?>
              <input type="number" class="form-control" id="totalLandArea" name="totalLandArea" placeholder="Enter land area" step="any" value="<?php echo $currentTotalLandArea; ?>">
            </div>
          </div>
          <div class="form-group">
            <label for="message" class="lab">Land Used</label>
            <div class="input-group">
            <span class="input-group-text"><i class="bi bi-check"></i></span>
              <?php
                  include 'connection.php';

                  $sql = "SELECT land_used FROM map_statics WHERE id = 1";
                  $result = $conn->query($sql);

                  if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $currentLandUsed = $row["land_used"];
                  } else {
                    $currentLandUsed = "Not available";
                  }

                  $conn->close();
                ?>
              <input type="number" class="form-control" id="landUsed" name="landUsed" placeholder="Enter land used" step="any" value="<?php echo $currentLandUsed; ?>">
            </div>
          </div>
        </div>
        <div class="form-group text-center">
          <div class="col">
            <button type="button" class="btn btn-secondary btn3" onclick="hideMapForm()">Close</button>
            <button type="submit" class="btn btn-primary btn4">Update</button>
          </div>
        </div>
      </div>
    </form>
  </div>

  <div class="form-container" id="staContainer">
    <div class="title">
      <i class="fas fa-chart-bar"></i><h2>Edit Statistics</h2>
      <button type="button" class="btn-close" aria-label="Close" onclick="hideStaForm()"></button>
    </div>
    <form action="information_update.php" method="POST">
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label for="message" class="lab">Foundating Years</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-people"></i></span>
                <?php
                  include 'connection.php';

                  $sql = "SELECT founding_years FROM statistics";
                  $result = $conn->query($sql);

                  if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $foundingYears = $row["founding_years"];
                  } else {
                    $foundingYears = "Not available";
                  }

                  $conn->close();
                ?>
              <input type="number" class="form-control" id="userName" name="founding_years" placeholder="Enter foundng years" value="<?php echo $foundingYears; ?>">
            </div>
          </div>
          <div class="form-group">
            <label for="message" class="lab">Environmental Health Status</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-tree"></i></span>
                <?php
                  include 'connection.php';

                  $sql = "SELECT environmental_health_status FROM statistics";
                  $result = $conn->query($sql);

                  if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $environmentalhealthstatus = $row["environmental_health_status"];
                  } else {
                    $environmentalhealthstatus = "Not available";
                  }

                  $conn->close();
                ?>
              <input type="number" class="form-control" id="userName" name="environmental_health_status" placeholder="Enter environmental health status" value="<?php echo $environmentalhealthstatus; ?>">
            </div>
          </div>
          <div class="form-group">
            <label for="message" class="lab">Partnerships Organization</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-building"></i></span>
                <?php
                  include 'connection.php';

                  $sql = "SELECT partnerships_organization FROM statistics";
                  $result = $conn->query($sql);

                  if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $partnershipsOrganization = $row["partnerships_organization"];
                  } else {
                    $partnershipsOrganization = "Not available";
                  }

                  $conn->close();
                ?>
              <input type="number" class="form-control" id="userName" name="partnerships_organization" placeholder="Enter partnerships organization" value="<?php echo $partnershipsOrganization; ?>">
            </div>
          </div>
          <div class="form-group">
            <label for="message" class="lab">Projects Made</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-briefcase"></i></span>
                <?php
                  include 'connection.php';

                  $sql = "SELECT projects_made FROM statistics";
                  $result = $conn->query($sql);

                  if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $projectsmade = $row["projects_made"];
                  } else {
                    $projectsmade = "Not available";
                  }

                  $conn->close();
                ?>
              <input type="number" class="form-control" id="userName" name="projects_made" placeholder="Enter projects made" value="<?php echo $projectsmade; ?>">
            </div>
          </div>
        </div>
        <div class="form-group text-center">
          <div class="col">
            <button type="button" class="btn btn-secondary btn3" onclick="hideStaForm()">Close</button>
            <button type="submit" class="btn btn-primary btn4">Update</button>
          </div>
        </div>
      </div>
    </form>
  </div>

  <div class="form-container" id="hisContainer">
    <div class="title">
      <i class="fas fa-history"></i><h2>Edit History</h2>
      <button type="button" class="btn-close" aria-label="Close" onclick="hideHisForm()"></button>
    </div>
    <form action="information_update.php" method="POST">
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <div class="input-group">
              <?php
                include 'connection.php';

                $sql = "SELECT context FROM history WHERE id = 1"; 
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                  $row = $result->fetch_assoc();
                  $currentContext = $row["context"];
                } else {
                  $currentContext = "History context not found.";
                }

                $conn->close();
              ?>
              <textarea id="context" name="context" rows="5" placeholder="Type any message" required><?php echo $currentContext; ?></textarea>
            </div>
          </div>
        </div>
        <div class="form-group text-center">
          <div class="col">
            <button type="button" class="btn btn-secondary btn3" onclick="hideHisForm()">Close</button>
            <button type="submit" class="btn btn-primary btn4">Update</button>
          </div>
        </div>
      </div>
    </form>
  </div>

  <div class="form-container" id="psContainer">
    <div class="title">
      <i class="fas fa-user-plus"></i><h2>Edit Population</h2>
      <button type="button" class="btn-close" aria-label="Close" onclick="hidePSForm()"></button>
    </div>
    <form action="information_update.php" method="POST">
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label for="message" class="lab">Number of Population</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-people"></i></span>
                <?php
                  include 'connection.php';

                  $sql = "SELECT number_of_population FROM population";
                  $result = $conn->query($sql);

                  if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $number_of_population = $row["number_of_population"];
                  } else {
                    $number_of_population = "";
                  }

                  $conn->close();
                ?>
              <input type="number" class="form-control" id="userName" name="number_of_population" placeholder="Enter username" value="<?php echo $number_of_population; ?>">
            </div>
          </div>
          <div class="form-group">
            <label for="message" class="lab">Average Household Size</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-house"></i></span>
              <?php
                  include 'connection.php';

                  $sql = "SELECT average_household_size FROM population";
                  $result = $conn->query($sql);

                  if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $averagehouseholdsize  = $row["average_household_size"];
                  } else {
                    $averagehouseholdsize  = "";
                  }

                  $conn->close();
                ?>
              <input type="number" class="form-control" id="userName" name="average_household_size" placeholder="Enter username" value="<?php echo $averagehouseholdsize; ?>">
            </div>
          </div>
        </div>
        <div class="form-group text-center">
          <div class="col">
            <button type="button" class="btn btn-secondary btn3" onclick="hidePSForm()">Close</button>
            <button type="submit" class="btn btn-primary btn4">Update</button>
          </div>
        </div>
      </div>
    </form>
  </div>

  <div class="form-container" id="peaContainer">
    <div class="title">
        <i class="fas fa-industry"></i><h2>Edit Economics</h2>
        <button type="button" class="btn-close" aria-label="Close" onclick="hidePEAForm()"></button>
    </div>
    <form action="information_update.php" method="POST">
        <?php
        include 'connection.php';

        $sql = "SELECT id, message FROM economics";
        $result = $conn->query($sql);

        $messages = '';

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $messages .= $row["message"] . "\n"; 
                echo '<input type="hidden" name="id[]" value="' . $row["id"] . '">'; 
            }
        } else {
            $messages = "Economic messages not found.";
        }

        $conn->close();
        ?>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <div class="input-group">
                        <textarea id="economics" name="messages" rows="5" placeholder="Type any message" required><?php echo $messages; ?></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group text-center">
            <div class="col" style="margin-left: -360px;">
                <button type="button" class="btn btn-secondary btn3" onclick="hidePEAForm()">Close</button>
                <button type="submit" class="btn btn-primary btn4">Update</button>
            </div>
        </div>
    </form>
  </div>

  <div class="form-container" id="befContainer">
    <div class="title">
        <i class="fas fa-building"></i><h2>Edit Major Business</h2>
        <button type="button" class="btn-close" aria-label="Close" onclick="hideBEForm()"></button>
    </div>
    <form action="information_update.php" method="POST">
        <?php
        include 'connection.php';

        $sql = "SELECT id, business_text FROM major_business";
        $result = $conn->query($sql);

        $business = '';

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $business .= $row["business_text"] . "\n"; 
                echo '<input type="hidden" name="id[]" value="' . $row["id"] . '">'; 
            }
        } else {
            $business = "Economic messages not found.";
        }

        $conn->close();
        ?>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <div class="input-group">
                        <textarea id="business_text" name="business_text" rows="5" placeholder="Type any message" required><?php echo $business; ?></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group text-center">
            <div class="col" style="margin-left: -360px;">
                <button type="button" class="btn btn-secondary btn3" onclick="hideBEForm()">Close</button>
                <button type="submit" class="btn btn-primary btn4">Update</button>
            </div>
        </div>
    </form>
</div>


  <div class="form-container" id="incomeContainer">
    <div class="title">
      <i class="fas fa-money-bill-wave"></i><h2>Edit Major Income</h2>
      <button type="button" class="btn-close" aria-label="Close" onclick="hideIncomeForm()"></button>
    </div>
    <form action="information_update.php" method="POST">
        <?php
        include 'connection.php';

        $sql = "SELECT id, income_text FROM major_income";
        $result = $conn->query($sql);

        $income = '';

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $income .= $row["income_text"] . "\n"; // Concatenate with previous data
                echo '<input type="hidden" name="id[]" value="' . $row["id"] . '">'; 
            }
        } else {
            $income = "Income messages not found.";
        }

        $conn->close();
        ?>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <div class="input-group">
                        <textarea id="income_text" name="income_text" rows="5" placeholder="Type any message" required><?php echo $income; ?></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group text-center">
            <div class="col" style="margin-left: -360px;">
                <button type="button" class="btn btn-secondary btn3" onclick="hideIncomeForm()">Close</button>
                <button type="submit" class="btn btn-primary btn4">Update</button>
            </div>
        </div>
    </form>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-euKpLsYQJz5jE0EEOxnTPI1a2ybp4QA9QfsB1LD73pI95/02djN3eVkD5bZlNumj" crossorigin="anonymous"></script>
  <script src="Admin.js"></script>
  <script>
    function validateForm() {
    var message = document.getElementById('message').value.trim();

    if (message === '') {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Please enter a message.'
        });
        return false;
    } else {
        Swal.fire({
            title: 'Are you sure?',
            text: 'Are you sure you want to update the introduction?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, update it!',
            cancelButtonText: 'No, cancel!',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                return true;
            } else {
                return false;
            }
        });
    }
}


    function validateMission() {
        var message = document.getElementById('message').value.trim();

        if (message === '') {
            alert('Please enter a message.');
            return false;
        } else {
            if (confirm('Are you sure you want to update the mission?')) {
                return true;
            } else {
                return false;
            }
        }
    }

    function validateVision() {
        var message = document.getElementById('message').value.trim();

        if (message === '') {
            alert('Please enter a message.');
            return false;
        } else {
            if (confirm('Are you sure you want to update the vision?')) {
                return true;
            } else {
                return false;
            }
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("peaContainer").addEventListener("keydown", function(event) {
      if (event.key === "Enter") {
        event.preventDefault();
        document.getElementById("peaContainer").submit(); 
      }
    });
  });
  
  function confirmLogout() {
        Swal.fire({
            title: 'Are you sure?',
            text: 'Are you sure you want to log out?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, log out',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'Information.php?logout=true';
            }
        });
    }
  </script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.1.7/sweetalert2.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-euKpLsYQJz5jE0EEOxnTPI1a2ybp4QA9QfsB1LD73pI95/02djN3eVkD5bZlNumj" crossorigin="anonymous"></script>
  <script src="js/Admin.js"></script>
</body>
</html>