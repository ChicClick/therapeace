class SideViewBarEngine extends HTMLElement {
    cssLink = document.head.querySelector('link[href="./generic-components/generic-side-view-bar.css"]');
    containerDiv = document.querySelector("generic-side-view-bar");

    constructor(
        title = "SIDEBAR TITLE",
        content  = "Dito yung HTML o TEXT",
        size = null
    ) {
        super();
        this.title = title;
        this.content = content;
        this.size = size;
    }

    render() {
        if (!this.containerDiv) {
            throw new Error("Please attach generic-side-view-bar element to html file along with the script");
        }

        this.containerDiv.innerHTML = '';
        this.containerDiv.classList.add("right-view");

        const container = document.createElement('div');
        container.classList.add("side-view-bar-container");

        if(this.size) {
            container.classList.add("view-lg");
        }

        const header = document.createElement('div');
        header.classList.add('side-view-bar-header');

        const title = document.createElement('div');
        title.classList.add('side-view-bar-title');
        title.textContent = this.title;

        const closeButton = document.createElement('button');
        closeButton.classList.add('side-view-bar-close-btn');
        closeButton.textContent = '✖'; // Use an icon or text for the close button
        closeButton.onclick = () => this.containerDiv.close(); // Attach close functionality

        header.appendChild(title);
        header.appendChild(closeButton);
        container.appendChild(header);

        const content = document.createElement('div');
        content.classList.add('side-view-bar-content');
        content.innerHTML = this.content;
        container.appendChild(content);

        if (!this.cssLink) {
            const cssLink = document.createElement("link");
            cssLink.rel = "stylesheet";
            cssLink.href = "./generic-components/generic-side-view-bar.css";
            document.head.appendChild(cssLink);
        }

        const backdropDiv = document.createElement("div");
        backdropDiv.classList.add("backdrop");

        this.containerDiv.appendChild(backdropDiv);
        this.containerDiv.appendChild(container);

        setTimeout(() => {
            this.containerDiv.querySelector('.side-view-bar-container').classList.add('visible');
        }, 100);
    }

    close() {
        const container = this.containerDiv.querySelector('.side-view-bar-container');
        const backdrop = this.containerDiv.querySelector('.backdrop');
        if (container && backdrop) {
            container.classList.remove('visible');
            setTimeout(() => {
                backdrop.remove();
                container.remove();
            }, 300);
        }
    }
}

customElements.define("generic-side-view-bar", SideViewBarEngine);