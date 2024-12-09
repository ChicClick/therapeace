<?php
include 'db_conn.php';
require_once __DIR__ . '/fpdf/fpdf.php';
require_once __DIR__ . '/fpdi/src/autoload.php';
require 'vendor/autoload.php';

use Aws\Exception\AwsException;
use Aws\S3\S3Client;
use setasign\Fpdi\Fpdi;

date_default_timezone_set('Asia/Manila');
// $db_name = getenv('DB_NAME');
// $bucketName = 'therapeace-pdf-reports';
// $region = 'ap-southeast-2';
// $accessKey = 'AKIATX3PIEANFBCNKAE2';
// $secretKey = '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';

$bucketName = getenv('BUCKET_NAME');
$region = getenv('REGION');
$accessKey = getenv('ACCESS_KEY');
$secretKey = getenv('SECRET_ACCESS_KEY');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Debugging incoming POST data
    var_dump($_POST);

    if (!isset($_POST['reportID']) || !isset($_POST['summary'])) {
        die("Error: Missing reportID or summary data in POST request.");
    }
}

$reportID = $_POST['reportID'];
$summary = $_POST['summary'];
$status = "verified";
$updated_at = date('Y-m-d H:i:s');

$templateFile = 'progressReport1.pdf';
if (!file_exists($templateFile)) {
    die("Error: Template PDF file not found.");
}

$pdf = new Fpdi();
$pdf->AddPage();
$pdf->setSourceFile($templateFile);
$template = $pdf->importPage(1);
$pdf->useTemplate($template, 0, 0, 210);
$pdf->SetFont('Arial', '', 10);
$pdf->SetXY(30, 80);
$pdf->MultiCell(0, 10, $summary);

$pdfContent = $pdf->Output('', 'S');

$s3Client = new S3Client([
    'version' => 'latest',
    'region' => $region,
    'credentials' => [
        'key' => $accessKey,
        'secret' => $secretKey,
    ],
    'http' => [
        'verify' => false, // Disable SSL verification
    ],
]);

$keyName = 'report_' . $reportID . '.pdf';

$s3Url = null;

try {

    $result = $s3Client->putObject([
        'Bucket' => $bucketName,
        'Key' => $keyName,
        'Body' => $pdfContent,
        'ACL' => 'private',
    ]);

    $s3Url = $s3Client->getObjectUrl($bucketName, $keyName);

    echo "File uploaded successfully. S3 URL: " . $s3Url . "\n";
} catch (AwsException $e) {
    echo "Error uploading file: " . $e->getMessage() . "\n";
    die("Error uploading file to S3.");
}

$query = "UPDATE reports SET summary = ?, status = ?, updated_at = ?, pdf_path = ? WHERE reportID = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("ssssi", $summary, $status, $updated_at, $keyName, $reportID);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        header("Location: therapist-dashboard.php?message=" . urlencode("Updated Successfully"));
        exit;
    } else {
        header("Location: therapist-dashboard.php?message=" . urlencode("No rows were affected. Either the reportID does not exist or the data is unchanged."));
        exit;
    }
} else {
    header("Location: therapist-dashboard.php?message=" . urlencode("Error updating PDF"));
    exit;
}

$stmt->close();
$conn->close();
