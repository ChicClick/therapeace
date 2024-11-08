<?php
include 'config.php';
include 'db_conn.php';

// Load the AWS SDK for PHP
require 'vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

// Instantiate an S3 client
$s3 = new S3Client([
    'version' => 'latest',
    'region'  => getenv('AWS_DEFAULT_REGION'), // Use your configured region
    'credentials' => [
        'key'    => getenv('AWS_ACCESS_KEY_ID'),
        'secret' => getenv('AWS_SECRET_ACCESS_KEY'),
    ],
]);

// Set your S3 bucket name
$bucketName = getenv('AWS_BUCKET_NAME'); // Assuming the bucket name is stored as an environment variable

// SQL query to fetch the required fields from the reports table along with the therapist name
$sql = "SELECT r.reportID, r.patientID, r.therapistID, t.therapistName, r.status, r.created_at, r.pdf_path 
        FROM reports r
        JOIN therapist t ON r.therapistID = t.therapistID
        WHERE r.patientID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $patientID);
$stmt->execute();
$result = $stmt->get_result();

// Fetch the report details from the database as before
if ($result->num_rows > 0) {
    $report = $result->fetch_assoc();

    // Check if the report's status is not 'pending' and the pdf_path is available
    $isReportAvailable = ($report['status'] != 'pending' && !empty($report['pdf_path']));

    if ($isReportAvailable) {
        // Get the S3 file URL (replace with your actual file path logic if needed)
        $pdfKey = $report['pdf_path']; // Assuming pdf_path stores the key for the S3 object
        try {
            // Generate a pre-signed URL for the PDF file
            $cmd = $s3->getCommand('GetObject', [
                'Bucket' => $bucketName,
                'Key'    => $pdfKey
            ]);
            $request = $s3->createPresignedRequest($cmd, '+20 minutes'); // URL valid for 20 minutes
            $pdfUrl = (string)$request->getUri();

            // Output or use the URL as needed (example: passing it to your front-end)
            echo "PDF available at: <a href='$pdfUrl'>View Report</a>";
        } catch (AwsException $e) {
            echo "Error fetching PDF from S3: " . $e->getMessage();
        }
    } else {
        echo "Report is pending or no PDF is available.";
    }
} else {
    echo "No reports found for this patient.";
}

$conn->close();
?>
