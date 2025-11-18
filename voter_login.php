<?php
include('db_connect.php');
session_start();
$message = '';
if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $stmt = $conn->prepare("SELECT id, password, voted, name FROM voters WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $hash, $voted, $name);
        $stmt->fetch();
        if (password_verify($password, $hash)) {
            session_regenerate_id(true);
            $_SESSION['voter_id'] = $id;
            $_SESSION['voter_name'] = $name;
            header("Location: voter_dashboard.php");
            exit();
        } else {
            $message = "Invalid email or password.";
        }
    } else {
        $message = "Invalid email or password.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Voter Login</title><link rel="stylesheet" href="style.css"></head>

    <body style="background-image: url('Voting.png'); 
             background-size: cover; 
             background-repeat: no-repeat; 
             background-position: center center; 
             min-height: 100vh;
             margin: 0;">


<header><h1>Voter Login</h1></header>
<nav><a href="index.php">Home</a></nav>
<div class="container">
  <form method="post">
    <h2>Login</h2>
    <?php if ($message): ?><div class="alert error"><?php echo $message; ?></div><?php endif; ?>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <input type="submit" name="login" value="Login">
    <p class="small">Don't have an account? <a href="voter_register.php">Register</a></p>
    <p style="margin-top:10px;">
   <a href="voter_forgot_password.php">Forgot Password?</a>
</p>
  </form>
</div>
</body>
</html>
