<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anveshana - Explore the World</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav id="navbar">
        <a href="#" class="nav-logo">Anveshana</a>
        <div class="nav-links">
            <a href="#features">Features</a>
            <a href="#destinations">Destinations</a>
            <a href="#testimonials">Testimonials</a>
            <a href="#contact">Contact</a>
        </div>
        <?php
        session_start();
        $avatar = 'images/destination/default.jpg';
        $username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
        if ($username) {
            $db_host = 'localhost';
            $db_user = 'root';
            $db_pass = '';
            $db_name = 'anveshana_admin';
            $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
            if (!$conn->connect_error) {
                $sql = "SELECT avatar FROM users WHERE username = '" . $conn->real_escape_string($username) . "' LIMIT 1";
                $result = $conn->query($sql);
                if ($result && $result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    if (!empty($row['avatar'])) {
                        $avatar = htmlspecialchars($row['avatar']);
                    } else {
                        $avatar = '';
                    }
                }
                $conn->close();
            }
        }
        ?>
        <div class="user-dropdown">
            <button class="user-dropbtn">
                <?php if (!empty($avatar)): ?>
                    <img src="<?= $avatar ?>" alt="Avatar" style="width:32px;height:32px;border-radius:50%;vertical-align:middle;object-fit:cover;margin-right:8px;">
                <?php else: ?>
                    <i class="fa-solid fa-user" style="font-size:32px;margin-right:8px;"></i>
                <?php endif; ?>
                <span><?= htmlspecialchars($username ?: 'User') ?></span> <i class="fas fa-caret-down"></i>
            </button>
            <div class="user-dropdown-content">
                <a href="dashboard.php">Dashboard</a>
                <a href="profile.php">Profile</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>
    
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1 class="logo">Anveshana</h1>
            <p class="tagline">Explore the World Like Never Before</p>
            <a href="#destinations" class="cta-button">Create Memories</a>
        </div>
        <div class="scroll-down" onclick="document.querySelector('#features').scrollIntoView({ behavior: 'smooth' })">
            <i class="fas fa-chevron-down"></i>
        </div>
    </section>
    
    <!-- Features Section -->
    <section class="features" id="features">
        <h2 class="section-title">Why Choose Anveshana?</h2>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-globe-americas"></i>
                </div>
                <h3 class="feature-title">Global Destinations</h3>
                <p class="feature-desc">Access thousands of destinations worldwide with our comprehensive travel network and local experts.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-cogs"></i>
                </div>
                <h3 class="feature-title">Smart Itinerary</h3>
                <p class="feature-desc">Our AI-powered system creates personalized itineraries based on your preferences and budget.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <h3 class="feature-title">24/7 Support</h3>
                <p class="feature-desc">Round-the-clock assistance from our travel experts wherever you are in the world.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-wallet"></i>
                </div>
                <h3 class="feature-title">Best Prices</h3>
                <p class="feature-desc">We guarantee the best prices with our price match policy and exclusive deals.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-user-shield"></i>
                </div>
                <h3 class="feature-title">Travel Safely</h3>
                <p class="feature-desc">Comprehensive travel insurance and safety alerts to ensure peace of mind.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-route"></i>
                </div>
                <h3 class="feature-title">Unique Experiences</h3>
                <p class="feature-desc">Go beyond tourism with authentic local experiences curated by our destination experts.</p>
            </div>
        </div>
    </section>
    
    <!-- Destinations Section -->
    <section class="destinations" id="destinations">
        <h2 class="section-title">Popular Destinations</h2>
        <?php
        // DB connection for index.php
        $db_host = 'localhost';
        $db_user = 'root';
        $db_pass = '';
        $db_name = 'anveshana_admin';
        $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
        if ($conn->connect_error) {
            echo '<div style="color:#c00;text-align:center;">Database connection failed.</div>';
        } else {
            $sql = "SELECT name, highlight, cover_image FROM destinations ORDER BY id DESC LIMIT 6";
            $result = $conn->query($sql);
        ?>
        <div class="destinations-grid">
            <?php
            if ($result && $result->num_rows > 0):
                $count = 0;
                while ($row = $result->fetch_assoc()):
                    if ($count >= 6) break;
                    $img = !empty($row['cover_image']) ? 'admin/images/destination/' . htmlspecialchars($row['cover_image']) : 'images/destination/default.jpg';
            ?>
            <div class="destination-card">
                <img src="<?= $img ?>" alt="<?= htmlspecialchars($row['name']) ?>" class="destination-img">
                <div class="destination-overlay">
                    <h3 class="destination-name"><?= htmlspecialchars($row['name']) ?></h3>
                    <p class="destination-desc"><?= htmlspecialchars($row['highlight']) ?></p>
                    <a href="destination.php?pname=<?= urlencode($row['name']) ?>" class="explore-btn">Explore</a>
                </div>
            </div>
            <?php $count++; endwhile; else: ?>
                <div style="grid-column:1/-1;text-align:center;color:#888;padding:2rem;">No destinations found.</div>
            <?php endif; ?>
        </div>
        <?php $conn->close(); } ?>
    </section>
    
    <!-- Testimonials Section -->
    <section class="testimonials" id="testimonials">
        <h2 class="section-title">Traveler Stories</h2>
        <div class="testimonials-container">
            <div class="testimonial-card">
                <p class="testimonial-text">"Anveshana made our honeymoon unforgettable! The personalized itinerary included all the romantic spots in Santorini we would have never found on our own. The 24/7 support was amazing when we had a last-minute change request."</p>
                <div class="testimonial-author">
                    <img src="https://randomuser.me/api/portraits/women/32.jpg" alt="Sarah J." class="author-img">
                    <div>
                        <p class="author-name">Sarah J.</p>
                        <p class="author-role">Honeymoon Traveler</p>
                    </div>
                </div>
            </div>
            
            <div class="testimonial-card">
                <p class="testimonial-text">"As a solo female traveler, safety is my top priority. Anveshana's local guides and real-time safety alerts gave me peace of mind while exploring Morocco. Their recommendations for authentic experiences were spot on!"</p>
                <div class="testimonial-author">
                    <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Priya M." class="author-img">
                    <div>
                        <p class="author-name">Priya M.</p>
                        <p class="author-role">Solo Traveler</p>
                    </div>
                </div>
            </div>
            
            <div class="testimonial-card">
                <p class="testimonial-text">"Our family vacation to Japan was perfectly planned by Anveshana. They considered our kids' ages and interests, finding activities we all enjoyed. The price match guarantee saved us over $800 on flights and hotels!"</p>
                <div class="testimonial-author">
                    <img src="https://randomuser.me/api/portraits/men/67.jpg" alt="David L." class="author-img">
                    <div>
                        <p class="author-name">David L.</p>
                        <p class="author-role">Family Traveler</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Newsletter Section -->
    <section class="newsletter" id="newsletter">
        <div class="newsletter-container">
            <h2 class="newsletter-title">Ready to Explore?</h2>
            <p class="newsletter-desc">Subscribe to our newsletter for exclusive travel deals, destination guides, and insider tips delivered straight to your inbox.</p>
            <form class="newsletter-form">
                <input type="email" placeholder="Your email address" class="newsletter-input" required>
                <button type="submit" class="newsletter-btn">Subscribe</button>
            </form>
        </div>
    </section>
    
    <!-- Footer -->
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
                    <li><a href="#">Home</a></li>
                    <li><a href="#features">Features</a></li>
                    <li><a href="#destinations">Destinations</a></li>
                    <li><a href="#testimonials">Testimonials</a></li>
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
            
            <div class="footer-contact">
                <h3>Contact Us</h3>
                <div class="contact-item">
                    <i class="fas fa-map-marker-alt contact-icon"></i>
                    <p>Still thinking but we will decide it soon</p>
                </div>
                <div class="contact-item">
                    <i class="fas fa-phone-alt contact-icon"></i>
                    <p>+91 9876543210</p>
                </div>
                <div class="contact-item">
                    <i class="fas fa-envelope contact-icon"></i>
                    <p>info@anveshana.com</p>
                </div>
            </div>
        </div>
        
        <div class="copyright">
            <p>&copy; 2025 Anveshana - Explore the World. All rights reserved.</p>
        </div>
    </footer>
    
    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
        
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html>
