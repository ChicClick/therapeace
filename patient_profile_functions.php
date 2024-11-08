<?php
include 'config.php';
include 'db_conn.php';

// Retrieve patient ID from session (assuming patientID is stored in session after login)
if (isset($_SESSION['patientID'])) {
    $patientID = $_SESSION['patientID'];
} else {
    die("Error: Patient not logged in.");
}

// Fetch patient details, including the profile image
$sql = "SELECT patientName, image, phone, email, address, parentID, relationship FROM patient WHERE patientID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $patientID);
$stmt->execute();
$result = $stmt->get_result();
$patientData = $result->fetch_assoc();
$stmt->close();

// Store retrieved values
$patientName = $patientData['patientName'];
$profileImage = $patientData['image'];
$phone = $patientData['phone'];
$email = $patientData['email'];
$address = $patientData['address'];
$parentID = $patientData['parentID'];
$patientRelationship = $patientData['relationship'];

// Fetch parent name if available
if ($parentID) {
    $sql = "SELECT parentName FROM parent WHERE parentID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $parentID);
    $stmt->execute();
    $stmt->bind_result($parentName);
    $stmt->fetch();
    $stmt->close();
} else {
    $parentName = '';
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $updatedPhone = $_POST['phone'];
    $updatedEmail = $_POST['email'];
    $updatedAddress = $_POST['address'];
    $updatedName = $_POST['patientName'];
    $updatedParentName = $_POST['parentName'];
    $updatedRelationship = $_POST['relationship'];


    if (isset($_FILES['profileImage']) && $_FILES['profileImage']['error'] == UPLOAD_ERR_OK) {
    $targetDir = "uploads/";
    $imageFileType = strtolower(pathinfo($_FILES['profileImage']['name'], PATHINFO_EXTENSION));
    $targetFile = $targetDir . "profile_" . $patientID . "." . $imageFileType;

    if (move_uploaded_file($_FILES['profileImage']['tmp_name'], $targetFile)) {
        $profileImagePath = $targetFile; // Set the new image path
    } else {
        die("Error uploading profile image.");
    }
} else {
    $profileImagePath = $profileImage; // Keep the old image if no new image uploaded
}


    // Update patient profile, including the image path if uploaded
    updatePatientProfile($patientID, $updatedName, $updatedEmail, $updatedPhone, $updatedAddress, $updatedParentName, $profileImagePath);

    header("Location: patientProfile.php?success=1");
    exit();
}

// Function to update patient profile and parent name
function updatePatientProfile($patientID, $name, $email, $phone, $address, $parentName, $relationship, $profileImagePath = null) {
    global $conn;

    $conn->begin_transaction();

    try {
        // Update SQL with conditional image path
        $sql = "UPDATE patient SET patientName = ?, email = ?, phone = ?, address = ?, relationship = ?";
        $params = [$name, $email, $phone, $address, $relationship];
        $types = "sssss"; // Update types to include the relationship

        if ($profileImagePath) {
            $sql .= ", image = ?";
            $params[] = $profileImagePath;
            $types .= "s";
        }
        $sql .= " WHERE patientID = ?";
        $params[] = $patientID;
        $types .= "s";

        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            throw new Exception('Error preparing statement: ' . htmlspecialchars($conn->error));
        }

        $stmt->bind_param($types, ...$params);
        if (!$stmt->execute()) {
            throw new Exception('Error executing update: ' . htmlspecialchars($stmt->error));
        }
        $stmt->close();

        // Update the parent name if a parent ID exists
        $stmt = $conn->prepare("SELECT parentID FROM patient WHERE patientID = ?");
        $stmt->bind_param("s", $patientID);
        $stmt->execute();
        $stmt->bind_result($parentID);
        $stmt->fetch();
        $stmt->close();

        if ($parentID) {
            $sql = "UPDATE parent SET parentName = ? WHERE parentID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $parentName, $parentID);
            if (!$stmt->execute()) {
                throw new Exception('Error updating parent name: ' . htmlspecialchars($stmt->error));
            }
            $stmt->close();
        }

        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        die('Error: ' . $e->getMessage());
    }
}


// Assuming $patientSchedule is already defined
$scheduleDate = $patientSchedule;

// Function to format the schedule date
function formatSchedule($scheduleDate) {
    $timestamp = strtotime($scheduleDate);
    $dayOfWeek = date("l", $timestamp);
    return "Every " . $dayOfWeek;
}

$formattedSchedule = formatSchedule($scheduleDate);
?>
