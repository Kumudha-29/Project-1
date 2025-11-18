<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['voter_id'])) {
    header("Location: voter_login.php");
    exit();
}

$voter_id = (int)$_SESSION['voter_id'];
$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Create or Update Complaint
    if (isset($_POST['title'], $_POST['description'], $_POST['election_id'])) {
        $title = trim($_POST['title']);
        $desc  = trim($_POST['description']);
        $election_id = (int)$_POST['election_id'];

        if (!empty($_POST['complaint_id'])) {
            // Update existing complaint
            $complaint_id = (int)$_POST['complaint_id'];
            $stmt = $conn->prepare("UPDATE complaints SET title=?, description=? WHERE id=? AND voter_id=?");
            $stmt->bind_param("ssii", $title, $desc, $complaint_id, $voter_id);
            $stmt->execute();
            $stmt->close();
            $message = "✅ Complaint updated successfully!";
        } else {
            // Create new complaint
            $stmt = $conn->prepare("INSERT INTO complaints (voter_id, election_id, title, description, status, created_at) VALUES (?, ?, ?, ?, 'Pending', NOW())");
            $stmt->bind_param("iiss", $voter_id, $election_id, $title, $desc);
            $stmt->execute();
            $stmt->close();
            $message = "✅ Complaint submitted successfully!";
        }
    }

    // Delete complaint
    if (isset($_POST['delete_id'])) {
        $delete_id = (int)$_POST['delete_id'];
        $stmt = $conn->prepare("DELETE FROM complaints WHERE id=? AND voter_id=?");
        $stmt->bind_param("ii", $delete_id, $voter_id);
        $stmt->execute();
        $stmt->close();
        $message = "✅ Complaint deleted successfully!";
    }
}

// Fetch elections for complaint creation
$elections = $conn->query("SELECT * FROM elections ORDER BY start_date ASC");

// Fetch voter complaints
$complaints = $conn->query("SELECT c.*, e.name AS election_name FROM complaints c JOIN elections e ON c.election_id=e.id WHERE c.voter_id=$voter_id ORDER BY c.created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>My Complaints</title>
<link rel="stylesheet" href="style.css">
<style>
/* Page & container */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #eef2f7;
    margin: 0;
    padding: 0;
}
.container {
    max-width: 1000px;
    margin: 50px auto;
    padding: 30px;
    background: rgba(255,255,255,0.98);
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
}
h1, h2 {
    color: #2c3e50;
    text-align: center;
}

/* Alerts */
.alert {
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-weight: bold;
    text-align: center;
}
.alert.success { background:#d4edda; color:#155724; }
.alert.error { background:#f8d7da; color:#721c24; }

/* Form */
form.complaint-form {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 30px;
    justify-content: space-between;
}
form.complaint-form select,
form.complaint-form input,
form.complaint-form textarea {
    flex: 1 1 48%;
    padding: 12px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 14px;
}
form.complaint-form textarea { flex: 1 1 100%; resize: vertical; min-height: 110px; }
form.complaint-form button {
    background: #007BFF;
    color: white;
    padding: 12px 20px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 16px;
    align-self: flex-end;
}
form.complaint-form button:hover { background: #0056b3; }

/* Table */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}
th, td {
    padding: 12px 10px;
    border-bottom: 1px solid #eee;
    text-align: left;
    vertical-align: middle;
}
th {
    background: #007BFF;
    color: white;
    text-align: left;
}

/* Ensure description wraps and is readable */
td.description {
    max-width: 420px;
    white-space: normal;
    word-wrap: break-word;
    word-break: break-word;
}

/* Actions column fixed width and no wrap so links don't stack or get clipped */
th.actions, td.actions {
    width: 160px;
    white-space: nowrap;
    text-align: center;
}

/* Action link styles */
td a {
    display: inline-block;
    margin: 0 6px;
    cursor: pointer;
    text-decoration: none;
    font-size: 14px;
    padding: 4px 6px;
    border-radius: 4px;
}
td a.edit { color: #155724; background: rgba(40,167,69,0.08); border: 1px solid rgba(40,167,69,0.18); }
td a.delete { color: #721c24; background: rgba(220,53,69,0.06); border: 1px solid rgba(220,53,69,0.12); }
td a:hover { text-decoration: none; opacity: 0.9; }

/* Small screens */
@media(max-width: 768px){
    form.complaint-form select, form.complaint-form input, form.complaint-form textarea { flex: 1 1 100%; }
    th.actions, td.actions { width: auto; white-space: normal; }
}
</style>
</head>
<body style="background-image: url('Voting.png'); 
             background-size: cover; 
             background-repeat: no-repeat; 
             background-position: center center; 
             min-height: 100vh;
             margin: 0;">

<div class="container">
<h1>My Complaints</h1>

<?php if($message): ?>
<div class="alert <?php echo strpos($message,'✅')!==false ? 'success' : 'error'; ?>"><?php echo $message; ?></div>
<?php endif; ?>

<h2>Submit / Edit Complaint</h2>
<form method="post" class="complaint-form">
    <input type="hidden" name="complaint_id" id="complaint_id">
    <select name="election_id" id="election_id" required>
        <option value="">-- Select Election --</option>
        <?php 
        // rewind result pointer in case it's been used earlier
        $elections->data_seek(0);
        while($e = $elections->fetch_assoc()): ?>
            <option value="<?php echo $e['id']; ?>"><?php echo htmlspecialchars($e['name']); ?></option>
        <?php endwhile; ?>
    </select>

    <input type="text" name="title" id="title" placeholder="Complaint Title" required>
    <textarea name="description" id="description" placeholder="Complaint Description" required></textarea>

    <button type="submit">Submit Complaint</button>
</form>

<h2>My Submitted Complaints</h2>
<table>
<tr>
<th>Election</th>
<th>Title</th>
<th class="description">Description</th>
<th>Status</th>
<th class="actions">Actions</th>
</tr>

<?php while($c = $complaints->fetch_assoc()): ?>
<tr>
<td><?php echo htmlspecialchars($c['election_name']); ?></td>
<td><?php echo htmlspecialchars($c['title']); ?></td>
<td class="description"><?php echo nl2br(htmlspecialchars($c['description'])); ?></td>
<td><?php echo htmlspecialchars($c['status']); ?></td>
<td class="actions">
    <!-- Edit uses JS to populate the form -->
    <a href="#" class="edit" onclick='editComplaint(<?php echo $c['id']; ?>, <?php echo json_encode($c['title']); ?>, <?php echo json_encode($c['description']); ?>, <?php echo (int)$c['election_id']; ?>); return false;'>Edit</a>

    <!-- Delete submits a unique inline form (no reliance on nextElementSibling) -->
    <a href="#" class="delete" onclick="if(confirm('Are you sure you want to delete this complaint?')) { document.getElementById('delForm<?php echo $c['id']; ?>').submit(); } return false;">Delete</a>
    <form method="post" id="delForm<?php echo $c['id']; ?>" style="display:inline;margin:0;padding:0;">
        <input type="hidden" name="delete_id" value="<?php echo $c['id']; ?>">
    </form>
</td>
</tr>
<?php endwhile; ?>
</table>
</div>

<script>
function editComplaint(id, title, desc, election_id){
    document.getElementById('complaint_id').value = id;
    document.getElementById('title').value = title;
    document.getElementById('description').value = desc;
    document.getElementById('election_id').value = election_id;
    window.scrollTo({top: 0, behavior: 'smooth'});
}
</script>

</body>
</html>
