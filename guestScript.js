document.addEventListener('DOMContentLoaded', function () {
    const categories = document.querySelectorAll('.category-section');
    const totalCategories = categories.length; 
    let currentCategoryIndex = 0;

    // Initially display the first category and hide others
    categories.forEach((category, index) => {
        category.style.display = (index === 0) ? 'block' : 'none';
    });


    document.getElementById('submit-button').style.display = 'none';

    // Initialize step indicator (display first set of steps)
    fixStepIndicator(currentCategoryIndex);

    // Function to show the next category
    window.nextCategory = function () {
        if (!validateForm()) return false; 

        categories[currentCategoryIndex].style.display = 'none';

        if (currentCategoryIndex < totalCategories - 1) {
            currentCategoryIndex++;
            categories[currentCategoryIndex].style.display = 'block';

            // Update the step indicator
            fixStepIndicator(currentCategoryIndex);

            if (currentCategoryIndex === totalCategories - 1) {
                document.getElementById('submit-button').style.display = 'block';
            } else {
                document.getElementById('submit-button').style.display = 'none';
            }
        }
    };

    window.prevCategory = function () {
        if (currentCategoryIndex > 0) {
            categories[currentCategoryIndex].style.display = 'none';
            currentCategoryIndex--;
            categories[currentCategoryIndex].style.display = 'block';

            fixStepIndicator(currentCategoryIndex);
            document.getElementById('submit-button').style.display = 'none';
        }
    };

    // function validateForm() {
    //     var inputs, valid = true;
    //     inputs = categories[currentCategoryIndex].getElementsByTagName("input");
        
    //     for (var i = 0; i < inputs.length; i++) {
    //         if (inputs[i].value == "") {
    //             inputs[i].className += " invalid";
    //             valid = false;
    //         }
    //     }

    //     if (valid) {
    //         document.getElementsByClassName("step")[currentCategoryIndex].className += " finish";
    //     }

    //     return valid;
    // }

    function validateForm() {
        return true; // Always allows form submission
    }
    

    function fixStepIndicator(n) {
        var steps = document.getElementsByClassName("step");
        var lines = document.getElementsByClassName("line");
      
        // Clear active and finish classes from all steps and reset line colors
        for (let i = 0; i < steps.length; i++) {
            steps[i].className = steps[i].className.replace(" active", "").replace(" finish", "");
        }
        for (let i = 0; i < lines.length; i++) {
            lines[i].style.backgroundColor = '#ccc'; // Reset line color
        }
    
        // Initially hide steps 5–9
        if (n < 4) {
            // Show only steps 1–4
            for (let i = 0; i < 9; i++) {
                if (i < 4) {
                    steps[i].style.display = 'inline-block'; // Show steps 1–4
                    if (i < 3) lines[i].style.display = 'inline-block'; // Show connecting lines 1–3
                } else {
                    steps[i].style.display = 'none'; // Hide steps 5–9 initially
                    if (i < 8) lines[i].style.display = 'none'; // Hide lines 4–8
                }
            }
        } else {
            // Once the user reaches step 5, show steps 5–9 and hide steps 1–4
            for (let i = 0; i < 9; i++) {
                if (i < 4) {
                    steps[i].style.display = 'none'; // Hide steps 1–4
                    if (i < 3) lines[i].style.display = 'none'; // Hide lines 1–3
                } else {
                    steps[i].style.display = 'inline-block'; // Show steps 5–9
                    if (i < 8) lines[i].style.display = 'inline-block'; // Show lines 4–8
                }
            }
        }
    
        // Mark the current step as active
        steps[n].className += " active";
    
        // Update the progress lines for completed steps
        for (let i = 0; i < n; i++) {
            steps[i].className += " finish"; // Mark finished steps
            if (i < lines.length) {
                lines[i].style.backgroundColor = '#ffcc00'; // Change line color to green
            }
        }
    }
});

