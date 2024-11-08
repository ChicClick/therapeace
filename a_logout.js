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
    window.location.href = 'a_logout.php'; // Redirect to logout script
});

// Cancel logout
cancelLogout.addEventListener('click', () => {
    logoutModal.style.display = 'none'; // Hide the modal
});