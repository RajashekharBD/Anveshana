<?php
session_start();
// Authentication check (uncomment when ready)
// if (!isset($_SESSION['email']) || $_SESSION['email'] !== 'admin') {
//     header('Location: login.php');
//     exit();
// }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | Anveshana</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        :root {
            --primary: #ff6b6b;
            --secondary: #5f27cd;
            --danger: #ffb400;
            --warning: #495456ff;
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
        
        .admin-sidebar {     background: linear-gradient(135deg, var(--primary), var(--secondary) 80%);
    color: #fff;
    padding: 1.5rem;
    width: 250px;
    box-shadow: 2px 0 18px rgba(95,39,205,0.10);
    min-height: 100vh;
    transition: background 0.3s;
}
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
                    <a href="#" class="active">
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
                    <h1>Dashboard Overview</h1>
                    <p>Welcome back, Admin! Here's what's happening with your platform today.</p>
                </div>
                
                <div class="user-profile">
                    <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Admin" class="user-avatar">
                    <div class="user-info">
                        <h4>Admin User</h4>
                        <p>Super Administrator</p>
                    </div>
                </div>
            </header>
            
            <!-- Stats Cards -->
            <?php
            // Fetch dashboard stats from DB
            $db_host = 'localhost';
            $db_user = 'root';
            $db_pass = '';
            $db_name = 'anveshana_admin';
            $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
            $total_users = 0;
            $todays_bookings = 0;
            $total_revenue = 0;
            $pending_requests = 0;
            if (!$conn->connect_error) {
                // Total users
                $res = $conn->query("SELECT COUNT(*) as cnt FROM users");
                if ($res && $row = $res->fetch_assoc()) $total_users = $row['cnt'];
                // Today's bookings
                $today = date('Y-m-d');
                $res = $conn->query("SELECT COUNT(*) as cnt FROM bookings WHERE DATE(created_at) = '$today'");
                if ($res && $row = $res->fetch_assoc()) $todays_bookings = $row['cnt'];
                // Total revenue
                $res = $conn->query("SELECT SUM(total_price) as sum FROM bookings");
                if ($res && $row = $res->fetch_assoc()) $total_revenue = $row['sum'] ? $row['sum'] : 0;
                // Pending requests (example: bookings with a 'pending' status if you have such a column)
                $pending_requests = 0; // Update this if you have a status column
                $conn->close();
            }
            ?>
            <div class="stats-grid">
                <div class="stat-card users animate-fadeIn">
                    <div class="stat-header">
                        <div>
                            <div class="stat-value"><?= number_format($total_users) ?></div>
                            <div class="stat-title">Total Users</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
                <div class="stat-card bookings animate-fadeIn delay-100">
                    <div class="stat-header">
                        <div>
                            <div class="stat-value"><?= number_format($todays_bookings) ?></div>
                            <div class="stat-title">Today's Bookings</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                    </div>
                </div>
                <div class="stat-card revenue animate-fadeIn delay-200">
                    <div class="stat-header">
                        <div>
                            <div class="stat-value">₹<?= number_format($total_revenue, 2) ?></div>
                            <div class="stat-title">Total Revenue</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                </div>
                <div class="stat-card pending animate-fadeIn delay-300">
                    <div class="stat-header">
                        <div>
                            <div class="stat-value"><?= number_format($pending_requests) ?></div>
                            <div class="stat-title">Pending Requests</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Charts Section -->
            <div class="charts-grid" style="display: flex; justify-content: center; align-items: flex-start;">
                <div class="chart-card animate-fadeIn delay-200" style="min-width: 400px; max-width: 800px; width: 100%; margin: 0 auto;">
                    <div class="chart-header">
                        <h3 class="chart-title">Booking Trends</h3>
                        <div class="chart-actions">
                            <button title="Daily" id="trend-daily" class="active"><i class="fas fa-calendar-day"></i></button>
                        </div>
                    </div>
                    <canvas id="bookingTrendsChart" height="120"></canvas>
                    <?php
                    // Get last 7 days booking counts
                    $db_host = 'localhost';
                    $db_user = 'root';
                    $db_pass = '';
                    $db_name = 'anveshana_admin';
                    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
                    $labels = [];
                    $counts = [];
                    if (!$conn->connect_error) {
                        for ($i = 6; $i >= 0; $i--) {
                            $date = date('Y-m-d', strtotime("-$i days"));
                            $labels[] = date('D', strtotime($date));
                            $sql = "SELECT COUNT(*) as cnt FROM bookings WHERE DATE(created_at) = '$date'";
                            $res = $conn->query($sql);
                            $cnt = 0;
                            if ($res && $row = $res->fetch_assoc()) $cnt = $row['cnt'];
                            $counts[] = $cnt;
                        }
                        $conn->close();
                    }
                    ?>
                    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                    <script>
                    const bookingTrendsCtx = document.getElementById('bookingTrendsChart').getContext('2d');
                    new Chart(bookingTrendsCtx, {
                        type: 'line',
                        data: {
                            labels: <?php echo json_encode($labels); ?>,
                            datasets: [{
                                label: 'Bookings',
                                data: <?php echo json_encode($counts); ?>,
                                borderColor: '#5f27cd',
                                backgroundColor: 'rgba(95,39,205,0.1)',
                                fill: true,
                                tension: 0.3
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: { legend: { display: false } },
                            scales: { y: { beginAtZero: true, precision: 0 } }
                        }
                    });
                    </script>
                </div>
            </div>
            
            <!-- Recent Activity -->
            <div class="activity-card animate-fadeIn delay-400">
                <h3 class="chart-title">Recent Activity</h3>
                <ul class="activity-list">
                <?php
                // Show latest 5 activities: signups (users), logins (if tracked), bookings
                $db_host = 'localhost';
                $db_user = 'root';
                $db_pass = '';
                $db_name = 'anveshana_admin';
                $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
                if (!$conn->connect_error) {
                    // Fetch signups (users) and bookings, merge and sort by time
                    $signup_sql = "SELECT name, email, created_at, 'signup' as type FROM users";
                    $booking_sql = "SELECT name, package_name, destination_name, people, created_at, 'booking' as type FROM bookings";
                    $signup_res = $conn->query($signup_sql);
                    $booking_res = $conn->query($booking_sql);
                    $activities = [];
                    if ($signup_res) {
                        while ($row = $signup_res->fetch_assoc()) {
                            $activities[] = [
                                'type' => 'signup',
                                'name' => $row['name'],
                                'email' => $row['email'],
                                'created_at' => $row['created_at']
                            ];
                        }
                    }
                    if ($booking_res) {
                        while ($row = $booking_res->fetch_assoc()) {
                            $activities[] = [
                                'type' => 'booking',
                                'name' => $row['name'],
                                'package_name' => $row['package_name'],
                                'destination_name' => $row['destination_name'],
                                'people' => $row['people'],
                                'created_at' => $row['created_at']
                            ];
                        }
                    }
                    // Sort all activities by created_at desc
                    usort($activities, function($a, $b) {
                        return strtotime($b['created_at']) - strtotime($a['created_at']);
                    });
                    $activities = array_slice($activities, 0, 5);
                    if (count($activities) > 0) {
                        foreach ($activities as $act) {
                            $user_avatar = 'https://randomuser.me/api/portraits/lego/1.jpg';
                            $created_at = $act['created_at'];
                            // Use Asia/Kolkata timezone for both now and created_at
                            $now = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
                            $created = new DateTime($created_at, new DateTimeZone('Asia/Kolkata'));
                            $diff = $now->getTimestamp() - $created->getTimestamp();
                            if ($diff < 0) {
                                $time_ago = 'just now';
                            } elseif ($diff < 60) {
                                $time_ago = $diff . ' second' . ($diff == 1 ? '' : 's') . ' ago';
                            } elseif ($diff < 3600) {
                                $mins = floor($diff/60);
                                $time_ago = $mins . ' minute' . ($mins == 1 ? '' : 's') . ' ago';
                            } elseif ($diff < 86400) {
                                $hrs = floor($diff/3600);
                                $time_ago = $hrs . ' hour' . ($hrs == 1 ? '' : 's') . ' ago';
                            } else {
                                $days = floor($diff/86400);
                                $time_ago = $days . ' day' . ($days == 1 ? '' : 's') . ' ago';
                            }
                            echo '<li class="activity-item">';
                            echo '<img src="' . htmlspecialchars($user_avatar) . '" alt="User" class="activity-avatar">';
                            echo '<div class="activity-content">';
                            echo '<div class="activity-user">' . htmlspecialchars($act['name']) . '</div>';
                            if ($act['type'] === 'signup') {
                                echo '<div class="activity-action">Signed up with email <b>' . htmlspecialchars($act['email']) . '</b></div>';
                            } elseif ($act['type'] === 'booking') {
                                $package = $act['package_name'] ? $act['package_name'] : ($act['destination_name'] ?? '');
                                $people = $act['people'] ?? 1;
                                echo '<div class="activity-action">Booked <b>' . htmlspecialchars($package) . '</b> for ' . intval($people) . ' ' . (intval($people) > 1 ? 'people' : 'person') . '</div>';
                            }
                            echo '<div class="activity-time">' . $time_ago . '</div>';
                            echo '</div>';
                            echo '</li>';
                        }
                    } else {
                        echo '<li class="activity-item"><div class="activity-content">No recent activity found.</div></li>';
                    }
                    $conn->close();
                } else {
                    echo '<li class="activity-item"><div class="activity-content">Could not connect to database.</div></li>';
                }
                ?>
                </ul>
            </div>
            
            <!-- Quick Actions -->
            <div class="quick-actions">
                <a href="destination.php?action=add" class="action-btn animate-fadeIn" style="text-decoration: none;">
                    <i class="fas fa-plus-circle"></i>
                    <span>Add Destination</span>
                </a>
                <button class="action-btn animate-fadeIn delay-100">
                    <i class="fas fa-user-plus"></i>
                    <span>Create User</span>
                </button>
                <button class="action-btn animate-fadeIn delay-200">
                    <i class="fas fa-chart-pie"></i>
                    <span>View Reports</span>
                </button>
                <button class="action-btn animate-fadeIn delay-300">
                    <i class="fas fa-bell"></i>
                    <span>Notifications</span>
                </button>
            </div>
        </main>
    </div>

    <script>
        // Simple animation trigger - in a real app you might use something like GSAP or AOS
        document.addEventListener('DOMContentLoaded', function() {
            const animatedElements = document.querySelectorAll('.animate-fadeIn');
            
            animatedElements.forEach(el => {
                // Trigger reflow to restart animation
                void el.offsetWidth;
                el.style.opacity = 1;
            });
            
            // Add hover effects to cards
            const cards = document.querySelectorAll('.stat-card, .chart-card, .activity-card');
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