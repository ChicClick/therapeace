<?php
include 'db_conn.php';
header('Content-Type: application/json');

try {
    $data = [
        "totalPatients" => 0,
        "chartData" => [],
        "appointmentData" => [],
        "growthPercentage" => 0,
        "dateRange" => ""
    ];

    // Fetch total patients count for the current year
    $yearStart = date("Y") . "-01-01"; // January 1st of the current year
    $yearEnd = date("Y-m-d"); // Today's date

    $yearlyPatientsSql = "SELECT COUNT(*) AS totalPatients FROM patient";
    $yearlyPatientsResult = $conn->query($yearlyPatientsSql);
    if ($yearlyPatientsResult->num_rows > 0) {
        $yearlyPatientsRow = $yearlyPatientsResult->fetch_assoc();
        $data['totalPatients'] = (int)$yearlyPatientsRow['totalPatients'];
    }

    // Growth Percentage calculation: Compare current week with the previous week
    $lastWeekStart = date("Y-m-d", strtotime("-1 week"));
    $currentWeekStart = date("Y-m-d", strtotime("this week"));

    $lastWeekPatientsSql = "SELECT COUNT(*) AS lastWeekPatients FROM patient WHERE birthday BETWEEN '$lastWeekStart' AND '$currentWeekStart'";
    $lastWeekPatientsResult = $conn->query($lastWeekPatientsSql);
    $currentWeekPatientsSql = "SELECT COUNT(*) AS currentWeekPatients FROM patient WHERE birthday BETWEEN '$currentWeekStart' AND '$yearEnd'";

    $currentWeekPatientsResult = $conn->query($currentWeekPatientsSql);

    if ($lastWeekPatientsResult->num_rows > 0 && $currentWeekPatientsResult->num_rows > 0) {
        $lastWeekPatients = $lastWeekPatientsResult->fetch_assoc()['lastWeekPatients'];
        $currentWeekPatients = $currentWeekPatientsResult->fetch_assoc()['currentWeekPatients'];

        if ($lastWeekPatients > 0) {
            $data['growthPercentage'] = round((($currentWeekPatients - $lastWeekPatients) / $lastWeekPatients) * 100, 1);
        } else {
            $data['growthPercentage'] = 0;
        }
    }

    // Fetch Patient Data for the chart (grouped by month)
    $monthlyPatientsSql = "SELECT DATE_FORMAT(birthday, '%Y-%m') AS month, COUNT(*) AS patient_count 
                          FROM patient 
                          WHERE DATE(birthday) >= '$yearStart' AND DATE(birthday) <= '$yearEnd' 
                          GROUP BY DATE_FORMAT(birthday, '%Y-%m')";
    $monthlyResult = $conn->query($monthlyPatientsSql);
    if ($monthlyResult->num_rows > 0) {
        while ($row = $monthlyResult->fetch_assoc()) {
            $data['chartData'][] = $row;
        }
    }

    // Fetch Appointment Data per Service using 'schedule' instead of 'date'
    $appointmentSql = "
    SELECT s.serviceName, COUNT(*) AS appointment_count
    FROM appointment a
    JOIN services s ON a.serviceID = s.serviceID
    GROUP BY s.serviceName
";
$appointmentResult = $conn->query($appointmentSql);
if ($appointmentResult->num_rows > 0) {
    while ($row = $appointmentResult->fetch_assoc()) {
        $data['appointmentData'][] = $row;
    }
}


    // Format the date range for the current month
    $currentMonthStart = date("Y-m-01"); // 1st day of the current month
    $currentMonthEnd = date("Y-m-t"); // Last day of the current month
    $data['dateRange'] = "Patients from " . date("j F", strtotime($currentMonthStart)) . " to " . date("j F, Y", strtotime($currentMonthEnd));

    echo json_encode($data);

} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
$conn->close();
?>
