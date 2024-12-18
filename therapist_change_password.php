<?php
include 'config.php'; 
include 'db_conn.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$messageDisplay = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $therapistID = $_SESSION['therapist_id'] ?? '';  // Get patient ID from session

    if (empty($patientId)) {
        $messageDisplay = 'Therapsit ID is required.';
    } else {

        $query = "SELECT password_hash FROM therapist WHERE therapistID = ?";
        $stmt = $conn->prepare($query);
    
        if ($stmt) {

            $stmt->bind_param("s", $therapistID);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {

                $stmt->bind_result($storedPassword);
                $stmt->fetch();

                if (isset($_POST['oldPassword'])) {
                    $oldPassword = $_POST['oldPassword'];

                    if (password_verify($oldPassword, $storedPassword)) {
            
                        if (isset($_POST['newPassword']) && isset($_POST['confirmPassword'])) {
                            $newPassword = $_POST['newPassword'];
                            $confirmPassword = $_POST['confirmPassword'];
                            
                            if ($newPassword === $confirmPassword) {

                                $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
                                
                                $updateQuery = "UPDATE therapist SET password_hash = ? WHERE therapistID = ?";
                                $updateStmt = $conn->prepare($updateQuery);
                                 
                                if ($updateStmt) {
    
                                    $updateStmt->bind_param("ss", $newPasswordHash, $therapistID);
                                    
                                    if ($updateStmt->execute()) {
                                        $messageDisplay = 'Password updated successfully.';
                                    } else {
                                        $messageDisplay = 'Error updating password.';
                                    }
                                } else {
                                    $messageDisplay = 'Error preparing update statement.';
                                }
                            } else {
                                $messageDisplay = 'New password and confirm password do not match.';
                            }
                        } else {
                            $messageDisplay = 'New password and confirm password are required.';
                        }
                    } else {
                        $messageDisplay = 'Incorrect old password.';
                    }
                }
            } else {
                $messageDisplay = 'Therapist not found.';
            }

            $stmt->close();
        } else {
            $messageDisplay = 'Error preparing statement.';
        }
    }
}

if ($messageDisplay) {
    echo "<script type='text/javascript'>
            alert('" . addslashes($messageDisplay) . "');
            window.location.href = 'therapist-dashboard.php'; 
          </script>";
    exit();
}

$conn->close();
?>
