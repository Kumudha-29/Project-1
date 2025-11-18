<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['voter_id'])) {
    header("Location: voter_login.php");
    exit();
}

$voter_id = (int)$_SESSION['voter_id'];
$message = '';
$vote_success = false;

// Handle vote submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['candidate_id'], $_POST['election_id'])) {
    $candidate_id = (int)$_POST['candidate_id'];
    $election_id = (int)$_POST['election_id'];

    // Check if voter already voted in this election
    $stmt = $conn->prepare("SELECT * FROM votes WHERE voter_id=? AND election_id=?");
    $stmt->bind_param("ii", $voter_id, $election_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $message = "You have already voted in this election.";
    } else {
        // Record the vote
        $stmt = $conn->prepare("INSERT INTO votes (voter_id, election_id, candidate_id) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $voter_id, $election_id, $candidate_id);
        if ($stmt->execute()) {
            $message = "✅ Your vote has been successfully cast!";
            $vote_success = true;
        } else {
            $message = "Error recording vote: " . $conn->error;
        }
    }
}

// Fetch all elections
$now = date('Y-m-d H:i:s');
$elections = $conn->query("SELECT * FROM elections ORDER BY start_date ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Vote</title>
<link rel="stylesheet" href="style.css">
</head>
<body style="background-image: url('Voting.png'); 
             background-size: cover; 
             background-repeat: no-repeat; 
             background-position: center center; 
             min-height: 100vh;
             margin: 0;">


<header><h1>Cast Your Vote</h1></header>
<nav>
  <a href="voter_dashboard.php">Dashboard</a>
  <a href="vote.php" class="active">Vote</a>
  <a href="results.php">Results</a>
  <a href="voter_logout.php">Logout</a>
</nav>

<div class="container">
<?php if ($message): ?>
  <div class="alert <?php echo $vote_success ? 'success' : 'error'; ?>"><?php echo $message; ?></div>
<?php endif; ?>

<h2>Elections</h2>
<?php while ($row = $elections->fetch_assoc()): 
    $start = $row['start_date'];
    $end   = $row['end_date'];
    $election_id = $row['id'];
?>
    <div class="election">
        <strong><?php echo htmlspecialchars($row['name']); ?></strong><br>
        <?php echo "Start: $start | End: $end"; ?><br>

        <?php if ($now < $start): ?>
            <button disabled>Not Started</button>
        <?php elseif ($now > $end): ?>
            <button disabled>Ended</button>
        <?php else: ?>
            <?php if (!$vote_success || (isset($_POST['election_id']) && $_POST['election_id'] != $election_id)): ?>
                <?php
                // Fetch candidates for this election
                $candidates_res = $conn->query("SELECT * FROM candidates WHERE election_id=$election_id ORDER BY name ASC");
                ?>
                <form method="post">
                    <input type="hidden" name="election_id" value="<?php echo $election_id; ?>">
                    <?php while($c = $candidates_res->fetch_assoc()): ?>
                        <label>
                            <input type="radio" name="candidate_id" value="<?php echo $c['id']; ?>" required>
                            <?php echo htmlspecialchars($c['name']); ?> (<?php echo htmlspecialchars($c['party']); ?>)
                        </label><br>
                    <?php endwhile; ?>
                    <button type="submit" onclick="return confirm('Are you sure you want to vote for this candidate?');">Vote Now</button>
                </form>
            <?php else: ?>
                <div class="already-voted">
    ⚠️ You have already voted in this election.
</div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <hr>
<?php endwhile; ?>
</div>
</body>
</html>
