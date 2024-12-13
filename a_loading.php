<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loading...</title>
    <link rel="icon" type="image/svg+xml" href="images/TheraPeace Logo.svg">
    <link rel="stylesheet" href="loadingstyles.css">
    <script>
        // Redirect to homepage after 2 seconds
        function redirectToHomepage() {
            document.body.classList.add('fade-out');
            setTimeout(function() {
                window.location.href = 'admindashboard.php';
            }, 1000); // Match this duration with the CSS transition time
        }

        setTimeout(redirectToHomepage, 2000);
    </script>
</head>
<body>
    <div class="sk-folding-cube">
        <div class="sk-cube1 sk-cube"></div>
        <div class="sk-cube2 sk-cube"></div>
        <div class="sk-cube4 sk-cube"></div>
        <div class="sk-cube3 sk-cube"></div>
    </div>
</body>
</html>
