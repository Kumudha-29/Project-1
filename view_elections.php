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
  <title>View Elections</title>
  <link rel="stylesheet" href="style.css">
</head>

<body style="background-image: url('Voting.png'); 
             background-size: cover; 
             background-repeat: no-repeat; 
             background-position: center center; 
             min-height: 100vh;
             margin: 0;">

<header>
  <h1>All Elections</h1>
</header>

<nav>
  <a href="voter_dashboard.php">Back to Dashboard</a>
  <a href="voter_logout.php">Logout</a>
</nav>

<div class="container">
  <table border="1" cellpadding="10" cellspacing="0">
    <tr>
      <th>Election Name</th>
      <th>Status</th>
      <th>Start Date</th>
      <th>End Date</th>
      <th>Action</th>
    </tr>
    <?php
    $result = $conn->query("SELECT * FROM elections ORDER BY start_date DESC");
    if ($result->num_rows > 0) {
        while ($election = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$election['name']}</td>
                    <td>{$election['status']}</td>
                    <td>{$election['start_date']}</td>
                    <td>{$election['end_date']}</td>
                    <td>";
            if ($election['status'] === 'Upcoming') {
                echo "<span style='color:gray;'>Not Started</span>";
            } elseif ($election['status'] === 'Ongoing') {
                echo "<a href='view_candidates.php?election_id={$election['id']}' class='btn green'>Vote Now</a>";
            } else {
                echo "<span style='color:red;'>Completed</span>";
            }
            echo "</td></tr>";
        }
    } else {
        echo "<tr><td colspan='5' style='text-align:center;'>No elections found.</td></tr>";
    }
    ?>
  </table>
</div>
</body>
</html>
