// therapist-dashboard.js

document.addEventListener("DOMContentLoaded", () => {
  const links = document.querySelectorAll(".left-section nav a");
  const sections = document.querySelectorAll(".right-section .content");
  const menuItems = document.querySelectorAll(".left-section ul li");

  links.forEach((link) => {
    link.addEventListener("click", (event) => {
      const targetId = link.getAttribute("data-target");

      // Check if the clicked link is the "Sign Out" link
      if (link.getAttribute("href") === "loginlanding.html") {
        // Allow default behavior for the "Sign Out" link
        return;
      }

      event.preventDefault();

      // Remove active class from all sections
      sections.forEach((section) => {
        section.classList.remove("active");
      });

      // Hide all menu items
      menuItems.forEach((item) => {
        item.classList.remove("active");
      });

      // Show the target section
      if (targetId) {
        document.getElementById(targetId).classList.add("active");
      }

      // Add active class to the clicked menu item
      link.parentElement.classList.add("active");
    });
  });

  /*-- NOTES SECTION --------------- ******************* --------- THIS IS A MARKER DO NOT REMOVE --*/
  openModal = () => {
    const modalContent = `
        <form id="notesForm" class="notesForm" action="add_notes.php" method="post">
                <div class="form-row">
                <div class="form-column-left">
                    <label for="patientSelect">Select Patient:</label>
                    <select id="patientSelect" name="patientID" required onclick="loadPatients()" onchange="loadServices()">
                        <option value="">Select a patient...</option> <!-- Default placeholder option -->
                    </select>

                    <label for="therapySelect">Select Service:</label>
                    <select id="therapySelect" name="serviceID" required>
                    </select>
                </div>
                    <div class="form-column-right">
                        <label for="sessionDate">Session Date:</label>
                        <input type="date" id="sessionDate" name="sessionDate" required>

                        <label for="sessionTime">Select Session Time:</label>
                        <select id="sessionTime" name="sessionTime">
                            <option value="">Select Time...</option>
                            <option value="9:00 AM">9:00 AM</option>
                            <option value="10:00 AM">10:00 AM</option>
                            <option value="11:00 AM">11:00 AM</option>
                            <option value="12:00 PM">12:00 PM</option>
                            <option value="1:00 PM">1:00 PM</option>
                            <option value="2:00 PM">2:00 PM</option>
                            <option value="3:00 PM">3:00 PM</option>
                            <option value="4:00 PM">4:00 PM</option>
                            <option value="5:00 PM">5:00 PM</option>
                            <option value="6:00 PM">6:00 PM</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="feedback">Feedback:
                        <a href="#" onclick="document.getElementById('feedbackImage').click();" style="text-decoration: underline; color: #432705; padding:5px; font-size:12px; border-radius:5px;">
                            <i class="fas fa-upload" style="margin-right: 5px;"></i> Attach Image
                        </a>
                    </label>
                    <div style="position: relative;">
                        <textarea id="feedback" name="feedback" required></textarea>
                        <div id="loadingIcon" class="loading-icon" style="display: none;"></div>
                    </div>
                    <input type="file" id="feedbackImage" accept="image/*" style="display: none;" onchange="extractTextFromImage()" />

                    <style>
                        .loading-icon {
                            position: absolute;
                            top: 40%;
                            left: 45%;
                            transform: translate(-50%, -50%);
                            border: 3px solid rgba(0, 0, 0, 0.2); /* Thinner border for smaller size */
                            border-top: 3px solid #432705; /* Thinner top border for color */
                            border-radius: 50%;
                            width: 20px; /* Smaller width */
                            height: 20px; /* Smaller height */
                            animation: spin 1s linear infinite;
                        }

                        @keyframes spin {
                            0% { transform: rotate(0deg); }
                            100% { transform: rotate(360deg); }
                        }
                    </style>
                </div>
                <button type="submit">Submit</button>
            </form>
        `;

    const sidebar = new SideViewBarEngine("ADD SESSION NOTES", modalContent);
    sidebar.render();
  };

  extractTextFromImage = async () => {
    const fileInput = document.getElementById("feedbackImage");
    if (fileInput.files.length === 0) return;

    const file = fileInput.files[0];
    const reader = new FileReader();
    const loadingIcon = document.getElementById("loadingIcon");
    const feedbackTextarea = document.getElementById("feedback");

    // Show loading icon
    loadingIcon.style.display = "block";

    reader.onload = async function (event) {
      const imageData = event.target.result;

      try {
        const result = await Tesseract.recognize(
          imageData,
          "eng", // Specify the language code
          {
            logger: (m) => console.log(m), // Optional: log progress
          }
        );

        const extractedText = result.data.text.trim(); // Get and trim extracted text

        if (extractedText) {
          // Insert the recognized text into the feedback textarea if text is found
          feedbackTextarea.value = extractedText;
        } else {
          // Display an error message if no text was found
          alert(
            "No text was found in the image. Please try a different image."
          );
        }
      } catch (error) {
        console.error("Error extracting text:", error);
        alert("An error occurred while processing the image");
      } finally {
        // Hide loading icon
        loadingIcon.style.display = "none";
      }
    };

    reader.readAsDataURL(file);
  };

  loadPatients = async () => {
    const patientSelect = document.getElementById("patientSelect");

    const today = new Date().toISOString().split("T")[0];
    document.getElementById("sessionDate").setAttribute("max", today);

    // Check if options are already loaded (beyond the default placeholder)
    if (patientSelect.options.length > 1) return;

    patientSelect.options[0].remove();

    console.log(patientSelect);

    try {
      const response = await fetch("notes_patient.php");
      const data = await response.json();

      data.forEach((patient) => {
        const option = document.createElement("option");
        option.value = patient.patientID;
        option.textContent = patient.patientName;
        patientSelect.appendChild(option);
      });

      await this.loadServices();
    } catch (error) {
      console.error("Error loading patient options:", error);
    }
  };

  loadServices = () => {
    const therapySelect = document.getElementById("therapySelect");
    const patientID = document.getElementById("patientSelect").value;

    // Only fetch options if a patient is selected
    if (!patientID) {
      therapySelect.innerHTML = ""; // Clear previous options
      return;
    }

    fetch(`notes_service.php?patientID=${patientID}`)
      .then((response) => response.text())
      .then((data) => {
        therapySelect.innerHTML = data; // Directly set fetched options to the select element
      })
      .catch((error) => console.error("Error loading service options:", error));
  };

  /*-- PROGRESS REPORT MARKER DO NOT REMOVE**--
        ----------------------------------------------------
        ----------------------------------------------------
    */

  /*****INIT FUNCTIONS DO NOT REMOVE ************************************* */
  fetchAccordionNotes();
  checkMessage();
});

