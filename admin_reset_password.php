<?php
include('db_connect.php');
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Reset Password</title>
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
  max-width: 400px;
  background: rgba(255,255,255,0.9);
  padding: 20px;
  border-radius: 12px;
  margin: 80px auto;
  text-align: center;
  box-shadow: 0 0 10px rgba(0,0,0,0.2);
}
input {
  width: 90%;
  padding: 10px;
  margin: 10px 0;
  border-radius: 5px;
  border: 1px solid #ccc;
}
button {
  background: #007bff;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 5px;
  cursor: pointer;
}
button:hover { background: #0056b3; }
#error {
  color: red;
  margin-bottom: 10px;
}
#success {
  color: green;
  margin-bottom: 10px;
}
</style>
</head>
<body>
<div class="container">
<h2>Admin Reset Password</h2>
<div id="error"></div>
<div id="success"></div>

<form id="resetForm" method="POST">
  <input type="text" name="username" id="username" placeholder="Enter admin username" required><br>
  <input type="password" name="new_password" id="new_password" placeholder="Enter new password" required><br>
  <button type="submit">Update Password</button>
</form>
</div>

<script>
document.getElementById('resetForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const username = document.getElementById('username').value.trim();
  const password = document.getElementById('new_password').value.trim();
  const errorDiv = document.getElementById('error');
  const successDiv = document.getElementById('success');
  errorDiv.textContent = '';
  successDiv.textContent = '';

  // Password validation
  const strongPassword = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
  if (!strongPassword.test(password)) {
    errorDiv.textContent = 'Password must be at least 8 characters long, include uppercase, lowercase, number, and special character.';
    return;
  }

  // Send data to same page (AJAX)
  const formData = new FormData();
  formData.append('username', username);
  formData.append('new_password', password);
  formData.append('action', 'update');

  const response = await fetch('', { method: 'POST', body: formData });
  const result = await response.text();

  if (result.includes('success')) {
    successDiv.textContent = '✅ Password updated successfully! Redirecting to login...';
    setTimeout(() => window.location.href = 'admin_login.php', 2000);
  } else {
    errorDiv.textContent = result;
  }
});
</script>

<?php
// Handle backend update if AJAX POST is received
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $username = trim($_POST['username']);
    $newPassword = $_POST['new_password'];

    $stmt = $conn->prepare("SELECT id FROM admin WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE admin SET password=? WHERE username=?");
        $update->bind_param("ss", $hash, $username);
        if ($update->execute()) {
            echo "success";
        } else {
            echo "Error updating password.";
        }
        $update->close();
    } else {
        echo "Username not found.";
    }
    exit;
}
?>
</body>
</html>
