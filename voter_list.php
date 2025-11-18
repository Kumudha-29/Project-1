<?php
session_start();
if (!isset($_SESSION['admin'])) { header('Location: admin_login.php'); exit(); }
include('db_connect.php');

if (isset($_GET['delete'])) {
    $del = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM voters WHERE id = ?");
    $stmt->bind_param("i", $del);
    $stmt->execute();
    $stmt->close();
    header("Location: voter_list.php");
    exit();
}

$voters = $conn->query("SELECT * FROM voters ORDER BY id ASC");
?>
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Voter List</title><link rel="stylesheet" href="style.css"></head>
<body style="background-image: url('Voting.png'); 
             background-size: cover; 
             background-repeat: no-repeat; 
             background-position: center center; 
             min-height: 100vh;
             margin: 0;">


<header><h1>Registered Voters</h1></header>
<nav><a href="admin_dashboard.php">Dashboard</a> <a href="candidate_form.php">Candidates</a> <a href="results.php">Results</a> <a href="logout.php">Logout</a></nav>

<div class="container">
  <h3>Voters</h3>
  <table>
    <tr><th>ID</th><th>Name</th><th>Email</th><th>Voted</th><th>Actions</th></tr>
    <?php while($v = $voters->fetch_assoc()): ?>
    <tr>
      <td><?php echo $v['id']; ?></td>
      <td><?php echo htmlspecialchars($v['name']); ?></td>
      <td><?php echo htmlspecialchars($v['email']); ?></td>
      <td><?php echo $v['voted'] ? 'Yes' : 'No'; ?></td>
      <td>
        <a href="voter_list.php?delete=<?php echo $v['id']; ?>" onclick="return confirm('Delete this voter?');">Delete</a>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>
</body>
</html>