const checkMessage = () => {
  const params = new URLSearchParams(window.location.search);

  if (params.has("message")) {
    const message = params.get("message"); // Get the message value

    const sanitizedMessage = message
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;");

    new MessagePopupEngine("INFORMATION", sanitizedMessage).instantiate();

    params.delete("message");

    const newUrl = params.toString()
      ? `${window.location.pathname}?${params.toString()}`
      : window.location.pathname;

    window.history.replaceState({}, "", newUrl);
  }
};

/** NOTES CALL BACK FUNCTION START */
fetchAccordionNotes = () => {
    const cardsList = document.querySelector("#patient-feedback");
    const datesContainer = document.querySelector("#patient-dates .therapist-feedback.list-items");
    const feedbackView = document.querySelector("#patient-feedback-view");
    const breadcrumb = document.querySelector("#breadcrumb");

    if (!cardsList || !datesContainer || !feedbackView || !breadcrumb) {
        throw new Error("Required containers are missing in the HTML.");
    }

    fetch("notes.php")
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (!Array.isArray(data)) {
                throw new Error("Fetched data is not an array.");
            }

            const groupedData = data.reduce((map, item) => {
                if (!map[item.patientID]) {
                    map[item.patientID] = { info: item, feedbacks: [] };
                }
                map[item.patientID].feedbacks.push(item);
                return map;
            }, {});

            Object.keys(groupedData).forEach(patientName => {
                const patient = groupedData[patientName].info;
                const card = document.createElement("div");
                card.className = "therapist-feedback card";
                card.dataset.patientName = patientName;
                card.innerHTML = `
                    <div class="therapist-feedback-card-content">
                        <div class="therapist-feedback-avatar">
                            <img src="images/${patient.image}" alt="Avatar">
                        </div>
                        <div class="therapist-feedback-info">
                            <span class="therapist-feedback-card-title">${patient.patient_name}</span>
                            <span class="therapist-feedback">ID: ${patient.patientID}</span>
                        </div>
                    </div>
                `;
                cardsList.appendChild(card);

                card.addEventListener("click", () => {

                    document.querySelectorAll(".therapist-feedback.card").forEach(c => {
                        c.classList.remove("selected");
                    });

                    card.classList.add("selected");
                    datesContainer.innerHTML = "";
                    breadcrumb.innerHTML = `Patient Feedback Notes » <span class="breadcrumb-item">${patientName}</span>`;

                    // Add feedback dates for the selected patient
                    groupedData[patientName].feedbacks.forEach(feedback => {
                        const listItem = document.createElement("li");
                        listItem.className = "therapist-feedback";
                        listItem.textContent = feedback.feedback_date;
                        listItem.dataset.feedback = feedback.feedback;
                        datesContainer.appendChild(listItem);

                        // Attach click listener to each date
                        listItem.addEventListener("click", () => {
                            document.querySelectorAll(".therapist-feedback.list-items li").forEach(li => {
                                li.classList.remove("selected");
                            });
                            listItem.classList.add("selected");

                            breadcrumb.innerHTML = `Patient Feedback Notes » <span class="breadcrumb-item">${patientName}</span> » <span class="breadcrumb-item">${feedback.feedback_date}</span>`;

                            feedbackView.innerHTML = `
                                <h4>Feedback Details</h4>
                                <p><strong>Date:</strong> ${feedback.feedback_date}</p>
                                <p><strong>Feedback:</strong> ${feedback.feedback}</p>
                                <p><strong>Service:</strong> ${feedback.service_name}</p>
                            `;
                        });
                    });
                });
            });
        })
        .catch(error => {
            console.error("Error fetching data:", error);
        });
};


