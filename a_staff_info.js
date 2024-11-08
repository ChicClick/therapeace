document.addEventListener("DOMContentLoaded", function() {
    const staffTable = document.getElementById('staff-tbody');
    const staffInfo = document.getElementById('staff-info');
    let currentStaffId = null;

    staffTable.addEventListener('click', function(e) {
        const clickedRow = e.target.closest('tr');
        if (clickedRow) {
            const staffId = clickedRow.getAttribute('data-staff-id');
            console.log('Clicked staff ID:', staffId); // Debugging line

            if (currentStaffId === staffId) {
                staffInfo.style.display = 'none';
                currentStaffId = null;
            } else {
                fetchStaffInfo(staffId);
                currentStaffId = staffId;
            }
        }
    });

    // Fetch staff info function
    function fetchStaffInfo(staffId) {
        staffInfo.style.display = 'none';
        clearStaffInfo();

        fetch(`a_fetch_staff_info.php?id=${staffId}`)
            .then(response => {
                if (!response.ok) { // Check for HTTP errors
                    throw new Error('Network response was not ok');
                }
                return response.json(); // Only call response.json() once
            })
            .then(staffData => {
                console.log('Fetched staff data:', staffData); // Debugging line
                if (staffData.error) {
                    console.error('Staff not found:', staffData.error);
                    alert('Staff not found'); // Notify user
                } else {
                    document.getElementById('staff_name').textContent = staffData.staff_name;
                    document.getElementById('staff-position').textContent = staffData.position;
                    document.getElementById('staff-phone').textContent = staffData.phone;
                    document.getElementById('staff-datehired').textContent = staffData.datehired;
                    document.getElementById('staff-gender').textContent = staffData.gender;
                    document.getElementById('staff-address').textContent = staffData.address;

                    staffInfo.style.display = 'block';
                }
            })
            .catch(err => console.error('Error fetching staff details:', err));
    }

    function clearStaffInfo() {
        document.getElementById('staff_name').textContent = '';
        document.getElementById('position').textContent = '';
        document.getElementById('phone').textContent = '';
        document.getElementById('datehired').textContent = '';
        document.getElementById('gender').textContent = '';
        document.getElementById('address').textContent = '';
    }
});
