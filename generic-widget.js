
const dayMapper = new Map([
    [0, "Sunday"],
    [1, "Monday"],
    [2, "Tuesday"],
    [3, "Wednesday"],
    [4, "Thursday"],
    [5, "Friday"],
    [6, "Saturday"],
]);

const timeMapper = new Map([
    [6, "6:00 AM"],
    [7, "7:00 AM"],
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
    [19, "7:00 PM"],
    [20, "8:00 PM"],
    [21, "9:00 PM"],
    [22, "10:00 PM"]
]);

class WidgetEngine {
    dataset = null;

    title = "Default Widget Title";
    cssLink = document.head.querySelector('link[href="generic-therapist-widget.css"]');
    genericWidget = document.querySelector("#generic-widget");

    titleDiv = null;
    containerDiv = null;

    constructor(
        dataset = []
    ){
        this.dataset = dataset;
    }

    instantiate() {
        if(!this.cssLink) {
            const cssLink = document.createElement("link");
            cssLink.rel = "stylesheet";
            cssLink.href = "generic-widget.css";
        
            document.head.appendChild(cssLink);  
        }

        const titleW = document.createElement("div");
        titleW.id = "widget-title";

        const container = document.createElement("div");
        container.id = "widget-container";

        if(this.genericWidget) {
            this.genericWidget.innerHTML = "";

            this.genericWidget.classList.add("popup-widget");
            this.genericWidget.appendChild(titleW);
            this.genericWidget.appendChild(container);

            this.titleDiv = document.querySelector("#widget-title");
            this.containerDiv = document.querySelector("#widget-container");
            return;
        }
    }

    createTitle(title) {
        this.title = title;

        const h4Title = document.createElement("h4");

        h4Title.textContent = this.title;

        this.titleDiv.appendChild(h4Title);

        for(const data of this.dataset) {
            this.createCards(new Therapist(data));
        }
    }

    createCards(data) {
        const card = document.createElement("div");
        card.classList.add("widget-card");
        console.log(data);
        let daysAvailable = data.days;
        let timesAvailable = data.times;
        
        console.log(daysAvailable);
        let daysDisplay = this.daysDisplay(data.days);
        let timesDisplay = this.timesDisplay(data.times);


        if(!daysDisplay) {
             daysDisplay = daysAvailable.map(day => dayMapper.get(day)).join(", ");
        }

        if(!timesDisplay) {
            timesDisplay = timesAvailable.map(time => timeMapper.get(time)).join(", ");
        }
       
        card.innerHTML = `
        <div class="widget-circle-pic">
            <img src="images/about 4.jpg">
        </div>
        <!-- Content container -->
        <div class="widget-card-content">
            <div class="widget-name">${data.name}</div>
            <div class="widget-specialization">${data.specialization}</div>
            <div class="widget-availability">
                <span>Days: ${daysDisplay}</span>
                <span>Times: ${timesDisplay}</span>
            </div>
        </div>
        `;
    
        this.containerDiv.appendChild(card);
    }

    daysDisplay(days = []) {
        const weekdays = [1, 2, 3, 4, 5];
        const monToFri = [1, 2, 3, 4, 5, 6];

        if(weekdays.every(day => days.includes(day)) && weekdays.length == days.length) {
            return "Weekdays"
        }
        
        if (monToFri.every(day => days.includes(day))) {
            return "Mon - Sat"
        }

        return null;
    }

    timesDisplay(times = []) {
        const morning = [9, 10, 11];
        const afternoon = [13, 14, 15, 16, 17];
        const wholeDay = [9, 10, 11, 13, 14, 15, 16, 17];

        if (times.every(time => morning.includes(time))) {
            return "9:00 AM to 12:00 PM";
        }

        if (times.every(time => afternoon.includes(time))) {
            return "1:00 PM to 5:00 PM";
        }

        if (times.every(time => wholeDay.includes(time)) && times.length === wholeDay.length) {
            return "Whole Day";
        }
    
        return null;
    }
}

class Therapist {
    name = "";
    specialization = "";
    days = null;
    times = null

    constructor(
        data
    ){
        if(data) {
            this.name = data.therapist_name;
            this.specialization = data.specialization;
            this.days = JSON.parse(data.days_available);
            this.times = JSON.parse(data.times_available);
        }
    }
}