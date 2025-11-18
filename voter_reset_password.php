<?php
session_start();
include('db_connect.php');

$message = '';
$token = isset($_GET['token']) ? $_GET['token'] : '';

if (!$token) die("Invalid or missing token.");

$stmt = $conn->prepare("SELECT id FROM voters WHERE password_reset_token=?");
$stmt->bind_param("s", $token);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows !== 1) die("Invalid or expired token.");

$row = $res->fetch_assoc();
$voter_id = $row['id'];

$updated = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_pass = $_POST['new_password'];

    // Basic password strength check (optional)
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $new_pass)) {
        $message = "❌ Password must have uppercase, lowercase, number, special character, and at least 8 characters.";
    } else {
        $hash = password_hash($new_pass, PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE voters SET password=?, password_reset_token=NULL, token_created_at=NULL WHERE id=?");
        $update->bind_param("si", $hash, $voter_id);
        if ($update->execute()) {
            $updated = true;
            $message = "✅ Password updated successfully. Redirecting to login...";
        } else {
            $message = "❌ Error updating password. Please try again.";
        }
        $update->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Voter Reset Password</title>
<link rel="stylesheet" href="style.css">
<style>
body {
  background-image: url('Voting.png');
  background-size: cover;
  background-repeat: no-repeat;
  background-position: center center;
  min-height: 100vh;
  margin: 0;
  font-family: Arial, sans-serif;
}
.container {
  width: 350px;
  margin: 100px auto;
  background: rgba(255, 255, 255, 0.95);
  padding: 25px;
  border-radius: 12px;
  box-shadow: 0 0 10px rgba(0,0,0,0.2);
  text-align: center;
}
h2 { color: #003399; }
input[type="password"] {
  width: 90%;
  padding: 10px;
  margin: 8px 0;
  border: 1px solid #aaa;
  border-radius: 5px;
}
button {
  background-color: #003399;
  color: white;
  padding: 10px;
  border: none;
  width: 100%;
  border-radius: 5px;
  cursor: pointer;
}
button:hover { background-color: #0055cc; }
p { font-size: 15px; color: #333; }
.success { color: green; font-weight: bold; }
.error { color: red; font-weight: bold; }
</style>
</head>
<body>

<div class="container">
  <h2>Reset Voter Password</h2>

  <?php if (!$updated): ?>
  <form method="post">
      <input type="password" name="new_password" placeholder="Enter new password" required>
      <button type="submit">Update Password</button>
  </form>
  <?php endif; ?>

  <?php if ($message): ?>
    <p class="<?php echo $updated ? 'success' : 'error'; ?>"><?php echo $message; ?></p>
  <?php endif; ?>
</div>

<?php if ($updated): ?>
<script>
// Redirect to login page after 3 seconds
setTimeout(function() {
  window.location.href = "voter_login.php";
}, 3000);
</script>
<?php endif; ?>

</body>
</html>
