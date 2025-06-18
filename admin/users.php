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

// Handle delete
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $conn->query("DELETE FROM users WHERE id = $id");
    header('Location: users.php?success=deleted');
    exit();
}

// Fetch all users
$users = [];
$result = $conn->query("SELECT * FROM users ORDER BY id DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users | Anveshana</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .users-table {
            width: 100%;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 4px 24px rgba(44,62,80,0.10);
            border-collapse: separate;
            border-spacing: 0;
            overflow: hidden;
            margin-bottom: 2rem;
        }
        .users-table th, .users-table td {
            padding: 16px 14px;
            text-align: left;
            font-size: 1.05rem;
            vertical-align: middle;
        }
        .users-table th {
            background: linear-gradient(90deg,rgba(197, 205, 47, 1),rgba(39, 205, 175, 1) 90%);
            color: #fff;
            font-weight: 600;
            border: none;
        }
        .users-table tr:not(:last-child) {
            border-bottom: 1px solid #f0f0f0;
        }
        .users-table tr {
            transition: background 0.2s;
        }
        .users-table tr:hover {
            background: #f8f7ff;
        }
        .btn.btn-view {
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
        .btn.btn-view:hover {
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
                    <a href="users.php" class="active">
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
        <main class="admin-main">
            <header class="admin-header">
                <div class="page-title">
                    <h1>Manage Users</h1>
                    <p>View, inspect, and manage all users</p>
                </div>
            </header>
            <?php
            if (isset($_GET['success']) && $_GET['success'] === 'deleted') {
                echo '<div class="alert alert-success">User deleted successfully!</div>';
            }
            ?>
            <div style="overflow-x:auto;">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>S.No.</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; foreach ($users as $user): ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= htmlspecialchars($user['name'] ?? $user['username'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($user['email'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($user['mobile'] ?? 'N/A') ?></td>
                            <td>
                                <a href="users.php?action=view&id=<?= $user['id'] ?>" class="btn btn-view">View</a>
                                <a href="users.php?action=delete&id=<?= $user['id'] ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php
            // View user details modal/section
            if (isset($_GET['action']) && $_GET['action'] === 'view' && isset($_GET['id'])) {
                $id = intval($_GET['id']);
                $result = $conn->query("SELECT * FROM users WHERE id = $id");
                if ($result && $result->num_rows > 0) {
                    $user = $result->fetch_assoc();
                    echo '<div style="display:flex;justify-content:center;align-items:center;min-height:60vh;">';
                    echo '<div style="background:#fff;border-radius:18px;box-shadow:0 4px 24px rgba(44,62,80,0.13);padding:2.5rem 2.5rem 2rem 2.5rem;max-width:370px;width:100%;text-align:center;">';
                    if (!empty($user['avatar'])) {
                        echo '<img src="' . htmlspecialchars($user['avatar']) . '" alt="Avatar" style="width:90px;height:90px;object-fit:cover;border-radius:50%;margin-bottom:1.2rem;border:3px solid #27cda7;background:#f8f8f8;">';
                    } else {
                        echo '<div style="width:90px;height:90px;border-radius:50%;background:#f8f8f8;border:3px solid #27cda7;display:flex;align-items:center;justify-content:center;margin:0 auto 1.2rem auto;font-size:2.5rem;color:#bdbdbd;"><i class="fas fa-user"></i></div>';
                    }
                    echo '<h2 style="margin-bottom:0.5rem;font-size:1.5rem;font-weight:600;color:#222;">' . htmlspecialchars($user['name'] ?? $user['username'] ?? 'N/A') . '</h2>';
                    echo '<div style="margin-bottom:1.1rem;color:#666;font-size:1.08rem;">' . htmlspecialchars($user['email'] ?? 'N/A') . '</div>';
                    if (isset($user['mobile'])) echo '<div style="margin-bottom:1.1rem;color:#666;font-size:1.08rem;"><i class="fas fa-phone" style="margin-right:7px;color:#27cda7;"></i>' . htmlspecialchars($user['mobile']) . '</div>';
                    if (isset($user['created_at'])) echo '<div style="margin-bottom:1.1rem;color:#888;font-size:0.98rem;"><i class="fas fa-calendar-alt" style="margin-right:7px;color:#c5cd2f;"></i>Joined: ' . htmlspecialchars($user['created_at']) . '</div>';
                    echo '<a href="users.php" class="btn btn-edit" style="margin-top:1rem;background:#27cda7;color:#fff;padding:0.5rem 1.5rem;border-radius:30px;text-decoration:none;font-weight:500;">Close</a>';
                    echo '</div>';
                    echo '</div>';
                }
            }
            ?>
        </main>
    </div>
</body>
</html>
