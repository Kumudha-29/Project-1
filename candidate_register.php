<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('db_connect.php');
session_start();

$message = '';
$redirect = false;

if (isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $party = trim($_POST['party']);
    $election_id = (int)$_POST['election_id'];

    if ($name && $party && $election_id) {
        // Check if candidate already exists for the same election
        $stmt = $conn->prepare("SELECT id FROM candidates WHERE name=? AND election_id=?");
        $stmt->bind_param("si", $name, $election_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = "This candidate is already registered for this election!";
        } else {
            $stmt->close();
            $stmt = $conn->prepare("INSERT INTO candidates (name, party, election_id) VALUES (?, ?, ?)");
            $stmt->bind_param("ssi", $name, $party, $election_id);
            if ($stmt->execute()) {
                $message = "✅ Registration successful! Redirecting to home page...";
                $redirect = true; // flag to redirect
            } else {
                $message = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
    } else {
        $message = "Please fill all fields!";
    }
}

// Fetch elections for dropdown
$elections = $conn->query("SELECT * FROM elections ORDER BY start_date ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Candidate Registration</title>
<link rel="stylesheet" href="style.css">
<?php if($redirect): ?>
    <!-- Redirect after 3 seconds -->
    <meta http-equiv="refresh" content="3;url=index.php">
<?php endif; ?>
</head>
<body style="background-image: url('Voting.png'); 
             background-size: cover; 
             background-repeat: no-repeat; 
             background-position: center center; 
             min-height: 100vh;
             margin: 0;">

<header><h1>Candidate Registration</h1></header>
<nav>
  <a href="index.php">Home</a>
</nav>

<div class="container">
<?php if($message): ?>
  <div class="alert success"><?php echo $message; ?></div>
<?php endif; ?>

<form method="post">
    <label>Full Name:</label><br>
    <input type="text" name="name" required><br><br>

    <label>Party:</label><br>
    <input type="text" name="party" required><br><br>

    <label>Election:</label><br>
    <select name="election_id" required>
        <option value="">--Select Election--</option>
        <?php while($row = $elections->fetch_assoc()): ?>
            <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['name']); ?></option>
        <?php endwhile; ?>
    </select><br><br>

    <input type="submit" name="register" value="Register">
</form>
</div>

<footer>
<p>© 2025 E-Voting System</p>
</footer>
</body>
</html>
