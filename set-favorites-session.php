<?php
require "config/config.php";
session_start();
$_SESSION['favorites'] = $_POST['favorites'];
?>