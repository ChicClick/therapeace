let globalTherapistFilter = new TherapistFilter();

document.addEventListener('DOMContentLoaded', () => {
    

    const links = document.querySelectorAll('.left-section nav a');
    const sections = document.querySelectorAll('.right-section .content');
    const menuItems = document.querySelectorAll('.left-section ul li');

    links.forEach(link => {
        link.addEventListener('click', (event) => {
            if (link.getAttribute('href') === 'registerlanding.php') {
                // Allow default behavior for the "Sign Out" link
                return; 
            }

            const targetId = link.getAttribute('data-target');

            // Check if the clicked link is the "Sign Out" link
            if (link.getAttribute('href') === 'adminlogin.php') {
                // Allow default behavior for the "Sign Out" link
                return; 
            }

            event.preventDefault();

            // Remove active class from all sections
            sections.forEach(section => {
                section.classList.remove('active');
            });

            // Hide all menu items
            menuItems.forEach(item => {
                item.classList.remove('active');
            });

            // Show the target section
            if (targetId) {
                document.getElementById(targetId).classList.add('active');
            }

            // Add active class to the clicked menu item
            link.parentElement.classList.add('active');
        });
    });
});
function filterSearch() {
    // Get the search input value
    const input = document.getElementById('searchInput').value.toLowerCase();
    
    // Get the table and its rows
    const table = document.getElementById('appointmentsTable');
    const rows = table.getElementsByTagName('tr');
    
    // Loop through table rows
    for (let i = 1; i < rows.length; i++) { // Start from 1 to skip the header row
        const cells = rows[i].getElementsByTagName('td');
        let found = false;
        
        // Loop through each cell in the row
        for (let j = 0; j < cells.length; j++) {
            if (cells[j].innerText.toLowerCase().includes(input)) {
                found = true;
                break;
            }
        }
        
        // Show or hide the row based on the search input
        rows[i].style.display = found ? '' : 'none';
    }
}

// Global variables to track the currently selected month and year
let selectedMonth = new Date().getMonth(); // Start from the current month (0-11)
let selectedYear = new Date().getFullYear(); // Start from the current year

// Open the calendar popup when "Add Appointment" is clicked
document.getElementById('add-appointment-button').addEventListener('click', openPopup);

function openPopup() {
    document.getElementById('appointment-popup-form').style.display = 'flex';
    // const calendar = new GenericCalendar("2024-11-11", "", "T001");
    // calendar.create();
}


// Function to open the appointment form popup
function openAppointmentPopup() {
    document.getElementById('appointment-popup-form').style.display = 'block';
}

// Event listener to close the appointment form popup when the close button is clicked
document.getElementById('close-popup').addEventListener('click', function() {
    document.getElementById('appointment-popup-form').style.display = 'none';
});


$(document).ready(function() {
    $('#patient-ID').change(function() {
        var patientId = $(this).val();
        console.log("Patient ID selected: " + patientId); // Debugging step

        // Check if a patient ID is selected
        if (patientId) {
            $.ajax({
                url: `a_fetch_patient_info.php?id=${patientId}`, // Endpoint to fetch patient details
                type: 'GET',
                success: function(response) {
                    try {
                        var patient = JSON.parse(response); // Parse the JSON response
                        console.log(patient);
                        // Update the fields with patient data
                        $('#patient-name').val(patient.patient_name);
                        $('#parentID').val(patient.parent_name);
                        $('#contact-number').val(patient.phone);
                    } catch (error) {
                        console.error("JSON parsing error:", error);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error("Error fetching patient data: " + textStatus, errorThrown);
                }
            });
        } else {
            // Clear the input fields if no patient is selected
            $('#patient-name').val('');
            $('#parentID').val('');
            $('#contact-number').val('');
        }
    });
      
});

// Fetching therapistName for autofill
$(document).ready(function() {
    $('#therapist').on('click', function() {
        // Only fetch data if the dropdown is empty
        if ($('#therapist option').length === 1) {
            $.ajax({
                url: 'a_fetch_therapist.php', // Path to your PHP file
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    // Clear existing options
                    $('#therapist').find('option:not(:first)').remove();
                    // Append new options
                    $.each(data, function(index, therapist) {
                        $('#therapist').append($('<option>', {
                            value: therapist.therapistID, // Use therapistID as the value
                            text: therapist.therapistName // Use therapistName as the display text
                        }));
                    });
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('Error fetching therapists:', textStatus, errorThrown);
                }
            });
        }
    });
});


