<?php
require "config/config.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['restaurants'])) {
    $restaurants = json_decode($_POST['restaurants'], true);
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($mysqli->connect_errno) {
        echo $mysqli->connect_error;
        exit();
    }

    $mysqli->set_charset('utf8');
    foreach ($restaurants as $restaurant) {

        $yelp_id = $restaurant['id'];
        $name = $mysqli->escape_string($restaurant['name']);
        $image_url = $mysqli->escape_string($restaurant['image_url']) ?? null;
        $url = $mysqli->escape_string($restaurant['url']) ?? null;
        $review_count = $restaurant['review_count'] ?? 0;
        $rating = $restaurant['rating'] ?? 0;
        $price = $mysqli->escape_string($restaurant['price']) ?? null;

        // Check if the restaurant already exists based on yelp_id
        $checkSql = "SELECT COUNT(*) AS count FROM restaurants WHERE yelp_id = '$yelp_id'";
        $checkResult = $mysqli->query($checkSql);

        if ($checkResult === false) {
            echo $mysqli->error;
            $mysqli->close();
            exit();
        }

        $row = $checkResult->fetch_assoc();
        $count = $row['count'];

        // Insert the restaurant only if it doesn't already exist
        if ($count == 0) {
            $insertSql = "INSERT INTO restaurants (yelp_id, restaurant_name, image_url, url, review_count, rating, price)
                            VALUES ('$yelp_id', '$name', '$image_url', '$url', $review_count, $rating, '$price');";

            $insertResult = $mysqli->query($insertSql);
            $restaurantId = $mysqli->insert_id;
            if (!$insertResult) {
                echo $mysqli->error;
                $mysqli->close();
                exit();
            }

            // Parse categories
            $categories = $restaurant['categories'] ?? [];

            foreach ($categories as $categoryData) {
                // Extract category title
                $categoryTitle = $mysqli->real_escape_string($categoryData['title']) ?? '';

                // Check if category exists in the categories table
                $checkCategoryQuery = "SELECT category_id FROM categories WHERE category_name = '$categoryTitle'";
                $checkCategoryResult = $mysqli->query($checkCategoryQuery);

                if ($checkCategoryResult === false) {
                    echo $mysqli->error;
                    $mysqli->close();
                    exit();
                }

                if ($checkCategoryResult->num_rows == 0) {
                    // Category does not exist, add it to categories table
                    $addCategoryQuery = "INSERT INTO categories (category_name) VALUES ('$categoryTitle')";
                    $addCategoryResult = $mysqli->query($addCategoryQuery);

                    if ($addCategoryResult === false) {
                        echo $mysqli->error;
                        $mysqli->close();
                        exit();
                    }

                    // Get the auto-generated category_id
                    $categoryId = $mysqli->insert_id;
                } else {
                    // Category exists, get its category_id
                    $categoryRow = $checkCategoryResult->fetch_assoc();
                    $categoryId = $categoryRow['category_id'];
                }

                // Add relationship between restaurant and category to restaurant_categories table
                $addRelationQuery = "INSERT INTO restaurant_categories (restaurant_id, category_id) VALUES ('$restaurantId', '$categoryId')";
                $addRelationResult = $mysqli->query($addRelationQuery);

                if ($addRelationResult === false) {
                    echo $mysqli->error;
                    $mysqli->close();
                    exit();
                }
            }
        }

    }

    // Close DB Connection
    $mysqli->close();
}
?>