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
        :root {
            --primary: #ff6b6b;
            --secondary: #5f27cd;
            --danger: #ffb400;
            --warning: #48dbfb;
            --dark: #22223b;
            --light: #f8f7ff;
            --gray: #a1a1aa;
            --gray-dark: #575366;
            --success: #d1fae5;
            --error: #fee2e2;
        }
        body {
            background: linear-gradient(120deg, #f8fafc 0%, #e0c3fc 100%);
            min-height: 100vh;
            background-attachment: fixed;
            background-repeat: no-repeat;
        }
        .admin-sidebar { background: linear-gradient(135deg, var(--primary), var(--secondary) 80%); color: #fff; padding: 1.5rem; width: 250px; box-shadow: 2px 0 18px rgba(95,39,205,0.10); min-height: 100vh; transition: background 0.3s; }
        .sidebar-header { display: flex; align-items: center; margin-bottom: 2rem; }
        .logo { font-size: 1.5rem; font-weight: 600; color: #fff; display: flex; align-items: center; }
        .logo i { margin-right: 0.5rem; color: var(--secondary); }
        .sidebar-menu a {
            text-decoration: none;
            color: #fff;
            display: flex;
            align-items: center;
            transition: color 0.3s;
            padding: 0.5rem 0;
            font-size: 1.08rem;
            font-weight: 500;
        }
        .sidebar-menu a i {
            margin-right: 0.75rem;
            font-size: 1.2rem;
        }
        .sidebar-menu a.active,
        .sidebar-menu a:active {
            color: var(--primary);
            background: #fff;
            border-radius: 8px;
            padding-left: 0.5rem;
        }
        .sidebar-menu a:hover {
            color: var(--primary);
            background: #fff;
            border-radius: 8px;
            padding-left: 0.5rem;
        }
        .active {
            color: var(--primary) !important;
            font-weight: 600;
        }
        .btn { padding: 0.85rem 2.2rem; background: linear-gradient(90deg, var(--primary), var(--secondary)); color: white; border: none; border-radius: 50px; cursor: pointer; font-family: 'Poppins', sans-serif; font-size: 1.08rem; font-weight: 600; box-shadow: 0 4px 16px rgba(44,62,80,0.10); transition: background 0.3s, transform 0.2s; margin-top: 0.5rem; }
        .btn:hover { background: linear-gradient(90deg, var(--secondary), var(--primary)); transform: translateY(-2px) scale(1.04); }
        /* Properly add action-btns styles */
        .action-btns a { margin-right: 10px; color: #3498db; text-decoration: none; font-weight: 500; }
        .action-btns a.delete { color: #e74c3c; }
    </style>
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
                    <a href="highlights.php">
                        <i class="fas fa-lightbulb"></i>
                        <span>Highlights</span>
                    </a>
                </li>
                <li>
                    <a href="feedback.php">
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
                            <th>Avatar</th>
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
                            <td>
                                <?php if (!empty($user['avatar'])): ?>
                                    <img src="<?= htmlspecialchars($user['avatar']) ?>" alt="Avatar" style="width:38px;height:38px;border-radius:50%;object-fit:cover;box-shadow:0 2px 8px rgba(44,62,80,0.10);">
                                <?php else: ?>
                                    <span style="display:inline-block;width:38px;height:38px;border-radius:50%;background:#eee;text-align:center;line-height:38px;color:#aaa;font-size:1.2em;box-shadow:0 2px 8px rgba(44,62,80,0.10);"><i class="fas fa-user"></i></span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($user['name'] ?? $user['username'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($user['email'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($user['mobile'] ?? 'N/A') ?></td>
                            <td class="action-btns">
                                <a href="users.php?id=<?= $user['id'] ?>" class="action-btns"><i class="fas fa-eye"></i> View</a>
                                <a href="users.php?action=delete&id=<?= $user['id'] ?>" class="delete" onclick="return confirm('Delete this user?');"><i class="fas fa-trash"></i> Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <!-- User Details Overlay Modal -->
            <?php if (isset($_GET['id'])): ?>
                <?php
                $id = intval($_GET['id']);
                $result = $conn->query("SELECT * FROM users WHERE id = $id");
                $user = $result && $result->num_rows > 0 ? $result->fetch_assoc() : null;
                ?>
                <style>
                body.modal-open { overflow: hidden; }
                .user-modal-overlay {
                    position: fixed;
                    top: 0; left: 0; width: 100vw; height: 100vh;
                    background: rgba(44,62,80,0.25);
                    z-index: 1000;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    animation: fadeInOverlay 0.2s;
                }
                @keyframes fadeInOverlay {
                    from { opacity: 0; }
                    to { opacity: 1; }
                }
                .user-details-card {
                    background: #fff;
                    border-radius: 18px;
                    box-shadow: 0 8px 32px rgba(44,62,80,0.18);
                    padding: 0 1.2rem 1.2rem 1.2rem;
                    max-width: 350px;
                    width: 100%;
                    text-align: left;
                    position: relative;
                    overflow: hidden;
                    animation: fadeInCard 0.35s;
                }
                @keyframes fadeInCard {
                    from { opacity: 0; transform: translateY(30px);}
                    to { opacity: 1; transform: translateY(0);}
                }
                .user-details-header {
                    background: linear-gradient(90deg, #ff6b6b, #5f27cd 90%);
                    color: #fff;
                    display: flex;
                    align-items: center;
                    padding: 1.1rem 1.2rem 0.7rem 1.2rem;
                    margin: -1px -1.2rem 1.1rem -1.2rem;
                    border-radius: 18px 18px 0 0;
                    box-shadow: 0 2px 8px rgba(95,39,205,0.08);
                }
                .user-details-icon {
                    background: #fff;
                    color: #5f27cd;
                    border-radius: 50%;
                    width: 38px;
                    height: 38px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 1.3rem;
                    margin-right: 0.7rem;
                    box-shadow: 0 2px 8px rgba(95,39,205,0.10);
                }
                .user-details-title {
                    font-size: 1.18rem;
                    font-weight: 600;
                    color: #fff;
                    letter-spacing: 0.01em;
                }
                .user-details-info {
                    margin-top: 0.5rem;
                }
                .user-details-row {
                    display: flex;
                    align-items: flex-start;
                    margin-bottom: 0.38rem;
                    font-size: 1.01rem;
                    color: #444;
                }
                .user-details-label {
                    min-width: 110px;
                    font-weight: 500;
                    color: #5f27cd;
                    display: flex;
                    align-items: center;
                    gap: 0.4em;
                }
                .user-details-row i {
                    color: #ff6b6b;
                    font-size: 1em;
                    margin-right: 0.3em;
                }
                .user-details-desc {
                    color: #888;
                    font-size: 0.98rem;
                    margin-bottom: 0.2rem;
                    align-items: flex-start;
                }
                .close-modal-btn {
                    position: absolute;
                    top: 12px; right: 16px;
                    background: none;
                    border: none;
                    color: #5f27cd;
                    font-size: 1.5rem;
                    cursor: pointer;
                    z-index: 10;
                    transition: color 0.2s;
                }
                .close-modal-btn:hover {
                    color: #ff6b6b;
                }
                </style>
                <div class="user-modal-overlay" id="userModalOverlay">
                    <div class="user-details-card">
                        <button class="close-modal-btn" onclick="closeUserModal()" title="Close"><i class="fas fa-times"></i></button>
                        <div class="user-details-header">
                            <span class="user-details-icon"><i class="fas fa-user"></i></span>
                            <span class="user-details-title">User Details</span>
                        </div>
                        <?php if ($user): ?>
                        <div class="user-details-info">
                            <div class="user-details-row"><span class="user-details-label"><i class="fas fa-user"></i> Name:</span> <span><?= htmlspecialchars($user['name'] ?? 'N/A') ?></span></div>
                            <div class="user-details-row"><span class="user-details-label"><i class="fas fa-envelope"></i> Email:</span> <span><?= htmlspecialchars($user['email'] ?? 'N/A') ?></span></div>
                            <div class="user-details-row"><span class="user-details-label"><i class="fas fa-phone"></i> Mobile:</span> <span><?= htmlspecialchars($user['mobile'] ?? 'N/A') ?></span></div>
                            <div class="user-details-row"><span class="user-details-label"><i class="fas fa-map-marker-alt"></i> Address:</span> <span><?= htmlspecialchars($user['address'] ?? 'N/A') ?></span></div>
                            <div class="user-details-row user-details-desc"><span class="user-details-label"><i class="fas fa-calendar"></i> Registered:</span> <span><?= htmlspecialchars($user['created_at'] ?? 'N/A') ?></span></div>
                        </div>
                        <?php else: ?>
                        <div class="user-details-info">User not found.</div>
                        <?php endif; ?>
                    </div>
                </div>
                <script>
                // Prevent background scroll when modal is open
                document.body.classList.add('modal-open');
                function closeUserModal() {
                    document.body.classList.remove('modal-open');
                    window.location.href = 'users.php';
                }
                // Close modal on ESC
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') closeUserModal();
                });
                // Close modal on click outside card
                document.getElementById('userModalOverlay').addEventListener('click', function(e) {
                    if (e.target === this) closeUserModal();
                });
                </script>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
