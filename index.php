<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anveshana - Explore the World</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- <link rel="stylesheet" href="css/style.css"> -->
    <style>
        :root {
    --primary: #ff6b6b;         /* Vibrant Coral */
    --primary-dark: #f744ee;    /* Pinkish Purple */
    --secondary: #5f27cd;       /* Interactive Purple */
    --danger: #ffb400;          /* Amber/Warning */
    --warning: #48dbfb;         /* Sky Blue */
    --dark: #22223b;            /* Rich Navy */
    --light: #f8f7ff;           /* Soft Off-White */
    --gray: #a1a1aa;            /* Modern Gray */
    --gray-dark: #575366;       /* Muted Dark Gray */
    --success: #d1fae5;
    --error: #fee2e2;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

body {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark) 60%, var(--secondary) 100%);
    min-height: 100vh;
    background-attachment: fixed;
    background-repeat: no-repeat;
}

/* Hero Section */
.hero {
    height: 100vh;
    background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), 
                url('https://images.unsplash.com/photo-1483729558449-99ef09a8c325?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    color: white;
    position: relative;
}

.hero-content {
    max-width: 800px;
    padding: 0 20px;
    animation: fadeIn 1.5s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.logo {
    font-size: 45px;
    font-weight: 600;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
}

.logo i {
    margin-right: 0.5rem;
    color: var(--secondary);
}

.tagline {
    font-size: 1.8rem;
    margin-bottom: 30px;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

.cta-button1 {
    display: inline-block;
    padding: 15px 30px;
    background: linear-gradient(to right, var(--primary), var(--secondary));
    color: white;
    text-decoration: none;
    border-radius: 50px;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.cta-button1:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
}

.cta-button {
    display: inline-block;
    padding: 15px 30px;
    background: linear-gradient(to right, var(--primary), var(--secondary));
    color: white;
    text-decoration: none;
    border-radius: 50px;
    font-weight: 600;
    font-size: 1.2rem;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.cta-button:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
}

.scroll-down {
    position: absolute;
    bottom: 30px;
    left: 50%;
    transform: translateX(-50%);
    color: white;
    font-size: 2rem;
    animation: bounce 2s infinite;
    cursor: pointer;
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% { transform: translateY(0) translateX(-50%); }
    40% { transform: translateY(-20px) translateX(-50%); }
    60% { transform: translateY(-10px) translateX(-50%); }
}

/* Navigation */
nav {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    padding: 20px 50px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    z-index: 1000;
    transition: all 0.3s ease;
}

nav.scrolled {
    background-color: rgba(255, 255, 255, 0.95);
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    padding: 15px 50px;
}

nav.scrolled .nav-logo, 
nav.scrolled .nav-links a {
    color: var(--dark);
}

.nav-logo {
    font-size: 1.8rem;
    font-weight: 700;
    color: white;
    text-decoration: none;
}

.nav-links {
    display: flex;
    gap: 30px;
}

.nav-links a {
    color: white;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    position: relative;
}

.nav-links a:hover {
    color: var(--secondary);
}

.nav-links a::after {
    content: '';
    position: absolute;
    bottom: -5px;
    left: 0;
    width: 0;
    height: 2px;
    background-color: var(--secondary);
    transition: width 0.3s ease;
}

.nav-links a:hover::after {
    width: 100%;
}

/* Features Section */
.features {
    padding: 100px 50px;
    background-color: white;
}

.section-title {
    text-align: center;
    font-size: 2.5rem;
    margin-bottom: 70px;
    color: var(--dark);
    position: relative;
}

.section-title::after {
    content: '';
    position: absolute;
    bottom: -15px;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 4px;
    background: linear-gradient(to right, var(--primary), var(--secondary));
    border-radius: 2px;
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 40px;
    max-width: 1200px;
    margin: 0 auto;
}

.feature-card {
    background-color: var(--light);
    border-radius: 15px;
    padding: 30px;
    text-align: center;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
}

.feature-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
}

.feature-icon {
    font-size: 3rem;
    margin-bottom: 20px;
    color: var(--primary);
}

.feature-title {
    font-size: 1.5rem;
    margin-bottom: 15px;
    color: var(--dark);
}

.feature-desc {
    color: #666;
    line-height: 1.6;
}

/* Destinations Section */
.destinations {
    padding: 100px 50px;
    background-color: #f5f5f5;
}

.destinations-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 30px;
    max-width: 1200px;
    margin: 0 auto;
}

