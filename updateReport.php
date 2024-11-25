<?php
include 'db_conn.php';
require_once __DIR__ . '/fpdf/fpdf.php'; 
require_once __DIR__ . '/fpdi/src/autoload.php'; 

use setasign\Fpdi\Fpdi;

// Set the correct timezone
date_default_timezone_set('Asia/Manila');

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
$updated_at = date('Y-m-d H:i:s'); // Current datetime

// Check if template exists
$templateFile = 'progressReport1.pdf';
if (!file_exists($templateFile)) {
    die("Error: Template PDF file not found.");
}

// Check if pdf_reports directory exists and is writable
if (!is_dir('pdf_reports')) {
    mkdir('pdf_reports', 0777, true);  // Create the directory if it doesn't exist
}

$pdf = new Fpdi();
$pdf->AddPage();
$pdf->setSourceFile($templateFile);
$template = $pdf->importPage(1);
$pdf->useTemplate($template, 0, 0, 210);
$pdf->SetFont('Arial', '', 10);
$pdf->SetXY(30, 80);
$pdf->MultiCell(0, 10, $summary);

// Define the path for saving the PDF
$pdfPath = 'pdf_reports/report_' . $reportID . '.pdf';
$pdf->Output($pdfPath, 'F');

// Check if PDF was created successfully
if (!file_exists($pdfPath)) {
    die("Error: PDF was not saved. Check directory permissions.");
}

// Update the database with the new values
$query = "UPDATE reports SET summary = ?, status = ?, updated_at = ?, pdf_path = ? WHERE reportID = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("ssssi", $summary, $status, $updated_at, $pdfPath, $reportID);

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
?>
