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
  <title>Contact</title>
  <link rel="icon" href="images/logo1.png" type="image/x-icon">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.1.7/sweetalert2.min.css">
  <link rel="stylesheet" href="css/BarangayContact&Message.css">
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
          <a href="Information.php"><i class="fas fa-chevron-right"></i>Information</a>
          <a href="Forms.php"><i class="fas fa-chevron-right"></i>Forms</a>
          <a href="BarangayFAQ.php"><i class="fas fa-chevron-right"></i>FAQ</a>
          <a href="#" onclick="()" class="contact1" id="cont"><i class="fas fa-chevron-right"></i>Contact</a>
        </div>
      </div>
      <a href="BarangayContact&Message.php?logout=true.php" class="a1"><i class="fas fa-sign-out-alt"></i> Log Out</a>
    </div>
  </div>

  <div class="title-with-icon">
    <a href="AdminDashboard.php" title="Dashboard"><i class="bi bi-house"></i></a>
    <p>Contact</p>
  </div>
  
  <div class="overlay" id="overlay" onclick="hideForm()"></div>
  
  <?php
    include 'connection.php';

    $limit = 5; 
    $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
    $offset = ($currentPage - 1) * $limit;

    $searchTerm = isset($_GET['query']) ? $_GET['query'] : '';

    $sql = "SELECT * FROM receivemessages WHERE 
                      name LIKE '%$searchTerm%' OR 
                      email LIKE '%$searchTerm%'
                  LIMIT $limit OFFSET $offset";

    $result = $conn->query($sql);

    if ($currentPage == 1) {
        $countSql = "SELECT COUNT(*) AS totalMessages FROM receivemessages WHERE 
                          name LIKE '%$searchTerm%' OR 
                          email LIKE '%$searchTerm%'";
        $countResult = $conn->query($countSql);
        $totalMessages = $countResult->fetch_assoc()['totalMessages'];
    } else {
        $totalMessages = $result->num_rows;
    }

    $conn->close();
