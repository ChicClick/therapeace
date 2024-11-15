class TableEngine extends HTMLElement {

    cssLink = document.head.querySelector('link[href="./generic-components/generic-table.css"]');
    
    constructor() {
        super(); // Always call `super()` in the constructor of a custom element
        this.data = [];
    }

    static get observedAttributes() {
        return ['data'];
    }

    attributeChangedCallback(name, oldValue, newValue) {
        if (name === 'data') {
            try {
                this.data = JSON.parse(newValue);
                this.render();
            } catch (error) {
                console.error('Invalid data format. Expected JSON:', error);
            }
        }
    }

    connectedCallback() {
        if (this.hasAttribute('data')) {
            const dataAttribute = this.getAttribute('data');
            try {
                this.data = JSON.parse(dataAttribute); // Parse the initial data
                this.render(); // Render the table
            } catch (error) {
                console.error('Invalid data format on initialization:', error);
            }
        }
    }

    render() {
        this.innerHTML = ''; // Clear existing content
        const table = document.createElement('table');
        table.classList.add('generic-table');

        if(!this.cssLink) {
            const cssLink = document.createElement("link");
            cssLink.rel = "stylesheet";
            cssLink.href = "./generic-components/generic-table.css";
        
            document.head.appendChild(cssLink);  
        }

        if (this.data.length > 0) {
            const headerRow = document.createElement('tr');
            Object.keys(this.data[0]).forEach((key) => {
                const th = document.createElement('th');
                th.textContent = key.toUpperCase();
                headerRow.appendChild(th);
            });
            table.appendChild(headerRow);

            this.data.forEach((row) => {
                const tr = document.createElement('tr');
                Object.values(row).forEach((value) => {
                    const td = document.createElement('td');
                    td.textContent = value;
                    tr.appendChild(td);
                });
                table.appendChild(tr);
            });
        } else {
            const noDataMessage = document.createElement('p');
            noDataMessage.textContent = 'No data available';
            this.appendChild(noDataMessage);
        }

        this.appendChild(table);
    }
}

// Define the custom element
customElements.define('generic-table', TableEngine);
