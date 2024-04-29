<?php
require "config/config.php";
session_start();

// If not user logged in, redirect to login
if (!isset($_SESSION["user_id"])) {
    header('Location: login.php');
} else {
    if (isset($_POST['yelp_id'])) {
        require "config/dbconnect.php";
        $user_id = $_SESSION['user_id'];
        $yelp_id = $_POST['yelp_id'];
        $restaurantQuery = "SELECT restaurant_id FROM restaurants WHERE yelp_id = '$yelp_id'";
        $restaurantResult = $mysqli->query($restaurantQuery);

        if ($restaurantResult === false) {
            echo $mysqli->error;
            $mysqli->close();
            exit();
        }

        // Fetch restaurant details
        $restaurantRow = $restaurantResult->fetch_assoc();
        if ($restaurantResult->num_rows == 1) {
            $restaurant_id = $restaurantRow['restaurant_id'];
            $checkQuery = "SELECT COUNT(*) AS count, favorites.favorite_id FROM favorites WHERE user_id = $user_id AND restaurant_id = $restaurant_id GROUP BY favorites.favorite_id;";
            $checkResult = $mysqli->query($checkQuery);

            if ($checkResult === false) {
                echo $mysqli->error;
                $mysqli->close();
                exit();
            }

            $row = $checkResult->fetch_assoc();
            $count = $row['count'];

            // If no entry exists, insert into favorites table 
            if ($count == 0) {
                $response = [
                    "code" => 0,
                    "message" => "Restaurant is not a favorite."
                ];
            } else {
                $response = [
                    "code" => 1,
                    "message" => "Restaurant is a favorite."
                ];
            }
        } else {
            $response = [
                "code" => 2,
                "message" => "Please wait."
            ];
        }
        $jsonData = json_encode($response);
        echo $jsonData;
    }
}
?>