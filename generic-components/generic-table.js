const columnKeyMapper = new Map([
    ["ABOUT", "ABOUT"],
    ["ADDRESS", "ADDRESS"],
    ["AVAILABILITY", "AVAILABILITY"],
    ["BIRTHDAY", "BIRTH DATE"],
    ["COMMUNICATION", "COMMUNICATION"],
    ["CHILDNAME", "CHILD NAME"], ["CHILD_NAME", "CHILD NAME"],
    ["CHILDAGE", "CHILD AGE"], ["CHILD_AGE", "CHILD AGE"],
    ["CONSENT", "CONSENT"],
    ["CREATED_AT", "CREATED AT"],
    ["DATEHIRED", "DATE HIRED"],
    ["DATESUBMITTED", "DATE SUBMITTED"],["DATE_SUBMITTED", "DATE SUBMITTED"],
    ["DAYS_AVAILABLE", "DAYS AVAILABLE"],
    ["DESCRIPTION", "DESCRIPTION"],
    ["EMAIL", "EMAIL"],
    ["FEEDBACK", "FEEDBACK"],
    ["FEEDBACK_TEXT", "FEEDBACK"],
    ["FEEDBACKDATE", "FEEDBACK DATE"],
    ["FLEXIBILITY", "FLEXIBILITY"],
    ["GENDER", "GENDER"],
    ["GUESTNAME", "GUEST NAME"], ["GUEST_NAME", "GUEST NAME"],
    ["IMAGE", "IMAGE"],
    ["NO_PATIENTS_HANDLE", "NO. OF PATIENTS HANDLE"], 
    ["PATIENT", "PATIENT"],
    ["PATIENTNAME", "PATIENT"], ["PATIENT-NAME", "PATIENT"], ["PATIENT_NAME", "PATIENT"],
    ["PHONE", "PHONE"],
    ["PHONENUMBER", "PHONE"],
    ["PRICE", "PRICE"],
    ["PARENT", "PARENT"],
    ["PARENTNAME", "PARENT"], ["PARENT-NAME", "PARENT"], ["PARENT_NAME", "PARENT"],
    ["PATIENT_STATUS", "STATUS"],
    ["RATING", "RATING"],
    ["RELATIONSHIP", "RELATIONSHIP"],
    ["REPORT_STATUS", "REPORT STATUS"], ["REPORTSTATUS","REPORT STATUS"], ["REPORT-STATUS", "REPORT STATUS"],
    ["REPORTS_STATUS", "REPORT STATUS"], ["REPORTSSTATUS","REPORT STATUS"], ["REPORTS-STATUS", "REPORT STATUS"],
    ["SCHEDULE", "SCHEDULE"],
    ["SERVICE", "SERVICE"],
    ["SERVICENAME", "SERVICE"], ["SERVICE-NAME", "SERVICE"], ["SERVICE_NAME", "SERVICE"],
    ["SHOW", "SHOW"],
    ["SPECIALIZATION", "SPECIALIZATION"],
    ["STAFF", "STAFF"],
    ["STAFF_DATEHIRED", "DATE HIRED"],
    ["STAFF_POSITION", "POSITION"],
    ["STAFFNAME", "STAFF"], ["STAFF-NAME", "STAFF"], ["STAFF_NAME", "STAFF"],
    ["STATUS", "STATUS"],
    ["SESSION_DATE", "DATE"],["SESSIONDATE","DATE"],
    ["SESSION_TIME", "TIME"],["SESSIONTIME","TIME"],
    ["THERAPIST", "THERAPIST"],
    ["THERAPISTNAME", "THERAPIST"], ["THERAPIST-NAME", "THERAPIST"], ["THERAPIST_NAME", "THERAPIST"],
    ["TIMES_AVAILABLE", "TIMES AVAILABLE"]
]);


const excludedKeyMapper = [
    "PATIENT_ID", "ID", "PATIENT-ID","PATIENTID",
    "PARENT_ID", "PARENT-ID", "PARENTID",
    "THERAPIST_ID", "THERAPIST-ID", "THERAPISTID",
    "SERVICE_ID", "SERVICE-ID", "SERVICEID",
    "PASSWORD_HASH", "PASSWORD-HASH","PASSWORDHASH",
    "RESET_TOKEN", "RESET-TOKEN",
    "RESET_TOKEN_EXPIRY", "RESET-TOKEN-EXPIRY",
    "APPOINTMENT_ID", "APPOINTMENT-ID", "APPOINTMENTID",
    "GUEST_ID", "GUEST-ID", "GUESTID",
    "ANSWER_ID", "ANSWER-ID", "ANSWERID",
    "RESPONSE_ID", "RESPONSE-ID", "RESPONSEID",
    "QUESTION_ID", "QUESTION-ID", "QUESTIONID",
    "PARENTQUESTION_ID", "PARENTQUESTION-ID", "PARENTQUESTIONID",
    "REPORT_ID", "REPORT-ID", "REPORTID",
    "STAFF_ID", "STAFF-ID", "STAFFID",
    "ADMIN_ID", "ADMIN-ID", "ADMINID",
    "FEEDBACK_ID", "FEEDBACK-ID", "FEEDBACKID",
    "SESSION_ID", "SESSION-ID", "SESSIONID",
    "GUEST-STATUS", "GUEST_STATUS", "GUESTSTATUS",
    "AVAILABILITY", "ABOUT"
];

const dateKey = ["schedule","sessionDate","feedbackdate", "date_submitted"];


class TableEngine extends HTMLElement {
    cssLink = document.head.querySelector('link[href="./generic-components/generic-table.css"]');
    tableType;

    static globalType = "";
    static url = './generic-components/table-fetch/';

    static dataSources = new Map([
        ['admin_appointments', 'admin_get_appointments.php'],
        ['admin_dashboard', 'admin_get_dashboard.php'],
        ['admin_feedbacks', 'admin_get_feedbacks.php'],
        ['admin_patients', 'admin_get_patients.php'],
        ['admin_admins', 'admin_get_admins.php'],
        ['admin_therapists', 'admin_get_therapists.php'],
        ['admin_services', 'admin_get_services.php'],
        ['admin_staffs', 'admin_get_staffs.php'],
        ['patient_appointments', 'patient_get_appointments.php'],
        ['patient_sessions', 'patient_get_sessions.php'],
        ['therapist_appointments', 'therapist_get_appointments.php'],
        ['therapist_patients', 'therapist_get_patients.php'],
        ['therapist_notes', 'therapist_get_notes.php'],
        ['therapist_pre-screening_pending', 'therapist_get_pre-screening.php?status=1'],
        ['therapist_pre-screening_complete', 'therapist_get_pre-screening.php?status=2'],
        ['therapist_progress', 'therapist_get_progress.php']
    ]);

