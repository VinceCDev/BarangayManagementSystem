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
  <title>Barangay Officials</title>
  <link rel="icon" href="images/logo1.png" type="image/x-icon">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.1.7/sweetalert2.min.css">
  <link rel="stylesheet" href="css/BarangayOfficial.css">
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
      <a href="BarangayOfficial.php" class="a1" id="official"><i class="fas fa-users"></i> Barangay Officials</a>
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
          <a href="Information.php"><i class="fas fa-chevron-right"></i>Information</a>
          <a href="Forms.php"><i class="fas fa-chevron-right"></i>Forms</a>
          <a href="BarangayFAQ.php"><i class="fas fa-chevron-right"></i>FAQ</a>
          <a href="BarangayContact&Message.php" class="contact1"><i class="fas fa-chevron-right"></i>Contact</a>
        </div>
      </div>
      <a href="#" onclick="confirmLogout()" class="a1"><i class="fas fa-sign-out-alt"></i> Log Out</a>
    </div>
  </div>

  <div class="title-with-icon">
    <a href="AdminDashboard.html" title="Dashboard"><i class="bi bi-house"></i></a>
    <p>Barangay Officials Records</p>
  </div>

  <div class="overlay" id="overlay" onclick="hideForm()"></div>
  
  <?php
include 'connection.php';

$sql = "SELECT * FROM barangay_officials";

if (isset($_GET['query'])) {
    $search_query = $_GET['query'];
    $sql .= " WHERE fullName LIKE '%$search_query%'";
}

$sqlCount = "SELECT COUNT(*) AS totalOfficials FROM barangay_officials";
$resultCount = $conn->query($sqlCount);
$rowCount = $resultCount->fetch_assoc();
$totalOfficialsCount = $rowCount['totalOfficials'];

$limit = 5;
$currentPage = isset($_GET['page']) ? $_GET['page'] : 1;

$offset = ($currentPage - 1) * $limit;

$sql .= " LIMIT $limit OFFSET $offset";

$resultOfficials = $conn->query($sql);

$officialsCountPerPage = $resultOfficials->num_rows;

$totalPages = ceil($totalOfficialsCount / $limit);

$conn->close();
?>

