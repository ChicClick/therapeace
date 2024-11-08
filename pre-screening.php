<?php
include 'db_conn.php';

// SQL query to fetch guest data where schedule is NULL
$sql = "
    SELECT 
        g.GuestID, 
        g.GuestName AS guest_name, 
        g.ChildName AS child_name,
        g.Age AS child_age,
        g.Email AS email, 
        g.DateSubmitted AS date_submitted, 
        g.status AS guest_status
    FROM 
        guest g
    WHERE 
        g.schedule IS NULL
";

// Execute the query
$result = $conn->query($sql);

// Display guests in a table
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['guest_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        
        // Format date_submitted to a more readable format with time
        $formatted_date = date("F j, Y  g:i A", strtotime($row['date_submitted']));
        echo "<td>" . htmlspecialchars($formatted_date) . "</td>";
        
        $status = $row['guest_status'];
        if ($status == 1) {
            $status_text = "Pending";
            $status_style = "background-color: #FDBC10; padding: 2px; border-radius: 5px;"; 
            $action_link = "<a href='javascript:void(0);' onclick='displayGuestChecklist(" . (int)$row['GuestID'] . "); setGuestId(" . (int)$row['GuestID'] . ");' style='color: #2D3748; text-decoration: none;'>Assess</a>"; 
        } elseif  ($status == 2) {   
            $status_text = "Completed";
            $status_style = "background-color: #4FD1C5; padding: 2px; border-radius: 5px;";  
            $action_link = "<a href='javascript:void(0);' onclick='displayGuestChecklistComplete(" . (int)$row['GuestID'] . ")' style='color: #2D3748; text-decoration: none;'>View</a>";
            $send_results_link = "<a href='send_results.php?guest_id=" . (int)$row['GuestID'] . "' style='color: #2D3748; text-decoration: none;'>Send Results</a>";
            $action_link .= " | $send_results_link";
        } else {
            $status_text = "Unknown"; 
            $status_style = ""; 
            $action_link = "N/A"; 
        }
        
        echo "<td><span style='$status_style'>" . htmlspecialchars($status_text) . "</span></td>";
        echo "<td>$action_link</td>"; 
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='5'>No guests found</td></tr>";
}
?>
