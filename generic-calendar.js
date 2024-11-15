
 class CalendarEngineComponent extends HTMLElement {

    selectedTimeSlots = ['9:00 AM', '10:00 AM', '11:00 AM', '12:00 PM', '1:00 PM', '2:00 PM', '3:00 PM', '4:00 PM', '5:00 PM', '6:00 PM'];
    blockedSelectedTimeSlots = [];
    genericCalendar = document.querySelector("generic-calendar");
    calendarDiv = null;
    timeDiv = null;
    messageDiv = null;
    cssLink = document.head.querySelector('link[href="generic-calendar.css"]');

    constructor(){
        super();
    }

    instantiate() {
        if(!this.cssLink) {
            const cssLink = document.createElement("link");
            cssLink.rel = "stylesheet";
            cssLink.href = "generic-calendar.css";
        
            document.head.appendChild(cssLink);  
        }

        if (this.calendarDiv && this.timeDiv && this.messageDiv) {
            return
        }

        const calendar = document.createElement("div");
        calendar.id = "calendar-div";

        const time = document.createElement("div");
        time.id = "time-div";

        const message = document.createElement("div");
        message.id = "message-div";

        if(this.genericCalendar) {
            this.genericCalendar.appendChild(calendar);
            this.genericCalendar.appendChild(time);
            this.genericCalendar.appendChild(message);

            this.calendarDiv = document.querySelector("#calendar-div");
            this.timeDiv = document.querySelector("#time-div");
            this.messageDiv = document.querySelector("#message-div");
            return;
        }

        throw new Error(`div with id 'generic-calendar was not found'`);
    }

    createCalendarContainer = () => {
        const divContainer = document.createElement("div");
        divContainer.id = "reschedulePopup";
        divContainer.classList.add("popup");
        divContainer.style.display = "block";
    
        const divContent = document.createElement("div");
        divContent.classList.add("popup-content");
    
        const spanClose = document.createElement("span");
        spanClose.classList.add("close");
        spanClose.innerHTML = "&times;";
        spanClose.addEventListener("click", () => {
            this.calendarDiv.innerHTML = "";
        });
    
        const h4Title = document.createElement("h4");
        h4Title.textContent = "Select Available Dates";
    
        const divCalendar = document.createElement("div");
        divCalendar.id = "calendar";

        const divCalendarLoading = document.createElement("div");
        divCalendarLoading.id = "calendar-loading";
        divCalendarLoading.textContent = "Loading";
    
        const divCalendarContainer = document.createElement("div");
        divCalendarContainer.classList.add("calendar-container");
    
        const divMonthNavigation = document.createElement("div");
        divMonthNavigation.classList.add("month-navigation");
    
        const linkPrevMonth = document.createElement("a");
        linkPrevMonth.href = "#";
        linkPrevMonth.id = "prevMonth";
        linkPrevMonth.classList.add("nav-link-month");
        linkPrevMonth.innerHTML = "&lt;";
        linkPrevMonth.addEventListener("click", (e) => {
            e.preventDefault();
                this.dateObj.selectedMonth--;
                if (this.dateObj.selectedMonth < 0) {
                    this.dateObj.selectedMonth = 11;
                    this.dateObj.selectedYear--;
                }
            this.reload();
        })
    
        const pCurrentMonth = document.createElement("p");
        pCurrentMonth.id = "currentMonth";
    
        const linkNextMonth = document.createElement("a");
        linkNextMonth.href = "#";
        linkNextMonth.id = "nextMonth";
        linkNextMonth.classList.add("nav-link-month");
        linkNextMonth.innerHTML = "&gt;";
        linkNextMonth.addEventListener("click", (e) => {
            e.preventDefault();
            this.dateObj.selectedMonth++;
                if (this.dateObj.selectedMonth > 11) {
                    this.dateObj.selectedMonth = 0;
                    this.dateObj.selectedYear++;
                }
            this.reload();
        })
    
        divMonthNavigation.appendChild(linkPrevMonth);
        divMonthNavigation.appendChild(pCurrentMonth);
        divMonthNavigation.appendChild(linkNextMonth);
    
        const divCalendarGrid = document.createElement("div");
        divCalendarGrid.classList.add("calendar-grid");
    
        divCalendarContainer.appendChild(divMonthNavigation);
        divCalendarContainer.appendChild(divCalendarGrid);
        
        divCalendar.appendChild(divCalendarLoading);
        divCalendar.appendChild(divCalendarContainer);

        const buttonProceed = document.createElement("button");
        buttonProceed.id = "proceedButton";
        buttonProceed.type = "button";
        buttonProceed.textContent = "Proceed →";
        buttonProceed.addEventListener("click", ()=> {
            if (this.dateObj.selectedDate) { 
                spanClose.click();
                this.createTimeContainer();
                this.generateAvailableTimes();
            } else {
                alert('Please select a date first.'); // Alert if no date is selected
            }
        })
        
        divContent.appendChild(spanClose);
        divContent.appendChild(h4Title);
        divContent.appendChild(divCalendar);
        divContent.appendChild(buttonProceed);
    
        divContainer.appendChild(divContent);
    
        this.calendarDiv.appendChild(divContainer);
    }

    createTimeContainer = () => {
        const divContainer = document.createElement("div");
        divContainer.id = "timePopup";
        divContainer.classList.add("popup");
        divContainer.style.display = "block";
    
        const divContent = document.createElement("div");
        divContent.classList.add("popup-content");
    
        const spanClose = document.createElement("span");
        spanClose.classList.add("close");
        spanClose.innerHTML = "&times;";
        spanClose.addEventListener("click", () => {
            this.timeDiv.innerHTML = "";
        });
    
        const h4Title = document.createElement("h4");
        h4Title.textContent = "Select Available Times";
    
        const divAvailableTimes = document.createElement("div");
        divAvailableTimes.id = "availableTimes";
        
        const h5Morning = document.createElement("h5");
        h5Morning.textContent = "Morning Sessions";
        const ulMorning = document.createElement("ul");
        ulMorning.id = "morningTimes"; // This is where morning times will be added dynamically
        divAvailableTimes.appendChild(h5Morning);
        divAvailableTimes.appendChild(ulMorning);
        
        const h5Afternoon = document.createElement("h5");
        h5Afternoon.textContent = "Afternoon Sessions";
        const ulAfternoon = document.createElement("ul");
        ulAfternoon.id = "afternoonTimes"; // This is where afternoon times will be added dynamically
        divAvailableTimes.appendChild(h5Afternoon);
        divAvailableTimes.appendChild(ulAfternoon);
    
        const confirmButton = document.createElement("button");
        confirmButton.id = "confirmTimeButton";
        confirmButton.textContent = "Reschedule →";
        confirmButton.addEventListener("click", () =>{
            this.confirmTime();
        });
    
        divContent.appendChild(spanClose);
        divContent.appendChild(h4Title);
        divContent.appendChild(divAvailableTimes);
        divContent.appendChild(confirmButton);
    
        divContainer.appendChild(divContent);
    
        this.timeDiv.appendChild(divContainer);
    }

    createMessagePopup() {
        const divContainer = document.createElement("div");
        divContainer.id = "messagePopup";
        divContainer.classList.add("popup");
        divContainer.style.display = "block";
        
        const divContent = document.createElement("div");
        divContent.classList.add("popup-content");
        
        const closeBtn = document.createElement("span");
        closeBtn.classList.add("close");
        closeBtn.id = "closePopup";
        closeBtn.addEventListener("click", () => {
            this.messageDiv.style.display = "none";
        });
        
        const messageParagraph = document.createElement("p");
        messageParagraph.id = "popupMessage";
        
        const confirmBtn = document.createElement("button");
        confirmBtn.id = "confirmPopup";
        confirmBtn.textContent = "Confirm";
        confirmBtn.addEventListener("click", () => {
            this.messageDiv.style.display = "none";
        });
        
        divContent.appendChild(closeBtn);
        divContent.appendChild(messageParagraph);
        divContent.appendChild(confirmBtn);
        
        divContainer.appendChild(divContent);

        this.messageDiv.appendChild(divContainer);
    }
 }
 
 class GenericCalendar extends CalendarEngineComponent {
    appointmentID = "";
    therapistID = ""
    appointmentDay = 0;
    selectedMonth = new Date().getMonth();
    selectedYear = new Date().getFullYear();

    dateObj;

    constructor(
        appointmentDate = null,
        appointmentID = null,
        therapistID = null
    ) {
        super();
        this.appointmentID = appointmentID;
        this.therapistID = therapistID;
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

        if (this.calendarDiv && this.timeDiv && this.messageDiv) {
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
            const response = await fetch(`booked-dates.php?therapist_id=${this.therapistID}`);
            const data = await response.json();
            
            if (data.bookedDates && data.blockedDates && data.blockedTimes) {
                this.blockedSelectedTimeSlots = [];

                for(const bookedDates of data.bookedDates) {
                    this.blockedSelectedTimeSlots.push(bookedDates);
                }

                console.log(JSON.parse(data.blockedDates));

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
                booked.includes(time)
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

            await fetch('patientRescheduleAppointment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    selectedDatetime: newSchedule,
                    appointmentID: this.appointmentID
                }),
            })
            .then(response => {
                return response.json()
            })
            .then(data => {

                if (data.success) {
                    this.openMessagePopup('Your appointment has been rescheduled successfully.');
                    setTimeout(()=> {
                        window.location.reload();
                    }, 1000);
                } else {
                    this.openMessagePopup('Failed to reschedule appointment. Please try again later.');
                }
            })
            .catch(error => {
                console.error('Error updating appointment:', error);
                this.openMessagePopup('An error occurred. Please try again.');
            });
        } else {
            this.openMessagePopup('Please select both a date and a time.');
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

    openMessagePopup(message) {
        this.createMessagePopup();
        this.messageDiv.style.block;
        document.querySelector("#messagePopup").style.display = "block";
        document.querySelector("#popupMessage").textContent = message;
    }

    getMonthName(month) {
        const monthNames = [
            "January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
        ];
        return monthNames[month];
    }
}

customElements.define("generic-calendar", GenericCalendar);