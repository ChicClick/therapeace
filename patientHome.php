<section class="hero-section">
        <div class="home-image-container">
        <img src="images/home.png" alt="TheraPeace Care" class="home-image">
        </div>
        <div class="hero-content">
        <div class="hero-text">
            <h1>Seamless Patient - 
                Care Experience</h1>
            <p>A management system for TheraPeace. Optimize business processes and services management all in one comprehensive system.</p>
            <button class="enroll-btn" onclick="window.location.href='patientAppointments.php';">View Appointments</button>
        </div>
        </div>
    </section>

    <section class="about-section" id="about">
        <h2>About Us</h2>
        <div class="about-content">
            <p>
            TheraPeace offers a wide range of functionalities, including appointment scheduling, staff management, and communication tools. Patients can easily book, reschedule, and cancel appointments online, with automated reminders sent to both patients and therapists. TheraPeace helps manage therapists' schedules, availability, and workload, facilitating communication and collaboration among staff members. Its reporting and analytics features provide insights into the therapy center's operations, aiding in informed decision-making. Additionally, the system includes secure communication tools, document management, user authentication, and role-based access control to protect sensitive information. With its ability to integrate with other healthcare systems and tools, along with a customizable interface, TheraPeace enhances the efficiency and effectiveness of therapy centers by reducing administrative burdens, improving patient care, and optimizing overall operations.
            </p>
        </div>
        <div class="about-images">
        <img src="images/about us image.png" alt="About Us Image">
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
        <div class="feedback-carousel">
            <?php
            // Include the PHP file that handles feedback display
            include 'fetch_feedbacks.php';
            ?>
        </div>
            <!-- Leave Feedback Button -->
            <button id="leave-feedback" class="leave-feedback" onclick="openFeedbackForm()">
                <i class="fas fa-comment-dots"></i> Leave Feedback
            </button>
    </section>


    