<?php
include 'db_conn.php';
$registration_successful = false;

// Check the role and prepare data accordingly
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $phoneNumber = $_POST['phoneNumber'];
    $address = $_POST['address'];
    $birthdate = $_POST['birthdate'];
    $gender = $_POST['gender'];
    $plainPassword = $_POST['password']; // Raw password

    // Hash the password using bcrypt
    $hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);

    // Output the raw and hashed passwords
    echo "Raw password: " . $plainPassword . "<br>";
    echo "Hashed password: " . $hashedPassword . "<br>";

    if ($role == 'admin') {
        $username = $_POST['username'];
        $stmt = $conn->prepare("INSERT INTO admin (firstname, lastname, phoneNumber, address, birthdate, gender, username, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $firstname, $lastname, $phoneNumber, $address, $birthdate, $gender, $username, $hashedPassword);
        $stmt->execute();

    } elseif ($role == 'therapist') {
        $therapist_number = $_POST['therapist_number'];
        $year_hired = $_POST['year_hired'];
        $month_hired = $_POST['month_hired'];
        $day_hired = $_POST['day_hired'];

        // Prepare and bind for therapist
        $stmt = $conn->prepare("INSERT INTO therapist (firstname, lastname, phoneNumber, address, birthdate, gender, therapist_number, year_hired, month_hired, day_hired, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssiisi", $firstname, $lastname, $phoneNumber, $address, $birthdate, $gender, $therapist_number, $year_hired, $month_hired, $day_hired, $hashedPassword);

    } elseif ($role == 'patient') {
        $patient_number = $_POST['patient_number'];

        // Prepare and bind for patient
        $stmt = $conn->prepare("INSERT INTO patient (firstname, lastname, phoneNumber, address, birthdate, gender, patient_number, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $firstname, $lastname, $phoneNumber, $address, $birthdate, $gender, $patient_number, $hashedPassword);
    }

    // Execute the statement and check for success
    if ($stmt->execute()) {
        $registration_successful = true;
        echo "Registration successful!<br>";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}

// Close the connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <style>
        /* Modal styles */
        .modal {
            display: none; /* Hidden by default */
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }
        .modal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 30%;
            text-align: center;
        }
        .modal button {
            padding: 10px 20px;
            margin-top: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>

<?php if ($registration_successful): ?>
    <!-- Trigger the modal when registration is successful -->
    <script type="text/javascript">
        window.onload = function() {
            document.getElementById("successModal").style.display = "block";
        };
    </script>
<?php endif; ?>

<!-- Modal structure -->
<div id="successModal" class="modal">
    <div class="modal-content">
        <h2>Successfully Registered!</h2>
        <p>Thank you for registering. Click the button below to proceed to the login page.</p>
        <button onclick="window.location.href='adminlogin.php'">Proceed to Login</button>
    </div>
</div>

</body>
</html>
