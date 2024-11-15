
/*
    Usage, include this js file in the script of HTML or PHP template
    <script src="./generic-components/generic-calendar.js" defer></script>
    create the element on the same template <generic-calendar></generic-calendar>
    on your js file that has listeners to open the calendar. Just declare new GenericCalendar() pass the params needed see
    other implementations
*/

 class CalendarEngineComponent extends HTMLElement {

    selectedTimeSlots = ['9:00 AM', '10:00 AM', '11:00 AM', '12:00 PM', '1:00 PM', '2:00 PM', '3:00 PM', '4:00 PM', '5:00 PM', '6:00 PM'];
    blockedSelectedTimeSlots = [];
    blockedTimeSlots = [];
    genericCalendar = document.querySelector("generic-calendar");
    calendarDiv = null;
    timeDiv = null;
    cssLink = document.head.querySelector('link[href="./generic-components/generic-calendar.css"]');

    constructor(){
        super();
    }

    instantiate() {
        if(!this.cssLink) {
            const cssLink = document.createElement("link");
            cssLink.rel = "stylesheet";
            cssLink.href = "./generic-components/generic-calendar.css";
        
            document.head.appendChild(cssLink);  
        }

        if (this.calendarDiv && this.timeDiv) {
            return
        }

        const calendar = document.createElement("div");
        calendar.id = "calendar-div";

        const time = document.createElement("div");
        time.id = "time-div";

        if(this.genericCalendar) {
            this.genericCalendar.appendChild(calendar);
            this.genericCalendar.appendChild(time);

            this.calendarDiv = document.querySelector("#calendar-div");
            this.timeDiv = document.querySelector("#time-div");
            return;
        }

        throw new Error(`div with id 'generic-calendar was not found'`);
    }

    createCalendarContainer = () => {
        const divContainer = document.createElement("div");
        divContainer.id = "reschedulePopup";
        divContainer.classList.add("popup");
        divContainer.style.display = "block";
    
        // Use innerHTML for the container's structure
        divContainer.innerHTML = `
            <div class="popup-content">
                <span class="close">&times;</span>
                <h4>Select Available Dates</h4>
                <div id="calendar">
                    <div id="calendar-loading">Loading</div>
                    <div class="calendar-container">
                        <div class="month-navigation">
                            <a href="#" id="prevMonth" class="nav-link-month">&lt;</a>
                            <p id="currentMonth"></p>
                            <a href="#" id="nextMonth" class="nav-link-month">&gt;</a>
                        </div>
                        <div class="calendar-grid"></div>
                    </div>
                </div>
                <button id="proceedButton" type="button">Proceed →</button>
            </div>
        `;
    
        divContainer.querySelector(".close").addEventListener("click", () => {
            this.calendarDiv.innerHTML = "";
        });
    
        divContainer.querySelector("#prevMonth").addEventListener("click", (e) => {
            e.preventDefault();
            this.dateObj.selectedMonth--;
            if (this.dateObj.selectedMonth < 0) {
                this.dateObj.selectedMonth = 11;
                this.dateObj.selectedYear--;
            }
            this.reload();
        });
    
        divContainer.querySelector("#nextMonth").addEventListener("click", (e) => {
            e.preventDefault();
            this.dateObj.selectedMonth++;
            if (this.dateObj.selectedMonth > 11) {
                this.dateObj.selectedMonth = 0;
                this.dateObj.selectedYear++;
            }
            this.reload();
        });
    
        divContainer.querySelector("#proceedButton").addEventListener("click", () => {
            if (this.dateObj.selectedDate) {
                divContainer.querySelector(".close").click();
                this.createTimeContainer();
                this.generateAvailableTimes();
            } else {
                alert('Please select a date first.');
            }
        });
    
        this.calendarDiv.appendChild(divContainer);
    };
    

    createTimeContainer = () => {
        const divContainer = document.createElement("div");
        divContainer.id = "timePopup";
        divContainer.classList.add("popup");
        divContainer.style.display = "block";

        divContainer.innerHTML = `
            <div class="popup-content">
                <span class="close">&times;</span>
                <h4>Select Available Times</h4>
                <div id="availableTimes">
                    <h5>Morning Sessions</h5>
                    <ul id="morningTimes"></ul>
                    <h5>Afternoon Sessions</h5>
                    <ul id="afternoonTimes"></ul>
                </div>
                <button id="backButton">Back</button>
                <button id="confirmTimeButton">${this.calendarAppointment.appointmentID ? "Reschedule →" : "Schedule →"}</button>
            </div>
        `;

        divContainer.querySelector(".close").addEventListener("click", () => {
            this.timeDiv.innerHTML = "";
        });
        
        divContainer.querySelector("#backButton").addEventListener("click", () => {
            this.timeDiv = null;
            this.create();
        });
        
        divContainer.querySelector("#confirmTimeButton").addEventListener("click", () => {
            this.confirmTime();
        });
    
        this.timeDiv.appendChild(divContainer);
    }
 }
 
 class GenericCalendar extends CalendarEngineComponent {

    calendarAppointment = new CalendarAppointment();
    appointmentDay = 0;
    selectedMonth = new Date().getMonth();
    selectedYear = new Date().getFullYear();

    dateObj;

    constructor(
        appointmentDate = null,
        calendarAppointment
    ) {
        super();
        this.calendarAppointment = calendarAppointment;

        if (appointmentDate) {
            const [year, month, day] = appointmentDate.split(' ')[0].split('-').map(Number);
            this.appointmentDay = day;
        }

        this.dateObj = {
            selectedMonth: this.selectedMonth,
            selectedYear: this.selectedYear,
            daysInMonth: new Date(this.selectedYear, this.selectedMonth + 1, 0).getDate(),
            selectedDate: null,
            selectedTime: null
           }
    }

    async create() {
        this.instantiate();

        if (this.calendarDiv && this.timeDiv) {
            this.calendarDiv.innerHTML = "";
            this.timeDiv.innerHTML = "";

            this.createCalendarContainer();
            setTimeout( async () => {
                const calendarFinalContainer = this.generateCalendar(this.calendarDiv);
                await this.fetchBookedDatesForTherapist( calendarFinalContainer);
            }, 1000);
            
            return;
        }

        throw new Error(`No Calendar and Time Div found`);
    }

    reload() {
        const calendarGrid = this.generateCalendar();
        this.fetchBookedDatesForTherapist(calendarGrid);
    }

    generateCalendar = () => {
        const days = ["S", "M", "T", "W", "TH", "F", "SA"];
        const calendarGrid = this.calendarDiv.querySelector(".calendar-grid"); 
        calendarGrid.innerHTML = '';
        
        for(const day of days) {
            const dayDiv = document.createElement('div');
            dayDiv.classList.add("day");
            dayDiv.textContent = day;
            calendarGrid.appendChild(dayDiv);
        }

        this.calendarDiv.querySelector('#currentMonth').innerText = `${this.getMonthName(this.dateObj.selectedMonth)} ${this.dateObj.selectedYear}`;
        const firstDayOfMonth = new Date(this.dateObj.selectedYear, this.dateObj.selectedMonth, 1).getDay(); // Get the first day of the month (0 = Sunday, 6 = Saturday)
        

        for (let i = 0; i < firstDayOfMonth; i++) {

            const blankDiv = document.createElement('div');
            blankDiv.classList.add('day', 'blank');
            calendarGrid.appendChild(blankDiv);
        }
    
        return calendarGrid;
    }

     async fetchBookedDatesForTherapist(calendarGrid) {
        try {
            const response = await fetch(`booked-dates.php?therapist_id=${this.calendarAppointment.therapistID}`);
            const data = await response.json();
            console.log(data);
            if (data) {
                this.blockedSelectedTimeSlots = [];
                this.generateTimeSlots(JSON.parse(data.blockedTimes));

                for(const bookedDates of data.bookedDates) {
                    this.blockedSelectedTimeSlots.push(bookedDates);
                }

                console.log(this.blockedSelectedTimeSlots);
                for (let day = 1; day <= this.dateObj.daysInMonth; day++) {
                    const dateString = `${this.dateObj.selectedYear}-${String(this.dateObj.selectedMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                    const dayDiv = document.createElement('div');
                    dayDiv.classList.add('day');
                    dayDiv.innerText = day;
    
                    const generatedDate = new Date(dateString);
                    const currentDate = new Date();
    
                    if (generatedDate.getDay() === 0) {
                        dayDiv.classList.add('disabled');
                    }

                    if(!data.blockedDates.includes(generatedDate.getDay())) {
                        dayDiv.classList.add('disabled');
                    }
    
                    if (generatedDate < currentDate) {
                        dayDiv.classList.add('disabled');
                    } else {
                        dayDiv.addEventListener('click', () => {
                            document.querySelectorAll('.day').forEach(el => el.classList.remove('selected'));
                            dayDiv.classList.add('selected');
                            this.dateObj.selectedDate = dateString;
                        });
                    }
    
                    if (this.appointmentDay === day) {
                        dayDiv.classList.add('selected');
                        this.dateObj.selectedDate = dateString;
                    }
    
                    calendarGrid.appendChild(dayDiv);
                }
            }
        } catch (error) {
            console.error("Error fetching booked dates for therapist:", error);
        } finally {
            this.calendarDiv.querySelector("#calendar-loading").innerHTML = "";
        }
    }

    generateTimeSlots(startHours) {
        for (const time of startHours) {
            if (typeof time !== "number" || isNaN(time)) {
                console.error(`Invalid hour: ${time}`);
                continue; // Skip invalid entries
            }
            let period = time < 12 ? "AM" : "PM";
            let displayHour = time % 12 === 0 ? 12 : time % 12; // Convert to 12-hour format
            this.blockedTimeSlots.push(`${displayHour}:00 ${period}`);
        }
    }
    

    generateAvailableTimes() {
        const booked = []
        this.blockedSelectedTimeSlots.filter(bookedDates =>  bookedDates.split(" ")[0] == this.dateObj.selectedDate)
        .forEach(filteredBookedDates => {
            booked.push(this.convertTo12HourFormat(filteredBookedDates));
        })

        const morningList = document.getElementById('morningTimes');
        const afternoonList = document.getElementById('afternoonTimes');
    
        morningList.innerHTML = "";
        afternoonList.innerHTML = "";
    
        const currentTime = new Date();
        const selectedDateObj = new Date(this.dateObj.selectedDate);
        const isToday = selectedDateObj.toDateString() === currentTime.toDateString();
    
        const nextHourTime = new Date(currentTime);
        nextHourTime.setHours(currentTime.getHours() + 1);
        nextHourTime.setMinutes(0);
    
        this.selectedTimeSlots.forEach(time => {
            const listItem = document.createElement('li');

            const radioButton = document.createElement('input');
            radioButton.type = 'radio';
            radioButton.name = 'timeSlot'; 
            radioButton.value = time;  
            radioButton.id = time;``
            radioButton.addEventListener("click", (e) =>{
                this.dateObj.selectedTime = this.convertTo24HourFormat(e.target.value);
            })
    
            const [timePart, period] = time.split(' ');
            let [hours, minutes] = timePart.split(':').map(num => parseInt(num));
            if (period === 'PM' && hours !== 12) hours += 12;
            if (period === 'AM' && hours === 12) hours = 0;
    
            const timeSlot = new Date(selectedDateObj);
            timeSlot.setHours(hours);
            timeSlot.setMinutes(minutes);
            timeSlot.setSeconds(0);
            timeSlot.setMilliseconds(0);
    
            if ((isToday && (timeSlot <= currentTime || timeSlot <= nextHourTime))||
                booked.includes(time) || this.blockedTimeSlots.includes(time)
            ) {
                radioButton.disabled = true;
                const label = document.createElement('label');
                label.textContent = "N/A";
                label.setAttribute('for', time); 
                
                listItem.appendChild(radioButton);
                listItem.appendChild(label);
            } else {
                const label = document.createElement('label');
                label.setAttribute('for', time);
                label.textContent = time;
    
                listItem.appendChild(radioButton);
                listItem.appendChild(label);
            }

            if (hours < 12) {
                morningList.appendChild(listItem);
            } else {
                afternoonList.appendChild(listItem);
            }
        });
    }

    async confirmTime() {
    if (this.dateObj.selectedDate && this.dateObj.selectedTime) {
            const newSchedule = `${this.dateObj.selectedDate} ${this.dateObj.selectedTime}`;

            if(this.calendarAppointment.appointmentID) {
                await fetch('patientRescheduleAppointment.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        selectedDatetime: newSchedule,
                        appointmentID: this.calendarAppointment.appointmentID
                    }),
                })
                .then(response => {
                    return response.json()
                })
                .then(data => {
    
                    if (data.success) {
                        new MessagePopupEngine("Success", "Your Appointment has been rescheduled successfully!").instantiate();
                        setTimeout(()=> {
                            window.location.reload();
                        }, 1000);
                    } else {
                        new MessagePopupEngine("Error", "Failed to reschedule appointment. Please try again later.").instantiate();
                    }
                })
                .catch(error => {
                    console.error('Error updating appointment:', error);
                    new MessagePopupEngine("Error", "An error occurred. Please try again.").instantiate();
                });
            }
            
            if(!this.calendarAppointment.appointmentID) {
                this.calendarAppointment.schedule = newSchedule;

                const params = new URLSearchParams();
                console.log(this.calendarAppointment);
                
                for (const key in this.calendarAppointment) {
                    if (this.calendarAppointment.hasOwnProperty(key)) {
                        params.append(key, this.calendarAppointment[key]);
                    }
                }

                await fetch('a_save_appointment.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: params.toString()
                })
                .then(response => {
                    return response.json()
                })
                .then(data => {
    
                    if (data.success) {
                        new MessagePopupEngine("Success", "Your Appointment has been scheduled successfully!").instantiate();
                        setTimeout(()=> {
                            window.location.reload();
                        }, 1000);
                    } else {
                        new MessagePopupEngine("Error", "Failed to reschedule appointment. Please try again later.").instantiate();
                    }
                })
                .catch(error => {
                    console.error('Error updating appointment:', error);
                    new MessagePopupEngine("Error", "An error occurred. Please try again.").instantiate();
                });
            }

        } else {
            new MessagePopupEngine("Information", "Please select both a date and a time").instantiate();
        }
    }

    convertTo12HourFormat(date) {

        const [datePart, timePart] = date.split(" ");

        let [hours, minutes] = timePart.split(":");

        hours = parseInt(hours);
        const suffix = hours >= 12 ? "PM" : "AM";
        hours = hours % 12 || 12;

        return `${hours}:${minutes} ${suffix}`;
    }

    convertTo24HourFormat(time) {
        let [timePart, period] = time.split(' ');
        if (!period) period = '';
    
        let [hour, minute, second] = timePart.split(':');
        hour = parseInt(hour);
        const isPM = period === 'PM';
    
        if (isPM && hour !== 12) {
            hour += 12;
        } else if (!isPM && hour === 12) {
            hour = 0;
        }
    
        return `${String(hour).padStart(2, '0')}:${String(minute).padStart(2, '0')}:${second || '00'}`;
    }

    getMonthName(month) {
        const monthNames = [
            "January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
        ];
        return monthNames[month];
    }
}

class CalendarAppointment {
    appointmentID;
    status;
    patientID;
    parentID;
    therapistID;
    serviceID;
    schedule;

    constructor(
        appointmentID = null,
        status = "ongoing",
        patientID = null,
        parentID = null,
        therapistID = null,
        serviceID = null,
        schedule = null,
    ){
        this.appointmentID = appointmentID,
        this.status = status,
        this.patientID = patientID;
        this.parentID = parentID;
        this.therapistID = therapistID;
        this.serviceID = serviceID;
        this.schedule = schedule
    }

}

customElements.define("generic-calendar", GenericCalendar);