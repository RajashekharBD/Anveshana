<?php
session_start();
// Authentication check (uncomment when ready)
// if (!isset($_SESSION['email']) || $_SESSION['email'] !== 'admin') {
//     header('Location: login.php');
//     exit();
// }

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process the form data and add to database
    $name = $_POST['name'] ?? '';
    $location = $_POST['location'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? 0;
    $region = $_POST['region'] ?? '';
    $travel_type = $_POST['travel_type'] ?? '';
    $best_season = $_POST['best_season'] ?? '';

    
    // Handle file upload
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "images/destination/";
        $imageFileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        
        // Check if image file is an actual image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            // Generate unique filename
            $image = uniqid() . '.' . $imageFileType;
            $targetFile = $targetDir . $image;
            
            // Try to upload file
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                // File uploaded successfully
            } else {
                $error = "Sorry, there was an error uploading your file.";
            }
        } else {
            $error = "File is not an image.";
        }
    }
    // Example updated query with new fields
// $stmt = $db->prepare("INSERT INTO destinations (name, location, description, price, image, region, travel_type, best_season) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
// $stmt->execute([$name, $location, $description, $price, $image, $region, $travel_type, $best_season]);

    // Here you would typically insert into database
    // $db->prepare("INSERT INTO destinations (name, location, description, price, image) VALUES (?, ?, ?, ?, ?)")->execute([$name, $location, $description, $price, $image]);
    
    $success = "Destination added successfully!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Destinations | Anveshana</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        :root {
    --primary: #ff6b6b;         /* Vibrant Coral */
    --primary-dark: #f744ee;    /* Pinkish Purple */
    --secondary: #5f27cd;       /* Interactive Purple */
    --danger: #ffb400;          /* Amber/Warning */
    --warning: #48dbfb;         /* Sky Blue */
    --dark: #22223b;            /* Rich Navy */
    --light: #f8f7ff;           /* Soft Off-White */
    --gray: #a1a1aa;            /* Modern Gray */
    --gray-dark: #575366;       /* Muted Dark Gray */
    --success: #d1fae5;
    --error: #fee2e2;
}

body {
    background: linear-gradient(135deg, var(--primary), var(--secondary) 80%);
    min-height: 100vh;
    background-attachment: fixed;
    background-repeat: no-repeat;
}

/* .admin-container {
    display: flex;
    font-family: 'Poppins', sans-serif;
}

.admin-sidebar {
    background: linear-gradient(135deg, var(--primary), var(--secondary) 80%);
    color: #fff;
    padding: 1.5rem;
    width: 250px;
    box-shadow: 2px 0 18px rgba(95,39,205,0.10);
    min-height: 100vh;
    transition: background 0.3s;
} */
/* 
.sidebar-header {
    display: flex;
    align-items: center;
    margin-bottom: 2rem;
} */

.logo {
    font-size: 1.5rem;
    font-weight: 600;
    color: #fff;
    display: flex;
    align-items: center;
}

.logo i {
    margin-right: 0.5rem;
    color: var(--secondary);
}

.sidebar-menu a {
    text-decoration: none;
    color: #333;
    display: flex;
    align-items: center;
    transition: color 0.3s;
}

.sidebar-menu a i {
    margin-right: 0.75rem;
    font-size: 1.2rem;
}

.sidebar-menu a:hover {
    color: var(--primary);
}

.active {
    color: var(--primary);
    font-weight: 500;
}

.admin-main {
    flex: 1;
    padding: 2rem;
}

.admin-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.page-title {
    font-size: 1.8rem;
    font-weight: 600;
    color: #333;
}

.user-profile {
    display: flex;
    align-items: center;
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    margin-right: 0.75rem;
}

.destination-form {
    background: rgba(255,255,255,0.85);
    border-radius: 18px;
    padding: 2.5rem 2rem;
    box-shadow: 0 8px 32px rgba(44,62,80,0.18);
    max-width: 600px;
    margin: 2.5rem auto;
    backdrop-filter: blur(2px);
    border: 1.5px solid rgba(255,255,255,0.25);
    transition: box-shadow 0.3s, transform 0.3s;
    position: relative;
    overflow: hidden;
}

.destination-form::before {
    content: '';
    position: absolute;
    top: -60px; left: -60px;
    width: 180px; height: 180px;
    background: radial-gradient(circle, var(--secondary) 0%, transparent 70%);
    opacity: 0.12;
    z-index: 0;
}

.form-group {
    margin-bottom: 1.7rem;
    position: relative;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: var(--primary-dark);
    letter-spacing: 0.02em;
}

.form-control {
    width: 100%;
    padding: 0.85rem 1.1rem 0.85rem 2.5rem;
    border: 1.5px solid var(--gray);
    border-radius: 6px;
    font-family: 'Poppins', sans-serif;
    font-size: 1.05rem;
    background: rgba(255,255,255,0.7);
    transition: border 0.2s, box-shadow 0.2s;
    outline: none;
    position: relative;
}

.form-control:focus {
    border: 1.5px solid var(--primary);
    box-shadow: 0 0 0 2px var(--primary), 0 2px 8px rgba(44,62,80,0.08);
}

textarea.form-control {
    min-height: 120px;
    resize: vertical;
}

.form-group i {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--gray-dark);
    font-size: 1.1rem;
    opacity: 0.7;
    z-index: 2;
}

