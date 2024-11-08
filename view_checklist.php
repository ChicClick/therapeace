<?php
include 'db_conn.php';

// Get guestID from URL
$guestID = isset($_GET['guestID']) ? (int)$_GET['guestID'] : 0;
$selectedTherapies = [];
$comments = '';

// Step 1: Fetch selected therapies and comments for the guest from the guest table
if ($guestID > 0) {
    $guestQuery = "SELECT matchTherapy, comments FROM guest WHERE guestID = ?";
    $guestStmt = $conn->prepare($guestQuery);
    
    if ($guestStmt === false) {
        die("MySQL prepare error: " . $conn->error);
    }

    $guestStmt->bind_param("i", $guestID);
    $guestStmt->execute();
    
    if ($guestStmt->error) {
        die("MySQL execute error: " . $guestStmt->error);
    }

    $guestStmt->bind_result($matchTherapy, $comments);
    
    if ($guestStmt->fetch()) {
        // Convert comma-separated matchTherapy to an array, check for null or empty string
        $selectedTherapies = !empty($matchTherapy) ? explode(',', $matchTherapy) : [];
    } else {
        echo "No matching records found for guestID: " . htmlspecialchars($guestID);
    }

    $guestStmt->close();
} else {
    echo "Invalid guest ID.";
    exit; // Exit if guest ID is invalid
}

// Step 2: Fetch all therapy options from the `service` table
$therapyOptions = [];
$optionsQuery = "SELECT serviceName FROM service";
$optionsResult = $conn->query($optionsQuery);

if ($optionsResult && $optionsResult->num_rows > 0) {
    while ($row = $optionsResult->fetch_assoc()) {
        $therapyOptions[] = $row['serviceName'];
    }
} else {
    echo "No therapy options available.";
}

$optionsResult->free(); // Free the result set
?>

<div class="checklist-right-section-view">
    <div class="checkbox-group">
        <div class="section-title">Select Suitable Therapy</div>
        <?php foreach ($therapyOptions as $therapy): 
            $isChecked = in_array($therapy, $selectedTherapies); ?>
            <label>
                <input type="checkbox" name="therapies[]" value="<?php echo htmlspecialchars($therapy); ?>" 
                    <?php echo $isChecked ? 'checked' : ''; ?> disabled>
                <?php echo htmlspecialchars($therapy); ?>
            </label>
        <?php endforeach; ?>
    </div>

    <div class="comments-section">
        <div class="section-title">Additional Diagnosis/Comments</div>
        <textarea name="comments" id="comments" placeholder="Enter comments here..." disabled><?php echo htmlspecialchars($comments); ?></textarea>
    </div>
</div>

<?php
$conn->close(); // Close the database connection
?>
