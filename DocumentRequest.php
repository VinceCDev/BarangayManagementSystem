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
    <title>Document Requests</title>
    <link rel="icon" href="images/logo1.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.1.7/sweetalert2.min.css">
    <link rel="stylesheet" href="css/BarangayContact&Message.css">
    <style>
        #requests {
            background-color: #1E63E9;
            color: white;
            border-radius: 8px;
        }
    </style>
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
                <button class="btn btn-primary plus-toggle" type="button" id="dropdownMenuButton">
                    <i class="fas fa-cog"></i>Page <i class="bi bi-plus"></i>
                </button>
                <div class="dropdown-content" id="dropdownContent">
                    <a href="Information.php"><i class="fas fa-chevron-right"></i>Information</a>
                    <a href="Forms.php"><i class="fas fa-chevron-right"></i>Forms</a>
                    <a href="BarangayFAQ.php"><i class="fas fa-chevron-right"></i>FAQ</a>
                    <a href="#" class="contact1" id="cont"><i class="fas fa-chevron-right"></i>Contact</a>
                </div>
            </div>
            <a href="#" onclick="confirmLogout()" class="a1" confirmLogout()><i class="fas fa-sign-out-alt"></i> Log Out</a>
        </div>
    </div>

    <div class="title-with-icon">
        <a href="AdminDashboard.php" title="Dashboard"><i class="bi bi-house"></i></a>
        <p style="margin-right: 1050px;">Document Request</p>
    </div>

    <?php
        include 'connection.php';

        $limit = isset($_GET['entries']) ? (int)$_GET['entries'] : 5;
        $search = isset($_GET['search']) ? $_GET['search'] : '';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;

        $sql_count = "SELECT COUNT(*) as total FROM document_requests WHERE fullName LIKE ?";
        $stmt_count = $conn->prepare($sql_count);
        $search_term = "%$search%";
        $stmt_count->bind_param("s", $search_term);
        $stmt_count->execute();
        $result_count = $stmt_count->get_result();
        $total = $result_count->fetch_assoc()['total'];

        $sql = "SELECT id, fullName, age, purpose, email, business FROM document_requests WHERE fullName LIKE ? OR age LIKE ? OR purpose LIKE ? OR email LIKE ? OR business LIKE ? LIMIT ?, ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssiii", $search_term, $search_term, $search_term, $search_term, $search_term, $offset, $limit);
        $stmt->execute();
        $result = $stmt->get_result();

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
                <input type="number" id="entries" name="entries" title="number" placeholder="0" value="<?php echo min($result->num_rows, $limit); ?>" min="1" max="5">
            </div>
            <div class="search-bar">
                <p>Search: </p>
                <input type="text" id="searchInput" value="<?php echo $search; ?>" placeholder="Search for names..." oninput="searchMessage()" style="padding-left: 10px;">
            </div>
        </div>
        <hr>
        <table class="table-no-border">
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Age</th>
                    <th>Purpose</th>
                    <th>Email</th>
                    <th>Business</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["fullName"] . "</td>";
                    echo "<td>" . $row["age"] . "</td>";
                    echo "<td>" . $row["purpose"] . "</td>";
                    echo "<td>" . $row["email"] . "</td>";
                    echo "<td>" . $row["business"] . "</td>";
                    echo '<td>';
                    echo '<a href="service_pdf.php?id=' . $row["id"] . '&action=view" class="btn btn-warning" target="_blank">View</a> ';
                    echo '<a href="service_pdf.php?id=' . $row["id"] . '&action=download" class="btn btn-success" download>Download</a> ';
                    echo "<a style='margin-right: 5px;' href='https://mail.google.com/mail/?view=cm&fs=1&to=" . urlencode($row["email"]) . "' target='_blank' class='btn btn-primary'>Email</a>";
                    echo '</td>';
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No Data Available</td></tr>";
            }
            ?>
            </tbody>
        </table>
        <div class="navigation-buttons">
            <p>Showing <?php echo $result->num_rows; ?> of 5 entries.</p>
            <?php
            $totalPages = ceil($total / $limit);
            ?>
            <a href="?page=<?php echo max(1, $page - 1); ?>&search=<?php echo $search; ?>&entries=<?php echo $limit; ?>" class="btn <?php echo $page == 1 ? 'btn-secondary disabled' : 'btn-primary'; ?>">Previous</a>
            <a href="?page=<?php echo min($totalPages, $page + 1); ?>&search=<?php echo $search; ?>&entries=<?php echo $limit; ?>" class="btn <?php echo $page == $totalPages ? 'btn-secondary disabled' : 'btn-primary'; ?>">Next</a>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 Barangay Paule 1. All rights reserved.</p>
        </div>
    </footer>

    <script>
        function searchMessage() {
            var input = document.getElementById("searchInput").value.toLowerCase();
            var tableRows = document.querySelectorAll("#barangayOfficialsDashboard tbody tr");
            var filteredRows = 0;

            tableRows.forEach(function (row) {
                var cells = row.getElementsByTagName("td");
                var matchFound = false;

                for (var i = 0; i < cells.length; i++) {
                    if (cells[i].innerText.toLowerCase().indexOf(input) > -1) {
                        matchFound = true;
                        break;
                    }
                }

                if (matchFound) {
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
                    window.location.href = 'DocumentRequest.php?logout=true';
                }
            });
        }

    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.1.7/sweetalert2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-euKpLsYQJz5jE0EEOxnTPI1a2ybp4QA9QfsB1LD73pI95/02djN3eVkD5bZlNumj" crossorigin="anonymous"></script>
    <script src="js/Admin.js"></script>
</body>
</html>