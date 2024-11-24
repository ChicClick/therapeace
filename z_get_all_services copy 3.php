<?php
require 'db_conn.php';

$response = [];

try {
    $sql = "SELECT serviceID, serviceName FROM services";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {

        while ($row = $result->fetch_assoc()) {
            $response[] = [
                'serviceID' => $row['serviceID'],
                'serviceName' => $row['serviceName'],
            ];
        }
    } else {
        $response['message'] = 'No services found.';
    }

    header('Content-Type: application/json');
    echo json_encode($response);

} catch (Exception $e) {

    http_response_code(500);
    echo json_encode(['error' => 'An error occurred: ' . $e->getMessage()]);
} finally {

    $conn->close();
}
