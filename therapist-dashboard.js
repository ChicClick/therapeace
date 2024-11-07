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


let selectedMonth = new Date().getMonth(); // Start from the current month (0-11)
let selectedYear = new Date().getFullYear(); // Start from the current year
let selectedTime = null; // To hold the selected time
let originalAppointmentDate = null; // To hold the original appointment date

// Function to open popup when "Reschedule" is clicked
function openPopup(appointmentDate) {
    document.getElementById('reschedulePopup').style.display = 'block';

    // Parse the appointment date
    const dateParts = appointmentDate.split(' ');
    const [year, month, day] = dateParts[0].split('-').map(Number);
    selectedYear = year; // Set selectedYear to appointment year
    selectedMonth = month - 1; // Month is 0-based in JavaScript

    // Call to generate calendar when popup is opened
    generateCalendar(day); // Pass day to pre-select it
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
function generateCalendar(selectedDay = null) { // Allow passing selected day
    const calendarGrid = document.querySelector('.calendar-grid');
    calendarGrid.innerHTML = ''; // Clear any previously generated calendar

    // Set the month display
    document.getElementById('currentMonth').innerText = `${getMonthName(selectedMonth)} ${selectedYear}`;

    const daysInMonth = new Date(selectedYear, selectedMonth + 1, 0).getDate(); // Get total days in the current month

    // Get therapist ID of the logged-in therapist (assuming you have it available)
    const therapistID = getLoggedInTherapistID();

    // Fetch booked dates for the therapist
    fetchBookedDatesForTherapist(therapistID)
        .then(bookedDates => {
            // Loop through the days of the month
            for (let day = 1; day <= daysInMonth; day++) {
                const dateString = `${selectedYear}-${String(selectedMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                const dayDiv = document.createElement('div');
                dayDiv.classList.add('day');
                dayDiv.innerText = day;

                // Create a date object for the generated date
                const generatedDate = new Date(dateString);
                const currentDate = new Date(); // Update current date each time

                // Check if the day is Sunday (getDay() === 0 means Sunday)
                if (generatedDate.getDay() === 0) {
                    dayDiv.classList.add('disabled'); // Disable Sundays
                }

                // If date is in the past, already booked, or already an appointment for the therapist, disable it
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

                // Pre-select the day if it matches the selected day
                if (selectedDay === day) {
                    dayDiv.classList.add('selected');
                    document.getElementById('selectedDate').value = dateString;
                }

                calendarGrid.appendChild(dayDiv);
            }
        })
        .catch(error => {
            console.error("Error fetching booked dates:", error);
        });
}

// Function to fetch booked dates for a therapist
function fetchBookedDatesForTherapist(therapistID) {
    return fetch(`/booked-dates.php?therapistID=${therapistID}`)
        .then(response => response.json())
        .then(data => data.bookedDates) // Assume the response contains a "bookedDates" array
        .catch(error => {
            console.error("Error fetching booked dates for therapist:", error);
            return []; // Return an empty array in case of error
        });
}

// Function to get logged-in therapist's ID (replace with actual logic)
function getLoggedInTherapistID() {
    // Replace this with actual logic to get the therapist's ID from session or JWT
    return; // Example therapist ID
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
            const appointmentDate = this.dataset.id; // Get the appointment date
            openPopup(appointmentDate); // Pass it to openPopup
        });
    });
});

// Function to open the time selection popup
function openTimePopup() {
    document.getElementById('timePopup').style.display = 'block';
    generateAvailableTimes(); // Call to populate available times
    originalAppointmentDate = document.getElementById('selectedDate').value; // Store the original date
}

// Function to close the time selection popup
function closeTimePopup() {
    document.getElementById('timePopup').style.display = 'none';
}

// Function to generate available times for morning and afternoon sessions
function generateAvailableTimes() {
    const morningTimes = ['9:00 AM', '10:00 AM', '11:00 AM', '12:00 PM'];
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
function selectTime(selectedTimeValue) {
    selectedTime = selectedTimeValue; // Store the selected time
    console.log('Selected time:', selectedTime);
    
    // Highlight the selected time in the list
    const timeItems = document.querySelectorAll('#morningTimes li, #afternoonTimes li');
    timeItems.forEach(item => {
        if (item.innerText === selectedTime) {
            item.classList.add('selected'); // Highlight selected time
        } else {
            item.classList.remove('selected'); // Remove highlight from other times
        }
    });
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

// Function to open the message popup with a specific message
function openMessagePopup(message) {
    document.getElementById('popupMessage').textContent = message;
    document.getElementById('messagePopup').style.display = 'block';
}

// Function to close the message popup
function closeMessagePopup() {
    document.getElementById('messagePopup').style.display = 'none';
}

// Existing function to close the reschedule popup
function closePopup() {
    document.getElementById('reschedulePopup').style.display = 'none';
}

// Existing function to close the time popup
function closeTimePopup() {
    document.getElementById('timePopup').style.display = 'none';
}

document.getElementById('confirmTimeButton').addEventListener('click', function() {
    const selectedDate = document.getElementById('selectedDate').value;
    // Use the selectedTime variable directly
    if (selectedDate && selectedTime) { // Ensure both date and time are selected
        const newSchedule = `${selectedDate} ${selectedTime}`; // Combine date and time into the required format

        // Send AJAX request to update the schedule in the database
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'update_appointment.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status === 200) {
                openMessagePopup('Appointment rescheduled successfully!'); // Open the popup with success message
                closeTimePopup(); // Close time popup
                // Optionally refresh the appointment list or perform any necessary updates
            } else {
                openMessagePopup('Error rescheduling appointment: ' + xhr.responseText); // Open the popup with error message
            }
        };

        // Send the original appointment date and new schedule
        xhr.send(`originalDate=${encodeURIComponent(originalAppointmentDate)}&newSchedule=${encodeURIComponent(newSchedule)}`);
    } else {
        openMessagePopup('Please select both date and time.'); // Open the popup if no selection is made
    }
});


// Event listener for closing the message popup
document.getElementById('closePopup').addEventListener('click', closeMessagePopup);

// Event listener for confirming the message popup
document.getElementById('confirmPopup').addEventListener('click', closeMessagePopup);



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

