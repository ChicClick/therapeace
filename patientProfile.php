<?php
include 'patient_get_profile.php';
include 'patient_profile_functions.php';

?>
    <div class="wrapper">
         <!-- Profile Section -->
         <section id="profile-section">
            <h1>PROFILE</h1>
            <hr>
            <div class="profile-container">
                <div class="profile-header">
                    <div class="profile-info">
                        <h2><?php echo htmlspecialchars($patientName); ?></h2>
                    </div>
                    <a href="#" class="nav-link" data-target="patientEditProfile.php">Edit Profile</a>
                </div>

                <div class="profile-details">
                    <h3>Contact Information</h3>    
                    <div class="info-section">
                        <div class="info-block">
                            <p><strong>Phone:</strong> <?php echo htmlspecialchars($patientPhone); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($patientEmail); ?></p>
                            <p><strong>Address:</strong> <?php echo htmlspecialchars($patientAddress); ?></p>
                        </div>
                        <div class="info-block">
                            <p><strong>Parent/Guardian:</strong> <?php echo htmlspecialchars($parentName); ?></p>
                            <p><strong>Relationship:</strong> <?php echo htmlspecialchars($patientRelationship); ?></p>
                        </div>
                    </div>

                    <h3>Basic Information</h3>  
                    <div class="info-section">
                        <div class="info-block">
                            <p><strong>Birthday:</strong> <?php echo htmlspecialchars($patientBirthday); ?></p>
                        </div>
                        <div class="info-block">
                            <p><strong>Gender:</strong> <?php echo htmlspecialchars($patientGender); ?></p>
                        </div>
                    </div>

                    <h3>About Sessions</h3>
                    <div class="info-section">
                        <div class="info-block">
                        <p><strong>Therapist:</strong> <?php echo htmlspecialchars($therapistName); ?></p>
                        <p><strong>Therapy Type:</strong> <?php echo htmlspecialchars($serviceName); ?></p>
                        </div>
                        <div class="info-block">
                        <p><strong>Schedule:</strong> <?php echo htmlspecialchars($formattedSchedule); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
<div>

