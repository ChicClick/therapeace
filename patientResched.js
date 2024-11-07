let selectedAppointmentID = null; // Declare this globally so it persists across the script

document.addEventListener('DOMContentLoaded', function () {
    const rescheduleButtons = document.querySelectorAll('.reschedule-button');
    const calendarGrid = document.querySelector('.calendar-grid');
    const currentMonthElem = document.getElementById('currentMonth');
    const prevMonthLink = document.getElementById('prevMonth');
    const nextMonthLink = document.getElementById('nextMonth');
    const selectedDateInput = document.getElementById('selectedDate');
    const reschedulePopup = document.getElementById('reschedulePopup');
    const proceedButton = document.getElementById('proceedButton');
    const timePopup = document.getElementById('timePopup');
    const morningTimesList = document.getElementById('morningTimes');
    const afternoonTimesList = document.getElementById('afternoonTimes');

    let currentDate = new Date();
    let availableDates = []; // Array to store available dates and their time slots
    let selectedDate = null; // Store the selected date globally
    let selectedTimeSlots = []; // Store the available time slots for the selected date

    // Initialize the calendar on page load
    updateCalendar();

    // Add event listeners for reschedule buttons
    rescheduleButtons.forEach(button => {
        button.addEventListener('click', function () {
            selectedAppointmentID = this.dataset.appointmentId; // Set the appointment ID from the button's data attribute
            console.log('Reschedule button clicked. Appointment ID:', selectedAppointmentID); // Log appointment ID
            openPopup();
        });
    });

    // Function to open the popup and fetch available dates
    function openPopup() {
        console.log('Opening popup for appointment ID:', selectedAppointmentID); // Log appointment ID when opening the popup
        reschedulePopup.style.display = 'block'; // Make the popup visible
        document.body.style.overflow = 'hidden'; // Disable scrolling on the body while popup is open
        
        fetch(`patientFetchAvailableDates.php?appointmentID=${selectedAppointmentID}`)
            .then(response => response.json())
            .then(data => {
                console.log("Available Dates:", data.dates); // Log the available dates
    
                // Assign the fetched available dates to the availableDates variable
                availableDates = data.dates.map(dateObj => dateObj); 
    
                // Update the calendar and load the available dates
                updateCalendar(); // Update calendar grid after popup is fully loaded
            })
            .catch(error => console.error('Error fetching available dates:', error));
    }

    // Function to close the popup
    function closePopup() {
        reschedulePopup.style.display = 'none'; // Hide the popup
        document.body.style.overflow = 'auto'; // Re-enable body scrolling
    }

    // Function to update the calendar grid
    function updateCalendar() {
        calendarGrid.innerHTML = ''; // Clear existing grid content
    
        const firstDayOfMonth = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
        const lastDayOfMonth = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
        const daysInMonth = lastDayOfMonth.getDate();
        const firstDayOfWeek = firstDayOfMonth.getDay();
    
        // Update the month header
        currentMonthElem.textContent = `${firstDayOfMonth.toLocaleString('default', { month: 'long' })} ${currentDate.getFullYear()}`;
    
        // Create empty cells for leading blank days
        for (let i = 0; i < firstDayOfWeek; i++) {
            const emptyCell = document.createElement('div');
            emptyCell.classList.add('empty');
            calendarGrid.appendChild(emptyCell);
        }
    
        // Populate calendar with days of the month
        for (let day = 1; day <= daysInMonth; day++) {
            const dateCell = document.createElement('div');
            dateCell.classList.add('date');
            dateCell.textContent = day;
    
            // Format the date for comparison (YYYY-MM-DD)
            const currentDateString = `${currentDate.getFullYear()}-${(currentDate.getMonth() + 1).toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
    
            // Check if the current date is available in availableDates
            const dateData = availableDates.find(dateObj => dateObj.date === currentDateString);
    
            if (dateData) {
                if (dateData.isAvailable) {
                    dateCell.classList.add('available');
                    dateCell.addEventListener('click', () => handleDateSelection(currentDateString, dateData.timeSlots));
                } else {
                    dateCell.classList.add('unavailable');
                }
            } else {
                dateCell.classList.add('unavailable');
            }
    
            calendarGrid.appendChild(dateCell);
        }
    }

    // Function to handle the date selection with available time slots
    function handleDateSelection(date, timeSlots) {
        console.log("Selected date:", date);
        console.log("Available time slots:", timeSlots);
        
        // Store the selected date and its time slots
        selectedDate = date;
        selectedTimeSlots = timeSlots;
        
        console.log("Stored time slots:", selectedTimeSlots); // Add this line
    
        // Remove 'selected' class from previously selected date (if any)
        const previouslySelectedDate = calendarGrid.querySelector('.selected');
        if (previouslySelectedDate) {
            previouslySelectedDate.classList.remove('selected');
        }
    
        // Add 'selected' class to the newly selected date cell
        const selectedDateCell = Array.from(calendarGrid.children).find(cell => cell.textContent === date.split('-')[2]);
        if (selectedDateCell) {
            selectedDateCell.classList.add('selected');
        }
    }
    

    const closeTimePopupBtn = document.querySelector('#timePopup .close');  // Select close button inside timePopup
    if (closeTimePopupBtn) {
        closeTimePopupBtn.addEventListener('click', closeTimePopup);  // Attach the closeTimePopup function to the close button
    }
    
    // Function to close the time popup
    function closeTimePopup() {
        timePopup.style.display = 'none'; // Hide the time popup
        document.body.style.overflow = 'auto'; // Re-enable body scrolling
    }
    

    // This function is called when the "Proceed" button is clicked to open the time popup
    function openTimePopup() {
        timePopup.style.display = 'block'; // Show the time popup
        document.body.style.overflow = 'hidden'; 

        // Close the reschedule popup (first popup)
        closePopup();
    }

    // Example of how you open the time popup when proceeding
    proceedButton.addEventListener('click', function () {
        if (selectedDate && selectedTimeSlots.length > 0) {
            openTimePopup(); // Show the time popup
            populateTimeSlots(); // Populate available time slots
        } else {
            console.error("No time slots available for the selected date.");
        }
    });

    function populateTimeSlots() {
        // Clear existing time slots
        morningTimesList.innerHTML = '';
        afternoonTimesList.innerHTML = '';
    
        // Split the available times into morning and afternoon
        selectedTimeSlots.forEach(time => {
            const listItem = document.createElement('li');
    
            // Create a radio button for the time slot
            const radioButton = document.createElement('input');
            radioButton.type = 'radio';
            radioButton.name = 'timeSlot';  // Ensure all time slots share the same name so only one can be selected
            radioButton.value = time;  // Set the value of the radio button
            radioButton.id = time; // Add an ID for easier reference
    
            // Create a label for the radio button and the time text
            const label = document.createElement('label');
            label.setAttribute('for', time); // Associate label with radio button by ID
            label.textContent = time;
    
            // Add the radio button and label to the list item
            listItem.appendChild(radioButton);
            listItem.appendChild(label);
    
            // Assume times before 12:00 PM are morning sessions, else afternoon
            if (parseInt(time.split(':')[0]) < 12) {
                morningTimesList.appendChild(listItem);
            } else {
                afternoonTimesList.appendChild(listItem);
            }
        });
    }

    // Event listeners for month navigation
    prevMonthLink.addEventListener('click', function () {
        currentDate.setMonth(currentDate.getMonth() - 1);
        updateCalendar();
    });

    nextMonthLink.addEventListener('click', function () {
        currentDate.setMonth(currentDate.getMonth() + 1);
        updateCalendar();
    });

    // Close the popup when clicking on the close button
    const closeBtn = document.querySelector('.close');
    closeBtn.addEventListener('click', closePopup);
});

// Function to confirm the selected time and proceed with rescheduling
function confirmTime() {
    const selectedTime = document.querySelector('input[name="timeSlot"]:checked');  // Get the checked radio button
    console.log("Selected time:", selectedTime);

    if (selectedTime) {
        const time = selectedTime.value;  // Get the value (time) of the selected radio button
        console.log("Confirmed time:", time);
        console.log("Selected appointment ID (from confirmTime):", selectedAppointmentID); // Log the appointment ID

        // Proceed with rescheduling logic, e.g., submitting the selected time
        if (selectedDate && time) {
            rescheduleAppointment(selectedDate, time);  // Assuming you have a function to handle rescheduling
        } else {
            console.error("Please select both a time slot and a date.");
        }
    } else {
        alert("Please select a time slot.");
    }
}


function rescheduleAppointment(date, time) {
    console.log("Attempting to reschedule appointment:", selectedAppointmentID, date, time);

    const selectedDatetime = `${date} ${time}`; // Combine date and time into one string

    fetch('patientRescheduleAppointment.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded', // Change content type to form data
        },
        body: new URLSearchParams({
            appointmentID: selectedAppointmentID, // Send the appointment ID
            selectedDatetime: selectedDatetime // Send the combined date and time
        })
    })
    .then(response => response.json())
.then(data => {
    console.log("Response data:", data);  // Log the response to see what comes back
    if (data.success) {
        alert("Appointment rescheduled successfully.");
        closePopup();
    } else {
        alert("Failed to reschedule appointment. " + (data.message || ""));
    }
})
.catch(error => {
    console.error("Error rescheduling appointment:", error);
});

}


