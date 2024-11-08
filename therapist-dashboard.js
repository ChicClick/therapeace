// therapist-dashboard.js

document.addEventListener('DOMContentLoaded', () => {
    const links = document.querySelectorAll('.left-section nav a');
    const sections = document.querySelectorAll('.right-section .content');
    const menuItems = document.querySelectorAll('.left-section ul li');

    links.forEach(link => {
        link.addEventListener('click', (event) => {
            const targetId = link.getAttribute('data-target');

            // Check if the clicked link is the "Sign Out" link
            if (link.getAttribute('href') === 'loginlanding.html') {
                // Allow default behavior for the "Sign Out" link
                return; 
            }

            event.preventDefault();

            // Remove active class from all sections
            sections.forEach(section => {
                section.classList.remove('active');
            });

            // Hide all menu items
            menuItems.forEach(item => {
                item.classList.remove('active');
            });

            // Show the target section
            if (targetId) {
                document.getElementById(targetId).classList.add('active');
            }

            // Add active class to the clicked menu item
            link.parentElement.classList.add('active');
        });
    });
});

function filterSearch() {
    // Get the search input value
    const input = document.getElementById('searchInput').value.toLowerCase();
    
    // Get the table and its rows
    const table = document.getElementById('appointmentsTable');
    const rows = table.getElementsByTagName('tr');
    
    // Loop through table rows
    for (let i = 1; i < rows.length; i++) { // Start from 1 to skip the header row
        const cells = rows[i].getElementsByTagName('td');
        let found = false;
        
        // Loop through each cell in the row
        for (let j = 0; j < cells.length; j++) {
            if (cells[j].innerText.toLowerCase().includes(input)) {
                found = true;
                break;
            }
        }
        
        // Show or hide the row based on the search input
        rows[i].style.display = found ? '' : 'none';
    }
}

document.querySelector('.view-notes-btn').addEventListener('click', function() {
    // Redirect to the feedback notes section
    window.location.href = '#notes-section'; // Redirect to the notes section
});



document.addEventListener("DOMContentLoaded", function() {
    const patientTable = document.getElementById('patients-tbody');
    const patientInfo = document.getElementById('patient-info'); // Reference to the patient info section
    let currentPatientId = null; // Track the current patient ID

    // Add click event listener to the patient table
    patientTable.addEventListener('click', function(e) {
        const clickedRow = e.target.closest('tr'); // Check if a row was clicked
        if (clickedRow) {
            const patientId = clickedRow.getAttribute('data-patient-id');
            console.log('Clicked patient ID:', patientId); // Debugging line

            if (currentPatientId === patientId) {
                // If the same patient is clicked again, hide the info
                patientInfo.style.display = 'none';
                currentPatientId = null; // Reset the current patient ID
            } else {
                // If a different patient is clicked, fetch and update the info
                fetchPatientInfo(patientId);
                currentPatientId = patientId; // Update the current patient ID
            }
        }
    });

    // Function to fetch and display patient info
    function fetchPatientInfo(patientId) {
        // Clear the current patient info
        patientInfo.style.display = 'none'; // Hide the info while updating
        clearPatientInfo();

        // Fetch the clicked patient's detailed information
        fetch(`fetch_patient_info.php?id=${patientId}`)
            .then(response => response.json())
            .then(patientData => {
                console.log('Fetched patient data:', patientData); // Debugging line
                if (patientData.error) {
                    console.error('Patient not found:', patientData.error);
                } else {
                    // Populate the patient info section
                    document.getElementById('patientName').textContent = patientData.patient_name; // Ensure this matches your JSON keys
                    document.getElementById('service').textContent = patientData.service; // Ensure this key exists in your PHP
                    document.getElementById('parent-name').textContent = patientData.parent_name;
                    document.getElementById('phone').textContent = patientData.phone;
                    document.getElementById('email').textContent = patientData.email;
                    document.getElementById('address').textContent = patientData.address;
                    document.getElementById('birthday').textContent = patientData.birthday;
                    document.getElementById('gender').textContent = patientData.gender;

                    // Show the patient info section
                    patientInfo.style.display = 'block';
                }
            })
            .catch(err => console.error('Error fetching patient details:', err));
    }

    // Function to clear patient info
    function clearPatientInfo() {
        document.getElementById('patientName').textContent = '';
        document.getElementById('service').textContent = '';
        document.getElementById('parent-name').textContent = '';
        document.getElementById('phone').textContent = '';
        document.getElementById('email').textContent = '';
        document.getElementById('address').textContent = '';
        document.getElementById('birthday').textContent = '';
        document.getElementById('gender').textContent = '';
    }
});


