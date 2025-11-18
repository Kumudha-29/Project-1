<?php
include('db_connect.php');
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle status updates
if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = (int)$_GET['id'];
    $allowedStatuses = ['Pending', 'In Progress', 'Resolved'];
    $status = $_GET['status'];

    if (in_array($status, $allowedStatuses)) {
        $stmt = $conn->prepare("UPDATE complaints SET status=? WHERE id=?");
        $stmt->bind_param("si", $status, $id);
        $stmt->execute();
    }

    header("Location: complaints_admin.php");
    exit();
}

// Handle delete
if (isset($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM complaints WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: complaints_admin.php");
    exit();
}

// Fetch complaints with voter info
$complaints = $conn->query("
    SELECT c.id, c.description, c.status, v.name AS voter_name, v.email AS voter_email
    FROM complaints c
    JOIN voters v ON c.voter_id = v.id
    ORDER BY c.id DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Complaints - Admin</title>
<link rel="stylesheet" href="style.css">
<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1 { text-align: center; margin-bottom: 20px; }
table { width: 100%; border-collapse: collapse; margin-top: 20px; }
th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
th { background: #007BFF; color: white; }
a.action-link { text-decoration: none; color: #007BFF; margin: 0 5px; }
a.action-link:hover { text-decoration: underline; color: #0056b3; }
</style>
</head>
<body style="background-image: url('Voting.png'); 
             background-size: cover; 
             background-repeat: no-repeat; 
             background-position: center center; 
             min-height: 100vh;
             margin: 0;">

<h1>Manage Complaints</h1>

<table>
<tr>
    <th>ID</th>
    <th>Voter Name</th>
    <th>Email</th>
    <th>Complaint</th>
    <th>Status</th>
    <th>Actions</th>
</tr>

<?php while ($row = $complaints->fetch_assoc()): ?>
<tr>
    <td><?php echo $row['id']; ?></td>
    <td><?php echo htmlspecialchars($row['voter_name']); ?></td>
    <td><?php echo htmlspecialchars($row['voter_email']); ?></td>
    <td><?php echo htmlspecialchars($row['description']); ?></td>
    <td><?php echo $row['status']; ?></td>
    <td>
        <?php if ($row['status'] === 'Pending'): ?>
            <a class="action-link" href="?id=<?php echo $row['id']; ?>&status=In+Progress">Mark In Progress</a>
            <a class="action-link" href="?id=<?php echo $row['id']; ?>&status=Resolved">Mark Resolved</a>
        <?php elseif ($row['status'] === 'In Progress'): ?>
            <a class="action-link" href="?id=<?php echo $row['id']; ?>&status=Resolved">Mark Resolved</a>
        <?php endif; ?>
        <a class="action-link" href="?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this complaint?');">Delete</a>
    </td>
</tr>
<?php endwhile; ?>

</table>

</body>
</html>
