function convertTo24HourFormat(time12h) {
    // Split the time into components
    const [time, modifier] = time12h.split(' ');

    // Split the hour and minutes
    let [hours, minutes] = time.split(':');

    // Convert hour to an integer
    hours = parseInt(hours, 10);

    // Adjust hours based on AM/PM
    if (modifier === 'PM' && hours !== 12) {
        hours += 12;
    } else if (modifier === 'AM' && hours === 12) {
        hours = 0;
    }

    // Format hours to always have two digits
    const formattedHours = hours.toString().padStart(2, '0');

    return `${formattedHours}:${minutes}`;
}

// Event listener for form submission
document.getElementById('appointment-form').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent the form from submitting immediately

    // Set the hidden field with the selected date and time values
    const selectedDate = document.getElementById('selectedDate').value;
    const selectedTime = document.getElementById('selectedTime').value;
    const time24H = convertTo24HourFormat(selectedTime);
    document.getElementById('selectedDateTime').value = `${selectedDate} ${time24H}`;

    // Close the appointment form popup
    document.getElementById('appointment-popup-form').style.display = 'none';

    // Show the confirmation popup
    openConfirmationPopup();
});

function openConfirmationPopup() {
    const confirmationPopup = document.getElementById('confirmationPopup');
    if (confirmationPopup) {
        confirmationPopup.style.display = 'block';
    } else {
        console.error('Confirmation popup not found in the DOM.');
    }
}

// Function to close the confirmation popup and submit the form
function closeConfirmationPopup() {
    const confirmationPopup = document.getElementById('confirmationPopup');
    if (confirmationPopup) {
        confirmationPopup.style.display = 'none';
        // Now programmatically submit the form
        document.getElementById('appointment-form').submit();
    }
}

// Optionally, add a listener for when the confirmation popup close button is clicked to submit the form
document.querySelector('.close').addEventListener('click', closeConfirmationPopup);

