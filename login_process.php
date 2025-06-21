<?php
// filepath: c:\xampp\htdocs\Anveshana\login.php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "anveshana_admin");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get and sanitize form data
$email = trim($_POST['email']);
$password = $_POST['password'];

// Basic validation
if (empty($email) || empty($password)) {
    header("Location: login.php?error=emptyfields");
    exit();
}

// Admin login check
if ($email === 'admin@gmail.com' && $password === 'ad123') {
    // Optionally, you can set admin session variables here
    header("Location: admin/admin.php");
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: login.php?error=invalidemail");
    exit();
}

// Check if user exists
$stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows < 1) {
    $stmt->close();
    $conn->close();
    header("Location: login.php?error=nouser");
    exit();
}

$stmt->bind_result($id, $fullname, $hashed_pass);
$stmt->fetch();

if (password_verify($password, $hashed_pass)) {
    // Login successful
    $_SESSION['userid'] = $id;
    $_SESSION['fullname'] = $fullname;
    $_SESSION['email'] = $email;
    $stmt->close();
    $conn->close();
    header("Location: dashboard.php?login=success");
    exit();
} else {
    // Wrong password
    $stmt->close();
    $conn->close();
    header("Location: login.php?error=wrongpassword");
    exit();
}
?>