<?php
include 'config.php';
include 'db_conn.php';
include 'generic_aws.php';

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
$parentName = '';
if ($parentID) {
    $sql = "SELECT parentName FROM parent WHERE parentID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $parentID);
    $stmt->execute();
    $stmt->bind_result($parentName);
    $stmt->fetch();
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $updatedPhone = $_POST['phone'];
    $updatedEmail = $_POST['email'];
    $updatedAddress = $_POST['address'];
    $updatedName = $_POST['patientName'];
    $updatedParentName = $_POST['parentName'];
    $updatedRelationship = $_POST['relationship'];

    $s3 = new AwsS3("image");

    if (isset($_FILES['image']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
        $imageTmpPath = $_FILES['image']['tmp_name'];
        $imageExt = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $imageName = uniqid('img_', true) . '.' . $imageExt;
        $s3Key = 'images/' . $imageName;

        $profileImagePath = $s3->uploadFile($imageTmpPath, $s3Key);
    } else {
        $profileImagePath = $profileImage;
    }

    updatePatientProfile($patientID, $updatedName, $updatedEmail, $updatedPhone, $updatedAddress, $updatedParentName, $updatedRelationship, $profileImagePath);

    echo json_encode([
        'success' => true,
        'message' => 'Profile Updated Successfully.'
    ]);
    exit();
}

// Function to update patient profile and parent name
function updatePatientProfile($patientID, $name, $email, $phone, $address, $parentName, $relationship, $profileImagePath) {
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
?>
