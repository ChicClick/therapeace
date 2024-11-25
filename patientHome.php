    <div class="hero-section">
        <div class="hero-text">
            <h1>Seamless Patient - Care Experience</h1>
            <p>Optimize business processes and services management all in one comprehensive system.</p>
            <button class="enroll-btn" onclick="window.location.href='guestPreScreening.php';">Enroll Now</button>
        </div>
    </div>

    <section class="about-section" id="about">
        <div class="about-images-container">
            <div class="about-image about-image-1">
                <img src="images/about 4.jpg" alt="Image 1">
              </div>
              <div class="about-image about-image-2">
                <img src="images/about 2.jpg" alt="Image 2">
              </div>
              <div class="about-image about-image-3">
                <img src="images/_home.jpg" alt="Image 3">
              </div>
        </div>
        <div class="about-content">
            <h2>About Us</h2>
            <p>
                TheraPeace offers a wide range of functionalities, including appointment scheduling, staff management, and communication tools. Patients can easily book, reschedule, and cancel appointments online, with automated reminders sent to both patients and therapists.
            </p>
            <br>
            <p>  
                TheraPeace helps manage therapists' schedules, availability, and workload, facilitating communication and collaboration among staff members. Its reporting and analytics features provide insights into the therapy center's operations, aiding in informed decision-making. Additionally, the system includes secure communication tools, document management, user authentication, and role-based access control to protect sensitive information. With its ability to integrate with other healthcare systems and tools, along with a customizable interface, TheraPeace enhances the efficiency and effectiveness of therapy centers by reducing administrative burdens, improving patient care, and optimizing overall operations.
            </p>
        </div>
        <div class="bee-trail-wrapper">
            <!-- Create a jagged or curved flight path for the bees -->
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1800 200">
                <path class="bee-trail-path" d="M0,150 C300,50, 600,250, 900,150 C1200,50, 1500,250, 1800,150"/>
                <!-- Add two bee icons -->
                <image class="bee bee-1" href="images/TheraPeace Logo.svg" width="30" height="30" />
                <image class="bee bee-2" href="images/TheraPeace Logo.svg" width="30" height="30" />
            </svg>
        </div>
    </section>

    <section class="services-section" id="services">
        <h2>Services Offered</h2>
        <div class="services-grid">
            <div class="service-item hexagon">
                <div class="service-content">
                    <h3>Behavioral Therapy</h3>
                    <i class="service-icon fa-solid fa-hand-holding-heart"></i>
                    <div class="service-description">
                        <p>Therapy to address behavioral issues</p>
                    </div>
                </div>
            </div>
            <div class="service-item hexagon">
                <div class="service-content">
                    <h3>Speech Therapy</h3>
                    <i class="service-icon fa-solid fa-comments"></i>
                    <div class="service-description">
                        <p>To improve speech and communication skills</p>
                    </div>
                </div>
            </div>
            <div class="service-item hexagon">
                <div class="service-content">
                    <h3>Free Screening</h3>
                    <i class="service-icon fa-solid fa-clipboard-list"></i>
                    <div class="service-description">
                        <p>Initial assessment of therapy needs</p>
                    </div>
                </div>
            </div>
            <div class="service-item hexagon">
                <div class="service-content">
                    <h3>Special Education</h3>
                    <i class="service-icon fa-solid fa-book-open"></i>
                    <div class="service-description">
                        <p>Description about Special Education</p>
                    </div>
                </div>
            </div>
            <div class="service-item hexagon">
                <div class="service-content">
                    <h3>Physical Therapy</h3>
                    <i class="service-icon fa-solid fa-wheelchair-move"></i>
                    <div class="service-description">
                        <p>Improvement of physical movement and strength</p>
                    </div>
                </div>
            </div>
            <div class="service-item hexagon">
                <div class="service-content">
                    <h3>Occupational Therapy</h3>
                    <i class="service-icon fa-solid fa-hands-holding-child"></i>
                    <div class="service-description">
                        <p>Therapy to improve daily living skills</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="feedback-section">
        <h2>Parent's Feedback</h2>
        <div class="feedback-carousel-container">
        <button class="carousel-control left" onclick="moveCarousel('left')">&lt;</button>
                <div class="feedback-carousel">
                    <?php
                    // Include the PHP file that handles feedback display
                    include 'fetch_parentfeedbacks.php';
                    ?>
                </div>
                <!-- Leave Feedback Button -->
                <button class="carousel-control right" onclick="moveCarousel('right')">&gt;</button>
        </div>
    </section>


    