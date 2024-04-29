<?php
session_start();
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Restaurant Finder | Logout</title>
    <?php include "include-require/head.html" ?>
    <meta name="description"
        content="The logout page firsts displays confirmation of logout. Then, it resets the user session and lets them go back to the home page.">
</head>

<body>
    <div id="page">
        <?php include "include-require/nav.php" ?>
        <div class="container mt-5">
            <h2 class="text-center text-success my-5">You have successfully logged out.</h2>
            <p class="text-center mt-3 fs-5">You can now go back to the home page.</p>
        </div>
    </div>


    <script>
        $(document).ready(function () {
            document.getElementById("loginNavbar").classList.add("hidden");
            document.getElementById("favoritesNavbar").classList.add("hidden");
        });

    </script>
    <?php include "include-require/bootstrap.html" ?>
</body>

</html>