    static setGlobalType(globalType) {
        if(!TableEngine.globalType) {
            TableEngine.globalType = globalType;
        }
    }

    constructor() {
        super();
        this.data = [];
        this.services = [];
        this.flexibility = [];
        this.communication = [];
        this.parent = [];
        this.reschedule = false;
        this.edit = false;
        this.delete = false;
        this.admin = true;
        this.avatar = false;
        this.color = false;
        this.dayMapper = new Map([
            [0, "Sunday"],
            [1, "Monday"],
            [2, "Tuesday"],
            [3, "Wednesday"],
            [4, "Thursday"],
            [5, "Friday"],
            [6, "Saturday"],
        ]);

         this.timeMapper = new Map([
            [8, "8:00 AM"],
            [9, "9:00 AM"],
            [10, "10:00 AM"],
            [11, "11:00 AM"],
            [12, "12:00 PM"],
            [13, "1:00 PM"],
            [14, "2:00 PM"],
            [15, "3:00 PM"],
            [16, "4:00 PM"],
            [17, "5:00 PM"],
            [18, "6:00 PM"],
        ]);

    }

    static get observedAttributes() {
        return ['data', 'reschedule', 'edit', 'delete', 'admin', 'avatar','color'];
    }

    async attributeChangedCallback(name, oldValue, newValue) {
        if (name === 'data') {
            const endpoint = TableEngine.dataSources.get(newValue);
            if (endpoint) {
                const fullUrl = `${TableEngine.url}${endpoint}`;
                this.tableType = newValue;
                await this.fetchServices();
                await this.fetchFlexibility();
                await this.fetchCommunication();
                await this.fetchParent();
                await this.fetchData(fullUrl);
            } else {
                console.error(`Invalid data source key: ${newValue}`);
            }
        }
        
        if (name === 'reschedule') {
            this.reschedule = newValue === 'true';
            this.render();
        }

        if (name === 'edit') {
            this.edit = newValue === 'true';
            this.render();
        }

        if (name === 'delete') {
            this.delete = newValue === 'true';
            this.render();
        }

        if (name === 'admin') {
            this.admin = newValue === 'true';
            this.render();
        }

        if (name === 'avatar') {
            this.avatar = newValue === 'true';
            this.render();
        }

        if(name === 'color') {
            this.color = newValue === 'true';
            this.render();
        }
    }

    async fetchParent() {
        try {
            const response = await fetch("z_get_all_parents.php");
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            const data = await response.json();
            this.parent = data;
            this.render();
        } catch (error) {
            console.error('Error fetching data:', error);
        }
    }

    async fetchData(url) {
        try {
            const response = await fetch(url);
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            const data = await response.json();
            this.data = data;
            this.render();
        } catch (error) {
            console.error(`Error fetching data ${this.tableType}:`, error);
        }
    }

    async fetchServices() {
        try {
            const services = await fetch("z_get_all_services.php");
            if(!services.ok) throw new Error(`HTTP error! status ${services.status}`);
            const data = await services.json();
            this.services = data;
        } catch (e) {
            console.error('Error fetching data:', e);
        }
    }

    async fetchFlexibility() {
        try {
            const flexibility = await fetch("z_get_all_flexibility.php");
            if(!flexibility.ok) throw new Error(`HTTP error! status ${flexibility.status}`);
            const data = await flexibility.json();
            this.flexibility = data;
        } catch (e) {
            console.error('Error fetching data:', e);
        }
    }

    async fetchCommunication() {
        try {
            const communication = await fetch("z_get_all_communication.php");
            if(!communication.ok) throw new Error(`HTTP error! status ${communication.status}`);
            const data = await communication.json();
            this.communication = data;
        } catch (e) {
            console.error('Error fetching data:', e);
        }
    }

