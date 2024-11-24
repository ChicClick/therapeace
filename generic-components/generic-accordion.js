class AccordionEngine extends HTMLElement {
    constructor(title, content, value) {
        super();
        // Properties
        this.title = title || "Generic Accordion Title";
        this.content = content || `<p>DEFINE your INNERHTML HERE</p>`;
        this.value = value; // Can be an array for parsing additional content
    }

    render() {
        // Clear any existing content
        this.innerHTML = "";

        // Ensure CSS is loaded
        if (!document.head.querySelector('link[href="./generic-components/generic-accordion.css"]')) {
            const cssLink = document.createElement("link");
            cssLink.rel = "stylesheet";
            cssLink.href = "./generic-components/generic-accordion.css";
            document.head.appendChild(cssLink);
        }

        // Create accordion structure
        const container = document.createElement("div");
        container.classList.add("accordion-container");

        const header = document.createElement("div");
        header.classList.add("accordion-header");
        header.textContent = this.title;
        header.onclick = () => this.toggle(); // Toggle accordion on click

        const contentDiv = document.createElement("div");
        contentDiv.classList.add("accordion-content");
        contentDiv.style.display = "none"; // Initially hidden

        // If `this.value` is an array, parse it; otherwise, use the provided content
        if (Array.isArray(this.value)) {
            this.parse(contentDiv);
        } else {
            contentDiv.innerHTML = this.content; // Plain or predefined content
        }

        container.appendChild(header);
        container.appendChild(contentDiv);
        this.appendChild(container);

        return this;
    }

    toggle() {
        // Toggle the display of the content
        const header = this.querySelector(".accordion-header");
        const content = this.querySelector(".accordion-content");

        // Toggle the expanded state
        header.classList.toggle("expanded");
        content.style.display = header.classList.contains("expanded") ? "block" : "none";
    }

    parse(contentDiv) {
        const ulAccordion = document.createElement("ul");
        ulAccordion.style.padding = "none";

        this.value.forEach(item => {
            const liAccordion = document.createElement("li");
            const liButton = document.createElement("button");

            liButton.textContent = "DETAILS";
            liButton.style.color = "#FFA500";
            liButton.style.textDecoration = "none";
            liButton.style.fontWeight = "600";
            liButton.style.border = "none";
            liButton.style.backgroundColor = "transparent";
            liButton.style.cursor = "pointer";

            liButton.addEventListener("click", () => {
                const cardHTML = `
                <div>
                    <p><strong>Name:</strong> ${item.patient_name}</p>
                    <p><strong>Service:</strong> ${item.service_name}</p>
                    <p><strong>Feedback Date:</strong> ${item.feedback_date}</p>
                    <hr>
                    <p><strong>Feedback Notes:</strong></p>
                    <p>${item.feedback || "No feedback provided"}</p>
                </div>

                `;

                new SideViewBarEngine("DETAILS", cardHTML).render();
            });

            liAccordion.textContent = `Date: ${item.feedback_date}`;
            liAccordion.appendChild(liButton);

            ulAccordion.appendChild(liAccordion);
        });

        // Append parsed content to the provided container
        contentDiv.appendChild(ulAccordion);
    }
}

customElements.define("generic-accordion", AccordionEngine);
