<?php
session_start();
include('db_connect.php');
if (!isset($_SESSION['voter_id'])) { header("Location: voter_login.php"); exit(); }

$voter_id = (int)$_SESSION['voter_id'];
$message = '';

// check if already voted
$stmt = $conn->prepare("SELECT voted FROM voters WHERE id = ?");
$stmt->bind_param("i", $voter_id);
$stmt->execute();
$stmt->bind_result($voted);
$stmt->fetch();
$stmt->close();

if ($voted) {
    $message = "You have already voted. Thank you!";
} else {
    if (isset($_POST['vote'])) {
        $candidate = (int)$_POST['candidate'];
        $ins = $conn->prepare("INSERT INTO votes (voter_id, candidate_id) VALUES (?, ?)");
        $ins->bind_param("ii", $voter_id, $candidate);
        if ($ins->execute()) {
            $upd = $conn->prepare("UPDATE voters SET voted = 1 WHERE id = ?");
            $upd->bind_param("i", $voter_id);
            $upd->execute();
            $upd->close();
            $message = "Your vote was recorded. Thank you!";
        } else {
            $message = "Error recording vote: " . $conn->error;
        }
        $ins->close();
    }
}

// fetch candidate list
$candidates = $conn->query("SELECT * FROM candidates ORDER BY name ASC");
?>
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Vote</title><link rel="stylesheet" href="style.css"></head>
<body style="background-image: url('Voting.png'); 
             background-size: cover; 
             background-repeat: no-repeat; 
             background-position: center center; 
             min-height: 100vh;
             margin: 0;">


<header><h1>Vote</h1></header>
<nav><a href="voter_dashboard.php">Dashboard</a> <a href="results.php">Results</a> <a href="voter_logout.php">Logout</a></nav>
<div class="container">
  <h2>Cast Your Vote</h2>
  <?php if ($message): ?><div class="alert <?php echo (strpos($message,'recorded')!==false?'success':'error');?>"><?php echo $message; ?></div><?php endif; ?>

  <?php if (!$voted): ?>
  <form method="post">
    <select name="candidate" required>
      <option value="">-- Select Candidate --</option>
      <?php while($r = $candidates->fetch_assoc()): ?>
        <option value="<?php echo $r['id']; ?>"><?php echo htmlspecialchars($r['name']); ?> (<?php echo htmlspecialchars($r['party']); ?>)</option>
      <?php endwhile; ?>
    </select>
    <input type="submit" name="vote" value="Submit Vote" onclick="return confirm('Are you sure you want to vote for this candidate?');">
  </form>
  <?php endif; ?>
</div>
</body>
</html>
