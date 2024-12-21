<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the database connection file
include 'db_conn.php';
include 'generic_aws.php';
require_once 'generic_mailer.php';

function generateRandomPassword($length = 6) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }
    return $randomString;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    try {

        $result = $conn->query("SELECT MAX(patientID) AS maxID FROM patient");
        $row = $result->fetch_assoc();
        $lastID = $row['maxID'];
        $patientID = "P" . str_pad(substr($lastID, 1) + 1, 3, '0', STR_PAD_LEFT);


        $stmt = $conn->prepare("INSERT INTO patient (patientID, patientName, phone, email, address, birthday, gender, parentID, relationship, serviceID, status, image, password_hash, guestID) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)");
        if (!$stmt) {
            die("Error in SQL preparation: " . $conn->error);
        }

        $s3 = new AwsS3("image");

        $imagePath = "https://therapeace-images-bucket-patient.s3.ap-southeast-2.amazonaws.com/default.jpg";
        if (isset($_FILES['image']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
            $imageTmpPath = $_FILES['image']['tmp_name'];
            $imageExt = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $imageName = uniqid('img_', true) . '.' . $imageExt;
            $s3Key = 'images/' . $imageName;
 
            $imagePath = $s3->uploadFile($imageTmpPath, $s3Key);
        
            if (!$imagePath) {
                die("Error uploading image to S3.");
            }
        }

        $randomPassword = generateRandomPassword();

        $hashed_password = password_hash($randomPassword, PASSWORD_DEFAULT);
        $guestID = intval($_POST['guestID']);
        $stmt->bind_param(
            "sssssssssssssi", 
            $patientID, 
            $_POST['patientName'], 
            $_POST['phone'], 
            $_POST['email'], 
            $_POST['address'], 
            $_POST['birthday'], 
            $_POST['gender'], 
            $_POST['parentID'], 
            $_POST['relationship'], 
            $_POST['serviceID'], 
            $_POST['status'], 
            $imagePath,
            $hashed_password,
            $guestID
        );

        // Execute the SQL query
        if ($stmt->execute()) {
            echo "Patient registration successful!";
    
            try {
                $mailer = new Mailer();
                $toEmail = $_POST['email'];
                $subject = 'Your Registration Details';
                $body = "
                    <div style='font-family: Arial, sans-serif; background-color: #FFF4CE; color: #432705; margin: 0; padding: 0;'>
                        <div style='max-width: 600px; margin: 20px auto; background-color: #FFFFFF; border: 1px solid #D57201; border-radius: 8px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); overflow: hidden;'>
                            <div style='background-color: #D57201; color: #FFFFFF; text-align: center; padding: 20px;'>
                                <h1 style='margin: 0; font-size: 24px;'>Welcome to TheraPeace</h1>
                            </div>
                            <div style='padding: 20px; line-height: 1.6;'>
                                <p>Dear <b style='color: #D57201;'>" . $_POST['patientName'] . "</b>,</p>
                                <p>Thank you for registering with <b style='color: #D57201;'>TheraPeace</b>.</p>
                                <p>Here are your credentials:</p>
                                <p>
                                    <b style='color: #D57201;'>Patient ID:</b> " . $patientID . "<br>
                                    <b style='color: #D57201;'>Password:</b> " . $randomPassword . "
                                </p>
                                <p>Please remember to change your password after your first login.</p>
                                <p>Best regards,<br>The TheraPeace Team</p>
                            </div>
                            <div style='background-color: #FDBC10; color: #432705; text-align: center; padding: 15px; font-size: 14px;'>
                                <p>&copy; 2024 TheraPeace. All rights reserved.</p>
                            </div>
                        </div>
                    </div>
                ";

                $mailer->sendEmail($toEmail, $subject, $body);
                echo " A confirmation email has been sent.";

                if (isset($_POST['guestID'])) {
  
                    if ($guestID > 0) {
                        try {
                            $sqlUpdateStatus = "UPDATE guest SET status = 2 WHERE GuestID = ?";
                            $stmt = $conn->prepare($sqlUpdateStatus);

                            if ($stmt->execute([$guestID])) {
                                if ($stmt->rowCount() > 0) {
                                    echo "Status updated successfully.";
                                } else {
                                    echo "No rows updated. Status may already be 2 or GuestID does not exist.";
                                }
                            } else {
                                echo "Query execution failed.";
                            }
                        } catch (PDOException $e) {
                            echo "Error: " . $e->getMessage();
                        }
                    } else {
                        echo "guestID is 0 or invalid. Skipping update.";
                    }
                } else {
                    echo "guestID is not set.";
                }

            } catch (Exception $e) {
                echo " However, the email could not be sent: {$mail->ErrorInfo}";
            }

            echo "<br><a href='registerlanding.php'>Back to Registration Landing</a>";
        } else {
            echo "Error executing SQL: " . $stmt->error;
        }

        $stmt->close();

    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

header("Location: admindashboard.php?active=patients-information-section");
$conn->close();
?>
