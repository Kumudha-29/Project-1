<?php
include('db_connect.php');
session_start();
$message = '';

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $hash);
        $stmt->fetch();
        if (password_verify($password, $hash)) {
            session_regenerate_id(true);
            $_SESSION['admin'] = $username;
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $message = "Invalid credentials.";
        }
    } else {
        $message = "Invalid credentials.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Admin Login</title><link rel="stylesheet" href="style.css"></head>
<body style="background-image: url('Voting.png'); 
             background-size: cover; 
             background-repeat: no-repeat; 
             background-position: center center; 
             min-height: 100vh;
             margin: 0;">

<header><h1>Admin Login</h1></header>
<nav><a href="index.php">Home</a></nav>
<div class="container">
  <form method="post">
    <h2>Admin Sign In</h2>
    <?php if($message): ?><div class="alert error"><?php echo $message; ?></div><?php endif; ?>
    <input type="text" name="username" placeholder="Username" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <input type="submit" name="login" value="Login">

<!-- Forgot Password Link -->
<p style="margin-top:10px;">
   <a href="admin_forgot_password.php">Forgot Password?</a>
</p>
  </form>
</div>
</body>
</html>
