<?php
include('db_connect.php');
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle messages from release_results.php
$msg = isset($_GET['msg']) ? $_GET['msg'] : '';

// Fetch stats
$totalVoters = $conn->query("SELECT COUNT(*) AS c FROM voters")->fetch_assoc()['c'];
$totalCandidates = $conn->query("SELECT COUNT(*) AS c FROM candidates")->fetch_assoc()['c'];
$totalVotes = $conn->query("SELECT COUNT(*) AS c FROM votes")->fetch_assoc()['c'];

// Fetch all elections
$elections = $conn->query("SELECT * FROM elections ORDER BY start_date ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard</title>
<link rel="stylesheet" href="style.css">
<style>
.container { max-width: 1000px; margin: 30px auto; font-family: Arial, sans-serif; }
.dashboard-cards { display: flex; gap: 20px; justify-content: center; margin-bottom: 30px; }
.card { padding: 20px; border-radius: 8px; color: #fff; width: 180px; text-align: center; }
.card h3 { font-size: 2em; margin: 0; }
.card.blue { background: #007bff; }
.card.green { background: #28a745; }
.card.orange { background: #fd7e14; }

.alert.success { background: #d4edda; color: #155724; padding: 15px; border-radius: 6px; margin-bottom: 20px; text-align: center; }

.elections-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
.elections-table th, .elections-table td { border: 1px solid #ddd; padding: 10px; text-align: center; }
.elections-table th { background: #007bff; color: #fff; }

.btn { padding: 6px 12px; border-radius: 5px; text-decoration: none; color: #fff; }
.btn.green { background: #28a745; }
.btn.green:hover { background: #218838; }
.btn.orange { background: #fd7e14; }
.btn.orange:hover { background: #e8590c; }

h2 { text-align: center; margin-bottom: 15px; }

nav {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}
nav a {
    padding: 8px 15px;
    border-radius: 5px;
    background: #007bff;
    color: #fff;
    text-decoration: none;
}
nav a.active, nav a:hover {
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


<header>
    <h1 style="text-align:center;">Admin Dashboard</h1>
</header>

<nav>
    <a href="admin_dashboard.php" class="active">Dashboard</a>
    <a href="manage_candidates.php">Candidates</a>
    <a href="manage_voters.php">Voters</a>
    <a href="create_election.php">Create Election</a>
    <a href="complaints_admin.php">Manage Complaints</a>
    <a href="results.php">Results</a>
    <a href="admin_logout.php">Logout</a>
</nav>

<div class="container">

<?php if($msg): ?>
    <div class="alert success"><?php echo htmlspecialchars($msg); ?></div>
<?php endif; ?>

<h2>Quick Statistics</h2>
<div class="dashboard-cards">
    <div class="card blue">
        <h3><?php echo $totalVoters; ?></h3>
        <p>Total Voters</p>
    </div>
    <div class="card green">
        <h3><?php echo $totalCandidates; ?></h3>
        <p>Total Candidates</p>
    </div>
    <div class="card orange">
        <h3><?php echo $totalVotes; ?></h3>
        <p>Total Votes</p>
    </div>
</div>

<h2>Manage Election Results</h2>
<table class="elections-table">
<tr>
    <th>Election Name</th>
    <th>Status</th>
    <th>Results Released</th>
    <th>Action</th>
</tr>
<?php while($row = $elections->fetch_assoc()): ?>
<tr>
    <td><?php echo htmlspecialchars($row['name']); ?></td>
    <td><?php echo $row['status']; ?></td>
    <td><?php echo $row['results_released'] == '1' ? 'Yes' : 'No'; ?></td>
    <td>
        <?php if($row['results_released'] == '0'): ?>
            <a class="btn green" href="release_results.php?election_id=<?php echo $row['id']; ?>">Release</a>
        <?php else: ?>
            <a class="btn orange" href="release_results.php?election_id=<?php echo $row['id']; ?>&hide=1">Hide</a>
        <?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>
</table>

</div>

<footer>
    <p style="text-align:center;">© 2025 E-Voting System | Admin Panel</p>
</footer>
</body>
</html>
