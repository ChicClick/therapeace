document.addEventListener("DOMContentLoaded", function() {
    // Get the button that opens the popup
    const editServiceButton = document.querySelector('.edit-service');
    // Get the popup
    const popup = document.getElementById('service-popup');
    // Get the <span> element that closes the popup
    const closePopupButton = document.getElementById('close-edit-service-popup');
    const editserviceForm = document.getElementById('editservice-form');

    let currentServiceData = {}; // To store the current service data when a row is clicked

    // When the user clicks on <span> (x), close the popup
    closePopupButton.addEventListener('click', function() {
        popup.style.display = 'none';
    });

    // When the user clicks anywhere outside of the popup content, close the popup
    window.addEventListener('click', function(event) {
        if (event.target === popup) {
            popup.style.display = 'none';
        }
    });

    // Select all service rows
    const serviceRows = document.querySelectorAll('.service-row');

    // Add a click event listener to each row
    serviceRows.forEach(row => {
        row.addEventListener('click', function() {
            // Fetch data from the clicked row
            currentServiceData = {
                serviceName: this.getAttribute('data-service-name'),
                serviceAvailability: this.getAttribute('data-service-availability'),
                serviceDescription: this.getAttribute('data-service-description'),
                servicePrice: this.getAttribute('data-service-price'),
                serviceAbout: this.getAttribute('data-service-about')
            };

            // Populate the service info section (to display service info before editing)
            document.getElementById('service-name').innerText = currentServiceData.serviceName;
            document.getElementById('service-description').innerText = currentServiceData.serviceDescription;
            document.getElementById('service-price').innerText = currentServiceData.servicePrice;
            document.getElementById('service-about').innerText = currentServiceData.serviceAbout;

            // Display the service info section
            document.getElementById('service-info').style.display = 'block';
        });
    });

    // When the user clicks the Edit button, open the popup with pre-filled data
    editServiceButton.addEventListener('click', function() {
        // Populate the form fields inside the popup for editing
        editserviceForm.querySelector('#service-name').value = currentServiceData.serviceName;
        editserviceForm.querySelector('#availability').value = currentServiceData.serviceAvailability;
        editserviceForm.querySelector('#description').value = currentServiceData.serviceDescription;
        editserviceForm.querySelector('#about').value = currentServiceData.serviceAbout;
        editserviceForm.querySelector('#price').value = currentServiceData.servicePrice;

        // Show the popup
        popup.style.display = 'block';
    });

    // Handle form submission for editing the service
    editserviceForm.addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent the form from submitting the traditional way

        // Gather form data
        const formData = new FormData(editserviceForm);

        // Send data to the server using AJAX
        fetch('a_editservice_endpoint.php', { // Replace with your actual server endpoint
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Service updated successfully!');
                popup.style.display = 'none'; // Close the popup
                // Optionally, refresh or update the service list on the page
            } else {
                alert('Error updating service: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('There was an error with the request.');
        });
    });
});