    render() {
        this.innerHTML = '';
        const table = document.createElement('table');
        table.classList.add('generic-table');
        table.id = this.admin ? "admin-table" : "normal-table";
    
        // Append CSS if it's not already included
        if (!this.cssLink) {
            const cssLink = document.createElement("link");
            cssLink.rel = "stylesheet";
            cssLink.href = "./generic-components/generic-table.css";
            document.head.appendChild(cssLink);  
        }
    
        if (this.data.length > 0) {
            const headerRow = document.createElement('tr');
            
            Object.keys(this.data[0]).forEach((key) => {
                const keyUpper = key.toUpperCase();
                if (key !== 'image' && !excludedKeyMapper.includes(keyUpper)) { // Skip 'image' key
                    const th = document.createElement('th');
                    const thContent = document.createElement("div");
                    thContent.classList.add("th-container");
                    thContent.textContent = columnKeyMapper.get(keyUpper) || key;
                    th.appendChild(thContent);
                    headerRow.appendChild(th);
                }
            });
    
            if (this.reschedule || this.edit || this.delete) {
                const th = document.createElement('th');
                const thContent = document.createElement("div");
                thContent.classList.add("th-container");
                thContent.textContent = 'ACTIONS';
                th.appendChild(thContent);
                headerRow.appendChild(th);
            }
    
            table.appendChild(headerRow);
    
            this.data.forEach((row) => {
                const tr = document.createElement('tr');
       
                tr.addEventListener("click", (e) => {
                    e.preventDefault();
                    this.handleRow(row);
                });
                
    
                const firstColumn = document.createElement('td');
                if (this.avatar) {
                        let image = row['image'];
                    if(!image.startsWith("images/")) {
                        image = "images/" + image;
                    }
                    const card = `  
                    <div class="avatar-container">
                      <img src="${image}" />
                      <span>${row['patient_name'] ? row['patient_name'] : row['therapist_name'] ? row['therapist_name'] : ''}</span>
                    </div>
                    `;
                    // Append the container to the first column
                    firstColumn.innerHTML = card;
                
                    // Append the first column to the row
                    tr.appendChild(firstColumn);
                }

                if(this.color) {
                    const color = this.services.find(service => String(service.serviceName) === String(row["service_name"]));
                    const card = `  
                    <div class="avatar-container">
                      <span class="color-container" style="background-color: ${color.serviceColor}"></span>
                      <span>${row['patient_name'] ? row['patient_name'] : row['therapist_name'] ? row['therapist_name'] : ''}</span>
                    </div>
                    `;
                    // Append the container to the first column
                    firstColumn.innerHTML = card;
                
                    // Append the first column to the row
                    tr.appendChild(firstColumn);
                }
                

                Object.entries(row).forEach(([key, value]) => {
                    const keyUpper = key.toUpperCase();
                    
                    if (!excludedKeyMapper.includes(keyUpper) && key !== 'image') {

                        if(this.color && key === "therapist_name") {
                            return;
                        }

                        if(this.avatar && key == "patient_name") {
                            return;
                        }

                        if (this.avatar && key === "therapist_name" && !Object.prototype.hasOwnProperty.call(row, "patient_name")) {
                            return;
                        }

                        if(dateKey.includes(key) ) {
                            const td = document.createElement('td');
                            const tdContent = document.createElement("div");
                            tdContent.classList.add("td-container");
                            if(value) {
                                value = new Date(value).toLocaleString();
                            }
                            tdContent.textContent = value;
                            td.appendChild(tdContent);
                            tr.appendChild(td);
                            return;
                        }

                        if(key === "show") {
                            const td = document.createElement('td');
                            const tdContent = document.createElement("div");
                            tdContent.classList.add("td-container");
                            let checkbox = document.createElement("input");
                            checkbox.setAttribute("class","generic-table-checkbox");
                            checkbox.setAttribute("type","checkbox");
                            checkbox.checked = row["show"];
                            
                            checkbox.addEventListener("click",(e)=> {
                                e.stopPropagation();
                                row["show"] = checkbox.checked;
                                this.handleCheckBox(row);
                            })

                            td.appendChild(checkbox);
                            tr.appendChild(td);
                            return;
                        }

                        if(key === "specialization") {
                            const td = document.createElement('td');
                            const tdContent = document.createElement("div");
                            tdContent.classList.add("td-container");
                            
                            const service = this.services.find(service => String(service.serviceID) === String(row.specialization)); //////
                               
                            tdContent.textContent = service.serviceName;
                            td.appendChild(tdContent);
                            tr.appendChild(td);
                            return;
                        }

                        if (key === "report_status") {
                            const td = document.createElement('td');
                            const tdContent = document.createElement("div");
                            tdContent.classList.add("td-container");
                            const badge = document.createElement('span');
                            badge.classList.add('badge');
                            
                            if (value === "pending") {
                                badge.classList.add('badge-pending');
                            } else if (value === "verified") {
                                badge.classList.add('badge-verified');
                            } else {
                                badge.textContent = value.toUpperCase();
                                tdContent.appendChild(badge);
                                td.appendChild(tdContent);
                                tr.appendChild(td);
                                return;
                            }
                        
                            badge.textContent = value.toUpperCase();
                            tdContent.appendChild(badge);
                            td.appendChild(tdContent)
                            tr.appendChild(td);

                            return
                        }

                        const td = document.createElement('td');
                        const tdContent = document.createElement("div");
                        tdContent.classList.add("td-container");
                        tdContent.textContent = value;
                        td.appendChild(tdContent);
                        tr.appendChild(td);
                    }
                });
    

                if (this.reschedule || this.edit || this.delete) {
                    const tdActions = document.createElement('td');
                    tdActions.classList.add("td-container");
                    if (this.reschedule) {
                        const rescheduleButton = document.createElement('button');
                        rescheduleButton.classList.add('reschedule-button');
                        rescheduleButton.innerHTML = 'RESCHEDULE';
                        rescheduleButton.addEventListener('click', (e) => {
                            e.stopPropagation();
                            e.preventDefault();
                            this.handleRescheduleClick(row);
                        });
                        tdActions.appendChild(rescheduleButton);
                    }
    
                    if (this.edit) {
                        const editButton = document.createElement('button');
                        editButton.classList.add('edit-button');
                        editButton.innerHTML = '<i class="fas fa-edit" title="Edit"></i>';
                        editButton.addEventListener('click', (e) => {
                            e.stopPropagation();
                            e.preventDefault();
                            this.handleEditClick(row);
                        });
                        tdActions.appendChild(editButton);
                    }
    
                    if (this.delete) {
                        const deleteButton = document.createElement('button');
                        deleteButton.classList.add('delete-button');
                        deleteButton.innerHTML = '<i class="fas fa-trash" title="Delete"></i>';
                        deleteButton.addEventListener('click', (e) => {
                            e.stopPropagation();
                            e.preventDefault();
                            this.handleDeleteClick(row);
                        });
                        tdActions.appendChild(deleteButton);
                    }
    
                    tr.appendChild(tdActions);
                }

                table.appendChild(tr);
            });
        } else {
            const noDataMessage = document.createElement('p');
            noDataMessage.textContent = 'No data available';
            this.appendChild(noDataMessage);
        }
    
        this.appendChild(table);

        // setTimeout(() => {
        //     const cells = document.querySelectorAll(".generic-table td, .generic-table th");
        //     console.log(cells);
        //     cells.forEach((cell) => {
        //         const cellWidth = cell.offsetWidth;
        //         console.log(cellWidth);
        //         const marginLeft = cellWidth * 0.35;
        //         const contentContainer = cell.querySelector(".td-container");
        
        //         if (contentContainer) {
        //             contentContainer.style.marginLeft = `${marginLeft}px`;
        //         }
        //     });
        // }, 2000);
    }
    
