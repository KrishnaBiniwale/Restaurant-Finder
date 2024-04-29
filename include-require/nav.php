<div class="container-fluid" id="content">
    <div class="row">
        <div class="container">
            <nav class="navbar navbar-expand-lg bg-body-tertiary">
                <div class="container-fluid row">
                    <a class="navbar-brand col-3 fs-3" href="home.php">Restaurant-Finder</a>
                    <div class="navbar col-6 text-center justify-content-center" id="navbarText">
                        <ul class="navbar-nav mb-2 mb-lg-0">
                            <li class="nav-item">
                                <a class="nav-link fs-5" aria-current="page" href="home.php">Home</a>
                            </li>
                            <li class="nav-item">
                                <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                                    <a class="nav-link fs-5" href="search-results.php" id="favoritesNavbar"
                                        onclick="setFavorites()">Favorites</a>
                                <?php endif; ?>
                            </li>
                        </ul>
                    </div>
                    <div class="navbar col-3 justify-content-end">

                        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                            <a class="nav-link fs-5 me-4" href="logout.php" id="loginNavbar">Logout</a>
                        <?php else: ?>
                            <a class="nav-link fs-5 me-4" href="login.php" id="loginNavbar">Login / Signup</a>
                        <?php endif; ?>

                    </div>
                </div>
            </nav>
        </div>
    </div>
</div>
<script>
    function setFavorites() {
        $.ajax({
            type: "POST",
            url: "set-favorites-session.php",
            data: { favorites: true },
            success: function (response) {
            }
        });
    }
</script>