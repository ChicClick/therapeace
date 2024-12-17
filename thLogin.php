<?php
include 'config.php';
include 'db_conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $therapistID = $_POST['therapist_number'];
    $hireDay = $_POST['hire_day'];
    $hireMonth = $_POST['hire_month'];
    $hireYear = $_POST['hire_year'];
    $password = $_POST['password'];

    // Validate and format the hire date
    if (checkdate($hireMonth, $hireDay, $hireYear)) {
        $dateHired = "$hireYear-$hireMonth-$hireDay"; // Format as YYYY-MM-DD
    } else {
        $_SESSION['error_message'] = "Invalid hire date.";
        header("Location: therapistLogin.php");
        exit;
    }

    // Query to check if the therapist exists in the database
    $sql = "SELECT * FROM therapist WHERE therapistID = ? AND dateHired = ? LIMIT 1";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('ss', $therapistID, $dateHired);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $therapist = $result->fetch_assoc();

            if (password_verify($password, $therapist['password_hash'])) {
                $_SESSION['therapist_id'] = $therapist['therapistID'];
                $_SESSION['therapist_name'] = $therapist['therapistName'];
                header("Location: t_loading.php");
                exit;
            } else {
                $_SESSION['error_message'] = "Invalid password.";
            }
        } else {
            $_SESSION['error_message'] = "Invalid therapist number or hire date.";
        }
        $stmt->close();
    } else {
        $_SESSION['error_message'] = "Database query error.";
    }
}
header("Location: therapistLogin.php");
exit;

?>