    handleCheckBox(row) {
        console.log("CHECKBOX", row);
        try {
            fetch('a_edit_feedback_show.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    feedbackID: row["feedbackID"],
                    show: row["show"]
                })
            })
            .then(res => res.json())
            .then(res => console.log(res))
            .catch(err => console.log('Error:', err));
        } catch (error) {
            console.log('Catch block error:', error);
        }
    }
    

    handleRescheduleClick(row) {
        const appointmentDate = row.schedule ?? null;
        const appointmentID = row.appointmentID ?? null;
        const therapistID = row.therapistID ?? null;

        const calendarAppointment = new CalendarAppointment(appointmentID,null,null,null,therapistID,null,null);
       const calendar = new GenericCalendar(appointmentDate, calendarAppointment);
       calendar.create();
    }
    
    handleEditClick(row) {
        console.log('Edit clicked for', row);
        if(this.tableType == "admin_services" && row["serviceID"]) {
            this.editService(row);
        }

        if(this.tableType == "admin_patients") {
            this.editAdminPatient(row);
        }

        if(this.tableType == "admin_staffs") {
            this.editAdminStaff(row);
        }

        if(this.tableType == "admin_therapists") {
            this.editAdminTherapists(row["therapistID"]);
        }
    }
    
    handleDeleteClick(row) {
        console.log('Delete clicked for', row);
        if(this.tableType == "admin_services" && row["serviceID"]) {
            this.deleteService(row["serviceID"]);
        }
    }

    handleRow(row) {
        console.log("ROW: ", row)
        if(this.tableType == "patient_sessions") {
            const patientNotes = new PatientNotes(row);
            this.patientSessionsFetch(patientNotes);
        }

        if(this.tableType == "therapist_patients" || this.tableType == "admin_patients") {
            this.patientsInfo(row.patientID, row.image);
        }

        if(this.tableType == "therapist_progress") {
            this.patientProgress(row.reportID, row.image, row.patient_name);
        }

        if(this.tableType == "therapist_pre-screening_pending" || this.tableType == "therapist_pre-screening_complete") {
            this.patientPrescreening(row["GuestID"], row["guest_name"], row["child_name"],row["child_age"], row["guest_status"], row["email"], row["phone"])
        }

        if(this.tableType == "admin_staffs") {
            this.staffsInfo(row["staffID"])
        }

        if(this.tableType == "admin_therapists") {
            this.therapistsInfo(row["therapistID"])
        }

        if(this.tableType == "admin_services") {
            this.servicesInfo(row)
        }
    }

    addPatient(guestID, childName, email, phone) {
        document.querySelector("#add-patient-button").click();
        setTimeout(()=> {
            document.querySelector('#patientName').value = childName;
            document.querySelector('#phone').value = phone;
            document.querySelector('#email').value = email;
        }, 500);
    }

    editAdminTherapists(therapistID) {
        try {
            fetch(`a_fetch_therapist_info.php?id=${therapistID}`)
            .then(async response => await response.json())
            .then(data => {
                const datehired = data["datehired"] || "";
                const birthday = data["birthday"] || "";
                const timesAvialable = JSON.parse(data["times_available"]) || [];
                const daysAvialable = JSON.parse(data["days_available"]) || [];
                const flexibility = JSON.parse(data["flexibility"]) || [];
                const communication = JSON.parse(data["communication"]) || [];

                console.log(daysAvialable)
                let specializationOptions = `<option value="">Please Select Specialization</option>`
                this.services.forEach(service =>{
                    specializationOptions += `<option value=${service.serviceID} ${service.serviceID == data["specialization"]  ? "selected" : ""}>${service.serviceName}</option>`
                });

                let daysAvailableCheckBox = ""
                this.dayMapper.forEach((k, v) => {
                    const isChecked = daysAvialable.includes(v);
                    daysAvailableCheckBox +=  `<div class="day-list">
                        <input name="days_available[]" type="checkbox" value="${v}" ${isChecked ? "checked" : ""}>
                        <label>${k}</label>
                    </div>`;
                });       

                let timesAvailableCheckBox = "";
                this.timeMapper.forEach((k, v) => {
                    const isChecked = timesAvialable.includes(v);

                    timesAvailableCheckBox +=  `<div class="day-list">
                        <input name="times_available[]" type="checkbox" value="${v}" ${isChecked ? "checked" : ""}>
                        <label>${k}</label>
                    </div>`;
                });
                
                let flexibilityCheckBox = "";
                this.flexibility.forEach((k, v) => {
                    const isChecked = flexibility.includes(v);
                    
                    flexibilityCheckBox +=  `<div class="day-list">
                        <input name="flexibility[]" type="checkbox" value="${v}" ${isChecked ? "checked" : ""}>
                        <label>${k.flexibilityName}</label>
                    </div>`;
                });

                let communicationCheckBox = "";
                this.communication.forEach((k, v) => {
                    const isChecked = communication.includes(v);

                    communicationCheckBox +=  `<div class="day-list">
                        <input name="communication[]" type="checkbox" value="${v}" ${isChecked ? "checked" : ""}>
                        <label>${k.communicationName}</label>
                    </div>`;
                });

                const therapistForm = `
                <form action="a_edit_therapist_info.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" value="${data["therapistID"]}" name="therapistID">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="patID">Therapist ID:</label>
                            <input type="text" value="${data["therapistID"]}" id="therapistID" name="tID" disabled required>
                        </div>
                        <div class="form-group">
                            <label for="therapistName">Therapist Name:</label>
                            <input value="${data["therapist_name"]}" type="text" id="therapistName" name="therapistName" placeholder="Enter Therapist Name" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="datehired">Date Hired:</label>
                            <input value="${datehired}" type="date" id="datehired" name="datehired" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input value="${data["email"] || ''}" type="text" id="email" name="email" placeholder="Enter email address" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Phone:</label>
                            <input value="${data["phone"] || ''}" type="text" id="phone" name="phone" placeholder="Enter phone number" required>
                        </div>

                        <div class="form-group">
                            <label for="gender">Gender:</label>
                            <select id="gender" name="gender" required>
                                <option ${data["gender"] == "Female" ? "selected" : ""} value="Female">Female</option>
                                <option ${data["gender"] == "Male" ? "selected" : ""} value="Male">Male</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="birthday">Birth Date:</label>
                            <input value="${birthday}" type="date" id="birthday" name="birthday" required>
                        </div>

                        <div class="form-group">
                            <label for="address">Address:</label>
                            <input value="${data["address"]}" type="text" id="address" name="address" placeholder="e.g. 123 Elm St., Brgy" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="specialization">Specialization:</label>
                            <select id="specialization" name="specialization" required>
                                ${specializationOptions}
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="days_available">Days Available:</label>
                            <div class="day-list-checkbox">
                                ${daysAvailableCheckBox}
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="times_available">Times Available:</label>
                            <div class="day-list-checkbox">
                                ${timesAvailableCheckBox}
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="flexibility">Flexibility:</label>
                            <div class="day-list-checkbox">
                                ${flexibilityCheckBox}
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="communication">Communication:</label>
                            <div class="day-list-checkbox">
                                ${communicationCheckBox}
                            </div>
                        </div>
                    </div>
                    <div class="btn-container">
                        <button type="submit" class="submit-btn">Save</button>
                    </div>
                </form>
                `;
            new SideViewBarEngine("EDIT THERAPIST INFORMATION",therapistForm).render();
            })
            .catch(e => console.error("Error fetching parents ", e));
        }
        catch{}
    }

    editAdminStaff(row) {
        try {
            fetch(`a_fetch_staff_info.php?id=${row["staffID"]}`)
            .then(async response => await response.json())
            .then(data => {

                const datehired = data["datehired"] || "";
    
                const staffForm = `
                <form action="a_editstaffprofile_endpoint.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" value="${data["staffID"]}" name="staffID">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="patID">Staff ID:</label>
                            <input type="text" value="${data["staffID"]}" id="staffID" name="sID" disabled required>
                        </div>
                        <div class="form-group">
                            <label for="staffName">Staff Name:</label>
                            <input value="${data["staff_name"]}" type="text" id="staffName" name="staffName" placeholder="Enter Staff Name" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone">Phone:</label>
                            <input value="${data["phone"] || ''}" type="text" id="phone" name="phone" placeholder="Enter Phone Number" required>
                        </div>
                        <div class="form-group">
                            <label for="position">Position:</label>
                            <input value="${data["position"] || ''}" type="text" id="position" name="position" placeholder="Enter Position" required>
                        </div>
                        <div class="form-group">
                            <label for="gender">Sex:</label>
                            <select id="gender" name="gender" required>
                                <option ${data["gender"] == "Female" ? "selected" : ""} value="Female">Female</option>
                                <option ${data["gender"] == "Male" ? "selected" : ""} value="Male">Male</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="datehired">Date Hired:</label>
                            <input value="${datehired}" type="date" id="datehired" name="datehired" required>
                        </div>
                        <div class="form-group">
                            <label for="address">Address:</label>
                            <input value="${data["address"]}" type="text" id="address" name="address" placeholder="e.g. 123 Elm St., Brgy" required>
                        </div>
                    </div>
                    <div class="btn-container">
                        <button type="submit" class="submit-btn">Save</button>
                    </div>
                </form>
                `;
            new SideViewBarEngine("EDIT STAFF INFORMATION",staffForm).render();
            })
            .catch(e => console.error("Error fetching parents ", e));
        }
        catch{}
    }

    editAdminPatient(row) {
        try {
            fetch(`a_fetch_patientID.php?id=${row["patientID"]}`)
            .then(async response => await response.json())
            .then(data => {
                let parentOptions = `<option value="">Select Parent Name</option>`;
                this.parent.forEach(parent => {
                    parentOptions += `<option ${data["parentID"] == parent.parentID ? "selected" : ""} value="${parent.parentID}">${parent.parentName}</option>`;
                });

                const birthdayValue = data["birthday"] || "";
    
                const patientForm = `
                <form action="a_edit_patient.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" value="${data["patientID"]}" name="patientID">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="patID">Patient ID:</label>
                            <input type="text" value="${data["patientID"]}" id="patientID" name="patID" placeholder="Enter Patient ID" disabled required>
                        </div>
                        <div class="form-group">
                            <label for="patientName">Patient Name:</label>
                            <input value="${data["patientName"]}" type="text" id="patientName" name="patientName" placeholder="Enter Patient Name" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone">Phone:</label>
                            <input value="${data["phone"] || ''}" type="text" id="phone" name="phone" placeholder="Enter Phone Number" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input value="${data["email"] || ''}" type="email" id="email" name="email" placeholder="Enter Email" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="birthday">Birthday:</label>
                            <input value="${birthdayValue}" type="date" id="birthday" name="birthday" required>
                        </div>
                        <div class="form-group">
                            <label for="address">Address:</label>
                            <input value="${data["address"]}" type="text" id="address" name="address" placeholder="Enter Address" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="gender">Sex:</label>
                            <select id="gender" name="gender" required>
                                <option ${data["gender"] == "Female" ? "selected" : ""} value="Female">Female</option>
                                <option ${data["gender"] == "Male" ? "selected" : ""} value="Male">Male</option>
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
                            <input value="${data["relationship"] || ''}" type="text" id="relationship" name="relationship" placeholder="Enter Relationship" required>
                        </div>
                        <div class="form-group">
                            <label for="status">Status:</label>
                            <select id="status" name="status" required>
                                <option ${data["status"] == "Active" ? "selected" : ""} value="Active">Active</option>
                                <option ${data["status"] == "Inactive" ? "selected" : ""} value="Inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="image">Profile Picture:</label>
                            <input type="file" id="image" name="image" accept="image/*">
                        </div>
                    </div>
                    <div class="btn-container">
                        <button type="submit" class="submit-btn">Save</button>
                    </div>
                </form>
                `;
            new SideViewBarEngine("EDIT PATIENT REGISTRATION",patientForm,"view-lg").render();
            })
            .catch(e => console.error("Error fetching parents ", e));
        }
        catch{}
    }

    editService(row) {
        new SideViewBarEngine("EDIT SERVICES", 
            `
            <form id="editservice-form" action="a_editservice_endpoint.php" method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label for="service-name">Service Name:</label>
                        <input value="${row["serviceName"]}" type="text" id="service-name" name="service-name" placeholder="Type service name you want to edit" required>
                    </div>
                    <div class="form-group">
                        <label for="availability">Availability:</label>
                        <select id="availability" name="availability" required>
                            <option value="Available" ${row["availability"] === "Available" ? "selected" : ""}>Available</option>
                            <option value="Not Available" ${row["availability"] === "Not Available" ? "selected" : ""}>Not Available</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="description">Description:</label>
                        <input value="${row["description"]}" type="text" id="description" name="description" required>
                    </div>
                    <div class="form-group">
                        <label for="about">About:</label>
                        <input value="${row["about"]}" type="text" id="about" name="about" required>
                    </div>
                    <div class="form-group">
                        <label for="price">Price:</label>
                        <input value="${row["price"]}" type="text" id="price" name="price" required>
                    </div>
                </div>
                <input type="hidden" name="id" value="${row["serviceID"]}"/>
                <div class="btn-container">
                    <button type="submit" class="submit-btn">Submit <i class="fas fa-arrow-right"></i></button>
                </div>
            </form>                  
            `
        ).render()
    }

    deleteService(rowID) {
        if (confirm("Are you sure you want to delete this service?")) {
            fetch('a_deleteservice.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'serviceID=' + rowID
            })
            .then(response => response.text())
            .then(data => {
                alert(data);
                window.location.href = "admindashboard.php?active=services-section"
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    }

    servicesInfo(row) {
        new SideViewBarEngine("SERVICE", 
            `
               <div class="profile-info">
                <h5>Service Information</h5>
                <div class="contact-info-wrapper">
                    <div class="contact-info-main">
                        <strong>Service Name:</strong><p> ${row["serviceName"] || 'N/A'}</p>
                        <strong>Availability:</strong><p> ${row["availability"] || 'N/A'}</p>
                        <strong>Description:</strong><p> ${row["description"] || 'N/A'}</p>
                        <strong>About:</strong><p> ${row["about"] || 'N/A'}</p>
                        <strong>Price:</strong><p> ${row["price"] || 'N/A'}</p>
                    </div>
                </div>
            </div>

            `
        ).render();
    }

    async therapistsInfo(therapistID) {
        await fetch(`a_fetch_therapist_info.php?id=${therapistID}`, {
            method: 'GET'
        })
        .then(response =>
            {   
                return response.json()
            } 
            )
        .then(async data => {
            if(data) {
                let daysInfo = [], timeInfo = [], commInfo = [], flexInfo = [], servicesInfo = [];

                const s = this.services.find(serv => serv.serviceID == data.specialization);
                servicesInfo.push(s.serviceName);

                JSON.parse(data.days_available).forEach(days =>{
                    const day =  this.dayMapper.get(days);
                    if(day) {
                        daysInfo.push(day);
                    }
                });

                JSON.parse(data.times_available).forEach(times =>{
                    const time =  this.timeMapper.get(times);
                    if(time) {
                        timeInfo.push(time);
                    }
                });

                JSON.parse(data.communication).forEach(comm => {
                    const com = this.communication.find(findcom => findcom.communicationID == comm);
                    if(com) {
                        commInfo.push(com.communicationName);
                    }
                });

                JSON.parse(data.flexibility).forEach(flex => {
                    const f = this.flexibility.find(findflex => findflex.flexibilityID == flex);
                    if(f) {
                        flexInfo.push(f.flexibilityName);
                    }
                });
///////marker
                new SideViewBarEngine(
                    "THERAPIST", 
                    `
                    <div class="patient-info" id="patient-info">
                        <div class="profile-header">
                            <img src="images/about 1.jpg" alt="Profile Picture" class="profile-picture">
                            <div class="profile-details">
                                <h2>${data.therapist_name}</h2>
                                <h3>${servicesInfo}</h3>
                            </div>
                        </div>
                        <div class="profile-info">
                            <h5>CONTACT INFORMATION</h5>
                            <div class="contact-info-wrapper">
                                <div class="contact-info-main">
                                    <strong>Address:</strong><p> ${data.address || 'N/A'}</p>
                                    <strong>Phone:</strong><p> ${data.phone || 'N/A'}</p>
                                    <strong>Email:</strong><p>${data.email || 'N/A'}</p> <!-- Added Email -->
                                    <strong>Birthday:</strong><p> ${data.birthday ? new Date(data.birthday).toLocaleDateString() : 'N/A'}</p> <!-- Added Birthday -->
                                </div>
                            </div>
                            <h5>BASIC INFORMATION</h5>
                            <strong>Date Hired:</strong><p> ${new Date(data.datehired).toLocaleDateString() || 'N/A'}</p>
                            <strong>Sex:</strong><p> ${data.gender || 'N/A'}</p>
                            <strong>Days Available:</strong><p> ${daysInfo || 'N/A'}</p> <!-- Added Days Available -->
                            <strong>Times Available:</strong><p> ${timeInfo || 'N/A'}</p> <!-- Added Times Available -->
                            <strong>Communication:</strong><p> ${commInfo || 'N/A'}</p> <!-- Added Communication -->
                           <strong>Flexibility:</strong> <p>${flexInfo || 'N/A'}</p> <!-- Added Flexibility -->
                        </div>
                    </div>

                    `
                ).render();
            }
        })
        .catch(error => {
            console.error('Error fetching staffs:', error);
        });
    }

    async staffsInfo(staffID) {
        await fetch(`a_fetch_staff_info.php?id=${staffID}`, {
            method: 'GET'
        })
        .then(response => response.json())
        .then(async data => {
            if(data) {
                new SideViewBarEngine(
                    "STAFF", 
                    `
                    <div class="patient-info" id="patient-info">
                        <div class="profile-header">
                            <img src="images/about 1.jpg" alt="Profile Picture" class="profile-picture">
                            <div class="profile-details">
                                <h2>${data.staff_name}</h2>
                                <h3>${data.position}</h3>
                            </div>
                        </div>
                        <div class="profile-info">
                            <h5>CONTACT INFORMATION</h5>
                            <div class="contact-info-wrapper">
                                <div class="contact-info-main">
                                    <strong>Address:</strong><p> ${data.address || 'N/A'}</p>
                                    <strong>Phone:</strong><p> ${data.phone || 'N/A'}</p>
                                </div>
                            </div>
                            <h5>BASIC INFORMATION</h5>
                            <strong>Date Hired:</strong><p> ${ new Date(data.datehired).toLocaleDateString() || 'N/A'}</p>
                            <strong>Sex:</strong><p> ${data.gender || 'N/A'}</p>
                        </div>
                        </div>
                       
                    </div>
                    `
                ).render();
            }
        })
        .catch(error => {
            console.error('Error fetching staffs:', error);
        });
    }

    async patientPrescreening(guestID, guestName, childName, childAge, guestStatus, email, phone) {
        let therapyArray = [];

        if (typeof matchTherapy === "string") {
            therapyArray = matchTherapy.split(",");
        } else {
            therapyArray = [];
        }

        await fetch(`therapist_checklist.php?id=${guestID}`, {
            method: 'GET'
        })
        .then(response => response.text())
        .then(async questions => {
            questions = JSON.parse(questions);
            const mainContent = document.createElement("div");
            mainContent.classList.add("checklist-container");
            const container = document.createElement("div");
            container.classList.add("checklist-left-section");
            container.innerHTML = ""; // Clear any existing content
            const personalDetails = document.createElement("div");
            personalDetails.innerHTML = `
               <div class="checkbox-group">
            
                <div class="section-title">Personal Details</div>

                    <div class="question">
                        <div class="question-row">
                            <span class="question-text">Name:</span>
                            <div class="answer-section">
                                <div class="answer-text" style="margin-top: 5px; font-size: 14px; color: #432705;">${guestName}</div> <!-- Replace with dynamic content -->
                            </div>
                        </div>
                    </div>
                    
                    <div class="question">
                        <div class="question-row">
                            <span class="question-text">Name of Child:</span>
                            <div class="answer-section">
                                <div class="answer-text" style="margin-top: 5px; font-size: 14px; color: #432705;">${childName}</div> <!-- Replace with dynamic content -->
                            </div>
                        </div>
                    </div>
                    
                    <div class="question">
                        <div class="question-row">
                            <span class="question-text">Age of Child:</span>
                            <div class="answer-section">
                                <div class="answer-text" style="margin-top: 5px; font-size: 14px; color: #432705;">${childAge ? childAge : 0}</div> <!-- Replace with dynamic content -->
                            </div>
                        </div>
                    </div>
            </div>
            `;
            
            // Append personal details to the container
            container.appendChild(personalDetails);
            for (const [category, questionList] of Object.entries(questions.data)) {

                const categoryDiv = document.createElement("div");
                categoryDiv.classList.add("checkbox-group");
            
                // Category Title
                const sectionTitle = document.createElement("div");
                sectionTitle.classList.add("section-title");
                sectionTitle.textContent = category;
                categoryDiv.appendChild(sectionTitle);
            
                // Create a form container for each question
                const formContainer = document.createElement("div");
                formContainer.classList.add("form-container");


            
                // Questions
                questionList.forEach((item) => {
                    // Create a container for each question
                    const questionDiv = document.createElement("div");
                    questionDiv.classList.add("question");
            
                    // Question text on the left
                    const questionTextDiv = document.createElement("div");
                    questionTextDiv.classList.add("question-text");
                    questionTextDiv.style.cssText = "font-weight: 600; font-size: 14px;";
                    questionTextDiv.textContent = item.questionText;
                    questionDiv.appendChild(questionTextDiv);
            
                    // Answer options on the right
                    const answerDiv = document.createElement("div");
                    answerDiv.classList.add("answer");
            
                    // Render options based on the input type
                    if (item.inputType === "checkbox" || item.inputType === "radio") {
                        (item.options || []).forEach((option) => {
                            // Create label element
                            const label = document.createElement("label");
                            label.classList.add(item.inputType === "checkbox" ? "styled-checkbox" : "styled-radio");

                            const input = document.createElement("input");
                            input.type = item.inputType;
                            input.name = `question-${item.questionID}`;
                            input.value = option;
                            input.disabled = true;
                    
                            if (item.selectedAnswer.trim() === option.trim()) {
                                input.checked = true;
                                input.setAttribute("checked", "checked");
                            }

                            label.appendChild(input);
                            label.appendChild(document.createTextNode(option));

                            answerDiv.appendChild(label);
                        });
                    } else {
                        // For non-checkbox/radio types, render the selected answer
                        const answerText = document.createElement("div");
                        answerText.classList.add("answer-text");
                        answerText.style.cssText = "font-size: 14px; color: #432705;";
                        answerText.textContent = item.selectedAnswer || "No answer provided";
                        answerDiv.appendChild(answerText);
                    }
            
                    questionDiv.appendChild(answerDiv);
            
                    // Append question div to the form container
                    formContainer.appendChild(questionDiv);
                });
            
                // Append the form container to the category div
                categoryDiv.appendChild(formContainer);
            
                // Append the category to the main container
                container.appendChild(categoryDiv);
            }

            if(TableEngine.globalType == "admin") {
                const containerForm = document.createElement("div");
                containerForm.classList.add("checklist-right-section");
                
                let therapiesHTML = `
                    <div class="form-container-right">
                        <form class="asses" action="save_form.php" method="post">
                            <input type="hidden" name="guestId" id="guestId" value="${guestID}">
                            <input type="hidden" name="comments" id="comments" value="N/A">
                            <div class="checkbox-group">
                                <div class="section-title">Getting Ready</div>
                                <div class="terms-container" style="max-height: 200px; overflow-y: auto; border: 1px solid #ccc; padding: 10px; margin-bottom: 10px;">
                                    <p><strong>Terms and Conditions:</strong></p>
                                    <p>1. You must follow the guidelines provided by the therapist during the sessions.</p>
                                    <p>2. The institution reserves the right to terminate services in case of non-compliance.</p>
                                    <p>3. Personal data will be handled according to our privacy policy.</p>
                                    <p>4. Therapy sessions are strictly confidential and must not be shared without consent.</p>
                                    <p>5. Payments for therapies are non-refundable once sessions have begun.</p>
                                </div>
                                <label>
                                    <input 
                                        type="checkbox" name="therapies[]" id="terms-checkbox"
                                        value="terms" ${guestStatus == 2 ? "disabled" : ""}
                                    > I have read and accept the terms and conditions.
                                </label>
                                <label>
                                    <input 
                                        type="checkbox" name="therapies[]" id="privacy-checkbox"
                                        value="privacy" ${guestStatus == 2 ? "disabled" : ""}
                                    > I consent to the privacy policy and data usage agreement.
                                </label>
                            </div>
                            <div class="btn-container-right">
                                <button
                                    id="save-button"
                                    type="button" 
                                    ${guestStatus == 2 ? "disabled" : ""}
                                    style="display: ${guestStatus == 2 ? "none" : "block"}"
                                >Register</button>
                            </div>
                        </form>
                    </div>
                `;

                containerForm.innerHTML = therapiesHTML;
                mainContent.appendChild(containerForm);

                setTimeout(()=> {
                    document.querySelector("#save-button")?.addEventListener("click",async ()=> {
                        this.addPatient(guestID, childName, email, phone);
                    });
                }, 1000); 
            }

            if(TableEngine.globalType == "therapist") {
                container.classList.remove("checklist-left-section")
                container.style.width = "100%!important";
            }    

            mainContent.appendChild(container);
           
 
            new SideViewBarEngine("PROGRESS", mainContent.innerHTML, "view-lg").render();
        })
        .catch(error => {
            console.error('Error fetching notes:', error);
        });
    }

    async patientProgress(reportID, imageURL, patientName) {
        await fetch(`progress_get_id.php?id=${reportID}`, {
            method: 'GET'
        })
        .then(response => response.json())
        .then(data => {
            if (data) {
                const image = "images/" + imageURL;
                const button = data.status != "verified" ? `                                        
                                        <button type="submit">
                                            Save
                                        </button>` : "";
                new SideViewBarEngine(
                    "PROGRESS REPORT", 
                    `
                    <div class="patient-info" id="patient-info">
                        <div class="profile-header">
                            <img src="${image}" alt="Profile Picture" class="profile-picture">
                            <div class="profile-details">
                                <h2>${patientName}</h2>
                                <span class"badge">${data.status}</h3>
                            </div>
                        </div>
                        <div class="form-container">
                            <form method="POST" action="updateReport.php">
                                    <div class="comments-section">
                                        <div class="section-title">Additional Diagnosis/Comments</div>
                                        <label for="notesTextarea">Save or Edit Report:</label>
                                        <textarea class="section-textarea" name="summary" id="notesTextarea" rows="4">${data.summary}</textarea>
                                        <input type="hidden" name="reportID" value="${reportID}"/>
                                        
                                        ${button}
                                    </div>
                            </form> 
                        </div>
                        </div>     
                    </div>
                    `
                ).render();
            } else {
                alert('Failed to load notes: ' + data.error);
            }
                    })
        .catch(error => {
            console.error('Error fetching notes:', error);
        });
    }

    async patientsInfo(patientID, imageUrl) {
        await fetch(`therapist_patient_info.php?id=${patientID}`, {
            method: 'GET'
        })
        .then(response => response.json())
        .then(data => {
            if (data) {
                const patient = new PatientInfo(data);
                
                let image = imageUrl;
                if(!imageUrl.startsWith("images/")) {
                    image = "images/" + imageUrl;
                }
                
                const notesHTML = data.notes.length > 0 
                    ? data.notes.map(note => `
                        <div class="note-item">
                            <strong>Date:</strong><p> ${note.feedbackdate}</p>
                            <p>${note.feedback}</p>
                        </div>
                    `).join('') 
                    : `<p>No notes yet</p>`;
            
                new SideViewBarEngine(
                    "PROFILES", 
                    `
                    <div class="patient-info" id="patient-info">
                        <div class="profile-header">
                            <img src="${image}" alt="Profile Picture" class="profile-picture">
                            <div class="profile-details">
                                <h2>${patient.patientName}</h2>
                                <h3>${patient.service || 'N/A'}</h3>
                            </div>
                        </div>
                        <div class="profile-info">
                            <h5>CONTACT INFORMATION</h5>
                            <div class="contact-info-wrapper">
                                <div class="contact-info-main">
                                    <strong>Parent/Guardian:</strong><p> ${patient.parentName || 'N/A'}</p>
                                    <strong>Address:</strong><p> ${patient.address || 'N/A'}</p>
                                    <strong>Phone:</strong><p> ${patient.phone || 'N/A'}</p>
                                    <strong>Email:</strong><p> ${patient.email || 'N/A'}</p>
                                </div>
                            </div>
                            <h5>BASIC INFORMATION</h5>
                            <strong>Birthday:</strong><p> ${patient.getFormattedBirthday()}</p>
                            <strong>Sex:</strong><p> ${patient.gender || 'N/A'}</p>
                        </div>
                        </div>
                       
                    </div>
                    `
                ).render();
            
                const notesButton = document.querySelector('.view-notes-btn');
                if (notesButton) {
                    notesButton.addEventListener('click', () => {
                        window.location.href = '#notes-section';
                    });
                }
            } else {
                alert('Failed to load notes: ' + data.error);
            }
                    })
        .catch(error => {
            console.error('Error fetching notes:', error);
        });
    }

    async patientSessionsFetch(patientNotes) {
        await fetch('patient_get_notes.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                sessionID: patientNotes.sessionID,
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const sessionData = {
                    schedule: patientNotes.schedule,
                    therapist: data.therapist,
                    notes: data.notes.join('') 
                };

                new SideViewBarEngine(
                    "SESSION OVERVIEW", 
                    `<div>
                    <blockquote style="padding: 10px; margin: 20px 0; border-left: 4px solid #ddd; background-color: #f9f9f9; font-style: italic;">
                      <p style="margin: 0; font-size: 1rem; color: #333;">${sessionData.notes}</p>
                    </blockquote>
                    <p style="margin: 0; font-size: 1rem; color: #333;">
                      <strong>Therapist:</strong> ${sessionData.therapist}
                    </p>
                  </div>
                  `
                    ).render();
            } else {
                alert('Failed to load notes: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error fetching notes:', error);
        });
    }

    async servicesFetch() {
        const servicesListMap = new Map();
    
        await fetch(`z_get_all_services.php`, {
            method: 'GET',
        })
        .then(response => response.json())
        .then(data => {
            // Check if data is an array to avoid errors
            if (Array.isArray(data)) {
                data.forEach(service => {
                    servicesListMap.set(service.serviceID, service.serviceName);
                });
            } else {
                console.error('Unexpected data format:', data);
            }
        })
        .catch(error => {
            console.error('Error fetching services:', error);
        });
    
        return servicesListMap;
    }
    
}

