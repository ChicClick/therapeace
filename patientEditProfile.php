<?php
include 'patient_get_profile.php';
include 'patient_profile_functions.php';

?>   
   <!-- Edit Profile Section (Initially Hidden) -->
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
                            <label for="gender">Gender:</label>
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