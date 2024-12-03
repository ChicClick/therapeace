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
                    <button id="edit-button" class="edit-button" onclick="toggleEditProfile()">Edit Profile</button>
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
                            <p><strong>Sex:</strong> <?php echo htmlspecialchars($patientGender); ?></p>
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

        <section id="edit-profile-section" style="display: none;">
            <h1>EDIT PROFILE</h1>
            <hr>
            <div class="profile-container">
                <h3>BASIC INFORMATION</h3>
                <form action="patient_profile_functions.php" method="POST">

                    <div class="input-group">
                    <div class="input-field">
                        <label for="patientName">Patient Name:</label>
                        <input type="text" name="patientName" id="patientName" value="<?php echo htmlspecialchars($patientName); ?>" required>
                    </div>
                    <div class="input-field">
                        <label for="parentName">Parent Name:</label>
                        <input type="text" name="parentName" id="parentName" value="<?php echo htmlspecialchars($parentName); ?>" required>
                    </div>
                    <div class="input-field">
                        <label for="relationship">Relationship:</label>
                        <input type="text" name="relationship" id="relationship" value="<?php echo htmlspecialchars($patientRelationship); ?>" required>
                    </div>
                    </div>

                    <!-- Grouping Phone, Email, Birthday, and Gender side by side -->
                    <div class="input-group">
                        <div class="input-field">
                            <label for="phone">Phone:</label>
                            <input type="text" name="phone" id="phone" value="<?php echo htmlspecialchars($patientPhone); ?>" required>
                        </div>
                        <div class="input-field">
                            <label for="email">Email:</label>
                            <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($patientEmail); ?>" required>
                        </div>
                        <div class="input-field">
                            <label for="birthday">Birthday:</label>
                            <input type="date" name="birthday" id="birthday" value="<?php echo htmlspecialchars($patientBirthday); ?>" required>
                        </div>
                        <div class="input-field">
                            <label for="gender">Sex:</label>
                            <input type="text" name="gender" id="gender" value="<?php echo htmlspecialchars($patientGender); ?>" required>
                        </div>
                    </div>
                    <div class="input-field">
                        <label for="address">Address:</label>
                        <input type="text" name="address" id="address" value="<?php echo htmlspecialchars($patientAddress); ?>" required>
                    </div>

                    <button type="submit">Save Changes</button>
                </form>
            </div>
        </section>
<div>