?>

  <div class="contact" id="barangayOfficialsDashboard">
      <div class="title-with-icon1">
          <i class="fas fa-chart-line"></i>
          <h3>List Chart</h3>
      </div>
      <hr>
      <div class="heading-and-buttons">
          <div class="show-entries">
              <label for="entries">Show Entries: </label>
              <input type="number" title="number" placeholder="0" value="<?php echo min($totalMessages, $limit * $currentPage); ?>">
          </div>    
          <div class="search-bar">
              <p>Search: </p>
              <input type="text" id="searchInput" onkeyup="searchMessage()" placeholder="Search for names..." style="padding-left: 10px;">
          </div>
      </div>
      <hr>
      <table class="table-no-border">
          <thead>
              <tr>
                  <th>Name of Sender</th>
                  <th>Age</th>
                  <th>Email</th>
                  <th>Contact Number</th>
                  <th>Message</th>
                  <th>Date</th>
                  <th>Action</th>
              </tr>
          </thead>
          <tbody>
          <?php
          include 'connection.php';

          $limit = 5;
          $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
          $offset = ($currentPage - 1) * $limit;

          $searchTerm = isset($_GET['query']) ? $_GET['query'] : '';

          $sql = "SELECT * FROM receivemessages WHERE 
                      name LIKE '%$searchTerm%' OR 
                      email LIKE '%$searchTerm%'
                  LIMIT $limit OFFSET $offset";

          $result = $conn->query($sql);

          if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                  echo "<tr>";
                  echo "<td>" . $row["name"] . "</td>";
                  echo "<td>" . $row["age"] . "</td>";
                  echo "<td>" . $row["email"] . "</td>";
                  echo "<td>" . $row["contact"] . "</td>";
                  echo "<td>" . $row["message"] . "</td>";
                  echo "<td>" . $row["created_at"] . "</td>";
                  echo "<td>";
                  echo "<a style='margin-right: 5px;' href='https://mail.google.com/mail/?view=cm&fs=1&to=" . urlencode($row["email"]) . "' target='_blank' class='btn btn-primary'>Email</a>";
                  echo "<a href='tel:" . $row["contact"] . "' class='btn btn-success'>Message</a>";
                  echo "</td>";
                  echo "</tr>";
              }
          } else {
              echo "<tr><td colspan='5'>No Data Available</td></tr>";
          }

          $conn->close();
          ?>
          </tbody>
      </table>
      <div class="navigation-buttons">
        <p>Showing <?php echo min($totalMessages, $limit * $currentPage); ?> of <?php echo $limit; ?> entries.</p>
        <?php
        $totalPages = ceil($totalMessages / $limit);
        ?>
        <a href="?page=<?php echo max(1, $currentPage - 1); ?>" class="btn <?php echo $currentPage == 1 ? 'btn-secondary disabled' : 'btn-primary'; ?>">Previous</a>
        <a href="?page=<?php echo min($totalPages, $currentPage + 1); ?>" class="btn <?php echo $currentPage == $totalPages ? 'btn-secondary disabled' : 'btn-primary'; ?>">Next</a>
    </div>
  </div>

  <div class="overlay" id="overlay" onclick="hideContactForm()"></div>
  
  <?php
    include 'connection.php';

    $sql = "SELECT COUNT(*) AS totalcontacts FROM contacts ";
    $result = $conn->query($sql);
            
    if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      $countcontacts = $row['totalcontacts'];
    } else {
      $countcontacts = 0;
    }
            
    $conn->close();
  ?>

  <div class="contact" id="contact1Dashboard">
    <div class="title-with-icon1">
      <i class="fas fa-chart-line"></i>
      <h3>List Chart</h3>
    </div>
    <hr>
    <div class="heading-and-buttons">
      <div class="show-entries">
        <label for="entries">Show Entries: </label>
        <input type="number" title="number" placeholder="0" value="<?php echo $countcontacts; ?>">
      </div>    
      <div class="search-bar">
            <p>Search: </p><input type="text" id="searchInput1" onkeyup="searchContactTable()" placeholder="Search for names..." style="padding-left: 10px;">
      </div>
    </div><hr>
    <table class="table-no-border">
        <thead>
            <tr>
                <th>Label</th>
                <th>Description</th>
                <th>Contacts</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php
          include 'connection.php';

          if(isset($_GET['query'])) {
              $search_query = $_GET['query'];
              $sql = "SELECT * FROM contacts WHERE label LIKE '%$search_query%'";
          } else {
              $sql = "SELECT * FROM contacts";
          }

          $result = $conn->query($sql);

          if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                  echo "<tr>";
                  echo "<td>" . $row["label"] . "</td>";
                  echo "<td>" . $row["description"] . "</td>";
                  echo "<td style='width: 20px !important;'>" . $row["contacts"] . "</td>";
                  echo "<td>";
                  echo "<a href='#' onclick='editContact(" . $row['id'] . ")' class='btn btn-primary btnedit' style='margin-right:5px;'>Edit</a>";
                  echo "</td>";
                  echo "</tr>";
              }
          } else {
              echo "<tr><td colspan='4'>No Data Available</td></tr>";
          }

          $conn->close();
        ?>
        </tbody>
    </table>
    <div class="navigation-buttons" style="width: 100%;">
      <p>Showing <?php echo $countcontacts; ?> of <?php echo $countcontacts; ?> entries.</p>
    </div>
  </div>
  <footer class="footer">
    <div class="container">
        <p>&copy; 2024 Barangay Paule 1. All rights reserved.</p>
    </div>
  </footer>
  
  <?php
include 'connection.php';

function fetchContactData($conn, $contactId) {
    $contactId = mysqli_real_escape_string($conn, $contactId);
    
    $sql = "SELECT * FROM contacts WHERE id = '$contactId'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row;
    } else {
        return null;
    }
}

if (isset($_GET['id'])) {
    $contactData = fetchContactData($conn, $_GET['id']);

    if ($contactData) {
        $contactId = $contactData["id"];
        $label = $contactData["label"];
        $description = $contactData["description"];
        $contact = $contactData["contacts"];
    } else {
        echo "Contact not found";
    }
} else {
    echo "Contact ID not provided";
}