<div class="barangay" id="barangayOfficialsDashboard">
    <div class="title-with-icon1">
        <i class="fas fa-chart-line"></i>
        <h3>List Chart</h3>
        <button type="button" class="btn btn-success" onclick="showForm()">Add Barangay Official</button>
    </div>
    <hr>
    <div class="heading-and-buttons">
        <div class="show-entries">
            <label for="entries">Show Entries: </label>
            <input type="number" title="number" placeholder="0" value="<?php echo $officialsCountPerPage; ?>">
        </div>
        <div class="search-bar">
            <p>Search: </p>
            <input type="text" id="searchInput" onkeyup="searchOfficial()" placeholder="Search for names..." style="padding-left: 10px;">
        </div>
    </div>
    <hr>
    <table class="table-no-border">
        <thead>
            <tr>
                <th>Photo</th>
                <th>Position</th>
                <th>Full Name</th>
                <th>Contact </th>
                <th>Address </th>
                <th>Start of Term </th>
                <th>End of Term</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($resultOfficials->num_rows > 0) {
                while ($row = $resultOfficials->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td><img src='photos/".$row['photo']."' alt='Photo' style='max-width: 70px; max-height: 70px; border-radius:50%'></td>";
                    echo "<td>".$row['position']."</td>";
                    echo "<td>".$row['fullName']."</td>";
                    echo "<td>".$row['contact']."</td>";
                    echo "<td>".$row['address']."</td>";
                    echo "<td>".$row['startOfTerm']."</td>";
                    echo "<td>".$row['endOfTerm']."</td>";
                    echo "<td class='action-buttons'>";
                    echo "<a href='#' onclick='editOfficial(" . $row['id'] . ")' class='btn btn-primary btnedit' style='margin-right:5px;'>Edit</a>";
                    echo "<button type='button' class='btn btn-danger' onclick='deleteOfficial(" . $row['id'] . ")'>Delete</button>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='8'>No records found</td></tr>";
            }
            ?>
        </tbody>
    </table>
    <div class="navigation-buttons">
        <p style="margin-right: 970px;">Showing <?php echo $officialsCountPerPage; ?> of <?php echo $limit; ?> entries.</p>
        <a style="margin-right: 110px;" href="?page=<?php echo $currentPage > 1 ? $currentPage - 1 : 1; ?>" class="btn <?php echo $currentPage == 1 ? 'btn-secondary disabled' : 'btn-primary'; ?>">Previous</a>
        <a href="?page=<?php echo $currentPage < $totalPages ? $currentPage + 1 : $totalPages; ?>" class="btn <?php echo $currentPage == $totalPages ? 'btn-secondary disabled' : 'btn-primary'; ?>">Next</a>
    </div>
</div>
  <footer class="footer">
    <div class="container">
      <p>&copy; 2024 Your Website Name. All rights reserved.</p>
    </div>
  </footer>

  <div class="form-container" id="formContainer" style="height: 550px">
    <div class="heading-with-icon"></h2><i class="fas fa-user-plus"></i><h3>Add Barangay Officials</h3>
      <button type="button" class="btn-close" aria-label="Close" onclick="hideForm()"></button>
    </div>
    <form action="barangay_official_insert.php" method="POST" id="officialForm" enctype="multipart/form-data">
  <div class="row">
    <div class="col-md-6">
      <div class="form-group">
        <label for="position" class="lab">Position</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-building"></i></span>
          <select class="form-control" id="position" name="position">
            <option value="" disabled selected>Select Position</option>
            <option value="Barangay Captain">Barangay Captain</option>
            <option value="Kagawad">Kagawad</option>
            <option value="SK Chairman">SK Chairman</option>
            <option value="Secretary">Secretary</option>
          </select>        
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label for="barangayPhoto" class="lab">Photo</label>
        <div class="input-group">
          <input type="file" class="form-control" id="barangayPhoto" name="barangayPhoto">
        </div>
      </div>
    </div>
  </div>
  <div class="row">
  <div class="col-md-6">
      <div class="form-group">
        <label for="fullName" class="lab">Full Name</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-person"></i></span>
          <input type="text" class="form-control" id="barangayFullName" name="barangayFullName" placeholder="Enter full name">
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label for="contact" class="lab">Contact</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
          <input type="text" class="form-control" id="barangayContact" name="barangayContact" placeholder="Enter contact">
        </div>
      </div>
    </div>
  </div>
  <div class="row">
  <div class="col-md-6">
      <div class="form-group">
        <label for="address" class="lab">Address</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
          <input type="text" class="form-control" id="barangayResident" name="barangayResident" placeholder="Enter address">
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label for="startOfTerm" class="lab">Start of Term</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-calendar"></i></span>
          <input type="date" class="form-control" id="StartofTerm" name="StartofTerm" placeholder="Enter start date">
        </div>
      </div>
    </div>
  </div>
  <div class="row">
  <div class="col-md-6">
      <div class="form-group">
        <label for="endOfTerm" class="lab">End of Term</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-calendar"></i></span>
          <input type="date" class="form-control" id="EndofTerm" name="EndofTerm" placeholder="Enter end date">
        </div>
      </div>
    </div>
  </div>
  <button type="button" class="btn btn-secondary btn3" onclick="hideForm()" style="margin-top:10px;">Close</button>
  <button type="submit" class="btn btn-primary btn4" id="addOfficialButton" style="margin-top:10px;">Add Official</button>
</form>
  </div>
  
<?php
include 'connection.php';

function fetchOfficialData($conn, $officialId) {
    $officialId = mysqli_real_escape_string($conn, $officialId);
    
    $sql = "SELECT * FROM barangay_officials WHERE id = '$officialId'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row;
    } else {
        return null;
    }
}

if (isset($_GET['id'])) {
    $officialData = fetchOfficialData($conn, $_GET['id']);

    if ($officialData) {
        $officialId = $officialData["id"];
        $officialPosition = $officialData["position"];
        $officialPhoto = $officialData["photo"];
        $officialFullName = $officialData["fullName"];
        $officialContact = $officialData["contact"];
        $officialAddress = $officialData["address"];
        $officialStartOfTerm = $officialData["startOfTerm"];
        $officialEndOfTerm = $officialData["endOfTerm"];
    } else {
        echo "Official not found";
    }
} else {
    echo "";
}
?>

