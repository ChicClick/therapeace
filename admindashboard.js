//therapist reg
function toggleCustomTime() {
    const select = document.getElementById('time-availability');
    const customTimeOptions = document.getElementById('custom-time-options');

    if (select.value === '[]') {
        customTimeOptions.style.display = 'block';
    } else {
        customTimeOptions.style.display = 'none';
    }
}

function updateCustomTime() {
    const checkboxes = document.querySelectorAll('#custom-time-options input[type="checkbox"]');
    const customOption = document.getElementById('custom-time');
    const selectedTimes = [];

    checkboxes.forEach(checkbox => {
        if (checkbox.checked) {
            selectedTimes.push(parseInt(checkbox.value));
        }
    });

    customOption.value = JSON.stringify(selectedTimes);
}

function updateCommunication() {
    var selectedMethods = [];
    document.querySelectorAll("input[name='communication[]']:checked").forEach(function(checkbox) {
        selectedMethods.push(checkbox.value);
    });
    console.log("Selected Communication Methods:", selectedMethods);
}

function updateFlexibility() {
    var selectedFlexibilities = [];
    document.querySelectorAll("input[name='flexibility[]']:checked").forEach(function(checkbox) {
        selectedFlexibilities.push(checkbox.value);
    });
    console.log("Selected Communication Methods:", selectedFlexibilities);
}

function toggleCustomDay() {
    const select = document.getElementById('day-availability');
    const customDayOptions = document.getElementById('custom-day-options');

    if (select.value === '[]') {
        customDayOptions.style.display = 'block';
    } else {
        customDayOptions.style.display = 'none';
    }
}

function updateCustomDay() {
    const checkboxes = document.querySelectorAll('#custom-day-options input[type="checkbox"]');
    const customOption = document.getElementById('custom-day');
    const selectedDays = [];

    checkboxes.forEach(checkbox => {
        if (checkbox.checked) {
            selectedDays.push(parseInt(checkbox.value));
        }
    });

    customOption.value = JSON.stringify(selectedDays);
}

function logMe() {
    const form = document.querySelector("#addstaff-form");
    const formData = new FormData(form);
    const formValues = Object.fromEntries(formData.entries());
}

//appointment
function toggleCustomAppointmentTime() {
    const select = document.getElementById('time-appointment-availability');
    const customTimeOptions = document.getElementById('custom-appointment-time-options');

    if (select.value === '[]') {
        customTimeOptions.style.display = 'block';
    } else {
        customTimeOptions.style.display = 'none';
    }
}

function updateAppointCustomTime  () {
    const checkboxes = document.querySelectorAll('#custom-appointment-time-options input[type="checkbox"]');
    const customOption = document.getElementById('custom-appointment-time');
    const selectedTimes = [];

    checkboxes.forEach(checkbox => {
        if (checkbox.checked) {
            selectedTimes.push(parseInt(checkbox.value));
        }
    });

    customOption.value = JSON.stringify(selectedTimes);
    console.log(customOption.value);
}

function toggleAppointmentCustomDay() {
    const select = document.getElementById('day-appointment-availability');
    const customDayOptions = document.getElementById('custom-appointment-day-options');

    if (select.value === '[]') {
        customDayOptions.style.display = 'block';
    } else {
        customDayOptions.style.display = 'none';
    }
}

function updateAppointmentCustomDay() {
    const checkboxes = document.querySelectorAll('#custom-appointment-day-options input[type="checkbox"]');
    const customOption = document.getElementById('custom-appointment-day');
    const selectedDays = [];

    checkboxes.forEach(checkbox => {
        if (checkbox.checked) {
            selectedDays.push(parseInt(checkbox.value));
        }
    });

    customOption.value = JSON.stringify(selectedDays);
    console.log("DAYS APPOINTMENT", customOption.value);
}