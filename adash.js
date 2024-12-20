let globalTherapistFilter = new TherapistFilter();

document.addEventListener("DOMContentLoaded", () => {
  TableEngine.setGlobalType("admin");
  const links = document.querySelectorAll(".left-section nav a");
  const sections = document.querySelectorAll(".right-section .content");
  const menuItems = document.querySelectorAll(".left-section ul li");

  // Function to activate a section and corresponding menu item
  function activateSection(targetId) {
    // Remove active class from all sections
    sections.forEach((section) => {
      section.classList.remove("active");
    });

    // Remove active class from all menu items
    menuItems.forEach((item) => {
      item.classList.remove("active");
    });

    // Activate the section with the targetId
    const targetSection = document.getElementById(targetId);
    if (targetSection) {
      targetSection.classList.add("active");
    }

    // Activate the corresponding menu item
    links.forEach((link) => {
      if (link.getAttribute("data-target") === targetId) {
        link.parentElement.classList.add("active");
      }
    });
  }

  // Check the URL for the 'active' parameter
  const urlParams = new URLSearchParams(window.location.search);
  const activeSection = urlParams.get("active");

  if (activeSection) {
    // Activate the section based on the 'active' parameter
    activateSection(activeSection);
    window.history.replaceState(null, "", window.location.pathname);
  } else {
    // Fallback: activate the first section by default
    if (sections.length > 0) {
      const defaultSection = sections[0].id;
      activateSection(defaultSection);
    }
  }

  // Handle menu link clicks
  links.forEach((link) => {
    link.addEventListener("click", (event) => {
      if (
        link.getAttribute("href") === "registerlanding.php" ||
        link.getAttribute("href") === "adminlogin.php"
      ) {
        // Allow default behavior for these links
        return;
      }

      event.preventDefault();

      const targetId = link.getAttribute("data-target");
      if (targetId) {
        activateSection(targetId);
      }
    });
  });
});

document.addEventListener("DOMContentLoaded", () => {
    const hamburgerMenu = document.getElementById('hamburgerMenu');
    const navbar = document.querySelector('.navbar');
    const navLinks = document.querySelectorAll('.navbar a'); // Select all navigation links inside the navbar
  
    // Add event listener to toggle the navbar visibility
    hamburgerMenu.addEventListener('click', function() {
        navbar.classList.toggle('active'); // Toggle the 'active' class on the navbar
    });
  
    // Add event listener to each nav link to close the navbar when clicked
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            navbar.classList.remove('active'); // Remove the 'active' class to close the navbar
        });
  });
});

function filterSearch() {
  // Get the search input value
  const input = document.getElementById("searchInput").value.toLowerCase();

  // Get the table and its rows
  const table = document.getElementById("appointmentsTable");
  const rows = table.getElementsByTagName("tr");

  // Loop through table rows
  for (let i = 1; i < rows.length; i++) {
    // Start from 1 to skip the header row
    const cells = rows[i].getElementsByTagName("td");
    let found = false;

    // Loop through each cell in the row
    for (let j = 0; j < cells.length; j++) {
      if (cells[j].innerText.toLowerCase().includes(input)) {
        found = true;
        break;
      }
    }

    // Show or hide the row based on the search input
    rows[i].style.display = found ? "" : "none";
  }
}

// update admin change password
document.addEventListener("DOMContentLoaded", () => {
  // Bind the changePassword function to a button or event
  const changePasswordButton = document.getElementById("changePasswordButton"); // Replace with your button's ID or relevant trigger
  if (changePasswordButton) {
    changePasswordButton.addEventListener("click", changePassword);
  }
});

