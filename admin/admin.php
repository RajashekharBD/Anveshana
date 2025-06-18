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
            <div class="stats-grid">
                <div class="stat-card users animate-fadeIn">
                    <div class="stat-header">
                        <div>
                            <div class="stat-value">1,248</div>
                            <div class="stat-title">Total Users</div>
                            <div class="stat-change positive">
                                <i class="fas fa-arrow-up"></i>
                                <span>12.5% from last month</span>
                            </div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card bookings animate-fadeIn delay-100">
                    <div class="stat-header">
                        <div>
                            <div class="stat-value">326</div>
                            <div class="stat-title">Today's Bookings</div>
                            <div class="stat-change positive">
                                <i class="fas fa-arrow-up"></i>
                                <span>8.3% from yesterday</span>
                            </div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card revenue animate-fadeIn delay-200">
                    <div class="stat-header">
                        <div>
                            <div class="stat-value">$24,560</div>
                            <div class="stat-title">Total Revenue</div>
                            <div class="stat-change positive">
                                <i class="fas fa-arrow-up"></i>
                                <span>22.1% from last month</span>
                            </div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card pending animate-fadeIn delay-300">
                    <div class="stat-header">
                        <div>
                            <div class="stat-value">18</div>
                            <div class="stat-title">Pending Requests</div>
                            <div class="stat-change negative">
                                <i class="fas fa-arrow-down"></i>
                                <span>2.4% from yesterday</span>
                            </div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Charts Section -->
            <div class="charts-grid">
                <div class="chart-card animate-fadeIn delay-200">
                    <div class="chart-header">
                        <h3 class="chart-title">Booking Trends</h3>
                        <div class="chart-actions">
                            <button title="Daily"><i class="fas fa-calendar-day"></i></button>
                            <button title="Weekly"><i class="fas fa-calendar-week"></i></button>
                            <button title="Monthly" class="active"><i class="fas fa-calendar-alt"></i></button>
                            <button title="Yearly"><i class="fas fa-calendar"></i></button>
                        </div>
                    </div>
                    <div class="chart-placeholder">
                        <span>Monthly booking chart will be displayed here</span>
                    </div>
                </div>
                
                <div class="chart-card animate-fadeIn delay-300">
                    <div class="chart-header">
                        <h3 class="chart-title">User Demographics</h3>
                        <div class="chart-actions">
                            <button title="Age"><i class="fas fa-user-friends"></i></button>
                            <button title="Location"><i class="fas fa-map-marker-alt"></i></button>
                            <button title="Device" class="active"><i class="fas fa-mobile-alt"></i></button>
                        </div>
                    </div>
                    <div class="chart-placeholder">
                        <span>User device distribution chart will be displayed here</span>
                    </div>
                </div>
            </div>
            
            <!-- Recent Activity -->
            <div class="activity-card animate-fadeIn delay-400">
                <h3 class="chart-title">Recent Activity</h3>
                <ul class="activity-list">
                    <li class="activity-item">
                        <img src="https://randomuser.me/api/portraits/women/32.jpg" alt="User" class="activity-avatar">
                        <div class="activity-content">
                            <div class="activity-user">Sarah Johnson</div>
                            <div class="activity-action">Booked a trip to Bali for 2 adults</div>
                            <div class="activity-time">10 minutes ago</div>
                        </div>
                    </li>
                    <li class="activity-item">
                        <img src="https://randomuser.me/api/portraits/men/75.jpg" alt="User" class="activity-avatar">
                        <div class="activity-content">
                            <div class="activity-user">Michael Chen</div>
                            <div class="activity-action">Left a 5-star review for Paris tour</div>
                            <div class="activity-time">25 minutes ago</div>
                        </div>
                    </li>
                    <li class="activity-item">
                        <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="User" class="activity-avatar">
                        <div class="activity-content">
                            <div class="activity-user">Emma Wilson</div>
                            <div class="activity-action">Created a new account</div>
                            <div class="activity-time">1 hour ago</div>
                        </div>
                    </li>
                    <li class="activity-item">
                        <img src="https://randomuser.me/api/portraits/men/41.jpg" alt="User" class="activity-avatar">
                        <div class="activity-content">
                            <div class="activity-user">David Kim</div>
                            <div class="activity-action">Cancelled booking #45821</div>
                            <div class="activity-time">2 hours ago</div>
                        </div>
                    </li>
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