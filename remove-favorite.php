<?php
require "config/config.php";
session_start();
if (isset($_SESSION['user_id']) && isset($_POST['yelp_id'])) {
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
            echo $mysqli->error;
            $mysqli->close();
            exit();
        }

        $row = $checkResult->fetch_assoc();
        $count = $row['count'];

        // If there is a favorite entry, remove it 
        if ($count == 1) {
            $deleteQuery = "DELETE FROM favorites WHERE user_id = $user_id AND restaurant_id = $restaurant_id";
            $result = $mysqli->query($deleteQuery);

            if (!$result) {
                echo $mysqli->error;
                $mysqli->close();
                exit();
            }

            $response = [
                "code" => 1,
                "message" => "Successfully removed favorite."
            ];
            // Otherwise, return that the restaurant was already removed from favorites
        } else {
            $response = [
                "code" => 2,
                "message" => "This favorite was already removed."
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
    ];
}

echo json_encode($response);
// Close DB Connection
$mysqli->close();
?>