const changePassword = async (e) => {
  e.preventDefault();
  const changePasswordForm = `
        <form id="changePasswordForm" action="admin_change_password.php" method="POST">
            <div class="form-group">
                <label for="oldPassword">Old Password</label>
                <input type="password" id="oldPassword" name="oldPassword" required>
            </div>

            <div class="form-group">
                <label for="newPassword">New Password</label>
                <input type="password" id="newPassword" name="newPassword" required>
                <ul id="passwordCriteria">
                    <li id="length" class="invalid">*Must be at least 8 characters long.</li>
                    <li id="lowercase" class="invalid">*Must contain a lowercase letter.</li>
                    <li id="uppercase" class="invalid">*Must contain an uppercase letter.</li>
                    <li id="number" class="invalid">*Must contain a number or special character.</li>
                </ul>
            </div>

            <div class="form-group">
                <label for="confirmPassword">Confirm New Password</label>
                <input type="password" id="confirmPassword" name="confirmPassword" required>
            </div>

            <div class="error-message" id="error-message"></div>

            <div class="form-group">
                <input type="submit" id="submitButton" value="Change Password" disabled>
            </div>
        </form>
    `;

  new SideViewBarEngine("Change Password", changePasswordForm).render();

  // Password validation logic
  const newPasswordInput = document.getElementById("newPassword");
  const confirmPasswordInput = document.getElementById("confirmPassword");
  const submitButton = document.getElementById("submitButton");
  const passwordCriteria = {
    length: false,
    lowercase: false,
    uppercase: false,
    number: false,
  };

  newPasswordInput.addEventListener("input", function () {
    const password = newPasswordInput.value;

    // Check length
    passwordCriteria.length = password.length >= 8;
    document.getElementById("length").className = passwordCriteria.length
      ? "valid"
      : "invalid";

    // Check for lowercase letter
    passwordCriteria.lowercase = /[a-z]/.test(password);
    document.getElementById("lowercase").className = passwordCriteria.lowercase
      ? "valid"
      : "invalid";

    // Check for uppercase letter
    passwordCriteria.uppercase = /[A-Z]/.test(password);
    document.getElementById("uppercase").className = passwordCriteria.uppercase
      ? "valid"
      : "invalid";

    // Check for number or special character
    passwordCriteria.number = /[0-9!@#$%^&*(),.?":{}|<>]/.test(password);
    document.getElementById("number").className = passwordCriteria.number
      ? "valid"
      : "invalid";

    // Enable/disable submit button based on validation
    toggleSubmitButton();
  });

  confirmPasswordInput.addEventListener("input", function () {
    // Enable/disable submit button based on validation
    toggleSubmitButton();
  });

  function toggleSubmitButton() {
    // Enable submit button if all criteria are met and passwords match
    if (
      passwordCriteria.length &&
      passwordCriteria.lowercase &&
      passwordCriteria.uppercase &&
      passwordCriteria.number &&
      newPasswordInput.value === confirmPasswordInput.value
    ) {
      submitButton.disabled = false;
    } else {
      submitButton.disabled = true;
    }
  }
};

// Global variables to track the currently selected month and year
let selectedMonth = new Date().getMonth(); // Start from the current month (0-11)
let selectedYear = new Date().getFullYear(); // Start from the current year

// Open the calendar popup when "Add Appointment" is clicked
document
  .getElementById("add-appointment-button")
  .addEventListener("click", openPopup);

function openPopup() {
  document.getElementById("appointment-popup-form").style.display = "flex";
}

// Function to open the appointment form popup
function openAppointmentPopup() {
  document.getElementById("appointment-popup-form").style.display = "block";
}

// Event listener to close the appointment form popup when the close button is clicked
document.getElementById("close-popup").addEventListener("click", function () {
  document.getElementById("appointment-popup-form").style.display = "none";
});

document
  .getElementById("add-patient-button")
  .addEventListener("click", function () {
    try {
      fetch("z_get_all_parents.php")
        .then(async (response) => await response.json())
        .then((data) => {
          let parentOptions = `<option value="">Select Parent Name</option>`;
          data.forEach((parent) => {
            parentOptions += `<option value="${parent.parentID}">${parent.parentName}</option>`;
          });

          const patientForm = `
            <form id="register-form" action="pRegister.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" id="guestID" name="guestID" value="0" />
                <div class="form-row">
                    <div class="form-group">
                        <label for="patientName">Patient Name: <span style="color: red;">*</span></label>
                        <input type="text" id="patientName" name="patientName" placeholder="Enter Patient Name" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone: <span style="color: red;">*</span></label>
                        <input type="tel" id="phone" name="phone" placeholder="09xxxxxxxxx" required minlength="11" maxlength="11" pattern="^[0-9]{11}$" title="Please enter an 11-digit contact number" oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="birthday">Birthday: <span style="color: red;">*</span></label>
                        <input type="date" id="birthday" name="birthday" required>
                    </div>
                    <div class="form-group">
                        <label for="address">Address: <span style="color: red;">*</span></label>
                        <input type="text" id="address" name="address" placeholder="Enter Address" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="gender">Sex: <span style="color: red;">*</span></label>
                        <select id="gender" name="gender" required>
                            <option value="Female">Female</option>
                            <option value="Male">Male</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="parentID">Parent Name: <span style="color: red;">*</span></label>
                        <select id="parentID" name="parentID" required>
                            ${parentOptions}
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="relationship">Relationship: <span style="color: red;">*</span></label>
                        <input type="text" id="relationship" name="relationship" placeholder="e.g., Mother, Father, Guardian" required>
                    </div>
                    <div class="form-group">
                        <label for="status">Status: <span style="color: red;">*</span></label>
                        <select id="status" name="status" required>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email: <span style="color: red;">*</span></label>
                        <input type="email" id="email" name="email" placeholder="Enter a valid email" pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$" 
                            title="Please enter a valid email address (e.g., yourname@gmail.com)">
                    </div>
                     <div class="form-group">
                        <label for="image">Profile Picture: <span style="color: red;">*</span></label>
                        <input type="file" id="image" name="image" accept="image/*">
                    </div>
                </div>
                <div id="form-error" style="color: red; display: none;">Error message here</div>
                <div class="btn-container">
                    <button id="admin-register-patient" type="button" class="submit-btn">Register</button>
                </div>
            </form>
            `;
          new SideViewBarEngine(
            "NEW PATIENT REGISTRATION",
            patientForm,
            "view-lg"
          ).render();
          setTimeout(() => {
            document
              .querySelector("#admin-register-patient")
              .addEventListener("click", async () => {
                const regForm = document.querySelector("#register-form");

                if (regForm.checkValidity()) {
                  const formData = new FormData(regForm);
                  document
                    .querySelector("#admin-register-patient")
                    .setAttribute("disabled", true);

                  try {
                    const response = await fetch(regForm.action, {
                      method: "POST",
                      body: formData,
                    });

                    if (!response.ok) {
                      const errorMsg = await response.text();
                      document.querySelector("#form-error").textContent =
                        errorMsg;
                      document.querySelector("#form-error").style.display =
                        "block";

                      Array.from(regForm.elements).forEach((element) => {
                        element.removeAttribute("disabled");
                      });
                    } else {
                      window.location.href = "admindashboard.php?active=patients-information-section";
                    }
                  } catch (error) {
                    console.error("An error occurred:", error);
                    alert("Unexpected error occurred.");
                  }
                } else {
                  regForm.reportValidity();
                }
              });
          });
        })
        .catch((e) => console.error("Error fetching parents ", e));
    } catch {}
  });

  document
  .getElementById("add-parent-button")
  .addEventListener("click", function () {
    const parentForm = `
            <form id="register-form" action="parentRegister.php" method="POST" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group">
                        <label for="parentName">Parent Name: <span style="color: red;">*</span></label>
                        <input type="text" id="parentName" name="parentName" placeholder="Enter Parent Name" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                      <label for="phone">Phone: <span style="color: red;">*</span></label>
                      <input type="tel" id="phone" name="contactno" placeholder="09xxxxxxxxx" required minlength="11" maxlength="11" pattern="^[0-9]{11}$" title="Please enter an 11-digit contact number" oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                    </div>
                </div>
                <div class="btn-container">
                    <button id="admin-register-parent" type="button" class="submit-btn">Register</button>
                </div>
            </form>
                `;
          
            new SideViewBarEngine(
              "NEW PARENT REGISTRATION",
              parentForm
            ).render();
            
          setTimeout(() => {
            document
              .querySelector("#admin-register-parent")
              .addEventListener("click", async () => {
                const regForm = document.querySelector("#register-form");

                if (regForm.checkValidity()) {
                  const formData = new FormData(regForm);
                  document
                    .querySelector("#admin-register-parent")
                    .setAttribute("disabled", true);

                  try {
                    const response = await fetch(regForm.action, {
                      method: "POST",
                      body: formData,
                    });

                    if (!response.ok) {
                      const errorMsg = await response.text();
                      document.querySelector("#form-error").textContent =
                        errorMsg;
                      document.querySelector("#form-error").style.display =
                        "block";

                      Array.from(regForm.elements).forEach((element) => {
                        element.removeAttribute("disabled");
                      });
                    } else {
                      window.location.href = "admindashboard.php?active=patients-information-section";
                    }
                  } catch (error) {
                    console.error("An error occurred:", error);
                    alert("Unexpected error occurred.");
                  }
                } else {
                  regForm.reportValidity();
                }
              });
          });
  });

$(document).ready(function () {
  $("#patient-ID").change(function () {
    var patientId = $(this).val();
    console.log("Patient ID selected: " + patientId); // Debugging step

    // Check if a patient ID is selected
    if (patientId) {
      $.ajax({
        url: `a_fetch_patient_info.php?id=${patientId}`, // Endpoint to fetch patient details
        type: "GET",
        success: async function (response) {
          try {
            var patient = await JSON.parse(response); // Parse the JSON response
            console.log(patient);
            // Update the fields with patient data
            $("#patient-name").val(patient.patient_name);
            $("#parentID").val(patient.parentID);
            $("#parentID").attr("data-title", patient.parent_name);
            $("#parentID").prop("readonly", true);
            $("#patient-name").prop("readonly", true);
            $("#contact-number").val(patient.phone);
            $("#contact-number").prop("readonly", true);
            $("#parentID").attr("title", $("#parentID").attr("data-title"));
          } catch (error) {
            console.error("JSON parsing error:", error);
          }
        },
        error: function (jqXHR, textStatus, errorThrown) {
          console.error(
            "Error fetching patient data: " + textStatus,
            errorThrown
          );
        },
      });
    } else {
      // Clear the input fields if no patient is selected
      $("#patient-name").val("");
      $("#parentID").val("");
      $("#contact-number").val("");
    }
  });
});

// Fetching therapistName for autofill
$(document).ready(function () {
  $("#therapist").on("click", function () {
    // Only fetch data if the dropdown is empty
    if ($("#therapist option").length === 1) {
      $.ajax({
        url: "a_fetch_therapist.php", // Path to your PHP file
        type: "GET",
        dataType: "json",
        success: function (data) {
          // Clear existing options
          $("#therapist").find("option:not(:first)").remove();
          // Append new options
          $.each(data, function (index, therapist) {
            $("#therapist").append(
              $("<option>", {
                value: therapist.therapistID, // Use therapistID as the value
                text: therapist.therapistName, // Use therapistName as the display text
              })
            );
          });
        },
        error: function (jqXHR, textStatus, errorThrown) {
          console.error("Error fetching therapists:", textStatus, errorThrown);
        },
      });
    }
  });
});

$(document).ready(function () {
  // Populate the table actions section
  $("#table-actions").html(`
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

  $("#table-actions").on("change", "input, select", function () {
    var startDate = $("#date-start").val();
    var endDate = $("#date-end").val();
    var minRating = $("#min-rating").val();

    var requestData = {
      date_start: startDate,
      date_end: endDate,
      minimum_rating: minRating,
    };

    $.ajax({
      url: "a_edit_feedbacks_settings.php",
      method: "POST",
      contentType: "application/json",
      data: JSON.stringify(requestData),
      success: function (response) {
        console.log(response.message);
      },
      error: function (xhr, status, error) {
        console.error("Error: " + error);
      },
    });
  });

  $.ajax({
    url: "a_fetch_feedbacks_settings.php",
    method: "GET",
    dataType: "json",
    success: function (data) {
      console.log(data);
      $("#date-start").val(data[0].date_start);
      $("#date-end").val(data[0].date_end);
      $("#min-rating").val(data[0].minimum_rating);
    },
    error: function (xhr, status, error) {
      console.error("Error fetching feedback settings:", error);
    },
  });
});

// Staff Section
// Get references to the buttons and table container
function setActive(tableId) {
  // Hide all tables
  const allTables = document.querySelectorAll(".table-container");
  allTables.forEach((table) => table.classList.add("hidden"));

  // Show the selected table
  const selectedTable = document.getElementById(tableId);
  selectedTable.classList.remove("hidden");
}

// Function to display data in the table
function displayTableData(data) {
  // Clear existing rows
  tableBody.innerHTML = "";

  // Loop through the data and create table rows
  data.forEach((item) => {
    const row = document.createElement("tr");
    row.innerHTML = `<td>${item.name}</td><td>${item.position}</td><td>${item.dateHired}</td>`;
    tableBody.appendChild(row);
  });

  // Show the table container
  tableContainer.classList.remove("hidden");
}

// for incrementing therapistID
document.addEventListener("DOMContentLoaded", function () {
  // Fetch next therapistID on page load
  fetch("a_incrementTherapistID.php")
    .then((response) => response.json())
    .then((data) => {
      if (data.therapistID) {
        document.getElementById("therapistID").value = data.therapistID;
      }
    })
    .catch((error) => console.error("Error fetching therapist ID:", error));
});
