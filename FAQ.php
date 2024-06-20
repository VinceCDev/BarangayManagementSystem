<!DOCTYPE html> 
<html lang="en"> 
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ</title>
    <link rel="icon" href="images/logo1.png" type="image/x-icon">
    <link rel="stylesheet" href="css/FAQ.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        .container{
            width: 95%;
            height: 40vh;
            position: relative;
            justify-content: center;
            background-size: cover;
            background-position: center;
            display: flex;
            padding: 70px 10% 0px;
            align-items: center;
            border-radius: 20px; 
            margin: 0 auto;
            text-align: center;
            flex-direction: column;
            margin-top: 550px;
        }
        
    </style>
</head> 
<body> 
    <header class="header"> 
        <a href="#" class="logo"> 
            <img src="images/logo1.png" alt="Error Image" height="60px" width="60px"/>
            <h2>Barangay Paule 1</h2> 
        </a>
        <button class="hamburger" title="hamburger" onclick="toggleMenu()"> 
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
            <a href="Certificate.php" style="--i:3">Services</a>
            <a id="faq1" href="FAQ.php" style="--i:4;color: white; background-color: #00aaff;
            padding: 12px; border-radius: 30px;">FAQ</a>
            <a href="Contact.php" style="--i:5">Contacts</a>
        </nav> 
    </header>
    <section class="FAQ">
        <h3 class="h31">Frequently Asked Questions</h3>
        <p>Here are the FAQ within the barangay</p>
        <hr>
        <section class="container">
        <?php
          include 'connection.php';

          $sql = "SELECT * FROM faq";
          $result = $conn->query($sql);

          if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                  $id = $row["id"];
                  $question = $row["question"];
                  $answer = $row["answer"];
                  ?>
                  <div class="faq-item">
                  <div class="question"><?php echo $id . ". " . $question; ?></div>
                  <div class="answer"><?php echo $answer; ?></div>
                  </div>
                  <?php
              }
          } else {
              echo "No FAQs found";
          }

          $conn->close();
        ?>
    </section>
    </section>
    <footer> 
      <p>&copy; 2024 Barangay Paule 1. All rights reserved.</p> 
    </footer>
    <script src="js/index.js"></script>    
    <script src="https://unpkg.com/typed.js@2.1.0/dist/typed.umd.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleMenu() {
            var navigation = document.querySelector('.navigation');
            var hamburger = document.querySelector('.hamburger');

            navigation.classList.toggle('active');

            hamburger.classList.toggle('active');
        }
    </script>
</body> 
</html>