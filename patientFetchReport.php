<?php
header('Content-Type: application/json'); // Set content type to JSON
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config.php';
include 'db_conn.php';
require 'vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

// $bucketName = 'therapeace-pdf-reports';
// $region = 'ap-southeast-2';
// $accessKey = 'AKIATX3PIEANFBCNKAE2';
// $secretKey = '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';

$bucketName = getenv('BUCKET_NAME');
$region = getenv('REGION');
$accessKey = getenv('ACCESS_KEY');
$secretKey = getenv('SECRET_ACCESS_KEY');

$s3Client = new S3Client([
    'version' => 'latest',
    'region'  => $region,
    'credentials' => [  
        'key'    => $accessKey,
        'secret' => $secretKey,
    ],
    'http' => [
        'verify' => false,
    ],
]);

$patientID = $_SESSION['patientID'];

$sql = "SELECT r.reportID, r.patientID, r.therapistID, t.therapistName, r.status, r.created_at, r.pdf_path
        FROM reports r
        JOIN therapist t ON r.therapistID = t.therapistID
        WHERE r.patientID = ?
        AND r.created_at = (
            SELECT MAX(sub_r.created_at)
            FROM reports sub_r
            WHERE sub_r.therapistID = r.therapistID AND sub_r.patientID = r.patientID
        )
        ORDER BY r.created_at DESC";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die('Query preparation failed: ' . $conn->error);
}

$stmt->bind_param("s", $patientID);
$stmt->execute();

// Bind result variables
$stmt->bind_result($reportID, $patientID, $therapistID, $therapistName, $status, $created_at, $pdf_path);

$response = [
    'isReportAvailable' => false,
    'reports' => [],
];

$foundReports = false; // Flag to check if any report is found

// Fetch results
while ($stmt->fetch()) {
    $foundReports = true;
    $reportCreationDate = new DateTime($created_at);
    $currentDate = new DateTime();
    $interval = $currentDate->diff($reportCreationDate);

    if ($interval->days <= 60) {
        $response['isReportAvailable'] = true;

        $s3Url = null;

        // Ensure that pdf_path is not empty before generating the URL
        if (!empty($pdf_path)) {
            try {
                $result = $s3Client->getObjectUrl($bucketName, $pdf_path);
                $s3Url = $result;
            } catch (AwsException $e) {
                // Log the error message
                error_log('Error fetching S3 URL: ' . $e->getMessage());
                $s3Url = null;
            }
        }

        $response['reports'][] = [
            'reportID' => $reportID,
            'patientID' => $patientID,
            'therapistID' => $therapistID,
            'therapistName' => $therapistName,
            'status' => $status,
            'created_at' => $created_at,
            'pdf_path' => $s3Url, // This will be null if pdf_path is empty
        ];
    }
}

if (!$foundReports) {
    error_log('No reports found for patient ' . $patientID);
}

$conn->close();

$json_response = json_encode($response);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['error' => 'JSON encoding failed: ' . json_last_error_msg()]);
    exit;
}

// Output the JSON response
echo $json_response;
