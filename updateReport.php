<?php
include 'db_conn.php';
require_once __DIR__ . '/fpdf/fpdf.php'; 
require_once __DIR__ . '/fpdi/src/autoload.php'; 
require 'vendor/autoload.php'; // Cloudinary autoload

use setasign\Fpdi\Fpdi;
use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\Uploader;

// Cloudinary Configuration
Configuration::instance([
    'cloud' => [
        'cloud_name' => 'dsint9lfi', // Replace with your actual values
        'api_key'    => '198781663135431',
        'api_secret' => 'yOxRp_tphu5AEbqITzQMAAeIxjWQ',
    ],
    'url' => [
        'secure' => true // Ensures HTTPS URLs
    ]
]);

// Set the correct timezone
date_default_timezone_set('Asia/Manila');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['reportID']) || !isset($_POST['summary'])) {
        die("Error: Missing reportID or summary data in POST request.");
    }
}

$reportID = $_POST['reportID'];
$summary = $_POST['summary'];
$status = "verified";
$updated_at = date('Y-m-d H:i:s'); // Current datetime

// Check if template exists
$templateFile = 'progressReport1.pdf';
if (!file_exists($templateFile)) {
    die("Error: Template PDF file not found.");
}

// Generate the PDF
$pdf = new Fpdi();
$pdf->AddPage();
$pdf->setSourceFile($templateFile);
$template = $pdf->importPage(1);
$pdf->useTemplate($template, 0, 0, 210);
$pdf->SetFont('Arial', '', 12);
$pdf->SetXY(50, 80);
$pdf->MultiCell(0, 10, $summary);

// Capture the PDF output in memory
$pdfContent = $pdf->Output('', 'S'); // 'S' means output as a string

// Upload the PDF to Cloudinary
try {
    $uploadResult = Uploader::upload(
        base64_encode($pdfContent),
        [
            'resource_type' => 'raw', // 'raw' for non-image files
            'public_id' => 'report_' . $reportID,
            'format' => 'pdf', // Ensure the file is treated as a PDF
            'overwrite' => true, // Optional: allows re-uploading with the same public_id
            'upload_preset' => '' // Optional: if using presets
        ]
    );
    $cloudinaryUrl = $uploadResult['url'];

    // Update the database with the Cloudinary URL
    $query = "UPDATE reports SET summary = ?, status = ?, updated_at = ?, pdf_path = ? WHERE reportID = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("ssssi", $summary, $status, $updated_at, $cloudinaryUrl, $reportID);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo "Report updated and PDF created/uploaded successfully. Cloudinary URL: " . $cloudinaryUrl;
        } else {
            echo "No rows were affected. Either the reportID does not exist or the data is unchanged.";
        }
    } else {
        echo "Error updating report: " . $stmt->error;
    }

    $stmt->close();
} catch (Exception $e) {
    die("Error uploading PDF to Cloudinary: " . $e->getMessage());
}

$conn->close();
?>
