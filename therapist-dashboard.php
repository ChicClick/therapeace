<?php
session_start();

// Check if the therapist is logged in
if (!isset($_SESSION['therapist_id'])) {
    // If not logged in, redirect to login page
    header("Location: therapistLogin.php");
    exit;
}

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
    <link rel="icon" type="image/svg+xml" href="images/TheraPeace Logo.svg">
    <link rel="stylesheet" href="styles-therapist.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="therapist-dashboard-feedback-notes.css">
    <script src="https://cdn.jsdelivr.net/npm/tesseract.js@4.0.2/dist/tesseract.min.js"></script>
</head>
<body>
    <!-- Left Section -->
    <div class="left-section">
        <div class="logo">
            <img src="images/TheraPeace Logo.svg" alt="TheraPeace Logo">
            <h2>TheraPeace</h2>
        </div>
        <button class="hamburger-menu" id="hamburgerMenu">
            <i class="fas fa-bars"></i> <!-- Hamburger icon -->
        </button>
        <nav class="navbar">
            <ul>
                <p>MENU</p>
                <li class="active"><a href="#" data-target="appointments-section"><i class="fas fa-calendar-check"></i> Appointments</a></li>
                <li><a href="#" data-target="patients-profile-section"><i class="fas fa-user"></i> Patient Profile</a></li>
                <li><a href="#" data-target="notes-section"><i class="fas fa-file-alt"></i> Feedback Notes</a></li>
                <li><a href="#" data-target="report-section"><i class="fas fa-chart-bar"></i> Progress Report</a></li>
                <li><a href="#" data-target="checklist-section"><i class="fas fa-clipboard-list"></i> Pre-Screening Response</a></li>
    
                <p>OTHERS</p>
                <li><a href="#" data-target="Edit-section"><i class="fa fa-cog"></i> Edit Profile</a></li>
                <li><a href="#" id="logoutBtn"><i class="fa fa-sign-out"></i> Sign Out</a></li>
            </ul>
        </nav>
    </div>


    <?php 
        if (isset($_GET['message']) && !empty($_GET['message'])) {
            $message = htmlspecialchars($_GET['message']); // Sanitize the message
            echo "
            <script defer>
                document.addEventListener('DOMContentLoaded', () => {
                    new MessagePopUpEngine('TEST', \"" . addslashes($message) . "\");
                });
            </script>
            ";
        }
    ?>
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
            <h4>UPCOMING APPOINTMENTS</h4>
            <div class="search-bar-content">
                <input type="text" placeholder="Search" id="searchInput" onkeyup="filterSearch()">
                <button><i class="fas fa-search"></i></button>
            </div>
            <generic-table data="therapist_upcoming_appointments" reschedule="true"></generic-table>
            <!-- Reschedule Popup -->
            <generic-calendar></generic-calendar>
        </div>

        <div id="patients-profile-section" class="content">
            <h4>PATIENT'S PROFILE</h4>
            <div class="search-bar-content">
                <input type="text" placeholder="Search">
                <button><i class="fas fa-search"></i></button>
            </div>
            <div class="main-content-container">
                <generic-table data="therapist_patients" avatar="true"></generic-table>
            </div>

            
        </div>

        <!-- Feedback Notes Section -->
        <div id="notes-section" class="content">
            <h4>FEEDBACK NOTES</h4>
            <button id="add-notes" class="add-notes" onclick="openModal()"><i class="fas fa-plus"></i> Add Notes</button> 
            <div class="therapist-feedback container">

            <header class="therapist-feedback">
                <h4 class="therapist-feedback" id="breadcrumb">Patient Feedback Notes » All Patients</h4>
            </header>
  
            <div class="therapist-feedback content">
                <!--LIST OF NAMES-->
                <aside class="therapist-feedback sidebar">
                <div class="search-container">
                    <input type="text" placeholder="Search">
                    <button><i class="fas fa-search"></i></button>
                </div>
                    <div id="patient-feedback" class="therapist-feedback cards-list">
                        <!--CARD CONTAINER------!-->
                    </div>
                </aside>
                
                <!--MAIN CONTNT -->
                <section id="patient-dates" class="therapist-feedback main-content">
                    <ul class="therapist-feedback list-items">

                    </ul>
                </section>

                <div id="patient-feedback-view" class="therapist-feedback feedback-details">
                    <p>Click on a date to see their details.</p>
                </div>
            </div>
            </div>

        </div> <!--feedback notes closing div -->

        <div id="report-section" class="content">
            <h4>PROGRESS REPORT</h4>
            <div class="search-bar-content">
                <input type="text" placeholder="Search">
                <button><i class="fas fa-search"></i></button>
            </div>

            <!-- data before for future references 'progress.php'; ?> -->
            <generic-table data="therapist_progress" avatar="true"></generic-table>
            
        </div>


        <div id="checklist-section" class="content">
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
            <!-- data before pre-screening.php do not remove for REFERENCE -->  
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
    <generic-message-popup></generic-message-popup>
    <generic-side-view-bar></generic-side-view-bar>
    <script>
        const therapistID = '<?php echo $_SESSION['therapist_id']; ?>';
    </script>

    <script src="./generic-components/generic-message-popup.js" defer></script>
    <script src="./generic-components/generic-calendar.js" defer></script>
    <script src="./generic-components/generic-table.js" defer></script>
    <script src="./generic-components/generic-side-view-bar.js" defer></script>
    <script src="./generic-components/generic-accordion.js" defer></script>
    <script src="therapist-dashboard.js" defer></script>
    
</body>
</html>
