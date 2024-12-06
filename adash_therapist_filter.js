/* Must include this script along with generic-message-popup.js
    in the HTML or PHP template .
    This script must be imported above the other script that uses this e.g. adash.js and generic-message-popup.js
    <script src="adash_therapist_filter.js" defer></script>
    <script src="SOME_OTHER_SCRIPTS" defer></script>
*/

class TherapistFilter {
    widget = null;
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

        let url = `a_fetch_therapist_service_filter.php`;

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
        widget.createTitle("Therapists Available: ");
        this.widget = widget;
    }

    getWidgetTherapistId() {
        return this.widget.selectedId;
    }

    async searchByName(name) {
        if(!name) {
            return;
        }

        let url = `a_fetch_therapist_service_filter_string.php?name=${name}`;
        try {
            await fetch(url)
                .then(response => response.json())
                .then(data => {
                    if(!data.error) {
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
        if (!this.name || this.name === "") {
            this.name = null;
        }
    
        let filteredData = this.data;

        if(this.specialization && this.specialization.length > 0) {
            filteredData = filteredData.filter(therapist => {
                return this.specialization == therapist.specialization;
            });
        }

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
    therapist.fetch();

    toggleCustomTime = () => {
        const select = document.getElementById('time-availability');
        const customTimeOptions = document.getElementById('custom-time-options');
    
        if (select.value === '[]') {
            customTimeOptions.style.display = 'block';
        } else {
            customTimeOptions.style.display = 'none';
        }
    }

    updateCustomTime = () => {
        const checkboxes = document.querySelectorAll('#custom-time-options input[type="checkbox"]');
        const customOption = document.getElementById('custom-time');
        const selectedTime = [];
    
        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
                selectedTime.push(parseInt(checkbox.value));
            }
        });
    
        customOption.value = JSON.stringify(selectedTime);
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
    
    updateCommunication = () => {
        let selectedMethods = [];
        document.querySelectorAll("input[name='communication[]']:checked").forEach(function(checkbox) {
            selectedMethods.push(parseInt(checkbox.value));
        });
        console.log("Selected Communication Methods:", selectedMethods);
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

    updateSpecialization = () => {
        let selectedSpecialization = [];

        document.querySelectorAll("input[name='specialization[]']:checked").forEach(function (checkbox) {
            selectedSpecialization.push(parseInt(checkbox.value));
        });
    
        console.log("Selected Specialization:", selectedSpecialization);
        document.getElementById("specialization").value = JSON.stringify(selectedSpecialization);
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
        console.log("Selected Flexibilities Methods:", selectedFlexibilities);
        therapist.filter();
    }

    updateSpecializationAppointment = () => {

        let value = document.querySelector("#serviceID").value;
        therapist.setSpecialization([value]);
        console.log("Selected Specialization:", value);
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

    resetFilters = () => {
        const customTimeAppointmentOptions = document.getElementById('custom-appointment-time-options');
        const customTimeAppointmentValue = document.getElementById('custom-appointment-time');
        customTimeAppointmentValue.value = '[]';
        customTimeAppointmentOptions.style.display = 'none';
        
        const customDayAppointmentOptions = document.getElementById('custom-appointment-day-options');
        const customDayAppointmentValue = document.getElementById('custom-appointment-day');
        customDayAppointmentValue.value = '[]';
        customDayAppointmentOptions.style.display = 'none';
        
        const communicationCheckboxes = document.querySelectorAll("input[name='appointmentCommunication[]']");
        communicationCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        
        const flexibilityCheckboxes = document.querySelectorAll("input[name='appointmentFlexibility[]']");
        flexibilityCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        
        const specializationCheckboxes = document.querySelectorAll("input[name='specializationAppointment[]']");
        specializationCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        
        const searchByNameInput = document.querySelector("#searchByName");
        searchByNameInput.value = '';
        
        therapist.clear();
        therapist.fetch();

        therapist.filter();
    };

    submitAppointmentForm = () => {
        const patientID = document.getElementById("patient-ID").value;
        const patientName = document.getElementById("patient-name").value;
        const parentID = document.getElementById("parentID").value;
        const contactNumber = document.getElementById("contact-number").value;
        const serviceID = document.getElementById("serviceID").value;
        const therapistID = therapist.getWidgetTherapistId();

        // Validate fields
        if (!patientID || !patientName || !parentID || !contactNumber || !serviceID) {
            new MessagePopupEngine("Information", "All fields are required!").instantiate();
            return; // Prevent form submission if any required field is empty
        }

        if (!therapistID) {
            new MessagePopupEngine("Information", "Please pick a therapist").instantiate();
            return;
        }
        
        calendarAppointment = new CalendarAppointment(null,"ongoing",patientID,parentID,therapistID,serviceID);

        calendar = new GenericCalendar(null, calendarAppointment);
        calendar.create();
    }
});