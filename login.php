<?php
include('db_connect.php');
session_start();
$message = '';

if (isset($_POST['login_type'])) {
    $login_type = $_POST['login_type'];

    // === ADMIN LOGIN ===
    if ($login_type == 'admin') {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        if ($username === 'admin' && $password === 'admin123') {
            $_SESSION['admin'] = 'Administrator';
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $message = "Invalid Admin credentials.";
        }
    }

    // === VOTER LOGIN ===
    if ($login_type == 'voter') {
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT id, name, password FROM voters WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $name, $hash);
            $stmt->fetch();
            if (password_verify($password, $hash)) {
                $_SESSION['voter'] = $name;
                header("Location: voter_dashboard.php");
                exit();
            } else {
                $message = "Invalid voter password.";
            }
        } else {
            $message = "Email not found.";
        }
        $stmt->close();
    }

    // === VOTER REGISTRATION ===
    if ($login_type == 'register') {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        if ($name == '' || $email == '' || $password == '') {
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
                    $message = "Registered successfully! You can now log in.";
                } else {
                    $message = "Error: " . $conn->error;
                }
                $ins->close();
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Login Portal</title>
<style>
body {
  font-family: Arial, sans-serif;
  background: #f0f2f5;
  display: flex; justify-content: center; align-items: center;
  height: 100vh; margin: 0;
}
.container {
  width: 350px; background: white; padding: 20px;
  border-radius: 10px; box-shadow: 0 0 15px rgba(0,0,0,0.1);
}
h2 { text-align: center; color: #333; }
.tab-buttons { display: flex; justify-content: space-between; margin-bottom: 15px; }
.tab-buttons button {
  flex: 1; padding: 10px; margin: 2px;
  border: none; cursor: pointer;
  background: #ddd; border-radius: 5px;
}
.tab-buttons button.active { background: #4CAF50; color: white; }

form { display: none; flex-direction: column; }
form.active { display: flex; }
input[type=text], input[type=email], input[type=password] {
  padding: 10px; margin: 5px 0; border: 1px solid #ccc;
  border-radius: 5px; width: 100%;
}
input[type=submit], button.reset-btn {
  background: #4CAF50; color: white; border: none; padding: 10px;
  border-radius: 5px; cursor: pointer; margin-top: 10px;
}
button.reset-btn { background: #f44336; }
.alert {
  background: #ffdddd; padding: 10px; border-left: 5px solid red;
  margin-bottom: 10px; border-radius: 4px;
}
.success { background: #ddffdd; border-left: 5px solid green; }
</style>
</head>
<body>
<div class="container">
  <h2>Login Portal</h2>

  <?php if ($message): ?>
    <div class="alert <?php echo (strpos($message,'successfully')!==false?'success':''); ?>">
      <?php echo htmlspecialchars($message); ?>
    </div>
  <?php endif; ?>

  <div class="tab-buttons">
    <button type="button" class="tab-btn active" data-target="admin">Admin</button>
    <button type="button" class="tab-btn" data-target="voter">Voter</button>
    <button type="button" class="tab-btn" data-target="register">Register</button>
  </div>

  <!-- Admin Login -->
  <form id="admin" class="active" method="post">
    <input type="hidden" name="login_type" value="admin">
    <input type="text" name="username" placeholder="Admin Username" required>
    <input type="password" name="password" placeholder="Admin Password" required>
    <input type="submit" value="Login">
    <button type="reset" class="reset-btn">Reset</button>
  </form>

  <!-- Voter Login -->
  <form id="voter" method="post">
    <input type="hidden" name="login_type" value="voter">
    <input type="email" name="email" placeholder="Voter Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <input type="submit" value="Login">
    <button type="reset" class="reset-btn">Reset</button>
  </form>

  <!-- Voter Registration -->
  <form id="register" method="post">
    <input type="hidden" name="login_type" value="register">
    <input type="text" name="name" placeholder="Full Name" required>
    <input type="email" name="email" placeholder="Email Address" required>
    <input type="password" name="password" placeholder="Create Password" required>
    <input type="submit" value="Register">
    <button type="reset" class="reset-btn">Reset</button>
  </form>
</div>

<script>
const tabs = document.querySelectorAll('.tab-btn');
const forms = document.querySelectorAll('form');
tabs.forEach(tab => {
  tab.addEventListener('click', () => {
    tabs.forEach(btn => btn.classList.remove('active'));
    tab.classList.add('active');
    forms.forEach(form => form.classList.remove('active'));
    document.getElementById(tab.dataset.target).classList.add('active');
  });
});
</script>
</body>
</html>
