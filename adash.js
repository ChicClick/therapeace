let globalTherapistFilter = new TherapistFilter();

document.addEventListener('DOMContentLoaded', () => {
    TableEngine.setGlobalType("admin");
    const links = document.querySelectorAll('.left-section nav a');
    const sections = document.querySelectorAll('.right-section .content');
    const menuItems = document.querySelectorAll('.left-section ul li');

    // Function to activate a section and corresponding menu item
    function activateSection(targetId) {
        // Remove active class from all sections
        sections.forEach(section => {
            section.classList.remove('active');
        });

        // Remove active class from all menu items
        menuItems.forEach(item => {
            item.classList.remove('active');
        });

        // Activate the section with the targetId
        const targetSection = document.getElementById(targetId);
        if (targetSection) {
            targetSection.classList.add('active');
        }

        // Activate the corresponding menu item
        links.forEach(link => {
            if (link.getAttribute('data-target') === targetId) {
                link.parentElement.classList.add('active');
            }
        });
    }

    // Check the URL for the 'active' parameter
    const urlParams = new URLSearchParams(window.location.search);
    const activeSection = urlParams.get('active');

    if (activeSection) {
        // Activate the section based on the 'active' parameter
        activateSection(activeSection);
        window.history.replaceState(null, '', window.location.pathname);
    } else {
        // Fallback: activate the first section by default
        if (sections.length > 0) {
            const defaultSection = sections[0].id;
            activateSection(defaultSection);
        }
    }

    // Handle menu link clicks
    links.forEach(link => {
        link.addEventListener('click', (event) => {
            if (link.getAttribute('href') === 'registerlanding.php' || link.getAttribute('href') === 'adminlogin.php') {
                // Allow default behavior for these links
                return;
            }

            event.preventDefault();

            const targetId = link.getAttribute('data-target');
            if (targetId) {
                activateSection(targetId);
            }
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
}


// Function to open the appointment form popup
function openAppointmentPopup() {
    document.getElementById('appointment-popup-form').style.display = 'block';
}

// Event listener to close the appointment form popup when the close button is clicked
document.getElementById('close-popup').addEventListener('click', function() {
    document.getElementById('appointment-popup-form').style.display = 'none';
});

document.getElementById('add-patient-button').addEventListener('click', function() {

    try {
        fetch("z_get_all_parents.php")
        .then(async response => await response.json())
        .then(data => {
            let parentOptions = `<option value="">Select Parent Name</option>`;
            data.forEach(parent => {
                parentOptions += `<option value="${parent.parentID}">${parent.parentName}</option>`;
            });

            const patientForm = `
            <form id="register-form" action="pRegister.php" method="POST" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group">
                        <label for="patientID">Patient ID:</label>
                        <input type="text" id="patientID" name="patientID" placeholder="Enter Patient ID" required>
                    </div>
                    <div class="form-group">
                        <label for="patientName">Patient Name:</label>
                        <input type="text" id="patientName" name="patientName" placeholder="Enter Patient Name" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="phone">Phone:</label>
                        <input type="text" id="phone" name="phone" placeholder="Enter Phone Number" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" placeholder="Enter Email" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="birthday">Birthday:</label>
                        <input type="date" id="birthday" name="birthday" required>
                    </div>
                    <div class="form-group">
                        <label for="address">Address:</label>
                        <input type="text" id="address" name="address" placeholder="Enter Address" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="gender">Sex:</label>
                        <select id="gender" name="gender" required>
                            <option value="Female">Female</option>
                            <option value="Male">Male</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="parentID">Parent Name:</label>
                        <select id="parentID" name="parentID" required>
                            ${parentOptions}
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="relationship">Relationship:</label>
                        <input type="text" id="relationship" name="relationship" placeholder="Enter Relationship" required>
                    </div>
                    <div class="form-group">
                        <label for="status">Status:</label>
                        <select id="status" name="status" required>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="image">Profile Picture:</label>
                        <input type="file" id="image" name="image" accept="image/*">
                    </div>
                    <div class="form-group">
                        <label for="password_hash">Password:</label>
                        <input type="password" id="password_hash" name="password" placeholder="Enter Password" required>
                    </div>
                </div>
                <div class="btn-container">
                    <button type="submit" class="submit-btn">Register</button>
                </div>
            </form>
            `;
        new SideViewBarEngine("NEW PATIENT REGISTRATION",patientForm,"view-lg").render();
        })
        .catch(e => console.error("Error fetching parents ", e));
    }
    catch{}
   
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
                success: async function(response) {
                    try {
                        var patient = await JSON.parse(response); // Parse the JSON response
                        console.log(patient);
                        // Update the fields with patient data
                        $('#patient-name').val(patient.patient_name);
                        $('#parentID').val(patient.parentID);
                        $('#parentID').attr('data-title', patient.parent_name);
                        $('#parentID').prop('readonly', true);
                        $('#patient-name').prop('readonly', true);
                        $('#contact-number').val(patient.phone);
                        $('#contact-number').prop('readonly', true);
                        $('#parentID').attr('title', $('#parentID').attr('data-title'));
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

$(document).ready(function () {
    // Populate the table actions section
    $('#table-actions').html(`
        <div>
            <label for="date-start">Start Date:</label>
            <input class="table-actions-date" type="date" id="date-start" name="date-start" value="">
        </div>

        <div>
            <label for="date-end">End Date:</label>
            <input class="table-actions-date" type="date" id="date-end" name="date-end" value="">
        </div>
 
        <div>
            <label for="min-rating">Minimum Rating:</label>
            <select class="table-actions-select" id="min-rating" name="min-rating">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
            </select>
        </div>
    `);

    $('#table-actions').on('change', 'input, select', function() {

        var startDate = $('#date-start').val();
        var endDate = $('#date-end').val();
        var minRating = $('#min-rating').val();

        var requestData = {
            date_start: startDate,
            date_end: endDate,
            minimum_rating: minRating
        };

        $.ajax({
            url: 'a_edit_feedbacks_settings.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(requestData),
            success: function(response) {
                console.log(response.message);
            },
            error: function(xhr, status, error) {
                console.error('Error: ' + error);
            }
        });
    });

    $.ajax({
        url: 'a_fetch_feedbacks_settings.php',
        method: 'GET',
        dataType: 'json',
        success: function (data) {
            console.log(data);
            $('#date-start').val(data[0].date_start);
            $('#date-end').val(data[0].date_end);
            $('#min-rating').val(data[0].minimum_rating);
        },
        error: function (xhr, status, error) {
            console.error('Error fetching feedback settings:', error);
        }
    });
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
