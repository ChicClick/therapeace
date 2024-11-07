let selectedAppointmentDate = null;  // To hold the original appointment date
let selectedNewDate = null;  // To hold the newly selected date
let selectedTimeSlot = null;  // To hold the selected time slot

// Button for sending results and opening rescheduling popup
document.getElementById('sendResultsButton').addEventListener('click', function() {
    openReschedulePopup();
});

// Function to open the reschedule popup
function openReschedulePopup() {
    document.getElementById('reschedulePopup').style.display = 'block';
    loadCalendar(selectedAppointmentDate);  // Load the calendar for the current month
}

// Function to close the reschedule popup
function closeReschedulePopup() {
    document.getElementById('reschedulePopup').style.display = 'none';
}

// Function to load the calendar for date selection
function loadCalendar(selectedDate = null) {
    const calendarContainer = document.querySelector('.calendar-grid');
    calendarContainer.innerHTML = '';  // Clear any existing content

    const currentMonth = new Date();
    const totalDaysInMonth = new Date(currentMonth.getFullYear(), currentMonth.getMonth() + 1, 0).getDate();
    const firstDayOfMonth = new Date(currentMonth.getFullYear(), currentMonth.getMonth(), 1).getDay();

    // Display month and year
    document.getElementById('currentMonth').innerText = `${getMonthName(currentMonth.getMonth())} ${currentMonth.getFullYear()}`;

    // Loop through the days of the month
    for (let day = 1; day <= totalDaysInMonth; day++) {
        const date = `${currentMonth.getFullYear()}-${String(currentMonth.getMonth() + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const dayElement = document.createElement('div');
        dayElement.classList.add('calendar-day');
        dayElement.innerText = day;

        // Disable past dates
        const currentDate = new Date();
        const dayDate = new Date(date);
        if (dayDate < currentDate) {
            dayElement.classList.add('disabled');
        } else {
            dayElement.addEventListener('click', function() {
                document.querySelectorAll('.calendar-day').forEach(d => d.classList.remove('selected'));
                dayElement.classList.add('selected');
                selectedNewDate = date;
            });
        }

        // Highlight the selected date if it's the same as the previous appointment date
        if (selectedDate && selectedDate === date) {
            dayElement.classList.add('selected');
            selectedNewDate = date;
        }

        calendarContainer.appendChild(dayElement);
    }
}

// Function to get the month name
function getMonthName(monthIndex) {
    const months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    return months[monthIndex];
}

// Function to open the time selection popup
function openTimeSelectionPopup() {
    if (selectedNewDate) {
        document.getElementById('timeSelectionPopup').style.display = 'block';
        generateAvailableTimeSlots();
    } else {
        alert("Please select a date first.");
    }
}

// Function to close the time selection popup
function closeTimeSelectionPopup() {
    document.getElementById('timeSelectionPopup').style.display = 'none';
}

// Function to generate available time slots for the selected date
function generateAvailableTimeSlots() {
    const morningSlots = ['9:00 AM', '10:00 AM', '11:00 AM', '12:00 PM'];
    const afternoonSlots = ['1:00 PM', '2:00 PM', '3:00 PM', '4:00 PM', '5:00 PM'];

    const morningContainer = document.getElementById('morningSlots');
    const afternoonContainer = document.getElementById('afternoonSlots');

    morningContainer.innerHTML = '';
    afternoonContainer.innerHTML = '';

    morningSlots.forEach(slot => {
        const li = document.createElement('li');
        li.innerText = slot;
        li.addEventListener('click', () => selectTimeSlot(slot));
        morningContainer.appendChild(li);
    });

    afternoonSlots.forEach(slot => {
        const li = document.createElement('li');
        li.innerText = slot;
        li.addEventListener('click', () => selectTimeSlot(slot));
        afternoonContainer.appendChild(li);
    });
}

// Function to handle time slot selection
function selectTimeSlot(slot) {
    selectedTimeSlot = slot;

    // Highlight the selected time
    const timeItems = document.querySelectorAll('#morningSlots li, #afternoonSlots li');
    timeItems.forEach(item => {
        if (item.innerText === slot) {
            item.classList.add('selected');
        } else {
            item.classList.remove('selected');
        }
    });
}

// Button to confirm the time selection and send results
document.getElementById('confirmTimeButton').addEventListener('click', function() {
    if (selectedNewDate && selectedTimeSlot) {
        // Make the request to update the appointment schedule
        updateAppointmentSchedule();
    } else {
        alert("Please select both a date and a time.");
    }
});

// Function to update the schedule in the database
function updateAppointmentSchedule() {
    const newAppointment = `${selectedNewDate} ${selectedTimeSlot}`;

    // Send AJAX request to update the appointment schedule
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'update_appointment.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status === 200) {
            showMessagePopup("Appointment rescheduled successfully!");
            closeTimeSelectionPopup();
        } else {
            showMessagePopup(`Error rescheduling appointment: ${xhr.responseText}`);
        }
    };

    xhr.send(`originalDate=${encodeURIComponent(selectedAppointmentDate)}&newAppointment=${encodeURIComponent(newAppointment)}`);
}

// Function to show a popup message
function showMessagePopup(message) {
    document.getElementById('messagePopup').innerText = message;
    document.getElementById('messagePopup').style.display = 'block';
}

// Function to close the message popup
function closeMessagePopup() {
    document.getElementById('messagePopup').style.display = 'none';
}

// Function to trigger the update when the 'Send Results' button is clicked
document.getElementById('sendResultsButton').addEventListener('click', function() {
    selectedAppointmentDate = "2024-11-07"; // Example appointment date, replace with dynamic value
    openReschedulePopup();
});
