<?php
// Include the external database connection file
include 'db_conn.php';

// SQL query to fetch guest data including child information
$sql = "
    SELECT 
        GuestID, 
        GuestName AS guest_name, 
        Email AS email, 
        DateSubmitted AS date_submitted, 
        status AS guest_status
    FROM 
        guest
";

// Execute the query
$result = $conn->query($sql);

// Check if there are any guests
if ($result->num_rows > 0) {
    // Output data for each row
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['guest_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td>" . htmlspecialchars($row['date_submitted']) . "</td>";
        
        $status = $row['guest_status'];
        if ($status == 1) {
            $status_text = "Pending";
            $status_style = "background-color: #FDBC10; padding: 2px; border-radius: 5px;"; 
            // Use JavaScript function instead of URL
            $action_link = "<a href='javascript:void(0);' onclick='showChecklist(" . htmlspecialchars(json_encode($row)) . ")' style='color: #2D3748; text-decoration: none;'>Assess</a>"; 
        } elseif ($status == 2) {
            $status_text = "Completed";
            $status_style = "background-color: #4FD1C5; padding: 2px; border-radius: 5px;"; 
            // Set separate URLs for Edit and Send Results
            $edit_url = "archive.php?id=" . urlencode($row['GuestID']); // URL for editing
            $view_url = "view_checklist.php?id=" . urlencode($row['GuestID']); // URL for sending results
            $action_link = "<a href='$edit_url' style='color: #2D3748; text-decoration: none;'>Archive</a> | <a href='$view_url' style='color: #2D3748; text-decoration: none;'>View</a>"; 
        } else {
            $status_text = "Unknown"; 
            $status_style = ""; // Default for unexpected status values
            $action_link = "N/A"; // No action available for unknown status
        }
        
        // Wrap the status text in a span
        echo "<td><span style='$status_style'>" . htmlspecialchars($status_text) . "</span></td>";
        
        // Output action link
        echo "<td>$action_link</td>"; // Directly output the action link
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='5'>No guests found</td></tr>";
}
?>