class MessagePopupEngine extends HTMLElement {
    message = "";

    title = "Default Widget Title";

    titleDiv = null;
    containerDiv = null;

    genericMessagePopup = document.querySelector("generic-message-popup");
    cssLink = document.head.querySelector('link[href="generic-message-popup.css"]');
    

    constructor(
        title, 
        message
    ){  
        super();
        this.title = title;
        this.message = message;
    }

    instantiate() {
        if(!this.cssLink) {
            const cssLink = document.createElement("link");
            cssLink.rel = "stylesheet";
            cssLink.href = "generic-message-popup.css";
        
            document.head.appendChild(cssLink);  
        }

        const titleW = document.createElement("div");
        titleW.id = "message-popup-title";

        const container = document.createElement("div");
        container.id = "message-popup-container";

        if(this.genericMessagePopup) {
            this.genericMessagePopup.innerHTML = "";

            this.genericMessagePopup.classList.add("generic-message-popup");
            this.genericMessagePopup.appendChild(titleW);
            this.genericMessagePopup.appendChild(container);

            this.titleDiv = document.querySelector("#message-popup-title");
            this.containerDiv = document.querySelector("#message-popup-container");

            this.createTitle();
            this.createMessage();
            this.createCloseButton();
            return;
        }
    }

    createTitle() {
        const h4Title = document.createElement("h4");

        h4Title.textContent = this.title;

        this.titleDiv.appendChild(h4Title);
    }

    createMessage() {
        this.containerDiv.innerHTML = this.message;
    }

    createCloseButton() {
        const buttonOk = document.createElement("button");
        buttonOk.id = "okButton";
        buttonOk.type = "button";
        buttonOk.textContent = "OK";
        buttonOk.addEventListener("click", ()=> {
            this.genericMessagePopup.innerHTML = "";
            this.genericMessagePopup.classList.remove("generic-message-popup");
        });

        this.genericMessagePopup.appendChild(buttonOk);
    }
}

customElements.define("generic-message-popup", MessagePopupEngine);