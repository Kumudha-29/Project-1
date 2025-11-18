<?php
include 'db_connect.php';

if (!isset($_GET['id'])) {
    die("Candidate ID not provided!");
}

$id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM candidates WHERE id = $id");
$candidate = $result->fetch_assoc();

if (!$candidate) {
    die("Candidate not found!");
}

if (isset($_POST['update'])) {
    $name = trim($_POST['name']);
    $party = trim($_POST['party']);
    $election_id = intval($_POST['election_id']);

    $stmt = $conn->prepare("UPDATE candidates SET name=?, party=?, election_id=? WHERE id=?");
    $stmt->bind_param("ssii", $name, $party, $election_id, $id);

    if ($stmt->execute()) {
        echo "<script>alert('✅ Candidate updated successfully!'); window.location='manage_candidates.php';</script>";
    } else {
        echo "<div class='alert error'>❌ Error updating candidate: " . $conn->error . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Candidate</title>
<link rel="stylesheet" href="style.css">

<style>
    body {
        font-family: 'Poppins', sans-serif;
        background: #f4f7fa;
        margin: 0;
        padding: 0;
    }

    header {
        background: #007bff;
        color: white;
        text-align: center;
        padding: 15px;
        font-size: 1.5rem;
        letter-spacing: 0.5px;
    }

    .container {
        max-width: 600px;
        margin: 40px auto;
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 0 12px rgba(0, 0, 0, 0.1);
    }

    h2 {
        text-align: center;
        color: #333;
        margin-bottom: 20px;
    }

    label {
        display: block;
        font-weight: 500;
        margin-bottom: 6px;
        color: #333;
    }

    input[type="text"], select {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 8px;
        margin-bottom: 15px;
        font-size: 15px;
    }

    button {
        background: #007bff;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        font-size: 15px;
        cursor: pointer;
        width: 100%;
    }

    button:hover {
        background: #0056b3;
    }

    .back-btn {
        display: block;
        text-align: center;
        margin-top: 15px;
        color: #007bff;
        text-decoration: none;
        font-weight: 500;
    }

    .back-btn:hover {
        text-decoration: underline;
    }

    .alert {
        margin-bottom: 15px;
        padding: 10px;
        border-radius: 6px;
    }

    .alert.error {
        background: #f8d7da;
        color: #721c24;
    }
</style>
</head>
<body style="background-image: url('Voting.png'); 
             background-size: cover; 
             background-repeat: no-repeat; 
             background-position: center center; 
             min-height: 100vh;
             margin: 0;">

<header>Edit Candidate</header>

<div class="container">
    <h2>Update Candidate Details</h2>

    <form method="POST">
        <label>Name:</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($candidate['name']); ?>" required>

        <label>Party:</label>
        <input type="text" name="party" value="<?php echo htmlspecialchars($candidate['party']); ?>" required>

        <label>Election:</label>
        <select name="election_id" required>
            <?php
            $elections = $conn->query("SELECT id, name FROM elections");
            while ($row = $elections->fetch_assoc()) {
                $selected = ($row['id'] == $candidate['election_id']) ? 'selected' : '';
                echo "<option value='{$row['id']}' $selected>{$row['name']}</option>";
            }
            ?>
        </select>

        <button type="submit" name="update">Update Candidate</button>
    </form>

    <a href="manage_candidates.php" class="back-btn">← Back to Candidate List</a>
</div>

</body>
</html>
