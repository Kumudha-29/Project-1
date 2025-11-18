<?php
session_start();
if (!isset($_SESSION['voter_id'])) {
    header("Location: voter_login.php");
    exit();
}
include('db_connect.php');

$voter_id = $_SESSION['voter_id'];
$election_id = $_GET['election_id'] ?? 0;

// Check if election exists
$election = $conn->query("SELECT * FROM elections WHERE id = '$election_id'")->fetch_assoc();
if (!$election) {
    echo "<p>Invalid election selected.</p>";
    exit();
}

// Check if voter has already voted
$checkVote = $conn->query("SELECT * FROM votes WHERE voter_id='$voter_id' AND election_id='$election_id'");
if ($checkVote->num_rows > 0) {
    ?>
    <div class="already-voted-container">
        <h2>⚠️ You have already voted!</h2>
        <p>You have already cast your vote for the election: <strong><?php echo htmlspecialchars($election['name']); ?></strong>.</p>
        <a href="view_elections.php" class="btn">Back to Elections</a>
    </div>

    <style>
    .already-voted-container {
        max-width: 500px;
        margin: 80px auto;
        background: #fff3cd;
        border: 1px solid #ffeeba;
        padding: 30px;
        border-radius: 12px;
        text-align: center;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        font-family: Arial, sans-serif;
    }
    .already-voted-container h2 {
        color: #856404;
        margin-bottom: 20px;
    }
    .already-voted-container p {
        font-size: 1.1em;
        color: #555;
        margin-bottom: 25px;
    }
    .already-voted-container .btn {
        display: inline-block;
        background: #007BFF;
        color: white;
        padding: 10px 20px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: bold;
        transition: 0.3s;
    }
    .already-voted-container .btn:hover {
        background: #0056b3;
    }
    </style>
    <?php
    exit();
}

// Fetch candidates
$candidates = $conn->query("SELECT * FROM candidates WHERE election_id='$election_id'");
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Vote - <?php echo htmlspecialchars($election['name']); ?></title>
  <link rel="stylesheet" href="style.css">
  <style>
    .candidate-card {
      border: 1px solid #ccc;
      padding: 15px;
      margin: 10px;
      border-radius: 8px;
      background-color: #f9f9f9;
    }
    .vote-btn {
      background-color: #007bff;
      color: white;
      border: none;
      padding: 8px 16px;
      border-radius: 5px;
      cursor: pointer;
    }
    .vote-btn:hover {
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
  <h1>Vote for <?php echo htmlspecialchars($election['name']); ?></h1>
</header>

<nav>
  <a href="voter_dashboard.php">Dashboard</a>
  <a href="view_elections.php">Back to Elections</a>
  <a href="voter_logout.php">Logout</a>
</nav>

<div class="container">
  <form method="POST" action="vote.php">
    <input type="hidden" name="election_id" value="<?php echo $election_id; ?>">
    <?php
    if ($candidates->num_rows > 0) {
        while ($row = $candidates->fetch_assoc()) {
            echo "<div class='candidate-card'>
                    <input type='radio' name='candidate_id' value='{$row['id']}' required> 
                    <b>{$row['name']}</b><br>
                    <small>Party: {$row['party']}</small>
                  </div>";
        }
        echo "<br><button type='submit' class='vote-btn'>Cast Vote</button>";
    } else {
        echo "<p>No candidates available for this election.</p>";
    }
    ?>
  </form>
</div>
</body>
</html>
