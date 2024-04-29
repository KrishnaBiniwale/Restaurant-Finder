<?php
require "config/config.php";
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Restaurant Finder | Search Results</title>
    <?php include "include-require/head.html" ?>
    <meta name="description"
        content="This page displays the search results from a user search. If the user is logged in, the user can add restaurants to their favorites list. Otherwise, users are directed to go log in.">
</head>

<body>
    <div id="page">
        <?php include "include-require/nav.php" ?>
        <div class="container mt-4">
            <div class="row justify-content-center" id="search-results">

                <div class="row text-center" id="results">
                    <div class="spinner-border container text-center mb-3" id="loading-spinner" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <?php if (!isset($_SESSION["favorites"]) || $_SESSION["favorites"] === "false"): ?>
                        <p class="fs-5" id="results-p"><em>Showing <strong><span id="numResults">0</span></strong> of
                                <strong><span id="totalResults">0</span></strong>
                                result(s) for "<span id="search-term"></span>"</em></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>


    <script>

        // Function to get URL parameters and decode them
        function getURLParams() {
            const urlParams = new URLSearchParams(window.location.search);
            const params = {};
            for (const [key, value] of urlParams) {
                params[key] = decodeURIComponent(value);
            }
            return params;
        }

        function displayFavorites() {
            $.ajax({
                type: "POST",
                url: "get-favorites.php",
                success: function (response) {
                    let restaurants = JSON.parse(response);
                    displayRestaurants(restaurants);
                }
            });
        }

        $(document).ready(function () {
            <?php if ($_SESSION['favorites'] === "true"): ?>
                displayFavorites();
            <?php else: ?>
                let params = getURLParams();
                // Base URL of the search endpoint
                let baseURL = "https://cors-anywhere.herokuapp.com/https://api.yelp.com/v3/businesses/search";

                // Construct the query URL using object literals and template literals
                let queryURL = `${baseURL}?${Object.keys(params).map(key => `${key}=${encodeURIComponent(params[key])}`).join('&')}`;
                let apiKey = "KsGd36gXbL3rlu3rd-ivS-Tlev_TS4iilgg1DmoGmIYEyEAJYJiVlfH9U6NAxHdTPmS6TdaFk3Wd_dOSLV5QnIpwuG2NyiNngMPHETdidSLzw2TyCL_QKRqc5_Q6ZXYx";
                fetch(queryURL, {
                    method: "GET",
                    headers: {
                        "accept": "application/json",
                        "x-requested-with": "xmlhttprequest",
                        "Access-Control-Allow-Origin": "*",
                        "Authorization": `Bearer ${apiKey}`
                    }
                })
                    .then(response => {
                        if (!response.ok) {
                            window.open("https://cors-anywhere.herokuapp.com/https://api.yelp.com/v3/businesses/search");
                            alert("Return to Home to Search");
                        }
                        return response.json();
                    })
                    .then(result => {
                        document.getElementById('search-term').innerHTML = params['term'];
                        displayRestaurants(result);
                    });
            <?php endif; ?>
        });

        function addRestaurantsToDB(restaurants) {
            $.ajax({
                type: "POST",
                url: "add-restaurants.php",
                data: { restaurants: restaurants },
                success: function (response) {
                }
            });
        }


        async function displayRestaurants(result) {
            removeAll();
            // Acquires the restaurant
            let restaurants;
            if (result['businesses']) {
                restaurants = result["businesses"];
            }
            else if (Array.isArray(result)) {
                restaurants = result;
            }
            else {
                restaurants = [];
                restaurants[0] = result;
            }
            // Adds restaurants to database if they don't already exist
            <?php if (!isset($_SESSION['favorites']) || $_SESSION['favorites'] === "false"): ?>
                await addRestaurantsToDB(JSON.stringify(restaurants));
            <?php else: ?>
                if (restaurants.length == 0) {
                    let p = document.createElement("p");
                    p.classList.add("fs-5");
                    p.innerHTML = "You don't have any favorites right now. To select a restaurant as a favorite, go to the home page and search for restaurants."
                    document.getElementById("results").appendChild(p);
                }
            <?php endif; ?>
            document.getElementById("loading-spinner").classList.add("hidden");
            for (let j = 0; j < restaurants.length; j++) {
                let restaurant = restaurants[j];
                if (restaurant["yelp_id"]) {
                    restaurant["id"] = restaurant["yelp_id"];
                    delete restaurant["yelp_id"];
                    restaurant["name"] = restaurant["restaurant_name"];
                    delete restaurant["restaurant_name"];
                }
                let restaurantContainer = document.createElement("div");
                restaurantContainer.classList.add("container", "col-lg-5", "col-lg-offset-1", "col-12", "restaurant", "border", "border-1", "border-dark-subtle", "rounded-5", "my-4");
                let leftCol = document.createElement("div");
                leftCol.classList.add("text-center", "col-lg-7", "col-12", "restaurant-left-col");

                let img = document.createElement("img");
                img.classList.add("img-fluid", "rounded-5");
                img.src = restaurant["image_url"];
                img.onerror = function () {
                    img.src = "https://www.healthifyme.com/blog/wp-content/uploads/2021/10/All-About-The-Right-Food-Plate-Method.jpg";
                };
                img.alt = restaurant["name"];
                leftCol.appendChild(img);

                let rightCol = document.createElement("div");
                rightCol.classList.add("col-lg-5", "col-12", "restaurant-right-col", "text-lg-left", "text-center", "text-lg-start");

                let restaurantName = document.createElement("div");
                restaurantName.classList.add("row", "pb-4", "restaurant-name", "text-center");
                let name = document.createElement("h4");
                name.classList.add("mt-3");
                name.innerHTML = restaurant["name"];
                restaurantName.appendChild(name);
                restaurantContainer.appendChild(restaurantName);

                let ratingTitle = document.createElement("h5");
                ratingTitle.classList.add("normal", "mt-4", "text-decoration-underline");
                ratingTitle.innerHTML = "Rating";
                let rating = document.createElement("h5");
                rating.classList.add("normal");
                let ratingNum = restaurant["rating"];
                let k;
                for (k = 1; k <= ratingNum; k++) {
                    let icon = document.createElement("i");
                    icon.classList.add("fa-solid", "fa-star");
                    rating.appendChild(icon);
                }
                if (k - ratingNum <= 0.5) {
                    let icon = document.createElement("i");
                    icon.classList.add("fa-solid", "fa-star-half-stroke");
                    rating.appendChild(icon);
                    k += 0.5;
                }
                while (k <= 5) {
                    let icon = document.createElement("i");
                    icon.classList.add("fa-regular", "fa-star");
                    rating.appendChild(icon);
                    k++;
                }
                rating.innerHTML += " (" + ratingNum + ")";
                rightCol.appendChild(ratingTitle);
                rightCol.appendChild(rating);

                let reviewCount = document.createElement("h5");
                reviewCount.classList.add("normal", "mt-4");
                reviewCount.innerHTML = "Total Reviews: " + restaurant["review_count"];
                rightCol.appendChild(reviewCount);

                let price = document.createElement("h5");
                price.classList.add("normal", "mt-4");
                if (restaurant['price']) {
                    price.innerHTML = "Price: " + restaurant["price"];
                }
                else {
                    price.innerHTML = "Price: N/A";
                }
                rightCol.appendChild(price);

                let categoriesText = document.createElement("h5");
                categoriesText.classList.add("normal", "mt-4", "text-decoration-underline");
                categoriesText.innerHTML = "Categories";
                rightCol.appendChild(categoriesText);

                let categories = document.createElement("h5");
                categories.classList.add("normal");
                for (let i = 0; i < restaurant['categories'].length; i++) {
                    if (restaurant['categories'][i]['category_name']) {
                        restaurant['categories'][i]['title'] = restaurant['categories'][i]['category_name'];
                        delete restaurant['categories'][i]['category_name'];
                    }
                    categories.innerHTML += restaurant['categories'][i]['title'] + ", ";
                }
                categories.innerHTML = categories.innerHTML.slice(0, -2);
                rightCol.appendChild(categories);

                let restaurantInfo = document.createElement("div");
                restaurantInfo.classList.add("row", "pb-4");
                restaurantInfo.appendChild(leftCol);
                restaurantInfo.appendChild(rightCol);
                <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                    let addToFavoritesWrapper = document.createElement("div");
                    addToFavoritesWrapper.classList.add("hidden", "add-to-favorites");
                    let alert = document.createElement("div");
                    alert.classList.add("alert", "alert-primary", "hidden", "mt-4");
                    alert.role = "alert";
                    let addToFavoritesButton = document.createElement("button");
                    addToFavoritesButton.classList.add("btn", "btn-success", "btn-lg", "col-12");
                    makeSpinnerButton(addToFavoritesButton);
                    $.ajax({
                        type: "POST",
                        url: "check-favorite.php",
                        data: { yelp_id: restaurant["id"] },
                        success: function (response) {
                            let result = JSON.parse(response);
                            if (result['code'] == 0 || result['code'] == 2) {
                                // Restaurant is not a favorite
                                addToFavoritesButton.innerHTML = "Add to Favorites";
                                addToFavoritesButton.addEventListener('click', function () {
                                    event.stopPropagation();
                                    event.preventDefault();
                                    makeSpinnerButton(addToFavoritesButton);
                                    $.ajax({
                                        type: "POST",
                                        url: "add-favorite.php",
                                        data: { yelp_id: restaurant["id"] },
                                        success: function (response) {
                                            addToFavoritesButton.innerHTML = "Add to Favorites";
                                            let result = JSON.parse(response);
                                            alert.innerHTML = result['message'];
                                            changeAlert(alert, result['code']);
                                        }
                                    });
                                });
                            } else if (result['code'] == 1) {
                                // Restaurant is a favorite
                                addToFavoritesButton.innerHTML = "Remove From Favorites";
                                addToFavoritesButton.addEventListener('click', function () {
                                    event.stopPropagation();
                                    event.preventDefault();
                                    makeSpinnerButton(addToFavoritesButton);
                                    $.ajax({
                                        type: "POST",
                                        url: "remove-favorite.php",
                                        data: { yelp_id: restaurant["id"] },
                                        success: function (response) {
                                            addToFavoritesButton.innerHTML = "Remove From Favorites";
                                            let result = JSON.parse(response);
                                            alert.innerHTML = result['message'];
                                            changeAlert(alert, result['code']);
                                        }
                                    });
                                });
                            }
                        }
                    });
                    addToFavoritesWrapper.appendChild(addToFavoritesButton);
                    addToFavoritesWrapper.appendChild(alert);
                    restaurantInfo.appendChild(addToFavoritesWrapper);
                    restaurantContainer.addEventListener('click', function () {
                        expandInfo(restaurantContainer, restaurant);
                    });
                <?php else: ?>
                    restaurantContainer.addEventListener('click', function () {
                        alert("You must login to add a favorite restaurant.");
                    });
                <?php endif; ?>
                restaurantContainer.appendChild(restaurantInfo);

                let searchResults = document.getElementById("search-results");
                searchResults.appendChild(restaurantContainer);
            }

            // Displays # of results and shows/hides appropriate information
            <?php if (!isset($_SESSION['favorites']) || $_SESSION['favorites'] === "false"): ?>
                let resultsP = document.getElementById("results-p");
                let navigation = document.getElementById("navigation");
                let numResults = document.getElementById("numResults");
                numResults.innerHTML = restaurants.length;
                let totalResults = document.getElementById("totalResults");
                totalResults.innerHTML = result['total'];
            <?php endif; ?>
            let restaurantContainers = document.querySelectorAll('.restaurant');
            for (let k = 0; k < restaurantContainers.length; k++) {
                restaurantContainers[k].classList.add("hover");
            }
        }

        // Changes alert based on whether it was successful
        function changeAlert(alert, code) {
            if (code == 1) {
                alert.classList.add("alert-primary");
                alert.classList.remove("alert-warning");
                alert.classList.remove("alert-danger");
            }
            else if (code == 2) {
                alert.classList.remove("alert-primary");
                alert.classList.add("alert-warning");
                alert.classList.remove("alert-danger");
            }
            else if (code == 3 || code == 4) {
                alert.classList.remove("alert-primary");
                alert.classList.remove("alert-warning");
                alert.classList.add("alert-danger");
            }
            alert.classList.remove("hidden");
        }

        function makeSpinnerButton(button) {
            button.innerHTML = "";
            let spinner = document.createElement("div");
            spinner.classList.add("spinner-border", "container", "text-center");
            spinner.role = "status";
            let spinnerSpan = document.createElement("span");
            spinnerSpan.classList.add("sr-only");
            spinnerSpan.innerHTML = "Loading...";
            spinner.appendChild(spinnerSpan);
            button.appendChild(spinner);
        }

        // Expands and retracts additional restaurant information
        function expandInfo(restaurantContainer, restaurant) {

            let expanded = false;
            if (restaurantContainer.classList.contains("col-lg-11")) {
                expanded = true;
            }

            // Determines whether a container expands left or right
            let rightContainer = getContainerPosition(restaurantContainer) || restaurantContainer.classList.contains("right-container");
            let leftCol = restaurantContainer.querySelector('div.restaurant-left-col');
            let rightCol = restaurantContainer.querySelector('div.restaurant-right-col');
            let addToFavorites = restaurantContainer.querySelector('div.add-to-favorites');
            let restaurantName = restaurantContainer.querySelector('div.restaurant-name');
            if (!expanded) {
                restaurantContainer.classList.add("col-lg-11", "expanded");
                leftCol.classList.remove("col-lg-7");
                leftCol.classList.add("col-lg-3");
                rightCol.classList.remove("col-lg-5");
                rightCol.classList.add("col-lg-3");
                addToFavorites.classList.remove("hidden");
                addToFavorites.classList.add("col-lg-5", "col-12", "mt-3", "col-lg-offset-1");
                restaurantName.classList.add("col-lg-6");
            }
            else {
                restaurantContainer.querySelector("div.alert").classList.add("hidden");
                restaurantContainer.classList.remove("col-lg-11", "expanded");
                leftCol.classList.add("col-lg-7");
                leftCol.classList.remove("col-lg-3");
                rightCol.classList.add("col-lg-5");
                rightCol.classList.remove("col-lg-3");
                addToFavorites.classList.add("hidden");
                addToFavorites.classList.remove("col-lg-6", "col-12", "mt-3", "me-3");
                restaurantName.classList.remove("col-lg-6");
            }
            if (window.innerWidth > 992) {
                swapAllContainers(restaurantContainer, rightContainer, expanded);
            }
        }


        function swapAllContainers(restaurantContainer, rightContainer, expanded) {
            let j = 0;
            if (rightContainer && expanded) {
                j++;
            }
            let containersBelow = false;
            for (let i = 0; i < restaurantContainer.parentNode.children.length; i++) {
                if (containersBelow) {
                    if (j % 2 && restaurantContainer.parentNode.children[i - 1] != restaurantContainer) {
                        swapContainers(restaurantContainer.parentNode.children[i - 1], restaurantContainer.parentNode.children[i]);
                    }
                    j++;
                } else if (restaurantContainer.parentNode.children[i] == restaurantContainer) {
                    containersBelow = true;
                }
            }

            if (rightContainer) {
                if (!expanded) {
                    swapContainers(restaurantContainer.previousElementSibling, restaurantContainer);
                    restaurantContainer.classList.add("right-container");
                } else {
                    swapContainers(restaurantContainer, restaurantContainer.nextElementSibling);
                    restaurantContainer.classList.remove("right-container");
                }
            }

            // Scroll to appropriate position
            let newItemTop = restaurantContainer.getBoundingClientRect().top;
            let newItemBottom = restaurantContainer.getBoundingClientRect().bottom;
            if (newItemTop < 0) {
                window.scrollTo({
                    top: newItemTop + window.pageYOffset,
                    left: 0,
                    behavior: 'instant',
                });
            } else if (newItemBottom > window.innerHeight) {
                window.scrollTo({
                    top: newItemBottom + window.pageYOffset - window.innerHeight,
                    left: 0,
                    behavior: 'instant',
                });
            }

        }

        function swapContainers(containerOriginal, containerInsert) {
            containerOriginal.parentNode.insertBefore(containerInsert, containerOriginal);
        }


        function getContainerPosition(restaurantContainer) {
            let restaurantDivs = document.querySelectorAll('div.restaurant');
            let index = 0;
            for (let i = 0; i < restaurantDivs.length; i++) {
                if (restaurantDivs[i] === restaurantContainer) {
                    break;
                }
                else if (!restaurantDivs[i].classList.contains("expanded")) {
                    index++;
                }
            }
            return index % 2;
        }


        function removeAll() {

            let restaurants = document.querySelectorAll('.restaurant');
            for (let i = 0; i < restaurants.length; i++) {
                restaurants[i].parentNode.removeChild(restaurants[i]);
            }

            let hr = document.querySelectorAll('hr');
            for (let i = 0; i < hr.length; i++) {
                hr[i].parentNode.removeChild(hr[i]);
            }
        }

    </script>
    <?php include "include-require/bootstrap.html" ?>
</body>

</html>