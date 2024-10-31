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
                <li><a href="#"><i class="fa fa-cog"></i> Settings</a></li>
                <li><a href="#"><i class="fa fa-users"></i> Accounts</a></li>
                <li><a href="#"><i class="fa fa-info-circle"></i> Help</a></li>
                <li><a href="loginlanding.html"><i class="fa fa-sign-out"></i> Sign Out</a></li>
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
                <p class="welcome-text">Welcome back, <?php echo htmlspecialchars($therapistName); ?>!</p>
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
                    <button id="confirmTimeButton">Reschedule  →</button>
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
                        <h2 id="patient-name">Name</h2>
                        <h3 id="service">Service</h3>
                    </div>
                    <button class="view-notes-btn">View Notes</button>
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
                <!-- Changed h5 to an anchor tag with id "notes-date" -->
                <a id="notes-date" style="cursor: pointer; color: #432705; text-decoration:none"></a>
                <div class="notes-container" id="notes-details" style="display:block">
                    <h5>Session Overview:</h5>
                </div>
            </div>
        </div>

        <div id="report-section" class="content">
            <h4>PROGRESS REPORT</h4>
            <div class="search-bar-content">
                <input type="text" placeholder="Search">
                <button><i class="fas fa-search"></i></button>
            </div>
            <table id="patients-table">
                <thead>
                    <tr>
                        <th>NAME</th>
                        <th>PARENT/GUARDIAN</th>
                        <th>COMPLIANCE</th>
                    </tr>
                </thead>
                <tbody>
                    <?php include 'patients.php'; ?>
                </tbody>
            </table>
            
            <div class="progress-info" id="progress-info" style="display:block;">
                <h4>PROGRESS REPORT</h4>
                <h5><a href="#" id="back-to-patients">< Back to the Patients</a></h5>
                <div class="notes-container">
                    <h5>Session Overview:</h5>
                    <p>In today's session, Emily showed great enthusiasm and made noticeable progress in her articulation exercises. She was particularly engaged during the interactive activities, which helped reinforce the sounds we have been working on.</p>

                    <h5>Key Progress:</h5>
                    <ul>
                        <li>Emily successfully produced the "s" and "r" sounds with increased clarity during structured activities.</li>
                        <li>We introduced a new game to practice blending sounds, which she enjoyed and participated in eagerly.</li>
                        <li>Emily set a small goal for herself to practice her speech sounds at home, particularly during reading time with her parents.</li>
                    </ul>

                    <h5>Areas for Focus:</h5>
                    <ul>
                        <li>It will be important to continue practicing the "s" and "r" sounds in more spontaneous speech contexts.</li>
                        <li>We will also start incorporating more complex sound combinations to challenge Emily and further enhance her speech clarity.</li>
                    </ul>
                    
                    <p>Overall, Emily made wonderful progress today, and I am excited to see her continued improvement in upcoming sessions.</p>
                </div>
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
                <a href="#" id="back-link">Back</a>
                <div class="checklist-header">
                    <div><span>Name:</span> <span id="checklist-name" style="color: #432705;"><?php echo $guestName; ?></span></div>
                    <div><span>Name of Child:</span> <span id="child-name" style="color: #432705;"><?php echo $childName; ?></span></div>
                    <div><span>Age of Child:</span> <span id="child-age" style="color: #432705;"><?php echo $childAge; ?></span></div>
                </div>

                <!-- Left Section -->
                <div class="checklist-left-section">
                    <!-- Populate questions dynamically -->
                    <?php 
                    
                    // Query to fetch questions and their options grouped by category
                    $sql = "SELECT questionID, category, questionText, options, inputType FROM prescreening_questions ORDER BY category";
                    $result = $conn->query($sql);
                    
                    $questions = [];
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            // Split options into an array
                            $optionsArray = explode(',', $row['options']); // Assuming options are comma-separated
                            $questions[$row['category']][] = [
                                'questionID' => $row['questionID'], // Capture question ID for checking answers
                                'questionText' => $row['questionText'],
                                'options' => $optionsArray,
                                'inputType' => $row['inputType'],
                            ];
                        }
                    }
                    
                    foreach ($questions as $category => $question_list): ?>
                        <div class="checkbox-group">
                            <div class="section-title"><?php echo htmlspecialchars($category); ?></div>
                            <?php foreach ($question_list as $item): ?>
                                <div class="question">
                                    <span style="font-weight: 400; margin-bottom:10px font: size 10px;"><?php echo htmlspecialchars($item['questionText']); ?></span>
                                    <?php
                                    // Determine the input type and render accordingly
                                    foreach ($item['options'] as $option): 
                                        $inputName = htmlspecialchars($item['questionText']); // Use question text as the name for unique identification
                                        $isChecked = (isset($guestAnswers[$item['questionID']]) && $guestAnswers[$item['questionID']] == $option) ? 'checked' : '';
                                    ?>
                                        <label>
                                            <?php if ($item['inputType'] === 'checkbox'): ?>
                                                <input type="checkbox" name="options[<?php echo $inputName; ?>][]" value="<?php echo htmlspecialchars($option); ?>" <?php echo $isChecked; ?>>
                                            <?php elseif ($item['inputType'] === 'radio'): ?>
                                                <input type="radio" name="options[<?php echo $inputName; ?>]" value="<?php echo htmlspecialchars($option); ?>" <?php echo $isChecked; ?>>
                                            <?php elseif ($item['inputType'] === 'number'): ?>
                                                <input type="number" name="options[<?php echo $inputName; ?>]" value="<?php echo htmlspecialchars($guestAnswers[$item['questionID']] ?? ''); ?>" placeholder="<?php echo htmlspecialchars($option); ?>">
                                            <?php elseif ($item['inputType'] === 'text'): ?>
                                                <input type="textarea" name="options[<?php echo $inputName; ?>]" value="<?php echo htmlspecialchars($guestAnswers[$item['questionID']] ?? ''); ?>" placeholder="<?php echo htmlspecialchars($option); ?>">
                                            <?php elseif ($item['inputType'] === 'textarea'): ?>
                                                <input type="text" name="options[<?php echo $inputName; ?>]" value="<?php echo htmlspecialchars($guestAnswers[$item['questionID']] ?? ''); ?>" placeholder="<?php echo htmlspecialchars($option); ?>">
                                             <?php elseif ($item['inputType'] === 'radio'): ?>
                                                <input type="radio" name="options[<?php echo $inputName; ?>]" value="<?php echo htmlspecialchars($option); ?>" <?php echo $isChecked; ?>>
                                                <?php endif; ?>
                                            <?php echo htmlspecialchars($option); ?>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>                                  

                <!-- Right Section -->
                <div class="checklist-right-section">
                    <!-- Static checkboxes for therapy options -->
                    <div class="checkbox-group">
                        <div class="section-title">Select Suitable Therapy</div>
                        <label><input type="checkbox"> Occupational Therapy</label>
                        <label><input type="checkbox"> Physical Therapy</label>
                        <label><input type="checkbox"> Speech Therapy</label>
                        <label><input type="checkbox"> Special Education</label>
                    </div>

                    <div class="comments-section">
                        <div class="section-title">Additional Diagnosis/Comments</div>
                        <textarea placeholder="Enter comments here..."></textarea>
                    </div>

                    <button class="save-button" onclick="saveForm()">Save</button>
                </div>
            </div>
        </div>
    </div>

    <script src="therapist-dashboard.js" defer></script>
</body>
</html>
