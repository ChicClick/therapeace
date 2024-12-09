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

    <div class="wrapper">
        <!-- Notes Tab -->
        <section id="notes" class="active">
        <h1 id="session-feedback-header">SESSION FEEDBACK NOTES</h1>
        <hr>
        <!-- Notes Search and Sort Section -->
        <div id="notes-table"> <!-- Wrapper to hide the entire section -->
        <button id="generateReportButton" onclick="generateReportButton()">Request Progress Report</button>
        <button id="viewReportButton" onclick="openProgressReportPopup()">   
             View Progress Report
        </button>

        <div id="confirmationMessage" style="display:none;"></div>

            <!-- Modal for generating report -->
            <div id="reportRequestModal" class="modal">
                <div class="modal-content">
                    <span class="close-button" onclick="closeReportRequestModal()">&times;</span>
                    <h2>Request a Report</h2>
                    <label for="therapistSelect">Select Therapist:</label>
                    <select id="therapistSelect"></select>
                    <button id="submitReportRequest" onclick="submitReportRequest()">Submit Request</button>
                </div>
            </div>
            
            <div class="search-sort-container">
                <div class="search-wrapper">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" placeholder="Search..." id="notesSearch" class="search-bar" onkeyup="searchNotes()">
                </div>
            </div>

            <generic-table admin="false" data="patient_sessions"></generic-table>
        </div>
        </section>

        <!-- Progress Report Popup -->
        <div id="progress-report-popup" class="modal">
            <div class="progress-report-modal-content">
                <!-- Close Button -->
                <span class="close-btn" onclick="closePopup()">&times;</span>
                <h2>Progress Report</h2>
                <div class="progress-report-card-modal-container">
                    <!-- THIS IS WHERE THE CARD GOES PLEASE SEE patientScript.js line 61 -->
                </div>
                
            </div>
    </div>
</div>