class PatientNotes {
    constructor(
        row = null
    ){  
        if(row) {
            this.sessionDate = row.sessionDate;
            this.sessionID = row.sessionID;
            this.sessionTime = row.sessionTime;
            this.therapistID = row.therapistID;
            this.therapistName = row.therapistName;
        }
    }
}

class PatientInfo {
    constructor(data) {
        this.patientID = data.patientID || null;
        this.patientName = data.patient_name || null;
        this.gender = data.gender || null;
        this.birthday = data.birthday || null;
        this.address = data.address || null;
        this.phone = data.phone || null;
        this.email = data.email || null;
        this.service = data.service || null;
        this.parentID = data.parentID || null;
        this.parentName = data.parent_name || null;
    }

    getFormattedBirthday() {
        if (!this.birthday) return "N/A";
        const date = new Date(this.birthday);
        return date.toLocaleDateString();
    }

    getFullInfo() {
        return `
            Patient ID: ${this.patientID}
            Name: ${this.patientName}
            Sex: ${this.gender}
            Birthday: ${this.getFormattedBirthday()}
            Address: ${this.address}
            Phone: ${this.phone}
            Email: ${this.email}
            Service: ${this.service}
            Parent ID: ${this.parentID}
            Parent Name: ${this.parentName}
        `.trim();
    }
}



customElements.define('generic-table', TableEngine);
