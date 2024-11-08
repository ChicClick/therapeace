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
async function generateCalendar(selectedDay = null) {
    const calendarGrid = document.querySelector('.calendar-grid');
    calendarGrid.innerHTML = ''; // Clear any previously generated calendar

    // Set the month display
    document.getElementById('currentMonth').innerText = `${getMonthName(selectedMonth)} ${selectedYear}`;

    const daysInMonth = new Date(selectedYear, selectedMonth + 1, 0).getDate();
    const firstDayOfMonth = new Date(selectedYear, selectedMonth, 1).getDay(); // Get the first day of the month (0 = Sunday, 6 = Saturday)

    // Fill the grid with blank days until the first day of the month
    for (let i = 0; i < firstDayOfMonth; i++) {
        const blankDiv = document.createElement('div');
        blankDiv.classList.add('day', 'blank'); // Style as blank day
        calendarGrid.appendChild(blankDiv);
    }

    // Get therapist ID of the logged-in therapist (assuming you have it available)
    const therapistID = getLoggedInTherapistID();

    // Fetch booked dates for the therapist
    await fetchBookedDatesForTherapist(therapistID)
        .then(bookedDates => {
            for (let day = 1; day <= daysInMonth; day++) {
                const dateString = `${selectedYear}-${String(selectedMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                const dayDiv = document.createElement('div');
                dayDiv.classList.add('day');
                dayDiv.innerText = day;

                const generatedDate = new Date(dateString);
                const currentDate = new Date();

                if (generatedDate.getDay() === 0) {
                    dayDiv.classList.add('disabled');
                }

                if (generatedDate < currentDate || bookedDates.includes(dateString)) {
                    dayDiv.classList.add('disabled');
                } else {
                    dayDiv.addEventListener('click', () => {
                        document.querySelectorAll('.day').forEach(el => el.classList.remove('selected'));
                        dayDiv.classList.add('selected');
                        document.getElementById('selectedDate').value = dateString;
                    });
                }

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
async function fetchBookedDatesForTherapist(therapistID) {
    try {
        const response = await fetch(`booked-dates.php?therapistID=${therapistID}`);
        console.log(response);
        const data = await response.json();
        return data.bookedDates;
    } catch (error) {
        console.error("Error fetching booked dates for therapist:", error);
        return [];
    }
}

// Function to get logged-in therapist's ID (replace with actual logic)
function getLoggedInTherapistID() {
    // Replace this with actual logic to get the therapist's ID from session or JWT
    return "T001"; // Example therapist ID
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
    const selectedTimeSlots = ['9:00 AM', '10:00 AM', '11:00 AM', '12:00 PM', '1:00 PM', '2:00 PM', '3:00 PM', '4:00 PM', '5:00 PM', '6:00 PM'];
    
    const selectedDate = document.querySelector("#selectedDate").value;
    const morningList = document.getElementById('morningTimes');
    const afternoonList = document.getElementById('afternoonTimes');

    morningList.innerHTML = ''; // Clear previous times
    afternoonList.innerHTML = '';

    const currentTime = new Date();
    const selectedDateObj = new Date(selectedDate);
    const isToday = selectedDateObj.toDateString() === currentTime.toDateString();

    const nextHourTime = new Date(currentTime);
    nextHourTime.setHours(currentTime.getHours() + 1);
    nextHourTime.setMinutes(0);

    selectedTimeSlots.forEach(time => {
        const listItem = document.createElement('li');

        const radioButton = document.createElement('input');
        radioButton.type = 'radio';
        radioButton.name = 'timeSlot'; 
        radioButton.value = time;  
        radioButton.id = time; 

        const [timePart, period] = time.split(' ');
        let [hours, minutes] = timePart.split(':').map(num => parseInt(num));
        if (period === 'PM' && hours !== 12) hours += 12;
        if (period === 'AM' && hours === 12) hours = 0;

        const timeSlot = new Date(selectedDateObj);
        timeSlot.setHours(hours);
        timeSlot.setMinutes(minutes);
        timeSlot.setSeconds(0);
        timeSlot.setMilliseconds(0);

        if (isToday && (timeSlot <= currentTime || timeSlot <= nextHourTime)) {
            radioButton.disabled = true;  
            const label = document.createElement('label');
            label.textContent = "N/A";
            label.setAttribute('for', time); 
            
            listItem.appendChild(radioButton);
            listItem.appendChild(label);
        } else {
            const label = document.createElement('label');
            label.setAttribute('for', time);
            label.textContent = time;

            listItem.appendChild(radioButton);
            listItem.appendChild(label);
        }

        if (hours < 12) {
            morningList.appendChild(listItem);
        } else {
            afternoonList.appendChild(listItem);
        }
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
document.getElementById('proceedButton').addEventListener('click', (e)=> {
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
    const popUpMessage = document.getElementById('popupMessage');
    const messagePop = document.getElementById('messagePopup');
    const timePop = document.querySelector("#timePopup");
    popUpMessage.textContent = message;
    messagePop.style.display = 'block';

    document.querySelector("#confirmPopup").addEventListener("click", ()=> {
        messagePop.style.display = "none";
        timePop.style.display = "none";
    });
}

// Function to close the message popup
function closeMessagePopup() {
    document.getElementById('messagePopup').style.display = 'none';
}

document.getElementById('confirmTimeButton').addEventListener('click', async () => {
    const selectedDate = document.getElementById('selectedDate').value;
    const selectedTime = document.querySelector('input[name="timeSlot"]:checked');

    if (selectedTime && selectedTime.value.length <= 5) {
        selectedTime.value = selectedTime.value + ":00";
    }

    if (selectedDate && selectedTime) {
        const newSchedule = `${selectedDate} ${convertTo24HourFormat(selectedTime.value)}`;
        console.log(newSchedule);
        await fetch('update_appointment.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                selectedDatetime: newSchedule,
                therapistID: getLoggedInTherapistID()  // matches the PHP script's expected name
            }),
        })
        .then(response => {
            return response.json()
        })
        .then(data => {

            if (data.success) {
                openMessagePopup('Your appointment has been rescheduled successfully.');
                setTimeout(()=> {
                    window.location.reload();
                }, 1000);
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
    let [timePart, period] = time.split(' ');
    if (!period) period = '';

    let [hour, minute, second] = timePart.split(':');
    hour = parseInt(hour);
    const isPM = period === 'PM';

    if (isPM && hour !== 12) {
        hour += 12;
    } else if (!isPM && hour === 12) {
        hour = 0;
    }

    return `${String(hour).padStart(2, '0')}:${String(minute).padStart(2, '0')}:${second || '00'}`;
}
