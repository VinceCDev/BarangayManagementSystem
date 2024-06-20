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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | Personal Data</title>
    <link rel="icon" href="images/logo1.png" type="image/x-icon">
    <link rel="stylesheet" href="css/Personal Data.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .number::after {
            content: "";
            position: absolute;
            top: 50%;
            left: calc(100% + 10px); 
            transform: translateY(-50%);
            width: 335px; 
            height: 1px; 
            background-color: black; 
        }

        .number:last-child::after {
            display: none; 
        }
    </style>
</head> 
<body> 
    <section class="contact">
        <h3 class="h31">Profile Data</h3>
        
        <div class="container">
            <div class="numbers">
                <span class="number first-child">
                    1
                    <p>Personal Data</p>
                </span>
                <span class="number">2<p>Other Information
                </p></span>
                <span class="number">3<p>Proof of Identity</p></span>
            </div>
            <form id="personalDataForm" action="personal_data_insert.php" method="POST" onsubmit="return validateForm()">
                <div class="form-group">
                    <div class="input-group">
                        <label for="firstname">First Name</label>
                        <input type="text" id="firstname" name="firstname" placeholder="First Name" required>
                    </div>
                    <div class="input-group">
                        <label for="middlename">Middle Name</label>
                        <input type="text" id="middlename" name="middlename" placeholder="Middle Name" required>
                    </div>
                    <div class="input-group">
                        <label for="lastname">Last Name</label>
                        <input type="text" id="lastname" name="lastname" placeholder="Last Name" required>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <label for="gender">Gender</label>
                        <select id="gender" name="gender" required>
                            <option value="" disabled selected>Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label for="birthdate">Birth Date</label>
                        <input type="date" id="birthdate" name="birthdate" required>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="Email Address" required>
                        <div class="underline"></div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <label for="contact">Contact</label>
                        <input type="tel" id="contact" name="contact" placeholder="Contact Number" required>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <label for="religion">Religion</label>
                        <input type="text" id="religion" name="religion" placeholder="Religion" required>
                    </div>
                    <div class="input-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" required>
                            <option value="" disabled selected>Select Status</option>
                            <option value="single">Single</option>
                            <option value="married">Married</option>
                            <option value="divorced">Divorced</option>
                            <option value="widowed">Widowed</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <label for="emergency_name">Person In Case of Emergency</label>
                        <input type="text" id="emergency_name" name="emergency_name" placeholder="Emergency Person" required>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <label for="emergency_contact">Emergency Contact</label>
                        <input type="tel" id="emergency_contact" name="emergency_contact" placeholder="Emergency Contact Number" required>
                    </div>
                </div>
                <button type="button" class="btn btn-primary btn-block" onclick="submitFormData()">
                    Next <i class="bi bi-arrow-right"></i>
                </button>        
            </form>
        </div>          
    </section>
    <footer> 
        <p>&copy; 2024 Barangay Paule 1. All rights reserved.</p> 
    </footer>
    <script>
        function submitFormData() {
            const isValid = validateForm(); // Check if the form is valid
            if (isValid) {
                const formData = new FormData(document.getElementById('personalDataForm'));

                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'personal_data_insert.php', true);
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        console.log(xhr.responseText);
                        window.location.href = "Other Info.php"; // Redirect to Important_Info.php
                    } else {
                        console.error('Error:', xhr.statusText); 
                    }
                };
                xhr.onerror = function() {
                    console.error('Request failed');
                };
                xhr.send(formData);
            }
        }

        function validateForm() {
            // Get all input fields
            const inputs = document.querySelectorAll('#personalDataForm input, #personalDataForm select');
            let isValid = true; // Set initial validation flag to true
            for (let i = 0; i < inputs.length; i++) {
                // Check if any field is empty
                if (!inputs[i].value) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Please fill out all the required fields',
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer);
                            toast.addEventListener('mouseleave', Swal.resumeTimer);
                        }
                    });
                    isValid = false; // Set validation flag to false
                    break; // Exit loop if any field is empty
                }
            }
            return isValid; // Return validation flag
        }
    </script>
    <script src="js/index.js"></script>  
    <script src="https://unpkg.com/typed.js@2.1.0/dist/typed.umd.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>