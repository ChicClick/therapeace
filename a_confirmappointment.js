// Event listener for form submission
document.getElementById('appointment-form').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent the form from submitting immediately

    // Set the hidden field with the selected date and time values
    document.getElementById('selectedDateTime').value = `${document.getElementById('selectedDate').value} ${document.getElementById('selectedTime').value}`;

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

