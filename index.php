<?php
session_start();

// Optional: Redirect users if already logged in
if (isset($_SESSION['admin'])) {
    header("Location: admin_dashboard.php");
    exit();
}

if (isset($_SESSION['voter'])) {
    header("Location: voter_dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>E-Voting System</title>
  <link rel="stylesheet" href="style.css">
</head>
<body style="background-image: url('Voting.png'); 
             background-size: cover; 
             background-repeat: no-repeat; 
             background-position: center center; 
             min-height: 100vh;
             margin: 0;">


  <header>
    <h1>E-Voting System</h1>
    <p>Empowering Democracy Through Technology</p>
  </header>

  <div class="home-container">
    <div class="home-card">
      <h3>Admin Login</h3>
      <p>Access the admin panel to manage candidates, voters, and view election data.</p>
      <a href="./admin_login.php">Login as Admin</a>
    </div>

    <div class="home-card">
      <h3>Voter Portal</h3>
      <p>Register, log in, and cast your vote securely in the ongoing elections.</p>
      <a href="./voter_register.php">Register / Login</a>
    </div>
    
    <div class="home-card">
      <h3>candidate Registration</h3>
      <p>Registration of candidate.</p>
      <a href="./candidate_register.php"> Candidate Register</a>
    </div>
  </div>

  <footer>
    <p>© 2025 E-Voting System | Designed for Secure & Transparent Elections</p>
  </footer>
</body>
</html>
