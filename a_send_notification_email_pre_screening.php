<?php
require 'db_conn.php';
require_once 'generic_mailer.php';

header('Content-Type: application/json');

$inputData = json_decode(file_get_contents('php://input'), true);

$email = $inputData[0] ?? null;
$guestName = $inputData[1] ?? 'Guest';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $subject = "Notification Email";
        $body = '
            <div style="font-family: Arial, sans-serif; color: #432705; line-height: 1.6;">
            <div style="background-color: #FDBC10; padding: 20px; border-radius: 5px;">
                <h1 style="color: #D57201; font-size: 24px; margin: 0;">TheraPeace Notification</h1>
            </div>
            <div style="padding: 20px; background-color: #FFF4CE; border: 1px solid #FBC22A; border-radius: 5px; margin-top: 10px;">
                <p>Dear <strong>' . htmlspecialchars($guestName) . '</strong>,</p>
                <p>
                    Thank you for choosing <strong>TheraPeace</strong> for your 
                    selected service. Unfortunately, we regret to inform you that there are no available slots 
                    for your preferred service at this time.
                </p>
                <p>
                    We understand that this may be disappointing, but we suggest you to look for other therapy centers 
                    that offer the service that you selected.
                </p>
                <p>
                    We appreciate your understanding and patience. If you have any other questions, please don\'t hesitate to contact us.
                </p>
                <p style="margin-top: 20px;">
                    <strong>Best Regards,</strong><br/>
                    TheraPeace Team
                </p>
            </div>
        </div>
        ';

        try {
            $mailer = new Mailer();

            $result = $mailer->sendEmail($email, $subject, $body);

            if ($result === "Message sent!") {
                http_response_code(200);
                echo json_encode([
                    "status" => "success",
                    "message" => "Email notification sent successfully to {$email}."
                ]);
            } else {
                http_response_code(500); 
                echo json_encode([
                    "status" => "error",
                    "message" => $result
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "message" => "An error occurred: " . $e->getMessage()
            ]);
        }
    } else {
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "message" => "Invalid email address received."
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode([
        "status" => "error",
        "message" => "Only POST method is allowed."
    ]);
}
