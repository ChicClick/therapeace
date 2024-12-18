<?php
// Database connection
include 'db_conn.php';

// Fetch the list of all tables in the database
$sql = "SHOW TABLES";
$result = $conn->query($sql);

// Timestamp for file name
$timestamp = date("Y-m-d_H-i-s");

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="TheraPeace_Export_' . $timestamp . '.csv"');
$output = fopen('php://output', 'w');

// Add a title for the export
fputcsv($output, ["TheraPeace Database Export"]);
fputcsv($output, ["Generated on: " . $timestamp]);
fputcsv($output, []); // Blank line for separation

if ($result->num_rows > 0) {
    while ($row = $result->fetch_row()) {
        $table = $row[0]; // Table name
        $sql_data = "SELECT * FROM $table";
        $data_result = $conn->query($sql_data);

        if ($data_result->num_rows > 0) {
            // Write table name as section header
            fputcsv($output, ["Table: $table"]);
            fputcsv($output, []); // Blank line for separation

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

            // Add a blank line after each table for better separation
            fputcsv($output, []); 
        }
    }
}

fclose($output);
$conn->close();
?>