<div class="form-container" id="editformContainer" style="height: 550px; display: none;">
    <div class="heading-with-icon">
        <h2><i class="fas fa-user-plus"></i>Edit Barangay Officials</h2>
        <button type="button" class="btn-close" aria-label="Close" onclick="hideeditForm()"></button>
    </div>
    <form action="barangay_official_update.php" method="POST" id="officialForm" enctype="multipart/form-data">
        <input type="hidden" name="official_id" value="<?php echo $officialId; ?>">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="position" class="lab">Position</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-building"></i></span>
                        <select class="form-control" id="position" name="position">
                            <option value="" disabled selected><?php echo $officialPosition; ?></option>
                            <option value="Barangay Captain">Barangay Captain</option>
                            <option value="Kagawad">Kagawad</option>
                            <option value="SK Chairman">SK Chairman</option>
                            <option value="Secretary">Secretary</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="barangayPhoto" class="lab">Photo</label>
                    <div class="input-group">
                        <input type="file" class="form-control" id="barangayPhoto" name="barangayPhoto" onchange="updateFileName(this)" placeholder="Select a file..." value="<?php echo $officialPhoto; ?>">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="fullName" class="lab">Full Name</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" class="form-control" id="barangayFullName" name="barangayFullName" placeholder="Enter full name" value="<?php echo $officialFullName; ?>">
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="contact" class="lab">Contact</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                        <input type="text" class="form-control" id="barangayContact" name="barangayContact" placeholder="Enter contact" value="<?php echo $officialContact; ?>">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="address" class="lab">Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                        <input type="text" class="form-control" id="barangayResident" name="barangayResident" placeholder="Enter address" value="<?php echo $officialAddress; ?>">
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="startOfTerm" class="lab">Start of Term</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                        <input type="date" class="form-control" id="StartofTerm" name="StartofTerm" placeholder="Enter start date" value="<?php echo $officialStartOfTerm; ?>">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="endOfTerm" class="lab">End of Term</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                        <input type="date" class="form-control" id="EndofTerm" name="EndofTerm" placeholder="Enter end date" value="<?php echo $officialEndOfTerm; ?>">
                    </div>
                </div>
            </div>
        </div>
        <button type="button" class="btn btn-secondary btn3" onclick="hideeditForm()" style="margin-top:10px;">Close</button>
        <button type="submit" class="btn btn-primary btn4" id="update" style="margin-top:10px;">Update</button>
    </form>
</div>
<script>

document.addEventListener('DOMContentLoaded', function() {
    var addBtn = document.getElementById('addOfficialButton');

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
                hideForm();
            }
        });
        }, 3000);
    }
    });

function deleteOfficial(officialId) {
  Swal.fire({
    title: 'Are you sure?',
    text: 'You will not be able to recover this official!',
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
            'Your official has been deleted.',
            'success'
          ).then(() => {
            window.location.reload();
          });
        }
      };
      xhttp.open("GET", "barangay_official_delete.php?id=" + officialId, true);
      xhttp.send();
    } else if (result.dismiss === Swal.DismissReason.cancel) {
      Swal.fire(
        'Cancelled',
        'Your official is safe',
        'error'
      );
    }
  });
}

function editOfficial(officialId) {
    if (officialId) {
        var editUrl = 'BarangayOfficial.php?id=' + officialId;
        window.location.href = editUrl;
    } else {
        console.error("Invalid userId:", officialId);
    }
}

window.onload = function() {
    var urlParams = new URLSearchParams(window.location.search);
    var officialId = urlParams.get('id'); 
    
    if (officialId) {
        var editUrl = 'BarangayOfficial.php?id=' + officialId;
        showeditForm(editUrl);
    }
}

function showeditForm(editUrl) {
    var overlay = document.getElementById('overlay');
    var formContainer = document.getElementById('editformContainer');
    var blurredBackground = document.createElement('div'); 
    blurredBackground.classList.add('blurred-background');

    document.body.appendChild(blurredBackground);

    overlay.style.display = 'block';
    formContainer.style.display = 'block';
}

function hideeditForm() {
    var overlay = document.getElementById('overlay');
    var formContainer = document.getElementById('editformContainer');
    var blurredBackground = document.querySelector('.blurred-background'); 

    blurredBackground.parentNode.removeChild(blurredBackground);

    overlay.style.display = 'none';
    formContainer.style.display = 'none';

    window.location.href = 'BarangayOfficial.php';
}

document.addEventListener('DOMContentLoaded', function() {
  var updateBtn = document.getElementById('update');

  if (updateBtn) {
    updateBtn.addEventListener('click', function() {
      Swal.fire({
        icon: 'success',
        title: 'Official Edit Successfully',
        text: 'You have successfuly edit Official.',
        timer: 12000,
        showConfirmButton: false
      }).then((result) => {
        if (result.dismiss === Swal.DismissReason.timer) {
          hideeditForm();
        }
      });
    }, 3000);
  }
});

function searchOfficial() {
    var input = document.getElementById("searchInput").value.toLowerCase();
    var tableRows = document.getElementsByTagName("tr");
    var filteredRows = 0;

    for (var i = 1; i < tableRows.length; i++) {
      var cells = tableRows[i].getElementsByTagName("td");
      var activityName = cells[2].innerText.toLowerCase();

      if (activityName.indexOf(input) > -1) {
        tableRows[i].style.display = "";
        filteredRows++;
      } else {
        tableRows[i].style.display = "none";
      }
    }
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
                window.location.href = 'BarangayOfficial.php?logout=true';
            }
        });
    }

</script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.1.7/sweetalert2.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-euKpLsYQJz5jE0EEOxnTPI1a2ybp4QA9QfsB1LD73pI95/02djN3eVkD5bZlNumj" crossorigin="anonymous"></script>
  <script src="js/Admin.js"></script>
</body>
</html>