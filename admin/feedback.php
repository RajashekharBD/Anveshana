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

// Fetch all feedback
$feedbacks = [];
$result = $conn->query("SELECT * FROM feedback ORDER BY created_at DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $feedbacks[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback | Anveshana</title>
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
        .feedback-table {
            width: 100%;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 4px 24px rgba(44,62,80,0.10);
            border-collapse: separate;
            border-spacing: 0;
            overflow: hidden;
            margin-bottom: 2rem;
        }
        .feedback-table th, .feedback-table td {
            padding: 16px 14px;
            text-align: left;
            font-size: 1.05rem;
            vertical-align: middle;
        }
        .feedback-table th {
            background: linear-gradient(90deg,rgba(197, 205, 47, 1),rgba(39, 205, 175, 1) 90%);
            color: #fff;
            font-weight: 600;
            border: none;
        }
        .feedback-table tr:not(:last-child) {
            border-bottom: 1px solid #f0f0f0;
        }
        .feedback-table tr {
            transition: background 0.2s;
        }
        .feedback-table tr:hover {
            background: #f8f7ff;
        }
        .feedback-message {
            max-width: 350px;
            word-break: break-word;
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
                <li><a href="admin.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                <li><a href="users.php"><i class="fas fa-users"></i><span>Users</span></a></li>
                <li><a href="bookings.php"><i class="fas fa-calendar-check"></i><span>Bookings</span></a></li>
                <li><a href="destination.php"><i class="fas fa-map-marked-alt"></i><span>Destinations</span></a></li>
                <li><a href="packages.php"><i class="fas fa-box"></i><span>Packages</span></a></li>
                <li><a href="highlights.php"><i class="fas fa-lightbulb"></i><span>Highlights</span></a></li>
                <li><a href="#" class="active"><i class="fas fa-comment-alt"></i><span>Feedback</span></a></li>
                <li><a href="#"><i class="fas fa-cog"></i><span>Settings</span></a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li>
            </ul>
        </aside>
        <main class="admin-main">
            <header class="admin-header">
                <div class="page-title">
                    <h1>Customer Feedback</h1>
                    <p>See what your customers are saying</p>
                </div>
            </header>
            <div style="overflow-x:auto;">
                <table class="feedback-table">
                    <thead>
                        <tr>
                            <th>S.No.</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Message</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($feedbacks)): ?>
                            <tr><td colspan="5" style="text-align:center;color:#888;">No feedback received yet.</td></tr>
                        <?php else: ?>
                            <?php $i=1; foreach ($feedbacks as $fb): ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= htmlspecialchars($fb['name'] ?? '') ?></td>
                                <td><?= htmlspecialchars($fb['email'] ?? '') ?></td>
                                <td class="feedback-message"><?= nl2br(htmlspecialchars($fb['message'] ?? '')) ?></td>
                                <td><?= htmlspecialchars($fb['created_at'] ?? '') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
