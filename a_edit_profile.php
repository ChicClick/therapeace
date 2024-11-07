<?php
// Include the database connection
include('db_conn.php');

// Start the session to access the admin's ID
session_start();
$admin_id = $_SESSION['adminID']; // Admin ID from session

// Query to fetch the admin's information based on admin_id
$query = "SELECT * FROM admin WHERE adminID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if admin exists
if ($result->num_rows > 0) {
    // Fetch admin details
    $admin = $result->fetch_assoc();
} else {
    echo "Admin not found!";
    exit();
}

// Extract birth date components (year, month, day)
$birthYear = date("Y", strtotime($admin['birthday']));
$birthMonth = date("m", strtotime($admin['birthday']));
$birthDay = date("d", strtotime($admin['birthday']));

// Close the prepared statement
$stmt->close();
?>

<form class="form-edit" action="a_update_profile.php" method="POST">
    <!-- Profile Picture Section -->
    <div class="profile-picture-section">
        <img src="images/about 4.jpg" alt="Profile Picture" class="profile-picture">
    </div>

    <!-- Left Column - Personal Information -->
    <div class="edit-info-section">
        <div class="form-group-profile">
            <label for="firstName">First Name</label>
            <input type="text" id="firstName" name="firstName" class="input-field" value="<?php echo htmlspecialchars($admin['firstname']); ?>">
        </div>

        <div class="form-group-profile">
            <label for="lastName">Last Name</label>
            <input type="text" id="lastName" name="lastName" class="input-field" value="<?php echo htmlspecialchars($admin['lastname']); ?>">
        </div>

        <div class="form-group-profile">
            <label for="username">Email</label>
            <input type="email" id="username" name="username" class="input-field" value="<?php echo htmlspecialchars($admin['username']); ?>">
        </div>

        <div class="form-group-profile">
            <label for="address">Address</label>
            <textarea id="address" name="address" class="input-field" rows="3" style="font-family:'Poppins';"><?php echo htmlspecialchars($admin['address']); ?></textarea>
        </div>
    </div>

    <!-- Right Column - Additional Information -->
    <div class="additional-info-section">
        <div class="form-group-profile">
            <label>Gender</label>
            <input type="text" class="input-field" value="<?php echo htmlspecialchars($admin['gender']); ?>" disabled>
        </div>

        <div class="form-group-profile">
            <label>Date of Birth</label>
            <div class="dob-fields">
                <select class="dob-field" name="birthMonth">
                    <option value="01" <?php echo ($birthMonth == '01') ? 'selected' : ''; ?>>January</option>
                    <!-- Add the rest of the months here... -->
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
            <label for="phoneNumber">Contact No.</label>
            <input type="tel" id="phoneNumber" name="phoneNumber" class="input-field" value="<?php echo htmlspecialchars($admin['phoneNumber']); ?>">
        </div>

        <button type="submit">Update Profile</button>
    </div>
</form>
