<?php
include('db_connect.php');
$message = '';

if (isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Proceed only if the password is valid — PHP-side check too (for safety)
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
        $message = "Password must include uppercase, lowercase, number, special character, and be at least 8 characters.";
    } elseif ($name == '' || $email == '' || $password == '') {
        $message = "All fields are required.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM voters WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = "Email already registered.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $ins = $conn->prepare("INSERT INTO voters (name, email, password) VALUES (?, ?, ?)");
            $ins->bind_param("sss", $name, $email, $hash);

            if ($ins->execute()) {
                $message = "Registered successfully! You can now login.";
            } else {
                $message = "Error: " . $conn->error;
            }
            $ins->close();
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Voter Register</title>
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
  margin: 80px auto;
  background: rgba(255, 255, 255, 0.95);
  padding: 25px;
  border-radius: 12px;
  box-shadow: 0 0 10px rgba(0,0,0,0.2);
}

h2 {
  text-align: center;
  color: #003399;
}

input[type="text"], input[type="email"], input[type="password"] {
  width: 90%;
  padding: 10px;
  margin: 8px 0;
  border: 1px solid #aaa;
  border-radius: 5px;
}

input[type="submit"] {
  background-color: #003399;
  color: white;
  padding: 10px;
  border: none;
  width: 100%;
  border-radius: 5px;
  cursor: pointer;
}

input[type="submit"]:hover {
  background-color: #0055cc;
}

.alert {
  text-align: center;
  padding: 10px;
  border-radius: 5px;
  margin-bottom: 10px;
}

.alert.error { background-color: #ffcccc; color: #b30000; }
.alert.success { background-color: #ccffcc; color: #006600; }

#passwordError {
  color: red;
  font-size: 14px;
  text-align: center;
  display: none;
}
</style>
</head>

<body>
<header><h1 style="text-align:center;">Voter Registration</h1></header>
<nav style="text-align:center;">
  <a href="index.php">Home</a> |
  <a href="voter_login.php">Voter Login</a>
</nav>

<div class="container">
  <form method="post" id="registerForm">
    <h2>Register as Voter</h2>

    <!-- Show PHP message only if password is valid and form actually submitted -->
    <?php if ($message && strpos($message, 'Password') === false): ?>
      <div class="alert <?php echo (strpos($message, 'Registered') !== false ? 'success' : 'error'); ?>">
        <?php echo $message; ?>
      </div>
    <?php endif; ?>

    <input type="text" name="name" id="name" placeholder="Full name" required>
    <input type="email" name="email" id="email" placeholder="Email address" required>
    <input type="password" name="password" id="password" placeholder="Choose a Password" required>
    <div id="passwordError">Password must include uppercase, lowercase, number, special character, and be at least 8 characters.</div>
    <input type="submit" name="register" value="Register">
    <p class="form-link" style="text-align:center;">
      Already have an account?
      <a href="voter_login.php">Login here</a>
    </p>
  </form>
</div>

<script>
// JavaScript validation — prevents reload if password is invalid
document.getElementById('registerForm').addEventListener('submit', function(event) {
  const password = document.getElementById('password').value;
  const passwordError = document.getElementById('passwordError');
  const pattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;

  if (!pattern.test(password)) {
    event.preventDefault();
    passwordError.style.display = 'block';
    document.getElementById('password').value = ''; // clear only password box
  } else {
    passwordError.style.display = 'none';
  }
});
</script>
</body>
</html>
