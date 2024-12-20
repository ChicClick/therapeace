let type = "";

document.addEventListener('DOMContentLoaded', () => {
    const addServiceButton = document.getElementById('add-service');
    const addServicePopup = document.getElementById('add-service-popup');
    const closeAddPopup = document.getElementById('close-add-popup');

    // Show the popup form when the Add Service button is clicked
    addServiceButton.addEventListener('click', () => {
        addServicePopup.style.display = 'flex';
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
            addServicePopup.style.display = 'none';
            this.reset();
            window.location.href = "admindashboard.php?active=services-section";
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
});
// Add staff 
document.addEventListener('DOMContentLoaded', () => {
    const addStaffButton = document.getElementById('add-staff');
    const addStaffPopup = document.getElementById('add-staff-popup');
    const addTherapistPopup = document.getElementById('add-therapist-popup');
    const closeAddStaff = document.getElementById('close-add-staff');
    const closeAddTherapist = document.getElementById('close-add-therapist');

    staff = () => {
        const dropdownMenu = document.querySelector(".dropdown-menu");
        
        type = "staff";
        addTherapistPopup.style.display = "none";
        addStaffPopup.style.display = "flex";
        addStaffButton.removeChild(dropdownMenu);
    }

    therapist = () => {
        const dropdownMenu = document.querySelector(".dropdown-menu");

        type = "therapist";
        addTherapistPopup.style.display = "block";
        addStaffPopup.style.display = "none";
        addStaffButton.removeChild(dropdownMenu);
    }

    addStaffButton.addEventListener('click', (e) => {
        
        const existingDropdown = addStaffButton.querySelector('.dropdown-menu');
        if (existingDropdown) {
            existingDropdown.classList.toggle('show');
            return;
        }
        
        const dropdownMenu = document.createElement('div');
        dropdownMenu.classList.add('dropdown-menu');
        
        const staffOption = document.createElement('div');
        staffOption.classList.add('dropdown-item');
        staffOption.textContent = "Staff";
        staffOption.addEventListener('click', () => {

            type = "staff";
            addTherapistPopup.style.display = "none";
            addStaffPopup.style.display = "flex";
            addStaffButton.removeChild(dropdownMenu);
        });
    
        const therapistOption = document.createElement('div');
        therapistOption.classList.add('dropdown-item');
        therapistOption.textContent = "Therapist";
        therapistOption.addEventListener('click', () => {

            type = "therapist";
            addTherapistPopup.style.display = "flex";
            addStaffPopup.style.display = "none";
            addStaffButton.removeChild(dropdownMenu);
        });
        
        dropdownMenu.appendChild(staffOption);
        dropdownMenu.appendChild(therapistOption);

        dropdownMenu.classList.toggle(".show");

        addStaffButton.appendChild(dropdownMenu);

        document.addEventListener('click', function(event) {
            if (!addStaffButton.contains(event.target)) {
                if (dropdownMenu.classList.contains('show')) {
                    dropdownMenu.classList.remove('show');
                }
            }
        });
    });
    
    // Hide the popup form when the close button is clicked
    closeAddStaff.addEventListener('click', () => {
        addStaffPopup.style.display = 'none';
    });

    closeAddTherapist.addEventListener('click', () => {
        addTherapistPopup.style.display = "none";
    });

    // Optional: Hide the popup when clicking outside of the form content
    window.addEventListener('click', (event) => {
        if (event.target == addStaffPopup) {
            addStaffPopup.style.display = 'none';
        }
    });

    document.getElementById('addstaff-form').addEventListener('submit', function(event) {
        event.preventDefault();
        
        const formData = new FormData(this);

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

    document.getElementById('addtherapist-form').addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(this);
        
        const submitButton = this.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.textContent = 'Submitting...';
        
        Array.from(this.elements).forEach(element => element.disabled = true);
    
        fetch('tRegister.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            let message = new MessagePopupEngine("Success!", data);
            message.instantiate();
            addTherapistPopup.style.display = 'none';
            this.reset();
        })
        .catch(error => {
            console.error('Error:', error);
        })
        .finally(() => {
            // re-enabl
            submitButton.disabled = false;
            submitButton.textContent = 'Submit';
            Array.from(this.elements).forEach(element => element.disabled = false);
        });
    });
    
});

document.addEventListener('DOMContentLoaded', () => {
    const addPatientButton = document.getElementById('add-patients');

    addPatientButton.addEventListener('click', (e) => {
        
        const existingDropdown = addPatientButton.querySelector('.dropdown-menu-patient');
        if (existingDropdown) {
            existingDropdown.classList.toggle('show');
            return;
        }
        
        const dropdownMenu = document.createElement('div');
        dropdownMenu.classList.add('dropdown-menu-patient');
        
        const patientOption = document.createElement('div');
        patientOption.classList.add('dropdown-item-patient');
        patientOption.textContent = "Patient";
        patientOption.addEventListener('click', () => {

            type = "patient";
            addStaffButton.removeChild(dropdownMenu);
        });
    
        const parentOption = document.createElement('div');
        parentOption.classList.add('dropdown-item-patient');
        parentOption.textContent = "Parent";
        parentOption.addEventListener('click', () => {

            type = "parent";
            addStaffButton.removeChild(dropdownMenu);
        });
        
        dropdownMenu.appendChild(patientOption);
        dropdownMenu.appendChild(parentOption);

        dropdownMenu.classList.toggle(".show");

        addPatientButton.appendChild(dropdownMenu);

        document.addEventListener('click', function(event) {
            if (!addPatientButton.contains(event.target)) {
                if (dropdownMenu.classList.contains('show')) {
                    dropdownMenu.classList.remove('show');
                }
            }
        });
    });
});