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
                                <a class="nav-link fs-5" href="favorites.php">Favorites</a>
                            </li>
                        </ul>
                    </div>
                     <div class="navbar col-3 justify-content-end" id="loginNavbar">

                        <?php if (isset($_SESSION['email']) && trim($_SESSION['email']) != '' ) : ?>
                            <a class="nav-link fs-5 me-4" href="logout.php">Logout</a>
                        <?php else: ?>
                            <a class="nav-link fs-5 me-4" href="login.php">Login / Signup</a>
                        <?php endif; ?>
                        
                    </div>
                </div>
            </nav>
        </div>
    </div>
</div>