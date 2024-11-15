/* Must include this script along with generic-message-popup.js
    in the HTML or PHP template .
    This script must be imported above the other script that uses this e.g. adash.js and generic-message-popup.js
    <script src="adash_therapist_filter.js" defer></script>
    <script src="SOME_OTHER_SCRIPTS" defer></script>
*/

class TherapistFilter {
    days_available = null;
    times_available = null;
    communication = null;
    flexibility = null;
    specialization = null;
    name = null;
    data = []; // To hold the fetched data

    constructor(data) {
        if (data) {
            this.days_available = data.days_available;
            this.times_available = data.times_available;
            this.communication = data.communication;
            this.flexibility = data.flexibility;
            this.specialization = data.specialization;
        }
    }

    setSpecialization(specialization) {
        this.specialization = specialization;
    }

    setDaysAvailable(days) {
        this.days_available = JSON.parse(days);
    }

    setTimesAvailable(times) {
        this.times_available = JSON.parse(times);
    }

    setCommunication(communication) {
        this.communication = JSON.parse(communication);
    }

    setFlexibility(flexibility) {
        this.flexibility = JSON.parse(flexibility);
    }

    setName(name) {
        this.name = name;
    }

    async fetch() {
        if (!this.specialization) {
            new MessagePopupEngine("Error", "Please Select Specialization").instantiate();
            return;
        }
    
        let url = `a_fetch_therapist_service_filter.php?specialization=${this.specialization}`;

        try {
            await fetch(url)
                .then(response => response.json())
                .then(data => {
                    this.data = data;
                    this.displayData(data);
                })
                .catch(error => {
                    new MessagePopupEngine("Error", `error on therapist fetch\n` + error).instantiate();
                });
        } catch (e) {
            console.error("FETCH ERROR: ", e);
        }
    }

    displayData(data) {
        let widget = new WidgetEngine(data);
        widget.instantiate();
        widget.createTitle("Therapist Match");
    }

    async searchByName(name) {
        console.log(name)
        if(!name) {
            return;
        }

        let url = `a_fetch_therapist_service_filter_string.php?name=${name}`;
        try {
            await fetch(url)
                .then(response => response.json())
                .then(data => {
                    if(!data.error) {
                        new MessagePopupEngine("Test Generic", "Test").instantiate();
                        this.displayData(data);
                    } else {
                        new MessagePopupEngine("Info", "No Therapist Found").instantiate();
                    }
                })
                .catch(error => {
                    new MessagePopupEngine("Error", `error on therapist fetch\n` + error).instantiate();
                });
        } catch (e) {
            console.error("FETCH ERROR: ", e);
        }
        
    }

    filter() {
        if (!this.specialization) {
            new MessagePopupEngine("Error", "Please Select Specialization").instantiate();
            return;
        }

        if (!this.name || this.name === "") {
            this.name = null;
        }
    
        let filteredData = this.data;

        if (this.days_available && this.days_available.length > 0) {
            filteredData = filteredData.filter(therapist => {
                const therapistDays = therapist.days_available ? JSON.parse(therapist.days_available) : [];
                return this.days_available.some(day => therapistDays.includes(day));
            });
        }
    
        if (this.times_available && this.times_available.length > 0) {
            filteredData = filteredData.filter(therapist => {
                const therapistTimes = therapist.times_available ? JSON.parse(therapist.times_available) : [];
                return this.times_available.some(time => therapistTimes.includes(time));
            });
        }
    
        if (this.communication && this.communication.length > 0) {
            filteredData = filteredData.filter(therapist => {
                const therapistCommunication = therapist.communication ? JSON.parse(therapist.communication) : [];
                return this.communication.some(method => therapistCommunication.includes(method));
            });
        }
    
        if (this.flexibility && this.flexibility.length > 0) {
            filteredData = filteredData.filter(therapist => {
                const therapistFlexibility = therapist.flexibility ? JSON.parse(therapist.flexibility) : [];
                return this.flexibility.some(method => therapistFlexibility.includes(method));
            });
        }

        if (this.name) {
            filteredData = filteredData.filter(therapist => {
                return therapist.therapist_name.toLowerCase().includes(this.name.toLowerCase());
            });
        }
    
        this.displayData(filteredData);
    }

    clear() {
        this.days_available = null;
        this.times_available = null;
        this.communication = null;
        this.flexibility = null;
        this.specialization = null;
        this.name = null;
        this.data = [];
    }
    
}


