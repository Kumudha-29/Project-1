<?php
include('db_connect.php');
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

$id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM elections WHERE id=$id");
$election = $result->fetch_assoc();

if (!$election) {
    die("Election not found.");
}

// Auto-update status based on current time
date_default_timezone_set('Asia/Kolkata');
$current_time = date('Y-m-d H:i:s');
if ($current_time < $election['start_date']) {
    $status = 'Upcoming';
} elseif ($current_time > $election['end_date']) {
    $status = 'Completed';
} else {
    $status = 'Ongoing';
}

if (isset($_POST['update'])) {
    $name = trim($_POST['name']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Automatically determine status
    $current_time = date('Y-m-d H:i:s');
    if ($current_time < $start_date) {
        $status = 'Upcoming';
    } elseif ($current_time > $end_date) {
        $status = 'Completed';
    } else {
        $status = 'Ongoing';
    }

    $stmt = $conn->prepare("UPDATE elections SET name=?, start_date=?, end_date=?, status=? WHERE id=?");
    $stmt->bind_param("ssssi", $name, $start_date, $end_date, $status, $id);
    if ($stmt->execute()) {
        header("Location: create_election.php"); // ✅ redirect to existing page
        exit();
    } else {
        $msg = "❌ Update failed!";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Election</title>
<link rel="stylesheet" href="style.css">
<style>
  body { background: #f4f7fa; font-family: 'Poppins', sans-serif; }
  .container { max-width: 700px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
  input, textarea, select { width: 100%; padding: 10px; margin: 8px 0; border-radius: 8px; border: 1px solid #ccc; }
  .btn { background: #007BFF; color: white; border: none; padding: 10px 18px; border-radius: 6px; cursor: pointer; text-decoration: none; }
  .btn:hover { opacity: 0.9; }
  .alert { margin-bottom: 15px; padding: 10px; border-radius: 6px; }
  .alert.success { background: #d4edda; color: #155724; }
  .alert.error { background: #f8d7da; color: #721c24; }
</style>
</head>
<body style="background-image: url('Voting.png'); 
             background-size: cover; 
             background-repeat: no-repeat; 
             background-position: center center; 
             min-height: 100vh;
             margin: 0;">



<header><h1>Edit Election</h1></header>

<nav>
  <a href="admin_dashboard.php">Dashboard</a>
  <a href="create_election.php" class="active">Elections</a>
  <a href="admin_logout.php">Logout</a>
</nav>

<div class="container">
  <h2>Update Election Details</h2>
  <?php if (isset($msg)): ?>
    <div class="alert <?php echo (strpos($msg,'✅')!==false)?'success':'error'; ?>"><?php echo $msg; ?></div>
  <?php endif; ?>

  <form method="post">
    <label>Election Name:</label>
    <input type="text" name="name" value="<?php echo htmlspecialchars($election['name']); ?>" required>
    
    <label>Start Date & Time:</label>
    <input type="datetime-local" name="start_date" value="<?php echo date('Y-m-d\TH:i', strtotime($election['start_date'])); ?>" required>
    
    <label>End Date & Time:</label>
    <input type="datetime-local" name="end_date" value="<?php echo date('Y-m-d\TH:i', strtotime($election['end_date'])); ?>" required>

    <button type="submit" name="update" class="btn">Update</button>
    <a href="create_election.php" class="btn" style="background:#6c757d;">Cancel</a>
  </form>
</div>

<footer>
  <p>© 2025 E-Voting System | Admin Panel</p>
</footer>

</body>
</html>
