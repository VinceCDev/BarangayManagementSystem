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
  <title>Residents</title>
  <link rel="icon" href="images/logo1.png" type="image/x-icon">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="css/Resident.css">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.1.7/sweetalert2.min.css">
</head>
<body>
    <div>
        <div class="header" style="margin-left: 269px;">
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
                  <a href="UserProfile.php"><i class="bi bi-person"></i> Profile</a>
                  <a href="ForgotPassword.php"><i class="bi bi-key"></i> Reset Password</a>
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
            <a href="Resident.php" class="a1" id="resident"><i class="fas fa-users"></i> Residents</a>
            <a href="DocumentRequest.php" class="a1" id="requests"><i class="fas fa-file"></i> Document Requests</a>
            <a href="Users.php" class="a1"><i class="fas fa-users-cog"></i> Users</a>
            <a href="Activity.php" class="a1"><i class="bi bi-activity"></i> Activity</a>
            <div class="dropdown" onclick="toggleDropdown()">
              <button class="btn btn-primary plus-toggle" type="button" id="dropdownMenuButton" >
                <i class="fas fa-cog"></i>Page <i class="bi bi-plus"></i>
              </button>
              <div class="dropdown-content" id="dropdownContent">
                <a href="Information.php"><i class="fas fa-chevron-right"></i>Information</a>
                <a href="Forms.php"><i class="fas fa-chevron-right"></i>Forms</a>
                <a href="BarangayFAQ.php"><i class="fas fa-chevron-right"></i>FAQ</a>
              </div>
            </div>
            <a href="#" onclick="confirmLogout()" class="a1"><i class="fas fa-sign-out-alt"></i> Log Out</a>
          </div>
    </div>
    
    <div class="title-with-icon" style="margin-left: 269px; z-index: 100;">
        <a href="AdminDashboard.php" title="Dashboard"><i class="bi bi-house"></i></a>
        <p>Barangay Residents Records</p>
    </div>
   
    <div class="overlay" id="overlay" onclick="hideResidentForm()"></div>
    
    <?php
        include 'connection.php';

        $sql = "SELECT COUNT(*) AS totalResidents FROM residents";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $rowCount = $row['totalResidents'];
        } else {
            $rowCount = 0;
        }

        $conn->close();
    ?>

    <div class="resident" id="barangayOfficialsDashboard">
        <div class="title-with-icon1">
            <i class="fas fa-chart-line"></i>
            <h3 style="margin-right: 932px;">List Chart</h3>
            <button type="button" class="btn btn-success" onclick="showResidentForm()">Add Barangay Resident</button>
        </div>
        <hr>
        <div class="heading-and-buttons">
            <div class="show-entries">
                <label for="entries">Show Entries: </label>
                <input type="number" title="number" placeholder="0" value="<?php echo $rowCount; ?>">
            </div>    
            <div class="search-bar">
                <p>Search: </p><input type="text" id="searchInput" onkeyup="searchResident()" placeholder="Search for names..." style="padding-left: 10px;">
            </div>
        </div>
        <hr>
        <table class="table-no-border">
            <thead>
                <tr>
                    <th>Photo</th>
                    <th>Name</th>
                    <th>Age</th>
                    <th>Occupation</th>
                    <th>Address</th>
                    <th>Contact</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody style="color: #000;">
                <?php
                include 'connection.php';

                $limit = 5;
                $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
                $offset = ($currentPage - 1) * $limit;

                $searchInput = isset($_GET['search']) ? $_GET['search'] : '';

                $sql = "SELECT * FROM residents WHERE full_name LIKE '%$searchInput%' LIMIT $limit OFFSET $offset";

                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td><img src='resident_photo/" . ($row["photo"] ?? '') . "' alt='Photo' style='max-width: 70px; max-height: 70px; border-radius:50%'></td>";
                        echo "<td>".$row['full_name']."</td>";
                        echo "<td>".$row['age']."</td>";
                        echo "<td>".$row['occupation']."</td>";
                        echo "<td>".$row['birth_place']."</td>";
                        echo "<td>".$row['contact']."</td>";
                        echo "<td class='action-buttons'>";
                        echo "<a href='#' onclick='editResident(" . $row['id'] . ")' class='btn btn-primary btnedit' style='margin-right:5px;'>Edit</a>";
                        echo "<button type='button' class='btn btn-danger' onclick='deleteResident(" . ($row['id'] ?? '') . ")'>Delete</button>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No matching records found</td></tr>";
                }

                $conn->close();
                ?>
            </tbody>
        </table>
        <div class="navigation-buttons">
            <p>Showing <?php echo $rowCount; ?> of <?php echo $limit; ?> entries.</p>
            <a href="?page=<?php echo $currentPage > 1 ? $currentPage - 1 : 1; ?>" class="btn <?php echo $currentPage == 1 ? 'btn-secondary disabled' : 'btn-primary'; ?>">Previous</a>
            <a href="?page=<?php echo $currentPage < ceil($rowCount / $limit) ? $currentPage + 1 : ceil($rowCount / $limit); ?>" class="btn <?php echo $currentPage == ceil($rowCount / $limit) ? 'btn-secondary disabled' : 'btn-primary'; ?>">Next</a>
        </div>
    </div>
    <footer class="footer" style="margin-top: 85px;">
    <div class="container">
        <p>&copy; 2024 Your Website Name. All rights reserved.</p>
    </div>
    </footer>
    
    <?php
    include 'connection.php';

    function fetchResidentData($conn, $residentId) {
        $residentId = mysqli_real_escape_string($conn, $residentId);
        
        $sql = "SELECT * FROM residents WHERE id = '$residentId'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row;
        } else {
            return null;
        }
    }

    if (isset($_GET['id'])) {
        $residentData = fetchResidentData($conn, $_GET['id']);

        if ($residentData) {
            $residentid = $residentData["id"];
            $residentphoto = $residentData["photo"];
            $residentfullname = $residentData["full_name"];
            $residentbirthdate = $residentData["birth_date"];
            $residentbirthplace  = $residentData["birth_place"];
            $residentage = $residentData["age"];
            $residenttotalhouseholds = $residentData["total_households"];
            $residentcontact = $residentData["contact"];
            $residentbloodtype = $residentData["blood_type"];
            $residentcivilstatus = $residentData["civil_status"];
            $residentoccupation = $residentData["occupation"];
            $residentmonthlyincome = $residentData["monthly_income"];
            $residenthouseholdperhead  = $residentData["household"];
            $residentlengthofstay = $residentData["length_of_stay"];
            $residentreligion = $residentData["religion"];
            $residentnationality = $residentData["nationality"];
            $residentgender = $residentData["gender"];
            $residenteducation = $residentData["education"];
        } else {
            echo "Resident not found";
        }
    } else {
        echo "Resident ID not provided";
    }
    ?>

    <div class="form-container" id="editresidentFormContainer" style="display:none;">
        <div class="heading-with-icon"><i class="fas fa-user-plus"></i><h3>Edit Resident</h3>
            <button type="button" class="btn-close" aria-label="Close" onclick="hideEditResidentForm()"></button>
        </div>
    <form action="resident_update.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="official_id" value="<?php echo $residentid; ?>">
        <div class="form-group">
            <label for="residentFirstName">Photo</label>
            <div class="input-group">
                <input type="file" class="form-control" id="residentphoto" name="photo" placeholder="Choose photo" value="<?php echo $residentphoto; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="residentMiddleName">Full Name</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person"></i></span>
                <input type="text" class="form-control" id="residentFullName" name="residentFullName" placeholder="Enter full name" value="<?php echo $residentfullname; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="residentBirthDate">Birth Date</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                <input type="date" class="form-control" id="residentBirthDate" name="residentBirthDate" placeholder="Enter birth date" value="<?php echo $residentbirthdate; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="residentBirthPlace">Birth Place</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                <input type="text" class="form-control" id="residentBirthPlace" name="residentBirthPlace" placeholder="Enter birth place" value="<?php echo $residentbirthplace; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="residentAge">Age</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-people"></i></span>
                <input type="number" class="form-control" id="residentAge" name="residentAge" placeholder="Enter age" value="<?php echo $residentage; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="residentTotalHouseholds">Total Households</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-house-door"></i></span>
                <input type="number" class="form-control" id="residentTotalHouseholds" name="residentTotalHouseholds" placeholder="Enter total households" value="<?php echo $residenttotalhouseholds; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="residentMaritalStatus">Contact</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person"></i></span>
                <input type="tel" class="form-control" id="residentContact" name="residentContact" placeholder="Enter contact" value="<?php echo $residentcontact; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="residentBloodType">Blood Type</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-heart"></i></span>
                <select class="form-control" id="residentBloodType" name="residentBloodType">
                    <option value="" disabled selected><?php echo $residentbloodtype; ?></option>
                    <option value="O">O</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="AB">AB</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="residentCivilStatus">Civil Status</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person"></i></span>
                <select class="form-control" id="residentCivilStatus" name="residentCivilStatus">
                    <option value="" disabled selected><?php echo $residentcivilstatus; ?></option>
                    <option value="Single">Single</option>
                    <option value="Married">Married</option>
                    <option value="Separated">Separated</option>
                    <option value="Widowed">Widowed</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="residentOccupation">Occupation</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-briefcase"></i></span>
                <input type="text" class="form-control" id="residentOccupation" name="residentOccupation" placeholder="Enter occupation" value="<?php echo $residentoccupation; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="residentMonthlyIncome">Monthly Income</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-cash"></i></span>
                <input type="number" class="form-control" id="residentMonthlyIncome" name="residentMonthlyIncome" placeholder="Enter monthly income" value="<?php echo $residentmonthlyincome; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="residentHousehold">Household Per Head</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-house"></i></span>
                <input type="number" class="form-control" id="residentHousehold" name="residentHousehold" placeholder="Enter household" value="<?php echo $residenthouseholdperhead; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="residentLengthOfStay">Length of Stay&nbsp;(<em>in years</em>)</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-calendar-check"></i></span>
                <input type="number" class="form-control" id="residentLengthOfStay" name="residentLengthOfStay" placeholder="Enter length of stay" value="<?php echo $residentlengthofstay; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="residentReligion">Religion</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-star"></i></span>
                <input type="text" class="form-control" id="residentReligion" name="residentReligion" placeholder="Enter religion" value="<?php echo $residentreligion; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="residentNationality">Nationality</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-flag"></i></span>
                <input type="text" class="form-control" id="residentNationality" name="residentNationality" placeholder="Enter nationality" value="<?php echo $residentnationality; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="residentGender">Gender</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person"></i></span>
                <select class="form-control" id="residentGender" name="residentGender">
                    <option value="" disabled selected><?php echo $residentgender; ?></option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Others">Others</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="residentEducation">Educational Attainment</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-book"></i></span>
                <select class="form-control" id="residentEducation" name="residentEducation">
                    <option value="" disabled selected><?php echo $residenteducation; ?></option>
                    <option value="Elementary">Elementary</option>
                    <option value="High School">High School</option>
                    <option value="College">College</option>
                </select>
            </div>
        </div>
        <div class="form-group text-center">
            <div class="col">
                <button type="button" class="btn btn-secondary btn3" onclick="hideEditResidentForm()">Close</button>
                <button type="submit" class="btn btn-primary btn4" id="update">Update</button>
            </div>
        </div>
    </form>
    </div>
    
    <div class="form-container" id="residentFormContainer">
        <div class="heading-with-icon"><i class="fas fa-user-plus"></i><h3>Add Resident</h3>
            <button type="button" class="btn-close" aria-label="Close" onclick="hideResidentForm()"></button>
        </div>
    <form action="resident_insert.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="residentFirstName">Photo</label>
            <div class="input-group">
                <input type="file" class="form-control" id="residentphoto" name="photo" placeholder="Choose photo">
            </div>
        </div>
        <div class="form-group">
            <label for="residentMiddleName">Full Name</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person"></i></span>
                <input type="text" class="form-control" id="residentFullName" name="residentFullName" placeholder="Enter full name">
            </div>
        </div>
        <div class="form-group">
            <label for="residentBirthDate">Birth Date</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                <input type="date" class="form-control" id="residentBirthDate" name="residentBirthDate" placeholder="Enter birth date">
            </div>
        </div>
        <div class="form-group">
            <label for="residentBirthPlace">Birth Place</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                <input type="text" class="form-control" id="residentBirthPlace" name="residentBirthPlace" placeholder="Enter birth place">
            </div>
        </div>
        <div class="form-group">
            <label for="residentAge">Age</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-people"></i></span>
                <input type="number" class="form-control" id="residentAge" name="residentAge" placeholder="Enter age">
            </div>
        </div>
        <div class="form-group">
            <label for="residentTotalHouseholds">Total Households</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-house-door"></i></span>
                <input type="number" class="form-control" id="residentTotalHouseholds" name="residentTotalHouseholds" placeholder="Enter total households">
            </div>
        </div>
        <div class="form-group">
            <label for="residentMaritalStatus">Contact</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person"></i></span>
                <input type="tel" class="form-control" id="residentContact" name="residentContact" placeholder="Enter contact">
            </div>
        </div>
        <div class="form-group">
            <label for="residentBloodType">Blood Type</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-heart"></i></span>
                <select class="form-control" id="residentBloodType" name="residentBloodType">
                    <option value="" disabled selected>Select Blood Type</option>
                    <option value="O">O</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="AB">AB</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="residentCivilStatus">Civil Status</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person"></i></span>
                <select class="form-control" id="residentCivilStatus" name="residentCivilStatus">
                    <option value="" disabled selected>Select Civil Status</option>
                    <option value="Single">Single</option>
                    <option value="Married">Married</option>
                    <option value="Separated">Separated</option>
                    <option value="Widowed">Widowed</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="residentOccupation">Occupation</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-briefcase"></i></span>
                <input type="text" class="form-control" id="residentOccupation" name="residentOccupation" placeholder="Enter occupation">
            </div>
        </div>
        <div class="form-group">
            <label for="residentMonthlyIncome">Monthly Income</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-cash"></i></span>
                <input type="number" class="form-control" id="residentMonthlyIncome" name="residentMonthlyIncome" placeholder="Enter monthly income">
            </div>
        </div>
        <div class="form-group">
            <label for="residentHousehold">Household Per Head</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-house"></i></span>
                <input type="number" class="form-control" id="residentHousehold" name="residentHousehold" placeholder="Enter household">
            </div>
        </div>
        <div class="form-group">
            <label for="residentLengthOfStay">Length of Stay(<em>in years</em>)</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-calendar-check"></i></span>
                <input type="number" class="form-control" id="residentLengthOfStay" name="residentLengthOfStay" placeholder="Enter length of stay">
            </div>
        </div>
        <div class="form-group">
            <label for="residentReligion">Religion</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-star"></i></span>
                <input type="text" class="form-control" id="residentReligion" name="residentReligion" placeholder="Enter religion">
            </div>
        </div>
        <div class="form-group">
            <label for="residentNationality">Nationality</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-flag"></i></span>
                <input type="text" class="form-control" id="residentNationality" name="residentNationality" placeholder="Enter nationality">
            </div>
        </div>
        <div class="form-group">
            <label for="residentGender">Gender</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person"></i></span>
                <select class="form-control" id="residentGender" name="residentGender">
                    <option value="" disabled selected>Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Others">Others</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="residentEducation">Educational Attainment</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-book"></i></span>
                <select class="form-control" id="residentEducation" name="residentEducation">
                    <option value="" disabled selected>Select Educational Attainment</option>
                    <option value="Elementary">Elementary</option>
                    <option value="High School">High School</option>
                    <option value="College">College</option>
                </select>
            </div>
        </div>
        <div class="form-group text-center">
            <div class="col">
                <button type="button" class="btn btn-secondary btn3" onclick="hideResidentForm()">Close</button>
                <button type="submit" class="btn btn-primary btn4" id="add">Add</button>
            </div>
        </div>
    </form>
    </div>
    <script>

    document.addEventListener('DOMContentLoaded', function() {
    var addBtn = document.getElementById('add');

    if (addBtn) {
        addBtn.addEventListener('click', function() {
        Swal.fire({
          icon: 'success',
          title: 'Official Added Successfully',
          text: 'You have added the official successfully.',
            timer: 12000,
            showConfirmButton: false
        }).then((result) => {
            if (result.dismiss === Swal.DismissReason.timer) {
                hideResidentForm();
            }
        });
        }, 3000);
    }
    });

    function deleteResident(residentId) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'You will not be able to recover this resident!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, cancel!',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        Swal.fire(
                            'Deleted!',
                            'Your resident has been deleted.',
                            'success'
                        ).then(() => {
                            location.reload();
                        });
                    }
                };
                xhttp.open("GET", "resident_delete.php?id=" + residentId, true);
                xhttp.send();
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                Swal.fire(
                    'Cancelled',
                    'Your resident is safe',
                    'error'
                );
            }
        });
    }

    function editResident(residentId) {
        if (residentId) {
            var editUrl = 'Resident.php?id=' + residentId;
            window.location.href = editUrl;
        } else {
            console.error("Invalid userId:", residentId);
        }
    }

    window.onload = function() {
        var urlParams = new URLSearchParams(window.location.search);
        var residentId = urlParams.get('id'); 
        
        if (residentId) {
            var editUrl = 'Resident.php?id=' + residentId;
            showEditResidentForm(editUrl);
        }
    }
    
    function showEditResidentForm(editUrl) {
    var overlay = document.getElementById('overlay');
    var formContainer = document.getElementById('editresidentFormContainer');
    var blurredBackground = document.createElement('div');
    blurredBackground.classList.add('blurred-background');

    document.body.appendChild(blurredBackground);

    overlay.style.display = 'block';
    formContainer.style.display = 'block';
    }

    function hideEditResidentForm() {
        var overlay = document.getElementById('overlay');
        var formContainer = document.getElementById('editresidentFormContainer');
        var blurredBackground = document.querySelector('.blurred-background'); 
        blurredBackground.parentNode.removeChild(blurredBackground);

        overlay.style.display = 'none';
        formContainer.style.display = 'none';

        window.location.href = 'Resident.php';
    }

    document.addEventListener('DOMContentLoaded', function() {
    var updateBtn = document.getElementById('update');

    if (updateBtn) {
        updateBtn.addEventListener('click', function() {
        Swal.fire({
            icon: 'success',
            title: 'Resident Edit Successfully',
            text: 'You have successfuly edit resident.',
            timer: 12000,
            showConfirmButton: false
        }).then((result) => {
            if (result.dismiss === Swal.DismissReason.timer) {
                hideEditResidentForm();
            }
        });
        }, 3000);
    }
    });

    function searchResident() {
        var input = document.getElementById("searchInput").value.toLowerCase();
        var tableRows = document.querySelectorAll("tbody tr"); 
        var filteredRows = 0;

        tableRows.forEach(function(row) {
        var cells = row.getElementsByTagName("td");
        var found = false;

        Array.from(cells).forEach(function(cell) {
            var cellText = cell.innerText.toLowerCase();
            if (cellText.includes(input)) {
                found = true;
            }
        });

        if (found) {
            row.style.display = "";
            filteredRows++;
        } else {
            row.style.display = "none";
        }
    });
    }

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
                window.location.href = 'Resident.php?logout=true';
            }
        });
    }

    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.1.7/sweetalert2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-euKpLsYQJz5jE0EEOxnTPI1a2ybp4QA9QfsB1LD73pI95/02djN3eVkD5bZlNumj" crossorigin="anonymous"></script>
    <script src="js/Admin.js"></script>
</body>
</html>