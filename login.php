<?php
    require "config/config.php";
    session_start();

	// Is this user already logged in?
	if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
		// User is already logged in. Redirect to homepage.
		header('Location: home.php');
	} else {
		// User is NOT logged in.

		// Was there a form submission?
		if (isset($_POST['email']) && isset($_POST['password'])) {
			// Are credentials valid? TODO
			if ($_POST['email'] == "trojan@usc.edu" && $_POST['password'] == "trojan") {
				// Valid login

				$_SESSION['logged_in'] = true;
				$_SESSION['email'] = $_POST['email'];

				header('Location: home.php');
			} else {
				// Invalid credentials
				$error = "Invalid username or password.";
			}
		}

	}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Restaurant Finder | Login</title>
    <?php include "include-require/head.html" ?>
</head>

<body>
    <div id="page">
        <?php include "include-require/nav.php" ?>
        <div class="container-fluid" id="login-form">
            <div class="row text-center mt-4">
                <h1>Login / Signup</h1>
            </div>
            <div class="row px-5 justify-content-center">
                <div class="col-lg-6 col-12 container col-offset-8">
                    <form method="POST">
                        <div class="mt-5 mb-3 text-center">
                            <p class="d-inline me-2">Select one:</p>
                            <input type="radio" class="btn-check" name="state" value="login" id="login">
                            <label class="btn btn-outline-primary" for="login">Login</label>
                            <input type="radio" class="btn-check" name="state" value="signup" id="signup">
                            <label class="btn btn-outline-success" for="signup">Signup</label>
                        </div>
                        <?php if (isset($error) && trim($error) != '' ) : ?>
                            <div class="text-danger font-italic mb-3">
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>
                        <div class="mb-3 hidden-toggle hidden">
                            <label for="email" class="form-label">Email address</label>
                            <input type="email" name="email" class="form-control" id="email" placeholder="">
                        </div>
                        <div class="mb-3 hidden-toggle hidden">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" id="password" class="form-control"
                                aria-describedby="passwordHelp">
                            <div class="hidden-toggle hidden form-text" id="passwordHelp">
                            </div>
                        </div>
                        <div class="hidden-toggle hidden">
                            <button type="submit" id="submit-button" class="btn btn-primary mb-3 btn-lg"></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function () {
            let loginRadio = document.getElementById('login');
            let signupRadio = document.getElementById('signup');

            loginRadio.addEventListener('change', function () {
                if (this.checked) {
                    login();
                }
            });

            signupRadio.addEventListener('change', function () {
                if (this.checked) {
                    signUp();
                }
            });
        });

        function login() {
            let hiddenElements = document.querySelectorAll('.hidden-toggle');
            for (let i = 0; i < hiddenElements.length; i++) {
                hiddenElements[i].classList.remove('hidden');
            }
            let emailLabel = document.getElementById('email');
            emailLabel.placeholder = "";
            let submitButton = document.getElementById('submit-button');
            submitButton.innerHTML = "Login";
            let passwordHelp = document.getElementById('passwordHelp');
            passwordHelp.innerHTML = "";
        }

        function signUp() {
            let hiddenElements = document.querySelectorAll('.hidden-toggle');
            for (let i = 0; i < hiddenElements.length; i++) {
                hiddenElements[i].classList.remove('hidden');
            }
            let emailLabel = document.getElementById('email');
            emailLabel.placeholder = "name@example.com";
            let submitButton = document.getElementById('submit-button');
            submitButton.innerHTML = "Sign Up";
            let passwordHelp = document.getElementById('passwordHelp');
            passwordHelp.innerHTML = "Your password must be 8 - 20 characters long, contain letters and numbers, and must not contain spaces, special characters, or emoji.";
        }
    </script>
    <?php include "include-require/bootstrap.html" ?>
</body>

</html>