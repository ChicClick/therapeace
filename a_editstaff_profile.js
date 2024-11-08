document.addEventListener("DOMContentLoaded", function() {
    const popup = document.getElementById('staff-profile-popup');
    const editStaffForm = document.getElementById('editstaff-profile-form');
    const closePopupButton = document.getElementById('close-edit-staff-profile-popup');
    let currentStaffID;  // Variable to store the current staff ID

    // Close popup on close button click
    closePopupButton.addEventListener('click', function() {
        popup.style.display = 'none';
    });

    // Close popup when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target === popup) {
            popup.style.display = 'none';
        }
    });

    // Select all "Edit Staff" buttons
    const editButtons = document.querySelectorAll('.edit-staff-profile');

    // Add click event listener to each "Edit Staff" button
    editButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            // Prevent triggering the row click event
            event.stopPropagation();

            // Retrieve the row data attributes
            const row = button.closest('tr');  // Get the closest table row
            currentStaffID = row.getAttribute('data-staff-id');
            const staffName = row.getAttribute('data-staff-name');
            const staffPosition = row.querySelector('td:nth-child(2)').innerText;
            const staffDateHired = row.querySelector('td:nth-child(3)').innerText;

            // Populate form fields with the retrieved data
            editStaffForm.querySelector('#staffName').value = staffName;
            editStaffForm.querySelector('#position').value = staffPosition;
            editStaffForm.querySelector('#datehired').value = staffDateHired;

            // Show the popup
            popup.style.display = 'block';
        });
    });

    // Handle form submission
    editStaffForm.addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent traditional form submission

        const formData = new FormData(editStaffForm);
        formData.append('staffID', currentStaffID); // Add staff ID to form data

        fetch('a_editstaffprofile_endpoint.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Staff profile updated successfully!');
                popup.style.display = 'none'; // Close popup
                location.reload(); // Refresh to show updated data
            } else {
                alert('Error updating staff profile: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('There was an error with the request.');
        });
    });
});
