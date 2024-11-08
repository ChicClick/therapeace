<?php
session_start();

// Check if the therapist is logged in
if (!isset($_SESSION['therapist_id'])) {
    // If not logged in, redirect to login page
    header("Location: therapistLogin.php");
    exit;
}

// Retrieve the therapist's name from the session
$therapistName = $_SESSION['therapist_name'];
$therapistID = $_SESSION['therapist_id'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Therapist Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles-therapist.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/tesseract.js@4.0.2/dist/tesseract.min.js"></script>
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
                <li class="active"><a href="#" data-target="appointments-section"><i class="fas fa-calendar-check"></i> Appointments</a></li>
                <li><a href="#" data-target="patients-profile-section"><i class="fas fa-user"></i> Patient Profile</a></li>
                <li><a href="#" data-target="notes-section"><i class="fas fa-file-alt"></i> Feedback Notes</a></li>
                <li><a href="#" data-target="report-section"><i class="fas fa-chart-bar"></i> Progress Report</a></li>
                <li><a href="#" data-target="checklist-section"><i class="fas fa-clipboard-list"></i> Pre-Screening Response</a></li>

                <p>OTHERS</p>
                    <!---
                        <li><a href="#"><i class="fa fa-cog"></i> Settings</a></li>
                        <li><a href="#"><i class="fa fa-users"></i> Accounts</a></li>
                        <li><a href="#"><i class="fa fa-info-circle"></i> Help</a></li>
                    -->
                <li><a href="#" data-target="Edit-section"><i class="fa fa-cog"></i> Edit Profile</a></li>
                <li><a href="#" id="logoutBtn"><i class="fa fa-sign-out"></i> Sign Out</a></li>
            </ul>
        </nav>
    </div>
     <!-- Logout Confirmation Modal -->
     <div id="logoutModal" class="modal-logout" style="display:none">
        <div class="modal-content-logout">
            <span class="close-logout" id="closeModal">&times;</span>
            <h2>Confirm Logout</h2>
            <p>Are you sure you want to log out?</p>
            <button id="confirmLogout">Yes, log me out</button>
            <button id="cancelLogout">Cancel</button>
        </div>
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
                <p class="welcome-text">
                    Welcome back, <?php echo htmlspecialchars($_SESSION['therapist_name'] ?? 'Therapist'); ?>!
                </p>
            </div>
        </div>

        <!-- Appointments Section -->
        <div id="appointments-section" class="content active">
            <h4>APPOINTMENTS</h4>
            <div class="search-bar-content">
                <input type="text" placeholder="Search" id="searchInput" onkeyup="filterSearch()">
                <button><i class="fas fa-search"></i></button>
            </div>
            <table id="appointmentsTable">
                <thead>
                    <tr>
                        <th>APPOINTMENT ID</th>
                        <th>NAME</th>
                        <th>PARENT/GUARDIAN</th>
                        <th>SERVICES</th>
                        <th>SCHEDULE</th>
                    </tr>
                </thead>
                <tbody>
                    <?php include 'appointments.php'; ?>
                </tbody>
            </table>
            <!-- Reschedule Popup -->
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
                    <button id="confirmTimeButton" >Reschedule  →</button>
                </div>
            </div>
            <!-- Message Popup -->
            <div id="messagePopup" class="popup" style="display: none;">
                <div class="popup-content">
                    <span class="close" id="closePopup"></span>
                    <p id="popupMessage"></p>
                    <button id="confirmPopup">Confirm</button>
                </div>
            </div>
        </div>

        <div id="patients-profile-section" class="content">
            <h4>PATIENT'S PROFILE</h4>
            <div class="search-bar-content">
                <input type="text" placeholder="Search">
                <button><i class="fas fa-search"></i></button>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>NAME</th>
                        <th>PARENT/GUARDIAN</th>
                        <th>COMPLETION</th>
                    </tr>
                </thead>
                <tbody id="patients-tbody">
                    <?php include 'patients.php'; ?>
                </tbody>
            </table>

            <div class="patient-info" id="patient-info" style="display:none;">
                <h4>PROFILES</h4>
                <div class="profile-header">
                    <img src="images/about 1.jpg" alt="Profile Picture" class="profile-picture">
                    <div class="profile-details">
                        <h2 id="patientName">Name</h2>
                        <h3 id="service">Service</h3>
                    </div>
                    <button class="view-notes-btn" style="displa:none"></button>
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

        <!-- Feedback Notes Section -->
        <div id="notes-section" class="content">
            <h4>FEEDBACK NOTES</h4>
            <div class="search-bar-content">
                <input type="text" placeholder="Search">
                <button><i class="fas fa-search"></i></button>
            </div>

            <!-- Add Notes Button -->
            <button id="add-notes" class="add-notes" onclick="openModal()"><i class="fas fa-plus"></i> Add Notes
                <script>
                    function openModal() {
                        document.getElementById('notesModal').style.display = 'block';
                    }
                </script>
            </button>    

            <table>
                <thead>
                    <tr>
                        <th>NAME</th>
                        <th>DATE</th>
                    </tr>
                </thead>
                <tbody>
                    <?php include 'notes.php'; ?>
                    <!-- The PHP code will generate rows dynamically here -->
                </tbody>
            </table>

            <div class="notes-info" id="notes-info" style="display:none">
                <h4>NOTES</h4>
                <a id="notes-date" style="cursor: pointer; color: #432705; text-decoration:none"></a>
                <div class="notes-container" id="notes-details" style="display:block">
                    <h5>Session Overview:</h5>
                </div>
            </div>

            
            <div id="notesModal" class="popup" style="display:none">
                <span class="closeNotes" onclick="closeNotes()">&times;</span>
                <script>
                    function closeNotes() {
                        document.getElementById('notesModal').style.display = 'none';
                    }
                </script>
                
                <h3>Add Session Notes</h3>
                <form id="notesForm" class="notesForm" action="add_notes.php" method="post">
                    <div class="form-row">
                    <div class="form-column-left">
                        <label for="patientSelect">Select Patient:</label>
                        <select id="patientSelect" name="patientID" required onclick="loadPatients()" onchange="loadServices()">
                            <option value="">Select a patient...</option> <!-- Default placeholder option -->
                        </select>

                        <label for="therapySelect">Select Service:</label>
                        <select id="therapySelect" name="serviceID" required>
                        </select>

                        <script>
                            function loadPatients() {
                                const patientSelect = document.getElementById("patientSelect");
                                
                                // Only fetch options if they are not already loaded
                                if (patientSelect.options.length > 1) return;

                                // Create a new AJAX request
                                fetch("notes_patient.php")
                                    .then(response => response.text())
                                    .then(data => {
                                        patientSelect.innerHTML += data; // Append fetched options to the select element
                                    })
                                    .catch(error => console.error('Error loading patient options:', error));

                            }

                            function loadServices() {
                                const therapySelect = document.getElementById("therapySelect");
                                const patientID = document.getElementById("patientSelect").value;

                                // Only fetch options if a patient is selected
                                if (!patientID) {
                                    therapySelect.innerHTML = ""; // Clear previous options
                                    return;
                                }

                                fetch(`notes_service.php?patientID=${patientID}`)
                                    .then(response => response.text())
                                    .then(data => {
                                        therapySelect.innerHTML = data; // Directly set fetched options to the select element
                                    })
                                    .catch(error => console.error('Error loading service options:', error));
                            } 
                        </script>
                    </div>
                        <div class="form-column-right">
                            <label for="sessionDate">Session Date:</label>
                            <input type="date" id="sessionDate" name="sessionDate" required>
                        
                            <label for="sessionTime">Select Session Time:</label>
                            <select id="sessionTime" name="sessionTime">
                                <option value="">Select Time...</option>
                                <option value="9:00 AM">9:00 AM</option>
                                <option value="10:00 AM">10:00 AM</option>
                                <option value="11:00 AM">11:00 AM</option>
                                <option value="12:00 PM">12:00 PM</option>
                                <option value="1:00 PM">1:00 PM</option>
                                <option value="2:00 PM">2:00 PM</option>
                                <option value="3:00 PM">3:00 PM</option>
                                <option value="4:00 PM">4:00 PM</option>
                                <option value="5:00 PM">5:00 PM</option>
                                <option value="6:00 PM">6:00 PM</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="feedback">Feedback:
                            <a href="#" onclick="document.getElementById('feedbackImage').click();" style="text-decoration: underline; color: #432705; padding:5px; font-size:12px; border-radius:5px;">
                                <i class="fas fa-upload" style="margin-right: 5px;"></i> Attach Image
                            </a>
                        </label>
                        <div style="position: relative;">
                            <textarea id="feedback" name="feedback" required></textarea>
                            <div id="loadingIcon" class="loading-icon" style="display: none;"></div>
                        </div>
                        <input type="file" id="feedbackImage" accept="image/*" style="display: none;" onchange="extractTextFromImage()" />

                        <style>
                            .loading-icon {
                                position: absolute;
                                top: 40%;
                                left: 45%;
                                transform: translate(-50%, -50%);
                                border: 3px solid rgba(0, 0, 0, 0.2); /* Thinner border for smaller size */
                                border-top: 3px solid #432705; /* Thinner top border for color */
                                border-radius: 50%;
                                width: 20px; /* Smaller width */
                                height: 20px; /* Smaller height */
                                animation: spin 1s linear infinite;
                            }

                            @keyframes spin {
                                0% { transform: rotate(0deg); }
                                100% { transform: rotate(360deg); }
                            }
                        </style>

                        <script>
                            async function extractTextFromImage() {
                                const fileInput = document.getElementById('feedbackImage');
                                if (fileInput.files.length === 0) return;

                                const file = fileInput.files[0];
                                const reader = new FileReader();
                                const loadingIcon = document.getElementById('loadingIcon');
                                const feedbackTextarea = document.getElementById('feedback');

                                // Show loading icon
                                loadingIcon.style.display = 'block';

                                reader.onload = async function(event) {
                                    const imageData = event.target.result;

                                    try {
                                        // Use Tesseract.js to recognize text from the image
                                        const result = await Tesseract.recognize(
                                            imageData,
                                            'eng', // Specify the language code
                                            {
                                                logger: (m) => console.log(m) // Optional: log progress
                                            }
                                        );

                                        const extractedText = result.data.text.trim(); // Get and trim extracted text

                                        if (extractedText) {
                                            // Insert the recognized text into the feedback textarea if text is found
                                            feedbackTextarea.value = extractedText;
                                        } else {
                                            // Display an error message if no text was found
                                            alert("No text was found in the image. Please try a different image.");
                                        }
                                    } catch (error) {
                                        console.error('Error extracting text:', error);
                                        alert('An error occurred while processing the image');
                                    } finally {
                                        // Hide loading icon
                                        loadingIcon.style.display = 'none';
                                    }
                                };

                                // Convert the image file to a data URL so Tesseract.js can process it
                                reader.readAsDataURL(file);
                            }
                        </script>
                    </div>
                    <button type="submit">Submit</button>
                </form>

                <div id="confirmationNotesPopup" class="popup" style="display:none;">
                    <div class="modal-content">
                        <span id="closePopup" class="close" onclick="closeConfirmationNotesPopup()">&times;</span>
                        <p id="confirmationText"></p>
                        <button onclick="closeAllModals()">OK</button>
                    </div>
                </div>
                <script>
                    // Function to show confirmation popup with a message
                    function showConfirmationPopup(message) {
                        document.getElementById('confirmationText').innerText = message;
                        document.getElementById('confirmationNotesPopup').style.display = 'block';
                        document.getElementById('notesModal').style.display = 'block';
                    }

                    // Function to close the confirmation popup
                    function closeConfirmationNotesPopup() {
                        document.getElementById('confirmationNotesPopup').style.display = 'none';
                    }

                    // Function to close the popup and redirect to the dashboard
                    function closeAllModals() {
                        closeConfirmationNotesPopup();
                        window.location.href = 'therapist-dashboard.php'; // Redirect to therapist dashboard
                    }
                </script>
            </div>
        </div>

        <div id="report-section" class="content">
            <h4>PROGRESS REPORT</h4>
            <div class="search-bar-content">
                <input type="text" placeholder="Search">
                <button><i class="fas fa-search"></i></button>
            </div>
            <table id="progress-table">
                <thead>
                    <tr>
                        <th>NAME</th>
                        <th>THERAPIST</th>
                        <th>STATUS</th>
                    </tr>
                </thead>
                <tbody id="progress-tbody">
                    <?php include 'progress.php'; ?>
                </tbody>
            </table>
            
            <div class="progress-info" id="progress-info" style="display:none;">
                <h4>PROGRESS REPORT</h4>
                <div class="progress-container">
                <label for="notesTextarea" style="display: block; margin-bottom: 5px;">Save or Edit Report:</label>
                    <textarea id="notesTextarea" rows="4" style="font-family:'Poppins'; width: 95%; padding: 10px; border-radius: 5px; border: 1px solid #ccc;"></textarea>
                    <button class="saveprogress-button" onclick="triggerSaveProgress()" style="margin-top: 10px; background-color: #907d66; color: white; padding: 8px 16px; border: none; border-radius: 5px; cursor: pointer;">
                        Save
                    </button>
                </div>
                <script>
                    function triggerSaveProgress() {
                        const reportID = <?php echo json_encode($reportID); ?>; // Assuming reportID is available
                        const summary = document.getElementById('notesTextarea').value;

                        // AJAX request to update the report
                        const xhr = new XMLHttpRequest();
                        xhr.open("POST", "updateReport.php", true);
                        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                        
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState === 4 && xhr.status === 200) {
                                alert("Progress report saved successfully.");
                                // Optionally, hide the progress-info div after saving
                                document.getElementById('progress-info').style.display = 'none';
                            }
                        };
                        
                        xhr.send("reportID=" + reportID + "&summary=" + encodeURIComponent(summary));
                    }
                </script>
            </div>
        </div>


        <div id="checklist-section" class="content">
            <h4>PRE-SCREENING RESPONSE</h4>
            <table id="pre-screening-table">
                <thead>
                    <tr>
                        <th>NAME</th>
                        <th>EMAIL</th>
                        <th>SUBMITTED</th>
                        <th>STATUS</th>
                        <th>ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    <?php include 'pre-screening.php'; ?>
                </tbody>
            </table>
            <div class="checklist-container" style="display: none;"> 
                <div class="checklist-header">
                    <a href="javascript:void(0);" id="back-checklist-link" onclick="backLink()">&larr; Back</a>
                    <div><span>Name:</span> <span id="checklist-name" style="color: #432705;"></span></div>
                    <div><span>Name of Child:</span> <span id="child-name" style="color: #432705;"></span></div>
                    <div><span>Age of Child:</span> <span id="child-age" style="color: #432705;"></span></div>
                </div>
                <!-- Left Section -->
                <div class="checklist-left-section">
                    <!-- Populate questions dynamically -->
                </div>               

                <!-- Right Section -->
                <div class="checklist-right-section">
                    <!-- Static checkboxes for therapy options -->
                    <form class="asses" action="save_form.php" method="post">
                        <!-- Hidden input to hold the guestId dynamically -->
                        <input type="hidden" name="guestId" id="guestId" value="">

                        <div class="checkbox-group">
                            <div class="section-title">Select Suitable Therapy</div>
                            <label><input type="checkbox" name="therapies[]" id="therapy-occupational" value="Occupational Therapy"> Occupational Therapy</label>
                            <label><input type="checkbox" name="therapies[]" id="therapy-physical" value="Physical Therapy"> Physical Therapy</label>
                            <label><input type="checkbox" name="therapies[]" id="therapy-speech" value="Speech Therapy"> Speech Therapy</label>
                            <label><input type="checkbox" name="therapies[]" id="therapy-special" value="Special Education"> Special Education</label>
                        </div>

                        <div class="comments-section">
                            <div class="section-title">Additional Diagnosis/Comments</div>
                            <textarea name="comments" id="comments" placeholder="Enter comments here..."></textarea>
                        </div>

                        <button type="submit" class="save-button">Save</button>
                    </form>

                    <script>
                        // JavaScript function to set guestId in the hidden input
                        function setGuestId(guestId) {
                            document.getElementById('guestId').value = guestId;
                        }
                    </script>
                </div>
            </div>
        </div>
        
        <div id="Edit-section" class="content">
            <h4>EDIT PROFILE</h4>
            
            <!-- Personal Information -->
            <div id="edit-profile-section" class="edit-container">
                <?php
                    include 'edit_profile.php'
                ?>
            </div>
            
        </div>
    </div>
    <script>
        const therapistID = '<?php echo $_SESSION['therapist_id']; ?>';
    </script>

    <script src="therapist-dashboard.js" defer></script>
    <script src="calendar.js"></script>
</body>
</html>
