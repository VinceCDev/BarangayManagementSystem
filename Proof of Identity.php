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
    <title>Profile | Proof of Identity</title>
    <link rel="icon" href="images/logo1.png" type="image/x-icon">
    <link rel="stylesheet" href="css/Proof of Identity.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap">
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
        <form id="personalDataForm" action="proof_insert.php" method="post" enctype="multipart/form-data">
            <div class="container1">
                <div class="upload-box">
                    <label for="file1">Upload 2 x 2 Picture</label>
                    <ul>
                        <li>The dimensions of a 2x2 picture are typically 2 inches by 2 inches.</li>
                        <li>The background of the photo is usually required to be plain white or off-white.</li>
                        <li>The subject should have a neutral facial expression with their eyes open and visible. </li>
                    </ul>
                    <input type="file" id="file1" name="file1" accept="image/*">
                </div>
                <div class="upload-box">
                    <label for="file2">Upload Valid ID</label>
                    <ul>
                        <li>Valid ID should be verified based on the Philipines. </li>
                        <li>Ensure that the uploaded document is clear and legible.</li>
                        <li>Uploaded file should be in PDF or image format.</li>
                    </ul>
                    <input type="file" id="file2" name="file2" accept="image/*">
                </div>
                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" onclick="redirectToPreviousPage()">
                        <i class="bi bi-arrow-left"></i> Back
                    </button>
                    <button type="submit" class="btn btn-primary" onclick="submitFormData()">
                        Submit <i class="bi bi-check"></i>
                    </button>                    
                </div>
            </div>      
        </form>
        </div>          
    </section>
    <footer> 
        <p>&copy; 2024 Barangay Paule 1. All rights reserved.</p> 
    </footer>
    <script>
        document.getElementById('personalDataForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent default form submission behavior

            // Perform any client-side validation here if needed

            // Submit the form via AJAX
            submitFormData();
        });

        // Function to submit form data via AJAX
        function submitFormData() {
            // Get form data
            const formData = new FormData(document.getElementById('personalDataForm'));

            // Create and send AJAX request
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'proof_insert.php', true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    console.log(xhr.responseText); // Log server response
                    // Redirect to Proof of Identity.php after successful submission
                    window.location.href = "UserProfile.php";
                } else {
                    console.error('Error:', xhr.statusText); // Log error
                }
            };
            xhr.onerror = function() {
                console.error('Request failed'); // Log request failure
            };
            xhr.send(formData); // Send form data
        }
        
        function redirectToPreviousPage() {
            window.location.href = "Other Info.html";
        }

        document.getElementById('file1').addEventListener('change', handleFileSelect);
        document.getElementById('file2').addEventListener('change', handleFileSelect);
        function handleFileSelect(event) {
    const file = event.target.files[0];
    const uploadStatus = document.getElementById('uploadStatus');
    const fileNameSpan = document.getElementById('file-name');
    const uploadProgressSpan = document.getElementById('upload-progress');
    const loadingIndicator = document.getElementById('loading-indicator');

    if (file) {
        const fileSizeInMb = file.size / (1024 * 1024);
        const uploadSpeedMbps = 10;
        const uploadTimeInSeconds = fileSizeInMb * 8 / uploadSpeedMbps;

        fileNameSpan.textContent = `File Name: ${file.name}`;
        uploadStatus.style.display = 'block'; 
        loadingIndicator.style.animationPlayState = 'running'; 

        let progress = 0;
        const interval = setInterval(() => {
            progress++;
            if (progress <= 100) {
                uploadProgressSpan.textContent = `Upload Progress: ${progress}%`;
            } else {
                clearInterval(interval);
                uploadStatus.style.backgroundColor = '#f8d7da'; 
                uploadProgressSpan.textContent = 'Upload Failed: Your image was not uploaded.';
                loadingIndicator.style.animationPlayState = 'paused'; 
            }
        }, uploadTimeInSeconds * 1000 / 100);
    } else {
        uploadStatus.style.display = 'none';
    }
}

    </script>
    <script src="js/index.js"></script>    
    <script src="js/map.js"></script>
    <script src="https://unpkg.com/typed.js@2.1.0/dist/typed.umd.js"></script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body> 
</html>