.destination-card {
    position: relative;
    height: 400px;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.destination-card:hover {
    transform: scale(1.03);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
}

.destination-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.destination-card:hover .destination-img {
    transform: scale(1.1);
}

.destination-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    padding: 30px;
    background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
    color: white;
}

.destination-name {
    font-size: 1.8rem;
    margin-bottom: 10px;
}

.destination-desc {
    margin-bottom: 15px;
    opacity: 0.9;
}

.explore-btn {
    display: inline-block;
    padding: 8px 20px;
    background-color: var(--secondary);
    color: white;
    text-decoration: none;
    border-radius: 50px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.explore-btn:hover {
    background-color: var(--primary);
}

/* Testimonials */
.testimonials {
    padding: 100px 50px;
    background-color: white;
}

.testimonials-container {
    max-width: 1200px;
    margin: 0 auto;
}

.testimonial-card {
    background-color: var(--light);
    padding: 30px;
    border-radius: 15px;
    margin: 20px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
}

.testimonial-text {
    font-style: italic;
    margin-bottom: 20px;
    color: #555;
    line-height: 1.6;
}

.testimonial-author {
    display: flex;
    align-items: center;
}

.author-img {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
    margin-right: 15px;
}

.author-name {
    font-weight: 600;
    color: var(--dark);
}

.author-role {
    color: #777;
    font-size: 0.9rem;
}

/* Newsletter */
.newsletter {
    padding: 100px 50px;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: white;
    text-align: center;
}

.newsletter-container {
    max-width: 600px;
    margin: 0 auto;
}

.newsletter-title {
    font-size: 2rem;
    margin-bottom: 20px;
}

.newsletter-desc {
    margin-bottom: 30px;
    opacity: 0.9;
}

.newsletter-form {
    display: flex;
    max-width: 500px;
    margin: 0 auto;
}

.newsletter-input {
    flex: 1;
    padding: 15px 20px;
    border: none;
    border-radius: 50px 0 0 50px;
    font-size: 1rem;
    outline: none;
}

.newsletter-btn {
    padding: 15px 30px;
    background-color: var(--dark);
    color: white;
    border: none;
    border-radius: 0 50px 50px 0;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.newsletter-btn:hover {
    background-color: #1a252f;
}

/* Footer */
footer {
    background-color: var(--dark);
    color: white;
    padding: 70px 50px 30px;
}

.footer-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 40px;
    max-width: 1200px;
    margin: 0 auto;
}

.footer-logo {
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 20px;
    background: linear-gradient(to right, var(--primary), var(--secondary));
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
}

.footer-about p {
    margin-bottom: 20px;
    opacity: 0.8;
    line-height: 1.6;
}

.social-links {
    display: flex;
    gap: 15px;
}

.social-link {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background-color: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    color: white;
    text-decoration: none;
    transition: all 0.3s ease;
}

.social-link:hover {
    background-color: var(--primary);
    transform: translateY(-3px);
}

.footer-links h3, .footer-contact h3 {
    font-size: 1.3rem;
    margin-bottom: 20px;
    position: relative;
}

.footer-links h3::after, .footer-contact h3::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 0;
    width: 40px;
    height: 3px;
    background: linear-gradient(to right, var(--primary), var(--secondary));
}

.footer-links ul {
    list-style: none;
}

.footer-links li {
    margin-bottom: 10px;
}

.footer-links a {
    color: white;
    text-decoration: none;
    opacity: 0.8;
    transition: all 0.3s ease;
}