// Patients Profile active row 
document.addEventListener("DOMContentLoaded", function() {
    const patientTable = document.getElementById('patients-tbody');
    const patientInfo = document.getElementById('patient-info'); // Reference to the patient info section
    let currentPatientId = null; // Track the current patient ID

    // Add click event listener to the patient table
    patientTable.addEventListener('click', function(e) {
        const clickedRow = e.target.closest('tr'); // Check if a row was clicked
        if (clickedRow) {
            const patientId = clickedRow.getAttribute('data-patient-id');
            console.log('Clicked patient ID:', patientId); // Debugging line

            if (currentPatientId === patientId) {
                // If the same patient is clicked again, hide the info
                patientInfo.style.display = 'none';
                currentPatientId = null; // Reset the current patient ID
            } else {
                // If a different patient is clicked, fetch and update the info
                fetchPatientInfo(patientId);
                currentPatientId = patientId; // Update the current patient ID
            }
        }
    });

    // Function to fetch and display patient info
    function fetchPatientInfo(patientId) {
        // Clear the current patient info
        patientInfo.style.display = 'none'; // Hide the info while updating
        clearPatientInfo();

        // Fetch the clicked patient's detailed information
        fetch(`a_fetch_patient_info.php?id=${patientId}`) // Corrected line
            .then(response => {
                if (!response.ok) { // Check for HTTP errors
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(patientData => {
                console.log('Fetched patient data:', patientData); // Debugging line
                if (patientData.error) {
                    console.error('Patient not found:', patientData.error);
                    alert('Patient not found'); // Notify user
                } else {
                    // Populate the patient info section
                    document.getElementById('patient_name').textContent = patientData.patient_name; // Ensure this matches your JSON keys
                    document.getElementById('service').textContent = patientData.service; // Ensure this key exists in your PHP
                    document.getElementById('parent_name').textContent = patientData.parent_name;
                    document.getElementById('phone').textContent = patientData.phone;
                    document.getElementById('email').textContent = patientData.email;
                    document.getElementById('address').textContent = patientData.address;
                    document.getElementById('birthday').textContent = patientData.birthday;
                    document.getElementById('gender').textContent = patientData.gender;

                    // Show the patient info section
                    patientInfo.style.display = 'block';
                }
            })
            .catch(err => console.error('Error fetching patient details:', err));
    }

    // Function to clear patient info
    function clearPatientInfo() {
        document.getElementById('patient_name').textContent = '';
        document.getElementById('service').textContent = '';
        document.getElementById('parent_name').textContent = '';
        document.getElementById('phone').textContent = '';
        document.getElementById('email').textContent = '';
        document.getElementById('address').textContent = '';
        document.getElementById('birthday').textContent = '';
        document.getElementById('gender').textContent = '';
    }
});




// Staff Section 
// Get references to the buttons and table container
function setActive(tableId) {
    // Hide all tables
    const allTables = document.querySelectorAll('.table-container');
    allTables.forEach(table => table.classList.add('hidden'));

    // Show the selected table
    const selectedTable = document.getElementById(tableId);
    selectedTable.classList.remove('hidden');
}

// Function to display data in the table
function displayTableData(data) {
    // Clear existing rows
    tableBody.innerHTML = '';

    // Loop through the data and create table rows
    data.forEach(item => {
        const row = document.createElement('tr');
        row.innerHTML = `<td>${item.name}</td><td>${item.position}</td><td>${item.dateHired}</td>`;
        tableBody.appendChild(row);
    });

    // Show the table container
    tableContainer.classList.remove('hidden');
}

// Event listeners for each button
clinicStaffBtn.addEventListener('click', () => {
    displayTableData(clinicStaffData);
});

clinicAdminBtn.addEventListener('click', () => {
    displayTableData(clinicAdminData);
});

clinicTherapistBtn.addEventListener('click', () => {
    displayTableData(clinicTherapistData);
});



//Service
//Add Service 
