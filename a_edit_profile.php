<?php
// Redirect if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: adminlogin.php");
    exit;
}

// Include database connection
include('db_conn.php');

// Get the logged-in admin's username
$username = $_SESSION['username'];

// Query to fetch the admin's information based on username
$query = "SELECT * FROM admin WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// Check if admin exists
if ($result->num_rows > 0) {
    // Fetch admin details
    $admin = $result->fetch_assoc();
    $firstname = $admin['firstname'];
} else {
    echo "Admin not found!";
    exit();
}

// Extract birth date components
$birthYear = date("Y", strtotime($admin['birthday']));
$birthMonth = date("m", strtotime($admin['birthday']));
$birthDay = date("d", strtotime($admin['birthday']));

// Close statement
$stmt->close();
?>

<form class="form-edit" action="a_update_profile.php" method="POST">
    <!-- Profile Picture Section -->
    <div class="profile-picture-section">
        <!-- Removed the image -->
    </div>

    <!-- Left Column - Personal Information -->
    <div class="edit-info-section">
        <div class="form-group-profile">
            <label for="firstName">First Name</label>
            <input type="text" id="firstName" name="firstname" class="edit-input-field" value="<?php echo htmlspecialchars($admin['firstname'] ?? ''); ?>">
        </div>

        <div class="form-group-profile">
            <label for="lastName">Last Name</label>
            <input type="text" id="lastName" name="lastname" class="edit-input-field" value="<?php echo htmlspecialchars($admin['lastname'] ?? ''); ?>">
        </div>

        <div class="form-group-profile">
            <label for="username">Email</label>
            <input type="email" id="username" name="username" class="edit-input-field" value="<?php echo htmlspecialchars($admin['username'] ?? ''); ?>">
        </div>

        <div class="form-group-profile">
            <label for="address">Address</label>
            <textarea id="address" name="address" class="edit-input-field" rows="3" style="font-family:'Poppins';"><?php echo htmlspecialchars($admin['address'] ?? ''); ?></textarea>
        </div>
    </div>

    <!-- Right Column - Additional Information -->
    <div class="additional-info-section">
        <div class="form-group-profile">
            <label>Sex</label>
            <input type="text" class="edit-input-field" value="<?php echo htmlspecialchars($admin['gender']); ?>" disabled>
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
            <label for="phoneNumber">Contact No.</label>
            <input type="tel" id="phoneNumber" name="phoneNumber" class="edit-input-field" value="<?php echo htmlspecialchars($admin['phoneNumber']); ?>">
        </div>

        <button class="save-profile" type="submit">Update Profile</button>
    </div>
</form>

