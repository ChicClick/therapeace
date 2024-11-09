// left section 
document.addEventListener('DOMContentLoaded', () => {
    const links = document.querySelectorAll('.left-section nav a');
    const sections = document.querySelectorAll('.right-section .content');
    const menuItems = document.querySelectorAll('.left-section ul li');

    links.forEach(link => {
        link.addEventListener('click', (event) => {
            if (link.getAttribute('href') === 'registerlanding.php') {
                // Allow default behavior for the "Sign Out" link
                return; 
            }

            const targetId = link.getAttribute('data-target');

            // Check if the clicked link is the "Sign Out" link
            if (link.getAttribute('href') === 'adminlogin.php') {
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

// Global variables to track the currently selected month and year
let selectedMonth = new Date().getMonth(); // Start from the current month (0-11)
let selectedYear = new Date().getFullYear(); // Start from the current year

// Open the calendar popup when "Add Appointment" is clicked
document.getElementById('add-appointment-button').addEventListener('click', openPopup);

function openPopup() {
    document.getElementById('reschedulePopup').style.display = 'block';
    generateCalendar(); // Call to generate calendar when popup is opened
}

// Close the calendar popup
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

let bookedDates = [];  // Array to store the booked dates from the server

// Fetch booked dates from the server (e.g., through an AJAX request)
async function fetchBookedDates() {
    try {
        const response = await fetch('a_bookeddate.php');  // Server-side script to get booked dates
        const data = await response.json();  // Assuming the server returns an array of date strings
        bookedDates = data;  // Store the booked dates in the bookedDates array
        console.log("Fetched Booked Dates: ", bookedDates); // Debug: log fetched booked dates
        generateCalendar();  // Regenerate the calendar after fetching booked dates
    } catch (error) {
        console.error("Error fetching booked dates:", error);
    }
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

        const isFullyBooked = bookedDates.includes(dateString);

        // Debug: log each generated date
        console.log("Generated Date: ", dateString, (isFullyBooked ? " - Yesss!!!" : " - Nooooo"));

        

        // Disable Sundays, past dates, and booked dates
        if (generatedDate.getDay() === 0 || generatedDate < currentDate || isFullyBooked) {
            dayDiv.classList.add('disabled'); // Add disabled class to Sundays, past dates, and booked dates
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

// Call the fetchBookedDates function to load booked dates
fetchBookedDates();



// Event listener for month navigation
document.getElementById('nextMonth').addEventListener('click', (e) => {
    e.preventDefault(); // Prevent the default anchor click behavior
    selectedMonth++;
    if (selectedMonth > 11) {
        selectedMonth = 0; // Reset to January if it exceeds December
        selectedYear++; // Increment year
    }
    generateCalendar(); // Regenerate calendar with updated month and year
});

document.getElementById('prevMonth').addEventListener('click', (e) => {
    e.preventDefault(); // Prevent the default anchor click behavior
    selectedMonth--;
    if (selectedMonth < 0) {
        selectedMonth = 11; // Reset to December if it goes below January
        selectedYear--; // Decrement year
    }
    generateCalendar(); // Regenerate calendar with updated month and year
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
    const morningTimes = ['9:00 AM', '10:00 AM', '11:00 AM', '12:00 PM'];
    const afternoonTimes = ['1:00 PM', '2:00 PM', '3:00 PM', '4:00 PM', '5:00 PM', '6:00 PM'];

    const morningList = document.getElementById('morningTimes');
    const afternoonList = document.getElementById('afternoonTimes');

    morningList.innerHTML = ''; // Clear previous times
    afternoonList.innerHTML = '';

    // Generate morning times
    morningTimes.forEach(time => {
        const li = document.createElement('li');
        li.innerText = time;
        li.classList.add('time-slot');
        li.addEventListener('click', () => selectTime(li, time)); // Pass both the element and time
        morningList.appendChild(li);
    });

    // Generate afternoon times
    afternoonTimes.forEach(time => {
        const li = document.createElement('li');
        li.innerText = time;
        li.classList.add('time-slot');
        li.addEventListener('click', () => selectTime(li, time)); // Pass both the element and time
        afternoonList.appendChild(li);
    });
}

// Function to convert 24-hour time format to 12-hour AM/PM format
function convertTo12HourFormat(time) {
    let [hours, minutes] = time.split(':');
    hours = parseInt(hours);

    let suffix = 'AM';
    if (hours >= 12) {
        suffix = 'PM';
        if (hours > 12) {
            hours -= 12;  // Convert to 12-hour format
        }
    }

    // Add leading zero for hours and minutes if needed
    return `${hours}:${minutes} ${suffix}`;
}

// Function to handle time selection
function selectTime(element, selectedTime) {
    // Remove 'selected' class from any previously selected time slot
    document.querySelectorAll('.time-slot').forEach(slot => slot.classList.remove('selected'));

    // Add 'selected' class to the clicked time slot
    element.classList.add('selected');

    // Set the value of the hidden input to the selected time
    document.getElementById('selectedTime').value = selectedTime;
    console.log('Selected time:', selectedTime); // For debugging
}

// Event listener for the Proceed button in the reschedule (calendar) popup
document.getElementById('proceedButton').addEventListener('click', () => {
    const selectedDate = document.getElementById('selectedDate').value;
    if (selectedDate) { // Ensure a date is selected
        closePopup(); // Close the reschedule popup
        openTimePopup(); // Open the time selection popup
    } else {
        alert('Please select a date first.'); // Alert if no date is selected
    }
});

// Event listener for the Proceed button in the time popup
document.querySelector('#timePopup #proceedButton').addEventListener('click', () => {
    const selectedTime = document.getElementById('selectedTime').value;

    if (selectedTime) { // Check if a time is selected
        closeTimePopup(); // Close the time selection popup
        openAppointmentPopup(); // Open the appointment form popup
    } else {
        alert('Please select a time first.'); // Alert if no time is selected
    }
});

// Function to open the appointment form popup
function openAppointmentPopup() {
    document.getElementById('appointment-popup-form').style.display = 'block';
}

// Event listener to close the appointment form popup when the close button is clicked
document.getElementById('close-popup').addEventListener('click', function() {
    document.getElementById('appointment-popup-form').style.display = 'none';
});


// Fetching patientID for autofill Selection
function toggleInput(selectElement) {
    // Get the selected value from the dropdown
    const selectedValue = selectElement.value;

    // Update the input field's value with the selected Patient ID
    document.getElementById('selected-patient-id').value = selectedValue;
}

$(document).ready(function() {
    $('#patient-ID').change(function() {
        var patientId = $(this).val();
        console.log("Patient ID selected: " + patientId); // Debugging step

        // Check if a patient ID is selected
        if (patientId) {
            $.ajax({
                url: 'a_fetch_patientID.php', // Endpoint to fetch patient details
                type: 'POST',
                data: { patient: patientId },
                success: function(response) {
                    try {
                        var patient = JSON.parse(response); // Parse the JSON response

                        // Update the fields with patient data
                        $('#patient-name').val(patient.patientName);
                        $('#parentID').val(patient.parentID);
                        $('#contact-number').val(patient.phone);
                    } catch (error) {
                        console.error("JSON parsing error:", error);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error("Error fetching patient data: " + textStatus, errorThrown);
                }
            });
        } else {
            // Clear the input fields if no patient is selected
            $('#patient-name').val('');
            $('#parentID').val('');
            $('#contact-number').val('');
        }
    });
      
});

// Fetching therapistName for autofill
$(document).ready(function() {
    $('#therapist').on('click', function() {
        // Only fetch data if the dropdown is empty
        if ($('#therapist option').length === 1) {
            $.ajax({
                url: 'a_fetch_therapist.php', // Path to your PHP file
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    // Clear existing options
                    $('#therapist').find('option:not(:first)').remove();
                    // Append new options
                    $.each(data, function(index, therapist) {
                        $('#therapist').append($('<option>', {
                            value: therapist.therapistID, // Use therapistID as the value
                            text: therapist.therapistName // Use therapistName as the display text
                        }));
                    });
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('Error fetching therapists:', textStatus, errorThrown);
                }
            });
        }
    });
});


// Patients Profile active row 
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
        fetch(`a_fetch_patient_info.php?id=${patientId}`) // Corrected line
            .then(response => {
                if (!response.ok) { // Check for HTTP errors
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(patientData => {
                console.log('Fetched patient data:', patientData); // Debugging line
                if (patientData.error) {
                    console.error('Patient not found:', patientData.error);
                    alert('Patient not found'); // Notify user
                } else {
                    // Populate the patient info section
                    document.getElementById('patient_name').textContent = patientData.patient_name; // Ensure this matches your JSON keys
                    document.getElementById('service').textContent = patientData.service; // Ensure this key exists in your PHP
                    document.getElementById('parent_name').textContent = patientData.parent_name;
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
        document.getElementById('patient_name').textContent = '';
        document.getElementById('service').textContent = '';
        document.getElementById('parent_name').textContent = '';
        document.getElementById('phone').textContent = '';
        document.getElementById('email').textContent = '';
        document.getElementById('address').textContent = '';
        document.getElementById('birthday').textContent = '';
        document.getElementById('gender').textContent = '';
    }
});




// Staff Section 
// Get references to the buttons and table container
function setActive(tableId) {
    // Hide all tables
    const allTables = document.querySelectorAll('.table-container');
    allTables.forEach(table => table.classList.add('hidden'));

    // Show the selected table
    const selectedTable = document.getElementById(tableId);
    selectedTable.classList.remove('hidden');
}

// Function to display data in the table
function displayTableData(data) {
    // Clear existing rows
    tableBody.innerHTML = '';

    // Loop through the data and create table rows
    data.forEach(item => {
        const row = document.createElement('tr');
        row.innerHTML = `<td>${item.name}</td><td>${item.position}</td><td>${item.dateHired}</td>`;
        tableBody.appendChild(row);
    });

    // Show the table container
    tableContainer.classList.remove('hidden');
}

// Event listeners for each button
clinicStaffBtn.addEventListener('click', () => {
    displayTableData(clinicStaffData);
});

clinicAdminBtn.addEventListener('click', () => {
    displayTableData(clinicAdminData);
});

clinicTherapistBtn.addEventListener('click', () => {
    displayTableData(clinicTherapistData);
});

// Add staff 
document.addEventListener('DOMContentLoaded', function () {
    const addStaffButton = document.getElementById('add-staff-button');
    const addStaffPopup = document.getElementById('staff-popup'); // Update the ID here
    const closePopupButton = document.getElementById('close-popup');

    // Show the popup when the button is clicked
    addStaffButton.addEventListener('click', function () {
        addStaffPopup.style.display = 'block';
    });

    // Hide the popup when the close button is clicked
    closePopupButton.addEventListener('click', function () {
        addStaffPopup.style.display = 'none';
    });

    // Hide the popup when clicking outside of the popup content
    window.addEventListener('click', function (event) {
        if (event.target === addStaffPopup) {
            addStaffPopup.style.display = 'none';
        }
    });
});

function handleSubmit() {
    /* TODO something */
}

//Service
//Add Service 
