<?php
require_once __DIR__ . '/tcpdf/tcpdf.php';
require_once __DIR__ . '/fpdi/src/autoload.php';
include "db_conn.php";

use setasign\Fpdi\Tcpdf\Fpdi;

function generateReport($patientID) {
    // Initialize FPDI
    $pdf = new Fpdi();
    $pdf->SetPrintHeader(false);
    $pdf->SetPrintFooter(false);

    // Load the existing PDF template
    $sourceFile = 'progressReport.pdf'; // Ensure this is the correct path to your template
    if (!file_exists($sourceFile)) {
        die(json_encode(['message' => 'The PDF template file does not exist.']));
    }

    // Prepare your SQL statement
    $stmt = $db->prepare("
        SELECT 
            sf.feedback,
            sf.feedbackDate,
            s.sessionDate,
            t.name AS therapistName
        FROM 
            sessionfeedbacknotes sf
        JOIN 
            sessions s ON sf.sessionID = s.sessionID
        JOIN 
            therapists t ON s.therapistID = t.therapistID
        WHERE 
            sf.patientID = ?
    ");
    $stmt->bind_param("s", $patientID);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if there are any results
    if ($result->num_rows > 0) {
        // Initialize TCPDF
        $pdf = new TCPDF(); // Make sure TCPDF is included and accessible
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Your Name');
        $pdf->SetTitle('Progress Report');
        $pdf->SetSubject('Progress Report for Patient ID: ' . htmlspecialchars($patientID));
        $pdf->SetKeywords('TCPDF, PDF, progress report');
        
        // Add a page
        $pdf->AddPage();

        // Generate content for PDF
        while ($row = $result->fetch_assoc()) {
            $sessionDate = isset($row['sessionDate']) ? $row['sessionDate'] : 'Date not available';
            $therapistName = isset($row['therapistName']) ? $row['therapistName'] : 'Therapist not available';
            $feedback = isset($row['feedback']) ? $row['feedback'] : 'No feedback available';
            $feedbackDate = isset($row['feedbackDate']) ? $row['feedbackDate'] : 'Date not available';

            // Add content to PDF
            $pdf->Cell(0, 10, 'Session Date: ' . htmlspecialchars($sessionDate), 0, 1);
            $pdf->Cell(0, 10, 'Therapist Name: ' . htmlspecialchars($therapistName), 0, 1);
            $pdf->Cell(0, 10, 'Feedback: ' . htmlspecialchars($feedback), 0, 1);
            $pdf->Cell(0, 10, 'Feedback Date: ' . htmlspecialchars($feedbackDate), 0, 1);
            $pdf->Ln(); // Line break for spacing
        }

        // Output the PDF
        $pdf->Output('progressReport_' . $patientID . '.pdf', 'D');
    } else {
        echo json_encode(['message' => 'No progress report found for patient ID: ' . htmlspecialchars($patientID)]);
    }
}

// Get the patientID from the POST request or query parameter
$patientID = $_POST['patientID'] ?? '';
if ($patientID) {
    generateReport($patientID);
} else {
    echo json_encode(['message' => 'Patient ID is required.']);
}
?>
