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
  <title>Blotter</title>
  <link rel="icon" href="images/logo1.png" type="image/x-icon">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="css/Blotter.css">
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
            <a href="Blotter.php" class="a1" id="blo"><i class="fas fa-book"></i> Blotter</a>
            <a href="Resident.php" class="a1"><i class="fas fa-users"></i> Residents</a>
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
                <a href="BarangayContact&Message.php" class="contact1" id="cont"><i class="fas fa-chevron-right"></i>Contact</a>
              </div>
            </div>
            <a href="#" onclick="confirmLogout()" class="a1"><i class="fas fa-sign-out-alt"></i> Log Out</a>
          </div>
    </div>
    
    <div class="title-with-icon" style="margin-left: 269px;">
        <a href="AdminDashboard.php" title="Dashboard"><i class="bi bi-house"></i></a>
        <p>Blotter Records</p>
    </div>

    <div class="overlay" id="overlay" onclick="hideBlotterForm()"></div>
    
    <?php
        include 'connection.php';

        $sql = "SELECT COUNT(*) AS totalBlotter FROM blotterrecords";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $countBlotter = $row['totalBlotter'];
        } else {
            $countBlotter = 0;
        }

        $conn->close();
    ?>

    <div class="blotter" id="barangayOfficialsDashboard">
        <div class="title-with-icon1">
            <i class="fas fa-chart-line"></i>
            <h3 style="margin-right: 1005px;">List Chart</h3>
            <button type="button" class="btn btn-success" onclick="showBlotterForm()">Add Blotter</button>
        </div>
        <hr>
        <div class="heading-and-buttons">
            <div class="show-entries">
                <label for="entries">Show Entries: </label>
                <input type="number" title="number" placeholder="0" value="<?php echo $countBlotter; ?>">
            </div>    
            <div class="search-bar">
                <p>Search: </p>
                <input type="text" id="searchInput" onkeyup="searchBlotter()" placeholder="Search for names..." style="padding-left: 10px;">
            </div>
        </div>
        <hr>
        <table class="table-no-border">
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Complainant</th> 
                    <th>Age</th>
                    <th>Contact</th>
                    <th>Person to Complaint</th>
                    <th>Age</th>
                    <th>Contact</th>
                    <th>Action Taken</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                include 'connection.php';

                $limit = 5;
                $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
                $offset = ($currentPage - 1) * $limit;

                $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

                $sql = "SELECT * FROM blotterrecords WHERE 
                            status LIKE '%$searchTerm%' OR 
                            complainant LIKE '%$searchTerm%' OR 
                            personToComplaint LIKE '%$searchTerm%' OR 
                            actionTaken LIKE '%$searchTerm%'
                        LIMIT $limit OFFSET $offset";

                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["status"] . "</td>";
                        echo "<td>" . $row["complainant"] . "</td>";
                        echo "<td>" . $row["age1"] . "</td>";
                        echo "<td>" . $row["contact1"] . "</td>";
                        echo "<td>" . $row["personToComplaint"] . "</td>";
                        echo "<td>" . $row["age2"] . "</td>";
                        echo "<td>" . $row["contact2"] . "</td>";
                        echo "<td>" . $row["actionTaken"] . "</td>";
                        echo "<td class='action-buttons'>";
                        echo "<a href='#' onclick='editBlotter(" . $row['id'] . ")' class='btn btn-primary btnedit' style='margin-right:5px;'>Edit</a>";
                        echo "<button type='button' class='btn btn-danger' onclick='deleteBlotter(".$row['id'].")'>Delete</button>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='9'>No Data Available</td></tr>";
                }

                $conn->close();
                ?>
            </tbody>
        </table>
        <div class="navigation-buttons">
            <p>Showing <?php echo $countBlotter; ?> of <?php echo $limit; ?> entries.</p>
            <a href="?page=<?php echo $currentPage > 1 ? $currentPage - 1 : 1; ?>" class="btn <?php echo $currentPage == 1 ? 'btn-secondary disabled' : 'btn-primary'; ?>">Previous</a>
            <a href="?page=<?php echo $currentPage < ceil($countBlotter / $limit) ? $currentPage + 1 : ceil($countBlotter / $limit); ?>" class="btn <?php echo $currentPage == ceil($countBlotter / $limit) ? 'btn-secondary disabled' : 'btn-primary'; ?>">Next</a>
        </div>
    </div>
    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 Barangay Paule 1. All rights reserved.</p>
        </div>
    </footer>

    <div class="form-container" id="blotterFormContainer">
        <div class="heading-with-icon">
            <i class="fas fa-balance-scale"></i><h3>Add Blotter</h3>
            <button type="button" class="btn-close" aria-label="Close" onclick="hideBlotterForm()"></button>
        </div>
    <form action="blotter_insert.php" method="POST">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="status"> Status</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-info-circle"></i></span>
                        <select class="form-control" id="status" name="status">
                            <option value="" disabled selected>Select Status</option>
                            <option value="Active">Active</option>
                            <option value="Not Active">Not Active</option>
                            <option value="Dismissed">Dismissed</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="complainant">Complainant</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" class="form-control" id="complainant" name="complainant" placeholder="Enter complainant">
                    </div>
                </div>
                <div class="form-group">
                    <label for="age1">Age</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="number" class="form-control" id="age1" name="age1" placeholder="Enter age">
                    </div>
                </div>
                <div class="form-group">
                    <label for="address1">Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                        <input type="text" class="form-control" id="address1" name="address1" placeholder="Enter address">
                    </div>
                </div>
                <div class="form-group">
                    <label for="contact1"> Contact</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-phone"></i></span>
                        <input type="tel" class="form-control" id="contact1" name="contact1" placeholder="Enter contact">
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="personToComplaint"> Person to Complaint</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" class="form-control" id="personToComplaint" name="personToComplaint" placeholder="Enter person to complaint">
                    </div>
                </div>
                <div class="form-group">
                    <label for="age2">Age</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="number" class="form-control" id="age2" name="age2" placeholder="Enter age">
                    </div>
                </div>
                <div class="form-group">
                    <label for="address2"> Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                        <input type="text" class="form-control" id="address2" name="address2" placeholder="Enter address">
                    </div>
                </div>
                <div class="form-group">
                    <label for="contact2">Contact</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-phone"></i></span>
                        <input type="tel" class="form-control" id="contact2" name="contact2" placeholder="Enter contact">
                    </div>
                </div>
                <div class="form-group">
                    <label for="actionTaken">Action Taken:</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-pencil"></i> </span>
                        <input type="text" class="form-control" id="actionTaken" name="actionTaken" placeholder="Enter action taken">
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group text-center">
            <div class="col">
                <button type="button" class="btn btn-secondary btn3" onclick="hideBlotterForm()">Close</button>
                <button type="submit" class="btn btn-primary btn4" id="add">Add</button>
            </div>
        </div>
    </form>
    </div>
    
    <?php
        include 'connection.php';

        function fetchBlotterData($conn, $blotterId) {
            $blotterId = mysqli_real_escape_string($conn, $blotterId);
            
            $sql = "SELECT * FROM blotterrecords WHERE id = '$blotterId'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                return $row;
            } else {
                return null;
            }
        }

        if (isset($_GET['id'])) {
            $blotterData = fetchBlotterData($conn, $_GET['id']);

            if ($blotterData) {
                $blotterid = $blotterData["id"];
                $blotterstatus = $blotterData["status"];
                $blottercomplainant = $blotterData["complainant"];
                $blotterage1 = $blotterData["age1"];
                $blotteraddress1  = $blotterData["address1"];
                $blottercontact1 = $blotterData["contact1"];
                $blotterpersonToComplaint = $blotterData["personToComplaint"];
                $blotterage2 = $blotterData["age2"];
                $blotteraddress2 = $blotterData["address2"];
                $blottercontact2  = $blotterData["contact2"];
                $blotteractionTaken = $blotterData["actionTaken"];
            } else {
                echo "Blotter not found";
            }
        } else {
            echo "Blotter ID not provided";
        }
    ?>

    <div class="form-container" id="editblotterFormContainer">
        <div class="heading-with-icon">
            <i class="fas fa-balance-scale"></i><h3>Edit Blotter</h3>
            <button type="button" class="btn-close" aria-label="Close" onclick="hideEditBlotterForm()"></button>
        </div>
        <form action="blotter_update.php" method="POST">
        <input type="hidden" name="blotter_id" value="<?php echo $blotterid; ?>">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="status"> Status</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-info-circle"></i></span>
                        <select class="form-control" id="status" name="status">
                            <option value="" disabled selected><?php echo $blotterstatus; ?></option>
                            <option value="Active">Active</option>
                            <option value="Not Active">Not Active</option>
                            <option value="Dismissed">Dismissed</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="complainant">Complainant</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" class="form-control" id="complainant" name="complainant" placeholder="Enter complainant" value="<?php echo $blottercomplainant; ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="age1">Age</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="number" class="form-control" id="age1" name="age1" placeholder="Enter age" value="<?php echo $blotterage1; ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="address1">Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                        <input type="text" class="form-control" id="address1" name="address1" placeholder="Enter address" value="<?php echo $blotteraddress1; ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="contact1"> Contact</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-phone"></i></span>
                        <input type="tel" class="form-control" id="contact1" name="contact1" placeholder="Enter contact" value="<?php echo $blottercontact1; ?>">
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="personToComplaint"> Person to Complaint</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" class="form-control" id="personToComplaint" name="personToComplaint" placeholder="Enter person to complaint" value="<?php echo $blotterpersonToComplaint; ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="age2">Age</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="number" class="form-control" id="age2" name="age2" placeholder="Enter age" value="<?php echo $blotterage2; ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="address2"> Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                        <input type="text" class="form-control" id="address2" name="address2" placeholder="Enter address" value="<?php echo $blotterage2; ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="contact2">Contact</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-phone"></i></span>
                        <input type="tel" class="form-control" id="contact2" name="contact2" placeholder="Enter contact" value="<?php echo $blottercontact2; ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="actionTaken">Action Taken:</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-pencil"></i> </span>
                        <input type="text" class="form-control" id="actionTaken" name="actionTaken" placeholder="Enter action taken" value="<?php echo $blotteractionTaken; ?>">
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group text-center">
            <div class="col">
                <button type="button" class="btn btn-secondary btn3" onclick="hideEditBlotterForm()">Close</button>
                <button type="submit" class="btn btn-primary btn4" id="update">Update</button>
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
            title: 'Blotter Added Successfully',
            text: 'You have added the blotter successfully.',
            timer: 12000,
            showConfirmButton: false
        }).then((result) => {
            if (result.dismiss === Swal.DismissReason.timer) {
                hideBlotterForm();
            }
        });
        }, 3000);
    }
    });

    function deleteBlotter(blotterId) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'You will not be able to recover this blotter!',
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
                            'Your blotter has been deleted.',
                            'success'
                        ).then(() => {
                            window.location.reload();
                        });
                    }
                };
                xhttp.open("GET", "blotter_delete.php?id=" + blotterId, true);
                xhttp.send();
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                Swal.fire(
                    'Cancelled',
                    'Your blotter is safe',
                    'error'
                );
            }
        });
    }

        function editBlotter(blotterId) {
            if (blotterId) {
                var editUrl = 'Blotter.php?id=' + blotterId;
                window.location.href = editUrl;
            } else {
                console.error("Invalid userId:", blotterId);
            }
        }

        window.onload = function() {
            var urlParams = new URLSearchParams(window.location.search);
            var blotterId = urlParams.get('id'); 
            
            if (blotterId) {
                var editUrl = 'Blotter.php?id=' + blotterId;
                showEditBlotterForm(editUrl);
            }
        }
        
        function showEditBlotterForm(editUrl) {
          var overlay = document.getElementById('overlay');
          var formContainer = document.getElementById('editblotterFormContainer');
          var blurredBackground = document.createElement('div'); 
          blurredBackground.classList.add('blurred-background'); 
        
          document.body.appendChild(blurredBackground);
        
          overlay.style.display = 'block';
          formContainer.style.display = 'block';
        }
        
        function hideEditBlotterForm() {
          var overlay = document.getElementById('overlay');
          var formContainer = document.getElementById('editblotterFormContainer');
          var blurredBackground = document.querySelector('.blurred-background'); 
        
          blurredBackground.parentNode.removeChild(blurredBackground);
        
          overlay.style.display = 'none';
          formContainer.style.display = 'none';

          window.location.href = 'Blotter.php';
        }

    document.addEventListener('DOMContentLoaded', function() {
    var updateBtn = document.getElementById('update');

    if (updateBtn) {
        updateBtn.addEventListener('click', function() {
        Swal.fire({
            icon: 'success',
            title: 'Blotter Edit Successfully',
            text: 'You have successfuly edit blotter.',
            timer: 12000,
            showConfirmButton: false
        }).then((result) => {
            if (result.dismiss === Swal.DismissReason.timer) {
                hideEditBlotterForm();
            }
        });
        }, 3000);
    }
    });

    function searchBlotter() {
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
                window.location.href = 'Blotter.php?logout=true';
            }
        });
    }
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.1.7/sweetalert2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-euKpLsYQJz5jE0EEOxnTPI1a2ybp4QA9QfsB1LD73pI95/02djN3eVkD5bZlNumj" crossorigin="anonymous"></script>
    <script src="js/Admin.js"></script>
</body>
</html>