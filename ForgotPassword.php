<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        include 'connection.php';

        $email = $_POST['userName'];

        $sql = "SELECT * FROM users WHERE userName = '$email'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            header("Location: ResetPassword.php?userName=$email");
            exit(); 
        } else {
            $errorMessage = "Email address not found. Please try again.";
        }

        $conn->close();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="icon" href="images/logo1.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="css/ForgotPassword.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
    @media (max-width: 768px) {
    .imgBx{
        display: none;
    }

    section {
        width: 90%;
        height: auto;
        margin: 0 auto;
        margin-top: 120px !important;
    }

    .contentBx {
        width: 100%;
    }

    .formBx {
        width: 100%;
        padding: 40px 20px; 
        background: rgba(255, 255, 255, 0.9);
        margin: 20px; 
    }

    .formBx h2 {
        font-size: 25px;
    }

    .inputBx {
        margin-bottom: 15px; 
    }
    
    .input-group input {
        padding: 10px; 
    }

    .password-toggle i {
        margin-left: 10px;
    }

    .remember {
        margin-bottom: 15px; 
    }

    .remember a {
        margin-left: 20px;
    }

    .inputBx input[type="submit"] {
        margin: 0 auto;
        margin-top: 20px;
    }

    .back{
      margin: auto;
    }

    .back a{
      margin: 0 auto;
    }
}
    </style>
</head>
<body>
    <div class="background-box"></div>
    <div class="background-box"></div>
    <section>
      <div class="imgBx">
        <img class="logo-image" src="images/logo1.png" alt="Barangay Logo">
        <div class="overlay-text">
          <h3>Barangay Paule 1</h3>
          <p class="welcome">Welcome!</p>
          <p class="message">Your gateway to staying connected to Barangay Community</p>
      </div>
      </div>
      <div class="contentBx">
        <div class="formBx">
          <img src="images/logo1.png" alt="Barangay Logo">
          <h2>Forgot Password</h2>
          <p>Enter your email associated with your account and we'll send a verification number</p>
          <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="inputBx">
              <label>Email Address</label>
              <div class="input-group">
                <span><i class="bi bi-person"></i></span>
                <input type="text" name="userName">
              </div>
            </div>
            <div class="inputBx">
                <?php if(isset($email)) { ?>
                    <a href="ResetPassword.php?userName=<?php echo $email;?>" class="btn btn-primary" style="width: 100%; border-radius: 50px;
                    margin: 0 auto; font-size: 18px; font-weight: 500;">Submit</a>
                <?php } else { ?>
                    <input type="submit" value="Submit" name="submitButton" class="btn btn-primary" style="width: 100%; border-radius: 50px;
                    margin: 0 auto; font-size: 18px; font-weight: 500;">
                <?php } ?>
              <div class="back">
                <a href="Login.php"><span><i class="bi bi-arrow-left"></i></span><p>Back to Login</p></a>
              </div>
            </div>
          </form>
        </div>
      </div>
    </section>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const passwordInput = document.getElementById('password');
        const passwordToggle = document.querySelector('.password-toggle');
    
        passwordInput.addEventListener('input', function() {
            if (passwordInput.value.trim() !== '') {
                passwordToggle.style.display = 'block'; // Display the eye icon
            } else {
                passwordToggle.style.display = 'none'; // Hide the eye icon
            }
        });
    
        passwordToggle.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            if (type === 'text') {
                passwordToggle.querySelector('i').classList.remove('bi-eye');
                passwordToggle.querySelector('i').classList.add('bi-eye-slash');
            } else {
                passwordToggle.querySelector('i').classList.remove('bi-eye-slash');
                passwordToggle.querySelector('i').classList.add('bi-eye');
            }
        });
    
        if (passwordInput.value.trim() === '') {
            passwordToggle.style.display = 'none';
        }
    });
    
    </script>
</body>
</html>