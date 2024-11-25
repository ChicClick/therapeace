<?php
include 'config.php';
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
    <title>TheraPeace - Patient Appointments</title>
    <link rel="icon" type="image/svg+xml" href="images/TheraPeace Logo.svg">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Architects+Daughter&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poly:ital@0;1&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="patientStyles.css">
    <link rel="stylesheet" href="patientReschedStyles.css">

    <script src="./generic-components/generic-side-view-bar.js" defer></script>
    <script src="./generic-components/generic-calendar.js" defer></script>
    <script src="./generic-components/generic-table.js" defer></script>
    <script src="./generic-components/generic-message-popup.js" defer></script>
    <script src="patientScript.js" defer></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" defer></script>
    <script src="patient_navigation_script.js" defer></script>
</head>
<body>
    <generic-side-view-bar></generic-side-view-bar>
    <nav>
        <div class="logo">
            <img src="images/TheraPeace Logo.svg" alt="TheraPeace Logo">
            <h1>TheraPeace</h1>
        </div>
        <div class="nav-container">
            <ul class="nav-links">
                <li><a href="#" class="nav-link" data-target="patientHome.php">Home</a></li>
                <li><a href="#" class="nav-link" data-target="patientAppointments.php">Appointments</a></li>
                <li><a href="#" class="nav-link" data-target="patientNotes.php">Notes</a></li>
            </ul>
            <div class="user-greeting">
                <span class="welcome-text">Welcome back, <?php echo htmlspecialchars($patientName); ?>!</span>
                <div class="dropdown">
                    <button class="dropbtn">▼</button>
                    <div class="dropdown-content">
                        <a href="#" class="nav-link" data-target="patientProfile.php"> <i class="fa fa-cog"></i> View Profile</a>
                        <a href="#" id="logoutBtn"> <i class="fa fa-sign-out"></i> Log Out</a>
                        <a href="#" id="leave-feedback-link" class="nav-link" onclick="openFeedbackForm();">
                            <i class="fas fa-comment-dots"></i> Leave Feedback
                        </a>
                    </div>
                </div>
            </div>
        </div>


    </nav>

    <!-- Main content please refer to patient_navigation_script.js if you want to navigation. 
    DO NOT INCLUDE HTML HEAD AND BODY when creating new php fie!!! -->

    <div id="content">
        <?php include 'patientHome.php' ?>
    </div>

    <div id="logoutModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeModal">&times;</span>
            <h2>Confirm Logout</h2>
            <p>Are you sure you want to log out?</p>
            <button id="confirmLogout">Yes, log me out</button>
            <button id="cancelLogout">Cancel</button>
        </div>
    </div>

    <footer class="footer">
        <footer class="footer">
            <div class="footer-container">
                <div class="footer-logo">
                    <img src="images/TheraPeace Logo.svg" alt="TheraPeace Logo">
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
    </body>
</html>




