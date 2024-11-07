document.addEventListener('DOMContentLoaded', () => {
    const addServiceButton = document.getElementById('add-service');
    const addServicePopup = document.getElementById('add-service-popup');
    const closeAddPopup = document.getElementById('close-add-popup');

    // Show the popup form when the Add Service button is clicked
    addServiceButton.addEventListener('click', () => {
        addServicePopup.style.display = 'block';
    });

    // Hide the popup form when the close button is clicked
    closeAddPopup.addEventListener('click', () => {
        addServicePopup.style.display = 'none';
    });

    // Optional: Hide the popup when clicking outside of the form content
    window.addEventListener('click', (event) => {
        if (event.target == addServicePopup) {
            addServicePopup.style.display = 'none';
        }
    });

    document.getElementById('addservice-form').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent the default form submission

        // Get form data
        const formData = new FormData(this);

        // Send form data to PHP script
        fetch('a_addservice.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            alert(data); // Display response message (success or error)
            addServicePopup.style.display = 'none'; // Close the popup after submission
            this.reset(); // Reset the form fields
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });

    // Delete Service Button
    document.getElementById('services-tbody').addEventListener('click', function(event) {
        if (event.target.classList.contains('delete-btn')) {
            const serviceID = event.target.getAttribute('data-id');
            
            if (confirm("Are you sure you want to delete this service?")) {
                fetch('a_deleteservice.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'serviceID=' + serviceID
                })
                .then(response => response.text())
                .then(data => {
                    alert(data); // Show success or error message
                    event.target.closest('tr').remove(); // Remove the row from the table
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        }
    });
});
// Add staff 
document.addEventListener('DOMContentLoaded', () => {
    const addStaffButton = document.getElementById('add-staff');
    const addStaffPopup = document.getElementById('add-staff-popup');
    const closeAddStaff = document.getElementById('close-add-staff');

    // Show the popup form when the Add Staff button is clicked
    addStaffButton.addEventListener('click', () => {
        addStaffPopup.style.display = 'block';
    });

    // Hide the popup form when the close button is clicked
    closeAddStaff.addEventListener('click', () => {
        addStaffPopup.style.display = 'none';
    });

    // Optional: Hide the popup when clicking outside of the form content
    window.addEventListener('click', (event) => {
        if (event.target == addStaffPopup) {
            addStaffPopup.style.display = 'none';
        }
    });

    document.getElementById('addstaff-form').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent the default form submission

        // Get form data
        const formData = new FormData(this);

        // Send form data to PHP script
        fetch('a_addstaff.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            alert(data); // Display response message (success or error)
            addStaffPopup.style.display = 'none'; // Close the popup after submission
            this.reset(); // Reset the form fields
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
});
