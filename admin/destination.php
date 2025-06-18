<?php
session_start();
// Authentication check (uncomment when ready)
// if (!isset($_SESSION['email']) || $_SESSION['email'] !== 'admin') {
//     header('Location: login.php');
//     exit();
// }

// Database connection
$db_host = 'localhost';
$db_user = 'root'; // Change if not using root
$db_pass = '';
$db_name = 'anveshana_admin';
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

// Handle Add/Edit form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $location = $_POST['location'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? 0;
    $region = $_POST['region'] ?? '';
    $travel_type = $_POST['travel_type'] ?? '';
    $best_season = $_POST['best_season'] ?? '';
    $images = [];

    // Handle multiple file uploads
    if (isset($_FILES['images']) && isset($_FILES['images']['name']) && is_array($_FILES['images']['name'])) {
        $targetDir = "images/destination/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        foreach ($_FILES['images']['name'] as $key => $imgName) {
            if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                $imageFileType = strtolower(pathinfo($imgName, PATHINFO_EXTENSION));
                $check = getimagesize($_FILES['images']['tmp_name'][$key]);
                if ($check !== false) {
                    $uniqueName = uniqid() . '.' . $imageFileType;
                    $targetFile = $targetDir . $uniqueName;
                    if (move_uploaded_file($_FILES['images']['tmp_name'][$key], $targetFile)) {
                        $images[] = $uniqueName;
                    } else {
                        $error = "Sorry, there was an error uploading one of the files.";
                    }
                } else {
                    $error = "One of the files is not a valid image.";
                }
            }
        }
    }

    $images_json = json_encode($images);

    // If editing
    if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        // If no new images uploaded, keep old images
        if (empty($images)) {
            $result = $conn->query("SELECT images FROM destinations WHERE id = $id");
            $row = $result->fetch_assoc();
            $images_json = $row['images'];
        }
        $stmt = $conn->prepare("UPDATE destinations SET name=?, location=?, description=?, price=?, images=?, region=?, travel_type=?, best_season=? WHERE id=?");
        $stmt->bind_param("ssssssssi", $name, $location, $description, $price, $images_json, $region, $travel_type, $best_season, $id);
        if ($stmt->execute()) {
            header('Location: destination.php?success=updated');
            exit();
        } else {
            $error = "Database error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        // Add new
        $stmt = $conn->prepare("INSERT INTO destinations (name, location, description, price, images, region, travel_type, best_season) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $name, $location, $description, $price, $images_json, $region, $travel_type, $best_season);
        if ($stmt->execute()) {
            header('Location: destination.php?success=added');
            exit();
        } else {
            $error = "Database error: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Handle delete
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $conn->query("DELETE FROM destinations WHERE id = $id");
    header('Location: destination.php?success=deleted');
    exit();
}

// Fetch all destinations for listing
$destinations = [];
$result = $conn->query("SELECT * FROM destinations ORDER BY id DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $destinations[] = $row;
    }
}

// If editing, fetch the destination
$edit_destination = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = $conn->query("SELECT * FROM destinations WHERE id = $id");
    if ($result && $result->num_rows > 0) {
        $edit_destination = $result->fetch_assoc();
    }
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
    background: linear-gradient(120deg, #f8fafc 0%, #e0c3fc 100%);
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

/* destination table */
    .destination-table {
        width: 100%;
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 4px 24px rgba(44,62,80,0.10);
        border-collapse: separate;
        border-spacing: 0;
        overflow: hidden;
        margin-bottom: 2rem;
    }
    .destination-table th, .destination-table td {
        padding: 16px 14px;
        text-align: left;
        font-size: 1.05rem;
        vertical-align: middle;
    }
    .destination-table th {
        background: linear-gradient(90deg,rgba(197, 205, 47, 1),rgba(39, 205, 175, 1) 90%);
        color: #fff;
        font-weight: 600;
        border: none;
    }
    .destination-table tr:not(:last-child) {
        border-bottom: 1px solid #f0f0f0;
    }
    .destination-table tr {
        transition: background 0.2s;
    }
    .destination-table tr:hover {
        background: #f8f7ff;
    }
    .table-img-thumb {
        width: 38px;
        height: 38px;
        border-radius: 6px;
        object-fit: cover;
        margin-right: 3px;
        border: 1.5px solid #eee;
        box-shadow: 0 1px 4px rgba(44,62,80,0.07);
    }
    .btn.btn-edit {
        background: linear-gradient(90deg, #5f27cd, #48dbfb);
        color: #fff;
        font-size: 0.97rem;
        padding: 0.35rem 1.1rem;
        border-radius: 30px;
        margin-right: 0.5rem;
        border: none;
        box-shadow: none;
        transition: background 0.2s;
    }
    .btn.btn-edit:hover {
        background: linear-gradient(90deg, #48dbfb, #5f27cd);
    }
    .btn.btn-delete {
        background: #e74c3c;
        color: #fff;
        font-size: 0.97rem;
        padding: 0.35rem 1.1rem;
        border-radius: 30px;
        border: none;
        box-shadow: none;
        transition: background 0.2s;
        font-weight: 500;
        text-decoration: none;
    }
    .btn.btn-delete:hover {
        background: #c0392b;
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
                    <a href="users.php">
                        <i class="fas fa-users"></i>
                        <span>Users</span>
                    </a>
                </li>
                <li>
                    <a href="bookings.php">
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
                    <a href="packages.php">
                        <i class="fas fa-box"></i>
                        <span>Packages</span>
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
                    <?php if (isset($_GET['action']) && $_GET['action'] === 'add'): ?>
                        <h1>Add New Destination</h1>
                        <p>Fill the form to add a new destination</p>
                    <?php elseif (isset($_GET['action']) && $_GET['action'] === 'edit'): ?>
                        <h1>Edit Destination</h1>
                        <p>Update the details of the destination</p>
                    <?php else: ?>
                        <h1>Manage Destinations</h1>
                        <p>View and manage all destinations</p>
                    <?php endif; ?>
                </div>
                <div class="user-profile">
                    <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Admin" class="user-avatar">
                    <div class="user-info">
                        <h4>Admin User</h4>
                        <p>Super Administrator</p>
                    </div>
                </div>
            </header>


            <?php
            // Show success message from redirect
            if (isset($_GET['success'])) {
                $msg = $_GET['success'];
                $successMsg = '';
                if ($msg === 'added') $successMsg = 'Destination added successfully!';
                elseif ($msg === 'updated') $successMsg = 'Destination updated successfully!';
                elseif ($msg === 'deleted') $successMsg = 'Destination deleted successfully!';
                if ($successMsg) {
                    echo '<div class="alert alert-success">' . $successMsg . '</div>';
                }
            }
            ?>
            <?php if (isset($error)): ?>
                <div class="alert alert-error">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <?php if ((isset($_GET['action']) && ($_GET['action'] === 'add' || $_GET['action'] === 'edit'))): ?>
                <?php $d = $edit_destination ?? null; ?>
                <div class="destination-form">
                    <form action="destination.php<?= isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id']) ? '?action=edit&id=' . intval($_GET['id']) : '' ?>" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="name">Destination Name</label>
                            <input type="text" id="name" name="name" class="form-control" required value="<?= htmlspecialchars($d['name'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="location">Location</label>
                            <input type="text" id="location" name="location" class="form-control" required value="<?= htmlspecialchars($d['location'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" class="form-control" required><?= htmlspecialchars($d['description'] ?? '') ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="region">Region</label>
                            <input type="text" id="region" name="region" class="form-control" required value="<?= htmlspecialchars($d['region'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="travel_type">Travel Type</label>
                            <select id="travel_type" name="travel_type" class="form-control" required>
                                <option value="">Select Type</option>
                                <option value="Adventure" <?= (isset($d['travel_type']) && $d['travel_type'] === 'Adventure') ? 'selected' : '' ?>>Adventure</option>
                                <option value="Cultural" <?= (isset($d['travel_type']) && $d['travel_type'] === 'Cultural') ? 'selected' : '' ?>>Cultural</option>
                                <option value="Beach" <?= (isset($d['travel_type']) && $d['travel_type'] === 'Beach') ? 'selected' : '' ?>>Beach</option>
                                <option value="Leisure" <?= (isset($d['travel_type']) && $d['travel_type'] === 'Leisure') ? 'selected' : '' ?>>Hill Station</option>
                                <option value="Wildlife" <?= (isset($d['travel_type']) && $d['travel_type'] === 'Wildlife') ? 'selected' : '' ?>>Wildlife</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="best_season">Best Season</label>
                            <select id="best_season" name="best_season" class="form-control" required>
                                <option value="">Select Season</option>
                                <option value="Summer" <?= (isset($d['best_season']) && $d['best_season'] === 'Summer') ? 'selected' : '' ?>>Summer</option>
                                <option value="Winter" <?= (isset($d['best_season']) && $d['best_season'] === 'Winter') ? 'selected' : '' ?>>Winter</option>
                                <option value="Spring" <?= (isset($d['best_season']) && $d['best_season'] === 'Spring') ? 'selected' : '' ?>>Monsoon</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="price">Price</label>
                            <input type="number" id="price" name="price" class="form-control" min="0" step="0.01" required value="<?= htmlspecialchars($d['price'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="images">Destination Images</label>
                            <div class="file-upload">
                                <input type="file" id="images" name="images[]" accept="image/*" multiple <?= isset($_GET['action']) && $_GET['action'] === 'add' ? 'required' : '' ?>>
                                <div id="imagePreviewContainer">
                                    <?php if (isset($d['images']) && $d['images']): ?>
                                        <?php foreach (json_decode($d['images'], true) as $img): ?>
                                            <img src="images/destination/<?= htmlspecialchars($img) ?>" class="file-upload-preview" style="display:inline-block;margin-right:10px;" />
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn"><?= isset($_GET['action']) && $_GET['action'] === 'edit' ? 'Update Destination' : 'Add Destination' ?></button>
                    </form>
                </div>
            <?php else: ?>
                <div style="margin-bottom: 2rem; text-align:right;">
                    <a href="destination.php?action=add" class="btn" style="text-decoration:none;">Add Destination</a>
                </div>
                <div style="overflow-x:auto;">
                    <table class="destination-table">
                        <thead>
                            <tr>
                                <th>S.No.</th>
                                <th>Name</th>
                                <th>Location</th>
                                <th>Price</th>
                                <th>Images</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; foreach ($destinations as $dest): ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= htmlspecialchars($dest['name']) ?></td>
                                    <td><?= htmlspecialchars($dest['location']) ?></td>
                                    <td>₹<?= number_format($dest['price'], 2) ?></td>
                                    <td>
                                        <?php if ($dest['images']): ?>
                                            <?php foreach (json_decode($dest['images'], true) as $img): ?>
                                                <img src="images/destination/<?= htmlspecialchars($img) ?>" class="table-img-thumb" />
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="destination.php?action=edit&id=<?= $dest['id'] ?>" class="btn btn-edit">Edit</a>
                                        <a href="destination.php?action=delete&id=<?= $dest['id'] ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this destination?');">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <script>
        // Multiple image preview functionality
        document.getElementById('images').addEventListener('change', function(e) {
            const container = document.getElementById('imagePreviewContainer');
            container.innerHTML = '';
            const files = e.target.files;
            if (files) {
                Array.from(files).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = function(ev) {
                        const img = document.createElement('img');
                        img.src = ev.target.result;
                        img.className = 'file-upload-preview';
                        img.style.display = 'inline-block';
                        img.style.marginRight = '10px';
                        container.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                });
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