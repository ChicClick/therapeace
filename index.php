<?php
include 'fetch_services.php'; 

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TheraPeace</title>
    <link rel="icon" type="image/svg+xml" href="images/TheraPeace Logo.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Architects+Daughter&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poly:ital@0;1&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="wrapper">
        <nav>
            <div class="logo">
                <img src="images/TheraPeace Logo.svg" alt="TheraPeace Logo">
                <h1>TheraPeace</h1>
            </div>
            <div class="nav-container">
                <ul class="nav-links">
                    <li><a href="#">Home</a></li>
                    <li><a href="#about" data-nav-link>About Us</a></li>
                    <li><a href="#services" data-nav-link>Services</a></li>
                    <li><a href="#rates" data-nav-link>Rates</a></li>
                    <!-- Add the login button as a list item -->
                    <li>
                        <button class="login-btn" onclick="window.location.href='loginlanding.html';">Login</button>
                    </li>
                </ul>
                <!-- Hamburger menu icon for smaller devices -->
                <div class="hamburger-menu" id="hamburger-menu">
                    <span class="bar"></span>
                    <span class="bar"></span>
                    <span class="bar"></span>
                </div>
            </div>
        </nav>

        
    <div class="hero-section">
        <div class="hero-text">
            <h1>Seamless Patient - Care Experience</h1>
            <p>Optimize business processes and services management all in one comprehensive system.</p>
            <button class="enroll-btn" onclick="window.location.href='guestPreScreening.php';">Enroll Now</button>
        </div>
    </div>



    <section class="about-section" id="about">
        <div class="about-images-container">
            <div class="about-image about-image-1">
                <img src="images/about 4.jpg" alt="Image 1">
              </div>
              <div class="about-image about-image-2">
                <img src="images/about 2.jpg" alt="Image 2">
              </div>
              <div class="about-image about-image-3">
                <img src="images/_home.jpg" alt="Image 3">
              </div>
        </div>
        <div class="about-content">
            <h2>About Us</h2>
                <p>
                    TheraPeace offers a wide range of functionalities, including appointment scheduling, staff management, and communication tools. Patients can easily book, reschedule, and cancel appointments online, with automated reminders sent to both patients and therapists.
                </p>
                <br>
                <p>  
                    TheraPeace helps manage therapists' schedules, availability, and workload, facilitating communication and collaboration among staff members. Its reporting and analytics features provide insights into the therapy center's operations, aiding in informed decision-making. Additionally, the system includes secure communication tools, document management, user authentication, and role-based access control to protect sensitive information. With its ability to integrate with other healthcare systems and tools, along with a customizable interface, TheraPeace enhances the efficiency and effectiveness of therapy centers by reducing administrative burdens, improving patient care, and optimizing overall operations.
                </p>
            <div class="bee-trail-wrapper">
                <!-- Create a jagged or curved flight path for the bees -->
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1800 200">
                    <path class="bee-trail-path" d="M0,150 C300,50, 600,250, 900,150 C1200,50, 1500,250, 1800,150"/>
                    <!-- Add two bee icons -->
                    <image class="bee bee-1" href="images/TheraPeace Logo.svg" width="30" height="30" />
                    <image class="bee bee-2" href="images/TheraPeace Logo.svg" width="30" height="30" />
                </svg>
            </div>
        </div>
        
    </section>

    <section class="services-section" id="services">
        <h2>Services Offered</h2>
        <div class="services-grid">
            <?php foreach ($services as $service): ?>
                <div class="service-item hexagon">
                    <div class="service-content">
                        <h3><?php echo htmlspecialchars($service['serviceName']); ?></h3>
                        <i class="service-icon <?php echo htmlspecialchars($service['icon']); ?>"></i>
                        <div class="service-description">
                            <p><?php echo nl2br(htmlspecialchars($service['description'])); ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="pricing-section" id="rates">
        <h2>Rates and Tuition Fees</h2>
        <div class="pricing-grid">
            <div class="pricing-item">
                <h3>MONTHLY</h3>
                <h1>₱ 7,000</h1>
                <p>This option is ideal for those who prefer to manage their budget by making smaller, manageable payments each month</p>
                <button class="enroll-btn-p">Enroll Now</button>
                <h4>₱ 15,000</h4>
                <h5>DOWNPAYMENT</h5>
            </div>
            <div class="pricing-item">
                <h3>ALL-IN</h3>
                <h1>₱ 45,000</h1>
                <p>By choosing this option, you can enjoy the convenience of completing your transaction in one go without the need for future payments.</p>
                <button class="enroll-btn-p">Enroll Now</button>
                <h5>ALL-IN CASH PAYMENT</h5>
            </div>
            <div class="pricing-item">
                <h3>WEEKLY</h3>
                <h1>₱ 2,000</h1>
                <p>The Weekly Installment Plan allows you to make payments on a weekly basis, providing even more flexibility in managing your finances.</p>
                <button class="enroll-btn-p">Enroll Now</button>
                <h4>₱ 5,000</h4>
                <h5>DOWNPAYMENT</h5>
            </div>
        </div>
    </section>

    <section class="feedback-section">
        <h2>Parent's Feedback</h2>
        <div class="feedback-carousel-container">
        <button class="carousel-control left" onclick="moveCarousel('left')">&lt;</button>
                <div class="feedback-carousel">
                    <?php
                    // Include the PHP file that handles feedback display
                    include 'fetch_parentfeedbacks.php';
                    ?>
                </div>
                <!-- Leave Feedback Button -->
                <button class="carousel-control right" onclick="moveCarousel('right')">&gt;</button>
        </div>
    </section>

    <footer class="footer">
        <footer class="footer">
            <div class="footer-container">
                <div class="footer-logo">
                    <img src="images/TheraPeace Logo.svg" alt="TheraPeace Logo">
                    <h2>TheraPeace</h2>
                    <p>Your Partner in Patient Care</p>
                </div>
                <div class="footer-contact">
                    <h3>Contact Us</h3>
                    <ul>
                        <li><i class="fas fa-map-marker-alt"></i> Polytechnic University of the Philippines</li>
                        <li><i class="fas fa-phone-alt"></i> +63 123 456 7890</li>
                        <li><i class="fas fa-envelope"></i> contact@therapeace.com</li>
                    </ul>
                </div>
                <div class="footer-social">
                    <h3>Follow Us</h3>
                    <ul class="social-icons">
                        <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                        <li><a href="#"><i class="fab fa-instagram"></i></a></li>
                        <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                        <li><a href="#"><i class="fab fa-linkedin"></i></a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 TheraPeace. All Rights Reserved.</p>
            </div>
        </footer>    
    <button class="scroll-top">Scroll to Top</button>
    
    
    <script src="guestScript.js" defer></script>
</div>
</body>
</html>
