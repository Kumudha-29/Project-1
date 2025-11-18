<?php
session_start();
if (!isset($_SESSION['voter_id'])) { 
    header("Location: voter_login.php"); 
    exit(); 
}
include('db_connect.php');
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Voter Dashboard</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .dashboard-actions {
      display: flex;
      gap: 20px;
      margin-top: 20px;
    }
    .dashboard-actions a {
      padding: 12px 20px;
      background-color: #007BFF;
      color: white;
      text-decoration: none;
      border-radius: 6px;
      font-weight: bold;
    }
    .dashboard-actions a:hover {
      background-color: #0056b3;
    }
  </style>
</head>
<body style="background-image: url('Voting.png'); 
             background-size: cover; 
             background-repeat: no-repeat; 
             background-position: center center; 
             min-height: 100vh;
             margin: 0;">


<header>
  <h1>Voter Dashboard</h1>
</header>

<nav>
  <a href="voter_dashboard.php" class="active">Dashboard</a>
   <a href="complaints_voter.php">Complaints</a>
  <a href="voter_logout.php">Logout</a>
</nav>

<div class="container">
  <h2>Hello, <?php echo htmlspecialchars($_SESSION['voter_name']); ?> 👋</h2>
  <p class="small">Choose an action below:</p>

  <div class="dashboard-actions">
    <a href="view_elections.php">View Elections</a>
    <a href="results.php">View Results</a>
  </div>
</div>
</body>
</html>
