<?php
// filepath: c:\xampp\htdocs\Anveshana\signup.php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$conn = new mysqli("localhost", "root", "", "anveshana");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get and sanitize form data
$fullname = trim($_POST['fullname']);
$email = trim($_POST['email']);
$password = $_POST['password'];
$confirmpassword = $_POST['confirmPassword']; // Note: matches HTML name

// Basic validation
if (empty($fullname) || empty($email) || empty($password) || empty($confirmpassword)) {
    header("Location: signup.html?error=emptyfields");
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: signup.html?error=invalidemail");
    exit();
}

if ($password !== $confirmpassword) {
    header("Location: signup.html?error=passwordcheck");
    exit();
}

if (strlen($password) < 8) {
    header("Location: signup.html?error=passwordlength");
    exit();
}

// Check if email already exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $stmt->close();
    header("Location: signup.html?error=emailtaken");
    exit();
}
$stmt->close();

// Hash the password
$hashed_pass = password_hash($password, PASSWORD_DEFAULT);

// Insert user into database
$stmt = $conn->prepare("INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $fullname, $email, $hashed_pass);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    // Redirect to login or dashboard after successful signup
    header("Location: login.php?signup=success");
    exit();
} else {
    $stmt->close();
    $conn->close();
    header("Location: signup.html?error=sqlerror");
    exit();
}
?>