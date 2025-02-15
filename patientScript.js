document.addEventListener('DOMContentLoaded', function() {
    
    // Get dropdown button and container
    const dropbtn = document.querySelector('.dropbtn');
    const dropdown = document.querySelector('.dropdown');

    previewProfilePicture = (event) => {
        const file = event.target.files[0];
        if (file) {
          const reader = new FileReader();
          reader.onload = function (e) {
            const profilePreview = document.getElementById('edit-pic');
            profilePreview.src = e.target.result;
          };
          reader.readAsDataURL(file);
        }
      }

    openFeedbackForm = () => {
  
        const feedbackFormContent = `
            <form id="feedbackForm" class="feedbackForm" action="submit_feedback.php" method="post">
                <div class="intro">
                    <p>We value your feedback! Please take a moment to share your thoughts about our service. 
                    Your input helps us improve and ensure a better experience for everyone.</p>
                </div>
                <div class="form-row">
                    <div class="form-column-right">
                        <label for="rating">Rating:</label>
                        <select id="rating" name="rating" required>
                            <option value="">Select Rating</option>
                            <option value="1">1 - Poor</option>
                            <option value="2">2 - Fair</option>
                            <option value="3">3 - Good</option>
                            <option value="4">4 - Very Good</option>
                            <option value="5">5 - Excellent</option>
                        </select>
                    </div>
                    <div class="form-column-right">
                        <label for="feedback">Feedback:</label>
                        <textarea id="feedback" name="feedback_text" required></textarea>
                    </div>
                </div>
                <div class="form-row">
                    <label>
                        <input type="checkbox" id="consent" name="consent">
                        I consent to having my feedback displayed publicly (anonymous).
                    </label>
                </div>
                <button type="submit">Submit</button>
            </form>
        `;
        
        const sidebar = new SideViewBarEngine("LEAVE FEEDBACK", feedbackFormContent);
        sidebar.render();
    };

    // Toggle dropdown on button click
    dropbtn.addEventListener('click', () => dropdown.classList.toggle('show'));

    // Close dropdown if clicking outside
    window.onclick = function(event) {
        if (!event.target.matches('.dropbtn') && dropdown.classList.contains('show')) {
            dropdown.classList.remove('show');
        }
    };

    changePassword = async (e) => {
        e.preventDefault();
        const changePasswordForm = `
            <form id="changePasswordForm" action="patient_change_password.php" method="POST">
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
        const newPasswordInput = document.getElementById('newPassword');
        const confirmPasswordInput = document.getElementById('confirmPassword');
        const submitButton = document.getElementById('submitButton');
        const passwordCriteria = {
            length: false,
            lowercase: false,
            uppercase: false,
            number: false
        };
    
        newPasswordInput.addEventListener('input', function() {
            const password = newPasswordInput.value;
    
            // Check length
            passwordCriteria.length = password.length >= 8;
            document.getElementById('length').className = passwordCriteria.length ? 'valid' : 'invalid';
    
            // Check for lowercase letter
            passwordCriteria.lowercase = /[a-z]/.test(password);
            document.getElementById('lowercase').className = passwordCriteria.lowercase ? 'valid' : 'invalid';
    
            // Check for uppercase letter
            passwordCriteria.uppercase = /[A-Z]/.test(password);
            document.getElementById('uppercase').className = passwordCriteria.uppercase ? 'valid' : 'invalid';
    
            // Check for number or special character
            passwordCriteria.number = /[0-9!@#$%^&*(),.?":{}|<>]/.test(password);
            document.getElementById('number').className = passwordCriteria.number ? 'valid' : 'invalid';
    
            // Enable/disable submit button based on validation
            toggleSubmitButton();
        });
    
        confirmPasswordInput.addEventListener('input', function() {
            // Enable/disable submit button based on validation
            toggleSubmitButton();
        });
    
        function toggleSubmitButton() {
            // Enable submit button if all criteria are met and passwords match
            if (passwordCriteria.length && passwordCriteria.lowercase && passwordCriteria.uppercase && passwordCriteria.number && newPasswordInput.value === confirmPasswordInput.value) {
                submitButton.disabled = false;
            } else {
                submitButton.disabled = true;
            }
        }
    };

});


editProfileSave = async (e) => {
    e.preventDefault();
    const formData = document.querySelector("#edit-profile-form");

    if(!formData.checkValidity()) {
        return;
    }

    const formValues = new FormData(formData);

    formValues.forEach((value, key) => {
        console.log(`${key}: ${value}`);
    });
    
    try {
        const response = await fetch("patient_profile_functions.php", {
            method: "POST",
            body: formValues,
        });

        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        new MessagePopupEngine("Success!", "Profile Update Successfully!").instantiate();
    } catch (err) {
        console.error("Error occurred:", err);
    }
}

fetchReport = async () => {
    const progressReportCardModalContent = document.querySelector('.progress-report-card-modal-container');
    progressReportCardModalContent.innerHTML = "";
    try{
        await fetch('patientFetchReport.php')
        .then(res =>  res.json())
        .then(data => {
            data.reports.forEach(report => {
                const reportCreationDate = new Date(report.updated_at);
                const currentDate = new Date();
                const interval = currentDate - reportCreationDate;
                const isReportAvailable= report.status != 'pending' && report.pdf_path;
                const action = isReportAvailable ? `
                     <p><a href="${report.pdf_path}" download>Download Report from ${report.therapistName}</a></p>
                ` : `
                    <p>Report is not available.</p>
                `;
                const card = `
                <div class="report-item">
                    <p><strong>Report ID:</strong>${report.reportID}</p>
                    <p><strong>Therapist:</strong>${report.therapistName}></p>
                    <p><strong>Status:</strong>${report.status}</p>
                    <p><strong>Updated At:</strong>${report.updated_at}</p>
    
                    ${action}
                    <hr>
                </div>`;
    
                progressReportCardModalContent.innerHTML += card;
            });

            return data.isReportAvailable;
        })
        .then(isReportAvailable => {
            if (isReportAvailable) {
                document.getElementById('progress-report-popup').style.display = 'block';
            } else {
                alert('No report available for this patient.');
            }
        })
        .catch(e => console.log(e))
    }
    catch (e){
        console.error("patientFetchReport.php Error:", e);
    }
}

function submitReportRequest() {
    const therapistID = document.getElementById('therapistSelect').value;

    // Submit report request to server
    fetch('patient_submit_report.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            'therapistID': therapistID
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message); // Display success message
            closeReportRequestModal(); // Automatically close modal on success
        } else {
            alert(data.error); // Display error message
        }
    })
    .catch(error => console.error('Error submitting report request:', error));
}

function generateReportButton() {
    document.getElementById('reportRequestModal').style.display = 'block';

    // Fetch the therapists for the logged-in patient
    fetch('patient_get_notes.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            'fetchTherapists': true // Indicate we want to fetch therapists
        })
    })
    .then(response => {
        // Log the raw response for debugging
        console.log('Response:', response);
        return response.json();
    })
    .then(data => {
        console.log('Data:', data);
        console.log('Therapists:', data.therapists);
        if (data.success) {
            const therapistSelect = document.getElementById('therapistSelect');
            therapistSelect.innerHTML = ''; // Clear existing options
            data.therapists.forEach(therapist => {
                console.log('Therapist:', therapist); // Log the therapist object
                const option = document.createElement('option');
                option.value = therapist.therapistID; // Use therapist ID for the value
                option.textContent = therapist.therapistName; // Display therapist name
                therapistSelect.appendChild(option);
            });
    
            // If no therapists are found, you might want to inform the user
            if (data.therapists.length === 0) {
                alert('No therapists found for your sessions.');
            }
        } else {
            alert(data.error); // Alert the error message
        }
    })
    
    .catch(error => console.error('Error fetching therapists:', error));
}

function closeReportRequestModal() {
    console.log('Close button clicked');
    document.getElementById('reportRequestModal').style.display = 'none';
}

        // Get logout modal element
        const logoutModal = document.getElementById('logoutModal');
        const logoutBtn = document.getElementById('logoutBtn');
        const closeModal = document.getElementById('closeModal');
        const confirmLogout = document.getElementById('confirmLogout');
        const cancelLogout = document.getElementById('cancelLogout');

        // Show the modal when logout button is clicked
        logoutBtn.addEventListener('click', (event) => {
            event.preventDefault(); // Prevent the default action
            logoutModal.style.display = 'block'; // Show the modal
        });

        // Close the modal when the user clicks on <span> (x)
        closeModal.addEventListener('click', () => {
            logoutModal.style.display = 'none';
        });

        // Close the modal when the user clicks outside of the modal
        window.addEventListener('click', (event) => {
            if (event.target === logoutModal) {
                logoutModal.style.display = 'none';
            }
        });

        // Confirm logout
        confirmLogout.addEventListener('click', () => {
            window.location.href = 'logout.php'; // Redirect to logout script
        });

        // Cancel logout
        cancelLogout.addEventListener('click', () => {
            logoutModal.style.display = 'none'; // Hide the modal
        });

    // Wait for the window to load fully
    window.addEventListener('load', () => {
        document.body.classList.add('loaded'); // Add the class to fade in
    });

    const scrollTopBtn = document.querySelector('.scroll-top');

    window.addEventListener('scroll', () => {
        if (window.scrollY > 300) {
            scrollTopBtn.style.display = 'block';
        } else {
            scrollTopBtn.style.display = 'none';
        }
    });

    if (scrollTopBtn) {
        scrollTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    const navbar = document.querySelector('nav'); // Get the navbar

    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) { // Change the value to adjust when the color change occurs
            navbar.classList.add('scrolled'); // Add class to navbar
        } else {
            navbar.classList.remove('scrolled'); // Remove class when not scrolled
        }

        if (window.scrollY > 300) {
            scrollTopBtn.style.display = 'block';
        } else {
            scrollTopBtn.style.display = 'none';
        }
    });

    function searchAppointments() {

        const input = document.getElementById("appointmentsSearch").value.toLowerCase();

        const tableEngine = document.querySelector("#table-patient-appointments");
        let tableData = tableEngine.data; 
        let tableKeys = Object.keys(tableEngine.data[0]); 
    
        let filteredData = tableData.filter((row) => {
            return tableKeys.some(key => {
              return String(row[key]).toLowerCase().includes(input.toLowerCase());
            });
          });
        
        tableEngine.filter = filteredData;

        if(!input) {
            tableEngine.filter = [];
        }
        
        tableEngine.render();
    }
    
    function searchNotes() {
        const input = document.getElementById("notesSearch").value.toLowerCase();

        const tableEngine = document.querySelector("#table-patient-notes");
        let tableData = tableEngine.data; 
        let tableKeys = Object.keys(tableEngine.data[0]); 
    
        let filteredData = tableData.filter((row) => {
            return tableKeys.some(key => {
              return String(row[key]).toLowerCase().includes(input.toLowerCase());
            });
          });
        
        tableEngine.filter = filteredData;

        if(!input) {
            tableEngine.filter = [];
        }
        
        tableEngine.render();
    }
    

    async function openProgressReportPopup() {
        await fetchReport();
    }
    
    function closePopup() {
        document.getElementById('progress-report-popup').style.display = 'none';
    }
    

    // JavaScript to toggle edit mode
    function toggleEditProfile() {
        const editSection = document.getElementById('edit-profile-section');
        const profileSection = document.getElementById('profile-section');
        const editButton = document.getElementById('edit-button');

        // Toggle the visibility of the sections
        editSection.style.display = (editSection.style.display === 'none' || editSection.style.display === '') ? 'block' : 'none';
        profileSection.style.display = (profileSection.style.display === 'none' || profileSection.style.display === '') ? 'none' : 'none';

        // Change the button text to Save Changes when in edit mode
        editButton.textContent = (editButton.textContent === 'Edit Profile') ? 'Save Changes' : 'Edit Profile';
    }

    

    let currentIndex = 0;

    function moveCarousel(direction) {
        const carousel = document.querySelector('.feedback-carousel');
        const items = document.querySelectorAll('.feedback-item'); // Dynamically fetch items
        const totalItems = items.length;
    
        if (totalItems === 0) return; // Exit if no feedback items
    
        if (direction === 'left') {
            currentIndex = (currentIndex - 1 + totalItems) % totalItems;
        } else if (direction === 'right') {
            currentIndex = (currentIndex + 1) % totalItems;
        }
    
        // Calculate the new offset
        const offset = -currentIndex * 100; // Assuming each item takes up 100% width
        carousel.style.transform = `translateX(${offset}%)`;
    }
    
    // Automatic sliding (optional)
    setInterval(() => moveCarousel('right'), 5000);
    


// Create an IntersectionObserver to observe when images come into view
const observer = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
        // Check if the image is in the viewport (intersecting)
        if (entry.isIntersecting) {
            entry.target.classList.add('visible'); // Add the 'visible' class to animate
            observer.unobserve(entry.target); // Stop observing once it's in view
        }
    });
}, {
    threshold: 0.5 // Trigger the observer when at least 50% of the image is visible
});

// Observe each image in the "about-image" class
document.querySelectorAll('.about-image').forEach(image => {
    observer.observe(image);
});
