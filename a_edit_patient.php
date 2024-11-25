<?php
include('db_conn.php');
include('config.php');
// Redirect if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: adminlogin.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $patientID = $_POST['patientID'];
    $patientName = $_POST['patientName'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $birthday = $_POST['birthday'];
    $address = $_POST['address'];
    $gender = $_POST['gender'];
    $parentID = $_POST['parentID'];
    $relationship = $_POST['relationship'];
    $status = $_POST['status'];
    $imageName = null;

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "images/";
        $imageName = basename($_FILES['image']['name']);
        $targetFile = $targetDir . $imageName;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            echo "Error uploading the file.";
            exit;
        }
    }

    $sql = "UPDATE patient SET 
                patientName = ?, 
                phone = ?, 
                email = ?, 
                birthday = ?, 
                address = ?, 
                gender = ?, 
                parentID = ?, 
                relationship = ?, 
                status = ?";

    if ($imageName) {
        $sql .= ", profile_picture = ?";
    }

    $sql .= " WHERE patientID = ?";

    if ($stmt = $conn->prepare($sql)) {
        if ($imageName) {
            $stmt->bind_param("ssssssssssi", $patientName, $phone, $email, $birthday, $address, $gender, $parentID, $relationship, $status, $imageName, $patientID);
        } else {
            $stmt->bind_param("sssssssssi", $patientName, $phone, $email, $birthday, $address, $gender, $parentID, $relationship, $status, $patientID);
        }

        if ($stmt->execute()) {
            header("Location: admindashboard.php?active=patients-information-section");
            exit;
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error: " . $conn->error;
    }
}

$conn->close();
?>
