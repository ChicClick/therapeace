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
    // Hide the notes info and notes container by default
    const notesInfo = document.getElementById("notes-info");
    const notesDetails = document.getElementById("notes-details");

    // Add click event listener to each table row
    const notesRows = document.querySelectorAll(".notes-row");
    notesRows.forEach(row => {
        row.addEventListener("click", function() {
            const dateCell = this.cells[1].textContent; // Get the date from the clicked row
            const notesDate = document.getElementById("notes-date");
            notesDate.textContent = "> " + dateCell; // Update the date in notes
            
            // Populate notes details based on the row clicked
            // For demonstration, static content. You can customize this based on your needs.
            document.getElementById("session-overview").innerText = "In today's session, details about the session will go here...";
            document.getElementById("key-progress").innerHTML = "<li>Key progress detail 1...</li><li>Key progress detail 2...</li>";
            document.getElementById("areas-for-focus").innerHTML = "<li>Focus area 1...</li><li>Focus area 2...</li>";

            // Show the notes info and toggle the notes details
            notesInfo.style.display = "block"; // Show notes info
            notesDetails.style.display = "block"; // Show notes details
        });
    });
});

document.addEventListener("DOMContentLoaded", function() {
    const patientTable = document.getElementById('patients-tbody');
    const patientInfo = document.getElementById('patient-info'); // Reference to the patient info section
    let currentPatientId = null; // Track the current patient ID

    // Add click event listener to each patient row
    patientTable.addEventListener('click', function(e) {
        if (e.target.closest('tr')) { // Check if a row was clicked
            const clickedRow = e.target.closest('tr');
            const patientId = clickedRow.getAttribute('data-patient-id');
            console.log('Clicked patient ID:', patientId); // Debugging line

            if (currentPatientId === patientId) {
                // If the same patient is clicked again, hide the info
                patientInfo.style.display = 'none';
                currentPatientId = null; // Reset the current patient ID
            } else {
                // If a different patient is clicked, fetch and update the info
                fetchPatientInfo(patientId);
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
                document.getElementById('patient-name').textContent = patientData.name;
                document.getElementById('service').textContent = patientData.service;
                document.getElementById('parent-name').textContent = patientData.parent_name;
                document.getElementById('phone').textContent = patientData.phone;
                document.getElementById('email').textContent = patientData.email;
                document.getElementById('address').textContent = patientData.address;
                document.getElementById('birthday').textContent = patientData.birthday;
                document.getElementById('gender').textContent = patientData.gender;

                // Show the patient info section
                patientInfo.style.display = 'block';
                currentPatientId = patientId; // Update the current patient ID
            }
        })
        .catch(err => console.error('Error fetching patient details:', err));
    }

    // Function to clear patient info
    function clearPatientInfo() {
        document.getElementById('patient-name').textContent = '';
        document.getElementById('service').textContent = '';
        document.getElementById('parent-name').textContent = '';
        document.getElementById('phone').textContent = '';
        document.getElementById('email').textContent = '';
        document.getElementById('address').textContent = '';
        document.getElementById('birthday').textContent = '';
        document.getElementById('gender').textContent = '';
    }    
});

// Global variables to track the currently selected month and year
let selectedMonth = new Date().getMonth(); // Start from the current month (0-11)
let selectedYear = new Date().getFullYear(); // Start from the current year

// Function to open popup when "Reschedule" is clicked
function openPopup() {
    document.getElementById('reschedulePopup').style.display = 'block';
    generateCalendar(); // Call to generate calendar when popup is opened
}

// Function to close popup
function closePopup() {
    document.getElementById('reschedulePopup').style.display = 'none';
}

