<?php
// Database connection
include 'db_conn.php';

// Fetch the list of all tables in the database
$sql = "SHOW TABLES";
$result = $conn->query($sql);

// Timestamp for file name
$timestamp = date("Y-m-d_H-i-s");

if ($result->num_rows > 0) {
    while ($row = $result->fetch_row()) {
        $table = $row[0]; // Table name
        $sql_data = "SELECT * FROM $table";
        $data_result = $conn->query($sql_data);

        if ($data_result->num_rows > 0) {
            // Create CSV file for each table with timestamped file name
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="TheraPeace_Export_' . $table . '_' . $timestamp . '.csv"');
            $output = fopen('php://output', 'w');

            // Output column headers
            $columns = $data_result->fetch_fields();
            $headers = [];
            foreach ($columns as $column) {
                $headers[] = $column->name;
            }
            fputcsv($output, $headers);

            // Output data rows
            while ($row_data = $data_result->fetch_assoc()) {
                fputcsv($output, $row_data);
            }

            fclose($output);
        }
    }
}

$conn->close();
?>
