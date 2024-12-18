<?php
include 'config.php';
include 'db_conn.php';
include 'generic_sms.php';
require_once 'generic_mailer.php';

$appointmentID = isset($_POST['appointmentID']) ? $_POST['appointmentID'] : null;
$selectedDatetime = isset($_POST['selectedDatetime']) ? $_POST['selectedDatetime'] : null;

// Log the incoming datetime
error_log("Selected datetime: " . $selectedDatetime);

// Validate the input data
if (empty($appointmentID) || empty($selectedDatetime)) {
    echo json_encode(['success' => false, 'message' => 'Invalid input data.']);
    exit;
}

// Validate the datetime format: 'Y-m-d H:i:s'
if (!DateTime::createFromFormat('Y-m-d H:i:s', $selectedDatetime)) {
    echo json_encode(['success' => false, 'message' => 'Invalid datetime format.']);
    exit;
}

// Prepare and execute the SQL query to update the appointment schedule
$sql = "UPDATE appointment 
        SET schedule = ? 
        WHERE appointmentID = ?";

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit;
}

$stmt->bind_param('si', $selectedDatetime, $appointmentID);


if ($stmt->execute()) {
    try {
        $appointmentInfoSql = "
            SELECT patientID, therapistID
            FROM appointment
            WHERE appointmentID = ?
        ";

        $stmt = $conn->prepare($appointmentInfoSql);
        if ($stmt === false) {
            throw new Exception("Failed to prepare appointment info query.");
        }

        $stmt->bind_param("i", $appointmentID);

        $stmt->execute();
        $result = $stmt->get_result();

        if (!$result || $result->num_rows === 0) {
            echo json_encode(["error" => "Appointment not found."]);
            exit();
        }

        $row = $result->fetch_assoc();
        $patientID = $row['patientID'];
        $therapistID = $row['therapistID'];

        $emailInfoSql = "
            SELECT 
                p.patientName AS patient_name,
                p.email AS patient_email,
                t.therapistName AS therapist_name,
                t.email AS therapist_email
            FROM patient p
            JOIN therapist t ON p.patientID = ? AND t.therapistID = ?
        ";

        $stmt = $conn->prepare($emailInfoSql);
        if ($stmt === false) {
            throw new Exception("Failed to prepare email info query.");
        }

        $stmt->bind_param("ss", $patientID, $therapistID);

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $row = $result->fetch_assoc()) {
            $patientName = $row['patient_name'];
            $patientEmail = $row['patient_email'];
            $therapistName = $row['therapist_name'];
            $therapistEmail = $row['therapist_email'];

            $subject = "Appointment Confirmation";
            $patientBody = emailTemplate($patientName, $selectedDatetime, "https://therapeace-d74d563df28a.herokuapp.com/patientHomepage.php");
            $therapistBody = emailTemplate($therapistName, $selectedDatetime, "https://therapeace-d74d563df28a.herokuapp.com/therapist-dashboard.php");

            $mailer = new Mailer();
            $mailer->sendEmail($patientEmail, $subject, $patientBody);
            $mailer->sendEmail($therapistEmail, $subject, $therapistBody);
        } else {
            echo json_encode(["error" => "Failed to retrieve patient or therapist details."]);
            exit();
        }

    }  catch (Exception $e) {
        echo json_encode(["error" => "The appointment has been scheduled. However, an email cannot be sent. Error: " . $e->getMessage()]);
        exit();
    }

    echo json_encode(['success' => true, 'message' => 'Appointment rescheduled successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error updating appointment: ' . $stmt->error]);
}

// Debugging: Log the result of the update
error_log("Rows affected: " . $stmt->affected_rows);

$stmt->close();
$conn->close();

function emailTemplate($name, $selectedDatetime, $url)
{

    if (empty($selectedDatetime)) {
        return '';
    }

    $template = '
    <table style="max-width: 600px; margin: 20px auto; background-color: #ffffff; border: 1px solid #ddd; border-radius: 8px; font-family: Arial, sans-serif; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
        <tr>
            <td style="background-color: #432705; color: #FFF4CE; text-align: center; font-size: 24px; padding: 20px; font-weight: bold;">
                Appointment Rescheduling Confirmation
            </td>
        </tr>

        <tr>
            <td style="padding: 20px; color: #432705;">
                <p style="font-size: 16px; line-height: 1.5;">
                    Dear ' . htmlspecialchars($name) . ',
                </p>
                <p style="font-size: 16px; line-height: 1.5;">
                    We are pleased to inform you that your appointment has been successfully rescheduled to the following date and time:
                </p>

                <ul style="margin: 20px 0; padding-left: 20px; list-style-type: none;">
                    <li style="margin: 8px 0; font-size: 16px; color: #D57201;">' . htmlspecialchars($selectedDatetime) . '</li>
                </ul>

                    <p style="font-size: 16px; line-height: 1.5;">
                        If you have any questions or need to make further changes, please contact our support team.
                    </p>
                    <a href="'. htmlspecialchars($url).'" style="display: block; width: fit-content; margin: 20px auto; padding: 10px 20px; background-color: #FBC22A; color: #432705; text-decoration: none; border-radius: 5px; font-size: 16px; font-weight: bold;">View Your Appointment</a>
                </td>
            </tr>

            <tr>
                <td style="background-color: #FDBC10; color: #432705; text-align: center; padding: 15px; font-size: 14px;">
                    © 2024 Therapeace | All rights reserved.
                </td>
            </tr>
        </table>
    ';

    return $template;
}

