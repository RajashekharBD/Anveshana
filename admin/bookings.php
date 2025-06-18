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
    $name = $_POST['name'] ?? '';
    $destination = $_POST['destination'] ?? '';
    $price = $_POST['price'] ?? 0;
    $description = $_POST['description'] ?? '';
    $id = $_POST['id'] ?? null;

    if ($id) {
        // Edit booking
        $stmt = $conn->prepare("UPDATE bookings SET name=?, destination=?, price=?, description=? WHERE id=?");
        $stmt->bind_param("ssssi", $name, $destination, $price, $description, $id);
        if ($stmt->execute()) {
            header('Location: bookings.php?success=updated');
            exit();
        } else {
            $error = "Database error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        // Add new booking
        $stmt = $conn->prepare("INSERT INTO bookings (name, destination, price, description) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $destination, $price, $description);
        if ($stmt->execute()) {
            header('Location: bookings.php?success=added');
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
    $conn->query("DELETE FROM bookings WHERE id = $id");
    header('Location: bookings.php?success=deleted');
    exit();
}

// Fetch all bookings for listing
$bookings = [];
$result = $conn->query("SELECT * FROM bookings ORDER BY id DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
}

// If editing, fetch the booking
$edit_booking = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = $conn->query("SELECT * FROM bookings WHERE id = $id");
    if ($result && $result->num_rows > 0) {
        $edit_booking = $result->fetch_assoc();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings | Anveshana</title>
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
        .bookings-form {
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
        .bookings-form label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #f744ee;
            letter-spacing: 0.02em;
        }
        .bookings-form input,
        .bookings-form textarea {
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
        .bookings-form input:focus,
        .bookings-form textarea:focus {
            border: 1.5px solid #ff6b6b;
            box-shadow: 0 0 0 2px #ff6b6b, 0 2px 8px rgba(44,62,80,0.08);
        }
        .bookings-form button {
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
        .bookings-form button:hover {
            background: linear-gradient(90deg, #5f27cd, #ff6b6b);
            transform: translateY(-2px) scale(1.04);
        }
        .bookings-table {
            width: 100%;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 4px 24px rgba(44,62,80,0.10);
            border-collapse: separate;
            border-spacing: 0;
            overflow: hidden;
            margin-bottom: 2rem;
        }
        .bookings-table th, .bookings-table td {
            padding: 16px 14px;
            text-align: left;
            font-size: 1.05rem;
            vertical-align: middle;
        }
        .bookings-table th {
            background: linear-gradient(90deg,rgba(197, 205, 47, 1),rgba(39, 205, 175, 1) 90%);
            color: #fff;
            font-weight: 600;
            border: none;
        }
        .bookings-table tr:not(:last-child) {
            border-bottom: 1px solid #f0f0f0;
        }
        .bookings-table tr {
            transition: background 0.2s;
        }
        .bookings-table tr:hover {
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
                    <a href="#" class="active">
                        <i class="fas fa-calendar-check"></i>
                        <span>Bookings</span>
                    </a>
                </li>
                <li>
                    <a href="destination.php">
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
                    <h1>All Bookings</h1>
                    <p>View, inspect, and manage all bookings made by users</p>
                </div>
            </header>
            <?php
            if (isset($_GET['success']) && $_GET['success'] === 'deleted') {
                echo '<div class="alert alert-success">Booking deleted successfully!</div>';
            }
            ?>
            <div style="overflow-x:auto;">
                <table class="bookings-table">
                    <thead>
                        <tr>
                            <th>S.No.</th>
                            <th>User Name</th>
                            <th>Email</th>
                            <th>Destination</th>
                            <th>Price</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; foreach ($bookings as $booking): ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= htmlspecialchars($booking['user_name'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($booking['email'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($booking['destination'] ?? 'N/A') ?></td>
                            <td><?= isset($booking['price']) ? '₹' . number_format($booking['price'], 2) : 'N/A' ?></td>
                            <td><?= htmlspecialchars($booking['created_at'] ?? 'N/A') ?></td>
                            <td>
                                <a href="bookings.php?action=view&id=<?= $booking['id'] ?>" class="btn btn-view">View</a>
                                <a href="bookings.php?action=delete&id=<?= $booking['id'] ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this booking?');">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php
            // View booking details modal/section
            if (isset($_GET['action']) && $_GET['action'] === 'view' && isset($_GET['id'])) {
                $id = intval($_GET['id']);
                $result = $conn->query("SELECT * FROM bookings WHERE id = $id");
                if ($result && $result->num_rows > 0) {
                    $booking = $result->fetch_assoc();
                    echo '<div style="display:flex;justify-content:center;align-items:center;min-height:60vh;">';
                    echo '<div style="background:#fff;border-radius:18px;box-shadow:0 4px 24px rgba(44,62,80,0.13);padding:2.5rem 2.5rem 2rem 2.5rem;max-width:370px;width:100%;text-align:center;">';
                    echo '<h2 style="margin-bottom:0.5rem;font-size:1.5rem;font-weight:600;color:#222;">Booking Details</h2>';
                    echo '<div style="margin-bottom:1.1rem;color:#666;font-size:1.08rem;"><b>User:</b> ' . htmlspecialchars($booking['user_name'] ?? 'N/A') . '</div>';
                    echo '<div style="margin-bottom:1.1rem;color:#666;font-size:1.08rem;"><b>Email:</b> ' . htmlspecialchars($booking['email'] ?? 'N/A') . '</div>';
                    echo '<div style="margin-bottom:1.1rem;color:#666;font-size:1.08rem;"><b>Destination:</b> ' . htmlspecialchars($booking['destination'] ?? 'N/A') . '</div>';
                    echo '<div style="margin-bottom:1.1rem;color:#666;font-size:1.08rem;"><b>Price:</b> ₹' . (isset($booking['price']) ? number_format($booking['price'], 2) : 'N/A') . '</div>';
                    echo '<div style="margin-bottom:1.1rem;color:#888;font-size:0.98rem;"><b>Date:</b> ' . htmlspecialchars($booking['created_at'] ?? 'N/A') . '</div>';
                    echo '<div style="margin-bottom:1.1rem;color:#888;font-size:0.98rem;"><b>Description:</b> ' . htmlspecialchars($booking['description'] ?? 'N/A') . '</div>';
                    echo '<a href="bookings.php" class="btn btn-edit" style="margin-top:1rem;background:#27cda7;color:#fff;padding:0.5rem 1.5rem;border-radius:30px;text-decoration:none;font-weight:500;">Close</a>';
                    echo '</div>';
                    echo '</div>';
                }
            }
            ?>
        </main>
    </div>
</body>
</html>
