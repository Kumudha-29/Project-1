<?php
include 'db_connect.php';
date_default_timezone_set('Asia/Kolkata'); // set your timezone

// ADD new election
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create'])) {
    $name = $_POST['name'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Get current date/time
    $now = date('Y-m-d\TH:i');

    // ✅ SERVER-SIDE VALIDATION: Prevent past dates
    if ($start_date < $now) {
        echo "<script>alert('Start date cannot be in the past!'); window.history.back();</script>";
        exit;
    }
    if ($end_date < $start_date) {
        echo "<script>alert('End date cannot be earlier than start date!'); window.history.back();</script>";
        exit;
    }

    // Automatically set initial status
    $current = date('Y-m-d H:i:s');
    if ($current < $start_date) {
        $status = "Upcoming";
    } elseif ($current >= $start_date && $current <= $end_date) {
        $status = "Ongoing";
    } else {
        $status = "Completed";
    }

    $stmt = $conn->prepare("INSERT INTO elections (name, start_date, end_date, status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $start_date, $end_date, $status);
    $stmt->execute();
    $stmt->close();

    header("Location: create_election.php");
    exit;
}

// DELETE election
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM elections WHERE id=$id");
    header("Location: create_election.php");
    exit;
}

// UPDATE election (Edit)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    $conn->query("UPDATE elections SET name='$name', start_date='$start_date', end_date='$end_date' WHERE id=$id");
    header("Location: create_election.php");
    exit;
}

// Auto-update statuses dynamically
$now = date('Y-m-d H:i:s');
$conn->query("UPDATE elections SET status='Upcoming' WHERE start_date > '$now'");
$conn->query("UPDATE elections SET status='Ongoing' WHERE start_date <= '$now' AND end_date >= '$now'");
$conn->query("UPDATE elections SET status='Completed' WHERE end_date < '$now'");

// Fetch all elections
$result = $conn->query("SELECT * FROM elections ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Election</title>
    <meta http-equiv="refresh" content="30"> <!-- auto-refresh every 30s -->
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f7fb;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 90%;
            max-width: 900px;
            margin: 50px auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0px 0px 15px rgba(0,0,0,0.1);
            padding: 25px;
        }
        h1 {
            text-align: center;
            color: #0d3b66;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 30px;
        }
        input {
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover { background-color: #0056b3; }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #ccc;
        }
        th {
            background-color: #0d3b66;
            color: white;
        }
        .btn-edit {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
        }
        .btn-delete {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
        }
        .btn-edit:hover { background-color: #218838; }
        .btn-delete:hover { background-color: #c82333; }
        .btn-back {
            display: block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
        }
        .btn-back:hover {
            background-color: #0056b3;
        }
        .status-upcoming { color: #007bff; font-weight: bold; }
        .status-ongoing { color: #28a745; font-weight: bold; }
        .status-completed { color: #6c757d; font-weight: bold; }
    </style>
</head>
<body style="background-image: url('Voting.png'); 
             background-size: cover; 
             background-repeat: no-repeat; 
             background-position: center center; 
             min-height: 100vh;
             margin: 0;">

    <div class="container">
        <h1>Create New Election</h1>
        <form method="POST">
            <input type="text" name="name" placeholder="Election Name" required>
            
            <label>Start Date & Time:</label>
            <input type="datetime-local" name="start_date" 
                   min="<?php echo date('Y-m-d\TH:i'); ?>" required>
            
            <label>End Date & Time:</label>
            <input type="datetime-local" name="end_date" 
                   min="<?php echo date('Y-m-d\TH:i'); ?>" required>
            
            <button type="submit" name="create">Create Election</button>
        </form>

        <h2 style="text-align:center; color:#0d3b66;">Existing Elections</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Election Name</th>
                <th>Start</th>
                <th>End</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= date("d M Y, h:i A", strtotime($row['start_date'])) ?></td>
                    <td><?= date("d M Y, h:i A", strtotime($row['end_date'])) ?></td>
                    <td class="status-<?= strtolower($row['status']) ?>"><?= $row['status'] ?></td>
                    <td>
                        <a href="edit_election.php?id=<?= $row['id'] ?>" class="btn-edit">Edit</a>
                        <a href="create_election.php?delete=<?= $row['id'] ?>" class="btn-delete" onclick="return confirm('Delete this election?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

        <a href="admin_dashboard.php" class="btn-back">⬅ Back to Dashboard</a>
    </div>
</body>
</html>
