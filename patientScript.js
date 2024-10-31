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
        // Combine all feedback notes into one text block
        let text = notes.join(". ");
        
        // Split into sentences
        let sentences = text.match(/[^\.!\?]+[\.!\?]+/g) || [];
        
        // Define important keywords for scoring (customize as per therapy context)
        const keywords = ["improved", "progress", "challenges", "achieved", "struggling", "success", "goal"];
        const scores = sentences.map(sentence => {
            let score = 0;
            
            // Increase score based on keyword presence
            keywords.forEach(keyword => {
                if (sentence.toLowerCase().includes(keyword)) score += 3;
            });
            
            // Increase score based on sentence length (prefer meaningful length)
            score += sentence.split(" ").length > 8 ? 2 : 0;
    
            return { sentence, score };
        });
    
        // Sort sentences by score in descending order
        scores.sort((a, b) => b.score - a.score);
        
        // Select top sentences to form a coherent summary
        const topSentences = scores.slice(0, 3).map(item => item.sentence);
    
        return topSentences.join(" ");
    }

});
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
    
    let sortOrderDateAppointments = 'asc'; // Initial sort order for appointment dates
    let sortOrderTherapistAppointments = 'asc'; // Initial sort order for therapist names
    let sortOrderDateNotes = 'asc'; // Initial sort order for notes dates
    let sortOrderTherapistNotes = 'asc'; // Initial sort order for notes therapist names

    function sortAppointmentsByDate() {
        const table = document.querySelector(".appointment-table");
        const rows = Array.from(table.querySelectorAll(".appointment-row"));

        rows.sort((a, b) => {
            const dateA = new Date(a.querySelector(".row-schedule").innerText);
            const dateB = new Date(b.querySelector(".row-schedule").innerText);
            return sortOrderDateAppointments === 'asc' ? dateA - dateB : dateB - dateA;
        });

        sortOrderDateAppointments = sortOrderDateAppointments === 'asc' ? 'desc' : 'asc';
        table.innerHTML = ''; // Clear table
        rows.forEach(row => table.appendChild(row)); // Append sorted rows
    }

    function sortAppointmentsByTherapist() {
        const table = document.querySelector(".appointment-table");
        const rows = Array.from(table.querySelectorAll(".appointment-row"));

        rows.sort((a, b) => {
            const nameA = a.querySelector(".row-therapist").innerText.toLowerCase();
            const nameB = b.querySelector(".row-therapist").innerText.toLowerCase();
            return sortOrderTherapistAppointments === 'asc' ? nameA.localeCompare(nameB) : nameB.localeCompare(nameA);
        });

        sortOrderTherapistAppointments = sortOrderTherapistAppointments === 'asc' ? 'desc' : 'asc';
        table.innerHTML = ''; // Clear table
        rows.forEach(row => table.appendChild(row)); // Append sorted rows
    }

    function sortNotesByDate() {
        const table = document.querySelectorAll(".appointment-table")[1]; // Select the second table for notes
        const rows = Array.from(table.querySelectorAll(".notes-row"));

        rows.sort((a, b) => {
            const dateA = new Date(a.querySelector(".row-schedule").innerText);
            const dateB = new Date(b.querySelector(".row-schedule").innerText);
            return sortOrderDateNotes === 'asc' ? dateA - dateB : dateB - dateA;
        });

        sortOrderDateNotes = sortOrderDateNotes === 'asc' ? 'desc' : 'asc';
        table.innerHTML = ''; // Clear table
        rows.forEach(row => table.appendChild(row)); // Append sorted rows
    }

    function sortNotesByTherapist() {
        const table = document.querySelectorAll(".appointment-table")[1]; // Select the second table for notes
        const rows = Array.from(table.querySelectorAll(".notes-row"));

        rows.sort((a, b) => {
            const nameA = a.querySelector(".row-therapist").innerText.toLowerCase();
            const nameB = b.querySelector(".row-therapist").innerText.toLowerCase();
            return sortOrderTherapistNotes === 'asc' ? nameA.localeCompare(nameB) : nameB.localeCompare(nameA);
        });

        sortOrderTherapistNotes = sortOrderTherapistNotes === 'asc' ? 'desc' : 'asc';
        table.innerHTML = ''; // Clear table
        rows.forEach(row => table.appendChild(row)); // Append sorted rows
    }

    