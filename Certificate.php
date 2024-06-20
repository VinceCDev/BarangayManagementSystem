<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificates</title>
    <link rel="icon" href="images/logo1.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://unpkg.com/typed.js@2.1.0/dist/typed.umd.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/Certificate.css">
    <style>
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            display: none;
        }

        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 5px;
            width: 100%;
            max-width: 600px;
            position: relative;
            display: flex;
            flex-direction: column;
            max-height: 90vh;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 5px;
            width: 95%;
            flex-shrink: 0;
        }

        .modal-header h3 {
            margin: 0;
            font-size: 30px;
        }

        .modal-header h3 i {
            margin-right: 10px;
        }

        .modal-header .close {
            background: none;
            border: none;
            font-size: 40px;
        }

        .modal-footer {
            margin-top: 15px;
            display: flex;
            justify-content: space-between;
            flex-shrink: 0;
        }

        .modal-body {
            overflow-y: auto;
            flex-grow: 1;
        }

        @media (max-width: 768px) {
            .modal-overlay {
                margin: 0 auto;
            }

            .modal-content {
                width: 90%;
                margin: 0 auto;
                margin-left: 0;
                margin-right: 0;
                margin-top: 0;
                height: 80vh;
            }

            .modal-header h3 {
                font-size: 23px;
            }

            .modal-header .close {
                font-size: 30px;
            }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                  <a href="Photos.php">Photo Album</a>
                </div>
            </div>
            <a id="Cert1" href="Certificate.php" style="--i:3; color: white; background-color: #00aaff; padding: 12px; border-radius: 30px;">Services</a>
            <a href="FAQ.php" style="--i:4">FAQ</a>
            <a href="Contact.php" style="--i:5">Contacts</a>
        </nav>
    </header>
    <section class="certificate">
        <h3 class="h31">Certificates</h3>
        <p>Here are the certificates can be viewed and requested</p>
        <hr>
        <h3 class="h32">General Services</h3>
        <?php
            include 'connection.php';

            $sql = "SELECT * FROM certificates";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $counter = 0;
                while($row = $result->fetch_assoc()) {
                    if ($counter % 2 == 0) {
                        echo '<div class="container">';
                    }
                    $certificate_id = $row["id"];
                    $certificate_name = $row["certificate_name"];
                    $file = $row["file"];
                    $requirements = $row["requirements"];
        ?>
        <div class="box">
            <h2 style="text-align: left;"><?php echo $certificate_name; ?></h2>
            <ul style="list-style-type: disc; padding-left: 20px;">
                <?php
                    $requirements_list = explode("\n", $requirements);
                    foreach ($requirements_list as $requirement) {
                        echo "<li>$requirement</li>";
                    }
                ?>
            </ul>
            <div class="button">
                <a id="view" href="uploads/<?php echo $file; ?>" class="a2" target="_blank"><em>View</em></a>
                <a id="requestButton" class="a1" href="javascript:void(0);" onclick="showRequestForm(<?php echo $certificate_id; ?>)"><em>Request</em></a>
            </div>
        </div>
        <?php
                    $counter++;
                    if ($counter % 2 == 0) {
                        echo '</div>';
                    }
                }
                if ($counter % 2 != 0) {
                    echo '</div>';
                }
            } else {
                echo "0 results";
            }
            $conn->close();
        ?>
    </section>

    <!-- Modal Form -->
    <div id="modalOverlay" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="bi bi-file-earmark-text"></i>Document Request</h3>
                <button type="button" class="close" onclick="closeRequestForm()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="requestForm" class="p-3" method="post" onsubmit="return handleSubmit(event)" action="services_submit.php">
                    <input type="hidden" id="certificateIdInput" name="certificate_id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="fullName" class="form-label">Full Name</label>
                            <input type="text" id="fullName" name="fullName" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="age" class="form-label">Age</label>
                            <input type="number" id="age" name="age" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="purpose" class="form-label">Purpose</label>
                            <input type="text" id="purpose" name="purpose" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" id="address" name="address" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="dob" class="form-label">Date of Birth</label>
                            <input type="date" id="dob" name="dob" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="civilStatus" class="form-label">Civil Status</label>
                            <input type="text" id="civilStatus" name="civilStatus" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="placeOfBirth" class="form-label">Place of Birth</label>
                            <input type="text" id="placeOfBirth" name="placeOfBirth" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="sex" class="form-label">Sex</label>
                            <input type="text" id="sex" name="sex" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="business" class="form-label">Business (if applicable)</label>
                            <input type="text" id="business" name="business" class="form-control">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeRequestForm()">Close</button>
                <button type="submit" class="btn btn-primary" form="requestForm">Submit</button>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 Barangay Paule 1. All rights reserved.</p>
    </footer>
    <script src="js/index.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script>
        function toggleMenu() {
            var navigation = document.querySelector('.navigation');
            var hamburger = document.querySelector('.hamburger');

            navigation.classList.toggle('active');
            hamburger.classList.toggle('active');
        }

        function showRequestForm(certificateId) {
            var modalOverlay = document.getElementById('modalOverlay');
            modalOverlay.style.display = 'flex';

            // Set the certificate ID in the form's hidden input field
            document.getElementById('certificateIdInput').value = certificateId;

            // Update the URL to include the certificate ID
            var currentURL = window.location.href;
            var newURL = currentURL.split('?')[0] + '?certificate_id=' + certificateId;
            window.history.replaceState(null, null, newURL);
        }

        function closeRequestForm() {
            var modalOverlay = document.getElementById('modalOverlay');
            modalOverlay.style.display = 'none';

            // Redirect the user back to Services.php
            window.location.href = 'Certificate.php';
        }

        function handleSubmit(event) {
            event.preventDefault(); // Prevent the form from submitting the traditional way

            var form = document.getElementById('requestForm');
            var isValid = true;

            // Check if all required fields are filled
            var requiredFields = [
                'fullName', 'age', 'purpose', 'address',
                'dob', 'civilStatus', 'placeOfBirth',
                'sex', 'email'
            ];

            requiredFields.forEach(function(fieldId) {
                var field = document.getElementById(fieldId);
                if (!field.value) {
                    isValid = false;
                }
            });

            if (isValid) {
                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Your request has been submitted successfully!',
                }).then(function() {
                    form.submit(); // Submit the form after showing the success message
                });
            } else {
                // Show warning message
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: 'Please fill out all required fields!',
                });
            }
        }
    </script>
</body>
</html>
