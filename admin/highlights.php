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
$error = '';
$success = '';
$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $destination_id = intval($_POST['destination_id'] ?? 0);
    $icon = trim($_POST['icon'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    if (!$destination_id || !$icon || !$title || !$description) {
        $error = 'All fields are required.';
    } else {
        if ($action === 'edit' && isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $stmt = $conn->prepare('UPDATE highlights SET destination_id=?, icon=?, title=?, description=? WHERE id=?');
            $stmt->bind_param('isssi', $destination_id, $icon, $title, $description, $id);
            if ($stmt->execute()) {
                $success = 'Highlight updated successfully!';
            } else {
                $error = 'Database error: ' . $stmt->error;
            }
            $stmt->close();
        } else {
            $stmt = $conn->prepare('INSERT INTO highlights (destination_id, icon, title, description) VALUES (?, ?, ?, ?)');
            $stmt->bind_param('isss', $destination_id, $icon, $title, $description);
            if ($stmt->execute()) {
                $success = 'Highlight added successfully!';
            } else {
                $error = 'Database error: ' . $stmt->error;
            }
            $stmt->close();
        }
    }
}

// Handle delete
if ($action === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $conn->query('DELETE FROM highlights WHERE id = ' . $id);
    header('Location: highlights.php?success=deleted');
    exit();
}

// Fetch all highlights
$highlights = [];
$result = $conn->query('SELECT h.*, d.name as destination_name FROM highlights h JOIN destinations d ON h.destination_id = d.id ORDER BY h.id DESC');
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $highlights[] = $row;
    }
}

// Fetch all destinations for dropdown
$destinations = [];
$res = $conn->query('SELECT id, name FROM destinations ORDER BY name');
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $destinations[] = $row;
    }
}

