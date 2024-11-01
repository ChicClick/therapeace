<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['username'])) {
    // If not logged in, redirect to login page
    header("Location: adminlogin.php");
    exit;
}

// Get the logged-in admin
if (isset($_SESSION['firstname'])) {
    $firstname = $_SESSION['firstname'];
} else {
    $patientName = "Guest"; // Fallback in case the name is not set
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TheraPeace</title>
    <link rel="stylesheet" href="adash.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css" />
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
</head>
<body>
    <!-- Left Section -->
    <div class="left-section">
        <div class="logo">
            <img src="images/logo.png" alt="TheraPeace Logo">
            <h2>TheraPeace</h2>
        </div>
        <nav>
            <ul>
                <p>MENU</p>
                <li><a href="#" data-target="dashboard-section"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="#" data-target="appointments-section"><i class="fas fa-calendar-check"></i> Appointments</a></li>
                <li><a href="#" data-target="patients-information-section"><i class="fas fa-user"></i> Patient Information</a></li>
                <li><a href="#" data-target="staff-section"><i class="fas fa-users"></i> Staffs</a></li>
                <li><a href="#" data-target="services-section"><i class="fas fa-briefcase-medical"></i> Services</a></li>


                <p>OTHERS</p>
                <li><a href="#"><i class="fa fa-cog"></i> Settings</a></li>
                <li><a href="#"><i class="fa fa-users"></i> Manage Accounts</a></li>
                <li><a href="#"><i class="fa fa-info-circle"></i> Help</a></li>
                <li><a href="adminlogin.php"><i class="fa fa-sign-out"></i> Sign Out</a></li>
            </ul>
        </nav>
    </div>

    <!-- Right Section -->
    <div class="right-section">
        <div class="top-bar">
            <div class="search-bar">
                <input type="text" placeholder="Search">
                <button><i class="fas fa-search"></i></button>
            </div>
            <div class="profile-section">
                <img src="images/about 3.jpg" alt="Profile Picture">
                <span class="welcome-text">Welcome back, Admin <?php echo htmlspecialchars($firstname); ?>!</span>

            </div>
        </div>

        <!-- Dashboard Section -->
        <div id="dashboard-section" class="content active">
            <h4>DASHBOARD</h4>
            
        </div>

        <!-- Appointments Section -->
        <div id="appointments-section" class="content">
            <h4>APPOINTMENTS</h4>

            <!-- Add Appointment Button -->
                <button id="add-appointment-button" class="add-appointment-button">
                    <i class="fas fa-plus"></i> Add Appointment
                </button>

                <!-- Calendar Modal -->
                <div id="reschedulePopup" class="popup" style="display: none;">
                    <div class="popup-content">
                        <span class="close" onclick="closePopup()">&times;</span>
                        <h4>Select Available Dates</h4>
                        <div id="calendar">
                            <div class="calendar-container">
                            <div class="month-navigation">
                                <a href="#" id="prevMonth" class="nav-link-month">&lt;</a> <!-- Previous month link -->
                                <p id="currentMonth"></p> <!-- Dynamic month display -->
                                <a href="#" id="nextMonth" class="nav-link-month">&gt;</a> <!-- Next month link -->
                            </div>
                                <div class="calendar-grid">
                                    <!-- Calendar will be dynamically generated here -->
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="selectedDate" name="selectedDate" value="">
                        <button id="proceedButton">Proceed  →</button>
                    </div>
                </div>
                <div id="timePopup" class="popup" style="display: none;">
                    <div class="popup-content">
                        <span class="close" onclick="closeTimePopup()">&times;</span>
                        <h4>Select Available Times</h4>
                        <div id="availableTimes">
                            <h5>Morning Sessions</h5>
                            <ul id="morningTimes">
                                <!-- Morning times will be dynamically added here -->
                            </ul>
                            <h5>Afternoon Sessions</h5>
                            <ul id="afternoonTimes">
                                <!-- Afternoon times will be dynamically added here -->
                            </ul>
                        </div>

                            <!-- Hidden field to store selected time -->
                            <input type="hidden" id="selectedTime" name="selectedTime" value="">

                        <button id="proceedButton">Proceed  →</button>
                    </div>
                </div>
                 <!-- Hidden Pop-Up Form for Adding Appointment -->
            <div id="appointment-popup-form" class="popup-form">
                <div class="popup-content-form">
                    <span class="close-btn" id="close-popup">&times;</span>
                    <h2>Add Appointment</h2>
                    <h5>Fill Up Form</h5>
                    <form id="appointment-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="patient-ID">PatientID:</label>
                                    <select id="patient-ID" name="patient-ID" required onchange="toggleInput(this)">
                                        <option value="">Select PatientID</option>
                                            <?php
                                                require 'db_conn.php';
                                                $sql = "SELECT patientID, patientName FROM patient";
                                                $result = $conn->query($sql);

                                                if ($result->num_rows > 0) {
                                                    while ($row = $result->fetch_assoc()) {
                                                        echo "<option value='" . $row["patientID"] . "'>" . $row["patientID"] . "</option>";
                                                    }
                                                } else {
                                                    // echo "<option value=''>No patients available</option>";
                                                }
                                                $conn->close();
                                            ?>                                                                                
                                    </select>
                            </div>
                            <div class="form-group">
                                <label for="patient-name">Patient Name:</label>
                                <input type="text" id="patient-name" name="patient-name" required>
                            </div>
                            <div class="form-group">
                                <label for="parent-guardian">Parent/Guardian:</label>
                                <input type="text" id="parent-guardian" name="parent-guardian" required>
                            </div>
                            <div class="form-group">
                                <label for="contact-number">Contact Number:</label>
                                <input type="text" id="contact-number" name="contact-number" required>
                            </div>
                            <div class="form-group">
                                <label for="therapist">Therapist:</label>
                                <select id="therapist" name="therapist" required>
                                    <option value="">Select a Therapist</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group services-group">
                            <label>Services:</label>
                            <div class="services-checkbox">
                            <?php
                                require 'db_conn.php';
                                $sql = "SELECT serviceID, serviceName FROM services";
                                $result = $conn->query($sql);

                                if ($result->num_rows > 0) {
                                    // Output a checkbox for each service
                                    while ($row = $result->fetch_assoc()) {
                                        $serviceID = $row['serviceID'];
                                        $serviceName = $row['serviceName'];
                                        echo '<label><input type="checkbox" name="services[]" value="' . htmlspecialchars($serviceID) . '"> ' . htmlspecialchars($serviceName) . '</label><br>';
                                    }
                                } else {
                                    echo "No services found.";
                                }
                        
                                $conn->close();
                                ?>
                            </div>
                        </div>

                        <form id="appointment-form" method="POST" action="save_appointment.php">
                        <input type="hidden" id="selectedDateTime" name="schedule">
                        <input type="hidden" id="patientIDHidden" name="patientID">
                        <input type="hidden" id="parentIDHidden" name="parentID">
                        <input type="hidden" id="therapistIDHidden" name="therapistID">
                        <input type="hidden" id="serviceIDHidden" name="serviceID">
                        <button type="submit" class="submit-btn">Submit <i class="fas fa-arrow-right"></i></button>
                        </form>

                    </form>
                </div>
            </div>

            <div class="search-bar-content">
                <input type="text" placeholder="Search by patient or therapist">
                <button><i class="fas fa-search"></i></button>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>NAME</th>
                        <th>THERAPIST</th>
                        <th>SERVICES</th>
                        <th>SCHEDULES</th>
                    </tr>
                </thead>
                <tbody>
                    <?php include 'a_appointment.php'; ?> 
                </tbody>
            </table>
        </div>        
        <!-- Patient Information Section -->
        <div id="patients-information-section" class="content">
            <h4>PATIENT INFORMATION</h4>
            <div class="search-bar-content">
                <input type="text" placeholder="Search">
                <button><i class="fas fa-search"></i></button>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>NAME</th>
                        <th>PARENT/GUARDIAN</th>
                        <th>SERVICES</th>
                        <th>COMPLETION</th>
                    </tr>
                </thead>
                <tbody id="patients-tbody">
                    <?php include 'a_patients.php'; ?>
                </tbody>
            </table>

            <div class="patient-info" id="patient-info" style="display:none">
                <h4>PROFILES</h4>
                <div class="profile-header">
                    <img src="images/about 1.jpg" alt="Profile Picture" class="profile-picture">
                    <div class="profile-details">
                        <h2 id="patient-name">Name</h2>
                        <h3 id="service">Service</h3>
                    </div>
                </div>
                <div class="profile-info">
                    <h5>CONTACT INFORMATION</h5>
                    <div class="contact-info-wrapper">
                        <div class="contact-info-main">
                            <p><strong>Parent/Guardian:</strong> <span id="parent-name"></span></p>
                            <p><strong>Phone:</strong> <span id="phone"></span></p>
                            <p><strong>Email:</strong> <span id="email"></span></p>
                        </div>
                        <div class="contact-info-additional">
                            <p><strong>Address:</strong> <span id="address"></span></p>
                        </div>
                    </div>
                    <h5>BASIC INFORMATION</h5>
                    <p><strong>Birthday:</strong> <span id="birthday"></span></p>
                    <p><strong>Gender:</strong> <span id="gender"></span></p>
                </div>
            </div>
        </div>       
        <!-- Staff Section -->
        <div id="staff-section" class="content">
            <div class="staff-header">
            <h4>STAFFS</h4>
            <!-- Add Staff Button -->
                <button id="add-staff-button" class="add-staff-button">
                    <i class="fas fa-plus"></i> Add Staff
                </button>
            </div>
            
            <div class="staff-container">
                    <div class="button-container">
                        <button id="clinic-staff-btn" class="clinic-staff" onclick="setActive('clinic-staff-table')">
                            <i class="fas fa-user"></i> Clinic Staff
                        </button>
                        <!-- Clinic Staff Table -->
                        <div id="clinic-staff-table" class="table-container hidden"> 
                            <table>
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Position</th>
                                        <th>Date Hired</th>
                                    </tr>
                                </thead>
                                <tbody id="staff-table">
                                    <?php include 'a_staff.php'; ?>
                                </tbody>
                            </table>
                        </div>

                        <button id="clinic-admin-btn" class="clinic-admin" onclick="setActive('clinic-admin-table')">
                            <i class="fas fa-user-tie"></i> Clinic Admin
                        </button>
                        <!-- Clinic Admin Table -->
                        <div id="clinic-admin-table" class="table-container hidden"> 
                            <table>
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Contact Number</th>
                                        <th>Birth Date</th>
                                    </tr>
                                </thead>
                                <tbody id="staff-table">
                                    <?php include 'a_admin.php'; ?>
                                </tbody>
                            </table>
                        </div>

                        <button id="clinic-therapist-btn" class="clinic-therapist" onclick="setActive('clinic-therapist-table')">
                            <i class="fas fa-user-md"></i> Therapists
                        </button>
                        <!-- Therapists Table -->
                        <div id="clinic-therapist-table" class="table-container hidden"> 
                            <table>
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Position</th>
                                        <th>Date Hired</th>
                                    </tr>
                                </thead>
                                <tbody id="staff-table">
                                    <?php include 'a_therapist.php'; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
            <script>
                function setActive(button) {
                    // Remove the active class from all buttons
                    const buttons = document.querySelectorAll('.button-container button');
                    buttons.forEach(btn => btn.classList.remove('active'));
                    
                    // Add the active class to the clicked button
                    button.classList.add('active');
                }
            </script>
            </div>    
        </div>   

         <!-- Services Section -->
        <div id="services-section" class="content">
            <h4>MANAGE SERVICES</h4>
            
            <table>
                <thead>
                    <tr>
                        <th>NAME OF SERVICE</th>
                        <th>AVAILABILITY</th>
                        <th>DESCRIPTION</th>
                    </tr>
                </thead>
                <tbody id="services-tbody">
                    <?php include 'a_services.php'; ?>
                </tbody>
            </table>
            <div class="service-info" id="service-info" style="display:none">
                <h4>VIEW SERVICE</h4>
                    <div class="service-header">
                        <img src="images/about 1.jpg" alt="Profile Picture" class="service-picture">
                        <div class="service-details">
                            <h2 id="service-name"></h2>
                            <h3 id="service-description"></h3>
                            <h4 id="service-price"></h4>
                        </div>

                        <button class="edit-service" id="edit-service">Edit Service</button>
                            <!-- Hidden Pop-Up Form for Editing Services -->
                        <div id="service-popup" class="popup-form">
                            <div class="popup-content-form">
                                <span class="close-btn" id="close-popup">&times;</span>
                                <h2>Manage Service</h2>
                                <form id="service-form">
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="service-name">Service Name:</label>
                                            <input type="text" id="service-name" name="service-name" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="availability">Availability:</label>
                                            <input type="text" id="availability" name="availability" placeholder="Available/Not Available" required>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="description">Description:</label>
                                            <input type="text" id="description" name="description" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="about">About:</label>
                                            <input type="text" id="about" name="about" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="price">Price:</label>
                                            <input type="text" id="price" name="price" required>
                                        </div>
                                    </div>

                                    <button type="submit" class="submit-btn">Submit <i class="fas fa-arrow-right"></i></button>
                                </form>
                            </div>
                        </div>

                        <script>
                            // Get the button that opens the popup
                            const editServiceButton = document.querySelector('.edit-service');
                            // Get the popup
                            const popup = document.getElementById('service-popup');
                            // Get the <span> element that closes the popup
                            const closePopupButton = document.getElementById('close-popup');
                            const serviceForm = document.getElementById('service-form');

                            // When the user clicks the button, open the popup
                            editServiceButton.addEventListener('click', function() {
                                popup.style.display = 'block';
                            });

                            // When the user clicks on <span> (x), close the popup
                            closePopupButton.addEventListener('click', function() {
                                popup.style.display = 'none';
                            });

                            // When the user clicks anywhere outside of the popup content, close the popup
                            window.addEventListener('click', function(event) {
                                if (event.target === popup) {
                                    popup.style.display = 'none';
                                }
                            });

                            // Handle form submission
                            serviceForm.addEventListener('submit', function(event) {
                                event.preventDefault(); // Prevent the default form submission

                                // Gather form data
                                const formData = new FormData(serviceForm);

                                // Send data to the server using AJAX
                                fetch('a_manageservice_endpoint.php', { // Replace with your actual server endpoint
                                    method: 'POST',
                                    body: formData
                                })
                                .then(response => response.json())
                                .then(data => {
                                    // Handle success or failure
                                    if (data.success) {
                                        alert('Service updated successfully!');
                                        popup.style.display = 'none'; // Close the popup
                                        // Optionally, refresh or update your services list
                                    } else {
                                        alert('Error updating service: ' + data.message);
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    alert('There was an error with the request.');
                                });
                            });
                        </script>

                        </div>
                            <div class="service-about">
                                <p id="service-about"></p>
                            </div>
                        </div>

            <script>
                // Select all service rows
                const serviceRows = document.querySelectorAll('.service-row');

                // Add a click event listener to each row
                serviceRows.forEach(row => {
                    row.addEventListener('click', function() {
                        // Fetch data from the clicked row
                        const serviceName = this.getAttribute('data-service-name');
                        const serviceAvailability = this.getAttribute('data-service-availability');
                        const serviceDescription = this.getAttribute('data-service-description');
                        const servicePrice = this.getAttribute('data-service-price');
                        const serviceAbout = this.getAttribute('data-service-about'); // Fetch service-about

                        // Populate the service info section
                        document.getElementById('service-name').innerText = serviceName;
                        document.getElementById('service-description').innerText = serviceDescription;
                        document.getElementById('service-price').innerText = servicePrice;
                        document.getElementById('service-about').innerText = serviceAbout; // Display service-about

                        // Display the service info section
                        document.getElementById('service-info').style.display = 'block';

                        // Optionally, scroll to the service info section
                        document.getElementById('service-info').scrollIntoView({ behavior: 'smooth' });
                    });
                });
            </script>

        </div>       

    </div>

    <script src="adash.js" defer></script>
</body>
</html>