document.addEventListener("DOMContentLoaded", function() {
    const selectTimeAppointmentAvailability = document.getElementById('time-appointment-availability');
    const customTimeAppointmentOptions = document.getElementById('custom-appointment-time-options');
    const selectDayAppointmentAvailability = document.getElementById('day-appointment-availability');
    const customDayAppointmentOptions = document.getElementById('custom-appointment-day-options');

    let therapist = new TherapistFilter();
    let debounceTimeout;

    therapist.setTimesAvailable(selectTimeAppointmentAvailability.value);
    therapist.setDaysAvailable(selectDayAppointmentAvailability.value);

    resetTherapist = () => {
        let widget = new WidgetEngine([]);
        widget.instantiate();
        widget.createTitle("Therapist Match");
    }

    toggleCustomTime = () => {
        const select = document.getElementById('time-availability');
        const customTimeOptions = document.getElementById('custom-time-options');
    
        if (select.value === '[]') {
            customTimeOptions.style.display = 'block';
        } else {
            customTimeOptions.style.display = 'none';
        }
    }
    
    updateCommunication = () => {
        let selectedMethods = [];
        document.querySelectorAll("input[name='communication[]']:checked").forEach(function(checkbox) {
            selectedMethods.push(parseInt(checkbox.value));
        });
        console.log("Selected Communication Methods:", selectedMethods);
        // Assuming you are using a hidden input field to store the selected values
        document.getElementById("communication").value = JSON.stringify(selectedMethods);
    }
    
    updateFlexibility = () => {
        let selectedFlexibilities = [];
        document.querySelectorAll("input[name='flexibility[]']:checked").forEach(function(checkbox) {
            selectedFlexibilities.push(parseInt(checkbox.value));
        });
        console.log("Selected Flexibilities:", selectedFlexibilities);
        // Assuming you are using a hidden input field to store the selected values
        document.getElementById("flexibility").value = JSON.stringify(selectedFlexibilities);
    }
    
    toggleCustomDay = () => {
        const select = document.getElementById('day-availability');
        const customDayOptions = document.getElementById('custom-day-options');
    
        if (select.value === '[]') {
            customDayOptions.style.display = 'block';
        } else {
            customDayOptions.style.display = 'none';
        }
    }
    
    updateCustomDay = () => {
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
    
    logMe = () => {
        const form = document.querySelector("#addstaff-form");
        const formData = new FormData(form);
        const formValues = Object.fromEntries(formData.entries());
    }
    
    /*--- 
        APPOINTMENT MARKER DO NOT REMOVE ^_^
    ---*/
    toggleCustomAppointmentTime = () => {
        if (selectTimeAppointmentAvailability.value === '[]') {
            customTimeAppointmentOptions.style.display = 'block';
        } else {
            customTimeAppointmentOptions.style.display = 'none';
        }

        console.log(selectTimeAppointmentAvailability.value);
        therapist.setTimesAvailable(selectTimeAppointmentAvailability.value);
        therapist.filter();
    }
    
    updateAppointCustomTime = () => {
        const checkboxes = document.querySelectorAll('#custom-appointment-time-options input[type="checkbox"]');
        const customOption = document.getElementById('custom-appointment-time');
        const selectedTimes = [];
    
        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
                selectedTimes.push(parseInt(checkbox.value));
            }
        });
    
        customOption.value = JSON.stringify(selectedTimes);
        therapist.setTimesAvailable(customOption.value);
        therapist.filter();
    }
    
    toggleAppointmentCustomDay = () => {
        if (selectDayAppointmentAvailability.value === '[]') {
            customDayAppointmentOptions.style.display = 'block';
        } else {
            customDayAppointmentOptions.style.display = 'none';
        }

        therapist.setDaysAvailable(selectDayAppointmentAvailability.value);
        therapist.filter();
    }
    
    updateAppointmentCustomDay = () => {
        console.log(therapist);
        const checkboxes = document.querySelectorAll('#custom-appointment-day-options input[type="checkbox"]');
        const customOption = document.getElementById('custom-appointment-day');
        const selectedDays = [];
    
        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
                selectedDays.push(parseInt(checkbox.value));
            }
        });
    
        customOption.value = JSON.stringify(selectedDays);
        therapist.setDaysAvailable(customOption.value);
        therapist.filter();
    }

    updateAppointmentCustomDay = () => {
        const checkboxes = document.querySelectorAll('#custom-appointment-day-options input[type="checkbox"]');
        const customOption = document.getElementById('custom-appointment-day');
        const selectedDays = [];
    
        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
                selectedDays.push(parseInt(checkbox.value));
            }
        });
    
        customOption.value = JSON.stringify(selectedDays);
        therapist.setDaysAvailable(customOption.value);
        therapist.filter();
    }
    
    updateAppointmentCommunication = () => {
        let selectedMethods = [];
        document.querySelectorAll("input[name='appointmentCommunication[]']:checked").forEach(checkbox => {
            selectedMethods.push(parseInt(checkbox.value));
        });
        
        // Set the selected communication methods in the therapist object
        therapist.setCommunication(JSON.stringify(selectedMethods));
        console.log("Selected Communication Methods:", selectedMethods);
    
        // Optionally, trigger the filter if you want to update the results immediately
        therapist.filter();
    }
    

    updateAppointmentFlexibility = () => {
        let selectedFlexibilities = [];
        document.querySelectorAll("input[name='appointmentFlexibility[]']:checked").forEach(function(checkbox) {
            selectedFlexibilities.push(parseInt(checkbox.value));
        });

        therapist.setFlexibility(JSON.stringify(selectedFlexibilities));
        console.log("Selected Communication Methods:", selectedFlexibilities);
        therapist.filter();
    }

    document.querySelector("#searchByName").addEventListener("keyup", function(event) {
        const inputValue = event.target.value;
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(function() {
            therapist.setName(inputValue);

            if(therapist.specialization) {
                therapist.filter();

                return;
            }

            therapist.searchByName(inputValue);
        }, 1000);
    });
    

    document.querySelector("#serviceID").addEventListener("change", function(){

        document.querySelector("#searchByName").value = "";
        therapist.setName(null);

        const specializationMap = new Map([
            ["Speech Therapy", "Speech Therapist"],
            ["Occupational Therapy", "Occupational Therapist"],
            ["Physical Therapy", "Physical Therapist"],
            ["Behavioral Therapy", "Behavioral Therapist"]
        ]);

        let specialization = specializationMap.get($(this).val());
        
        console.log(specialization);

        if (specialization && specialization != undefined && specialization != null) {
            therapist.setSpecialization(specialization);
            therapist.fetch();
            console.log(therapist);
        } else {
            therapist.clear();
            resetTherapist();
        }
    });

    resetTherapist();
});