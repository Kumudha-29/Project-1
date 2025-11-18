<?php
include 'db_connect.php';

if (!isset($_GET['id'])) {
    die("Candidate ID not provided!");
}

$id = $_GET['id'];

// Prepare and execute delete query
$stmt = $conn->prepare("DELETE FROM candidates WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "<script>
            alert('Candidate deleted successfully!');
            window.location='manage_candidates.php';
          </script>";
} else {
    echo "<p style='color:red;'>Error deleting candidate: ".$conn->error."</p>";
}
?>
