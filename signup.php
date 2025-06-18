<?php
// filepath: c:\xampp\htdocs\Anveshana\signup.php

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Database connection
    $conn = new mysqli("localhost", "root", "", "anveshana_admin");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get and sanitize form data
    $name = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $mobile = trim($_POST['mobile']);
    $avatar = null; // Default avatar, or you can set a default image path here
    $created_at = date('Y-m-d H:i:s');
    $updated_at = $created_at;

    // Basic validation
    if (empty($name) || empty($email) || empty($password) || empty($mobile)) {
        header("Location: signup.php?error=emptyfields");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: signup.php?error=invalidemail");
        exit();
    }


    // Validate mobile number (basic: 10 digits)
    if (!preg_match('/^[0-9]{10}$/', $mobile)) {
        header("Location: signup.php?error=invalidmobile");
        exit();
    }

    if (strlen($password) < 8) {
        header("Location: signup.php?error=passwordlength");
        exit();
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->close();
        header("Location: signup.php?error=emailtaken");
        exit();
    }
    $stmt->close();

    // Hash the password
    $hashed_pass = password_hash($password, PASSWORD_DEFAULT);

    // Insert user into database
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, avatar, created_at, updated_at, mobile) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $name, $email, $hashed_pass, $avatar, $created_at, $updated_at, $mobile);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        // Redirect to login or dashboard after successful signup
        header("Location: login.php?signup=success");
        exit();
    } else {
        $stmt->close();
        $conn->close();
        header("Location: signup.php?error=sqlerror");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up | Anveshana - Explore the World</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/signup.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-hero">
            <div class="auth-hero-content">
                <h2>Join Anveshana</h2>
                <p>Create your account to unlock personalized travel experiences, save your favorite destinations, and access exclusive deals.</p>
                
                <div class="auth-hero-benefits">
                    <div class="benefit-item">
                        <i class="fas fa-check-circle benefit-icon"></i>
                        <span>Personalized travel recommendations</span>
                    </div>
                    <div class="benefit-item">
                        <i class="fas fa-check-circle benefit-icon"></i>
                        <span>Save and organize your dream trips</span>
                    </div>
                    <div class="benefit-item">
                        <i class="fas fa-check-circle benefit-icon"></i>
                        <span>Exclusive member-only deals</span>
                    </div>
                    <div class="benefit-item">
                        <i class="fas fa-check-circle benefit-icon"></i>
                        <span>Fast checkout for bookings</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="auth-form-container">
            <div class="auth-form">
                <div class="auth-logo">
                    <h2>Anveshana</h2>
                    <p>Explore the World</p>
                </div>
                
                <h3 class="auth-title">Create Your Account</h3>
                
    <form id="signupForm" action="signup.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="fullname">Full Name <span style="color:red">*</span></label>
                        <input type="text" id="fullname" name="fullname" class="form-control" placeholder="Enter your full name" required>

                        <i class="fas fa-user input-icon"></i>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address <span style="color:red">*</span></label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" required>
                        <i class="fas fa-envelope input-icon"></i>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password <span style="color:red">*</span></label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Create a password" required>
                        <i class="fas fa-lock input-icon"></i>
                    </div>
                    
                    <div class="form-group">
                        <label for="mobile">Mobile Number <span style="color:red">*</span></label>
                        <input type="text" id="mobile" name="mobile" class="form-control" placeholder="Enter your 10-digit mobile number" required pattern="[0-9]{10}">
                        <i class="fas fa-phone input-icon"></i>
                    </div>
                    
                    <div class="form-group">
                        <label for="avatar">Avatar</label>
                        <input type="file" id="avatar" name="avatar" class="form-control" accept="image/*">
                        <i class="fas fa-image input-icon"></i>
                    </div>
                    <button type="submit" class="auth-btn">Sign Up</button>
                </form>
                
                <div class="social-auth">
                    <p>Or sign up with</p>
                    <div class="social-buttons">
                        <div class="social-btn google">
                            <i class="fab fa-google"></i>
                        </div>
                        <div class="social-btn facebook">
                            <i class="fab fa-facebook-f"></i>
                        </div>
                        <div class="social-btn twitter">
                            <i class="fab fa-twitter"></i>
                        </div>
                    </div>
                </div>
                
                <div class="auth-alt">
                    Already have an account? <a href="login.php">Log in</a>
                </div>
                
                <div class="terms">
                    By signing up, you agree to our <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>.
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Form validation
        // Remove confirm password JS validation, add mobile validation
        document.getElementById('signupForm').addEventListener('submit', function(e) {
            const mobile = document.getElementById('mobile').value;
            if (!/^[0-9]{10}$/.test(mobile)) {
                alert('Please enter a valid 10-digit mobile number!');
                e.preventDefault();
            }
        });
        document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const error = urlParams.get('error');
    
    if (error) {
        let errorMessage = '';
        switch(error) {
            case 'emptyfields':
                errorMessage = 'Please fill in all fields';
                break;
            case 'invalidemail':
                errorMessage = 'Invalid email address';
                break;
            case 'invalidmobile':
                errorMessage = 'Please enter a valid 10-digit mobile number';
                break;
            case 'passwordlength':
                errorMessage = 'Password must be at least 8 characters';
                break;
            case 'emailtaken':
                errorMessage = 'Email already registered';
                break;
            case 'sqlerror':
                errorMessage = 'Database error. Please try again';
                break;
            case 'wrongpassword':
                errorMessage = 'Incorrect password';
                break;
            case 'nouser':
                errorMessage = 'No account with this email';
                break;
            default:
                errorMessage = 'An error occurred';
        }
        
        // Show notification - adjust based on your notification system
        showNotification('Error', errorMessage, 'error');
    }
});
    </script>
</body>
</html>