.btn {
    padding: 0.85rem 2.2rem;
    background: linear-gradient(90deg, var(--primary), var(--secondary));
    color: white;
    border: none;
    border-radius: 50px;
    cursor: pointer;
    font-family: 'Poppins', sans-serif;
    font-size: 1.08rem;
    font-weight: 600;
    box-shadow: 0 4px 16px rgba(44,62,80,0.10);
    transition: background 0.3s, transform 0.2s;
    margin-top: 0.5rem;
}

.btn:hover {
    background: linear-gradient(90deg, var(--secondary), var(--primary));
    transform: translateY(-2px) scale(1.04);
}

.file-upload {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.file-upload-preview {
    width: 110px;
    height: 110px;
    border-radius: 10px;
    object-fit: cover;
    display: none;
    box-shadow: 0 2px 8px rgba(44,62,80,0.10);
    border: 2px solid var(--primary);
    background: #fff;
}

input[type="file"]::-webkit-file-upload-button {
    visibility: hidden;
}

input[type="file"]::before {
    content: 'Choose Image';
    display: inline-block;
    background: var(--primary);
    color: #fff;
    border-radius: 50px;
    padding: 0.5rem 1.2rem;
    outline: none;
    white-space: nowrap;
    cursor: pointer;
    font-size: 1rem;
    font-family: 'Poppins', sans-serif;
    margin-right: 10px;
    transition: background 0.2s;
}

input[type="file"]:hover::before {
    background: var(--secondary);
}

.alert {
    padding: 1rem;
    border-radius: 6px;
    margin-bottom: 1.5rem;
    font-weight: 500;
    letter-spacing: 0.01em;
}

.alert-success {
    background: #d1fae5;
    color: #065f46;
}

.alert-error {
    background: #fee2e2;
    color: #b91c1c;
}

@media (max-width: 700px) {
    .destination-form {
        padding: 1.2rem 0.5rem;
        max-width: 98vw;
    }
    .file-upload-preview {
        width: 70px;
        height: 70px;
    }
}
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar Navigation -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <i class="fas fa-compass"></i>
                    <span>Anveshana</span>
                </div>
            </div>
            
            <ul class="sidebar-menu">
                <li>
                    <a href="admin.php">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fas fa-users"></i>
                        <span>Users</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fas fa-calendar-check"></i>
                        <span>Bookings</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="active">
                        <i class="fas fa-map-marked-alt"></i>
                        <span>Destinations</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fas fa-comment-alt"></i>
                        <span>Feedback</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </a>
                </li>
                <li>
                    <a href="../logout.php">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </aside>
        
        <!-- Main Content -->
        <main class="admin-main">
            <header class="admin-header">
                <div class="page-title">
                    <h1><?= isset($_GET['action']) && $_GET['action'] === 'add' ? 'Add New' : 'Manage' ?> Destination</h1>
                    <p><?= isset($_GET['action']) && $_GET['action'] === 'add' ? 'Fill the form to add a new destination' : 'View and manage all destinations' ?></p>
                </div>
                
                <div class="user-profile">
                    <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Admin" class="user-avatar">
                    <div class="user-info">
                        <h4>Admin User</h4>
                        <p>Super Administrator</p>
                    </div>
                </div>
            </header>
            
            <?php if (isset($success)): ?>
                <div class="alert alert-success">
                    <?= $success ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-error">
                    <?= $error ?>
                </div>
            <?php endif; ?>
            
            <div class="destination-form">
                <form action="destination.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="name">Destination Name</label>
                        <input type="text" id="name" name="name" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" id="location" name="location" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" class="form-control" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="region">Region</label>
                        <input type="text" id="region" name="region" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="travel_type">Travel Type</label>
                        <select id="travel_type" name="travel_type" class="form-control" required>
                            <option value="">Select Type</option>
                            <option value="Adventure">Adventure</option>
                            <option value="Cultural">Cultural</option>
                            <option value="Beach">Beach</option>
                            <option value="Leisure">Hill Station</option>
                            <option value="Wildlife">Wildlife</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="best_season">Best Season</label>
                        <select id="best_season" name="best_season" class="form-control" required>
                            <option value="">Select Season</option>
                            <option value="Summer">Summer</option>
                            <option value="Winter">Winter</option>
                            <option value="Spring">Monsoon</option>
                        </select>
                    </div>

                    
                    <div class="form-group">
                        <label for="price">Price</label>
                        <input type="number" id="price" name="price" class="form-control" min="0" step="0.01" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="image">Destination Image</label>
                        <div class="file-upload">
                            <input type="file" id="image" name="image" accept="image/*" required>
                            <img id="imagePreview" class="file-upload-preview" src="#" alt="Preview">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn">Add Destination</button>
                </form>
            </div>
        </main>
    </div>

    <script>
        // Image preview functionality
        document.getElementById('image').addEventListener('change', function(e) {
            const preview = document.getElementById('imagePreview');
            const file = e.target.files[0];
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            
            if (file) {
                reader.readAsDataURL(file);
            }
        });
        
        // Animation and hover effects
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.destination-form');
            cards.forEach(card => {
                card.addEventListener('mouseenter', () => {
                    card.style.transform = 'translateY(-5px)';
                    card.style.boxShadow = '0 10px 15px -3px rgba(0, 0, 0, 0.1)';
                });
                
                card.addEventListener('mouseleave', () => {
                    card.style.transform = '';
                    card.style.boxShadow = '';
                });
            });
        });
    </script>
</body>
</html>