/** NOTES CALL BACK FUNCTION END */

function filterSearch() {
  Object.entries(groupedByPatientName).forEach(([key, value]) => {
    const accordionTitle = `${key} (${value[0]["service_name"]})`;

    const accordion = new AccordionEngine(accordionTitle, "", value).render();
    accordionContainer.appendChild(accordion);
  });
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

function fetchFeedback(date, id) {
  const notesContainer = document.getElementById("notes-info");
  const notesDetails = document.getElementById("notes-details");
  const notesDate = document.getElementById("notes-date");

  // Format the date to match the format used in the table
  const formattedDate = new Date(date).toLocaleString("en-US", {
    year: "numeric",
    month: "long",
    day: "numeric",
  });

  // Toggle visibility based on the current state and clicked row
  if (
    notesContainer.style.display === "block" &&
    notesContainer.getAttribute("data-id") === id
  ) {
    notesContainer.style.display = "none";
    notesDetails.innerHTML = "<h5>Session Overview:</h5>";
    notesContainer.removeAttribute("data-id");
  } else {
    // Show the notes container and populate with feedback
    notesContainer.style.display = "block";
    notesDate.innerText = formattedDate; // Set the formatted date
    notesContainer.setAttribute("data-id", id);

    // Fetch feedback from PHP script
    fetch(`fetch_feedback.php?date=${date}`)
      .then((response) => response.json())
      .then((data) => {
        notesDetails.innerHTML = "<h5>Session Overview:</h5>"; // Reset content
        if (data.success) {
          notesDetails.innerHTML += `<p>${data.feedback}</p>`;
        } else {
          notesDetails.innerHTML += `<p>No feedback available for this date.</p>`;
        }
      })
      .catch((error) => {
        console.error("Error fetching feedback:", error);
        notesDetails.innerHTML += `<p>Error loading feedback.</p>`;
      });
  }
}

// therapist-dashboard.js
function displayGuestChecklist(guestID) {
  // Hide the guest table
  const prescreeningTable = document.getElementById("pre-screening-table");
  prescreeningTable.style.display = "none";

  // Fetch guest data using guestID
  fetchGuestData(guestID)
    .then((guestData) => {
      // Display guest information in the header
      document.getElementById("checklist-name").innerText =
        guestData.guest_name;
      document.getElementById("child-name").innerText =
        guestData.child_name || "";
      document.getElementById("child-age").innerText =
        guestData.child_age || "";

      // Show the checklist container
      document.querySelector(".checklist-container").style.display = "block";

      // Now fetch the checklist questions and answers
      fetchChecklist(guestID);
    })
    .catch((error) => console.error("Error fetching guest data:", error));
}

function fetchGuestData(guestID) {
  return fetch(`fetch_guest_data.php?guestID=${guestID}`)
    .then((response) => {
      if (!response.ok) {
        throw new Error("Network response was not ok");
      }
      return response.json(); // Parse JSON response
    })
    .then((data) => {
      return {
        guest_name: data.guest_name,
        child_name: data.child_name,
        child_age: data.child_age,
      };
    });
}

function fetchChecklist(guestID) {
  const checklistSection = document.querySelector(".checklist-left-section");
  checklistSection.innerHTML = "Loading checklist...";
  console.log("checklist");
  fetch(`fetch_checklist.php?guestID=${guestID}`)
    .then((response) => {
      if (!response.ok) {
        throw new Error("Network response was not ok");
      }
      return response.text();
    })
    .then((data) => {
      checklistSection.innerHTML = data; // Populate checklist with fetched data
    })
    .catch((error) => console.error("Error loading checklist:", error));
}

function displayGuestChecklistComplete(guestID) {
  // Hide the guest table
  const prescreeningTable = document.getElementById("pre-screening-table");
  prescreeningTable.style.display = "none";

  // Fetch guest data using guestID
  fetchGuestData(guestID)
    .then((guestData) => {
      // Display guest information in the header
      document.getElementById("checklist-name").innerText =
        guestData.guest_name;
      document.getElementById("child-name").innerText =
        guestData.child_name || "";
      document.getElementById("child-age").innerText =
        guestData.child_age || "";

      // Show the checklist container
      document.querySelector(".checklist-container").style.display = "block";

      // Now fetch the checklist questions and answers
      fetchChecklistComplete(guestID);
    })
    .catch((error) => console.error("Error fetching guest data:", error));
}

function fetchChecklistComplete(guestID) {
  const checklistSection = document.querySelector(".checklist-left-section");
  checklistSection.innerHTML = "Loading checklist...";

  fetch(`fetch_checklist.php?guestID=${guestID}`)
    .then((response) => {
      if (!response.ok) {
        throw new Error("Network response was not ok");
      }
      return response.text();
    })
    .then((data) => {
      checklistSection.innerHTML = data; // Populate checklist with fetched data
    })
    .catch((error) => console.error("Error loading checklist:", error));

  const checklistRightSection = document.querySelector(
    ".checklist-right-section"
  );
  document.querySelector(".asses").style.display = "none";

  fetch(`view_checklist.php?guestID=${guestID}`)
    .then((response) => {
      if (!response.ok) {
        throw new Error("Network response was not ok");
      }
      return response.text();
    })
    .then((data) => {
      checklistRightSection.innerHTML = data; // Populate checklist with fetched data
    })
    .catch((error) => console.error("Error loading checklist:", error));
}

function fetchProgress(id) {
  const progressContainer = document.getElementById("progress-info");
  const notesTextarea = document.getElementById("notesTextarea");
  const saveButton = progressContainer.querySelector(".saveprogress-button"); // Corrected class selector

  if (
    progressContainer.style.display === "block" &&
    progressContainer.getAttribute("data-id") === id
  ) {
    progressContainer.style.display = "none";
    progressContainer.removeAttribute("data-id");
  } else {
    progressContainer.style.display = "block";
    progressContainer.setAttribute("data-id", id);

    fetch(`fetch_report.php?reportID=${id}`)
      .then((response) => response.json()) // Parse JSON response
      .then((data) => {
        console.log("Fetched data:", data); // Debugging: check the fetched data

        // Populate the textarea with the fetched summary
        notesTextarea.value = data.summary;

        // Check the status and disable the textarea if 'verified'
        if (data.status === "verified") {
          notesTextarea.disabled = true;
          saveButton.style.display = "none"; // Hide Save button
        } else {
          notesTextarea.disabled = false;
          saveButton.style.display = "inline-block"; // Show Save button if not "verified"
        }
      })
      .catch((error) => console.error("Error fetching data:", error));
  }
}

function backLink() {
  // Hide the checklist container and show the table again
  document.querySelector(".checklist-container").style.display = "none";
  document.getElementById("pre-screening-table").style.display = "table"; // or 'block' if using a block layout
}

// Get logout modal element
const logoutModal = document.getElementById("logoutModal");
const logoutBtn = document.getElementById("logoutBtn");
const closeModal = document.getElementById("closeModal");
const confirmLogout = document.getElementById("confirmLogout");
const cancelLogout = document.getElementById("cancelLogout");

// Show the modal when logout button is clicked
logoutBtn.addEventListener("click", (event) => {
  event.preventDefault(); // Prevent the default action

  logoutModal.style.display = "block";
});

// Close the modal when the user clicks on <span> (x)
closeModal.addEventListener("click", () => {
  logoutModal.style.display = "none";
});

// Close the modal when the user clicks outside of the modal
window.addEventListener("click", (event) => {
  if (event.target === logoutModal) {
    logoutModal.style.display = "none";
  }
});

// Confirm logout
confirmLogout.addEventListener("click", () => {
  window.location.href = "t_logout.php"; // Redirect to logout script
});

// Cancel logout
cancelLogout.addEventListener("click", () => {
  logoutModal.style.display = "none"; // Hide the modal
});
