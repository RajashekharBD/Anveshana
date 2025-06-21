<?php
// info.php - Show details for a single destination
session_start();
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'anveshana_admin';
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

$pname = $_GET['pname'] ?? '';
$destination = null;
if ($pname) {
    $stmt = $conn->prepare('SELECT * FROM destinations WHERE name = ? LIMIT 1');
    $stmt->bind_param('s', $pname);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $destination = $result->fetch_assoc();
    }
    $stmt->close();
}
// Prepare image and gallery
$imgSrc = 'admin/images/destination/default.jpg';
$galleryImages = [];
if ($destination) {
    // Prefer cover_image if available
    if (!empty($destination['cover_image'])) {
        $imgSrc = 'admin/images/destination/' . $destination['cover_image'];
    }
    // Prepare gallery images from gallery_images field
    if (!empty($destination['gallery_images'])) {
        $imagesArr = json_decode($destination['gallery_images'], true);
        if (is_array($imagesArr) && !empty($imagesArr)) {
            $galleryImages = array_map(function($img) { return 'admin/images/destination/' . $img; }, $imagesArr);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $destination ? htmlspecialchars($destination['name']) . ' | Anveshana - Explore the World' : 'Destination Not Found' ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #3498db;
            --secondary: #2ecc71;
            --dark: #2c3e50;
            --light: #ecf0f1;
            --accent: #e74c3c;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #f9f9f9; color: var(--dark); overflow-x: hidden; }
        nav { position: fixed; top: 0; left: 0; width: 100%; padding: 20px 50px; display: flex; justify-content: space-between; align-items: center; z-index: 1000; background-color: rgba(255, 255, 255, 0.95); box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1); }
        .nav-logo { font-size: 1.8rem; font-weight: 700; color: var(--dark); text-decoration: none; }
        .nav-links { display: flex; gap: 30px; }
        .nav-links a { color: var(--dark); text-decoration: none; font-weight: 500; transition: all 0.3s ease; position: relative; }
        .nav-links a:hover { color: var(--secondary); }
        .nav-links a::after { content: ''; position: absolute; bottom: -5px; left: 0; width: 0; height: 2px; background-color: var(--secondary); transition: width 0.3s ease; }
        .nav-links a:hover::after { width: 100%; }
        .destination-hero {
            height: 70vh;
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('<?= htmlspecialchars($imgSrc) ?>');
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: white;
            margin-top: 80px;
            max-width: 1600px;
            margin-left: auto;
            margin-right: auto;
            border-radius: 24px;
            box-shadow: 0 8px 32px rgba(44,62,80,0.13);
        }
        .destination-title { font-size: 4rem; font-weight: 700; margin-bottom: 20px; text-shadow: 0 2px 10px rgba(0,0,0,0.3); }
        .destination-subtitle { font-size: 1.5rem; max-width: 800px; margin-bottom: 30px; }
        .destination-cta { display: flex; gap: 20px; }
        .btn-primary { display: inline-block; padding: 15px 30px; background: var(--primary); color: white; text-decoration: none; border-radius: 50px; font-weight: 600; font-size: 1.1rem; transition: all 0.3s ease; }
        .btn-primary:hover { background: var(--secondary); transform: translateY(-3px); }
        .btn-outline { display: inline-block; padding: 15px 30px; background: transparent; color: white; border: 2px solid white; text-decoration: none; border-radius: 50px; font-weight: 600; font-size: 1.1rem; transition: all 0.3s ease; }
        .btn-outline:hover { background: white; color: var(--dark); transform: translateY(-3px); }
        .destination-content { max-width: 1200px; margin: 50px auto; padding: 0 20px; }
        .section-title { font-size: 2.5rem; margin-bottom: 30px; color: var(--dark); position: relative; display: inline-block; }
        .section-title::after { content: ''; position: absolute; bottom: -10px; left: 0; width: 50%; height: 4px; background: linear-gradient(to right, var(--primary), var(--secondary)); border-radius: 2px; }
        .destination-description { font-size: 1.1rem; line-height: 1.8; margin-bottom: 40px; color: #555; }
        .gallery { margin: 60px 0; }
        .gallery-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 30px; }
        .gallery-item { height: 250px; border-radius: 10px; overflow: hidden; position: relative; }
        .gallery-img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
        .gallery-item:hover .gallery-img { transform: scale(1.1); }
        .highlights-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; margin: 40px 0; }
        .highlight-card { background-color: white; border-radius: 15px; padding: 30px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05); transition: all 0.3s ease; }
        .highlight-card:hover { transform: translateY(-10px); box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1); }
        .highlight-icon { font-size: 2.5rem; color: var(--primary); margin-bottom: 20px; }
        .highlight-title { font-size: 1.3rem; margin-bottom: 15px; color: var(--dark); }
        .highlight-desc { color: #666; line-height: 1.6; }
        .map-container {
            margin: 2rem auto 0 auto;
            width: 100%;
            max-width: 500px;
            height: 400px;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 6px 32px rgba(44,62,80,0.13);
            background: #fff;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: stretch;
        }
        .map-header {
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            color: #fff;
            font-size: 1.1rem;
            font-weight: 600;
            padding: 12px 20px;
            border-top-left-radius: 18px;
            border-top-right-radius: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
            letter-spacing: 0.5px;
        }
        .map-iframe {
            flex: 1;
            border: none;
            width: 100%;
            height: 100%;
        }
        /* Packages */
        .packages {
            margin: 60px 0;
        }
        
        .package-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }
        
        .package-card {
            background-color: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        
        .package-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }
        
        .package-img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .package-content {
            padding: 25px;
        }
        
        .package-title {
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: var(--dark);
        }
        
        .package-desc {
            color: #666;
            margin-bottom: 20px;
            line-height: 1.6;
            font-size: 1.3rem;
        }
        
        .package-features {
            margin-bottom: 20px;
        }
        
        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            color: #555;
        }
        
        .feature-icon {
            color: var(--secondary);
            margin-right: 10px;
        }
        
        .package-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 20px;
        }
        
        footer { background-color: var(--dark); color: white; padding: 70px 50px 30px; }
        .footer-container { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 40px; max-width: 1200px; margin: 0 auto; }
        .footer-logo { font-size: 1.8rem; font-weight: 700; margin-bottom: 20px; background: linear-gradient(to right, var(--primary), var(--secondary)); -webkit-background-clip: text; background-clip: text; color: transparent; }
        .footer-about p { margin-bottom: 20px; opacity: 0.8; line-height: 1.6; }
        .social-links { display: flex; gap: 15px; }
        .social-link { display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background-color: rgba(255, 255, 255, 0.1); border-radius: 50%; color: white; text-decoration: none; transition: all 0.3s ease; }
        .social-link:hover { background-color: var(--primary); transform: translateY(-3px); }
        .footer-links h3 { font-size: 1.3rem; margin-bottom: 20px; position: relative; }
        .footer-links h3::after { content: ''; position: absolute; bottom: -10px; left: 0; width: 40px; height: 3px; background: linear-gradient(to right, var(--primary), var(--secondary)); }
        .footer-links ul { list-style: none; }
        .footer-links li { margin-bottom: 10px; }
        .footer-links a { color: white; text-decoration: none; opacity: 0.8; transition: all 0.3s ease; }
        .footer-links a:hover { opacity: 1; color: var(--secondary); padding-left: 5px; }
        .copyright { text-align: center; margin-top: 50px; padding-top: 20px; border-top: 1px solid rgba(255, 255, 255, 0.1); opacity: 0.7; }
        @media (max-width: 768px) { .destination-hero { height: 60vh; margin-top: 70px; } .destination-title { font-size: 2.5rem; } .destination-subtitle { font-size: 1.2rem; } .destination-cta { flex-direction: column; gap: 15px; } nav { padding: 15px 20px; } .nav-links { gap: 15px; } }
        @media (max-width: 480px) { .destination-hero { height: 50vh; } .destination-title { font-size: 2rem; } .section-title { font-size: 2rem; } }
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
            <a href="login.html" class="btn-primary">Get Started</a>
        </div>
    </nav>
    <!-- Destination Hero -->
    <section class="destination-hero">
        <h1 class="destination-title"><?= $destination ? htmlspecialchars($destination['name']) : 'Destination Not Found' ?></h1>
        <p class="destination-subtitle"><?= $destination ? htmlspecialchars($destination['highlight']) : '' ?></p>
        <div class="destination-cta">
            <?php if ($destination): ?>
                <a href="#packages" class="btn-primary" style="margin-right:10px;">Book Now</a>
            <?php endif; ?>
            <a href="#gallery" class="btn-outline">View Gallery</a>
        </div>
    </section>
    <!-- Destination Content -->
    <section class="destination-content">
        <?php if ($destination): ?>
        <h2 class="section-title">About <?= htmlspecialchars($destination['name']) ?></h2>
        <p class="destination-description">
            <?= htmlspecialchars($destination['description']) ?>
        </p>
        <!-- Highlights Section (customize as needed) -->
        <?php
        // Fetch highlights for this destination from DB
        $highlights = [];
        if ($destination) {
            $stmt = $conn->prepare('SELECT icon, title, description FROM highlights WHERE destination_id = ?');
            $stmt->bind_param('i', $destination['id']);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $highlights[] = $row;
            }
            $stmt->close();
        }
        ?>
        <div class="highlights">
            <h2 class="section-title">Why Visit <?= htmlspecialchars($destination['name']) ?>?</h2>
            <div class="highlights-grid">
                <?php if (empty($highlights)): ?>
                    <div style="grid-column: 1/-1; text-align:center; color:#888; padding:2rem;">No highlights available for this destination.</div>
                <?php else: foreach ($highlights as $hl): ?>
                <div class="highlight-card">
                    <div class="highlight-icon"><i class="<?= htmlspecialchars($hl['icon']) ?>"></i></div>
                    <h3 class="highlight-title"><?= htmlspecialchars($hl['title']) ?></h3>
                    <p class="highlight-desc"><?= htmlspecialchars($hl['description']) ?></p>
                </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
        <div class="gallery" id="gallery">
            <h2 class="section-title">Gallery</h2>
            <div class="gallery-grid">
                <?php if (empty($galleryImages)): ?>
                    <div style="grid-column: 1/-1; text-align:center; color:#888; padding:2rem;">No gallery images available.</div>
                <?php else: foreach ($galleryImages as $image): ?>
                <div class="gallery-item">
                    <img src="<?= htmlspecialchars($image) ?>" alt="Gallery image" class="gallery-img" loading="lazy" onclick="openLightbox(this.src)">
                </div>
                <?php endforeach; endif; ?>
            </div>
            <!-- Lightbox Modal -->
            <div id="lightboxModal" style="display:none;position:fixed;z-index:9999;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.85);align-items:center;justify-content:center;">
                <span onclick="closeLightbox()" style="position:absolute;top:30px;right:50px;font-size:2.5rem;color:#fff;cursor:pointer;font-weight:bold;">&times;</span>
                <img id="lightboxImg" src="" style="max-width:90vw;max-height:80vh;border-radius:10px;box-shadow:0 8px 32px rgba(0,0,0,0.4);">
            </div>
        </div>

        <!-- Packages Section -->
        <div class="packages" id="packages" style="margin:60px 0;">
            <h2 class="section-title">Tour Packages</h2>
            <?php
            $packages = [];
            if ($destination) {
                $stmt = $conn->prepare('SELECT * FROM packages WHERE destination = ?');
                $stmt->bind_param('s', $destination['name']);
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    $packages[] = $row;
                }
                $stmt->close();
            }
            ?>
            <?php if (empty($packages)): ?>
                <div style="text-align:center; color:#888; padding:2rem;">No packages available for this destination.</div>
            <?php else: ?>
            <div class="package-grid">
                <?php foreach ($packages as $pkg): ?>
                <div class="package-card">
                    <img src="images/packages/<?= htmlspecialchars($pkg['image']) ?>" alt="<?= htmlspecialchars($pkg['package_name']) ?>" class="package-img">
                    <div class="package-content">
                        <h3 class="package-title"><i class="fas fa-suitcase-rolling" style="color:var(--primary);margin-right:8px;"></i><?= htmlspecialchars($pkg['package_name']) ?></h3>
                        <p class="package-desc">
                            <?= nl2br(htmlspecialchars($pkg['description'])) ?>
                        </p>
                        <div class="package-price">₹<?= number_format($pkg['price'],2) ?> per person</div>
                        <?php
                        if (isset($_SESSION['userid'])) {
                            // User is logged in, show direct booking button
                            ?>
                            <a href="book.php?package_id=<?= $pkg['id'] ?>&pname=<?= urlencode($destination['name']) ?>&id=<?= urlencode($destination['id']) ?>&cover_image=<?= urlencode($destination['cover_image'] ?? '') ?>&highlight=<?= urlencode($destination['highlight'] ?? '') ?>" class="btn-primary">Book Now</a>
                            <?php
                        } else {
                            // User not logged in, show login/signup buttons
                            ?>
                            <a href="login.php" class="btn-primary">Book Now</a>
                            <!-- <a href="signup.php" class="signup-btn">Sign Up</a> -->
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
        <div class="map-container">
            <?php 
                $mapQuery = !empty($destination['location']) ? $destination['location'] : $destination['name']; 
                $mapQuery = urlencode($mapQuery);
            ?>
            <div class="map-header">
                <i class="fas fa-map-marker-alt"></i> Location Map
            </div>
            <iframe class="map-iframe" src="https://www.google.com/maps?q=<?= $mapQuery ?>&output=embed" allowfullscreen loading="lazy"></iframe>
        </div>
        <?php else: ?>
        <div style="padding:3rem;text-align:center;color:#c00;font-size:1.3rem;">Destination not found.</div>
        <?php endif; ?>
    </section>
    <footer id="contact">
        <div class="footer-container">
            <div class="footer-about">
                <h3 class="footer-logo">Anveshana</h3>
                <p>Discover the world with our comprehensive tourism management system. We connect travelers with authentic experiences and local experts worldwide.</p>
                <div class="social-links">
                    <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
            <div class="footer-links">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="index.html">Home</a></li>
                    <li><a href="index.html#features">Features</a></li>
                    <li><a href="index.html#destinations">Destinations</a></li>
                    <li><a href="index.html#testimonials">Testimonials</a></li>
                    <li><a href="#">Blog</a></li>
                    <li><a href="#">Careers</a></li>
                </ul>
            </div>
            <div class="footer-links">
                <h3>Support</h3>
                <ul>
                    <li><a href="#">FAQs</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Terms & Conditions</a></li>
                    <li><a href="#">Booking Policy</a></li>
                    <li><a href="#">Refund Policy</a></li>
                </ul>
            </div>
            <div class="footer-links">
                <h3>Contact Us</h3>
                <ul>
                    <li><a href="#"><i class="fas fa-map-marker-alt"></i> Still thinking but we will decide it soon</a></li>
                    <li><a href="tel:+919876543210"><i class="fas fa-phone-alt"></i> +91 9876543210</a></li>
                    <li><a href="mailto:info@anveshana.com"><i class="fas fa-envelope"></i> info@anveshana.com</a></li>
                </ul>
            </div>
        </div>
        <div class="copyright">
            <p>&copy; <?= date('Y') ?> Anveshana - Explore the World. All rights reserved.</p>
        </div>
    </footer>
    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Lightbox functionality
        function openLightbox(src) {
            var modal = document.getElementById('lightboxModal');
            var img = document.getElementById('lightboxImg');
            img.src = src;
            modal.style.display = 'flex';
        }
        function closeLightbox() {
            var modal = document.getElementById('lightboxModal');
            modal.style.display = 'none';
            document.getElementById('lightboxImg').src = '';
        }
        // Close lightbox on ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeLightbox();
        });
        // Close lightbox on click outside image
        document.getElementById('lightboxModal').addEventListener('click', function(e) {
            if (e.target === this) closeLightbox();
        });
    </script>
</body>
</html>