function fetchFeedback(date, id) {
    const notesContainer = document.getElementById("notes-info");
    const notesDetails = document.getElementById("notes-details");
    const notesDate = document.getElementById("notes-date");

    // Format the date to match the format used in the table
    const formattedDate = new Date(date).toLocaleString('en-US', {
        year: 'numeric', month: 'long', day: 'numeric'
    });

    // Toggle visibility based on the current state and clicked row
    if (notesContainer.style.display === "block" && notesContainer.getAttribute("data-id") === id) {
        notesContainer.style.display = "none";
        notesDetails.innerHTML = "<h5>Session Overview:</h5>";
        notesContainer.removeAttribute("data-id");
    } else {
        // Show the notes container and populate with feedback
        notesContainer.style.display = "block";
        notesDate.innerText = formattedDate; // Set the formatted date
        notesContainer.setAttribute("data-id", id);

        // Fetch feedback from PHP script
        fetch(`fetch_feedback.php?date=${date}`)
            .then(response => response.json())
            .then(data => {
                notesDetails.innerHTML = "<h5>Session Overview:</h5>"; // Reset content
                if (data.success) {
                    notesDetails.innerHTML += `<p>${data.feedback}</p>`;
                } else {
                    notesDetails.innerHTML += `<p>No feedback available for this date.</p>`;
                }
            })
            .catch(error => {
                console.error("Error fetching feedback:", error);
                notesDetails.innerHTML += `<p>Error loading feedback.</p>`;
            });
    }
}



// therapist-dashboard.js
function displayGuestChecklist(guestID) {
    // Hide the guest table
    const prescreeningTable = document.getElementById('pre-screening-table');
    prescreeningTable.style.display = 'none'; 

    // Fetch guest data using guestID
    fetchGuestData(guestID)
        .then(guestData => {
            // Display guest information in the header
            document.getElementById('checklist-name').innerText = guestData.guest_name;
            document.getElementById('child-name').innerText = guestData.child_name || ""; 
            document.getElementById('child-age').innerText = guestData.child_age || ""; 

            // Show the checklist container
            document.querySelector('.checklist-container').style.display = 'block';

            // Now fetch the checklist questions and answers
            fetchChecklist(guestID);
        })
        .catch(error => console.error('Error fetching guest data:', error));
}

function fetchGuestData(guestID) {
    return fetch(`fetch_guest_data.php?guestID=${guestID}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json(); // Parse JSON response
        })
        .then(data => {
            return {
                guest_name: data.guest_name,
                child_name: data.child_name,
                child_age: data.child_age
            };
        });
}

function fetchChecklist(guestID) {
    const checklistSection = document.querySelector('.checklist-left-section');
    checklistSection.innerHTML = 'Loading checklist...';

    fetch(`fetch_checklist.php?guestID=${guestID}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(data => {
            checklistSection.innerHTML = data; // Populate checklist with fetched data
        })
        .catch(error => console.error('Error loading checklist:', error));
}

function displayGuestChecklistComplete(guestID) {
    // Hide the guest table
    const prescreeningTable = document.getElementById('pre-screening-table');
    prescreeningTable.style.display = 'none'; 

    // Fetch guest data using guestID
    fetchGuestData(guestID)
        .then(guestData => {
            // Display guest information in the header
            document.getElementById('checklist-name').innerText = guestData.guest_name;
            document.getElementById('child-name').innerText = guestData.child_name || ""; 
            document.getElementById('child-age').innerText = guestData.child_age || ""; 

            // Show the checklist container
            document.querySelector('.checklist-container').style.display = 'block';

            // Now fetch the checklist questions and answers
            fetchChecklistComplete(guestID);
        })
        .catch(error => console.error('Error fetching guest data:', error));
}


function fetchChecklistComplete(guestID) {
    const checklistSection = document.querySelector('.checklist-left-section');
    checklistSection.innerHTML = 'Loading checklist...';

    fetch(`fetch_checklist.php?guestID=${guestID}`)
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.text();
    })
    .then(data => {
        checklistSection.innerHTML = data; // Populate checklist with fetched data
    })
    .catch(error => console.error('Error loading checklist:', error));


    const checklistRightSection = document.querySelector('.checklist-right-section');
    document.querySelector('.asses').style.display = 'none';

    fetch(`view_checklist.php?guestID=${guestID}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(data => {
            checklistRightSection.innerHTML = data; // Populate checklist with fetched data
        })
        .catch(error => console.error('Error loading checklist:', error));
}



function fetchProgress(id) {
    const progressContainer = document.getElementById("progress-info");
    const notesTextarea = document.getElementById("notesTextarea");
    const saveButton = progressContainer.querySelector(".saveprogress-button"); // Corrected class selector

    if (progressContainer.style.display === "block" && progressContainer.getAttribute("data-id") === id) {
        progressContainer.style.display = "none";
        progressContainer.removeAttribute("data-id");
    } else {
        progressContainer.style.display = "block";
        progressContainer.setAttribute("data-id", id);

        fetch(`fetch_report.php?reportID=${id}`)
            .then(response => response.json()) // Parse JSON response
            .then(data => {
                console.log("Fetched data:", data); // Debugging: check the fetched data

                // Populate the textarea with the fetched summary
                notesTextarea.value = data.summary;

                // Check the status and disable the textarea if 'verified'
                if (data.status === "verified") {
                    notesTextarea.disabled = true;
                    saveButton.style.display = "none"; // Hide Save button
                } else {
                    notesTextarea.disabled = false;
                    saveButton.style.display = "inline-block"; // Show Save button if not "verified"
                }
            })
            .catch(error => console.error('Error fetching data:', error));
    }
}

function backLink() {
    // Hide the checklist container and show the table again
    document.querySelector('.checklist-container').style.display = 'none';
    document.getElementById('pre-screening-table').style.display = 'table'; // or 'block' if using a block layout
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
    window.location.href = 't_logout.php'; // Redirect to logout script
});

// Cancel logout
cancelLogout.addEventListener('click', () => {
    logoutModal.style.display = 'none'; // Hide the modal
});
