<?php
include('db_connect.php');
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $party = trim($_POST['party']);
    $password = $_POST['password'];

    // Password validation
    if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&]).{8,}$/', $password)) {
        echo json_encode(["status" => "error", "message" => "Password must be at least 8 characters long and include letters, numbers, and a special character."]);
        exit;
    }

    // Check for empty fields
    if (empty($name) || empty($username) || empty($party) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "Please fill all fields."]);
        exit;
    }

    // Check if username already exists
    $stmt = $conn->prepare("SELECT id FROM candidates WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Username already exists!"]);
        exit;
    }
    $stmt->close();

    // Hash password and insert data
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO candidates (name, username, password, party, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssss", $name, $username, $hashed, $party);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "✅ Candidate registered successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database error: " . $conn->error]);
    }
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
?>
