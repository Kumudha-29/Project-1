<?php
include("db_connect.php"); // make sure this file connects to your database

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $party = $_POST['party'];
    $election_id = $_POST['election_id'];

    if (!empty($name) && !empty($party) && !empty($election_id)) {
        $sql = "INSERT INTO candidates (name, party, election_id) VALUES ('$name', '$party', '$election_id')";
        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Candidate added successfully'); window.location='manage_candidates.php';</script>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        echo "<script>alert('All fields are required');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Candidate</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f9fa;
            padding: 30px;
        }
        .container {
            background: white;
            width: 400px;
            margin: auto;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0px 0px 8px rgba(0,0,0,0.1);
        }
        input, select, button {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        button {
            background: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
        h2 {
            text-align: center;
        }
    </style>
</head>
<body style="background-image: url('Voting.png'); 
             background-size: cover; 
             background-repeat: no-repeat; 
             background-position: center center; 
             min-height: 100vh;
             margin: 0;">



<div class="container">
    <h2>Add Candidate</h2>
    <form method="POST" action="">
        <label>Candidate Name:</label>
        <input type="text" name="name" required>

        <label>Party Name:</label>
        <input type="text" name="party" required>

        <label>Election ID:</label>
        <input type="number" name="election_id" required>

        <button type="submit" name="submit">Add Candidate</button>
    </form>
</div>

</body>
</html>
