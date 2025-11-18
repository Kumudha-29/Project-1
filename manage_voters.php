<?php
include('db_connect.php');
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Delete voter if requested
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $conn->query("DELETE FROM voters WHERE id='$id'");
    header("Location: manage_voters.php?msg=Voter+deleted+successfully");
    exit();
}

$result = $conn->query("SELECT * FROM voters");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Voters</title>
<link rel="stylesheet" href="style.css">
<style>
.container {
  background: #fff;
  width: 80%;
  max-width: 800px;
  margin: 50px auto;
  padding: 30px 40px;
  border-radius: 12px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
h2 {
  text-align: center;
  color: #2c3e50;
  margin-bottom: 25px;
}
table {
  width: 100%;
  border-collapse: collapse;
}
th, td {
  padding: 12px;
  text-align: center;
  border-bottom: 1px solid #ddd;
}
th {
  background: #2c7be5;
  color: white;
}
a {
  text-decoration: none;
}
.action-links a {
  margin: 0 8px;
  font-weight: 500;
}
.action-links a.edit {
  color: #007bff;
}
.action-links a.delete {
  color: red;
}
.action-links a:hover {
  text-decoration: underline;
}
.alert {
  text-align: center;
  background: #d4edda;
  color: #155724;
  padding: 10px;
  border-radius: 6px;
  margin-bottom: 15px;
}
</style>
</head>
<body style="background-image: url('Voting.png'); 
             background-size: cover; 
             background-repeat: no-repeat; 
             background-position: center center; 
             min-height: 100vh;
             margin: 0;">


<header><h1>Manage Voters</h1></header>

<nav>
  <a href="admin_dashboard.php">Dashboard</a>
  <a href="manage_candidates.php">Candidates</a>
  <a href="manage_voters.php" class="active">Voters</a>
  <a href="results.php">Results</a>
  <a href="admin_logout.php">Logout</a>
</nav>

<div class="container">
  <?php if (isset($_GET['msg'])): ?>
    <div class="alert"><?php echo htmlspecialchars($_GET['msg']); ?></div>
  <?php endif; ?>

  <h2>Registered Voters</h2>
  <table>
    <tr>
      <th>ID</th>
      <th>Name</th>
      <th>Email</th>
      <th>Action</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
      <td><?php echo $row['id']; ?></td>
      <td><?php echo htmlspecialchars($row['name']); ?></td>
      <td><?php echo htmlspecialchars($row['email']); ?></td>
      <td class="action-links">
        <a href="edit_voter.php?id=<?php echo $row['id']; ?>" class="edit">Edit</a>
        <a href="manage_voters.php?delete_id=<?php echo $row['id']; ?>" class="delete" onclick="return confirm('Delete this voter?')">Delete</a>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>

<footer>
  <p>© 2025 E-Voting System | Admin Panel</p>
</footer>
</body>
</html>
