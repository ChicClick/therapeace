<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TheraPeace Interface</title>
    <link rel="stylesheet" href="loginlanding.css">
</head>
<body>
        <!-- Left Section -->
        <div class="left-section">
            <a href="admindashboard.php" class="back-link">&larr; Back</a>
            <img src="images/logo.png" alt="TheraBee Logo" class="logo">
            <h2>Register Your Role</h2>
            <button class="role-button" onclick="redirectToPatientRegister()">Patient</button>
            <button class="role-button" onclick="redirectToTherapistRegister()">Therapist</button>
            <button class="role-button" onclick="redirectToParentRegister()">Save a Parent</button>
        </div>
        
        <!-- Right Section -->
        <div class="right-section">
            <div class="text-content">
            <h1>Welcome to TheraPeace</h1>
            <p>
                A management system for TheraBee.<br>
                Optimize business processes<br>
                and service management all in one comprehensive system.
            </p>
            </div>
            <div class="illustration">
                <!-- Placeholder for an image or illustration -->
                <img src="images/background.png" alt="Illustration">
            </div>
        </div>
</body>
</html>

<script>
    function redirectToPatientRegister() {
        window.location.href = 'patientRegister.html';
    }

    function redirectToTherapistRegister() {
        window.location.href = 'therapistRegister.html';
    }

    function redirectToParentRegister() {
        window.location.href = 'parentRegister.html';
    }
    </script>
    