// Show the pre-screening form
function showPreScreeningForm() {
    // Hide the pre-screening card and show the pre-screening form
    document.querySelector('.pre-screening-section').classList.add('hidden');
    document.querySelector('#pre-screening-form').classList.remove('hidden');
    document.querySelector('.test-intro').classList.remove('hidden');
}



function showModal(responseID) {
    // Set the responseID value in the hidden input field
    document.getElementById('responseID').value = responseID;

    // Show the modal
    document.getElementById('success-modal').style.display = 'block';
    document.body.style.overflow = "hidden"; // Disable scroll
}

// Function to close the modal
function closeModal() {
    document.getElementById('success-modal').style.display = 'none';
    document.body.style.overflow = "auto"; // Enable scroll
}



const navbar = document.querySelector('nav');
const scrollTopBtn = document.querySelector('.scroll-top'); 
let lastScrollTop = 0;
const scrollThreshold = 100;

window.addEventListener('scroll', function() {
    let scrollTop = window.pageYOffset || document.documentElement.scrollTop;

    if (scrollTop > scrollThreshold) {
        if (scrollTop > lastScrollTop) {
            // User is scrolling down and has passed the threshold - hide the navbar
            navbar.style.transform = 'translateY(-100%)';
            navbar.style.opacity = '0';
        } else {
            // User is scrolling up - show the navbar
            navbar.style.transform = 'translateY(0)';
            navbar.style.opacity = '1';
        }
    }

    lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;

    const navLinks = document.querySelector(".nav-links");
    // Only close the menu if it's open (active)
    if (navLinks.classList.contains("active")) {
        navLinks.classList.remove("active");
    }
});

if(scrollTopBtn) {
    window.addEventListener('scroll', () => {
    if (window.scrollY > 50) {
        navbar.classList.add('scrolled'); 
    } else {
        navbar.classList.remove('scrolled'); 
    }

    if (window.scrollY > 300) { 
        scrollTopBtn.style.display = 'block';
    } else {
        scrollTopBtn.style.display = 'none';
    }

    // Add the click event to scroll to the top
    scrollTopBtn.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth' // Smooth scroll to top
        });
    });
});

}


const carousel = document.querySelector('.feedback-carousel');
    const leftControl = document.querySelector('.carousel-control.left');
    const rightControl = document.querySelector('.carousel-control.right');
    let currentIndex = 0;

    function getItems() {
        return document.querySelectorAll('.feedback-item'); // Dynamically get the items
    }

    function updateCarousel() {
        const items = getItems();
        const totalItems = items.length;

        if (totalItems > 0) {
            const offset = -currentIndex * 100; // Translate based on percentage to show one item
            carousel.style.transform = `translateX(${offset}%)`;
        }
    }

    function showNext() {
        const items = getItems();
        const totalItems = items.length;
        if (totalItems > 0) {
            currentIndex = (currentIndex + 1) % totalItems;
            updateCarousel();
        }
    }

    function showPrev() {
        const items = getItems();
        const totalItems = items.length;
        if (totalItems > 0) {
            currentIndex = (currentIndex - 1 + totalItems) % totalItems;
            updateCarousel();
        }
    }

    leftControl.addEventListener('click', showPrev);
    rightControl.addEventListener('click', showNext);

    // Optional: Automatic sliding
    setInterval(showNext, 5000); // Adjust timing as desired

    // Initialize carousel position
    updateCarousel();


// Create an IntersectionObserver to observe when images come into view
const observer = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
        // Check if the image is in the viewport (intersecting)
        if (entry.isIntersecting) {
            entry.target.classList.add('visible'); // Add the 'visible' class to animate
            observer.unobserve(entry.target); // Stop observing once it's in view
        }
    });
}, {
    threshold: 0.5 // Trigger the observer when at least 50% of the image is visible
});

// Observe each image in the "about-image" class
document.querySelectorAll('.about-image').forEach(image => {
    observer.observe(image);
});

document.getElementById("hamburger-menu").addEventListener("click", function() {
    document.querySelector(".nav-links").classList.toggle("active");
});

