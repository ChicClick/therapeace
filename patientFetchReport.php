    <?php 
    include 'config.php';
    include 'db_conn.php';

    $patientID = $_SESSION['patientID'];
    // SQL query to fetch the most recently created report for the specified patient
    $sql = "SELECT r.reportID, r.patientID, r.therapistID, t.therapistName, r.status, r.created_at, r.pdf_path 
            FROM reports r
            JOIN therapist t ON r.therapistID = t.therapistID
            WHERE r.patientID = ?
            ORDER BY r.created_at DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $patientID);
    $stmt->execute();
    $result = $stmt->get_result();

    // Initialize $report and $isReportAvailable variables
    $report = null;
    $isReportAvailable = false;

    // Check if a row was returned
    if ($result->num_rows > 0) {
        $report = $result->fetch_assoc();
        
        // Check if the report is more recent than one week
        $reportCreationDate = new DateTime($report['created_at']);
        $currentDate = new DateTime();
        $interval = $currentDate->diff($reportCreationDate);

        // Only mark the report as available if it is less than or equal to 7 days old
        if ($interval->days <= 7) {
            $isReportAvailable = ($report['status'] != 'pending' && !empty($report['pdf_path']));
        } else {
            // If the report is older than 1 week, do not show it
            $report = null;
        }
    }

    $conn->close();
    ?>
