<?php
include('db_connect.php');
session_start();
if (!isset($_SESSION['admin'])) { header("Location: admin_login.php"); exit(); }

if(isset($_GET['election_id'])) {
    $election_id = (int)$_GET['election_id'];
    $hide = isset($_GET['hide']) ? 1 : 0;

    if($hide) {
        $conn->query("UPDATE elections SET results_released='0' WHERE id=$election_id");
        $msg = "Results hidden for this election.";
    } else {
        $conn->query("UPDATE elections SET results_released='1' WHERE id=$election_id");
        $msg = "Results released for this election.";
    }
}

header("Location: admin_dashboard.php?msg=".urlencode($msg));
exit();
?>
