<?php
// Include the database connection
include('db_conn.php');


$therapist_id = $_SESSION['therapist_id']; // Therapist ID from session

// Query to fetch the therapist's information based on therapist_id
$query = "SELECT * FROM therapist WHERE therapistID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $therapist_id); // Bind therapist_id as an integer parameter
$stmt->execute();
$result = $stmt->get_result();

// Check if therapist exists
if ($result->num_rows > 0) {
    // Fetch therapist details
    $therapist = $result->fetch_assoc();
} else {
    echo "Therapist not found!";
    exit();
}

// Extract first name and last name from therapistName
$fullName = $therapist['therapistName'];
$nameParts = explode(' ', $fullName);

// The last word will be the last name
$lastName = array_pop($nameParts); // Remove and return the last word
$firstName = implode(' ', $nameParts); // Join the remaining words for the first name

// Extract birth date components (year, month, day)
$birthYear = date("Y", strtotime($therapist['birthday']));
$birthMonth = date("m", strtotime($therapist['birthday']));
$birthDay = date("d", strtotime($therapist['birthday']));

// Close the prepared statement
$stmt->close();
?>

<form class="form-edit" action="update_profile.php" method="POST">
    <!-- Profile Picture Section -->
    <div class="profile-picture-section">
        <img src="images/about 4.jpg" alt="Profile Picture" class="profile-picture">
    </div>

    <!-- Left Column - Personal Information -->
    <div class="edit-info-section">
        <div class="form-group-profile">
            <label for="firstName">First Name</label>
            <input type="text" id="firstName" name="firstName" class="input-field" value="<?php echo htmlspecialchars($firstName); ?>">
        </div>

        <div class="form-group-profile">
            <label for="lastName">Last Name</label>
            <input type="text" id="lastName" name="lastName" class="input-field" value="<?php echo htmlspecialchars($lastName); ?>">
        </div>

        <div class="form-group-profile">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" class="input-field" value="<?php echo htmlspecialchars($therapist['email']); ?>">
        </div>

        <div class="form-group-profile">
            <label>Availability</label>
            <div class="input-field-service"><?php echo htmlspecialchars($therapist['availability']); ?></div>
        </div>

        <div class="form-group-profile">
            <label for="address">Address</label>
            <textarea id="address" name="address" class="input-field" rows="3" style="font-family:'Poppins';"><?php echo htmlspecialchars($therapist['address']); ?></textarea>
        </div>
    </div>

    <!-- Right Column - Additional Information -->
    <div class="additional-info-section">
        <div class="form-group-profile">
            <label>Gender</label>
            <input type="text" class="input-field" value="<?php echo htmlspecialchars($therapist['gender']); ?>" disabled>
        </div>

        <div class="form-group-profile">
            <label>Date of Birth</label>
            <div class="dob-fields">
                <select class="dob-field" name="birthMonth">
                    <option value="01" <?php echo ($birthMonth == '01') ? 'selected' : ''; ?>>January</option>
                    <option value="02" <?php echo ($birthMonth == '02') ? 'selected' : ''; ?>>February</option>
                    <option value="03" <?php echo ($birthMonth == '03') ? 'selected' : ''; ?>>March</option>
                    <option value="04" <?php echo ($birthMonth == '04') ? 'selected' : ''; ?>>April</option>
                    <option value="05" <?php echo ($birthMonth == '05') ? 'selected' : ''; ?>>May</option>
                    <option value="06" <?php echo ($birthMonth == '06') ? 'selected' : ''; ?>>June</option>
                    <option value="07" <?php echo ($birthMonth == '07') ? 'selected' : ''; ?>>July</option>
                    <option value="08" <?php echo ($birthMonth == '08') ? 'selected' : ''; ?>>August</option>
                    <option value="09" <?php echo ($birthMonth == '09') ? 'selected' : ''; ?>>September</option>
                    <option value="10" <?php echo ($birthMonth == '10') ? 'selected' : ''; ?>>October</option>
                    <option value="11" <?php echo ($birthMonth == '11') ? 'selected' : ''; ?>>November</option>
                    <option value="12" <?php echo ($birthMonth == '12') ? 'selected' : ''; ?>>December</option>
                </select>
                <select class="dob-field" name="birthDay">
                    <?php for ($i = 1; $i <= 31; $i++) : ?>
                        <option value="<?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?>" <?php echo ($birthDay == str_pad($i, 2, '0', STR_PAD_LEFT)) ? 'selected' : ''; ?>><?php echo $i; ?></option>
                    <?php endfor; ?>
                </select>
                <select class="dob-field-year" name="birthYear">
                    <?php for ($i = 1900; $i <= date("Y"); $i++) : ?>
                        <option value="<?php echo $i; ?>" <?php echo ($birthYear == $i) ? 'selected' : ''; ?>><?php echo $i; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
        </div>

        <div class="form-group-profile">
            <label for="phone">Contact No.</label>
            <input type="tel" id="phone" name="phone" class="input-field" value="<?php echo htmlspecialchars($therapist['phone']); ?>">
        </div>

        <div class="form-group-profile">
            <label>Service</label>
            <div class="input-field-service"><?php echo htmlspecialchars($therapist['specialization']); ?></div>
        </div>

        <button class="save-profile" type="submit">Update Profile</button>
    </div>
</form>
