<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TheraPeace - Therapist Login</title>
    <link rel="stylesheet" href="loginstyles.css">
    <link rel="icon" type="image/svg+xml" href="images/TheraPeace Logo.svg">
</head>
<body>
    <!-- Left Section -->
    <div class="left-section">
        <a href="loginlanding.html" class="back-link">&larr; Back</a>
        <img src="images/TheraPeace Logo.svg" alt="TheraPeace Logo" class="logo">
        <h2>Sign In as Therapist</h2>

        <!-- Loading Screen -->
        <div id="loading" style="display: none;" class="sk-folding-cube">
            <div class="sk-cube1 sk-cube"></div>
            <div class="sk-cube2 sk-cube"></div>
            <div class="sk-cube4 sk-cube"></div>
            <div class="sk-cube3 sk-cube"></div>
        </div>
        
        <!-- Therapist Login Form -->
        <form action="thLogin.php" method="POST" onsubmit="showLoading()">
        <?php if (!empty($_SESSION['error_message'])) : ?>
            <p class="error-message" style="color: red;"><?php echo $_SESSION['error_message']; ?></p>
        <?php endif; ?>
            <input type="text" name="therapist_number" placeholder="Therapist Number" required>

            <!-- Dropdown Selection for Day, Month, Year -->
            <div class="hire-date-fields">
                <select name="hire_day" required>
                    <option value="" disabled selected>Day Hired</option>
                    <?php for ($i = 1; $i <= 31; $i++) : ?>
                        <option value="<?= $i; ?>"><?= $i; ?></option>
                    <?php endfor; ?>
                </select>
                <select name="hire_month" required>
                    <option value="" disabled selected>Month Hired</option>
                    <?php 
                    $months = [
                        "January", "February", "March", "April", "May", "June", 
                        "July", "August", "September", "October", "November", "December"
                    ];
                    foreach ($months as $index => $month) : ?>
                        <option value="<?= $index + 1; ?>"><?= $month; ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="hire_year" required>
                    <option value="" disabled selected>Year Hired</option>
                    <?php for ($i = date('Y'); $i >= 1900; $i--) : ?>
                        <option value="<?= $i; ?>"><?= $i; ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <input type="password" name="password" placeholder="Password" required>
            <a href="therapistForgotPassword.php" class="forgot-password">Forgot Password?</a>
            <button type="submit" class="proceed-button">Proceed &rarr;</button>
        </form>
    </div>
    
    <!-- Right Section -->
    <div class="right-section">
        <div class="text-content">
            <h1>Welcome to TheraPeace</h1>
            <p>
                Enhance operational efficiency and service delivery through our comprehensive platform.
                Simplifying processes to support a better future for child development.
            </p>
        </div>
        <div class="illustration">
            <!-- Placeholder for an image or illustration -->
            <img src="images/background.png" alt="Illustration">
        </div>
    </div>

    <script>
        console.log("Loading screen triggered"); // Add this line
        function showLoading() {
            document.getElementById("loading").style.display = "block"; // Show loader
        }
    </script>
</body>
</html>
