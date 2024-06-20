<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact</title>
    <link rel="icon" href="images/logo1.png" type="image/x-icon">
    <link rel="stylesheet" href="css/Contact.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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
                <button class="btn btn-primary dropdown-toggle" style="--i:2" title="dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                    Our Barangay</button>
                <div class="dropdown-content">
                  <a href="GeneralInformation.php">General Information</a>
                  <a href="History.php">History</a>
                  <a href="Maps.php">Maps</a>
                  <a href="Photos.php">Photo Album</a>
                </div>
            </div>
            <a href="Certificate.php" style="--i:3">Services</a>
            <a href="FAQ.php" style="--i:4">FAQ</a>
            <a id="cont" href="Contact.php" id="cont" style="--i:5; color: white; background-color: #00aaff;
            padding: 12px; border-radius: 30px;">Contacts</a>
        </nav>
    </header>
    <section class="contact">
        <h3 class="h31">Contacts</h3>
        <p>Here are the contacts within the barangay</p>
        <hr>
        <div class="container">
            <div class="contact-info">
                <div class="box">
                    <?php
                    include 'connection.php';

                    $sql = "SELECT * FROM contacts where id=1";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        $label  = $row["label"];
                        $contact  = $row["contacts"];
                    } else {
                        $label  = "";
                        $contact  = "";
                    }

                    $conn->close();
                   ?>
                    <a href="tel:<?php echo $contact;?>" target="_blank">
                        <h3><i class='bi bi-telephone'></i><?php echo $label;?></h3>
                        <p><?php echo $contact;?></p>
                    </a>                    
                </div>
                <div class="box">
                    <?php
                    include 'connection.php';

                    $sql = "SELECT * FROM contacts where id=2";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        $labels  = $row["label"];
                        $contacts  = $row["contacts"];
                    } else {
                        $labels  = "";
                        $contacts  = "";
                    }

                    $conn->close();
                   ?>
                    <a href="https://mail.google.com/mail/?view=cm&fs=1&to=<?php echo urlencode($contacts);?>" target="_blank">
                        <h3><i class='bi bi-envelope'></i><?php echo $labels;?></h3>
                        <p><?php echo $contacts;?></p>
                    </a>
                </div>
                <div class="box">
                    <?php
                    include 'connection.php';

                    $sql = "SELECT * FROM contacts where id=3";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        $fblabels  = $row["label"];
                        $fbcontacts  = $row["contacts"];
                    } else {
                        $fblabels  = "";
                        $fbcontacts  = "";
                    }

                    $conn->close();
                   ?>
                    <a href="<?php echo $fbcontacts;?>" target="_blank">
                        <h3><i class='bi bi-facebook'></i><?php echo $fblabels;?></h3>
                        <p>web.facebook.com/paule.uno</p>
                    </a>
                </div>
                <div class="box">
                    <a href="https://www.google.com/maps/place/Paule+1,+Rizal,+Laguna/@14.109326,121.4010232,16z/data=!3m1!4b1!4m6!3m5!1s0x33bd5a4cc34d11a3:0x15f01b4eabdbeee8!8m2!3d14.1073484!4d121.4013285!16s%2Fg%2F1tgn74f0?entry=ttu" target="_blank">
                        <h3><i class='bi bi-geo-alt'></i> Location</h3>
                        <p>Barangay Paule 1, Rizal, Laguna</p>
                    </a>
                </div>
            </div>  
           <div class="form-container">
    <form id="message-form" action="contact_insert.php" method="POST">
        <h3>Send Message</h3>
        <div class="form-group">
            <div class="input-group" id="name-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" placeholder="Name">
            </div>
            <div class="input-group" id="age-group">
                <label for="age">Age:</label>
                <input type="number" id="age" name="age" placeholder="Age">
            </div>
        </div>
        <div class="form-group">
            <div class="input-group" id="email-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="Email Address">
            </div>
            <div class="input-group" id="contact-group">
                <label for="contact">Contact:</label>
                <input type="text" id="contact" name="contact" placeholder="Contact Number">
            </div>
        </div>
        <div class="form-group" id="message-group">
            <label for="message-text">Message:</label>
            <textarea id="message-text" name="message" rows="5" placeholder="Type any message"></textarea>
        </div>
        <button type="submit" class="my-loading-button">
            Send Message <i class="bi bi-envelope-fill"></i>
        </button>
    </form>
</div>
</div>
</section>
<footer>
    <p>&copy; 2024 Barangay Paule 1. All rights reserved.</p>
</footer>
<script src="js/index.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('message-form').addEventListener('submit', function(event) {
    event.preventDefault(); 

    var name = document.getElementById('name').value;
    var age = document.getElementById('age').value;
    var email = document.getElementById('email').value;
    var contact = document.getElementById('contact').value;
    var message = document.getElementById('message-text').value;

    if (!name.trim() || !age.trim() || !email.trim() || !contact.trim() || !message.trim()) {
        Swal.fire({
            title: 'Warning!',
            text: 'Please fill all required fields before submitting.',
            icon: 'warning',
            confirmButtonText: 'OK',
            animation: true,
            customClass: {
                confirmButton: 'my-warning-button wrong'
            }
        });
        return;
    }

    var formData = new FormData();
    formData.append('name', name);
    formData.append('age', age);
    formData.append('email', email);
    formData.append('contact', contact);
    formData.append('message', message);

    fetch('contact_insert.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        Swal.fire({
            title: 'Success!',
            text: 'Message sent successfully!',
            icon: 'success',
            confirmButtonText: 'OK',
            animation: true,
            customClass: {
                confirmButton: 'my-confirm-button success'
            }
        }).then(() => {
            window.location.href = 'Contact.php';
        });
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            title: 'Error!',
            text: 'An error occurred while processing your request. Please try again later.',
            icon: 'error',
            confirmButtonText: 'OK',
            animation: true,
            customClass: {
                confirmButton: 'my-confirm-button wrong'
            }
        });
    });
});

function toggleMenu() {
    var navigation = document.querySelector('.navigation');
    var hamburger = document.querySelector('.hamburger');

    navigation.classList.toggle('active');

    hamburger.classList.toggle('active');
}
</script>
</body>
</html>