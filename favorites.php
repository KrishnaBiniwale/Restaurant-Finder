<?php
require "config/config.php";
session_start();

// If not user logged in, redirect to login
if (!isset($_SESSION["user_id"])) {
    header('Location: login.php');
} else {
    // Else, GET from favorites and display
    require "config/dbconnect.php";
    $user = $_SESSION['user_id'];

}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Restaurant Finder | Favorites</title>
    <?php include "include-require/head.html" ?>
    <style>

    </style>
</head>

<body>
    <div id="page">
        <?php include "include-require/nav.php" ?>
    </div>


    <script>
        $(document).ready(function () {
        });

    </script>
    <?php include "include-require/bootstrap.html" ?>
</body>

</html>