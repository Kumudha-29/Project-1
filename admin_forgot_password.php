<?php
session_start();
include('db_connect.php');

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $stmt = $conn->prepare("SELECT id FROM admin WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $row = $res->fetch_assoc();
        $token = bin2hex(random_bytes(16));
        $now = date('Y-m-d H:i:s');

        $update = $conn->prepare("UPDATE admin SET password_reset_token=?, token_created_at=? WHERE id=?");
        $update->bind_param("ssi", $token, $now, $row['id']);
        $update->execute();

        $resetLink = "http://localhost/evoting/admin_reset_password.php?token=$token";
        $message = "✅ Password reset link (simulated): <a href='$resetLink'>$resetLink</a>";
    } else {
        $message = "⚠️ Username not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Forgot Password</title>
<link rel="stylesheet" href="style.css">
</head>
<body style="background-image: url('Voting.png'); 
             background-size: cover; 
             background-repeat: no-repeat; 
             background-position: center center; 
             min-height: 100vh;
             margin: 0;">

<div class="container">
<h2>Admin Forgot Password</h2>
<form method="post">
    <input type="text" name="username" placeholder="Enter your username" required>
    <button type="submit">Send Reset Link</button>
</form>
<?php if ($message) echo "<p>$message</p>"; ?>
<p><a href="admin_login.php">Back to Login</a></p>
</div>
</body>
</html>