.footer-links a:hover {
    opacity: 1;
    color: var(--secondary);
    padding-left: 5px;
}

.contact-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 15px;
}

.contact-icon {
    margin-right: 10px;
    color: var(--secondary);
}

.copyright {
    text-align: center;
    margin-top: 50px;
    padding-top: 20px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    opacity: 0.7;
}

/* Responsive Design */
@media (max-width: 768px) {
    .logo {
        font-size: 2.5rem;
    }
    
    .tagline {
        font-size: 1.3rem;
    }
    
    nav {
        padding: 15px 20px;
    }
    
    .nav-links {
        gap: 15px;
    }
    
    .features, .destinations, .testimonials, .newsletter {
        padding: 70px 20px;
    }
    
    .newsletter-form {
        flex-direction: column;
    }
    
    .newsletter-input {
        border-radius: 50px;
        margin-bottom: 10px;
    }
    
    .newsletter-btn {
        border-radius: 50px;
    }

    /* Tablet styles */
    .container {
        width: 95%;
    }
}

@media (max-width: 480px) {
    .nav-links {
        display: none;
    }
    
    .logo {
        font-size: 2rem;
    }
    
    .destinations-grid {
        grid-template-columns: 1fr;
    }

    /* Mobile phone styles */
    .container {
        padding: 10px;
        font-size: 14px;
    }
}
    </style>
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
            <a href="login.php" class="cta-button1">Get Started</a>
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
        <div class="destinations-grid">
            <div class="destination-card">
                <img src="images/destination/kashmir.avif" alt="Kashmir" class="destination-img">
                <div class="destination-overlay">
                    <h3 class="destination-name">Kashmir</h3>
                    <p class="destination-desc">Tropical paradise with lush jungles, volcanic mountains, and coral reefs.</p>
                    <a href="destination.html" class="explore-btn">Explore</a>
                </div>
            </div>
            
            <div class="destination-card">
                <img src="images/destination/mysore.jpg" alt="Mysore" class="destination-img">
                <div class="destination-overlay">
                    <h3 class="destination-name">Mysore</h3>
                    <p class="destination-desc">The city of love, art, fashion, gastronomy, and culture.</p>
                    <a href="destination.html" class="explore-btn">Explore</a>
                </div>
            </div>
            
            <div class="destination-card">
                <img src="images/destination/manali.jpg" alt="Manali" class="destination-img">
                <div class="destination-overlay">
                    <h3 class="destination-name">Manali</h3>
                    <p class="destination-desc">Ultramodern meets traditional in this bustling metropolis.</p>
                    <a href="destination.html" class="explore-btn">Explore</a>
                </div>
            </div>
            
            <div class="destination-card">
                <img src="images/destination/rajasthan.jpg" alt="Rajasthan" class="destination-img">
                <div class="destination-overlay">
                    <h3 class="destination-name">Rajasthan</h3>
                    <p class="destination-desc">The city that never sleeps with iconic landmarks and diverse culture.</p>
                    <a href="destination.html" class="explore-btn">Explore</a>
                </div>
            </div>
            
            <div class="destination-card">
                <img src="images/destination/delhi.jpg" alt="Delhi" class="destination-img">
                <div class="destination-overlay">
                    <h3 class="destination-name">Delhi</h3>
                    <p class="destination-desc">Stunning natural beauty with Table Mountain and pristine beaches.</p>
                    <a href="destination.html" class="explore-btn">Explore</a>
                </div>
            </div>
            
            <div class="destination-card">
                <img src="images/destination/kerala.jpg" alt="Kerala" class="destination-img">
                <div class="destination-overlay">
                    <h3 class="destination-name">Kerala</h3>
                    <p class="destination-desc">Vibrant harbor city with iconic Opera House and Bondi Beach.</p>
                    <a href="destination.html" class="explore-btn">Explore</a>
                </div>
            </div>
        </div>
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