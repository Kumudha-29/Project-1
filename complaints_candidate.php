<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['candidate_id'])) {
    header("Location: candidate_login.php");
    exit();
}

$candidate_id = (int)$_SESSION['candidate_id'];
$message = '';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['complaint_id'], $_POST['status'])) {
    $id = (int)$_POST['complaint_id'];
    $status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE complaints SET status=? WHERE id=? AND candidate_id=?");
    $stmt->bind_param("sii", $status, $id, $candidate_id);
    $stmt->execute();
    $stmt->close();
    $message = "Complaint status updated.";
}

// Fetch complaints assigned to candidate or related election
$complaints = $conn->query("
    SELECT c.*, v.name AS voter_name, e.name AS election_name
    FROM complaints c
    JOIN voters v ON c.voter_id=v.id
    JOIN elections e ON c.election_id=e.id
    WHERE c.candidate_id=$candidate_id OR c.election_id IN (SELECT election_id FROM candidates WHERE id=$candidate_id)
    ORDER BY c.created_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Candidate Complaints</title>
<link rel="stylesheet" href="style.css">
</head>
<body style="background-image: url('Voting.png'); 
             background-size: cover; 
             background-repeat: no-repeat; 
             background-position: center center; 
             min-height: 100vh;
             margin: 0;">

<header><h1>Complaints</h1></header>

<div class="container">
<?php if($message): ?>
<div class="alert success"><?php echo $message; ?></div>
<?php endif; ?>

<table>
<tr>
<th>Voter</th>
<th>Title</th>
<th>Description</th>
<th>Election</th>
<th>Status</th>
<th>Action</th>
</tr>

<?php while($c=$complaints->fetch_assoc()): ?>
<tr>
    <td><?php echo htmlspecialchars($c['voter_name']); ?></td>
    <td><?php echo htmlspecialchars($c['title']); ?></td>
    <td><?php echo htmlspecialchars($c['description']); ?></td>
    <td><?php echo htmlspecialchars($c['election_name']); ?></td>
    <td><?php echo $c['status']; ?></td>
    <td>
        <form method="post">
            <input type="hidden" name="complaint_id" value="<?php echo $c['id']; ?>">
            <select name="status">
                <option value="Pending" <?php if($c['status']=='Pending') echo 'selected'; ?>>Pending</option>
                <option value="In Progress" <?php if($c['status']=='In Progress') echo 'selected'; ?>>In Progress</option>
                <option value="Resolved" <?php if($c['status']=='Resolved') echo 'selected'; ?>>Resolved</option>
            </select>
            <button type="submit">Update</button>
        </form>
    </td>
</tr>
<?php endwhile; ?>
</table>
</div>
</body>
</html>
