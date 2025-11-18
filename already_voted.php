<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['voter_id'])) {
    header("Location: voter_login.php");
    exit();
}

$voter_id = $_SESSION['voter_id'];

// Fetch the election the voter has voted in (optional: show multiple if needed)
$result = $conn->query("SELECT e.name AS election_name 
                        FROM votes v
                        JOIN elections e ON v.election_id = e.id
                        WHERE v.voter_id='$voter_id'");
$election = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Already Voted</title>
<link rel="stylesheet" href="style.css">
<style>
body {
    font-family: Arial, sans-serif;
    background: #f0f4f7;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.vote-message {
    background: #fff3cd;
    border: 1px solid #ffeeba;
    padding: 40px;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    max-width: 500px;
}

.vote-message h2 {
    color: #856404;
    margin-bottom: 20px;
}

.vote-message p {
    font-size: 1.1em;
    color: #555;
}

.vote-message a {
    display: inline-block;
    margin-top: 25px;
    padding: 10px 20px;
    background: #007BFF;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    transition: 0.3s;
}

.vote-message a:hover {
    background: #0056b3;
}
</style>
</head>
<body style="background-image: url('Voting.png'); 
             background-size: cover; 
             background-repeat: no-repeat; 
             background-position: center center; 
             min-height: 100vh;
             margin: 0;">



<div class="already-voted ">
    <h2>⚠️ You have already voted!</h2>
    <?php if ($election): ?>
        <p>You have already cast your vote for the election: <strong><?php echo htmlspecialchars($election['election_name']); ?></strong>.</p>
    <?php else: ?>
        <p>You have already cast your vote.</p>
    <?php endif; ?>
    <a href="voter_dashboard.php">Back to Dashboard</a>
</div>

</body>
</html>
