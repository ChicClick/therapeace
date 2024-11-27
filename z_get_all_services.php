<?php
require 'db_conn.php';

$response = [];

try {
    $sql = "SELECT serviceID, serviceName FROM services";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $numServices = $result->num_rows;
        $startColor = [255, 215, 0];
        $endColor = [255, 153, 0];

        $index = 0;
        while ($row = $result->fetch_assoc()) {

            $t = $index / max(1, $numServices - 1);
            $r = (int)($startColor[0] + $t * ($endColor[0] - $startColor[0]));
            $g = (int)($startColor[1] + $t * ($endColor[1] - $startColor[1]));
            $b = (int)($startColor[2] + $t * ($endColor[2] - $startColor[2]));

            $color = sprintf("#%02x%02x%02x", $r, $g, $b);

            $response[] = [
                'serviceID' => $row['serviceID'],
                'serviceName' => $row['serviceName'],
                'serviceColor' => $color,
            ];

            $index++;
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
?>
