<?php
    include 'db_conn.php';

    $therapist_id = $_SESSION['therapist_id']; // Assuming therapist ID is stored in session

    // SQL query to join the patient and parent tables and filter by therapistID
    $sql = "
        SELECT 
            patient.patientID, 
            patient.patientName AS patient_name, 
            parent.parentName AS parent_name,
            patient.image AS image
        FROM patient
        JOIN parent ON patient.parentID = parent.parentID
        WHERE patient.therapistID = ?"; // Filter patients by therapistID

    // Prepare statement to avoid SQL injection
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $therapist_id); // Bind therapist ID as integer
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Output data for each row
        while ($row = $result->fetch_assoc()) {
            echo "<tr data-patient-id='" . $row['patientID'] . "' data-patient-name='" . htmlspecialchars($row['patient_name'], ENT_QUOTES) . "'>";
            echo "<td><img src='images/about 4.jpg' alt='Profile Image' class='profile-img'> " . htmlspecialchars($row['patient_name'], ENT_QUOTES) . "</td>";
            echo "<td data-parent-name='" . htmlspecialchars($row['parent_name'], ENT_QUOTES) . "'>" . htmlspecialchars($row['parent_name'], ENT_QUOTES) . "</td>";
            echo "<td></td>"; // Placeholder for actions or completion
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='3'>No profiles found</td></tr>";
    }

    $conn->close();
?>