// Close database connection
$conn->close();
?>


  <div class="form-container" id="contactForm">
    <div class="heading-with-icon"><i class="fas fa-running"></i><h2>Edit Activity</h2>
        <button type="button" class="btn-close" aria-label="Close" onclick="hideContactForm()"></button>
    </div>
    <form action="contact_update.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="contact_id" value="<?php echo $contactId; ?>">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="editLabel" class="lab">Label</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-pen"></i></span>
                        <input type="text" class="form-control" id="editLabel" name="label" placeholder="Label" value="<?php echo $label; ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="editDescription" class="lab">Description</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-pen"></i></span>
                        <input type="text" class="form-control" id="editDescription" name="description" placeholder="Description" value="<?php echo $description; ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="editContact" class="lab">Contact</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                        <input type="text" class="form-control" id="editContact" name="contact" placeholder="Contact" value="<?php echo $contact; ?>">
                    </div>
                </div>
            </div>
            <div class="form-group text-center">
                <div class="col">
                    <button type="button" class="btn btn-secondary btn3" onclick="hideContactForm()"> Close</button>
                    <button type="submit" class="btn btn-primary btn4" id="update">Update</button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function editContact(contactId) {
    if (contactId) {
        var editUrl = 'BarangayContact&Message.php?id=' + contactId;

        window.location.href = editUrl;
      } else {
        console.error("Invalid contactId:", contactId);
      }
    }

    window.onload = function() {
      var urlParams = new URLSearchParams(window.location.search);
      var contactId = urlParams.get('id');
          
    if (contactId) {
      var editUrl = 'BarangayContact&Message.php?id=' + contactId;
      showContactForm(editUrl);
  }
}

function showContactForm(editUrl) {
    var overlay = document.getElementById('overlay');
    var formContainer = document.getElementById('contactForm');
    var blurredBackground = document.createElement('div');
    blurredBackground.classList.add('blurred-background');
    document.body.appendChild(blurredBackground);
    overlay.style.display = 'block';
    formContainer.style.display = 'block';
}

function hideContactForm() {
    var overlay = document.getElementById('overlay');
    var formContainer = document.getElementById('contactForm');
    var blurredBackground = document.querySelector('.blurred-background');
    blurredBackground.parentNode.removeChild(blurredBackground);
    overlay.style.display = 'none';
    formContainer.style.display = 'none';

    window.location.href = 'BarangayContact&Message.php';
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
          hideContactForm();
        }
      });
    }, 3000);
  }
});
    function searchMessage() {
        var input = document.getElementById("searchInput").value.toLowerCase();
        var tableRows = document.querySelectorAll("#barangayOfficialsDashboard tbody tr");
        var filteredRows = 0;

        tableRows.forEach(function(row) {
            var cells = row.getElementsByTagName("td");
            var activityName = cells[2].innerText.toLowerCase();

            if (activityName.indexOf(input) > -1) {
                row.style.display = "";
                filteredRows++;
            } else {
                row.style.display = "none";
            }
        });
        document.getElementById("searchInput1").value = "";
    }

    function searchContactTable() {
        var input = document.getElementById("searchInput1").value.toLowerCase();
        var tableRows = document.querySelectorAll("#contact1Dashboard tbody tr");
        var filteredRows = 0;

        tableRows.forEach(function(row) {
            var cells = row.getElementsByTagName("td");
            var activityName = cells[2].innerText.toLowerCase();

            if (activityName.indexOf(input) > -1) {
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
                window.location.href = 'BarangayContact&Message.php?logout=true';
            }
        });
    }
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.1.7/sweetalert2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-euKpLsYQJz5jE0EEOxnTPI1a2ybp4QA9QfsB1LD73pI95/02djN3eVkD5bZlNumj" crossorigin="anonymous"></script>
<script src="js/Admin.js"></script>
</body>
</html>