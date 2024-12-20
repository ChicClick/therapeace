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
    $firstname = "Guest"; // Fallback in case the name is not set
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TheraPeace</title>
    <link rel="icon" type="image/svg+xml" href="images/TheraPeace Logo.svg">
    <link rel="stylesheet" href="adash.css">
    <link rel="stylesheet" href="dashboard-table.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css" />
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

</head>
<body>
    <generic-side-view-bar></generic-side-view-bar>
    <!-- Left Section -->
    <div class="left-section">
        <div class="logo">
            <!-- Logo Update -->
            <img src="images/TheraPeace Logo.svg" alt="TheraPeace Logo">
            <h2>TheraPeace</h2>
        </div>
        <button class="hamburger-menu" id="hamburgerMenu">
            <i class="fas fa-bars"></i> <!-- Hamburger icon -->
        </button>
        <nav class="navbar">
            <ul>
                <p>MENU</p>
                <li><a href="#" data-target="dashboard-section"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="#" data-target="appointments-section"><i class="fas fa-calendar-check"></i> Appointments</a></li>
                <li><a href="#" data-target="patients-information-section"><i class="fas fa-user"></i> Patient Information</a></li>
                <li><a href="#" data-target="staff-section"><i class="fas fa-users"></i> Staffs</a></li>
                <li><a href="#" data-target="services-section"><i class="fas fa-briefcase-medical"></i> Services</a></li>
                <li><a href="#" data-target="checklist-section"><i class="fas fa-clipboard-list"></i> Pre-Screening Response</a></li>
                <li><a href="#" data-target="feedbacks-section"><i class="fas fa-comment"></i> Feedbacks</a></li>

                <p>OTHERS</p>
                <!-- Removed the manage account tab -->
                <li><a href="#" id="changePassword" onclick="changePassword(event)"> <i class="fa fa-key"></i> Change Password</a></li>
                <li><a href="#" data-target="edit-profile-section"><i class="fa fa-cog"></i> Edit Profile</a></li>
                <li><a href="#" id="backupData" onclick="backupData(event)"> <i class="fa-solid fa-file"></i> Download Data</a></li>
                <li><a href="#" id="logoutBtn"><i class="fa fa-sign-out"></i>Log Out</a></li>
            </ul>
        </nav>
    </div>
    <!-- Logout Confirmation Modal -->
    <div id="logoutModal" class="modal">
        <div class="modal-content-logout">
            <span class="close" id="closeModal">&times;</span>
            <h2>Confirm Logout</h2>
            <p>Are you sure you want to log out?</p>
            <button id="confirmLogout">Yes, log me out</button>
            <button id="cancelLogout">Cancel</button>
        </div>
    </div>

    <!-- Right Section -->
    <div class="right-section">
    <generic-message-popup></generic-message-popup>
        <div class="top-bar">

            <!-- removed the search bar -->

            <div class="profile-section">
                <!-- removed the profile picture -->
                <span class="welcome-text">Welcome back, Admin!</span>

            </div>
        </div>
                    <!-- Dashboard Section -->
            <div id="dashboard-section" class="content active">
                <h4>DASHBOARD</h4>

                <div class="dashboard">
                    <div class="data-section">
                        <div class="data-section-header">
                            <div>
                                <h3>Data</h3>
                                
                                <h1>Loading...</h1>
                                <p class="growth-percentage">+ 2.1% vs last week</p>
                                <p class="date-range">Patients from 1-12 August, 2024</p>
                            </div>
                            <div>
                                <button class="view-report">Generate Report</button>
                            </div>
                        </div>
                        <canvas id="patientChart"></canvas>
                    </div>
                    <div class="dashboard-appointment-section">
                        <h3>Appointments</h3>
                        <canvas id="appointmentChart"></canvas>
                    </div>
                </div>
                <div class="dashboard-table">
                    <h3>Therapists</h3>
                    <generic-table data="admin_dashboard" color="true"></generic-table>
                </div>
               
            </div>

        <!-- Appointments Section -->
        <div id="appointments-section" class="content">
            <h4>APPOINTMENTS</h4>

            <!-- Add Appointment Button -->
                <button id="add-appointment-button" class="add-appointment-button">
                    <i class="fas fa-plus"></i> Add Appointment
                </button>

                <!-- Calendar Modal -->
                <generic-calendar></generic-calendar>

                  <!-- Hidden Pop-Up Form for Adding Appointment -->
                  <div id="appointment-popup-form" class="popup-form">
                    <div class="popup-content-form">
                        <span class="close-btn" id="close-popup">&times;</span>
                        <h2>Add Appointment</h2>
                        <h5>Fill Up Form</h5>
                        <form id="appointment-form">
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="required" for="patient-ID">PatientID:</label>
                                    <select id="patient-ID" name="patient-ID" required>
                                        <option value="">Select PatientID</option>
                                        <?php
                                        require 'db_conn.php';
                                        $sql = "SELECT patientID, patientName FROM patient";
                                        $result = $conn->query($sql);
                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                echo "<option value='" . $row["patientID"] . "'>" . $row["patientID"] . " - " . $row["patientName"] . "</option>";
                                            }
                                        }
                                        $conn->close();
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="required" for="patient-name">Patient Name:</label>
                                    <input type="text" id="patient-name" name="patient-name" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label class="required" for="parent-guardian">Parent/Guardian:</label>
                                    <input type="text" id="parentID" name="parentID" required>
                                </div>
                                <div class="form-group">
                                    <label class="required" for="contact-number">Contact Number:</label>
                                    <input type="text" id="contact-number" name="contact-number" required>
                                </div>
                            </div>

                            <div class="form-row">
                                        <div class="form-group">
                                        <label class="required" for="serviceID">Services:</label>
                                            <select onchange="updateSpecializationAppointment()" id="serviceID" name="serviceID" required>
                                                <option value="">Select Available Service</option>
                                                <?php
                                                require 'db_conn.php';
                                                $sql = "SELECT serviceID, serviceName FROM services WHERE availability = 'Available'";
                                                $result = $conn->query($sql);
                                                if ($result->num_rows > 0) {
                                                    while ($row = $result->fetch_assoc()) {
                                                        echo "<option value='" . $row["serviceID"] . "'>" . $row["serviceName"] . "</option>";
                                                    }
                                                }
                                                $conn->close();
                                                ?>
                                        </select>
                                        </div>

                            </div>
                            <hr />
                            <h5>Filters</h5>

                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="searchByName">Search by Name:</label>
                                            <input type="text" id="searchByName" name="searchByName">
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="days_available">Day Availability</label>
                                            <select id="day-appointment-availability" name="days_available" onchange="toggleAppointmentCustomDay()">
                                                <option value="[1,2,3,4,5,6]" selected>Full-Time</option>
                                                <option id="custom-appointment-day" value="[]">Custom</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <div id="custom-appointment-day-options" style="display: none; margin-top: 10px;">
                                                <label>Select Custom Days:</label>
                                                <div class="day-list-checkbox">
                                                    <div class="day-list"><input type="checkbox" value="1" onchange="updateAppointmentCustomDay()"> <label>M</label></div>
                                                    <div class="day-list"><input type="checkbox" value="2" onchange="updateAppointmentCustomDay()"> <label>T</label></div>
                                                    <div class="day-list"><input type="checkbox" value="3" onchange="updateAppointmentCustomDay()"> <label>W</label></div>
                                                    <div class="day-list"><input type="checkbox" value="4" onchange="updateAppointmentCustomDay()"> <label>TH</label></div>
                                                    <div class="day-list"><input type="checkbox" value="5" onchange="updateAppointmentCustomDay()"> <label>F</label></div>
                                                    <div class="day-list"><input type="checkbox" value="6" onchange="updateAppointmentCustomDay()"> <label>SA</label></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>               
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="time-availability">Time Availability</label>
                                            <select id="time-appointment-availability" name="times_available" onchange="toggleCustomAppointmentTime()">
                                                <option value="[9,10,11,13,14,15,16,17]" selected>Full-Time</option>
                                                <option value="[9,10,11]">Morning Shift</option>
                                                <option value="[13,14,15,16,17]">Afternoon</option>
                                                <option id="custom-appointment-time" value="[]">Custom</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <div id="custom-appointment-time-options" style="display: none; margin-top: 10px;">
                                                <label>Select Custom Hours:</label>
                                                <div class="time-list-checkbox">
                                                    <div class="time-list"><input type="checkbox" value="9" onchange="updateAppointCustomTime()"> <label>9 AM</label></div>
                                                    <div class="time-list"><input type="checkbox" value="10" onchange="updateAppointCustomTime()"> <label>10 AM</label></div>
                                                    <div class="time-list"><input type="checkbox" value="11" onchange="updateAppointCustomTime()"> <label>11 AM</label></div>
                                                    <div class="time-list"><input type="checkbox" value="13" onchange="updateAppointCustomTime()"> <label>1 PM</label></div>
                                                    <div class="time-list"><input type="checkbox" value="14" onchange="updateAppointCustomTime()"> <label>2 PM</label></div>
                                                    <div class="time-list"><input type="checkbox" value="15" onchange="updateAppointCustomTime()"> <label>3 PM</label></div>
                                                    <div class="time-list"><input type="checkbox" value="16" onchange="updateAppointCustomTime()"> <label>4 PM</label></div>
                                                    <div class="time-list"><input type="checkbox" value="17" onchange="updateAppointCustomTime()"> <label>5 PM</label></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group">
                                        <label for="appointmentCommunication">Communication Methods:</label>
                                            <div class="communication-checkboxes">
                                                <?php
                                                    require 'db_conn.php';
                                                    $sql = "SELECT * FROM communication";
                                                    $result = $conn->query($sql);
                                                    if ($result->num_rows > 0) {
                                                        while ($row = $result->fetch_assoc()) {
                                                            echo "<div class='checkbox-item'>";
                                                            echo "<input type='checkbox' name='appointmentCommunication[]' value='" . $row["id"] . "' id='app_comm_" . $row["id"] . "' onchange='updateAppointmentCommunication()'>";
                                                            echo "<label data-title='" . $row["description"] . "' for='app_comm_" . $row["id"] . "'>" . $row["name"] . "</label>";
                                                            echo "</div>";
                                                        }
                                                    }
                                                    $conn->close();
                                                    ?>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="form-group">
                                                                                            <label for="appointmentFlexibility">Flexibility:</label>
                                                                                                <div class="flexibility-checkboxes">
                                                                                                    <?php
                                                    require 'db_conn.php';
                                                    $sql = "SELECT * FROM flexibility";
                                                    $result = $conn->query($sql);
                                                    if ($result->num_rows > 0) {
                                                        while ($row = $result->fetch_assoc()) {
                                                            echo "<div class='checkbox-item'>";
                                                            echo "<input type='checkbox' name='appointmentFlexibility[]' value='" . $row["id"] . "' id='app_flex_" . $row["id"] . "' onchange='updateAppointmentFlexibility()'>";
                                                            echo "<label data-title='" . $row["description"] . "' for='app_flex_" . $row["id"] . "'>" . $row["name"] . "</label>";
                                                            echo "</div>";
                                                        }
                                                    }
                                                    $conn->close();
                                                    ?>
                                            </div>
                                        </div>
                                    </div>
                            <div class="form-row">
                                <div class="form-group">

                                </div>
                            </div>

                            <input type="hidden" id="selectedDateTime" name="schedule">
                            <div class="btn-container">
                                <button type="button" class="reset-btn" onclick="resetFilters()">Filters <i class="fas fa-eraser"></i></button>
                                <button type="button" class="submit-btn" onclick="submitAppointmentForm()">Submit <i class="fas fa-arrow-right"></i></button>
                            </div>
                        </form>

                    </div>
                    <generic-widget></generic-widget>
                </div>
            <div class="search-bar-content">
                <input type="text" placeholder="Search by patient or therapist" id="adminSearchAppointments" onkeyup="filterSearchAdminAppointment()">
                <button><i class="fas fa-search"></i></button>
            </div>

                    <!-- FOR REFERENCE DO NOT REMOVE 'a_appointment.php';?> -->
            <div class="dashboard-table">
                <generic-table id="table-appointments-admin" data="admin_appointments" avatar="true"></generic-table>
            </div>

        </div>
        <!-- Patient Information Section -->
        <div id="patients-information-section" class="content">
            <h4>PATIENT INFORMATION</h4>

            <button id="add-patients" class="add-staff">&nbsp;&nbsp;&nbsp;&nbsp;<i class="fas fa-plus"></i> Add &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <div class="dropdown-menu-patient .show">
                    <div id="add-patient-button"  class="dropdown-item">Patient</div>
                    <div id="add-parent-button" class="dropdown-item">Parent</div>
                </div>
            </button>
            <div class="search-bar-content">
                <input type="text" placeholder="Search Table" id="adminSearchPatients" onkeyup="filterSearchAdminPatient()">
                <button><i class="fas fa-search"></i></button>
            </div>

            <div class="tabs-container">
                <div class="tab tab-staff active" data-target="content-patients">Patients</div>
                <div class="tab tab-clients" data-target="content-parents">Parents</div>
            </div>

                <div id="content-patients" class="content-container active">
                    <generic-table id="table-admin-patients" data="admin_patients" avatar="true" edit="true"></generic-table>
                </div>                                    
                <div id="content-parents" class="content-container">
                    <generic-table id="table-admin-parents" data="admin_parents"></generic-table>     
                </div>

                <script>
                const tabsPatient = document.querySelectorAll('.tab');
                const contentsPatient = document.querySelectorAll('.content-container');

                tabsPatient.forEach(tab => {
                    tab.addEventListener('click', () => {
                   
                    tabsPatient.forEach(t => t.classList.remove('active'));
                    contentsPatient.forEach(c => c.classList.remove('active'));

                   
                    tab.classList.add('active');
                    document.getElementById(tab.dataset.target).classList.add('active');
                    });
                });
                </script>

                    <!--'a_patients.php'; -->
        </div>
        <!-- Staff Section -->
        <div id="staff-section" class="content">
            <div class="staff-header">
            <h4>STAFFS</h4>
            <!-- Add Staff Button -->
                <button id="add-staff" class="add-staff">&nbsp;&nbsp;&nbsp;&nbsp;<i class="fas fa-plus"></i> Add &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <div class="dropdown-menu .show">
                        <div class="dropdown-item" onclick="staff()">Staff</div>
                        <div class="dropdown-item" onclick="therapist()">Therapist</div>
                    </div>
            </button>

                    <!-- Hidden Pop-Up Form for Adding Staff -->
                <div id="add-staff-popup" class="popup-form">
                            <div class="popup-content-form popup-margin">
                                <span class="close-btn" id="close-add-staff">&times;</span>
                                <h2 id="add-staff-popup-title">Add Staff</h2>
                                <form id="addstaff-form">
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="staffName">Staff Name: <span style="color: red;">*</span></label>
                                            <input type="text" id="staffName" name="staffName" placeholder="e.g. John Doe" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="position">Position: <span style="color: red;">*</span></label>
                                            <input type="text" id="position" name="position" placeholder="Teacher/Helper etc." required>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="phoneNumber">Contact Number: <span style="color: red;">*</span></label>
                                            <input type="tel" id="phoneNumber" name="phoneNumber" placeholder="09xxxxxxxxx" required minlength="11" maxlength="11" pattern="^[0-9]{11}$" title="Please enter an 11-digit contact number" oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                                        </div>
                                        <div class="form-group">
                                            <label for="address">Home Address: <span style="color: red;">*</span></label>
                                            <input type="text" id="address" name="address" placeholder="e.g. 123 Elm St., Brgy 1" required>
                                        </div>
                                        <div class="form-group">
                                        <label for="gender">Sex: <span style="color: red;">*</span></label>
                                            <select id="gender" name="gender" required>
                                                <option value="" disabled selected>Select Sex</option>
                                                <option value="Male">Male</option>
                                                <option value="Female">Female</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="datehired">Date Hired: <span style="color: red;">*</span></label>
                                            <input type="date" id="datehired" name="datehired" required>
                                        </div>
                                    </div>

                                    <div class="btn-container">
                                        <button type="submit" class="submit-btn">Submit <i class="fas fa-arrow-right"></i></button>
                                    </div>

                                </form>
                            </div>
                </div>

                <div id="add-therapist-popup" class="popup-form">
                            <div class="popup-content-form popup-margin">
                                <span class="close-btn" id="close-add-therapist">&times;</span>
                                <h2 id="add-staff-popup-title">Add Therapist</h2>
                                <form id="addtherapist-form">
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="therapistID">Therapist ID:</label>
                                            <input type="text" id="therapistID" name="therapisID" placeholder="Therapist ID" readonly required>
                                        </div>
                                        <div class="form-group">
                                            <label for="password">Password: <span style="color: red;">*</span></label>
                                            <input type="password" id="password" name="password" required pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$" 
                                                title="Password must be at least 8 characters long, contain at least one lowercase letter, uppercase letter, special character and a number.">
                                        </div>
                                        <div class="form-group">
                                            <label for="therapistName">Name: <span style="color: red;">*</span></label>
                                            <input type="text" id="therapistName" name="therapistName" placeholder="Enter Full Name" required>
                                        </div>
                                        <div class="form-group">
                                        <label for="specialization">Specialization: <span style="color: red;">*</span></label>
                                        <select id="specialization" name="specialization" required>
                                            <option value="" disabled selected>Select a specialization</option>
                                            <?php
                                            require 'db_conn.php';
                                            $sql = "SELECT serviceID, serviceName FROM services";
                                            $result = $conn->query($sql);
                                            if ($result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    echo "<option value=" . $row["serviceID"] . ">" . $row["serviceName"] . "</option>";
                                                }
                                            } else {
                                                echo "<option value='' disabled>No services available</option>";
                                            }
                                            $conn->close();
                                            ?>
                                        </select>

                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="email">Email: <span style="color: red;">*</span></label>
                                            <input type="email" id="email" name="email" placeholder="Enter a valid email" required 
                                                pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$" 
                                                title="Please enter a valid email address (e.g., yourname@gmail.com)">
                                        </div>
                                        <div class="form-group">
                                            <label for="phone">Contact Number: <span style="color: red;">*</span></label>
                                            <input type="text" id="phone" name="phone" placeholder="09xxxxxxxxx" required minlength="11" maxlength="11" pattern="^[0-9]{11}$" title="Please enter an 11-digit contact number" oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                                        </div>
                                        <div class="form-group">
                                            <label for="address">Home Address: <span style="color: red;">*</span></label>
                                            <input type="text" id="address" name="address" placeholder="e.g. 123 Elm St., Brgy 1" required>
                                        </div>
                                        <div class="form-group">
                                        <label for="gender">Sex: <span style="color: red;">*</span></label>
                                            <select id="gender" name="gender" required>
                                                <option value="" disabled selected>Select Sex</option>
                                                <option value="Male">Male</option>
                                                <option value="Female">Female</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="datehired">Date Hired: <span style="color: red;">*</span></label>
                                            <input type="date" id="datehired" name="datehired" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="birthday">Birth Date: <span style="color: red;">*</span></label>
                                            <input type="date" id="birthday" name="birthday" required>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="day-availability">Day Availability</label>
                                            <select id="day-availability" name="days_available" required onchange="toggleCustomDay()">
                                                <option value="[1,2,3,4,5,6]" selected>Full-Time</option>
                                                <option id="custom-day" value="[]">Custom</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <div id="custom-day-options" style="display: none; margin-top: 10px;">
                                                <label>Select Custom Days:</label>
                                                <div class="day-list-checkbox">
                                                    <div class="day-list"><input type="checkbox" value="1" onchange="updateCustomDay()"> <label>M</label></div>
                                                    <div class="day-list"><input type="checkbox" value="2" onchange="updateCustomDay()"> <label>T</label></div>
                                                    <div class="day-list"><input type="checkbox" value="3" onchange="updateCustomDay()"> <label>W</label></div>
                                                    <div class="day-list"><input type="checkbox" value="4" onchange="updateCustomDay()"> <label>TH</label></div>
                                                    <div class="day-list"><input type="checkbox" value="5" onchange="updateCustomDay()"> <label>F</label></div>
                                                    <div class="day-list"><input type="checkbox" value="6" onchange="updateCustomDay()"> <label>SA</label></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="time-availability">Time Availability</label>
                                            <select id="time-availability" name="times_available" required onchange="toggleCustomTime()">
                                                <option value="[9,10,11,13,14,15,16,17]" selected>Full-Time</option>
                                                <option value="[9,10,11]">Morning Shift</option>
                                                <option value="[13,14,15,16,17]">Afternoon</option>
                                                <option id="custom-time" value="[]">Custom</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <div id="custom-time-options" style="display: none; margin-top: 10px;">
                                                <label>Select Custom Hours:</label>
                                                <div class="time-list-checkbox">
                                                    <div class="time-list"><input type="checkbox" value="9" onchange="updateCustomTime()"> <label>9 AM</label></div>
                                                    <div class="time-list"><input type="checkbox" value="10" onchange="updateCustomTime()"> <label>10 AM</label></div>
                                                    <div class="time-list"><input type="checkbox" value="11" onchange="updateCustomTime()"> <label>11 AM</label></div>
                                                    <div class="time-list"><input type="checkbox" value="13" onchange="updateCustomTime()"> <label>1 PM</label></div>
                                                    <div class="time-list"><input type="checkbox" value="14" onchange="updateCustomTime()"> <label>2 PM</label></div>
                                                    <div class="time-list"><input type="checkbox" value="15" onchange="updateCustomTime()"> <label>3 PM</label></div>
                                                    <div class="time-list"><input type="checkbox" value="16" onchange="updateCustomTime()"> <label>4 PM</label></div>
                                                    <div class="time-list"><input type="checkbox" value="17" onchange="updateCustomTime()"> <label>5 PM</label></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group">
                                        <label for="communication">Communication Methods:</label>
                                            <div class="communication-checkboxes">
                                                <?php
                                                    require 'db_conn.php';
                                                    $sql = "SELECT * FROM communication";
                                                    $result = $conn->query($sql);
                                                    if ($result->num_rows > 0) {
                                                        while ($row = $result->fetch_assoc()) {
                                                            // Create a checkbox for each communication method from the database
                                                            echo "<div class='checkbox-item'>";
                                                            echo "<input type='checkbox' name='communication[]' value='" . $row["id"] . "' id='comm_" . $row["id"] . "' onchange='updateCommunication()'>";
                                                            echo "<label data-title='" . $row["description"] . "' for='comm_" . $row["id"] . "'>" . $row["name"] . "</label>";
                                                            echo "</div>";
                                                        }
                                                    }
                                                    $conn->close();
                                                    ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                        <label for="flexibility">Flexibility:</label>
                                            <div class="communication-checkboxes">
                                                <?php
                                                    require 'db_conn.php';
                                                    $sql = "SELECT * FROM flexibility";
                                                    $result = $conn->query($sql);
                                                    if ($result->num_rows > 0) {
                                                        while ($row = $result->fetch_assoc()) {
                                                            echo "<div class='checkbox-item'>";
                                                            echo "<input type='checkbox' name='flexibility[]' value='" . $row["id"] . "' id='flex_" . $row["id"] . "' onchange='updateFlexibility()'>";
                                                            echo "<label data-title='" . $row["description"] . "' for='flex_" . $row["id"] . "'>" . $row["name"] . "</label>";
                                                            echo "</div>";
                                                        }
                                                    }
                                                    $conn->close();
                                                    ?>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" id="communication" name="communication" value="[]">
                                    <input type="hidden" id="flexibility" name="flexibility" value="[]">

                                    <div class="btn-container">
                                        <button type="submit" class="submit-btn">Submit <i class="fas fa-arrow-right"></i></button>
                                    </div>

                                </form>
                            </div>
                </div>
            </div>


            <div class="tabs-container">
                <div class="tab tab-staff active" data-target="content-staff">Staffs</div>
                <div class="tab tab-clients" data-target="content-admins">Admins</div>
                <div class="tab tab-projects" data-target="content-therapists">Therapist</div>
            </div>

                <div id="content-staff" class="content-container active">
                        <generic-table data="admin_staffs" edit="true"></generic-table>
                        <!-- include 'a_staff.php' -->
                </div>                                    
                <div id="content-admins" class="content-container">
                    <generic-table data="admin_admins"></generic-table>
                        <!--php include 'a_admin.php';-->
                 
                </div>
                
                <div id="content-therapists" class="content-container">
                    <generic-table data="admin_therapists" edit="true"></generic-table>
                    <!--php include 'a_therapist.php';? -->
                </div>

                <script>
                const tabs = document.querySelectorAll('.tab');
                const contents = document.querySelectorAll('.content-container');

                tabs.forEach(tab => {
                    tab.addEventListener('click', () => {
                   
                    tabs.forEach(t => t.classList.remove('active'));
                    contents.forEach(c => c.classList.remove('active'));

                   
                    tab.classList.add('active');
                    document.getElementById(tab.dataset.target).classList.add('active');
                    });
                });
                </script>

        </div>

         <!-- Services Section -->
        <div id="services-section" class="content">
            <h4>MANAGE SERVICES</h4>

            <button class="add-service" id="add-service"><i class="fas fa-plus"></i> Add Service</button>

                <!-- Hidden Pop-Up Form for Adding Services -->
                <div id="add-service-popup" class="popup-form">
                            <div class="popup-content-form popup-margin">
                                <span class="close-btn" id="close-add-popup">&times;</span>
                                <h2>Add Service</h2>
                                <form id="addservice-form">
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="serviceName">Service Name:</label>
                                            <input type="text" id="serviceName" name="serviceName" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="availability">Availability:</label>
                                            <select id="availability" name="availability" required>
                                                <option value="Available">Available</option>
                                                <option value="Not Available">Not Available</option>
                                            </select>
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
                                    <div class="btn-container">
                                        <button type="submit" class="submit-btn">Submit <i class="fas fa-arrow-right"></i></button>
                                    </div>

                                </form>
                            </div>
                        </div>
                        <div class="dashboard-table">
                            <generic-table data="admin_services" edit="true" delete="true"></generic-table>
                        </div>
                        
                            <!-- 'a_services.php' -->
                 

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
                            <div class="popup-content-form popup-margin">
                                <span class="close-btn" id="close-edit-service-popup">&times;</span>
                                <h2>Edit Service</h2>
                                <form id="editservice-form">
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="service-name">Service Name:</label>
                                            <input type="text" id="service-name" name="service-name" placeholder="Type service name you want to edit" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="availability">Availability:</label>
                                            <select id="availability" name="availability" required>
                                                <option value="Available">Available</option>
                                                <option value="Not Available">Not Available</option>
                                            </select>
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
                                    <div class="btn-container">
                                        <button type="submit" class="submit-btn">Submit <i class="fas fa-arrow-right"></i></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                                </div>
                                    <div class="service-about">
                                        <p id="service-about"></p>
                                    </div>
                                </div>
        </div>
            <!-- Edit Profile Section -->
            <div id="edit-profile-section" class="content">
                <h4>Edit Profile</h4>

                <!--Personal Information  -->
                <div id="edit-profile-section" class="edit-container">
                    <?php
                    include 'a_edit_profile.php'
                    ?>
                </div>

            </div>

              <!-- Feedbacks Section -->
        <div id="feedbacks-section" class="content">
            <h4>Feedbacks</h4>
            <div id="table-actions" class="table-actions">
               
            </div>
            <div class="dashboard-table">
                <generic-table data="admin_feedbacks"></generic-table>
            </div>
            
        </div>

        <div id="checklist-section" class="content">

            <!-- Pre-Screening Section -->
            <h4>PRE-SCREENING RESPONSE</h4>
            
            <div class="tabs-container">
                <div class="tab tab-pending active" data-target="content-pending">Pending</div>
                <div class="tab tab-complete" data-target="content-complete">Complete</div>
            </div>

            <div id="content-pending" class="content-container active">
                <generic-table data="therapist_pre-screening_pending"></generic-table>
            </div>
            <div id="content-complete" class="content-container">
                <generic-table data="therapist_pre-screening_complete"></generic-table>
            </div>
        </div>
        <script>
                const tabsPreScreen = document.querySelectorAll('.tab');
                const contentsPreScreen = document.querySelectorAll('.content-container');

                tabsPreScreen.forEach(tab => {
                    tab.addEventListener('click', () => {
  
                    tabsPreScreen.forEach(t => t.classList.remove('active'));
                    contentsPreScreen.forEach(c => c.classList.remove('active'));
     
                    tab.classList.add('active');
                    document.getElementById(tab.dataset.target).classList.add('active');
                    });
                });
        </script>

    </div>

    <script src="./generic-components/generic-message-popup.js" defer></script>
    <script src="adash_therapist_filter.js" defer></script>
    <script src="./generic-components/generic-widget.js" defer></script>
    <script src="adash.js" defer></script>
    <script src="admindashboard.js" defer></script>
    <script src="a_dashgenerate_pdf.js" defer></script>
    <script src="a_editservice.js" defer></script>
    <script src="a_add_delete_service.js" defer></script>
    <script src="a_confirmappointment.js" defer></script>
    <script src="a_editstaff_profile.js" defer></script>
    <script src="a_logout.js" defer></script>
    <script src="./generic-components/generic-calendar.js" defer></script>
    <script src="./generic-components/generic-table.js" defer></script>
    <script src="./generic-components/generic-side-view-bar.js" defer></script>

</body>
</html>
