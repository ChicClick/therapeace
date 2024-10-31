<?php
session_start(); // Add this to ensure the session is started
if (isset($_SESSION['patientName'])) {
    $patientName = $_SESSION['patientName']; // Retrieve the patient's name from the session
} else {
    // Handle the case where the session variable is not set (e.g., redirect to login page)
    header("Location: patientLogin.php");
    exit;
} 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TheraPeace</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Architects+Daughter&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poly:ital@0;1&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav>
        <div class="logo">
            <img src="images/logo.png" alt="TheraBee Logo" class="logo">
            <h1>TheraPeace</h1>
        </div>
        <ul class="nav-links">
            <li><a href="#">Home</a></li>
            <li><a href="patientAppointments.php" data-nav-link>Appointments</a></li>
            <li><a href="patientAppointments.php#notes">Notes</a></li>
        </ul>
        <div class="user-greeting">
            <span class="welcome-text">Welcome back, <?php echo htmlspecialchars($patientName); ?>!</span>
            <i class="notification-icon">🔔</i>
            <div class="dropdown">
                <button class="dropbtn">▼</button>
                <div class="dropdown-content">
                    <a href="editProfile.php">Edit Profile</a>
                    <a href="settings.php">Settings</a>
                    <a href="#" id="logoutBtn">Log Out</a>
                </div>
            </div>
        </div>
    </nav>

    <section class="hero-section">
        <div class="home-image-container">
        <img src="images/home.png" alt="TheraPeace Care" class="home-image">
        </div>
        <div class="hero-content">
        <div class="hero-text">
            <h1>Seamless Patient - 
                Care Experience</h1>
            <p>A management system for TheraPeace. Optimize business processes and services management all in one comprehensive system.</p>
            <button class="enroll-btn" onclick="window.location.href='patientAppointments.php';">View Appointments</button>
        </div>
        </div>
    </section>

    <section class="about-section" id="about">
        <h2>About Us</h2>
        <div class="about-content">
            <p>
            TheraPeace offers a wide range of functionalities, including appointment scheduling, staff management, and communication tools. Patients can easily book, reschedule, and cancel appointments online, with automated reminders sent to both patients and therapists. TheraPeace helps manage therapists' schedules, availability, and workload, facilitating communication and collaboration among staff members. Its reporting and analytics features provide insights into the therapy center's operations, aiding in informed decision-making. Additionally, the system includes secure communication tools, document management, user authentication, and role-based access control to protect sensitive information. With its ability to integrate with other healthcare systems and tools, along with a customizable interface, TheraPeace enhances the efficiency and effectiveness of therapy centers by reducing administrative burdens, improving patient care, and optimizing overall operations.
            </p>
        </div>
        <div class="about-images">
        <img src="images/about us image.png" alt="About Us Image">
        </div>
    </section>

    <section class="services-section" id="services">
        <h2>Services Offered by TheraBee</h2>
        <div class="services-grid">
            <div class="service-item hexagon">
                <div class="service-content">
                    <h3>Charity Therapy</h3>
                    <i class="service-icon fa-solid fa-hand-holding-heart"></i>
                    <div class="service-description">
                        <p>Description about Charity Therapy</p>
                    </div>
                </div>
            </div>
            <div class="service-item hexagon">
                <div class="service-content">
                    <h3>Speech Therapy</h3>
                    <i class="service-icon fa-solid fa-comments"></i>
                    <div class="service-description">
                        <p>Description about Speech Therapy</p>
                    </div>
                </div>
            </div>
            <div class="service-item hexagon">
                <div class="service-content">
                    <h3>Free Screening</h3>
                    <i class="service-icon fa-solid fa-clipboard-list"></i>
                    <div class="service-description">
                        <p>Description about Free Screening</p>
                    </div>
                </div>
            </div>
            <div class="service-item hexagon">
                <div class="service-content">
                    <h3>Special Education</h3>
                    <i class="service-icon fa-solid fa-book-open"></i>
                    <div class="service-description">
                        <p>Description about Special Education</p>
                    </div>
                </div>
            </div>
            <div class="service-item hexagon">
                <div class="service-content">
                    <h3>Physical Therapy</h3>
                    <i class="service-icon fa-solid fa-wheelchair-move"></i>
                    <div class="service-description">
                        <p>Description about Physical Therapy</p>
                    </div>
                </div>
            </div>
            <div class="service-item hexagon">
                <div class="service-content">
                    <h3>Occupational Therapy</h3>
                    <i class="service-icon fa-solid fa-hands-holding-child"></i>
                    <div class="service-description">
                        <p>Description about Occupational Therapy</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="feedback-section">
        <h2>Parent's Feedback</h2>
        <div class="feedback-carousel">
            <div class="feedback-item">
                <p>"Mom's about to send us flying off from therapy with Mommy Dana's expert parenting skills..."</p>
                <h4>Mommy Dana</h4>
            </div>
            <div class="feedback-item">
                <p>"Thanks to Teacher Julia, my son has improved significantly in his speech and interaction with other kids..."</p>
                <h4>Mommy Mikkee</h4>
            </div>
            <div class="feedback-item">
                <p>"Our daughter has grown confident and more expressive thanks to the amazing therapy sessions..."</p>
                <h4>Mommy Wynn</h4>
            </div>
        </div>
    </section>

    <!-- Logout Confirmation Modal -->
    <div id="logoutModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeModal">&times;</span>
            <h2>Confirm Logout</h2>
            <p>Are you sure you want to log out?</p>
            <button id="confirmLogout">Yes, log me out</button>
            <button id="cancelLogout">Cancel</button>
        </div>
    </div>


<section id="notes"></section>
<footer class="footer">
        <footer class="footer">
            <div class="footer-container">
                <div class="footer-logo">
                    <img src="images/logo.png" alt="TheraPeace Logo">
                    <h2>TheraPeace</h2>
                    <p>Your Partner in Patient Care</p>
                </div>
                <div class="footer-links">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="#">Home</a></li>
                        <li><a href="#about">About Us</a></li>
                        <li><a href="#services">Services</a></li>
                        <li><a href="#rates">Rates</a></li>
                    </ul>
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

<script src="patientScript.js"></script>

</body>
</html>




