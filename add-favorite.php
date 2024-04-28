<?php
require "config/config.php";
session_start();
if (isset($_SESSION['user_id']) && isset($_POST['yelp_id']) && isset($_POST['add'])) {
    require "config/dbconnect.php";
    $user_id = $_SESSION['user_id'];
    $yelp_id = $_POST['yelp_id'];
    $add = $_POST['add'];


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
        // Check if the entry already exists in the favorites table
        $checkQuery = "SELECT COUNT(*) AS count FROM favorites WHERE user_id = $user_id AND restaurant_id = $restaurant_id";
        $checkResult = $mysqli->query($checkQuery);

        if ($checkResult === false) {
            //echo $mysqli->error;
            $mysqli->close();
            exit();
        }

        $row = $checkResult->fetch_assoc();
        $count = $row['count'];

        // If no entry exists, either insert into favorites table or display that it is not a favorite
        if ($count == 0) {
            // If adding favorite, add favorite successfully
            if ($add === "true") {
                $insertQuery = "INSERT INTO favorites (user_id, restaurant_id) VALUES ($user_id, $restaurant_id)";
                $result = $mysqli->query($insertQuery);

                if (!$result) {
                    echo $mysqli->error;
                    $mysqli->close();
                    exit();
                }

                $response = [
                    "code" => 1,
                    "message" => "Favorite added successfully."
                ];
                // Otherwise, return that the restaurant is not a favorite
            } else {
                $response = [
                    "code" => 0,
                    "message" => "Restaurant is not a favorite."
                ];
            }
        } else {
            // TODO: Remove favorite here
            $response = [
                "code" => 2,
                "message" => "Favorite already exists for this user and restaurant."
            ];
        }
    } else {
        $response = [
            "code" => 3,
            "message" => "Error: Restaurant does not exist."
        ];
    }
} else {
    $response = [
        "code" => 4,
        "message" => "Error: User not logged in.",
        "whynot" => $_POST
    ];
}

$response['add'] = $add;
echo json_encode($response);
// Close DB Connection
$mysqli->close();
?>