// If editing, fetch the highlight
$edit_highlight = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = $conn->query('SELECT * FROM highlights WHERE id = ' . $id);
    if ($result && $result->num_rows > 0) {
        $edit_highlight = $result->fetch_assoc();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Highlights | Anveshana</title>
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
        body { background: linear-gradient(120deg, #f8fafc 0%, #e0c3fc 100%); min-height: 100vh; background-attachment: fixed; background-repeat: no-repeat; }
        .admin-container { display: flex; font-family: 'Poppins', sans-serif; }
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
        .admin-main { flex: 1; padding: 2rem; }
        .admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .page-title { font-size: 1.8rem; font-weight: 600; color: #333; }
        .user-profile { display: flex; align-items: center; }
        .user-avatar { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; margin-right: 0.75rem; }
        .highlight-form { background: rgba(255,255,255,0.85); border-radius: 18px; padding: 2.5rem 2rem; box-shadow: 0 8px 32px rgba(44,62,80,0.18); max-width: 600px; margin: 2.5rem auto; backdrop-filter: blur(2px); border: 1.5px solid rgba(255,255,255,0.25); transition: box-shadow 0.3s, transform 0.3s; position: relative; overflow: hidden; }
        .highlight-form::before { content: ''; position: absolute; top: -60px; left: -60px; width: 180px; height: 180px; background: radial-gradient(circle, var(--secondary) 0%, transparent 70%); opacity: 0.12; z-index: 0; }
        .form-group { margin-bottom: 1.7rem; position: relative; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--secondary); letter-spacing: 0.02em; }
        .form-control { width: 100%; padding: 0.85rem 1.1rem 0.85rem 2.5rem; border: 1.5px solid var(--gray); border-radius: 6px; font-family: 'Poppins', sans-serif; font-size: 1.05rem; background: rgba(255,255,255,0.7); transition: border 0.2s, box-shadow 0.2s; outline: none; position: relative; }
        .form-control:focus { border: 1.5px solid var(--primary); box-shadow: 0 0 0 2px var(--primary), 0 2px 8px rgba(44,62,80,0.08); }
        .form-group i { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--gray-dark); font-size: 1.1rem; opacity: 0.7; z-index: 2; }
        .btn { padding: 0.85rem 2.2rem; background: linear-gradient(90deg, var(--primary), var(--secondary)); color: white; border: none; border-radius: 50px; cursor: pointer; font-family: 'Poppins', sans-serif; font-size: 1.08rem; font-weight: 600; box-shadow: 0 4px 16px rgba(44,62,80,0.10); transition: background 0.3s, transform 0.2s; margin-top: 0.5rem; }
        .btn:hover { background: linear-gradient(90deg, var(--secondary), var(--primary)); transform: translateY(-2px) scale(1.04); }
        .alert { padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem; font-weight: 500; letter-spacing: 0.01em; }
        .alert-success { background: #d1fae5; color: #065f46; }
        .alert-error { background: #fee2e2; color: #b91c1c; }
        .highlights-table { width: 100%; background: #fff; border-radius: 14px; box-shadow: 0 4px 24px rgba(44,62,80,0.10); border-collapse: separate; border-spacing: 0; overflow: hidden; margin-bottom: 2rem; }
        .highlights-table th, .highlights-table td { padding: 16px 14px; text-align: left; font-size: 1.05rem; vertical-align: middle; }
        .highlights-table th { background: linear-gradient(90deg,rgba(197, 205, 47, 1),rgba(39, 205, 175, 1) 90%); color: #fff; font-weight: 600; border: none; }
        .highlights-table tr:not(:last-child) { border-bottom: 1px solid #f0f0f0; }
        .highlights-table tr { transition: background 0.2s; }
        .highlights-table tr:hover { background: #f8f7ff; }
        .action-btns a { margin-right: 10px; color: #3498db; text-decoration: none; font-weight: 500; }
        .action-btns a.delete { color: #e74c3c; }
        @media (max-width: 700px) { .highlight-form { padding: 1.2rem 0.5rem; max-width: 98vw; } }
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
                <li><a href="packages.php"><i class="fas fa-box"></i><span>Packages</span></a></li>
                <li><a href="highlights.php" class="active"><i class="fas fa-lightbulb"></i><span>Highlights</span></a></li>
                <li><a href="feedback.php"><i class="fas fa-comment-alt"></i><span>Feedback</span></a></li>
                <li><a href="#"><i class="fas fa-cog"></i><span>Settings</span></a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li>
            </ul>
        </aside>
        <!-- Main Content -->
        <main class="admin-main">
            <header class="admin-header">
                <div class="page-title">
                    <h1><?= ($action === 'edit') ? 'Edit Highlight' : 'Manage Highlights' ?></h1>
                    <p><?= ($action === 'edit') ? 'Update the details of the highlight' : 'Add, edit, or remove destination highlights' ?></p>
                </div>
                <div class="user-profile">
                    <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Admin" class="user-avatar">
                    <div class="user-info">
                        <h4>Admin User</h4>
                        <p>Super Administrator</p>
                    </div>
                </div>
            </header>
            <?php if (isset($_GET['success']) && $_GET['success'] === 'deleted'): ?>
                <div class="alert alert-success">Highlight deleted successfully!</div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-error"><?= $error ?></div>
            <?php endif; ?>
            <?php if ($action === 'add' || ($action === 'edit' && isset($_GET['id']))): ?>
            <div class="highlight-form">
                <form action="highlights.php<?= ($action === 'edit' && isset($_GET['id'])) ? '?action=edit&id=' . intval($_GET['id']) : '?action=add' ?>" method="POST">
                    <div class="form-group">
                        <label for="destination_id">Destination</label>
                        <select id="destination_id" name="destination_id" class="form-control" required>
                            <option value="">Select Destination</option>
                            <?php foreach ($destinations as $dest): ?>
                                <option value="<?= $dest['id'] ?>" <?= (isset($edit_highlight) && $edit_highlight['destination_id'] == $dest['id']) ? 'selected' : '' ?>><?= htmlspecialchars($dest['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="icon">Icon (FontAwesome class, e.g., <code>fas fa-tree</code>)</label>
                        <input type="text" id="icon" name="icon" class="form-control" required value="<?= isset($edit_highlight) ? htmlspecialchars($edit_highlight['icon']) : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" id="title" name="title" class="form-control" required value="<?= isset($edit_highlight) ? htmlspecialchars($edit_highlight['title']) : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" class="form-control" required><?= isset($edit_highlight) ? htmlspecialchars($edit_highlight['description']) : '' ?></textarea>
                    </div>
                    <button type="submit" class="btn"><?= ($action === 'edit') ? 'Update Highlight' : 'Add Highlight' ?></button>
                    <?php if ($action === 'edit'): ?>
                        <a href="highlights.php" class="btn" style="background:#e74c3c;margin-left:10px;">Cancel</a>
                    <?php endif; ?>
                </form>
            </div>
            <?php else: ?>
            <div style="margin-bottom: 2rem; text-align:right;">
                <a href="highlights.php?action=add" class="btn" style="text-decoration:none;">Add New Highlight</a>
            </div>
            <div style="overflow-x:auto;">
                <table class="highlights-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Destination</th>
                            <th>Icon</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($highlights)): ?>
                            <tr><td colspan="6" style="text-align:center;color:#888;">No highlights found.</td></tr>
                        <?php else: foreach ($highlights as $hl): ?>
                        <tr>
                            <td><?= $hl['id'] ?></td>
                            <td><?= htmlspecialchars($hl['destination_name']) ?></td>
                            <td><i class="<?= htmlspecialchars($hl['icon']) ?>"></i> <span style="font-size:0.9em;opacity:0.7;">(<?= htmlspecialchars($hl['icon']) ?>)</span></td>
                            <td><?= htmlspecialchars($hl['title']) ?></td>
                            <td><?= htmlspecialchars($hl['description']) ?></td>
                            <td class="action-btns">
                                <a href="highlights.php?action=edit&id=<?= $hl['id'] ?>"><i class="fas fa-edit"></i> Edit</a>
                                <a href="highlights.php?action=delete&id=<?= $hl['id'] ?>" class="delete" onclick="return confirm('Delete this highlight?');"><i class="fas fa-trash"></i> Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
