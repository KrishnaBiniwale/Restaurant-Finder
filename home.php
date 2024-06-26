<?php
require "config/config.php";
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<title>Restaurant Finder | Home</title>
	<?php include "include-require/head.html" ?>
	<meta name="description"
		content="The Restaurant Finder home page serves as a hub for users to search for restaurants. Additionally, navbar links allow users to log in, and for logged in users to access their favorites list.">
</head>

<body>
	<div id="map">
	</div>
	<div id="page">
		<?php include "include-require/nav.php" ?>
		<div class="container" id="search-form">
			<div class="row justify-content-center text-center">
				<?php if (isset($_SESSION['fname']) && trim($_SESSION['fname']) != ''): ?>
					<h2>
						Welcome, <?php echo $_SESSION['fname']; ?>.
					</h2>
				<?php endif; ?>
			</div>
			<div class="row justify-content-center">
				<img id="home-img" src="img/homepage.jpg" alt="Home Page">
			</div>
			<div class="mt-3">
				<h4 class="text-center fw-normal">Search For Restaurant</h4>
				<form name="form" id="form" action="search-results.php" method="GET">
					<div class="row">
						<div class="col-12 col-lg-4 mb-2 d-flex justify-content-center pt-2">

							<input type="radio" class="btn-check" name="sort_by" value="best_match"
								id="best-match" checked>
							<label class="btn btn-outline-danger me-2" for="best-match">Best Match</label>

							<input type="radio" class="btn-check" name="sort_by" value="rating" id="rating">
							<label class="btn btn-outline-danger me-2" for="rating">Rating</label>

							<input type="radio" class="btn-check" name="sort_by" value="review_count"
								id="review-count">
							<label class="btn btn-outline-danger me-2" for="review_count">Review Count</label>

							<input type="radio" class="btn-check" name="sort_by" value="distance"
								id="distance">
							<label class="btn btn-outline-danger" for="distance">Distance</label>
						</div>
						<div class="col-12 col-lg-4 justify-content-center mb-2 px-4">
							<script>
								$(function () {
									$("#slider-range").slider({
										range: true,
										min: 1,
										max: 4,
										values: [1, 4],
										slide: function (event, ui) {
											let minPrice = "";
											let i;
											for (i = 1; i <= ui.values[0]; i++) {
												minPrice += "$";
											}
											let maxPrice = minPrice;
											while (i <= ui.values[1]) {
												maxPrice += "$";
												i++;
											}
											$("#amount").val(minPrice + " - " + maxPrice);
											let priceString = "";
											for (let k = ui.values[0]; k <= ui.values[1]; k++) {
												priceString += k + ", ";
											}
											priceString = priceString.slice(0, -2);
											// Update price with slider values
											$("#price").val(priceString);
										}
									});
									$("#amount").val("$ - $$$$");
									$("#price").val("1, 2, 3, 4");
								});
							</script>
							<h5>
								<label for="amount">Price Range:</label>
								<input type="text" id="amount" readonly=""
									style="border:0; color:#f6931f; font-weight:bold;">
							</h5>
							<div id="slider-range"></div>
							<input type="hidden" id="price" name="price">
						</div>
						<div class="col-12 col-lg-4 d-flex justify-content-center mt-1">
							<button onclick="revealMap()" type="button" class="mapbtn btn btn-lg btn-primary">
								Google Maps <i class="fa-solid fa-map-pin"></i></button>
						</div>
					</div>
					<div class="row mt-4">
						<div class="form-floating col-10">
							<input type="text" class="form-control" id="term" name="term" placeholder="Restaurant">
							<label for="term" class="ms-3">Restaurant Name or Cuisine Type</label>
						</div>
						<div class="col-2 my-auto">
							<button type="submit" onclick="resetFavorites()" class="btn btn-lg btn-success px-4"><i
									class="fa-solid fa-magnifying-glass"></i></button>
						</div>
					</div>
					<div class="hidden">
						<label>
							<input type="number" id="latitude" name="latitude" value="34.02116" min="-90" max="90"
								step="0.000000000000001">
						</label>
						<label>
							<input type="number" id="longitude" name="longitude" value="-118.287132" min="-180"
								max="180" step="0.000000000000001">
						</label>
					</div>
				</form>
			</div>
		</div>
		<div class="container mt-4" id="search-results">

			<div class="row text-center hidden" id="results">
				<p><em>Showing <strong><span id="numResults">0</span></strong> of <strong><span
								id="totalResults">0</span></strong>
						result(s)</em></p>
			</div>
		</div>
	</div>


	<script>

		function revealMap() {
			initMap();
			$('#map').show();
			document.getElementById('page').classList.add("dim");
		}

		function initMap() {
			let map_position = new google.maps.LatLng(34.02116, -118.287132);

			const myLatlng = { lat: 34.02116, lng: -118.287132 };
			const map = new google.maps.Map(document.getElementById("map"), {
				zoom: 6,
				center: map_position,
			});

			let marker = new google.maps.Marker({
				position: map_position,
				map: map
			});
			// Create the initial InfoWindow.
			let infoWindow = new google.maps.InfoWindow({});

			infoWindow.open(map);
			// Configure the click listener.
			map.addListener("click", (mapsMouseEvent) => {
				// Close the current InfoWindow.
				infoWindow.close();
				// Create a new InfoWindow.
				infoWindow = new google.maps.InfoWindow({
					position: mapsMouseEvent.latLng,
				});
				let latlong = JSON.parse(JSON.stringify(mapsMouseEvent.latLng.toJSON(), null));
				$('#map').hide();
				document.getElementById('page').classList.remove("dim");
				document.getElementById('latitude').value = latlong['lat'];
				document.getElementById('longitude').value = latlong['lng'];
			});
		}

		function resetFavorites() {
			$.ajax({
				type: "POST",
				url: "set-favorites-session.php",
				data: { favorites: false },
				success: function (response) {
				}
			});
		}
	</script>

	<script
		src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCLiCh8XRH9Tkb5pyJLRYyfnK5TC_rdidg&callback=initMap&v=weekly"
		defer></script>


	<script>
		$(document).ready(function () {
			$('#map').hide();
		});
	</script>
	<?php include "include-require/bootstrap.html" ?>
</body>

</html>
