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
            font-family: 'Poppins', sans-serif;
        }
        .admin-container {
            display: flex;
            min-height: 100vh;
        }
        .admin-sidebar {
            background: linear-gradient(135deg, var(--primary), var(--secondary) 80%);
            color: #fff;
            padding: 1.5rem;
            width: 250px;
            box-shadow: 2px 0 18px rgba(95,39,205,0.10);
            min-height: 100vh;
            transition: background 0.3s;
        }
        .sidebar-header {
            display: flex;
            align-items: center;
            margin-bottom: 2rem;
        }
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
        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .sidebar-menu li {
            margin-bottom: 0.25rem;
        }
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
        .admin-main {
            flex: 1;
            padding: 2rem 2.5rem;
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
        .action-btns a {
            margin-right: 10px;
            color: #3498db;
            text-decoration: none;
            font-weight: 500;
        }
        .action-btns a.delete {
            color: #e74c3c;
        }
        /* Properly add action-btns styles */
        .action-btns a { margin-right: 10px; color: #3498db; text-decoration: none; font-weight: 500; }
        .action-btns a.delete { color: #e74c3c; }
        /* ...keep your other table, modal, and form styles as is... */
        /* Bookings Form */
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
            color: var(--dark);
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
            box-shadow: 0 0 0 2px rgba(255,107,107,0.2), 0 2px 8px rgba(44,62,80,0.08);
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

        /* Bookings Table */
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

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: black;
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
                    <a href="settings.php">
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
                            <th>Package Name</th>
                            <th>Mobile</th>
                            <!-- <th>Payment Status</th> -->
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; foreach ($bookings as $booking): ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= htmlspecialchars($booking['name'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($booking['email'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($booking['package_name'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($booking['mobile'] ?? 'N/A') ?></td>
                            <!-- Payment Status column removed -->
                            <td class="action-btns">
                                <a href="bookings.php?id=<?= $booking['id'] ?>" class="action-btns"><i class="fas fa-eye"></i> View</a>
                                <a href="bookings.php?action=delete&id=<?= $booking['id'] ?>" class="delete" onclick="return confirm('Delete this booking?');"><i class="fas fa-trash"></i> Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>


        </main>
    </div>

    <!-- Booking Details Overlay Modal -->
    <?php if (isset($_GET['id'])): ?>
        <?php
        $id = intval($_GET['id']);
        $result = $conn->query("SELECT * FROM bookings WHERE id = $id");
        $booking = $result && $result->num_rows > 0 ? $result->fetch_assoc() : null;
        // Fetch payment details for this booking
        $payment = null;
        $paymentResult = $conn->query("SELECT amount, transaction_id, status, created_at FROM payments WHERE booking_id = $id LIMIT 1");
        if ($paymentResult && $paymentResult->num_rows > 0) {
            $payment = $paymentResult->fetch_assoc();
        }
        ?>
        <style>
        body.modal-open { overflow: hidden; }
        .booking-modal-overlay {
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
        .booking-details-card {
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
        .booking-details-header {
            background: linear-gradient(90deg, #ff6b6b, #5f27cd 90%);
            color: #fff;
            display: flex;
            align-items: center;
            padding: 1.1rem 1.2rem 0.7rem 1.2rem;
            margin: -1px -1.2rem 1.1rem -1.2rem;
            border-radius: 18px 18px 0 0;
            box-shadow: 0 2px 8px rgba(95,39,205,0.08);
        }
        .booking-details-icon {
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
        .booking-details-title {
            font-size: 1.18rem;
            font-weight: 600;
            color: #fff;
            letter-spacing: 0.01em;
        }
        .booking-details-info {
            margin-top: 0.5rem;
        }
        .booking-details-row {
            display: flex;
            align-items: flex-start;
            margin-bottom: 0.38rem;
            font-size: 1.01rem;
            color: #444;
        }
        .booking-details-label {
            min-width: 110px;
            font-weight: 500;
            color: #5f27cd;
            display: flex;
            align-items: center;
            gap: 0.4em;
        }
        .booking-details-row i {
            color: #ff6b6b;
            font-size: 1em;
            margin-right: 0.3em;
        }
        .booking-details-desc {
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
        <div class="booking-modal-overlay" id="bookingModalOverlay">
            <div class="booking-details-card">
                <button class="close-modal-btn" onclick="closeBookingModal()" title="Close"><i class="fas fa-times"></i></button>
                <div class="booking-details-header">
                    <span class="booking-details-icon"><i class="fas fa-calendar-check"></i></span>
                    <span class="booking-details-title">Booking Details</span>
                </div>
                <?php if ($booking): ?>
                <div class="booking-details-info">
                    <div class="booking-details-row"><span class="booking-details-label"><i class="fas fa-user"></i> User Name:</span> <span><?= htmlspecialchars($booking['name'] ?? 'N/A') ?></span></div>
                    <div class="booking-details-row"><span class="booking-details-label"><i class="fas fa-envelope"></i> Email:</span> <span><?= htmlspecialchars($booking['email'] ?? 'N/A') ?></span></div>
                    <div class="booking-details-row"><span class="booking-details-label"><i class="fas fa-box"></i> Package Name:</span> <span><?= htmlspecialchars($booking['package_name'] ?? 'N/A') ?></span></div>
                    <div class="booking-details-row"><span class="booking-details-label"><i class="fas fa-rupee-sign"></i> Price:</span> <span><?= isset($booking['total_price']) ? '₹' . number_format($booking['total_price'], 2) : (isset($booking['price']) ? '₹' . number_format($booking['price'], 2) : 'N/A') ?></span></div>
                    <?php if ($payment): ?>
                        <div class="booking-details-row"><span class="booking-details-label"><i class="fas fa-info-circle"></i> Status:</span> <span><?= htmlspecialchars($payment['status'] ?? 'N/A') ?></span></div>
                        <div class="booking-details-row"><span class="booking-details-label"><i class="fas fa-calendar"></i> Date:</span> <span><?= htmlspecialchars($payment['created_at'] ?? ($booking['created_at'] ?? 'N/A')) ?></span></div>
                        <div class="booking-details-row"><span class="booking-details-label"><i class="fas fa-receipt"></i> Transaction ID:</span> <span><?= htmlspecialchars($payment['transaction_id'] ?? 'N/A') ?></span></div>
                    <?php else: ?>
                        <div class="booking-details-row"><span class="booking-details-label"><i class="fas fa-info-circle"></i> Status:</span> <span>No payment</span></div>
                        <div class="booking-details-row"><span class="booking-details-label"><i class="fas fa-calendar"></i> Date:</span> <span><?= htmlspecialchars($booking['created_at'] ?? 'N/A') ?></span></div>
                        <div class="booking-details-row"><span class="booking-details-label"><i class="fas fa-receipt"></i> Transaction ID:</span> <span>N/A</span></div>
                    <?php endif; ?>
                    <!-- <div class="booking-details-row booking-details-desc"><span class="booking-details-label"><i class="fas fa-align-left"></i> Description:</span> <span><?= htmlspecialchars($booking['description'] ?? 'N/A') ?></span></div> -->
                </div>
                <?php else: ?>
                <div class="booking-details-info">Booking not found.</div>
                <?php endif; ?>
            </div>
        </div>
        <script>
        // Prevent background scroll when modal is open
        document.body.classList.add('modal-open');
        function closeBookingModal() {
            document.body.classList.remove('modal-open');
            window.location.href = 'bookings.php';
        }
        // Close modal on ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeBookingModal();
        });
        // Close modal on click outside card
        document.getElementById('bookingModalOverlay').addEventListener('click', function(e) {
            if (e.target === this) closeBookingModal();
        });
        </script>
    <?php endif; ?>

    <!-- Payment Details Overlay Modal -->
    <?php if (isset($_GET['payment_id'])): ?>
        <?php
        $id = intval($_GET['payment_id']);
        $result = $conn->query("SELECT b.total_price, b.created_at AS booking_date, p.amount, p.transaction_id, p.status FROM bookings b LEFT JOIN payments p ON b.id = p.booking_id WHERE b.id = $id");
        $row = $result && $result->num_rows > 0 ? $result->fetch_assoc() : null;
        ?>
        <style>
        body.modal-open { overflow: hidden; }
        .payment-modal-overlay {
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
        .payment-details-card {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(44,62,80,0.18);
            padding: 2.5rem 2.5rem 2rem 2.5rem;
            max-width: 370px;
            width: 100%;
            text-align: center;
            position: relative;
            overflow: hidden;
            animation: fadeInCard 0.35s;
        }
        .payment-details-card h2 {
            margin-bottom: 0.5rem;
            font-size: 1.5rem;
            font-weight: 600;
            color: #222;
        }
        .payment-details-card .payment-row {
            margin-bottom: 1.1rem;
            color: #666;
            font-size: 1.08rem;
        }
        .payment-details-card .payment-row b {
            color: #5f27cd;
        }
        .payment-details-card .payment-row.date {
            color: #888;
            font-size: 0.98rem;
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
        <div class="payment-modal-overlay" id="paymentModalOverlay">
            <div class="payment-details-card">
                <button class="close-modal-btn" onclick="closePaymentModal()" title="Close"><i class="fas fa-times"></i></button>
                <h2>Payment Status</h2>
                <?php if ($row): ?>
                    <div class="payment-row"><b>Price:</b> <?= isset($row['total_price']) ? '₹' . number_format($row['total_price'], 2) : 'N/A' ?></div>
                    <div class="payment-row date"><b>Date:</b> <?= htmlspecialchars($row['booking_date'] ?? 'N/A') ?></div>
                    <?php if (!empty($row['amount'])): ?>
                        <div class="payment-row"><b>Amount:</b> <?= isset($row['amount']) ? '₹' . number_format($row['amount'], 2) : 'N/A' ?></div>
                        <div class="payment-row"><b>Transaction ID:</b> <?= htmlspecialchars($row['transaction_id'] ?? 'N/A') ?></div>
                        <div class="payment-row"><b>Status:</b> <?= htmlspecialchars($row['status'] ?? 'N/A') ?></div>
                    <?php else: ?>
                        <span style="color:#e67e22;">No payment found for this booking.</span>
                    <?php endif; ?>
                <?php else: ?>
                    <div style="color:#e67e22;">No payment found for this booking.</div>
                <?php endif; ?>
            </div>
        </div>
        <script>
        document.body.classList.add('modal-open');
        function closePaymentModal() {
            document.body.classList.remove('modal-open');
            window.location.href = 'bookings.php';
        }
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closePaymentModal();
        });
        document.getElementById('paymentModalOverlay').addEventListener('click', function(e) {
            if (e.target === this) closePaymentModal();
        });
        </script>
    <?php endif; ?>
</body>
</html>
