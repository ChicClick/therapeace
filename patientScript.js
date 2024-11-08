document.addEventListener('DOMContentLoaded', function() {
    
    // Get dropdown button and container
    const dropbtn = document.querySelector('.dropbtn');
    const dropdown = document.querySelector('.dropdown');

    // Toggle dropdown on button click
    dropbtn.addEventListener('click', () => dropdown.classList.toggle('show'));

    // Close dropdown if clicking outside
    window.onclick = function(event) {
        if (!event.target.matches('.dropbtn') && dropdown.classList.contains('show')) {
            dropdown.classList.remove('show');
        }
    };

    // Function to handle showing the correct section
function showSection(sectionId) {
    document.querySelectorAll('section').forEach(section => section.classList.add('hidden'));

    // If switching to appointments, ensure notes section is hidden
    if (sectionId === 'appointments') {
        notesSection.style.display = 'none'; // Hide the notes section
        notesTable.classList.remove('hidden'); // Show the notes table if it was hidden
    }

    document.getElementById(sectionId).classList.remove('hidden');
}

// Event listeners for navigation links
document.querySelector('a[href="#notes"]').addEventListener('click', function(event) {
    event.preventDefault();
    showSection('notes'); // Show notes section when Notes tab is clicked
});

document.querySelector('a[data-nav-link]').addEventListener('click', function(event) {
    event.preventDefault();
    showSection('appointments'); // Show appointments section
});


    const notesSection = document.getElementById('open-notes');
    const notesTable = document.getElementById('notes-table');

    // Function to format the date from 'YYYY-MM-DD' to 'Month DD, YYYY - Day'
    function formatDate(dateString) {
        const options = { year: 'numeric', month: 'long', day: 'numeric', weekday: 'long' };
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', options);
    }

    function showNotes(sessionInfo) {
        // Format the schedule date and set it in the header
        const formattedSchedule = formatDate(sessionInfo.schedule);
        
        // Update the header to include the formatted schedule
        document.getElementById('session-feedback-header').textContent = formattedSchedule;
        
        document.getElementById('note-therapist').textContent = sessionInfo.therapist;
        document.getElementById('notes-content').innerHTML = sessionInfo.notes;

        // Hide the entire notes-table section including the header
        document.getElementById('notes-table').classList.add('hidden');
        notesSection.style.display = 'block';
    }

    document.querySelectorAll('.notes-row').forEach(function(row) {
        row.addEventListener('click', function() {
            const sessionID = row.dataset.sessionId; // Get session ID from the clicked row
            const patientID = '<?php echo $_SESSION["patientID"]; ?>'; // Get patient ID from the session

            // Get the schedule date and therapist name from the clicked row
            const scheduleDate = row.getAttribute('data-schedule'); 
            const therapistName = row.getAttribute('data-therapist');

            // Fetch the notes for the selected session
            fetch('patient_get_notes.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    sessionID: sessionID,
                    patientID: patientID
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const sessionData = {
                        schedule: scheduleDate, // Use the scheduleDate obtained from the clicked row
                        therapist: data.therapist,
                        notes: data.notes.join('') // Join notes if they are in an array
                    };
                    showNotes(sessionData); // Call function to display notes
                } else {
                    alert('Failed to load notes: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error fetching notes:', error);
            });
        });
    });

    document.getElementById('back-to-appointments').addEventListener('click', function() {
        // Hide the notes section
        notesSection.style.display = 'none';
        
        // Show the notes table again
        notesTable.classList.remove('hidden');

        // Reset the header to its default value
        document.getElementById('session-feedback-header').textContent = 'SESSION FEEDBACK NOTES'; // Set to your default title
        document.getElementById('note-therapist').textContent = ''; // Clear therapist name, if needed
    });

    // Event listener for the Generate Report button
    document.getElementById('generateReportButton').addEventListener('click', generateReportButton);

    // Show the report request modal
    function generateReportButton() {
        document.getElementById('reportRequestModal').style.display = 'block';

        // Fetch the therapists for the logged-in patient
        fetch('patient_get_notes.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                'fetchTherapists': true // Indicate we want to fetch therapists
            })
        })
        .then(response => {
            // Log the raw response for debugging
            console.log('Response:', response);
            return response.json();
        })
        .then(data => {
            console.log('Data:', data);
            console.log('Therapists:', data.therapists);
            if (data.success) {
                const therapistSelect = document.getElementById('therapistSelect');
                therapistSelect.innerHTML = ''; // Clear existing options
                data.therapists.forEach(therapist => {
                    console.log('Therapist:', therapist); // Log the therapist object
                    const option = document.createElement('option');
                    option.value = therapist.therapistID; // Use therapist ID for the value
                    option.textContent = therapist.therapistName; // Display therapist name
                    therapistSelect.appendChild(option);
                });
        
                // If no therapists are found, you might want to inform the user
                if (data.therapists.length === 0) {
                    alert('No therapists found for your sessions.');
                }
            } else {
                alert(data.error); // Alert the error message
            }
        })
        
        .catch(error => console.error('Error fetching therapists:', error));
    }

    // Event listener for submitting the report request
    document.getElementById('submitReportRequest').addEventListener('click', function() {
        const therapistID = document.getElementById('therapistSelect').value;

        // Submit report request to server
        fetch('patient_submit_report.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                'therapistID': therapistID
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message); // Display success message
                closeReportRequestModal(); // Automatically close modal on success
            } else {
                alert(data.error); // Display error message
            }
        })
        .catch(error => console.error('Error submitting report request:', error));
    });
   
});

