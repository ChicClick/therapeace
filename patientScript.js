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
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                showNotes(data); // Display the notes using the showNotes function
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

    document.getElementById('generateReportButton').addEventListener('click', function() {
        fetch('patientGenerateReport.php')
            .then(response => response.json())
            .then(data => {
                let summary = summarizeText(data.notes);  // Pass feedback notes to summarization function
                document.getElementById('reportContent').innerText = summary;
                document.getElementById('progressReport').style.display = 'block';
            })
            .catch(error => console.error('Error:', error));
    });
    
    // Simple summarization function
    function summarizeText(notes) {
        let text = notes.join(". ");
        let sentences = text.match(/[^\.!\?]+[\.!\?]+/g) || [];
        
        const keywords = ["improved", "progress", "challenges", "achieved", "struggling", "success", "goal"];
        const scores = sentences.map(sentence => {
            let score = 0;
            keywords.forEach(keyword => {
                if (sentence.toLowerCase().includes(keyword)) score += 3;
            });
            score += sentence.split(" ").length > 8 ? 2 : 0;
            return { sentence, score };
        });

        scores.sort((a, b) => b.score - a.score);
        const topSentences = scores.slice(0, 3).map(item => item.sentence);
        return topSentences.join(" ");
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
    });

    function searchAppointments() {
        const input = document.getElementById("appointmentsSearch").value.toLowerCase();
        const rows = document.querySelectorAll(".appointment-row");
        
        rows.forEach(row => {
            const therapistName = row.getAttribute("data-therapist").toLowerCase();
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

    let sortOrderDateAppointments = 'asc'; // Initial sort order for appointment dates
    let sortOrderTherapistAppointments = 'asc'; // Initial sort order for therapist names

    // Sort appointments by date
    document.getElementById('sortByDate').addEventListener('click', function() {
        const appointmentsTable = document.querySelector('#appointments-table tbody');
        const rows = Array.from(appointmentsTable.querySelectorAll('tr'));
        
        rows.sort((a, b) => {
            const dateA = new Date(a.querySelector('.appointment-date').textContent);
            const dateB = new Date(b.querySelector('.appointment-date').textContent);
            return sortOrderDateAppointments === 'asc' ? dateA - dateB : dateB - dateA;
        });
        
        sortOrderDateAppointments = sortOrderDateAppointments === 'asc' ? 'desc' : 'asc'; // Toggle sort order
        rows.forEach(row => appointmentsTable.appendChild(row)); // Append sorted rows
    });

    // Sort appointments by therapist name
    document.getElementById('sortByTherapist').addEventListener('click', function() {
        const appointmentsTable = document.querySelector('#appointments-table tbody');
        const rows = Array.from(appointmentsTable.querySelectorAll('tr'));
        
        rows.sort((a, b) => {
            const nameA = a.querySelector('.therapist-name').textContent.toLowerCase();
            const nameB = b.querySelector('.therapist-name').textContent.toLowerCase();
            return sortOrderTherapistAppointments === 'asc' ? nameA.localeCompare(nameB) : nameB.localeCompare(nameA);
        });
        
        sortOrderTherapistAppointments = sortOrderTherapistAppointments === 'asc' ? 'desc' : 'asc'; // Toggle sort order
        rows.forEach(row => appointmentsTable.appendChild(row)); // Append sorted rows
    });

});
