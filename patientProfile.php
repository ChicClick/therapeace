<?php
include 'patient_get_profile.php';
include 'patient_profile_functions.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
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
                <li><a href="patientAppointments.php#appointments">Appointments</a></li>
                <li><a href="patientAppointments.php#notes">Notes</a></li>
            </ul>
            <div class="user-greeting">
                <span class="welcome-text">Welcome back, <?php echo htmlspecialchars($patientName); ?>!</span>
                <i class="notification-icon">🔔</i>
                <div class="dropdown">
                    <button class="dropbtn">▼</button>
                    <div class="dropdown-content">
                        <a href="patientProfile.php">Edit Profile</a>
                        <a href="settings.php">Settings</a>
                        <a href="#" id="logoutBtn">Log Out</a>
                    </div>
                </div>
            </div>
        </nav>

         <!-- Profile Section -->
         <section id="profile-section">
            <h1>PROFILE</h1>
            <hr>
            <div class="profile-container">
                <div class="profile-header">
                    <div class="profile-info">
                        <h2><?php echo htmlspecialchars($patientName); ?></h2>
                    </div>
                    <button id="edit-button" class="edit-button" onclick="toggleEditProfile()">Edit Profile</button>
                </div>

                <div class="profile-details">
                    <h3>Contact Information</h3>    
                    <div class="info-section">
                        <div class="info-block">
                            <p><strong>Phone:</strong> <?php echo htmlspecialchars($patientPhone); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($patientEmail); ?></p>
                            <p><strong>Address:</strong> <?php echo htmlspecialchars($patientAddress); ?></p>
                        </div>
                        <div class="info-block">
                            <p><strong>Parent/Guardian:</strong> <?php echo htmlspecialchars($parentName); ?></p>
                            <p><strong>Relationship:</strong> <?php echo htmlspecialchars($patientRelationship); ?></p>
                        </div>
                    </div>

                    <h3>Basic Information</h3>  
                    <div class="info-section">
                        <div class="info-block">
                            <p><strong>Birthday:</strong> <?php echo htmlspecialchars($patientBirthday); ?></p>
                        </div>
                        <div class="info-block">
                            <p><strong>Gender:</strong> <?php echo htmlspecialchars($patientGender); ?></p>
                        </div>
                    </div>

                    <h3>About Sessions</h3>
                    <div class="info-section">
                        <div class="info-block">
                        <p><strong>Therapist:</strong> <?php echo htmlspecialchars($therapistName); ?></p>
                        <p><strong>Therapy Type:</strong> <?php echo htmlspecialchars($serviceName); ?></p>
                        </div>
                        <div class="info-block">
                        <p><strong>Schedule:</strong> <?php echo htmlspecialchars($formattedSchedule); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Edit Profile Section (Initially Hidden) -->
        <section id="edit-profile-section" style="display: none;">
            <h1>EDIT PROFILE</h1>
            <hr>
            <div class="profile-container">
                <h3>BASIC INFORMATION</h3>
                <form action="patient_profile_functions.php" method="POST">

                    <div class="input-group">
                    <div class="input-field">
                        <label for="patientName">Patient Name:</label>
                        <input type="text" name="patientName" id="patientName" value="<?php echo htmlspecialchars($patientName); ?>" required>
                    </div>
                    <div class="input-field">
                        <label for="parentName">Parent Name:</label>
                        <input type="text" name="parentName" id="parentName" value="<?php echo htmlspecialchars($parentName); ?>" required>
                    </div>
                    <div class="input-field">
                        <label for="relationship">Relationship:</label>
                        <input type="text" name="relationship" id="relationship" value="<?php echo htmlspecialchars($patientRelationship); ?>" required>
                    </div>
                    </div>

                    <!-- Grouping Phone, Email, Birthday, and Gender side by side -->
                    <div class="input-group">
                        <div class="input-field">
                            <label for="phone">Phone:</label>
                            <input type="text" name="phone" id="phone" value="<?php echo htmlspecialchars($patientPhone); ?>" required>
                        </div>
                        <div class="input-field">
                            <label for="email">Email:</label>
                            <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($patientEmail); ?>" required>
                        </div>
                        <div class="input-field">
                            <label for="birthday">Birthday:</label>
                            <input type="date" name="birthday" id="birthday" value="<?php echo htmlspecialchars($patientBirthday); ?>" required>
                        </div>
                        <div class="input-field">
                            <label for="gender">Gender:</label>
                            <input type="text" name="gender" id="gender" value="<?php echo htmlspecialchars($patientGender); ?>" required>
                        </div>
                    </div>
                    <div class="input-field">
                        <label for="address">Address:</label>
                        <input type="text" name="address" id="address" value="<?php echo htmlspecialchars($patientAddress); ?>" required>
                    </div>

                    <button type="submit">Save Changes</button>
                </form>
            </div>
        </section>

        <!-- Footer -->
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
        </footer>
    </div>
    <script src="patientScript.js"></script>
</body>
</html>