// Function to get the month name for display
function getMonthName(month) {
    const monthNames = [
        "January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];
    return monthNames[month];
}

// Function to generate calendar dynamically
function generateCalendar() {
    const calendarGrid = document.querySelector('.calendar-grid');
    calendarGrid.innerHTML = ''; // Clear any previously generated calendar

    // Set the month display
    document.getElementById('currentMonth').innerText = `${getMonthName(selectedMonth)} ${selectedYear}`;

    const daysInMonth = new Date(selectedYear, selectedMonth + 1, 0).getDate(); // Get total days in the current month

    for (let day = 1; day <= daysInMonth; day++) {
        const dateString = `${selectedYear}-${String(selectedMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const dayDiv = document.createElement('div');
        dayDiv.classList.add('day');
        dayDiv.innerText = day;

        // Create a date object for the generated date
        const generatedDate = new Date(dateString);
        const currentDate = new Date(); // Update current date each time

        // If date is in the past or already booked, disable it
        if (generatedDate < currentDate || bookedDates.includes(dateString)) {
            dayDiv.classList.add('disabled');
        } else {
            dayDiv.addEventListener('click', () => {
                // Handle date selection
                document.querySelectorAll('.day').forEach(el => el.classList.remove('selected'));
                dayDiv.classList.add('selected');
                document.getElementById('selectedDate').value = dateString;
            });
        }
        calendarGrid.appendChild(dayDiv);
    }
}

// Event listener for month navigation
document.getElementById('nextMonth').addEventListener('click', (e) => {
    e.preventDefault(); // Prevent the default anchor click behavior
    selectedMonth++;
    if (selectedMonth > 11) {
        selectedMonth = 0; // Reset to January if it exceeds December
        selectedYear++; // Increment year
    }
    generateCalendar();
});

document.getElementById('prevMonth').addEventListener('click', (e) => {
    e.preventDefault(); // Prevent the default anchor click behavior
    selectedMonth--;
    if (selectedMonth < 0) {
        selectedMonth = 11; // Reset to December if it goes below January
        selectedYear--; // Decrement year
    }
    generateCalendar();
});

// Event listener for reschedule link
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.reschedule-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            openPopup();
        });
    });
});

// Function to open the time selection popup
function openTimePopup() {
    document.getElementById('timePopup').style.display = 'block';
    generateAvailableTimes(); // Call to populate available times
}

// Function to close the time selection popup
function closeTimePopup() {
    document.getElementById('timePopup').style.display = 'none';
}

// Function to generate available times for morning and afternoon sessions
function generateAvailableTimes() {
    const morningTimes = ['9:00 AM', '10:00 AM', '11:00 AM','12:00 AM'];
    const afternoonTimes = ['1:00 PM', '2:00 PM', '3:00 PM', '4:00 PM', '5:00 PM', '6:00 PM'];

    const morningList = document.getElementById('morningTimes');
    const afternoonList = document.getElementById('afternoonTimes');

    morningList.innerHTML = ''; // Clear previous times
    afternoonList.innerHTML = '';

    morningTimes.forEach(time => {
        const li = document.createElement('li');
        li.innerText = time;
        li.addEventListener('click', () => selectTime(time));
        morningList.appendChild(li);
    });

    afternoonTimes.forEach(time => {
        const li = document.createElement('li');
        li.innerText = time;
        li.addEventListener('click', () => selectTime(time));
        afternoonList.appendChild(li);
    });
}

// Function to handle time selection
function selectTime(selectedTime) {
    console.log('Selected time:', selectedTime);
    // You can store the selected time or do something with it here
}

// Event listener for the Proceed button in the reschedule popup
document.getElementById('proceedButton').addEventListener('click', function() {
    const selectedDate = document.getElementById('selectedDate').value;
    if (selectedDate) { // Ensure a date is selected
        closePopup(); // Close the reschedule popup
        openTimePopup(); // Open the time selection popup
    } else {
        alert('Please select a date first.'); // Alert if no date is selected
    }
});

// Function to close the main popup
function closePopup() {
    document.getElementById('reschedulePopup').style.display = 'none';
}

// Additional functions for generating the calendar can remain as they are
document.getElementById('backButton').addEventListener('click', function(event) {
    event.preventDefault(); // Prevent default anchor link behavior

    // Hide the time popup
    document.getElementById('timePopup').style.display = 'none';
    
    // Show the date selection popup (assuming you have an element for it)
    document.getElementById('reschedulePopup').style.display = 'block'; // Change this to your date popup's ID
});

function closeTimePopup() {
    document.getElementById('timePopup').style.display = 'none';
}
document.getElementById('backButton').addEventListener('click', function(event) {
    event.preventDefault(); // Prevent default anchor link behavior

    // Hide the time popup
    document.getElementById('timePopup').style.display = 'none';
    
    // Show the date selection popup (assuming you have an element for it)
    document.getElementById('reschedulePopup').style.display = 'block'; // Change this to your date popup's ID
});


function fetchFeedback(date, id) {
    const notesContainer = document.getElementById("notes-info");
    const notesDetails = document.getElementById("notes-details");
    const notesDate = document.getElementById("notes-date");

    // Toggle visibility based on the current state and clicked row
    if (notesContainer.style.display === "block" && notesContainer.getAttribute("data-id") === id) {
        notesContainer.style.display = "none";
        notesDetails.innerHTML = "<h5>Session Overview:</h5>";
        notesContainer.removeAttribute("data-id");
    } else {
        // Show the notes container and populate with feedback
        notesContainer.style.display = "block";
        notesDate.innerText = date;
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

document.addEventListener('DOMContentLoaded', function() {
    const notesContainer = document.getElementById('notes-container');
    const notesDateLink = document.getElementById('notes-date');

    // Hide notes container when the link is clicked
    notesDateLink.addEventListener('click', function(event) {
        event.preventDefault(); // Prevent default link behavior
        notesContainer.style.display = 'none'; // Hide the notes container
    });
});


function showChecklist(guestData) {
    // Hide the table
    document.getElementById('pre-screening-table').style.display = 'none';

    // Show the checklist section
    document.getElementById('checklist-section').style.display = 'block';
    
    // Populate the checklist with guest data
    document.getElementById('checklist-name').innerText = guestData.guest_name;
    document.getElementById('child-name').innerText = guestData.child_name || ""; // Use child data from guestData
    document.getElementById('child-age').innerText = guestData.child_age || ""; // Use child data from guestData

    // Show the checklist container
    document.querySelector('.checklist-container').style.display = 'block';
}

document.addEventListener('DOMContentLoaded', function() {
    const checklistContainer = document.getElementById('checklist-container');
    const prescreeningTable = document.getElementById('pre-screening-table');
    const showChecklistLink = document.getElementById('show-checklist-link');
    const backLink = document.getElementById('back-link');

    // Show checklist when link is clicked
    showChecklistLink.addEventListener('click', function(event) {
        event.preventDefault(); // Prevent default link behavior
        prescreeningTable.style.display = 'none'; // Hide pre-screening table
        checklistContainer.style.display = 'block'; // Show checklist
    });

    // Hide checklist and show pre-screening table when back link is clicked
    backLink.addEventListener('click', function(event) {
        event.preventDefault(); // Prevent default link behavior
        checklistContainer.style.display = 'none'; // Hide checklist
        prescreeningTable.style.display = 'block'; // Show pre-screening table
    });
});
