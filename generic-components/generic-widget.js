/*
    Usage, include this js file in the script of HTML or PHP template
    <script src="generic-widget.js"></script>
    create the element on the same template <generic-widget></generic-widget>
    on your js file that has listeners to open the widget. Just declare new WidgetEngine().instatiate() pass the params needed see
    other implementations

    FAQ: Do change WidgetEngine class for future use of polymorphism like the calendar.
*/
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

const communicationMapper = new Map([
    [1, "Picture Communication"],
    [2, "Flash Cards/Symbols"],
    [3, "Ipad/Tablets"],
    [4, "Sign Language/ Body Language"],
    [5, "Communication Books"],
]);

const flexibilityMapper = new Map([
    [1, "Sensory Flexibility"],
    [2, "Adjusting Communication Mode/Tools"],
    [3, "Emotional Sensitivity"]
])

const specializationMap = new Map([
    [1, "Speech"],
    [2, "Occupational"],
    [3, "Physical"],
    [4, "Behavioral"]
]);

class WidgetEngine extends HTMLElement{
    dataset = null;

    title = "Default Widget Title";
    cssLink = document.head.querySelector('link[href="./generic-components/generic-therapist-widget.css"]');
    genericWidget = document.querySelector("generic-widget");
    selectedId = "";

    titleDiv = null;
    containerDiv = null;

    constructor(
        dataset = []
    ){
        super();
        this.dataset = dataset;
    }

    instantiate() {
        if(!this.cssLink) {
            const cssLink = document.createElement("link");
            cssLink.rel = "stylesheet";
            cssLink.href = "./generic-components/generic-widget.css";
        
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
        this.title = title + ` ${this.dataset.length}`;

        const h4Title = document.createElement("h4");

        h4Title.textContent = this.title;

        this.titleDiv.appendChild(h4Title);

        if(!this.dataset || this.dataset.length == 0) {
            this.containerDiv.innerHTML = `
                <p> No Data </p>
            `
            return;
        }

        for(const data of this.dataset) {
            this.createCards(new Therapist(data));
        }
    }

    createCards(data) {
        const card = document.createElement("div");
        card.classList.add("widget-card");
        let daysAvailable = data.days;
        let timesAvailable = data.times;
        
        let daysDisplay = this.daysDisplay(data.days);
        let timesDisplay = this.timesDisplay(data.times);
        let communicationDisplay = this.communicationDisplay(data.communication);
        let flexibilityDisplay = this.flexibilityDisplay(data.flexibility);
        let specialization = this.specializationDisplay(data.specialization);

        if(!daysDisplay) {
             daysDisplay = daysAvailable.map(day => dayMapper.get(day)).join(", ");
        }

        if(!timesDisplay) {
            timesDisplay = timesAvailable.map(time => timeMapper.get(time)).join(", ");
        }
       
        card.innerHTML = `
        <label class="widget-card-label">
            <input type="radio" value="${data.id}" name="widget-card-select" class="widget-radio">
            <div class="widget-card">
                <div class="widget-circle-pic">
                    <img src="images/about 4.jpg">
                </div>
                <!-- Content container -->
                <div class="widget-card-content">
                    <div class="widget-name">${data.name}</div>
                    <div class="widget-specialization">${specialization} Therapist</div>
                    <div class="widget-availability">
                        <span>Days: ${daysDisplay}</span>
                        <span>Times: ${timesDisplay}</span>
                        <span>Communication: ${communicationDisplay}</span>
                        <span>Flexibility: ${flexibilityDisplay}</span>
                    </div>
                </div>
            </div>
        </label>
    `;

    const radioInput = card.querySelector(".widget-radio");
    radioInput.addEventListener("change", () => {
        if (radioInput.checked) {
            const h4Title = document.createElement("h4");

            h4Title.textContent = this.title + ` - (${data.name})`;
            
            this.titleDiv.innerHTML = "";
            this.titleDiv.appendChild(h4Title);

            this.selectedId = data.id;
        }
    });
    
    
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

    communicationDisplay(communication = []) {
        if (!communication || communication.length === 0) {
            return "Not specified";
        }

        return communication.map(id => communicationMapper.get(id)).join(", ");
    }
    
    flexibilityDisplay(flexibility = []) {
        if (!flexibility || flexibility.length === 0) {
            return "Not specified";
        }

        return flexibility.map(id => flexibilityMapper.get(id)).join(", ");
    }

    specializationDisplay(specialization = []) {
        if (!specialization || specialization.length === 0) {
            return "No Specialization to show";
        }

        return specialization.map(id => specializationMap.get(id)).join(", ");
    }
    
}

class Therapist {
    id = "";
    name = "";
    specialization = "";
    days = null;
    times = null;
    flexibility = null;
    communication = null;

    constructor(
        data
    ){
        if(data) {
            this.id = data.therapistID
            this.name = data.therapist_name;
            this.specialization = JSON.parse(data.specialization);
            this.days = JSON.parse(data.days_available);
            this.times = JSON.parse(data.times_available);
            this.flexibility = JSON.parse(data.flexibility);
            this.communication = JSON.parse(data.communication);
        }
    }
}

class ViewReports extends WidgetEngine {
    constructor(){
        super();
    }
}

customElements.define("generic-widget", WidgetEngine);