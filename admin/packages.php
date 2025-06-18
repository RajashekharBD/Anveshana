<?php
session_start();
// Authentication check (uncomment when ready)
// if (!isset($_SESSION['email']) || $_SESSION['email'] !== 'admin') {
//     header('Location: login.php');
//     exit();
// }

// Database connection
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'anveshana_admin';
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

// Handle Add/Edit form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $package_name = $_POST['package_name'] ?? '';
    $destination = $_POST['destination'] ?? '';
    $price = $_POST['price'] ?? 0;
    $description = $_POST['description'] ?? '';
    $id = $_POST['id'] ?? null;
    $image_filename = null;

    // Handle image upload if file is provided
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../images/packages/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $tmp_name = $_FILES['image']['tmp_name'];
        $original_name = basename($_FILES['image']['name']);
        $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif'];
        if (in_array($ext, $allowed)) {
            $image_filename = uniqid('pkg_', true) . '.' . $ext;
            $target_path = $upload_dir . $image_filename;
            if (!move_uploaded_file($tmp_name, $target_path)) {
                $error = 'Failed to upload image.';
            }
        } else {
            $error = 'Invalid image file type.';
        }
    }

    if ($id) {
        // Edit package
        if ($image_filename) {
            $stmt = $conn->prepare("UPDATE packages SET package_name=?, destination=?, price=?, description=?, image=? WHERE id=?");
            $stmt->bind_param("sssssi", $package_name, $destination, $price, $description, $image_filename, $id);
        } else {
            $stmt = $conn->prepare("UPDATE packages SET package_name=?, destination=?, price=?, description=? WHERE id=?");
            $stmt->bind_param("ssssi", $package_name, $destination, $price, $description, $id);
        }
        if ($stmt->execute()) {
            header('Location: packages.php?success=updated');
            exit();
        } else {
            $error = "Database error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        // Add new package
        $stmt = $conn->prepare("INSERT INTO packages (package_name, destination, price, description, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $package_name, $destination, $price, $description, $image_filename);
        if ($stmt->execute()) {
            header('Location: packages.php?success=added');
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
    $conn->query("DELETE FROM packages WHERE id = $id");
    header('Location: packages.php?success=deleted');
    exit();
}

// Fetch all packages for listing
$packages = [];
$result = $conn->query("SELECT * FROM packages ORDER BY id DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $packages[] = $row;
    }
}

// If editing, fetch the package
$edit_package = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = $conn->query("SELECT * FROM packages WHERE id = $id");
    if ($result && $result->num_rows > 0) {
        $edit_package = $result->fetch_assoc();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Packages | Anveshana</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background: linear-gradient(120deg, #f8fafc 0%, #e0c3fc 100%);
            min-height: 100vh;
            background-attachment: fixed;
            background-repeat: no-repeat;
        }
        .packages-form {
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
        .packages-form label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #f744ee;
            letter-spacing: 0.02em;
        }
        .packages-form input,
        .packages-form textarea {
            width: 100%;
            padding: 0.85rem 1.1rem;
            border: 1.5px solid #a1a1aa;
            border-radius: 6px;
            font-family: 'Poppins', sans-serif;
            font-size: 1.05rem;
            background: rgba(255,255,255,0.7);
            transition: border 0.2s, box-shadow 0.2s;
            outline: none;
            margin-bottom: 1.2rem;
        }
        .packages-form input:focus,
        .packages-form textarea:focus {
            border: 1.5px solid #ff6b6b;
            box-shadow: 0 0 0 2px #ff6b6b, 0 2px 8px rgba(44,62,80,0.08);
        }
        .packages-form button {
            padding: 0.85rem 2.2rem;
            background: linear-gradient(90deg, #ff6b6b, #5f27cd);
            color: white;
            border: none;
            border-radius: 50px;
            font-size: 1.08rem;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 16px rgba(44,62,80,0.10);
            transition: background 0.3s, transform 0.2s;
            margin-top: 0.5rem;
        }
        .packages-form button:hover {
            background: linear-gradient(90deg, #5f27cd, #ff6b6b);
            transform: translateY(-2px) scale(1.04);
        }
        .packages-table {
            width: 100%;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 4px 24px rgba(44,62,80,0.10);
            border-collapse: separate;
            border-spacing: 0;
            overflow: hidden;
            margin-bottom: 2rem;
        }
        .packages-table th, .packages-table td {
            padding: 16px 14px;
            text-align: left;
            font-size: 1.05rem;
            vertical-align: middle;
        }
        .packages-table th {
            background: linear-gradient(90deg,rgba(197, 205, 47, 1),rgba(39, 205, 175, 1) 90%);
            color: #fff;
            font-weight: 600;
            border: none;
        }
        .packages-table tr:not(:last-child) {
            border-bottom: 1px solid #f0f0f0;
        }
        .packages-table tr {
            transition: background 0.2s;
        }
        .packages-table tr:hover {
            background: #f8f7ff;
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
                <li><a href="admin.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                <li><a href="users.php"><i class="fas fa-users"></i><span>Users</span></a></li>
                <li><a href="bookings.php"><i class="fas fa-calendar-check"></i><span>Bookings</span></a></li>
                <li><a href="destination.php"><i class="fas fa-map-marked-alt"></i><span>Destinations</span></a></li>
                <li><a href="#" class="active"><i class="fas fa-box"></i><span>Packages</span></a></li>
                <li><a href="#"><i class="fas fa-comment-alt"></i><span>Feedback</span></a></li>
                <li><a href="#"><i class="fas fa-cog"></i><span>Settings</span></a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li>
            </ul>
        </aside>
        <!-- Main Content -->
        <main class="admin-main">
            <header class="admin-header">
                <div class="page-title">
                    <?php if (isset($_GET['action']) && $_GET['action'] === 'add'): ?>
                        <h1>Add New Package</h1>
                        <p>Fill the form to add a new package</p>
                    <?php elseif (isset($_GET['action']) && $_GET['action'] === 'edit'): ?>
                        <h1>Edit Package</h1>
                        <p>Update the details of the package</p>
                    <?php else: ?>
                        <h1>Manage Packages</h1>
                        <p>View and manage all packages</p>
                    <?php endif; ?>
                </div>
                <div class="user-profile">
                    <img src="https://randomuser.me/api/portraits/men/44.jpg" alt="Admin" class="user-avatar">
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
                if ($msg === 'added') $successMsg = 'Package added successfully!';
                elseif ($msg === 'updated') $successMsg = 'Package updated successfully!';
                elseif ($msg === 'deleted') $successMsg = 'Package deleted successfully!';
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
                <?php $p = $edit_package ?? null; ?>
                <div class="packages-form">
                    <form action="packages.php<?= isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id']) ? '?action=edit&id=' . intval($_GET['id']) : '' ?>" method="POST" enctype="multipart/form-data">
                        <?php if (isset($_GET['action']) && $_GET['action'] === 'edit'): ?>
                            <input type="hidden" name="id" value="<?= intval($_GET['id']) ?>">
                        <?php endif; ?>
                        <label for="package_name">Package Name</label>
                        <input type="text" id="package_name" name="package_name" required value="<?= htmlspecialchars($p['package_name'] ?? '') ?>">
                        <label for="destination">Destination</label>
                        <input type="text" id="destination" name="destination" required value="<?= htmlspecialchars($p['destination'] ?? '') ?>">
                        <label for="price">Price</label>
                        <input type="number" id="price" name="price" min="0" step="0.01" required value="<?= htmlspecialchars($p['price'] ?? '') ?>">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" required><?= htmlspecialchars($p['description'] ?? '') ?></textarea>
                        <label for="image">Image</label>
                        <input type="file" id="image" name="image" accept="image/*" <?= isset($_GET['action']) && $_GET['action'] === 'add' ? 'required' : '' ?>>
                        <?php if (isset($p['image']) && $p['image']): ?>
                            <div style="margin-bottom:1rem;">
                                <img src="../images/packages/<?= htmlspecialchars($p['image']) ?>" alt="Current Image" style="max-width:120px; border-radius:8px; box-shadow:0 2px 8px #ccc;">
                            </div>
                        <?php endif; ?>
                        <button type="submit" class="btn"><?= isset($_GET['action']) && $_GET['action'] === 'edit' ? 'Update Package' : 'Add Package' ?></button>
                    </form>
                </div>
            <?php else: ?>
                <div style="margin-bottom: 2rem; text-align:right;">
                    <a href="packages.php?action=add" class="btn" style="text-decoration:none;">Add Package</a>
                </div>
                <div style="overflow-x:auto;">
                    <table class="packages-table">
                        <thead>
                            <tr>
                                <th>S.No.</th>
                                <th>Package Name</th>
                                <th>Destination</th>
                                <th>Image</th>
                                <th>Price</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; foreach ($packages as $package): ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= htmlspecialchars($package['package_name']) ?></td>
                                    <td><?= htmlspecialchars($package['destination']) ?></td>
                                    <td>
                                        <?php if (!empty($package['image'])): ?>
                                            <img src="../images/packages/<?= htmlspecialchars($package['image']) ?>" alt="Image" style="max-width:70px; border-radius:6px; box-shadow:0 1px 4px #ccc;">
                                        <?php else: ?>
                                            <span style="color:#aaa;">No image</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>₹<?= number_format($package['price'], 2) ?></td>
                                    <td><?= htmlspecialchars($package['description']) ?></td>
                                    <td>
                                        <a href="packages.php?action=edit&id=<?= $package['id'] ?>" class="btn btn-edit">Edit</a>
                                        <a href="packages.php?action=delete&id=<?= $package['id'] ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this package?');">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
