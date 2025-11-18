<?php
include 'db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Candidates</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 20px;
        }
        h2 {
            text-align: center;
        }
        form, table {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin: 20px auto;
            width: 80%;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        input, select, button {
            padding: 8px;
            margin: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        table th {
            background-color: #007BFF;
            color: white;
        }
        button {
            background-color: #007BFF;
            color: white;
            border: none;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body style="background-image: url('Voting.png'); 
             background-size: cover; 
             background-repeat: no-repeat; 
             background-position: center center; 
             min-height: 100vh;
             margin: 0;">

<h2>Manage Candidates</h2>

<form method="POST" action="">
    <h3>Add Candidate</h3>
    <label>Candidate Name:</label>
    <input type="text" name="name" required>
    <label>Party:</label>
    <input type="text" name="party" required>

    <label>Election:</label>
    <select name="election_id" required>
        <option value="">-- Select Election --</option>
        <?php
        $result = $conn->query("SELECT id, name FROM elections");
        while ($row = $result->fetch_assoc()) {
            echo "<option value='".$row['id']."'>".$row['name']."</option>";
        }
        ?>
    </select>
    <button type="submit" name="add">Add Candidate</button>
</form>

<?php
if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $party = $_POST['party'];
    $election_id = $_POST['election_id'];

    $stmt = $conn->prepare("INSERT INTO candidates (name, party, election_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $name, $party, $election_id);

    if ($stmt->execute()) {
        echo "<p style='color:green; text-align:center;'>Candidate added successfully!</p>";
    } else {
        echo "<p style='color:red; text-align:center;'>Error: ".$conn->error."</p>";
    }
}
?>

<h3>All Candidates</h3>
<table>
    <tr>
        <th>ID</th>
        <th>Candidate Name</th>
        <th>Party</th>
        <th>Election</th>
        <th>Created At</th>
        <th>Action</th>
    </tr>

    <?php
    $query = "SELECT c.*, e.name AS election_name FROM candidates c 
              LEFT JOIN elections e ON c.election_id = e.id ORDER BY c.id DESC";
    $result = $conn->query($query);

    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>".$row['id']."</td>
            <td>".$row['name']."</td>
            <td>".$row['party']."</td>
            <td>".$row['election_name']."</td>
            <td>".$row['created_at']."</td>
            <td>
                <a href='edit_candidate.php?id=".$row['id']."'>Edit</a> | 
                <a href='delete_candidate.php?id=".$row['id']."' onclick='return confirm(\"Are you sure?\");'>Delete</a>
            </td>
        </tr>";
    }
    ?>
</table>
</body>
</html>
