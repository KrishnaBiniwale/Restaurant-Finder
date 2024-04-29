<?php
require "config/config.php";
session_start();

// If not user logged in, redirect to login
if (!isset($_SESSION["user_id"])) {
    header('Location: login.php');
} else {
    // Else, GET from favorites and display
    require "config/dbconnect.php";
    $user_id = $_SESSION['user_id'];
    $favoritesQuery = "SELECT restaurants.* FROM favorites LEFT JOIN restaurants on favorites.restaurant_id = restaurants.restaurant_id WHERE favorites.user_id = $user_id;";
    $favoritesResult = $mysqli->query($favoritesQuery);
    if ($favoritesResult === false) {
        echo $mysqli->error;
        $mysqli->close();
        exit();
    }

    $restaurantRows = $favoritesResult->fetch_all(MYSQLI_ASSOC);
    foreach ($restaurantRows as $key => $row) {
        $restaurant_id = $row["restaurant_id"];
        $categoriesQuery = "SELECT categories.category_name FROM restaurant_categories LEFT JOIN categories ON categories.category_id = restaurant_categories.category_id LEFT JOIN restaurants ON restaurants.restaurant_id = restaurant_categories.restaurant_id WHERE restaurants.restaurant_id = $restaurant_id;";
        $categoriesResult = $mysqli->query($categoriesQuery);
        if ($categoriesResult === false) {
            echo $mysqli->error;
            $mysqli->close();
            exit();
        }
        $categories = $categoriesResult->fetch_all(MYSQLI_ASSOC);
        $restaurantRows[$key]["categories"] = $categories;
    }

    $jsonData = json_encode($restaurantRows);
    echo $jsonData;
}