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
                        console.log('Selected Date:', dateString);  // Debugging the selected date
                    });
                }

                // Pre-select the day if it matches the selected day
                if (selectedDay === day) {
                    dayDiv.classList.add('selected');
                    document.getElementById('selectedDate').value = dateString;
                    console.log('Selected Date (Pre-selected):', dateString);  // Debugging the selected date
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
    console.log('Selected Time:', selectedTime);
    
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

document.getElementById('confirmTimeButton').addEventListener('click', function() {
    const selectedDate = document.getElementById('selectedDate').value; // This is the selected date in YYYY-MM-DD format
    const selectedTime = selectedTime; // Use the directly stored value

    if (selectedDate && selectedTime) {
        // Convert selected time to 24-hour format
        const formattedTime = convertTo24HourFormat(selectedTime);

        // Create a new schedule combining the selected date and time
        const newSchedule = `${selectedDate} ${formattedTime}`;

        // Perform the AJAX request to update the appointment
        fetch('/update_appointment.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                originalAppointmentDate: originalAppointmentDate,
                newSchedule: newSchedule,
            }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                openMessagePopup('Your appointment has been rescheduled successfully.');
            } else {
                openMessagePopup('Failed to reschedule appointment. Please try again later.');
            }
        })
        .catch(error => {
            console.error('Error updating appointment:', error);
            openMessagePopup('An error occurred. Please try again.');
        });
    } else {
        openMessagePopup('Please select both a date and a time.');
    }
});

// Function to convert time to 24-hour format
function convertTo24HourFormat(time) {
    const [hour, minute] = time.split(':');
    let hour24 = parseInt(hour);
    const isPM = time.includes('PM');
    if (isPM && hour24 !== 12) {
        hour24 += 12; // Convert PM hours to 24-hour format
    }
    if (!isPM && hour24 === 12) {
        hour24 = 0; // Handle midnight (12 AM)
    }
    return `${String(hour24).padStart(2, '0')}:${minute}`;
}
