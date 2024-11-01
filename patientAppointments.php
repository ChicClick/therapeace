<?php
include 'patient_get_appointments.php';
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Appointments</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="patientStyles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Architects+Daughter&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poly:ital@0;1&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <div class="wrapper">
    <!-- Navbar -->
    <nav>
        <div class="logo">
            <img src="images/logo.png" alt="TheraBee Logo" class="logo">
            <h1>TheraPeace</h1>
        </div>
        <ul class="nav-links">
            <li><a href="patientHomepage.php">Home</a></li>
            <li><a href="#appointments" data-nav-link>Appointments</a></li>
            <li><a href="#notes">Notes</a></li>
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

    <!-- Appointments Tab -->
    <section id="appointments" class="active">
        <h1>APPOINTMENTS</h1>
        <hr>
        <div class="search-sort-container">
            <div class="search-wrapper">
                <i class="fas fa-search search-icon"></i>
                <input type="text" placeholder="Search by therapist name..." id="appointmentsSearch" class="search-bar" onkeyup="searchAppointments()">
            </div>
        </div>
        <div class="appointments-header">
            <div class="header-schedule">
                SCHEDULE 
                <button onclick="sortAppointmentsByDate()" class="sort-button" title="Sort by Date">
                    <i class="fas fa-sort"></i>
                </button>
            </div>
            <div class="header-therapist">
                THERAPIST 
                <button onclick="sortAppointmentsByTherapist()" class="sort-button" title="Sort by Therapist">
                    <i class="fas fa-sort"></i>
                </button>
            </div>
            <div class="header-action">ACTION</div>
        </div>
        <div class="appointment-table">
            <?php if (!empty($appointments)): ?>
                <?php foreach ($appointments as $appointment): ?>
                    <div class="table-row appointment-row" data-therapist="<?php echo htmlspecialchars($appointment['therapistName']); ?>">
                        <div class="row-schedule"><?php $scheduleDate = date("F d, Y h:iA", strtotime(htmlspecialchars($appointment['schedule']))); echo $scheduleDate; ?></div>
                        <div class="row-therapist"><?php echo htmlspecialchars($appointment['therapistName']); ?></div>
                        <div class="row-reschedule">
                            <a href="#" class="reschedule">RESCHEDULE</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No appointments found.</p>
            <?php endif; ?>
        </div>
    </section>

        <!-- Notes Tab -->
        <section id="notes" class="hidden">
        <h1 id="session-feedback-header">SESSION FEEDBACK NOTES</h1>
        <hr>
        <!-- Notes Search and Sort Section -->
        <div id="notes-table"> <!-- Wrapper to hide the entire section -->
        <button id="generateReportButton">Generate Progress Report</button>
            <div id="progressReport" style="display: none;">
                <h3>Progress Report</h3>
                <p id="reportContent"></p>
            </div>

            <div class="search-sort-container">
                <div class="search-wrapper">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" placeholder="Search by therapist name..." id="notesSearch" class="search-bar" onkeyup="searchNotes()">
                </div>
            </div>
            <div class="notes-header">
                <div class="header-schedule">
                    SCHEDULE 
                    <button onclick="sortNotesByDate()" class="sort-button" title="Sort by Date">
                        <i class="fas fa-sort"></i>
                    </button>
                </div>
                <div class="header-therapist">
                    THERAPIST 
                    <button onclick="sortNotesByTherapist()" class="sort-button" title="Sort by Therapist">
                        <i class="fas fa-sort"></i>
                    </button>
                </div>
            </div>

            <div class="appointment-table">
                <?php if (!empty($sessions)): ?>
                    <?php foreach ($sessions as $session): ?>
                        <div class="table-row notes-row" 
                            data-session-id="<?php echo htmlspecialchars($session['sessionID']); ?>" 
                            data-schedule="<?php echo htmlspecialchars($session['sessionDate'] . ' ' . $session['sessionTime']); ?>" 
                            data-therapist="<?php echo htmlspecialchars($session['therapistName']); ?>">
                            <div class="row-schedule"><?php $sessionDateTime = date("F d, Y h:iA", strtotime($session['sessionDate'] . ' ' . $session['sessionTime'])); echo htmlspecialchars($sessionDateTime); ?></div>
                            <div class="row-therapist"><?php echo htmlspecialchars($session['therapistName']); ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No sessions found.</p>
                <?php endif; ?>
            </div>
        </div>
        </section>

        
        <!-- View Feedback Notes -->
        <section id="open-notes" class="hidden"> <!-- Initially hidden -->
            <div class="notes-container">
            <h3>
                <button id="back-to-appointments" class="back-button">&#60;</button>
                Session Overview
                <span id="note-schedule" class="hidden"></span> <!-- Keep it hidden if not needed -->
            </h3>
                <p>Therapist: <span id="note-therapist"></span></p>
                <div id="notes-content"></div>
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

    <!-- Footer -->
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

    <script src="patientScript.js"></script>
    </div>
</body>
</html>
