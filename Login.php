<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="icon" href="images/logo1.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/Login.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
    .my-loading-button {
      color: white;
      background-color: #333;
      border-radius: 5px;
      padding: 10px 20px;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .my-loading-button:hover {
      background-color: #555;
    }

    .my-confirm-button {
      color: white;
      background-color: #00ff99;
      border-radius: 5px;
      padding: 10px 20px;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .my-confirm-button:hover {
      background-color: #00ff70;
    }

    .my-cancel-button {
      color: white;
      background-color: #ff3333;
      border-radius: 5px;
      padding: 10px 20px;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .my-cancel-button:hover {
      background-color: #ff1a1a;
    }

    .my-warning-button {
      color: white;
      background-color: #ffb300;
      border-radius: 5px;
      padding: 10px 20px;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .my-warning-button:hover {
      background-color: #ffa000;
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
          <h3 style="margin-top: 250px;">Barangay Paule 1</h3>
          <p class="welcome">Welcome!</p>
          <p class="message">Your gateway to staying connected to Barangay Community</p>
      </div>
      </div>
      <div class="contentBx">
        <div class="formBx">
          <img src="images/logo1.png" alt="Barangay Logo">
          <h2>Login</h2>
          <form id="loginForm" action="login_test.php" method="POST">
            <div class="inputBx">
              <label>Username</label>
              <div class="input-group">
                <span><i class="bi bi-person"></i></span>
                <input type="text" name="username" title="text" required value="<?php echo isset($_COOKIE['remembered_username']) ? htmlspecialchars($_COOKIE['remembered_username']) : ''; ?>">
              </div>
            </div>
            <div class="inputBx">
              <label>Password</label>
              <div class="input-group">
                <span><i class="bi bi-lock"></i></span>
                <input type="password" id="password" name="password" title="password" required>
                <span class="password-toggle"><i class="bi bi-eye-slash"></i></span>
              </div>
            </div>
            <div class="remember">
              <label><input type="checkbox" name="remember"> Remember Me</label>
              <a href="ForgotPassword.php">Forgot Password?</a>
            </div>
            <div class="inputBx">
              <input type="submit" value="Log In" name="">
            </div>
            <footer>
              <p>&copy; 2024 Barangay Paule 1. All rights reserved.</p>
            </footer>
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
            passwordToggle.style.display = 'block'; 
        } else {
            passwordToggle.style.display = 'none'; 
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

    document.getElementById('loginForm').addEventListener('submit', function(event) {
        event.preventDefault(); 
        Swal.fire({
            title: 'Please wait...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();

                fetch('login_test.php', {
                    method: 'POST',
                    body: new FormData(document.getElementById('loginForm'))
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Success!',
                            text: 'You have successfully logged in!',
                            icon: 'success',
                            confirmButtonText: 'OK',
                            animation: true,
                            customClass: {
                                confirmButton: 'my-confirm-button success'
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'AdminDashboard.php'; // Redirect to AdminDashboard.php on success
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Invalid username or password. Please try again!',
                            icon: 'error',
                            confirmButtonText: 'OK',
                            animation: true,
                            customClass: {
                                confirmButton: 'my-confirm-button wrong'
                            }
                        });
                    }
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
            }
        });
    });
});
    </script>
</body>
</html>