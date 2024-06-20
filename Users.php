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
  <title>Users</title>
  <link rel="icon" href="images/logo1.png" type="image/x-icon">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.1.7/sweetalert2.min.css">
  <link rel="stylesheet" href="css/Users.css">
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
        <a href="Resident.php" class="a1"><i class="fas fa-users"></i> Residents</a>
        <a href="DocumentRequest.php" class="a1" id="requests"><i class="fas fa-file"></i> Document Requests</a>
        <a href="Users.php" class="a1" id="user"><i class="fas fa-users-cog"></i> Users</a>
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

  <div class="title-with-icon" style="margin-left: 269px; z-index: 100;">
    <a href="AdminDashboard.php" title="Dashboard"><i class="bi bi-house"></i></a>
    <p>Users</p>
  </div>

  <div class="overlay" id="overlay" onclick="hideUserForm()"></div>
  
  <?php
    include 'connection.php';

    $sql = "SELECT COUNT(*) AS totalUsers FROM users";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $countUsers = $row['totalUsers'];
    } else {
        $countUsers = 0;
    }

    $conn->close();
  ?>

  <div class="users" id="barangayOfficialsDashboard">
      <div class="title-with-icon1">
          <i class="fas fa-chart-line"></i>
          <h3>List Chart</h3>
          <button type="button" class="btn btn-success" onclick="showUserForm()">Add User</button>
      </div>
      <hr>
      <div class="heading-and-buttons">
          <div class="show-entries">
              <label for="entries">Show Entries: </label>
              <input type="number" title="number" placeholder="0" value="<?php echo $countUsers; ?>">
          </div>    
          <div class="search-bar">
              <p>Search: </p>
              <input type="text" id="searchInput" onkeyup="searchUsers()" placeholder="Search for names..." style="padding-left: 10px;">
          </div>
      </div>
      <hr>
      <table class="table-no-border">
          <thead>
              <tr>
                  <th style="width: 25%;">Full Name</th>
                  <th>Email</th>
                  <th>Password</th>
                  <th>Type</th>
                  <th>Action</th>
              </tr>
          </thead>
          <tbody>
              <?php
              include 'connection.php';

              // Define pagination parameters
              $limit = 5;
              $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
              $offset = ($currentPage - 1) * $limit;

              // Get search term from URL
              $searchInput = isset($_GET['search']) ? $_GET['search'] : '';

              // Construct SQL query based on search term and pagination
              $sql = "SELECT * FROM users WHERE fullName LIKE '%$searchInput%' LIMIT $limit OFFSET $offset";

              $result = $conn->query($sql);

              if ($result->num_rows > 0) {
                  while ($row = $result->fetch_assoc()) {
                      echo "<tr>";
                      echo "<td style='display: none;'>" . $row['id'] . "</td>"; 
                      echo "<td style='width: 25%;'>" . $row["fullName"] . "</td>";
                      echo "<td>" . $row["userName"] . "</td>";
                      echo "<td>";
                      echo str_repeat("&#8226;", strlen($row["password"]));
                      echo "</td>";
                      echo "<td>" . $row["userType"] . "</td>";
                      echo "<td class='action-buttons'>";
                      echo "<a href='#' onclick='editUser(" . $row['id'] . ")' class='btn btn-primary btnedit' style='margin-right:5px;'>Edit</a>";
                      echo "<button type='button' class='btn btn-danger' onclick='deleteUser(" . $row['id'] . ")'>Delete</button>";
                      echo "</td>";
                      echo "</tr>";
                  }
              } else {
                  echo "<tr><td colspan='5'>No matching records found</td></tr>";
              }

              $conn->close();
              ?>
          </tbody>
      </table>
      <div class="navigation-buttons">
          <p>Showing <?php echo $countUsers; ?> of <?php echo $limit; ?> entries.</p>
          <?php
          $totalPages = ceil($countUsers / $limit);
          ?>
          <a href="?page=<?php echo max(1, $currentPage - 1); ?>" class="btn <?php echo $currentPage == 1 ? 'btn-secondary disabled' : 'btn-primary'; ?>">Previous</a>
          <a href="?page=<?php echo min($totalPages, $currentPage + 1); ?>" class="btn <?php echo $currentPage == $totalPages ? 'btn-secondary disabled' : 'btn-primary'; ?>">Next</a>
      </div>
  </div>

  <footer class="footer" style="margin-top: 50px;">
    <div class="container">
        <p>&copy; 2024 Barangay Paule 1. All rights reserved.</p>
    </div>
  </footer>

  <div class="form-container" id="userContainer">
      <div class="title">
  <i class="fas fa-user-plus"></i><h2>Add User</h2>
  <button type="button" class="btn-close" aria-label="Close" onclick="hideUserForm()"></button>
  </div>
  <form action="users_insert.php" method="POST">
      <div class="row">
          <div class="col-md-6">
                  <div class="form-group">
                    <label for="fullName" class="lab">Full Name</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bi bi-person"></i></span>
                      <input type="text" class="form-control" id="userFullName" name="userFullName" placeholder="Enter full name">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="contact" class="lab">Username</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bi bi-person"></i></span>
                      <input type="email" class="form-control" id="userName" name="userName" placeholder="Enter username">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="address" class="lab"> Password</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bi bi-lock"></i></span>
                      <input type="text" class="form-control" id="password" name="password" placeholder="Enter password">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="usertype" class="lab">Type</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bi bi-building"></i></span>
                      <select title="usertype" class="form-control" id="usertype" name="usertype">
                        <option value="" disabled selected>Select User Type</option>
                        <option value="Admin">Admin</option>
                        <option value="Resident">Resident</option>
                    </select>        
                    </div>
                  </div>
          </div>
          <div class="form-group text-center">
          <div class="col">
            <button type="button" class="btn btn-secondary btn3" onclick="hideUserForm()"> Close</button>
              <button type="submit" class="btn btn-primary btn4" id="add">Add</button>
          </div>
      </div>
      </div>
    </form>
  </div>
  
  <div class="form-container" id="edituserContainer" style=" width: 580px; height: 480px; ">
  <div class="title">
    <i class="fas fa-user-plus"></i><h2>Edit User</h2>
    <button type="button" class="btn-close" aria-label="Close" onclick="hideEditUserForm()"></button>
  </div>
  <form action="user_update.php" method="POST">
    <?php
      include 'connection.php';

      function fetchUserData($conn, $userId) {
          $userId = mysqli_real_escape_string($conn, $userId);
          
          $sql = "SELECT * FROM users WHERE id = '$userId'";
          $result = $conn->query($sql);

          if ($result->num_rows > 0) {
              $row = $result->fetch_assoc();
              return $row;
          } else {
              return null;
          }
      }

      if (isset($_GET['id'])) {
          $userData = fetchUserData($conn, $_GET['id']);

          if ($userData) {
              $usersid = $userData["id"];
              $usersfullname = $userData["fullName"];
              $username = $userData["userName"];
              $userspassword  = $userData["password"];
              $users_type = $userData["userType"];
          } else {
              echo "User not found";
          }
      } else {
          echo "User ID not provided";
      }

      $conn->close();
    ?>
    <input type="hidden" name="users_id" value="<?php echo $usersid; ?>">
    <div class="row">
      <div class="col-md-6">
        <div class="form-group" style="width: 250px;">
          <label for="fullName" class="lab">Full Name</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-person"></i></span>
            <input type="text" class="form-control" id="userFullName" name="userFullName" placeholder="Enter full name" value="<?php echo $usersfullname; ?>">
          </div>
        </div>
        <div class="form-group" style="width: 250px;">
          <label for="contact" class="lab">Username</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-person"></i></span>
            <input type="text" class="form-control" id="userName" name="userName" placeholder="Enter username" value="<?php echo $username ?>">
          </div>
        </div>
        <div class="form-group" style="width: 250px;">
          <label for="address" class="lab">Password</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="text" class="form-control" id="password" name="password" placeholder="Enter password" value="<?php echo $userspassword ?>">
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group" style="width: 250px;">
          <label for="newpassword" class="lab">New Password</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="text" class="form-control" id="newpassword" name="newpassword" placeholder="Enter new password">
          </div>
        </div>
        <div class="form-group" style="width: 250px;">
          <label for="confirmpassword" class="lab">Confirm Password</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="text" class="form-control" id="confirmpassword" name="confirmpassword" placeholder="Confirm password">
          </div>
        </div>
        <div class="form-group" style="width: 250px;">
          <label for="usertype" class="lab">Type</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-building"></i></span>
            <select title="usertype" class="form-control" id="usertype" name="usertype">
              <option value="<?php echo $users_type; ?>" disabled selected><?php echo $users_type; ?></option>
              <option value="Admin">Admin</option>
              <option value="Resident">Resident</option>
            </select>        
          </div>
        </div>
      </div>
    </div>
    <div class="form-group text-center" style="width: 360px; background: #fff;">
      <div class="col">
        <button type="button" style="margin-left: 165px;" class="btn btn-secondary btn3" onclick="hideEditUserForm()"> Close</button>
        <button type="submit" style="margin-left: 170px;" class="btn btn-primary btn4" id="update">Update</button>
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
                hideUserForm();
              }
          });
          }, 3000);
      }
      });

      function deleteUser(userId) {
          Swal.fire({
              title: 'Are you sure?',
              text: 'Are you sure you want to delete this user?',
              icon: 'warning',
              showCancelButton: true,
              confirmButtonText: 'Yes, delete it!',
              cancelButtonText: 'No, cancel!',
              reverseButtons: true
          }).then((result) => {
              if (result.isConfirmed) {
                  var xhttp = new XMLHttpRequest();
                  xhttp.onreadystatechange = function () {
                      if (this.readyState == 4 && this.status == 200) {
                           Swal.fire({
                            title: 'Deleted!',
                            text: 'User has been deleted.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Reload the page after successful deletion
                                location.reload();
                            }
                        });
                      }
                  };
                  xhttp.open("GET", "user_delete.php?id=" + userId, true);
                  xhttp.send();
              } else if (
                  result.dismiss === Swal.DismissReason.cancel
              ) {
                  Swal.fire(
                      'Cancelled',
                      'Your user is safe',
                      'error'
                  )
              }
          });
      }
            
      function editUser(userId) {
          if (userId) {
              var editUrl = 'Users.php?id=' + userId;

              window.location.href = editUrl;
          } else {
              console.error("Invalid userId:", userId);
          }
      }

      window.onload = function() {
          var urlParams = new URLSearchParams(window.location.search);
          var userId = urlParams.get('id');
          
          if (userId) {
              var editUrl = 'Users.php?id=' + userId;
              showEditUserForm(editUrl);
          }
      }

      function showEditUserForm(editUrl) {
          var overlay = document.getElementById('overlay');
          var formContainer = document.getElementById('edituserContainer');
          var blurredBackground = document.createElement('div'); 
          blurredBackground.classList.add('blurred-background'); 

          document.body.appendChild(blurredBackground);

          overlay.style.display = 'block';
          formContainer.style.display = 'block';
      }

      function hideEditUserForm() {
          var overlay = document.getElementById('overlay');
          var formContainer = document.getElementById('edituserContainer');
          var blurredBackground = document.querySelector('.blurred-background'); 
        
          blurredBackground.parentNode.removeChild(blurredBackground);
        
          overlay.style.display = 'none';
          formContainer.style.display = 'none';

          window.location.href = 'Users.php';
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
              hideEditUserForm();
            }
          });
        }, 3000);
      }
    });

      function searchUsers() {
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
                window.location.href = 'Users.php?logout=true';
            }
        });
    }
  </script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.1.7/sweetalert2.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-euKpLsYQJz5jE0EEOxnTPI1a2ybp4QA9QfsB1LD73pI95/02djN3eVkD5bZlNumj" crossorigin="anonymous"></script>
  <script src="js/Admin.js"></script>
</body>
</html>