function closeReportRequestModal() {
    console.log('Close button clicked');
    document.getElementById('reportRequestModal').style.display = 'none';
}

        // Get logout modal element
        const logoutModal = document.getElementById('logoutModal');
        const logoutBtn = document.getElementById('logoutBtn');
        const closeModal = document.getElementById('closeModal');
        const confirmLogout = document.getElementById('confirmLogout');
        const cancelLogout = document.getElementById('cancelLogout');

        // Show the modal when logout button is clicked
        logoutBtn.addEventListener('click', (event) => {
            event.preventDefault(); // Prevent the default action
            logoutModal.style.display = 'block'; // Show the modal
        });

        // Close the modal when the user clicks on <span> (x)
        closeModal.addEventListener('click', () => {
            logoutModal.style.display = 'none';
        });

        // Close the modal when the user clicks outside of the modal
        window.addEventListener('click', (event) => {
            if (event.target === logoutModal) {
                logoutModal.style.display = 'none';
            }
        });

        // Confirm logout
        confirmLogout.addEventListener('click', () => {
            window.location.href = 'logout.php'; // Redirect to logout script
        });

        // Cancel logout
        cancelLogout.addEventListener('click', () => {
            logoutModal.style.display = 'none'; // Hide the modal
        });

    // Wait for the window to load fully
    window.addEventListener('load', () => {
        document.body.classList.add('loaded'); // Add the class to fade in
    });

    const scrollTopBtn = document.querySelector('.scroll-top');

    window.addEventListener('scroll', () => {
        if (window.scrollY > 300) {
            scrollTopBtn.style.display = 'block';
        } else {
            scrollTopBtn.style.display = 'none';
        }
    });

    scrollTopBtn.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
    

    const navbar = document.querySelector('nav'); // Get the navbar

    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) { // Change the value to adjust when the color change occurs
            navbar.classList.add('scrolled'); // Add class to navbar
        } else {
            navbar.classList.remove('scrolled'); // Remove class when not scrolled
        }

        if (window.scrollY > 300) {
            scrollTopBtn.style.display = 'block';
        } else {
            scrollTopBtn.style.display = 'none';
        }
    });

    function searchAppointments() {
        // Get the value from the search input, converting it to lowercase for case-insensitive comparison
        const input = document.getElementById("appointmentsSearch").value.toLowerCase();
        // Select all rows in the appointments table
        const rows = document.querySelectorAll(".appointment-row");
        
        // Loop through each appointment row
        rows.forEach(row => {
            // Get the therapist name from the data attribute, converting it to lowercase
            const therapistName = row.getAttribute("data-therapist").toLowerCase();
            
            // Check if the therapist name includes the input string
            if (therapistName.includes(input)) {
                row.style.display = ""; // Show the row if it matches
            } else {
                row.style.display = "none"; // Hide the row if it doesn't match
            }
        });
    }
    
    function searchNotes() {
        const input = document.getElementById("notesSearch").value.toLowerCase();
        const rows = document.querySelectorAll(".notes-row");
    
        rows.forEach(row => {
            const therapistName = row.getAttribute("data-therapist").toLowerCase();
            if (therapistName.includes(input)) {
                row.style.display = ""; // Show row
            } else {
                row.style.display = "none"; // Hide row
            }
        });
    }
    

    function openProgressReportPopup(reportID) {
        if (reportID !== null) {
            // Open the popup and load the data for the selected report
            document.getElementById('progress-report-popup').style.display = 'block';
            // You can now use reportID to fetch specific report details if necessary
        } else {
            alert('No report available for this patient.');
        }
    }
    
    function closePopup() {
        document.getElementById('progress-report-popup').style.display = 'none';
    }
    

    // JavaScript to toggle edit mode
    function toggleEditProfile() {
        const editSection = document.getElementById('edit-profile-section');
        const profileSection = document.getElementById('profile-section');
        const editButton = document.getElementById('edit-button');

        // Toggle the visibility of the sections
        editSection.style.display = (editSection.style.display === 'none' || editSection.style.display === '') ? 'block' : 'none';
        profileSection.style.display = (profileSection.style.display === 'none' || profileSection.style.display === '') ? 'none' : 'none';

        // Change the button text to Save Changes when in edit mode
        editButton.textContent = (editButton.textContent === 'Edit Profile') ? 'Save Changes' : 'Edit Profile';
    }