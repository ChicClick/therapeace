<?php
require 'db_conn.php';
include 'generic_sms.php';
require_once 'generic_mailer.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$conn) {
        die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
    }

    $patientID = $_POST['patientID'] ?? null;
    $parentID = $_POST['parentID'] ?? null;
    $therapistID = $_POST['therapistID'] ?? null;
    $serviceID = $_POST['serviceID'] ?? null;
    $schedule = isset($_POST['schedule']) ? json_decode($_POST['schedule'], true) : null;

    if (!is_array($schedule)) {
        echo json_encode(["error" => "Invalid schedule format. Must be an array."]);
        exit();
    }

    if (!$patientID || !$parentID || !$therapistID || !$serviceID || !$schedule) {
        echo json_encode(["error" => "All fields are required."]);
        exit();
    }

    function checkExistence($table, $column, $value)
    {
        global $conn;
        $allowedTables = ['parent', 'therapist', 'services'];
        $allowedColumns = ['parentID', 'therapistID', 'serviceID'];

        if (!in_array($table, $allowedTables) || !in_array($column, $allowedColumns)) {
            return false;
        }

        $stmt = $conn->prepare("SELECT 1 FROM $table WHERE $column = ?");
        $stmt->bind_param("s", $value);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    if (!checkExistence('parent', 'parentID', $parentID)) {
        echo json_encode(["error" => "ParentID does not exist."]);
        exit();
    }
    if (!checkExistence('therapist', 'therapistID', $therapistID)) {
        echo json_encode(["error" => "TherapistID does not exist."]);
        exit();
    }
    if (!checkExistence('services', 'serviceID', $serviceID)) {
        echo json_encode(["error" => "ServiceID does not exist."]);
        exit();
    }

    $sql = "INSERT INTO appointment (patientID, parentID, therapistID, serviceID, schedule) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(["error" => "Failed to prepare SQL statement."]);
        exit();
    }

    foreach ($schedule as $scheduleTime) {
        $stmt->bind_param("sssss", $patientID, $parentID, $therapistID, $serviceID, $scheduleTime);

        if (!$stmt->execute()) {
            echo json_encode(["error" => "Failed to execute SQL statement for schedule $scheduleTime."]);
            exit();
        }
    }

    try {
        $emailInfoSql = "SELECT
        p.patientName AS patient_name,
        p.email AS patient_email,
        t.therapistName AS therapist_name,
        t.email AS therapist_email
    FROM
        patient p
    JOIN
        therapist t
    WHERE
        p.patientID = ? AND t.therapistID = ?";

        $stmt = $conn->prepare($emailInfoSql);
        if ($stmt === false) {
            throw new Exception("Failed to prepare SQL statement.");
        }

        $stmt->bind_param("ss", $patientID, $therapistID);

        $stmt->execute();
        $result = $stmt->get_result();

        if (!$result) {
            echo json_encode(["error" => "Failed to execute SQL query."]);
            exit();
        }

        $mailerPatient = new Mailer();
        $mailerTherapist = new Mailer();

        $row = $result->fetch_assoc();

        if ($row) {
            $patientName = $row['patient_name'];
            $patientEmail = $row['patient_email'];
            $therapistName = $row['therapist_name'];
            $therapistEmail = $row['therapist_email'];

            $subject = 'Appointment Confirmation';

            $patientBody = emailTemplate($patientName, $schedule, "https://therapeace-d74d563df28a.herokuapp.com/patientHomepage.php");
            $therapistBody = emailTemplate($therapistName, $schedule, "https://therapeace-d74d563df28a.herokuapp.com/therapist-dashboard.php");

            $mailerPatient->sendEmail($patientEmail, $subject, $patientBody);
            $mailerTherapist->sendEmail($therapistEmail, $subject, $therapistBody);
        } else {
            echo json_encode(["error" => "No appointment found."]);
            exit();
        }
    } catch (Exception $e) {
        echo json_encode(["error" => "The appointment has been scheduled. However, an email cannot be sent. Error: " . $e->getMessage()]);
        exit();
    }

    try {
        $phoneSql = "SELECT
                        p.phone AS patient_phone,
                        t.phone AS therapist_phone
                     FROM
                        patient p
                     JOIN
                        therapist t
                     WHERE
                        p.patientID = ? AND t.therapistID = ?";

        $stmt = $conn->prepare($phoneSql);
        if ($stmt === false) {
            throw new Exception("Failed to prepare SQL statement.");
        }

        $stmt->bind_param("ss", $patientID, $therapistID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $patientPhone = $row['patient_phone'];
            $therapistPhone = $row['therapist_phone'];

            $smsSender = new SmsSender();
            $smsMessage = 'Appointment has been scheduled. Please check your email for further details';

            try {
                $smsSender->sendSMS($patientPhone, $smsMessage);
            } catch (Exception $e) {
                echo json_encode(["error" => "An error occurred while sending SMS to the patient."]);
                exit();
            }

            try {
                $smsSender->sendSMS($therapistPhone, $smsMessage);
            } catch (Exception $e) {
                echo json_encode(["error" => "An error occurred while sending SMS to the therapist."]);
                exit();
            }

        } else {
            echo json_encode(["error" => "No records found for the specified patient and therapist."]);
            exit();
        }
    } catch (Exception $e) {
        echo json_encode(["error" => "There was an error with the appointment scheduling process."]);
        exit();
    }

    echo json_encode(["success" => "Appointments has been scheduled. Please inform the parent about the email and sms notification as their confirmation"]);

    $stmt->close();
    $conn->close();
}

function emailTemplate($name, $schedule, $url)
{
    if (!is_array($schedule) || empty($schedule)) {
        return '';
    }

    $template = '
    <table style="max-width: 600px; margin: 20px auto; background-color: #ffffff; border: 1px solid #ddd; border-radius: 8px; font-family: Arial, sans-serif; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
        <tr>
            <td style="background-color: #432705; color: #FFF4CE; text-align: center; font-size: 24px; padding: 20px; font-weight: bold;">
                Appointment Confirmation
            </td>
        </tr>

        <tr>
            <td style="padding: 20px; color: #432705;">
                <p style="font-size: 16px; line-height: 1.5;">
                    Dear ' . htmlspecialchars($name) . ',
                </p>
                <p style="font-size: 16px; line-height: 1.5;">
                    We are pleased to confirm your appointment(s) with the following schedule(s):
                </p>

                <ul style="margin: 20px 0; padding-left: 20px; list-style-type: none;">
    ';

    foreach ($schedule as $time) {
        $template .= '
            <li style="margin: 8px 0; font-size: 16px; color: #D57201;">' . htmlspecialchars($time) . '</li>
        ';
    }

    $template .= '
                    </ul>

                    <p style="font-size: 16px; line-height: 1.5;">
                        If you have any questions or need to make changes, please contact our support team.
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
