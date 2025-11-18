<?php
// login_portal_debug.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('db_connect.php');
session_start();

$debug = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['user_type'] ?? 'admin';
    $email = isset($_POST['email']) ? $conn->real_escape_string(trim($_POST['email'])) : '';
    $password = isset($_POST['password']) ? $conn->real_escape_string(trim($_POST['password'])) : '';

    $debug['posted'] = $_POST;
    $debug['db_connected'] = ($conn && !$conn->connect_error) ? true : false;

    if ($type === 'admin') {
        // try username/email variants: check what column exists
        // Preferred admin columns: username or email
        $adminCols = [];
        $res = $conn->query("SHOW COLUMNS FROM admin");
        if ($res) {
            while ($c = $res->fetch_assoc()) $adminCols[] = $c['Field'];
        }
        $debug['admin_columns'] = $adminCols;

        // try both email and username if present
        $sqls = [];
        if (in_array('email', $adminCols)) {
            $sqls[] = "SELECT * FROM admin WHERE email='$email' AND password='$password'";
        }
        if (in_array('username', $adminCols)) {
            $sqls[] = "SELECT * FROM admin WHERE username='$email' AND password='$password'";
        }
        if (empty($sqls)) {
            $debug['admin_error'] = 'No admin columns email/username found. Columns: ' . implode(',',$adminCols);
        } else {
            foreach ($sqls as $sql) {
                $debug['sql_attempt'][] = $sql;
                $r = $conn->query($sql);
                if ($r && $r->num_rows>0) {
                    $debug['admin_login_success'] = $r->fetch_assoc();
                    $_SESSION['admin'] = $email;
                    header("Location: admin_dashboard.php");
                    exit();
                } else {
                    $debug['last_result'][] = $r ? "rows:".$r->num_rows : "query failed: ".$conn->error;
                }
            }
            if (!isset($debug['admin_login_success'])) $debug['admin_login_fail'] = true;
        }
    }

    if ($type === 'voter') {
        $voterCols = [];
        $res = $conn->query("SHOW COLUMNS FROM voters");
        if ($res) {
            while ($c = $res->fetch_assoc()) $voterCols[] = $c['Field'];
        }
        $debug['voter_columns'] = $voterCols;

        $sqls = [];
        if (in_array('email', $voterCols)) {
            $sqls[] = "SELECT * FROM voters WHERE email='$email' AND password='$password'";
        }
        if (in_array('voter_id', $voterCols) && !empty($email)) {
            // if user typed numeric id or id value
            $sqls[] = "SELECT * FROM voters WHERE voter_id='$email' AND password='$password'";
        }
        if (empty($sqls)) {
            $debug['voter_error'] = 'No expected voters columns (email/voter_id) found. Columns: '.implode(',',$voterCols);
        } else {
            foreach ($sqls as $sql) {
                $debug['sql_attempt_voter'][] = $sql;
                $r = $conn->query($sql);
                if ($r && $r->num_rows>0) {
                    $debug['voter_login_success'] = $r->fetch_assoc();
                    $_SESSION['voter'] = $email;
                    header("Location: voter_dashboard.php");
                    exit();
                } else {
                    $debug['last_result_voter'][] = $r ? "rows:".$r->num_rows : "query failed: ".$conn->error;
                }
            }
            if (!isset($debug['voter_login_success'])) $debug['voter_login_fail'] = true;
        }
    }

    if ($type === 'register') {
        $name = $conn->real_escape_string($_POST['name'] ?? '');
        $email = $conn->real_escape_string($_POST['email'] ?? '');
        $password = $conn->real_escape_string($_POST['password'] ?? '');
        $debug['register_attempt'] = compact('name','email');
        // simple register: check 'voters' columns first
        $resCols = $conn->query("SHOW COLUMNS FROM voters");
        $cols = [];
        while ($c = $resCols->fetch_assoc()) $cols[] = $c['Field'];
        $debug['voter_columns'] = $cols;
        // verify insert columns exist
        if (in_array('name',$cols) && in_array('email',$cols) && in_array('password',$cols)) {
            $check = $conn->query("SELECT * FROM voters WHERE email='$email' LIMIT 1");
            $debug['register_check'] = $check ? $check->num_rows : 'err:'.$conn->error;
            if ($check && $check->num_rows==0) {
                $ins = "INSERT INTO voters (name,email,password) VALUES ('$name','$email','$password')";
                $debug['register_sql'] = $ins;
                $ok = $conn->query($ins);
                $debug['register_result'] = $ok ? 'inserted' : 'error: '.$conn->error;
                if ($ok) {
                    $_SESSION['voter'] = $email;
                    header("Location: voter_dashboard.php");
                    exit();
                }
            } else {
                $debug['register_fail'] = 'already exists';
            }
        } else {
            $debug['register_fail'] = 'required columns missing';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Login Debug</title></head>
<body>
<h2>Login Debug Page</h2>
<p>Use this form to reproduce the problem — it will print debug info below.</p>

<form method="post">
  <label>User type:
    <select name="user_type">
      <option value="admin">Admin</option>
      <option value="voter">Voter</option>
      <option value="register">Register</option>
    </select>
  </label><br><br>

  <label>Email/VoterID/Username: <input type="text" name="email" required></label><br>
  <label>Password: <input type="password" name="password" required></label><br>
  <label>Name (for register): <input type="text" name="name"></label><br>
  <button type="submit">Submit</button>
</form>

<hr>
<h3>Debug output</h3>
<pre><?php echo htmlspecialchars(print_r($debug, true)); ?></pre>
</body>
</html>
