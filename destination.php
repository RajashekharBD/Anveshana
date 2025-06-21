<?php
session_start();
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'anveshana_admin';
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

// Get filter values from GET
$region = $_GET['region'] ?? '';
$travel_type = $_GET['type'] ?? '';
$best_season = $_GET['season'] ?? '';

// Build query for new columns
$sql = "SELECT * FROM destinations WHERE 1=1";
$params = [];
$types = '';
if ($region) {
    $sql .= " AND region = ?";
    $params[] = $region;
    $types .= 's';
}
if ($travel_type) {
    $sql .= " AND travel_type = ?";
    $params[] = $travel_type;
    $types .= 's';
}
if ($best_season) {
    $sql .= " AND best_season = ?";
    $params[] = $best_season;
    $types .= 's';
}
$sql .= " ORDER BY id DESC";
$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$destinations = [];
while ($row = $result->fetch_assoc()) {
    $destinations[] = $row;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/destination.css">
    <title>Destinations</title>
    <style>
    .destination-desc {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        min-height: 2.6em;
        position: relative;
        margin-bottom: 0.5em;
        transition: all 0.2s;
        white-space: normal;
    }
    .destination-desc.expanded {
        display: block;
        -webkit-line-clamp: unset;
        max-height: none;
        overflow: visible;
    }
    .more-link {
        color: #007bff;
        cursor: pointer;
        font-size: 1em;
        display: inline;
        margin: 0 0 0 0.3em;
        background: none;
        border: none;
        padding: 0;
        text-decoration: underline;
        font-family: inherit;
        font-weight: normal;
        outline: none;
        box-shadow: none;
        transition: color 0.2s;
    }
    .more-link:hover,
    .more-link:focus {
        color: #0056b3;
        text-decoration: underline;
    }
    nav { position: fixed; top: 0; left: 0; width: 100%; padding: 20px 50px; display: flex; justify-content: space-between; align-items: center; z-index: 1000; background-color: rgba(255, 255, 255, 0.95); box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1); }
    .nav-logo { font-size: 1.8rem; font-weight: 700; color: #2c3e50; text-decoration: none; }
    .nav-links { display: flex; gap: 30px; }
    .nav-links a { color: #2c3e50; text-decoration: none; font-weight: 500; transition: all 0.3s ease; position: relative; }
    .nav-links a:hover { color: #2ecc71; }
    .nav-links a::after { content: ''; position: absolute; bottom: -5px; left: 0; width: 0; height: 2px; background-color: #2ecc71; transition: width 0.3s ease; }
    .nav-links a:hover::after { width: 100%; }
    body { padding-top: 90px; }
    .user-dropdown {
        position: relative;
        display: inline-block;
    }
    .user-dropbtn {
        background: none;
        border: none;
        color: #2c3e50;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        padding: 0 16px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .user-dropbtn:focus {
        outline: none;
    }
    .user-dropdown-content {
        display: none;
        position: absolute;
        right: 0;
        background-color: #fff;
        min-width: 160px;
        box-shadow: 0 8px 16px rgba(0,0,0,0.15);
        z-index: 1;
        border-radius: 8px;
        margin-top: 8px;
        overflow: hidden;
    }
    .user-dropdown-content a {
        color: #2c3e50;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
        transition: background 0.2s;
    }
    .user-dropdown-content a:hover {
        background-color: #f1f1f1;
    }
    .user-dropdown.show .user-dropdown-content {
        display: block;
    }
    .user-btn {
        background: linear-gradient(90deg, #3498db 0%, #2ecc71 100%);
        color: #fff !important;
        border: none;
        border-radius: 24px;
        padding: 8px 22px;
        font-size: 1.05rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 2px 8px rgba(44,62,80,0.08);
        transition: background 0.2s, box-shadow 0.2s;
        cursor: pointer;
    }
    .user-btn:hover, .user-btn:focus {
        background: linear-gradient(90deg, #2ecc71 0%, #3498db 100%);
        box-shadow: 0 4px 16px rgba(44,62,80,0.13);
        color: #fff !important;
    }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav id="navbar">
        <a href="index.html" class="nav-logo">Anveshana</a>
        <div class="nav-links">
            <a href="index.html#features">Features</a>
            <a href="index.html#destinations">Destinations</a>
            <a href="index.html#testimonials">Testimonials</a>
            <a href="index.html#contact">Contact</a>
            <?php
            if (!isset($_SESSION['userid'])) {
                // Not logged in
                echo '<a href="login.php" class="btn-primary">Get Started</a>';
            } else {
                // Logged in, fetch user name and avatar
                $user_id = $_SESSION['userid'];
                $user_name = 'User';
                $avatar = '';
                $db_host = 'localhost';
                $db_user = 'root';
                $db_pass = '';
                $db_name = 'anveshana_admin';
                $conn2 = new mysqli($db_host, $db_user, $db_pass, $db_name);
                if (!$conn2->connect_error) {
                    $stmt2 = $conn2->prepare("SELECT name, avatar FROM users WHERE id = ?");
                    $stmt2->bind_param("i", $user_id);
                    $stmt2->execute();
                    $stmt2->bind_result($user_name_db, $avatar_db);
                    if ($stmt2->fetch()) {
                        $user_name = htmlspecialchars($user_name_db);
                        $avatar = !empty($avatar_db) ? htmlspecialchars($avatar_db) : '';
                    }
                    $stmt2->close();
                    $conn2->close();
                }
            ?>
            <div class="user-dropdown" id="userDropdown">
                <button class="user-dropbtn user-btn" id="userDropBtn">
                    <?php if ($avatar): ?>
                        <img src="<?= $avatar ?>" alt="Avatar" style="width:32px;height:32px;border-radius:50%;object-fit:cover;margin-right:8px;">
                    <?php else: ?>
                        <i class="fas fa-user-circle" style="font-size:32px;margin-right:8px;"></i>
                    <?php endif; ?>
                    <span><?= $user_name ?></span>
                    <i class="fas fa-caret-down"></i>
                </button>
                <div class="user-dropdown-content" id="userDropdownContent">
                    <a href="profile.php">View Profile</a>
                    <a href="logout.php">Logout</a>
                </div>
            </div>
            <script>
                // Dropdown toggle
                document.addEventListener('DOMContentLoaded', function() {
                    var btn = document.getElementById('userDropBtn');
                    var dropdown = document.getElementById('userDropdown');
                    btn && btn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        dropdown.classList.toggle('show');
                    });
                    document.addEventListener('click', function() {
                        dropdown.classList.remove('show');
                    });
                });
            </script>
            <?php } ?>
        </div>
    </nav>
    <div class="main">
        <section class="destinations" id="destinations">
            <h2 class="section-title">Popular Destinations</h2>
            <!-- Filter Section -->
            <section class="filter-section">
                <form method="get" class="filter-container">
                    <div class="filter-group">
                        <label for="region" class="filter-label">Region</label>
                        <select id="region" name="region" class="filter-select">
                            <option value="">All Regions</option>
                            <option value="north" <?= $region=='north'?'selected':'' ?>>North India</option>
                            <option value="south" <?= $region=='south'?'selected':'' ?>>South India</option>
                            <option value="east" <?= $region=='east'?'selected':'' ?>>East India</option>
                            <option value="west" <?= $region=='west'?'selected':'' ?>>West India</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="type" class="filter-label">Travel Type</label>
                        <select id="type" name="type" class="filter-select">
                            <option value="">All Types</option>
                            <option value="adventure" <?= $travel_type=='adventure'?'selected':'' ?>>Adventure</option>
                            <option value="cultural" <?= $travel_type=='cultural'?'selected':'' ?>>Cultural</option>
                            <option value="beach" <?= $travel_type=='beach'?'selected':'' ?>>Beach</option>
                            <option value="hill-station" <?= $travel_type=='hill-station'?'selected':'' ?>>Hill Station</option>
                            <option value="wildlife" <?= $travel_type=='wildlife'?'selected':'' ?>>Wildlife</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="season" class="filter-label">Best Season</label>
                        <select id="season" name="season" class="filter-select">
                            <option value="">All Seasons</option>
                            <option value="summer" <?= $best_season=='summer'?'selected':'' ?>>Summer</option>
                            <option value="winter" <?= $best_season=='winter'?'selected':'' ?>>Winter</option>
                            <option value="monsoon" <?= $best_season=='monsoon'?'selected':'' ?>>Monsoon</option>
                        </select>
                    </div>
                    <button class="filter-btn" type="submit">Filter</button>
                </form>
            </section>
            <div class="destinations-grid">
                <?php if (count($destinations) === 0): ?>
                    <p style="padding:2rem; color:#888;">No destinations found for selected filters.</p>
                <?php else: foreach ($destinations as $d): ?>
                <div class="destination-card">
                    <?php 
                        // Use cover_image for the main image
                        $imgSrc = 'admin/images/destination/default.jpg'; // fallback image
                        if (!empty($d['cover_image'])) {
                            $imgSrc = 'admin/images/destination/' . $d['cover_image'];
                        }
                        $descId = 'desc_' . md5($d['name'] . $d['id']);
                    ?>
                    <img src="<?= htmlspecialchars($imgSrc) ?>" alt="<?= htmlspecialchars($d['name']) ?>" class="destination-img">
                    <div class="destination-overlay">
                        <h3 class="destination-name"><?= htmlspecialchars($d['name']) ?></h3>
                        <p class="destination-desc" id="<?= $descId ?>"><?= htmlspecialchars($d['highlight']) ?></p>
                        <a href="info.php?pname=<?= urlencode($d['name']) ?>" class="explore-btn">Explore</a>
                    </div>
                </div>
                <?php endforeach; endif; ?>
            </div>
        </section>
    </div>
    <!-- No JS needed for description toggle -->
</body>
</html>