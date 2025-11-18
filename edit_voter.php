<?php
include('db_connect.php');
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = $conn->query("SELECT * FROM voters WHERE id = $id");
    $voter = $result->fetch_assoc();
}

if (isset($_POST['update'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];

    $conn->query("UPDATE voters SET name='$name', email='$email' WHERE id=$id");

    header("Location: manage_voters.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Voter</title>
<link rel="stylesheet" href="style.css">
<style>
.container {
  background: #fff;
  max-width: 500px;
  margin: 60px auto;
  padding: 30px 40px;
  border-radius: 12px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
.container h2 {
  text-align: center;
  color: #2c3e50;
  margin-bottom: 25px;
}
form label {
  display: block;
  margin-top: 10px;
  font-weight: 600;
  color: #333;
}
form input[type="text"],
form input[type="email"] {
  width: 100%;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 8px;
  margin-top: 5px;
  font-size: 16px;
}
form .btn {
  display: inline-block;
  background: #2c7be5;
  color: #fff;
  padding: 10px 20px;
  border: none;
  border-radius: 8px;
  margin-top: 20px;
  cursor: pointer;
  font-size: 16px;
}
form .btn:hover {
  background: #1a5dc9;
}
.cancel {
  text-decoration: none;
  color: #fff;
  background: #6c757d;
  padding: 10px 20px;
  border-radius: 8px;
  margin-left: 10px;
}
.cancel:hover {
  background: #5a6268;
}
</style>
</head>
<body style="background-image: url('Voting.png'); 
             background-size: cover; 
             background-repeat: no-repeat; 
             background-position: center center; 
             min-height: 100vh;
             margin: 0;">



<header><h1>Edit Voter</h1></header>

<div class="container">
  <h2>Update Voter Details</h2>
  <form method="POST">
    <label>Name</label>
    <input type="text" name="name" value="<?php echo htmlspecialchars($voter['name']); ?>" required>

    <label>Email</label>
    <input type="email" name="email" value="<?php echo htmlspecialchars($voter['email']); ?>" required>

    <button type="submit" name="update" class="btn">Update</button>
    <a href="manage_voters.php" class="cancel">Cancel</a>
  </form>
</div>

</body>
</html>
